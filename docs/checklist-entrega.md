# Checklist de entrega — TP de vulnerabilidades

Marcá cada ítem antes de abrir el PR.

## Repo y proceso

- [ ] Forkeé el repo y cloné **mi fork**.
- [ ] Creé la rama `grupo-XX`.
- [ ] Copié `entregables/plantilla.md` a `entregables/grupo-XX/entrega.md`.
- [ ] Todos los integrantes aparecen con nombre y rol.
- [ ] Los commits son convencionales y por aporte.
- [ ] El repo no contiene claves, tokens, `.env` ni `secret.yml`.

## Laboratorio

- [ ] `./scripts/arrancar-2h.sh` levanta los 3 servicios `healthy`.
- [ ] `./scripts/inventario.sh` muestra las versiones reales.
- [ ] `./scripts/parchear.sh` aplica las versiones corregidas.
- [ ] La prueba funcional mínima pasa después del parche.
- [ ] `./scripts/verificar-contencion.sh` muestra `SIN SALIDA` en los 3 servicios.
- [ ] El laboratorio quedó apagado (`docker compose down -v`).

## Entregables

- [ ] `entrega.md` con inventario, matriz, cambios, contención y conclusión.
- [ ] `deteccion.md` con hipótesis, hallazgos, regla y limitaciones.
- [ ] `informe.md` individual, 2–3 páginas.
- [ ] Capturas o salidas referenciadas (si usaste `capturas/`).
- [ ] No hay texto “pendiente”, “TODO” o placeholders.
- [ ] Las tablas están completas y se entienden sin abrir el lab.

## Evidencia

- [ ] Cada afirmación técnica tiene un comando, una salida o una captura.
- [ ] Se distingue “probado” de “planeado”.
- [ ] Se declara lo que no se pudo probar y por qué.
- [ ] El change log incluye reversión.

## Entrega

- [ ] `git push origin grupo-XX`.
- [ ] PR abierto a `rodriguezemautn/goys-2026-tp-vulnerabilidades:main`.
- [ ] Título del PR: `[Grupo XX] Entrega TP vulnerabilidades`.
- [ ] Enlace del PR (o del fork) pegado en el aula virtual.
- [ ] Entregado **antes de salir del aula del jueves 29/10**.
