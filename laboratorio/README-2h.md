# Laboratorio — versión 2 horas

**Clase 10 — GOYS** · **Duración:** 120 minutos · **Equipos:** 4 personas
**Base:** [`README.md`](README.md) (versión de 90 minutos) · **Cambio principal:** el tramo 3 (caza) pasa a actividad asincrónica.

---

## 1. Qué se hace en clase y qué queda asincrónico

| Tramo | Versión 90 min | Versión 2 h |
|---|---|---|
| 1. Inventario y exposición | 20 min | **25 min en clase** |
| 2. Priorización, mitigación y parche | 25 min | **35 min en clase** |
| 3. Detección y caza | 25 min | **Asincrónico** (guía + `audit.log`) |
| 4. Contención y arquitectura | 20 min | **25 min en clase** |
| Comité / cierre | — | **10 min** (mini-comité) |

**Entregables en clase:** inventario, matriz + registro de cambios, contención.
**Entregable asincrónico:** análisis de detección (5 preguntas, antes de la clase del jueves 29/10).

---

## 2. Antes de la clase (docente)

```bash
cd clases/clase-10-seguridad/laboratorio
./scripts/preparar.sh
```

Eso deja listas:

- `php:8.3.35-apache` (vulnerable) y `php:8.3.36-apache` (parcheada).
- `goys-vuln-lab-node:22.23.3-bookworm-slim` (vulnerable) y `...:22.23.4...` (parcheada).
- `goys-vuln-lab-py:1.0.9` (vulnerable) y `...:1.2.1` (parcheada).

> **No arranques los contenedores en la preparación.** `preparar.sh` solo hace pull/build.
> El arranque se hace al inicio de la clase.

**Checklist del docente:**

- [ ] `./scripts/preparar.sh` terminó sin errores.
- [ ] `./scripts/arrancar-2h.sh` levanta los 3 servicios **healthy**.
- [ ] `./scripts/inventario.sh` muestra PHP 8.3.35, Node 22.23.3, brotli 1.0.9.
- [ ] `./scripts/verificar-contencion.sh` muestra `SIN SALIDA` en los 3 servicios.
- [ ] `docker compose --env-file versions.env down -v` deja el entorno limpio.
- [ ] Tenés un plan B si Docker falla: proyectar tu lab y repartir las salidas impresas.

---

## 3. Arranque en clase (5 min)

```bash
cd clases/clase-10-seguridad/laboratorio
./scripts/arrancar-2h.sh
```

El script espera a que los healthchecks estén `healthy` y corre el inventario.
Si las imágenes no existen, las construye (por eso conviene `preparar.sh` antes).

---

## 4. Guion de 120 minutos

### 0–10 min — Reglas y encuadre

- Leer en voz alta: red aislada, no PoCs, solo contenedores de la cátedra, Ley 26.388.
- Repartir roles: **inventario**, **cambios**, **contención**, **comunicador**.
- Recordar la consigna: no se evalúa “explotar”, se evalúa **decidir y verificar**.

### 10–35 min — Tramo 1: inventario y exposición

```bash
./scripts/inventario.sh
```

Completar:

| Activo | Versión | ¿Expuesto? | ¿Contenido no confiable? | ¿Afectado? | Evidencia |
|---|---|:---:|:---:|:---:|---|
| Portal PHP | | | | | |
| API Node | | | | | |
| Servicio Python | | | | | |

**Preguntas que el docente dispara:**

- ¿Qué SAPI usa PHP? ¿Por qué mod_php es el de mayor riesgo?
- ¿Qué versión de Brotli trae Node? ¿El string alcanza como evidencia?
- ¿Este servicio está expuesto a Internet? ¿Cómo lo sabés si no hay puertos publicados?
- ¿Qué activo procesa contenido no confiable? ¿Y cuál no?

**Entregable 1:** tabla completa + supuestos de exposición.

### 35–70 min — Tramo 2: priorizar, mitigar, parchear

**a) Matriz (10 min):**

| Activo | CVSS | Explotación real | Exposición | Criticidad | Decisión y plazo |
|---|---|---|---|---|---|
| Portal PHP | | | | | |
| API Node | | | | | |
| Servicio Python | | | | | |

**b) Mitigación PHP (5 min):** editar `php/php.ini` y poner
`session.upload_progress.enabled = 0`; luego `docker compose restart php-lab` y verificar.

**c) Parche (15 min):**

```bash
./scripts/parchear.sh
```

Debe mostrar PHP 8.3.36, Node 22.23.4 y brotli 1.2.1.
**Ojo:** en Node 22.23.4 el string de Brotli puede seguir en 1.1.0; la señal es la
versión de Node.

