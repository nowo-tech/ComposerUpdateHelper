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

---

## Requirements

### Plugin (`src/`)

- **FR-PLUGIN-001**: `Plugin` — Composer plugin lifecycle and script registration.
- **FR-INST-001**: `Installer` — copy/update helper scripts and default YAML ignore list.

### Shipped runtime (documented, outside `src/` inventory)

- `bin/generate-composer-require.sh`, `bin/process-updates.php`, `bin/lib/*` (VersionResolver, DependencyAnalyzer, i18n loader, etc.).

---

## Success Criteria

- **SC-001**: **2/2** `src/` files mapped.
- **SC-002**: Install + script smoke test documented.

---

## Explicit non-goals

- Executing `composer update` automatically without integrator approval.
- Symfony bundle integration.

---

## Validation

PHPUnit for plugin/installer, inventory audit.
