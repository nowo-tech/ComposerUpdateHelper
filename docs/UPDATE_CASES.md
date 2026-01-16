# Update Cases and Scenarios

This document explains the different update scenarios that Composer Update Helper currently handles, and identifies scenarios that are not yet fully supported.

## Currently Supported Cases

### 1. Basic Package Updates

**Scenario**: A package has a newer version available that is compatible with all current dependencies.

**Example**:
- Installed: `vendor/package:1.2.0`
- Latest: `vendor/package:1.3.0`
- Result: ✅ Update suggested

**Handling**: The system compares installed version with latest version and suggests update if newer.

---

### 2. Dependent Package Constraint Conflicts

**Scenario**: A package update is blocked because another installed package requires an older version.

**Example**:
- Trying to update: `rector/rector:2.3.1`
- Blocked by: `lexik/jwt-authentication-bundle` requires `rector/rector ^1.2`
- Result: ❌ Update filtered with clear message: `lexik/jwt-authentication-bundle requires rector/rector ^1.2`

**Handling**: 
- The system checks `composer.lock` for all packages that depend on the target package
- Verifies if the proposed version satisfies all dependent constraints
- If not satisfied, the update is filtered and the conflicting dependent is reported

**Status**: ✅ Fully supported

---

### 3. Package Requirement Conflicts

**Scenario**: A package update requires a newer version of a transitive dependency that is not installed or is outdated.

**Example**:
- Trying to update: `scheb/2fa-google-authenticator:8.2.0`
- Requires: `spomky-labs/otphp:^11.4`
- Installed: `spomky-labs/otphp:11.3.0`
- Result: ⚠️ Update filtered, transitive dependency suggested

**Handling**:
- The system checks all requirements of the proposed package version
- Verifies if installed versions satisfy those requirements
- If not satisfied, suggests updating the transitive dependency
- Generates a command that includes both packages

**Status**: ✅ Fully supported with transitive dependency suggestions

---

### 4. Self-Version Constraints

**Scenario**: A package requires another package at the exact same version (e.g., `self.version` constraint).

**Example**:
- Trying to update: `scheb/2fa-google-authenticator:8.2.0`
- Requires: `scheb/2fa-bundle: self.version` (means version 8.2.0)
- Installed: `scheb/2fa-bundle:8.1.0`
- Result: ⚠️ Update filtered, suggests updating `scheb/2fa-bundle:8.2.0` together

**Handling**:
- Detects `self.version` or `@self` constraints
- Extracts the required version from the proposing package version
- Suggests updating the related package to match the version
- Includes both packages in the same update command

**Status**: ✅ Fully supported

---

### 5. Framework Version Constraints

**Scenario**: A package update would exceed the framework version constraint (e.g., Symfony 7.4.* constraint).

**Example**:
- Framework constraint: `symfony:7.4.*`
- Trying to update: `symfony/console:8.0.0`
- Result: ✅ Update limited to `symfony/console:7.4.x` (latest in 7.4.* range)

**Handling**:
- Detects framework constraints from `composer.json` (`extra.symfony.require`) or installed core packages
- Limits package updates to versions compatible with the framework constraint
- Supported frameworks: Symfony, Laravel, Yii, CakePHP, Laminas, CodeIgniter, Slim

**Status**: ✅ Fully supported for major frameworks

---

### 6. Ignored Packages

**Scenario**: User explicitly ignores certain packages from updates via configuration.

**Example**:
```yaml
ignore:
  - doctrine/orm
  - symfony/security-bundle
```

**Handling**:
- Packages in the `ignore` list are excluded from update suggestions
- Still shown in output with available versions for information
- Configuration supports both `.yaml` and `.yml` extensions

**Status**: ✅ Fully supported

---

### 7. Force-Included Packages

**Scenario**: User wants to force-include packages even if they are in the ignore list.

**Example**:
```yaml
ignore:
  - symfony/*
include:
  - symfony/security-bundle  # Override ignore for this specific package
```

**Handling**:
- `include` section has priority over `ignore` section
- Allows fine-grained control: ignore framework packages but include specific ones

**Status**: ✅ Fully supported

---

### 8. Production vs Development Dependencies

**Scenario**: Separating production and development dependency updates.

**Example**:
- Production: `symfony/console:7.4.0`
- Development: `phpunit/phpunit:12.5.5`

**Handling**:
- Automatically categorizes packages as `(prod)` or `(dev)`
- Generates separate commands: `composer require` vs `composer require --dev`
- Maintains proper dependency separation