**d) Verificación y reversión (5 min):**

- ¿La app sigue respondiendo?
- ¿Qué cambio exactamente?
- ¿Cómo volvés atrás si el parche rompe algo?

**Entregable 2:** matriz + registro de cambios (antes/después/reversión).

### 70–95 min — Tramo 4: contención

```bash
./scripts/verificar-contencion.sh
```

Revisar en `docker-compose.yml`:

- `networks.labnet.internal: true`
- `read_only: true` + `tmpfs`
- `cap_drop: ALL`
- `mem_limit` / `pids_limit`
- `user: node` / `user: nobody`

**Prueba clave:** salida a Internet debe fallar en los 3 servicios.
**Pregunta:** ¿qué control extra agregarías y qué daño evita?

**Entregable 3:** evidencia de los controles + prueba de salida fallida.

### 95–110 min — Tramo 3: explicar la caza (asincrónica)

No se hace el análisis completo en clase. El docente muestra 5 minutos:

```bash
./scripts/detectar.sh
```

Y explica la consigna asincrónica:

1. Leer `logs/audit.log`.
2. Escribir una hipótesis de caza (proceso, padre, horario, evidencia).
3. Identificar los casos anómalos y justificar.
4. Proponer una regla para Node.js (`node` que lanza un shell).
5. Declarar limitaciones del log (usuario, IP de origen, retención).

**Entrega:** antes de la clase del jueves 29/10, individual o por equipo, 1 página.

### 110–120 min — Mini-comité y cierre

Una sola pregunta por equipo, 2 minutos cada uno:

> “El parche no llega hasta el viernes. ¿Qué hacemos en las próximas 2 horas y qué
> le decimos a la gerencia?”

Cierre:

- ¿Qué evidencia cambió tu decisión?
- ¿Qué no pudiste probar?
- ¿Qué harías distinto en las primeras 2 horas?

---

## 5. Entregable asincrónico — detección

**Archivo:** `../entregables/deteccion.md` (o el nombre que indique el aula virtual).
**Plazo:** jueves 29/10, antes de la clase.

| Pregunta | Qué se espera |
|---|---|
| 1. Hipótesis | Una frase: proceso padre, proceso hijo, horario, evidencia esperada. |
| 2. Hallazgos | Hosts y líneas anómalas de `audit.log`; ¿hay compromiso? |
| 3. Falsos positivos | ¿Qué línea parece sospechosa pero puede ser legítima? |
| 4. Regla Node | Proceso `node` que lanza `sh`, `bash`, `curl` o `wget`; qué observa y qué se pierde. |
| 5. Limitaciones | Usuario, IP de origen, retención, ruido, ausencia de telemetría. |

> **Pista:** `ppid_comm=sshd` + `comm=bash` puede ser una sesión administrativa.
> El log no trae usuario ni IP de origen; no alcanza para acusar.

---

## 6. Evaluación en la versión 2 h

Se mantiene la rúbrica de [`../docs/rubrica.md`](../docs/rubrica.md).
El tramo 3 asincrónico alimenta el 35% del taller. El comité se reduce a una
pregunta por equipo (10 min) y el informe individual sigue siendo de 2–3 páginas.

| Instancia | Peso | Cuándo |
|---|---:|---|
| Cuestionario de entrada | 15% | Antes de la clase |
| Taller (inventario + parche + contención) | 35% | En clase |
| Detección asincrónica | dentro del 35% | antes de la clase |
| Mini-comité | 20% | Últimos 10 min |
| Informe individual | 30% | jueves 29/10, en clase |

---

## 7. Qué se recorta y por qué

- **No se hace el análisis completo del log en clase:** 25 minutos no alcanzan para
  leer, hipotetizar y escribir una regla con calidad. Se hace asincrónico.
- **No se hace el comité completo de 40 minutos:** se reduce a una pregunta y 2
  minutos por equipo. El resto se evalúa en el informe.
- **No se agregan tramos nuevos:** la versión 2 h es la misma columna vertebral,
  con menos profundidad en detección y comunicación.

---

## 8. Plan B si Docker falla

1. El docente proyecta su propio lab y ejecuta `inventario.sh`, `parchear.sh` y
   `verificar-contencion.sh`.
2. Los equipos completan las tablas con las salidas proyectadas.
3. Para detección, se reparte `logs/audit.log` impreso o en el aula virtual.
4. No se pierde el objetivo: la clase evalúa decisiones, no la instalación de Docker.
