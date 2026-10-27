# Cuestionario de entrada — Clase 10 (PHP2Shell / Node2Shell)

**Individual · 25 minutos · se entrega antes de la clase**
**Objetivo:** llegar con los conceptos mínimos para decidir, no para memorizar CVE.

> Respondé sin buscar durante el cuestionario. Si no sabés algo, escribí **“no lo sé”**
> y una línea de cómo lo averiguarías. Eso también se evalúa.

---

## Parte A — 8 preguntas

**1.** Dos vulnerabilidades tienen CVSS 4.0 9.2. Una está expuesta a Internet y se
explota en el mundo real; la otra está en un servicio interno sin datos críticos y
solo tiene PoC de laboratorio. ¿Cuál priorizás primero y por qué? Nombrá al menos
dos criterios además del CVSS.

**2.** PHP2Shell (CVE-2026-109618 y CVE-2026-109619) está en el **núcleo de PHP**.
Un sitio corre WordPress actualizado, pero con PHP 8.3.30. ¿Está afectado? ¿Por qué?

**3.** El equipo de sistemas actualizó el core de Drupal y puso el sitio en modo
mantenimiento. ¿Eso protege contra PHP2Shell? Justificá.

**4.** Node2Shell (CVE-2026-111497) está en el decodificador Brotli. Mencioná **dos
caminos** por los que una aplicación puede llegar al decoder sin llamar directamente
a una API de Brotli.

**5.** ¿Qué diferencia hay entre `npm audit` limpio y una imagen Docker segura para
Node2Shell? ¿Dónde vive el decoder?

**6.** ¿Por qué un inventario basado solo en el manifiesto de dependencias puede
fallar con Brotli? Pensá en runtimes, binarios, appliances y copias vendorizadas.

**7.** Ordená de mayor a menor urgencia y justificá: (a) CVE 9.2 en un servicio interno
sin datos críticos, (b) CVE 7.5 expuesto a Internet y en KEV, (c) CVE 9.2 con PoC de
laboratorio y sin explotación real, (d) CVE 9.2 en un appliance EOL que no se puede
parchear.

**8.** Un PHP worker empieza a lanzar `sh`, `curl` o `wget` después de un request
sospechoso. ¿Qué señal de detección construirías y qué falso positivo esperarías?

---

## Parte B — Tarea corta

Elegí un sistema real o ficticio (por ejemplo, el portal de una cooperativa con
WordPress y una API en Node.js) y respondé:

1. **¿Qué datos necesitarías** para saber si está afectado por PHP2Shell y por
   Node2Shell? Listá al menos 5.
2. **¿Qué es lo que todavía no sabés** y cómo lo averiguarías? Tres líneas.
3. **¿Qué harías en las primeras 2 horas** si el CERT te avisa de un RCE crítico?
   Listá 5 acciones, en orden.

---

## Entrega

Subí tu respuesta al aula virtual antes de la clase. En clase se discuten los
errores comunes, no las respuestas correctas.
