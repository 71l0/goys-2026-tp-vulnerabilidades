# Consigna — TP de gestión de vulnerabilidades

**Materia:** GOYS — UTN FR La Plata · **Año:** 2026
**Entrega:** jueves 29/10/2026, en el aula · **Modalidad:** equipos de 4
**Repo base:** `goys-2026-tp-vulnerabilidades` · **Fork:** uno por grupo, con el resto
como colaboradores

---

## 1. Contexto

En octubre de 2026 se divulgaron dos familias de vulnerabilidades críticas:

- **PHP2Shell** (CVE-2026-109618 y CVE-2026-109619): RCE sin autenticación en el
  núcleo de PHP, con explotación real confirmada y entrada en CISA KEV.
- **Node2Shell / HTTP2Shell** (CVE-2026-111497): out-of-bounds write en el decoder
  Brotli, con RCE demostrado en laboratorio, explotación real confirmada y entrada en
  CISA KEV.

La cadena **PHP2Shell + Fragnesia** (CVE-2026-46300, LPE del kernel Linux) también está
documentada en el contexto: llegar a root no se corrige solo con el parche de PHP.

El TP simula la respuesta de un equipo de operaciones: **no se explota nada**, se
gestiona el riesgo. El laboratorio tiene tres servicios vulnerables en una red
aislada; el equipo debe inventariarlos, priorizar, mitigar, parchear, detectar y
contener, dejando evidencia reproducible.

---

## 2. Objetivos

Al finalizar, el equipo debe poder:

1. Determinar exposición a partir de versiones, SAPI, configuración y dependencias
   transitivas.
2. Priorizar con CVSS, explotación real (KEV/EPSS), exposición y criticidad.
3. Aplicar y verificar mitigaciones y parches con plan de reversión.
4. Detectar y contener con procesos, logs, red y límites de recursos.
5. Comunicar el riesgo a un comité no técnico.

---

## 3. Entregables

Todo dentro de `entregables/grupo-XX/`:

### 3.1. `entrega.md` — taller (en clase)

1. **Inventario y exposición:** activo, versión, SAPI, ¿expuesto?, ¿procesa contenido
   no confiable?, ¿afectado?, evidencia.
2. **Matriz de priorización:** CVSS, explotación real, exposición, criticidad,
   decisión y plazo.
3. **Mitigación, parche y reversión:** antes/después, prueba funcional, cómo se
   revierte.
4. **Contención:** al menos 3 controles verificados y qué daño evita cada uno.
5. **Conclusión de 5 líneas:** ¿qué harían distinto en las primeras 2 horas?

### 3.2. `deteccion.md` — asincrónico (antes de la clase)

Con `laboratorio/logs/audit.log`:

1. **Hipótesis de caza:** proceso padre, proceso hijo, horario, evidencia esperada.
2. **Hallazgos:** hosts y líneas anómalas. ¿Hay compromiso? ¿Qué lo sostiene?
3. **Falsos positivos:** una línea que parezca sospechosa pero pueda ser legítima.
4. **Regla para Node.js:** proceso `node` que lanza `sh`, `bash`, `curl` o `wget`;
   qué observa y qué falso negativo podría tener.
5. **Limitaciones del log:** usuario, IP de origen, retención, ruido.

### 3.3. `informe.md` — individual (al cierre de la clase)

2–3 páginas dirigidas a un gerente:

1. Situación y nivel de riesgo para una organización ficticia asignada.
2. Plan de acción priorizado con plazos y responsables.
3. Indicadores que confirmarían o descartarían un compromiso.
4. Qué cambiaría en los procesos para la próxima vez.
5. Una vulnerabilidad **distinta** a las vistas (por ejemplo CVE-2026-115560) para
   demostrar transferencia.

### 3.4. `capturas/` — opcional

Salidas de comandos, capturas de `docker compose ps`, `docker top`, `docker compose
config`, o cualquier evidencia que no entre cómoda en el Markdown. Si usás capturas,
que tengan nombre descriptivo y estén referenciadas en `entrega.md`.

---

## 4. Laboratorio

