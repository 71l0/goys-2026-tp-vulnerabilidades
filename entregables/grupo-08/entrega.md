# Entregable — TP PHP2Shell / Node2Shell

**Grupo:** Grupo 08 · **Fork del grupo:** goys-2026-tp-vulnerabilidades · **Rama:** `grupo-08`
**Fecha:** jueves 29/10/2026

| Integrante | Usuario de GitHub | Rol en el TP |
|---|---|---|
|**Ulises Mateo Bucchino** | ulisesutnfrlp7 | **Cambios/Parche** | 
| **Jano Stratakis** | janostra | **Detección** | 
| **Valentín Garzaniti** | Vgarzaniti | **Contención/Comunicación** | 
| **Sofía Belén Raggi** | 71l0 | **Inventario** | 

> Roles sugeridos: inventario, cambios/parche, detección, contención/comunicación.
> Todos tienen que poder explicar su parte y una parte ajena.

---

## 1. Inventario

| Activo | Versión | ¿Expuesto a Internet? | ¿Procesa contenido no confiable? | ¿Afectado? | Evidencia | Justificacion |
|--------|---------|:---------------------:|:--------------------------------:|:----------:|-----------|---------------|
| Portal PHP | PHP 8.3.35 | No (80/tcp) | Si* | Si | Inventario PHP; contenedor php-lab, puerto interno 80/tcp, sin publicación visible en Docker.| PHP 8.3.35 es anterior a la versión corregida 8.3.36 para PHP2Shell.|
| API Node.js | Node.js 22.23.3 | No (8000/tcp) | Si* | Si | Inventario Node.js; contenedor node-lab, puerto interno 3000/tcp, sin publicación visible en Docker.| Node.js 22.23.3 es anterior a 22.23.4, que corrige CVE-2026-111497.|
| Servicio Python | Python 3.11.18 | No (3000/tcp) | Si* | Si | Inventario Python; contenedor py-lab, puerto interno 8000/tcp, sin publicación visible en Docker.| Brotli 1.0.9 está dentro del rango vulnerable de CVE-2026-111497; falta confirmar que el servicio utiliza ese decoder para descomprimir entradas no confiables.|

(La evidencia de los valores se encuentran en [`/capturas/tramo_1`](./capturas/tramo_1/))

**Supuestos de exposición:** _¿qué asumieron sobre el proxy, la red y el despliegue?_

Para evaluar la exposicion de los servicios del laboratorio, asumimos lo siguiente:

- **Proxy Inverso**: se considera que no existe un proxy inverso (ejemplo: Nginx o Traefik) ni un balanceador que publique los servicios PHP, Node.js o Python hacia Internet.
- **Red**: los tres servicios se ejecutan en una red aislada de Docker. No se identificaron puertos publicados directamente en el host en la salida de `docker ps --format "table {{.Names}}\t{{.Ports}}"`, por lo tanto consideramos que esta evidencia es consistente con respecto al aislamiento previsto, aunque no descarta por sí sola otras vías de acceso.
- **Despliegue**: los contenedores php-lab, node-lab y py-lab se consideran servicios de laboratorio, sin publicación externa, conforme a la consigna de la práctica.
- **Alcance de la evaluación**: la ausencia de exposición directa a Internet no se interpreta como ausencia de vulnerabilidad. Los servicios se evalúan según las versiones instaladas, las dependencias utilizadas y el procesamiento de contenido no confiable.
- **Limitaciones**: estos supuestos se basan en la arquitectura declarada por la consigna y en la evidencia disponible. No se afirma que se haya verificado exhaustivamente la configuración de red, las reglas de firewall ni la existencia de rutas alternativas de acceso.

En consecuencia, los tres servicios se consideran no expuestos directamente a Internet en el escenario evaluado, sin descartar que la evaluación deba actualizarse si se incorpora un proxy, se publican puertos o cambia la arquitectura de despliegue.

---

## 2. Priorización y decisión

| Activo | CVSS | Explotación real | Exposición | Criticidad | Decisión y plazo |
|--------|:----:|:----------------:|:----------:|:----------:|------------------|
| Portal PHP | Alta (RCE vía `session.upload_progress`) | Sí — técnica conocida en PHP < 8.3.36 | No — red interna del laboratorio | Alta | Mitigar ya (`session.upload_progress.enabled = 0`) + parchear a 8.3.36 en el día |
| API Node.js | Media (brotli / runtime) | No confirmada | No — red interna del laboratorio | Media | Parchear a Node 22.23.4 en el día; revisar dependencias brotli |
| Servicio Python | Media (brotli 1.0.9 → 1.2.1) | No confirmada | No — red interna del laboratorio | Media | Actualizar a Python con brotli 1.2.1 en el día |

