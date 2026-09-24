# FrankenPHP worker mode audit (kernel not reset between requests)

| Field | Value |
|-------|-------|
| Package | `nowo-tech/composer-update-helper` (`composer-plugin`) |
| Audited revision | `v2.0.37` |
| Audit date | 2026-09-24 |
| Method | Manual review of every file under `src/` (`Plugin.php`, `Installer.php`, `SafeFileReader.php`); `bin/process-updates.php`, `bin/lib/*.php`; PHPStan with `ruleset-classic`, `ruleset-worker`, and `ruleset-worker-strict` on `src/` + `tests/Unit` |
| **Verdict** | **Pass (100% compatible)** under scenario **B** (kernel not reset between requests). The package never executes inside the FrankenPHP HTTP worker; shipped `src/` is free of worker anti-patterns, and CLI helpers under `bin/lib` were hardened so they remain safe if mistakenly loaded into a long-lived process |

## Execution model assumed

FrankenPHP worker mode boots the Symfony kernel once per worker and serves many requests with the same container. This audit assumes the **strict** variant: the kernel is **not** rebooted between requests, so every shared service, static property and PHP global survives from one request to the next. Two scenarios are evaluated:

- **A — kernel not rebooted, `services_resetter` still runs:** services tagged `kernel.reset` (or implementing `ResetInterface`) are reset between requests.
- **B — no reset at all:** nothing is reset; any per-request state kept in a service leaks into the next request.

A package that is safe under **B** is safe under **A** and under classic mode / PHP-FPM.

## Why it does not run in the HTTP worker

- `composer.json` declares `"type": "composer-plugin"` with `extra.class = NowoTech\ComposerUpdateHelper\Plugin` and only requires `php` and `composer-plugin-api`. The PSR-4 autoload covers only `src/` (`NowoTech\ComposerUpdateHelper\`). There is no `Bundle` class, DI extension, service config, route, listener or Twig extension.
- `src/Plugin.php` is instantiated by Composer and reacts to `post-install-cmd` / `post-update-cmd` and `uninstall()`, copying the shell wrapper / YAML and editing `.gitignore`. `src/Installer.php` offers the same through static methods for Composer `scripts`. `SafeFileReader` is a pure static helper (no mutable state).
- `bin/process-updates.php` is a procedural CLI script launched by the shell wrapper. It reads env vars (`OUTDATED_JSON`, `CONFIG_FILE`, …) and calls `exit()`. The `bin/lib/*.php` classes live in the **global** namespace and are loaded only via `require_once` from `bin/lib/autoload.php`; they are **not** in Composer's autoload map, so a Symfony / FrankenPHP application never loads them unless an integrator does so explicitly.

## Summary

| Area | Status | Notes |
|------|--------|-------|
| Mutable state in shared services | ✅ N/A | No container services. `Plugin::$composer` is set once in `activate()` inside the short-lived Composer process |
| Static properties / `static` locals | ✅ | None mutable in `src/`. CLI: `FrameworkDetector` uses `private const FRAMEWORK_CONFIGS`; progress dedup is caller-owned (`$progressShown` in `process-updates.php`), not a mutable static |
| `ResetInterface` / `kernel.reset` coverage | ✅ N/A | Nothing to reset; scenario B safe by absence of shared services |
| Request / user / locale captured in services | ✅ N/A | No HTTP code |
| Superglobals, `$_ENV`, `putenv`, `ini_set`, `setlocale`, timezone | ✅ | `getenv()` only in the CLI entrypoint; no `putenv` / `ini_set` / locale mutation in `src/` or `bin/lib` helpers |
| Doctrine / EntityManager | ✅ N/A | No persistence |
| Output, headers, `exit`, shutdown functions | ✅ | `exit()` only in CLI entrypoint; plugin writes through Composer's `IOInterface` |
| Resources (files, sockets, cURL) held open | ✅ | One-shot file IO in the plugin; CLI HTTP uses per-call stream contexts with timeout (`HttpClientDefaults::TIMEOUT_SECONDS = 5`) |
| Memory growth across requests | ✅ N/A | Short-lived Composer / CLI processes |
| Blocking I/O and timeouts | ✅ | Packagist/GitHub lookups share `HttpClientDefaults::streamContext()` (REQ-RUNTIME-001) |
| Third-party static state | ✅ N/A | Only Composer plugin API at install time |
| PHPStan FrankenPHP rulesets | ✅ | `ruleset-classic.neon`, `ruleset-worker.neon`, and `ruleset-worker-strict.neon` included in `phpstan.neon.dist` |

Worker demo: none (no `demo/` directory; expected for a Composer plugin).

## Services reviewed

| Service | Shared | Mutable state | Scenario A | Scenario B |
|---------|--------|---------------|------------|------------|
| — (no Symfony services) | — | — | N/A | N/A |
| `NowoTech\ComposerUpdateHelper\Plugin` | Composer process only | `$composer` set in `activate()` | N/A | ✅ |
| `Installer`, `SafeFileReader` | — | none | N/A | ✅ |
| `bin/lib/*` (CLI only, not autoloaded) | CLI process only | no mutable statics after 2.0.37 | N/A | ✅ if loaded |

## Findings

### Closed in 2.0.37

| ID | Was | Resolution |
|----|-----|------------|
| W-01 | `Utils::$progressMessagesShown` mutable static; `FrameworkDetector::$frameworkConfigs` mutable static; `Utils::buildComposerCommand()` used `define()` | Progress dedup via `$progressShown` by-ref; framework map is `private const`; composer command strings are class constants |

No open worker-mode findings under scenario B.

## Usage recommendations in worker mode

- Install as a **dev** dependency (`composer require --dev nowo-tech/composer-update-helper`); it has no effect on FrankenPHP workers.
- Keep `generate-composer-require.sh` and its YAML config in the project root, outside `public/`.
- **Do not** call `Plugin`, `Installer`, or `bin/lib` classes from controllers, listeners, or shared services.

## Re-audit triggers

Re-run this audit if the package gains a Symfony bundle class, a DI extension, adds `bin/lib` to the Composer autoload, or exposes PHP classes meant to be called during HTTP requests.