La guía de 2 horas está en [`../laboratorio/README-2h.md`](../laboratorio/README-2h.md).
La versión de 90 minutos queda como referencia en
[`../laboratorio/README.md`](../laboratorio/README.md).

Servicios:

| Servicio | Runtime | Rol |
|---|---|---|
| `php-lab` | PHP 8.3.35 + Apache | Portal con sesiones y subida de archivos |
| `node-lab` | Node.js 22.23.3 + Express 5 | API que decodifica Brotli |
| `py-lab` | Python 3.11 + Brotli 1.0.9 | Servicio interno con decoder propio |

La red `labnet` es interna, sin salida a Internet. Los servicios corren sin
privilegios, con filesystem read-only y límites de memoria.

**Arranque:**

```bash
cd laboratorio
./scripts/arrancar-2h.sh
```

**Parche:**

```bash
./scripts/parchear.sh
```

**Detección y contención:**

```bash
./scripts/detectar.sh
./scripts/verificar-contencion.sh
```

**Limpieza:**

```bash
docker compose --env-file versions.env down -v
```

---

## 5. Reglas

1. **No se descargan ni ejecutan PoCs.** Los repositorios con “exploits” no están
   verificados y pueden traer malware.
2. **Solo los contenedores de la cátedra.** Ley 26.388: acceso o daño informático no
   autorizado está penado.
3. **No se commitean secretos.** Nada de claves, tokens, `.env` ni `secret.yml`.
4. **Cada cambio se documenta:** comando, antes/después, evidencia y reversión.
5. **Si no se puede probar, se declara.** No se inventa evidencia.
6. **Commits convencionales:** `docs(entrega): ...`, `fix(php): ...`, `ops(backup): ...`.

---

## 6. Evaluación

| Instancia | Peso | Qué mira |
|---|---:|---|
| Cuestionario de entrada | 15% | Comprensión y calidad de las dudas |
| Taller | 35% | Inventario, priorización, parche, detección, contención |
| Comité de crisis | 20% | Claridad, objeciones, comunicación no técnica |
| Informe individual | 30% | Transferencia y síntesis |

Detalle en [`rubrica.md`](rubrica.md). La nota final de la materia integra esta
instancia con el resto de la cursada.

- Cuestionario de entrada: [`cuestionario-entrada.md`](cuestionario-entrada.md).
- Preguntas del comité: [`comite.md`](comite.md).

---

## 7. Entrega

1. Un integrante del grupo forkea el repo y agrega al resto como **colaboradores**
   del fork. Todos aceptan la invitación. Es **un solo fork por grupo**.
2. Todos clonan el fork del grupo.
3. Un integrante crea y sube la rama `grupo-XX`
   (`git checkout -b grupo-XX && git push -u origin grupo-XX`); el resto la baja con
   `git fetch origin && git checkout grupo-XX`.
4. Cada uno commitea con su cuenta y pushea a `origin grupo-XX`.
5. Abrir **un PR por grupo** a `rodriguezemautn/goys-2026-tp-vulnerabilidades:main`
   con título `[Grupo XX] Entrega TP vulnerabilidades`.
6. Si el PR no está disponible, pegar el enlace del fork del grupo en el aula virtual.
7. **Antes de salir del aula del jueves 29/10.**

---

## 8. Fuentes

- PHP: https://www.php.net/archive/2026.php#2026-10-21-5
- Node.js: https://nodejs.org/en/blog/vulnerability/october-2026-security-releases
- CISA: https://www.cisa.gov/news-events/alerts/2026/10/21/critical-vulnerabilities-php-require-immediate-patching
- CISA KEV: https://www.cisa.gov/known-exploited-vulnerabilities-catalog
- NCSC-NL: https://github.com/NCSC-NL/php2shell
- CERT-EU: https://www.cert.europa.eu/publications/security-advisories/2026-018/
- Microsoft .NET: https://devblogs.microsoft.com/dotnet/dotnet-october-2026-oob-security-updates/

Contexto y cronología: [`../recursos/contexto.md`](../recursos/contexto.md).
