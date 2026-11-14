# Feature Specification: ComposerUpdateHelper baseline (100% code coverage)

**Feature Branch**: `001-baseline`  
**Status**: Active  

**Package**: `nowo-tech/composer-update-helper`  
**Code inventory**: [`code-inventory.md`](code-inventory.md)

---

## Summary

Composer **plugin** that installs **`generate-composer-require.sh`** and PHP libraries for analyzing `composer outdated` output, resolving safe version bumps, i18n help text, and batch update recommendations.

---

## User Scenarios

### US-01 — Script installation (P1)

**Given** plugin requirement, **When** Composer installs the package, **Then** `Installer`/`Plugin` place scripts and config into the project.

### US-02 — Update analysis (P1)

**Given** outdated packages, **When** integrator runs the shell entrypoint, **Then** `bin/lib/*` analyzers produce require commands and impact notes (documented in README, not in `src/` inventory).

### US-03 — Ignore / include list with inline comments (P1)

**Given** `generate-composer-require.yaml` entries such as `- vendor/package  # reason`, **When** the integrator runs the shell entrypoint, **Then** `vendor/package` is listed under "Ignored packages" (or force-included) and is not suggested in the `composer require` commands.

---

## Requirements

### Plugin (`src/`)

- **FR-PLUGIN-001**: `Plugin` — Composer plugin lifecycle and script registration.
- **FR-INST-001**: `Installer` — copy/update helper scripts and default YAML ignore list.

### Shipped runtime (documented, outside `src/` inventory)

- `bin/generate-composer-require.sh`, `bin/process-updates.php`, `bin/lib/*` (VersionResolver, DependencyAnalyzer, i18n loader, etc.).
- **FR-CFG-001**: `bin/lib/ConfigLoader` — `readPackagesFromYaml()`, `readConfigValue()` and the legacy `readPackagesFromTxt()` strip YAML-style trailing comments (`#` preceded by whitespace, outside quotes) and surrounding quotes. `vendor/hash#name` (no whitespace before `#`) and `'a # b'` (quoted) are kept intact; a quoted scalar is returned as a string (no bool/number coercion). Covered by `tests/Unit/ConfigLoaderTest.php`; `bin/lib/ConfigLoader.php` is in PHPStan `bootstrapFiles`.

---

## Success Criteria

- **SC-001**: **3/3** `src/` files mapped.
- **SC-002**: Install + script smoke test documented.
- **SC-003**: Every `ignore` / `include` format shown in `docs/CONFIGURATION.md` (including inline comments) is honoured by `ConfigLoader` (FR-CFG-001).

---

## Explicit non-goals

- Executing `composer update` automatically without integrator approval.
- Symfony bundle integration.

---

## Validation

PHPUnit for plugin/installer and `bin/lib/ConfigLoader` (`tests/Unit/ConfigLoaderTest.php`), inventory audit.