**Status**: ✅ Fully supported

---

### 9. Version Comparison to Avoid Unnecessary Updates

**Scenario**: Preventing suggestions for packages already at or above the target version.

**Example**:
- Installed: `vendor/package:2.0.0`
- Latest: `vendor/package:2.0.0` (or `1.9.0`)
- Result: ✅ No update suggested

**Handling**:
- Compares installed version with proposed version
- Skips if installed >= proposed version
- Only suggests actual updates

**Status**: ✅ Fully supported

---

### 10. Multiple Transitive Dependencies

**Scenario**: A package update requires updating multiple transitive dependencies.

**Example**:
- Trying to update: `scheb/2fa-bundle:8.2.0`
- Requires: `scheb/2fa-email:8.2.0` (self.version)
- Requires: `spomky-labs/otphp:^11.4`
- Result: ⚠️ Suggests updating all three packages together

**Handling**:
- Collects all required transitive updates
- Groups them by production/dev
- Generates commands that include all related packages
- Ensures all dependencies are updated together

**Status**: ✅ Fully supported

---

## Partially Supported Cases

### 11. Wildcard Version Constraints

**Scenario**: Package versions with wildcards (e.g., `7.4.*`, `^8.0`, `~1.2`).

**Example**:
- Framework constraint: `symfony:7.4.*`
- Latest version: `symfony/console:7.4.3`

**Handling**:
- ✅ Framework constraints with wildcards are supported
- ⚠️ Dependency checking is skipped for wildcard constraints (`*`, `^`, `~`)
- ⚠️ Only specific version constraints trigger dependency conflict detection

**Status**: ⚠️ Partially supported (dependency checking only for specific versions)

---

## Not Yet Supported Cases

### 12. Circular Dependency Conflicts

**Scenario**: Package A requires Package B at version X, while Package B requires Package A at version Y.

**Example**:
- `vendor/package-a:2.0` requires `vendor/package-b:^1.5`
- `vendor/package-b:1.6` requires `vendor/package-a:^2.1`
- Result: ❌ No detection or resolution strategy

**Current Behavior**: Each package would be filtered individually, not recognizing the circular nature.

**What's Needed**: 
- Detection of circular dependencies
- Resolution strategies (update both together, or suggest breaking the circular dependency)

**Priority**: Medium

---

### 13. Cascading Conflict Chains

**Scenario**: A conflict chain where Package A → B → C → D, and updating D requires updating C, B, and A.

**Example**:
- Update `package-d:4.0` requires `package-c:^3.0`
- Update `package-c:3.0` requires `package-b:^2.0`
- Update `package-b:2.0` requires `package-a:^1.5`
- All are currently at older versions

**Current Behavior**: 
- ✅ Transitive dependencies are suggested
- ❌ No automatic detection of the full chain depth
- ❌ No optimization to update all packages in the chain at once

**What's Needed**:
- Recursive traversal to detect full dependency chains
- Optimized command generation for multi-level chains
- Better visualization of dependency chains

**Priority**: Medium-High

---

### 14. Abandoned Package Conflicts

**Scenario**: A package is abandoned and conflicts with newer versions of dependencies, but no compatible version exists.

**Example**:
- `abandoned/package:1.0.0` requires `old/dependency:^2.0`
- `old/dependency` is at version 5.0.0, no longer supports ^2.0
- `abandoned/package` has no newer version

**Current Behavior**: 
- Update would be filtered with conflict message
- ❌ No suggestion to find alternatives or migrate away

**What's Needed**:
- Detection of abandoned packages
- Suggestions for alternative packages (via Packagist suggestions)
- Migration path recommendations

**Priority**: Low-Medium

---

### 15. Repository-Specific Conflicts

**Scenario**: Conflicts that only exist in private repositories or specific repository configurations.

**Current Behavior**: 
- ✅ Basic support for any Composer repository
- ❌ No special handling for repository-specific constraints
- ❌ No detection of repository priority conflicts

**What's Needed**:
- Better support for repository-specific version constraints
- Detection of conflicts between repositories
- Suggestions when conflicts might be resolved by adjusting repository priorities

**Priority**: Low

---

### 16. Batch Update Optimization

**Scenario**: Optimizing multiple package updates to minimize dependency resolution steps.

**Example**:
- 10 packages need updates
- Some updates depend on others
- Optimal strategy: group updates to minimize resolver runs

**Current Behavior**: 
- ✅ Suggests all compatible updates together
- ❌ No optimization for update order
- ❌ No batching strategy to minimize resolver calls

