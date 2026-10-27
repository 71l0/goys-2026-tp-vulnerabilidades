# Entregables

Cada grupo trabaja en su propia carpeta:

```
entregables/
├── plantilla.md
└── grupo-XX/
    ├── entrega.md
    ├── deteccion.md
    ├── informe.md
    └── capturas/        # opcional
```

## Cómo empezar

```bash
mkdir -p entregables/grupo-XX
cp entregables/plantilla.md entregables/grupo-XX/entrega.md
```

- **`entrega.md`:** inventario, matriz, cambios/parche, contención y conclusión.
  Se completa en clase.
- **`deteccion.md`:** análisis de `laboratorio/logs/audit.log`. Se entrega antes de
  la clase del jueves 29/10.
- **`informe.md`:** informe individual de 2–3 páginas. Se entrega al finalizar la
  clase del jueves 29/10.
- **`capturas/`:** opcional; si la usás, referenciá cada archivo en `entrega.md`.

## Reglas

- No commitees claves, tokens, `.env` ni `secret.yml`.
- No incluyas PoCs ni exploits.
- No dejes placeholders (`TODO`, `pendiente`, `<!-- -->`).
- Si no pudiste probar algo, escribilo y explicá por qué.

Checklist: [`../docs/checklist-entrega.md`](../docs/checklist-entrega.md).