**Justificación:** el Portal PHP es el activo más crítico porque `session.upload_progress.enabled=1` es explotable para RCE en versiones anteriores a 8.3.36, y el inventario confirmó que el valor estaba en `1`. Se prioriza la mitigación inmediata (deshabilitar el progreso de subida) y el parche de versión el mismo día. Node y Python se tratan con menor prioridad por no tener explotación real confirmada en el entorno y por estar en red aislada, pero se parchean igual para reducir superficie. La evidencia nueva que cambiaría la decisión sería confirmar exposición pública de alguno de los servicios (puertos publicados en `docker-compose.yml`), lo que subiría la criticidad de todos.

---

## 3. Mitigación, parche y reversión

| Fecha/hora | Activo | Cambio | Antes | Después | Reversión |
|------------|--------|--------|-------|---------|-----------|
| 29/10/2026 — Fase 4 | Portal PHP | Mitigación `session.upload_progress.enabled` | `1` | `0` | `git checkout -- laboratorio/php/php.ini` y `docker compose restart php-lab` |
| 29/10/2026 — Fase 4 | Portal PHP | Parche de versión | PHP 8.3.35 | PHP 8.3.36 | Volver a tag anterior con `PHP_TAG` en `versions.env` y `docker compose up` |
| 29/10/2026 — Fase 4 | API Node.js | Parche de versión | Node v22.23.3 | Node v22.23.4 | Volver a tag anterior con `NODE_TAG` en `versions.env` |
| 29/10/2026 — Fase 4 | Servicio Python | Parche de versión (brotli) | brotli 1.0.9 | brotli 1.2.1 | Volver a tag anterior de imagen Python |

**Prueba funcional mínima:** `docker compose exec -T php-lab php -r 'echo ini_get("session.upload_progress.enabled");'` devolvió `0`, y `docker compose ps` muestra `php-lab`, `node-lab` y `py-lab` en estado `healthy`/`running`.

**Qué no se pudo probar:** Acceso real desde red externa (no hay salida a Internet ni proxy público en el laboratorio isolado), por lo que no se validó exposición a Internet ni ataque activo contra el puerto publicado.

**Evidencia (outputs):**

Mitigación PHP:
```
$ docker compose restart php-lab
$ docker compose exec -T php-lab php -r 'echo ini_get("session.upload_progress.enabled");'
0
```

Parche (`./scripts/parchear.sh`):
```
== Versiones después del parche ==
=== PHP ===
PHP 8.3.36 (cli) (built: Oct 28 2026 01:26:59) (NTS)
session.upload_progress.enabled=0
=== Node.js ===
v22.23.4 / brotli 1.1.0
-- dependencias con brotli:
(sin paquete brotli externo: lo aporta el runtime)
=== Python ===
python 3.11.18
brotli 1.2.1
```

---

## 4. Detección y caza

**Hipótesis:** _una frase: proceso, padre, horario, evidencia esperada._

**Hallazgos en `audit.log`:**

| Línea / timestamp | Host | Padre → hijo | ¿Anómalo? | Por qué |
|-------------------|------|--------------|:---------:|---------|
| | | | | |
| | | | | |

**¿Hay compromiso?** _sí/no/indeterminado, con evidencia._

**Regla propuesta para Node.js:** _qué proceso observa, qué condición y qué falso
negativo podría tener._

**Limitaciones del log:** _usuario, IP de origen, retención, ruido, etc._

---

## 5. Contención

| Control | Configuración | Evidencia | ¿Funciona? |
|---------|---------------|-----------|:----------:|
| Red sin salida | `internal: true` | | |
| Usuario sin privilegios | | | |
| Filesystem read-only | | | |
| Capabilities mínimas | | | |
| Límites de memoria/PIDs | | | |
| Rechazo de Brotli innecesario | | | |

**Prueba de salida a Internet:** _resultado en los tres servicios._

---

## 6. Conclusión (5 líneas)

_¿Qué harían distinto en las primeras 2 horas?_

---

## 7. Checklist antes de entregar

- [ ] El laboratorio arrancó y los 3 servicios quedaron `healthy`.
- [ ] Inventario con evidencia (versión, SAPI, configuración).
- [ ] Matriz con decisión y plazo.
- [ ] Parche aplicado y verificado; reversión documentada.
- [ ] Al menos 3 controles de contención probados.
- [ ] `deteccion.md` completo.
- [ ] `informe.md` individual completo.
- [ ] Sin claves, tokens ni placeholders.
- [ ] Repo pusheado y PR abierto.

Checklist completa: [`../docs/checklist-entrega.md`](../docs/checklist-entrega.md).