**What's Needed**:
- Dependency graph analysis
- Optimal update ordering algorithm
- Batching strategy to group updates efficiently

**Priority**: Medium

---

### 17. Pre-Installation Conflict Prediction

**Scenario**: Predicting conflicts before actually running `composer require`, using dry-run or simulation.

**Current Behavior**: 
- ✅ Uses `composer show` to check package requirements
- ❌ No actual dry-run of `composer require`
- ❌ May miss some edge cases that only appear during actual resolution

**What's Needed**:
- Integration with Composer's dependency resolver
- Dry-run simulation of updates
- More accurate conflict prediction

**Priority**: High (would improve accuracy significantly)

---

### 18. Alternative Package Suggestions

**Scenario**: When a package update is blocked by conflicts, suggest alternative packages that might work.

**Example**:
- `package-a:2.0` conflicts with `dependency-x`
- Alternative `package-b:2.0` might work with `dependency-x`

**Current Behavior**: 
- ❌ No alternative package detection
- ❌ No migration path suggestions

**What's Needed**:
- Package alternatives database (Packagist provides this)
- Migration path analysis
- Cost-benefit analysis of alternatives

**Priority**: Low

---

### 19. Conflict Resolution Strategies

**Scenario**: Providing multiple resolution strategies when conflicts are detected.

**Example**:
- Conflict: `package-a:2.0` conflicts with `dependency-x:1.0`
- Strategy 1: Update `dependency-x` to `2.0` (if compatible)
- Strategy 2: Keep `package-a:1.9` (if compatible with `dependency-x:1.0`)
- Strategy 3: Remove `dependency-x` (if not critical)

**Current Behavior**: 
- ✅ Suggests transitive dependency updates (Strategy 1)
- ❌ No fallback version suggestions (Strategy 2)
- ❌ No dependency removal suggestions (Strategy 3)

**What's Needed**:
- Multiple resolution strategy generation
- Evaluation of each strategy
- User choice of preferred strategy

**Priority**: Medium-High

---

### 20. Conflict Impact Analysis

**Scenario**: Analyzing the impact of resolving conflicts (which packages would be affected).

**Example**:
- Updating `dependency-x` to resolve conflict might affect 5 other packages
- Removing `dependency-x` might break 3 packages

**Current Behavior**: 
- ❌ No impact analysis
- ❌ No visualization of affected packages

**What's Needed**:
- Impact analysis before suggesting updates
- Visualization of dependency graph
- Warning about breaking changes

**Priority**: Medium

---

## Summary

### Fully Supported (10 cases)
1. Basic package updates
2. Dependent package constraint conflicts
3. Package requirement conflicts
4. Self-version constraints
5. Framework version constraints
6. Ignored packages
7. Force-included packages
8. Production vs development dependencies
9. Version comparison to avoid unnecessary updates
10. Multiple transitive dependencies

### Partially Supported (1 case)
11. Wildcard version constraints (dependency checking only for specific versions)

### Not Yet Supported (10 cases)
12. Circular dependency conflicts
13. Cascading conflict chains (optimization needed)
14. Abandoned package conflicts
15. Repository-specific conflicts
16. Batch update optimization
17. Pre-installation conflict prediction (needs Composer resolver integration)
18. Alternative package suggestions
19. Conflict resolution strategies (multiple strategies)
20. Conflict impact analysis

---

## Recommendations

### High Priority
- **Pre-installation conflict prediction** (#17): Would significantly improve accuracy by using Composer's resolver
- **Cascading conflict chains** (#13): Better handling of deep dependency chains
- **Conflict resolution strategies** (#19): Multiple strategies for better user experience

### Medium Priority
- **Batch update optimization** (#16): More efficient update commands
- **Conflict impact analysis** (#20): Better user awareness
- **Wildcard constraints dependency checking** (#11): Complete the partial support

### Low Priority
- **Circular dependency conflicts** (#12): Less common but useful
- **Alternative package suggestions** (#18): Nice-to-have feature
- **Abandoned package conflicts** (#14): Edge case
- **Repository-specific conflicts** (#15): Edge case

---

## Testing Recommendations

To ensure comprehensive coverage, test cases should be created for:

1. ✅ All currently supported cases (10 cases)
2. ⚠️ Edge cases in partially supported scenarios
3. 📝 Mock/test scenarios for not-yet-supported cases (for future development)

---

*Last updated: 2026-01-15*
*Document version: 1.0*
