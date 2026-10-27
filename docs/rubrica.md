# Rúbrica — TP de gestión de vulnerabilidades

**Materia:** GOYS — UTN FR La Plata · **Entrega:** jueves 29/10/2026

---

## 1. Distribución de la nota

| Instancia | Peso | Qué evalúa |
|---|---:|---|
| Cuestionario de entrada | 15% | Comprensión y calidad de las dudas |
| Taller (4 entregables) | 35% | Evidencia técnica, decisión y reversión |
| Comité de crisis | 20% | Claridad, objeciones, comunicación no técnica |
| Informe individual | 30% | Transferencia a un caso nuevo |

---

## 2. Taller — criterios (35%)

| Criterio | Inicial (1) | Competente (3) | Destacado (5) |
|---|---|---|---|
| **Diagnóstico de exposición** | Lista versiones sin contexto. | Distingue activos expuestos y no expuestos con evidencia. | Incluye dependencias transitivas, SAPI, configuración y supuestos explícitos. |
| **Priorización** | Ordena solo por CVSS. | Combina explotación real, exposición y criticidad. | Justifica y declara qué evidencia nueva cambiaría el orden. |
| **Mitigación y parche** | Aplica cambios sin verificar. | Verifica resultado y prueba funcional. | Incluye plan de reversión y ventana de cambio. |
| **Detección** | Copia reglas sin entenderlas. | Explica qué observa la regla y sus límites. | Propone mejoras y anticipa falsos positivos. |
| **Contención** | No aplica controles o no los prueba. | Verifica al menos 3 controles. | Conecta cada control con el daño que evita y mide el residual. |
| **Evidencia y honestidad** | Capturas sueltas o afirmaciones sin respaldo. | Evidencia clara, antes/después. | Declara lo que no pudo probar y por qué. |

**Puntaje del taller:** promedio de los 6 criterios, convertido a 35%.

---

## 3. Comité de crisis — criterios (20%)

| Criterio | Inicial (1) | Competente (3) | Destacado (5) |
|---|---|---|---|
| **Claridad ejecutiva** | Jerga sin destinatario. | Mensaje claro con decisión solicitada. | Adapta el mensaje a gerencia, clientes y técnicos. |
| **Manejo de objeciones** | Se defiende con generalidades. | Responde con evidencia y reconoce límites. | Cambia la decisión si la evidencia lo justifica. |
| **Priorización y plazos** | “Todo urgente”. | Ordena por riesgo y propone plazos. | Distingue parche, mitigación, compensación y aceptación del riesgo. |
| **Comunicación de crisis** | Detalles técnicos de más o de menos. | Explica impacto y próximos pasos sin dar armas al atacante. | Anticipa preguntas de clientes, prensa y regulador. |
| **Trabajo en equipo** | Habla uno solo. | Todos aportan. | Roles claros y traspaso de información. |

**Puntaje del comité:** promedio de los 5 criterios, convertido a 20%.

---

## 4. Informe individual (30%)

**Formato:** 2–3 páginas, dirigido a un gerente. **Entrega:** al finalizar la clase
del jueves 29/10.

| Criterio | Peso | Qué mira |
|---|---:|---|
| Situación y riesgo | 20% | Explica el caso asignado sin copiar el comunicado. |
| Plan priorizado | 30% | Acciones, plazos, dueños y reversión. |
| Indicadores de compromiso | 25% | Qué confirmaría/descartaría un incidente y con qué evidencia. |
| Transferencia | 15% | Aplica el marco a una vulnerabilidad **distinta** (ej. CVE-2026-115560). |
| Comunicación | 10% | Claro, breve, sin jerga innecesaria. |

---

## 5. Reglas de corrección

- **No se premia** haber ejecutado un exploit. Se premia **decidir y verificar**.
- **No se penaliza** declarar una limitación; se penaliza **inventar evidencia**.
- Un entregable sin **antes/después** o sin **reversión** no puede superar “competente”
  en mitigación/parche.
- Un comité que responde “parchamos todo” sin orden ni plazos no supera “inicial”.
- La entrega es **en el repo** (fork + PR o enlace). Un archivo fuera del repo no
  cuenta como entregado.
