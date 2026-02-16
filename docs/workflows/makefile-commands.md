# Makefile command reference

Use `make help` to list all available targets.

## Common daily commands

- `make doctor` — verify local tool availability.
- `make setup` — native bootstrap with host PHP/Composer.
- `make setup-docker` — bootstrap with Dockerized PHP/Composer.
- `make dev` — full Laravel foreground development stack.

## Background stack commands

- `make stack-up` — start Laravel (`pitstop-web`) + Vite in background.
- `make stack-status` — show process/container health.
- `make stack-logs` — tail Laravel container + Vite logs.
- `make stack-down` — stop background processes.
- `make stack-up STACK_APP_PORT=8088` — override app port when default is occupied.
- `make stack-up VITE_PORT=5175` — override Vite port when 5173 is occupied.

## Quality commands

- `make quality` — full local quality gate (host PHP + npm).
- `make quality-docker` — full quality gate with Dockerized PHP.
- `make lint-docker` — run Pint checks via Dockerized composer.

## Notes

- Background Vite PID is stored in `.pids/vite.pid`.
- Laravel app port for `stack-up` uses `STACK_APP_PORT` (default `8001`).
- Vite binds to `0.0.0.0` in stack mode so remote browsers can load dev assets.
