# TP — Gestión de vulnerabilidades: PHP2Shell y Node2Shell

**Materia:** Gestión Operativa y Seguridad en Redes (GOYS) — UTN FR La Plata
**Año:** 2026 · **Entrega:** jueves 29/10/2026, en el aula · **Actualizado:** 29/10/2026
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

> **Un solo fork por grupo.** No forkeen uno por integrante: el trabajo quedaría
> partido en cuatro historias y el PR no incluiría los commits de todos. El fork es
> del grupo y todos empujan a él con su propia cuenta.

1. **Un integrante del grupo forkea** este repo desde el botón *Fork* de GitHub.
   Elijan a alguien que esté disponible los dos días: solo el dueño del fork puede
   agregar colaboradores.

2. Ese integrante agrega al resto como **colaboradores** del fork: en el fork,
   *Settings → Collaborators → Add people*, con el usuario de GitHub de cada uno.
   Cada invitado tiene que **aceptar la invitación** (le llega por mail y por
   `github.com/notifications`). Si no la acepta, el `push` falla con `403`.

3. **Todos clonan el fork del grupo** (la misma URL), no el repo de la cátedra:

   ```bash
   git clone https://github.com/USUARIO-DEL-FORK/goys-2026-tp-vulnerabilidades.git
   cd goys-2026-tp-vulnerabilidades
   ```

4. Crean la rama del grupo (una sola, en el fork del grupo) y la suben:

   ```bash
   git checkout -b grupo-XX
   git push -u origin grupo-XX
   ```

   Si la rama ya existe porque otro integrante la creó, usen
   `git fetch origin && git checkout grupo-XX` en vez de `-b`.

5. Copian la plantilla a la carpeta del grupo:

   ```bash
   mkdir -p entregables/grupo-XX
   cp entregables/plantilla.md entregables/grupo-XX/entrega.md
   ```

6. Levantan el laboratorio:

   ```bash
   cd laboratorio
   ./scripts/arrancar-2h.sh
   ```

   El primer arranque puede tardar: construye las imágenes de Node y Python.
   Si ya las tienen, el script espera los healthchecks y corre el inventario.

**Si algo falla al principio:**

- `Permission denied` o `403` al pushear: la invitación de colaborador no fue
  aceptada, o están pusheando con otra cuenta de GitHub.
- `repository not found`: están usando la URL del repo base en vez de la del fork
  del grupo.
- `Updates were rejected`: hagan `git pull --rebase origin grupo-XX` y vuelvan a
  pushear.

### Actualizar el fork con upstream

El repo de la cátedra puede recibir correcciones después de que forkeaste. Tu fork
**no se actualiza solo**. Si el docente avisa que hay cambios, sincronizá:

**Opción simple (GitHub):**

1. En tu fork, botón **Sync fork → Update branch** para actualizar `main`.
2. Traé ese `main` a la rama del grupo:

   ```bash
   git fetch origin
   git checkout grupo-XX
   git merge origin/main
   git push origin grupo-XX
   ```

**Opción CLI (agregar el upstream una sola vez):**

```bash
git remote add upstream https://github.com/rodriguezemautn/goys-2026-tp-vulnerabilidades.git
git fetch upstream
git checkout main
git merge upstream/main
git push origin main
git checkout grupo-XX
git merge main
git push origin grupo-XX
```

- Usá `merge`, no `rebase`: no reescribís la historia ni necesitás `force-push`.
- Si el PR ya está abierto, el push a `grupo-XX` lo actualiza.
- Los cambios del upstream son en `docs/`, `recursos/` y `README.md`: no deberían
  tocar `entregables/grupo-XX/`.
- Si ya agregaste el upstream antes, salteá el `git remote add`.

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
| **Martes 27/10** | Fork del grupo, colaboradores, clone, lectura y primer arranque |
| **Miércoles 28/10** | Detección asincrónica + borrador del informe |
| **Jueves 29/10, en aula** | Taller completo, comité de crisis, cierre y push/PR |

La entrega se considera hecha cuando la rama `grupo-XX` del fork del grupo está
**pusheada** y el PR está abierto (o el enlace del fork está en el aula virtual)
antes de salir del aula.

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
Cuestionario: [`docs/cuestionario-entrada.md`](docs/cuestionario-entrada.md).
Preguntas del comité: [`docs/comite.md`](docs/comite.md).

---

## 7. Cómo entregar

1. Cada integrante commitea y pushea en **el fork del grupo**:

   ```bash
   git add entregables/grupo-XX
   git commit -m "docs(entrega): cierre TP grupo XX"
   git push origin grupo-XX
   ```

   No hace falta que cada uno abra un PR: alcanza con **un PR por grupo**.

2. Abrí **un Pull Request** desde el fork del grupo hacia
   `rodriguezemautn/goys-2026-tp-vulnerabilidades`, rama `main`.
   Título sugerido: `[Grupo XX] Entrega TP vulnerabilidades`.

3. Si el PR no está disponible, pegá el enlace del fork del grupo en el aula virtual.

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
