# Contexto — PHP2Shell y Node2Shell/HTTP2Shell

**Lectura previa al TP.** No reemplaza los avisos oficiales. La situación cambia;
verificá las fuentes antes de decidir.

---

## 1. PHP2Shell

| Campo | Valor |
|---|---|
| CVEs | **CVE-2026-109618** (OOM) y **CVE-2026-109619** (stale global state) |
| CVSS 4.0 | 9.2 |
| Tipo | RCE sin autenticación y divulgación de memoria del worker |
| Afecta | PHP 5.4+; mod_php, PHP-FPM, php-cgi, FrankenPHP, LiteSpeed |
| Mayor riesgo | Apache mod_php (el ataque puede correr antes de la app) |
| Corregido | **8.5.12, 8.4.27, 8.3.36, 8.2.35** (21/10/2026) |
| Explotación | **Real, confirmada**; CISA KEV desde el 26/10/2026 |

**Qué pasó.** Un error fatal u OOM interrumpe PHP antes de la limpieza normal y deja
estado del request apuntando a memoria liberada o sin inicializar. El cleanup o el
request siguiente lo trata como válido: corrupción, fuga de memoria y, en
configuraciones favorables, RCE. No depende de la app: una página “Hello World” sobre
PHP vulnerable está expuesta.

**Primeros hitos:**

- **17/10** — Amazon MadPot ve un intento cuatro días antes del disclose.
- **21/10 08:37 UTC** — Delft ve el primer request “parecido a PHP2Shell”.
- **21/10 10:37 UTC** — CrowdSec bloquea un request antes de que exista el CVE.
- **21/10 14:00 UTC** — PHP publica los fixes.
- **24/10** — Karimi confirma RCE en honeypots con PHP 8.4.26/mod_php.
- **26/10** — CISA agrega ambos CVE a **KEV**.

**Lectura correcta:** hay explotación real y muchos intentos, pero **no hay una lista
pública de víctimas**. Las apps que se nombran en las investigaciones (WordPress,
Drupal, Joomla, MediaWiki, Roundcube, Magento, phpMyAdmin, Nextcloud, Laravel,
Symfony, WHMCS) son **targets de demostración**, no sitios atacados.

---

## 2. Node2Shell / HTTP2Shell

| Campo | Valor |
|---|---|
| CVE | **CVE-2026-111497** |
| CVSS 4.0 | 9.2 (Google, CNA); Node.js lo clasifica High |
| Tipo | Out-of-bounds write en el decoder Brotli (CWE-787 + CWE-252) |
| Afecta | Brotli **1.0.3–1.2.0**; Node.js 22/24/26, Bun, Python, .NET, Envoy, Istio |
| Corregido | Brotli **1.0.10 / 1.1.1 / 1.2.1**; Node **22.23.4 / 24.22.1 / 26.12.1** |
| Explotación | **Sin confirmación en el mundo real** al 27/10; RCE en laboratorio |

**Cómo llega al decoder.** No hace falta importar Brotli: `fetch()` descomprime
respuestas automáticamente; Express 5 / body-parser 2 descomprimen request bodies
`br` por defecto; Envoy/Istio con el decompressor habilitado; Python HTTP clients con
Brotli instalado; .NET si se habilita la decompresión.

**Hallazgo.** Lo reportó **OpenAI Codex Security** (Khai Tran y Sylvie Mayer). El root
cause se había corregido en Brotli en febrero de 2026, pero no se reconoció como
vulnerabilidad. Imperva demostró RCE en Node.js 24.22.0 Linux arm64; 24.22.1 rechaza
la misma entrada. No hay explotación maliciosa confirmada ni KEV al 27/10.

---

## 3. IA agéntica: qué cambió

- **Descubrimiento:** Calif usó GPT‑5.6 Sol; Wiz usó Wiz Atlas + Gemini 4 Argon +
  GPT‑5.6 Sol; OpenAI coordinó *Patch the Planet*. Tres equipos llegaron a las mismas
  fallas en agosto de 2026.
- **Exploit:** los modelos ayudaron a adaptar cadenas de memoria a versiones,
  configuraciones y layouts.
- **Post-explotación:** Unit 42 reportó comandos en lotes con `==SHELLUSER==` /
  `==PASSWD==`, tareas cortas y pivotes rápidos. En 2 horas: hashes, secretos MFA,
  NTLM relay a AD e intento de Domain Admin. **Caveat:** Unit 42 después no pudo
  confirmar que el acceso inicial haya sido PHP2Shell.
- **Velocidad:** Artemis vio una copia armada del PoC **30 minutos** después del PoC.
- **Lo que no está probado:** que los atacantes hayan usado IA para escribir el
  exploit. La IA acortó descubrimiento, weaponización y post-explotación; el criterio
  humano sigue siendo el que decide.

---

## 4. Fuentes

- PHP: https://www.php.net/archive/2026.php#2026-10-21-5
- Node.js: https://nodejs.org/en/blog/vulnerability/october-2026-security-releases
- CISA: https://www.cisa.gov/news-events/alerts/2026/10/21/critical-vulnerabilities-php-require-immediate-patching
- CISA KEV: https://www.cisa.gov/known-exploited-vulnerabilities-catalog
- NCSC-NL: https://github.com/NCSC-NL/php2shell
- CERT-EU: https://www.cert.europa.eu/publications/security-advisories/2026-018/
- Wiz: https://www.wiz.io/blog/php2shell-php-rce
- Calif: https://calif.io/php2shell/
- OpenAI: https://openai.com/index/coordinated-disclosure-of-critical-vulnerabilities/
- Unit 42: https://unit42.paloaltonetworks.com/cve-2026-109618-cve-2026-109619/
- Imperva: https://www.imperva.com/blog/node2shell-from-brotli-data-to-remote-code-execution/
- Delft: https://php2shell.delftintellab.com/
