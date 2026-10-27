# Laboratorio defensivo — PHP2Shell / Node2Shell

**Clase 10 — Gestión Operativa y Seguridad en Redes (GOYS)**
**Duración:** 90 minutos · **Modalidad:** equipos de 4 · **Entorno:** Docker local, red aislada.

> **¿Tenés 2 horas?** Usá la variante [`README-2h.md`](README-2h.md): mismos tramos,
> con la caza (tramo 3) como actividad asincrónica, healthchecks y arranque rápido
> con `./scripts/arrancar-2h.sh`.

> **Este laboratorio es defensivo.** No se descargan ni ejecutan PoCs, exploits ni
> payloads de Internet. Solo se inventaría, se prioriza, se mitiga, se parchea, se
> detecta y se contiene. Todo se ejecuta sobre contenedores provistos por la cátedra.

---

## 1. Reglas

1. **Red aislada:** la red `labnet` es `internal: true`; los contenedores no tienen
   salida a Internet. No la cambies durante el ejercicio.
2. **No se ejecutan exploits:** el objetivo es decidir y verificar, no atacar.
3. **Solo sistemas propios:** en Argentina, el acceso o daño informático no autorizado
   está penado por la Ley 26.388. El laboratorio es sobre contenedores de la cátedra.
4. **Cada cambio se documenta:** antes/después, comando, evidencia y reversión.
5. **Si algo no se puede probar, se declara.** Un entregable honesto vale más que uno
   que finge haber verificado todo.

---

## 2. Requisitos

- Docker Engine 24+ con `docker compose` v2.
- 2 GB de RAM libre y ~1 GB de disco para las imágenes.
- No hace falta abrir puertos: se opera con `docker compose exec` y `docker top`.

---

## 3. Arranque

```bash
cd clases/clase-10-seguridad/laboratorio
docker compose --env-file versions.env up -d --build
docker compose ps
```

Servicios:

| Servicio | Contenedor | Runtime | Rol en el ejercicio |
|----------|-----------|---------|---------------------|
| `php-lab` | `php-lab` | PHP 8.3.35 + Apache | Portal con sesiones y subida de archivos |
| `node-lab` | `node-lab` | Node.js 22.23.3 + Express 5 | API que decodifica cuerpos Brotli |
| `py-lab` | `py-lab` | Python 3.11 + brotli 1.0.9 | Servicio interno con decodificador propio |

Las versiones vulnerables y corregidas están en `versions.env`. **No cambies las
versiones a mano**: usá los scripts o las variables de entorno.

---

## 4. Tramo 1 — Inventario y exposición (20 min)

Pregunta: **¿estamos afectados?** Respondé con evidencia, no con intuición.

```bash
./scripts/inventario.sh
```

Si preferís comando por comando:

```bash
# PHP: versión, SAPI y estado de la función de progreso de subida
docker compose exec -T php-lab php -v
docker compose exec -T php-lab php -r \
  'var_dump(ini_get("session.upload_progress.enabled"));'

# Node.js: runtime y versión de Brotli embebido
docker compose exec -T node-lab node -p \
  "process.version + ' / brotli ' + process.versions.brotli"
docker compose exec -T node-lab npm ls --all | grep -i brotli || true

# Python: paquetes que traen su propio decodificador
docker compose exec -T py-lab python -c \
  "import brotli; print(getattr(brotli, '__version__', 'desconocida'))"
```

**Entregable 1:** planilla de inventario con:

| Activo | Versión | ¿Expuesto a Internet? | ¿Procesa contenido no confiable? | ¿Afectado? | Evidencia |
|--------|---------|:---------------------:|:--------------------------------:|:----------:|-----------|
| Portal PHP | | | | | |
| API Node.js | | | | | |
| Servicio Python | | | | | |

> **Ojo:** en este lab **ningún** servicio está publicado a Internet. La pregunta de
> exposición se responde con la arquitectura, no con un `curl` desde tu casa. Si el
> servicio estuviera detrás de un proxy, la respuesta cambia: anotá el supuesto.

---

## 5. Tramo 2 — Priorización, mitigación y parche (25 min)

### 5.1. Matriz de decisión

| Activo | Expuesto a Internet | Explotación real conocida | Criticidad del negocio | Parche disponible | Decisión y plazo |
|--------|:------------------:|:-------------------------:|:----------------------:|:-----------------:|------------------|
| Portal PHP | | | | | |
| API Node.js | | | | | |
| Servicio Python | | | | | |

Criterios que deben aparecer en la justificación:

- **CVSS** no es suficiente: importa la exposición, la explotación real (KEV/EPSS) y
  la criticidad del activo.
- Un 9.2 en un servicio interno sin datos críticos puede ir después de un 7.5
  expuesto a Internet y ya explotado.
- Si no hay parche, se mitiga y se compensa; no se “espera”.

### 5.2. Mitigación inmediata en PHP

```bash
docker compose exec -T php-lab php -r \
  'echo ini_get("session.upload_progress.enabled"), PHP_EOL;'
```

Si está en `1`, la mitigación provisoria es deshabilitarla. En este lab se hace con
el `php.ini` montado en `php/php.ini`:

```bash
# 1) Editá php/php.ini y poné session.upload_progress.enabled = 0
# 2) Reiniciá el servicio
docker compose restart php-lab
# 3) Verificá el cambio
docker compose exec -T php-lab php -r \
  'echo ini_get("session.upload_progress.enabled"), PHP_EOL;'
```

> **Nota:** `session.upload_progress.enabled=0` es una mitigación **temporal**. No
> reemplaza el parche y puede romper funcionalidad que dependa del progreso de subida.
> Verificá la aplicación después de aplicarla.

