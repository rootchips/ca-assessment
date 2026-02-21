.PHONY: setup reset help docker

setup:
	@$(MAKE) docker up

reset:
	@$(MAKE) docker rebuild

help:
	@echo "Quick start (no config): make setup"
	@echo "Reset from scratch: make reset"
	@echo "Docker commands: make docker [up|build|down|restart|rebuild|logs|logs-clean]"

docker:
	@cmd="$(word 2,$(MAKECMDGOALS))"; \
	if [ -z "$$cmd" ]; then \
		echo "Usage: make docker [up|build|down|restart|rebuild|logs|logs-clean]"; \
		exit 1; \
	fi; \
	web_port="$${FRONTEND_HOST_PORT:-45173}"; \
	api_port="$${BACKEND_HOST_PORT:-48000}"; \
	ws_port="$${REVERB_HOST_PORT:-48080}"; \
	project_name="$${COMPOSE_PROJECT_NAME:-$$(basename "$$(pwd)")}"; \
	stale_backend_name="$$project_name-backend-1"; \
	case "$$cmd" in \
		up) docker rm -f "$$stale_backend_name" >/dev/null 2>&1 || true; docker compose rm -sf backend >/dev/null 2>&1 || true; docker compose up -d --remove-orphans && docker compose exec backend php artisan db:seed --force; echo "WEB: http://localhost:$$web_port"; echo "API: http://localhost:$$api_port"; echo "WebSocket: ws://localhost:$$ws_port" ;; \
		build) docker compose build ;; \
		down) docker compose down ;; \
		restart) docker compose restart ;; \
		rebuild) docker compose down && docker compose build --no-cache && (docker rm -f "$$stale_backend_name" >/dev/null 2>&1 || true) && (docker compose rm -sf backend >/dev/null 2>&1 || true); docker compose up -d --remove-orphans && docker compose exec backend php artisan migrate --force && docker compose exec backend php artisan db:seed --force && docker compose exec backend sh -lc ": > storage/logs/laravel.log"; echo "WEB: http://localhost:$$web_port"; echo "API: http://localhost:$$api_port"; echo "WebSocket: ws://localhost:$$ws_port" ;; \
		logs) docker compose exec backend sh -lc "tail -n 200 storage/logs/laravel.log" ;; \
		logs-clean) docker compose exec backend sh -lc ": > storage/logs/laravel.log && tail -n 20 storage/logs/laravel.log" ;; \
		*) echo "Unknown docker command: $$cmd"; echo "Usage: make docker [up|build|down|restart|rebuild|logs|logs-clean]"; exit 1 ;; \
	esac

%:
	@:
