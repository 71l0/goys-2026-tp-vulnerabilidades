# TP — Gestión de vulnerabilidades: PHP2Shell y Node2Shell

**Materia:** Gestión Operativa y Seguridad en Redes (GOYS) — UTN FR La Plata
**Año:** 2026 · **Entrega:** jueves 29/10/2026, en el aula
**Modalidad:** equipos de 4 · **Entorno:** Docker local

> **Este TP es defensivo.** No se desarrollan ni ejecutan exploits. Se trabaja sobre
> inventario, priorización, mitigación, parcheo, detección, contención y comunicación.
> Si encontrás un repo con “exploit”, “scanner” o “PoC”, no lo abras: no está verificado
> y puede traer malware.

---

## 1. Qué vas a hacer

Vas a gestionar dos incidentes reales de octubre de 2026:

- **PHP2Shell** — CVE-2026-109618 y CVE-2026-109619, RCE sin autenticación en el
  núcleo de PHP.
- **Node2Shell / HTTP2Shell** — CVE-2026-111497, out-of-bounds write en el decoder
  Brotli que afecta a Node.js, Bun, Python, .NET, Envoy, Istio y más.

El objetivo no es “explotar”: es **decidir y verificar** con evidencia. El laboratorio
te da tres servicios vulnerables en una red aislada; vos tenés que inventariarlos,
priorizar, mitigar, parchear, detectar y contener.

---

## 2. Cómo empezar

1. **Forkeá** este repo desde el botón *Fork* de GitHub.
2. Cloná **tu fork**:

   ```bash
   git clone https://github.com/TU-USUARIO/goys-2026-tp-vulnerabilidades.git
   cd goys-2026-tp-vulnerabilidades
   ```

3. Creá una rama por grupo:

   ```bash
   git checkout -b grupo-XX
   ```

4. Copiá la plantilla a tu carpeta:

   ```bash
   mkdir -p entregables/grupo-XX
   cp entregables/plantilla.md entregables/grupo-XX/entrega.md
   ```

5. Levantá el laboratorio:

   ```bash
   cd laboratorio
   ./scripts/arrancar-2h.sh
   ```

   El primer arranque puede tardar: construye las imágenes de Node y Python.
   Si ya las tenés, el script espera los healthchecks y corre el inventario.

---

## 3. Qué tenés que entregar

Todo dentro de `entregables/grupo-XX/`:

| Archivo | Qué contiene | Cuándo |
|---|---|---|
| `entrega.md` | Inventario, matriz de priorización, cambios/parche, contención | En clase, jueves 29/10 |
| `deteccion.md` | Hipótesis de caza, hallazgos en `audit.log`, regla para Node, limitaciones | Antes de la clase del jueves |
| `informe.md` | Informe individual de 2–3 páginas para un gerente | Al finalizar la clase del jueves |
| `capturas/` | Evidencia visual o salidas de comandos (opcional) | En clase |

La plantilla base está en [`entregables/plantilla.md`](entregables/plantilla.md).
La guía del laboratorio de 2 horas está en
[`laboratorio/README-2h.md`](laboratorio/README-2h.md).

> **Importante:** no edites `docs/`, `recursos/` ni el código del laboratorio para
> “hacer que funcione”. Si necesitás cambiar una configuración para la mitigación
> (por ejemplo `laboratorio/php/php.ini`), registralo en el change log.

---

## 4. Cronograma

| Fecha | Qué |
|---|---|
| **Martes 27/10** | Fork, clone, lectura de fuentes, primer arranque del lab |
| **Miércoles 28/10** | Detección asincrónica + borrador del informe |
| **Jueves 29/10, en aula** | Taller completo, comité de crisis, cierre y push/PR |

La entrega se considera hecha cuando el repo de tu fork está **pusheado** y el PR
está abierto (o el enlace del fork está en el aula virtual) antes de salir del aula.

---

## 5. Reglas

1. **Red aislada:** el laboratorio usa una red Docker interna sin salida a Internet.
   No la cambies.
2. **No se ejecutan exploits ni PoCs.** El ejercicio es defensivo.
3. **Solo los contenedores de la cátedra.** En Argentina, el acceso o daño informático
   no autorizado está penado por la Ley 26.388.
4. **No commitees secretos.** Nada de claves, tokens, `.env` ni `secret.yml`.
5. **Cada cambio se documenta:** antes/después, comando, evidencia y reversión.
6. **Si no se puede probar, se declara.** Un entregable honesto vale más que uno que
   finge haber verificado todo.
7. **Commits convencionales:** `docs(entrega): ...`, `fix(php): ...`, `ops(backup): ...`.

---

## 6. Evaluación

| Instancia | Peso | Qué mira |
|---|---:|---|
| Cuestionario de entrada | 15% | Comprensión y calidad de las dudas |
| Taller (inventario, parche, detección, contención) | 35% | Evidencia técnica y justificación |
| Comité de crisis | 20% | Claridad, objeciones, comunicación no técnica |
| Informe individual | 30% | Transferencia a un caso nuevo |

Detalle y niveles en [`docs/rubrica.md`](docs/rubrica.md).

---

## 7. Cómo entregar

1. Commiteá y pusheá en tu fork:

   ```bash
   git add entregables/grupo-XX
   git commit -m "docs(entrega): cierre TP grupo XX"
   git push origin grupo-XX
   ```

2. Abrí un **Pull Request** desde tu fork hacia
   `rodriguezemautn/goys-2026-tp-vulnerabilidades`, rama `main`.
   Título sugerido: `[Grupo XX] Entrega TP vulnerabilidades`.

3. Si el PR no está disponible, pegá el enlace de tu fork en el aula virtual.

---

## 8. Fuentes primarias

- PHP: https://www.php.net/archive/2026.php#2026-10-21-5
- Node.js: https://nodejs.org/en/blog/vulnerability/october-2026-security-releases
- CISA: https://www.cisa.gov/news-events/alerts/2026/10/21/critical-vulnerabilities-php-require-immediate-patching
- CISA KEV: https://www.cisa.gov/known-exploited-vulnerabilities-catalog
- NCSC-NL: https://github.com/NCSC-NL/php2shell
- CERT-EU: https://www.cert.europa.eu/publications/security-advisories/2026-018/
- Microsoft .NET: https://devblogs.microsoft.com/dotnet/dotnet-october-2026-oob-security-updates/

Contexto y cronología: [`recursos/contexto.md`](recursos/contexto.md).

---

## 9. Licencia y uso

Material educativo de la cátedra GOYS (UTN FR La Plata). El laboratorio es una
simulación defensiva; no usar las versiones vulnerables fuera del entorno aislado.