### 5.3. Parcheo

```bash
./scripts/parchear.sh
```

Equivale a:

```bash
PHP_TAG=8.3.36-apache \
NODE_TAG=22.23.4-bookworm-slim \
BROTLI_VERSION=1.2.1 \
  docker compose --env-file versions.env up -d --build
```

**Entregable 2:** matriz completa + registro de cambios:

| Fecha/hora | Activo | Cambio | Evidencia antes | Evidencia después | Reversión |
|------------|--------|--------|-----------------|-------------------|-----------|
| | | | | | |

**Prueba funcional mínima:** después del parche, el portal PHP debe seguir respondiendo
y la API Node debe seguir devolviendo su versión.

> **Dos matices que conviene mirar:**
> - En Node.js 22.23.4, `process.versions.brotli` puede seguir mostrando `1.1.0`
>   porque el fix se backportea al decoder embebido. La señal confiable es la
>   **versión de Node.js** (22.23.4), no el string de Brotli.
> - El parche de PHP **no cambia** `session.upload_progress.enabled`. La mitigación
>   se aplica aparte en `php/php.ini` y se mantiene hasta el próximo bugfix regular
>   (CVE-2026-115560).

---

## 6. Tramo 3 — Detección y caza de amenazas (25 min)

```bash
./scripts/detectar.sh
```

O a mano:

```bash
# Árbol de procesos dentro de cada contenedor
docker top php-lab -eo pid,ppid,user,comm,args
docker top node-lab -eo pid,ppid,user,comm,args
docker top py-lab -eo pid,ppid,user,comm,args

# Hijos sospechosos en el log sintético
grep -E 'ppid_comm=(apache2|php-fpm|node|python)' logs/audit.log \
  | grep -E 'comm=(sh|bash|curl|wget|nc)'
```

**Tarea:**

1. Escribí una hipótesis de caza en una frase: qué proceso, qué padre, qué horario y
   qué evidencia esperarías.
2. Encontrá en `logs/audit.log` los casos anómalos. ¿Hay compromiso? ¿Qué hosts?
   ¿Qué líneas lo sostienen?
3. Leé la regla SIGMA de CISA para PHP2Shell y explicá qué observa y qué podría
   perderse (falsos negativos).
4. Proponé una regla equivalente para Node.js: proceso `node` que lanza un shell.

**Entregable 3:** hipótesis + hallazgos + regla propuesta + limitaciones del log.

> **Pista:** `ppid_comm=sshd` + `comm=bash` puede ser una sesión administrativa
> legítima. El log no trae usuario ni IP de origen: no alcanza para acusar. Anotá
> esa limitación; es parte del ejercicio.

---

## 7. Tramo 4 — Contención y arquitectura (20 min)

El parche puede llegar tarde. El objetivo es reducir el daño posible.

```bash
./scripts/verificar-contencion.sh
```

Controles que ya están en el compose:

| Control | Dónde se ve | Qué demuestra |
|---------|-------------|---------------|
| Red interna sin salida | `networks.labnet.internal: true` | Una RCE no puede descargar la segunda etapa |
| Usuario sin privilegios | `user: node` / `user: nobody` | El proceso no es root |
| Filesystem read-only | `read_only: true` + `tmpfs` | No puede escribir binarios ni persistir |
| Capabilities mínimas | `cap_drop: ALL` | No puede cambiar de usuario ni montar |
| Límites de memoria/PIDs | `mem_limit`, `pids_limit` | Un agotamiento de memoria no tumba el host |
| Rechazo de Brotli innecesario | Proxy o app que no acepta `Content-Encoding: br` | Reduce la superficie de Node2Shell |

**Entregable 4:** capturas o salidas de:

- `docker compose config` mostrando `read_only`, `cap_drop`, `mem_limit` e `internal`.
- Prueba de salida a Internet fallida en los tres servicios.
- Una prueba de que el servicio sigue funcionando después de los controles.

---

## 8. Entregable final del equipo

Un solo archivo Markdown (o PDF) con:

1. Inventario (tabla).
2. Matriz de priorización y decisiones.
3. Registro de cambios y reversión.
4. Detección: hipótesis, hallazgos, regla propuesta y limitaciones.
5. Contención: controles aplicados y evidencia.
6. **Una conclusión de 5 líneas:** ¿qué harían distinto en las primeras 2 horas?

Plantilla: [`../entregables/plantilla.md`](../entregables/plantilla.md).

---

## 9. Limpieza

```bash
docker compose --env-file versions.env down -v
```

---

## 10. Problemas frecuentes

| Síntoma | Causa probable | Solución |
|---------|----------------|----------|
| `permission denied` al arrancar Apache | `read_only` sin `tmpfs` | Verificá los `tmpfs` del compose |
| `npm install` falla en el build | Sin red en el host | El build necesita Internet; la red interna es solo para runtime |
| `docker compose exec` no encuentra el servicio | Contenedor caído | `docker compose ps` y `docker compose logs` |
| La prueba de salida “funciona” | Cambiaste `internal: true` | Volvé a la red aislada; no es parte del ejercicio |
| No aparece `ps` dentro del contenedor | Imagen mínima | Usá `docker top` desde el host |

---

## 11. Nota para el docente

- Verificá las versiones y el estado de explotación **el día anterior**: la situación
  de estos CVE cambia rápido.
- Si el build de Node o Python falla por red, construí las imágenes antes de la clase
  y llevá un `docker save`/`docker load` de respaldo.
- No proyectes las claves ni tokens de ningún entorno real.
- El laboratorio no demuestra explotación: demuestra **gestión**. Eso es lo que se
  evalúa.
