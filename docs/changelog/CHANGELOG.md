# Changelog

All notable changes should be documented in this file.

## Unreleased

Recent baseline commits reviewed during this doc pass:

- `72b83e8` — governance/docs/tooling phase-two rollout
- `733bb64` — lint-green pass, auth test alignment, and Vite manifest coverage

### Added

- Repository governance baseline (`.github` workflows/templates and `CODEOWNERS`)
- Docs taxonomy under `docs/` with root document policy
- Workspace-level editor/task recommendations in `pitstop.code-workspace`
- Lint/format tooling for PHP, JS, CSS, and markdown/config scope
- Comprehensive `Makefile` command surface with bootstrap, quality, and Docker fallbacks
- Background full-stack launch targets: `stack-up`, `stack-down`, `stack-status`, `stack-logs`
- Environment-driven CORS configuration in `config/cors.php` for remote host access

### Changed

- PHP codebase auto-formatted to make `composer lint` pass cleanly
- Auth tests aligned to project-specific behavior (`home` redirect and allowed signup domain)
- Vite inputs expanded so all page-specific CSS entries are available in build manifest
- Setup/onboarding/workflow documentation updated to reflect Makefile-first workflows
- `make stack-up` now prints remote-access URLs and fails fast when selected app port is already occupied
- Background Vite stack mode now binds on `0.0.0.0` for remote browser asset access
