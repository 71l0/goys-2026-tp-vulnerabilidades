.PHONY: up down inventario parchear detectar contener logs

up:
	docker compose --env-file versions.env up -d --build

down:
	docker compose --env-file versions.env down -v

inventario:
	./scripts/inventario.sh

parchear:
	./scripts/parchear.sh

detectar:
	./scripts/detectar.sh

contener:
	./scripts/verificar-contencion.sh

logs:
	docker compose logs --tail=50
