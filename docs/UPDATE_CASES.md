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
- Result: ⚠️ System checks if `lexik/jwt-authentication-bundle` has a newer version that supports `rector/rector:2.3.1`
  - If newer compatible version found: ✅ Suggests updating both packages together
  - If not found: ❌ Update filtered with clear message: `lexik/jwt-authentication-bundle requires rector/rector ^1.2`

**Handling**: 
- The system checks `composer.lock` for all packages that depend on the target package
- Verifies if the proposed version satisfies all dependent constraints
- **NEW**: If not satisfied, automatically checks if dependent packages have newer versions that support the proposed update
- If compatible dependent version found, suggests updating both packages together
- If not found, the update is filtered and the conflicting dependent is reported
- Example: When `zircote/swagger-php:6.0.2` conflicts with `nelmio/api-doc-bundle` requiring `^4.11.1 || ^5.0`, the system checks if `nelmio/api-doc-bundle:6.0.0` exists and requires `zircote/swagger-php:^6.0`
  - If found: Suggests `composer require --with-all-dependencies zircote/swagger-php:6.0.2 nelmio/api-doc-bundle:6.0.0`

**Status**: ✅ Fully supported with automatic dependent package update detection

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

### 14. Conflict Impact Analysis

**Scenario**: Analyzing the impact of updating packages when conflicts are detected (which packages would be affected).

**Example**:
- Updating `package-a` to `2.0` conflicts with `dependency-x` which requires `^1.5`
- The analysis shows that `dependent-package-1` and `dependent-package-2` would be affected
- Transitive packages that depend on these affected packages are also identified

**Handling**:
- Automatically analyzes which packages would be affected by updating a conflicting package
- Shows direct affected packages (packages that directly depend on the conflicting package)
- Shows transitive affected packages (packages that depend on directly affected packages)
- Displays impact analysis in output with clear formatting
- Provides complete dependency chain visualization

**Implementation Details**:
- Uses `ImpactAnalyzer::analyzeImpact()` to analyze impact when conflicts are detected
- Recursively checks transitive dependencies (up to 5 levels deep to prevent infinite loops)
- Format: `📊 Impact analysis: Updating package-a to 2.0 would affect: - dependent-package-1 (requires package-a:^1.5)`
- Integrated into conflict detection flow

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
- ✅ **Dependency checking now supports wildcard constraints** (`*`, `^`, `~`)
- ✅ Full dependency conflict detection for wildcard constraints using `versionSatisfiesConstraint`

**Status**: ✅ Fully supported

---

## Not Yet Supported Cases

The following scenarios are **documented but not yet implemented**. When these cases occur, the tool will:

- ❌ **Filter packages** showing conflict messages
- ❌ **Not provide automatic solutions** - Requires manual intervention from the user  
- ⚠️ **Limited guidance** - User must analyze and resolve conflicts manually

Each case below explains what happens currently, what's missing, and what manual steps the user must take.

### 12. Circular Dependency Conflicts

**Scenario**: Package A requires Package B at version X, while Package B requires Package A at version Y.

**Example**:
- `vendor/package-a:2.0` requires `vendor/package-b:^1.5`
- `vendor/package-b:1.6` requires `vendor/package-a:^2.1`
- Result: ❌ **No solution provided** - Both packages filtered separately without recognizing the circular dependency

**Current Behavior**: 
- Each package would be filtered individually with separate conflict messages
- ❌ No recognition of the circular nature of the conflict
- ❌ No suggestion to update both packages together
- ❌ No strategy to break the circular dependency

**What's Needed**: 
- Detection of circular dependencies
- Resolution strategies (update both together, or suggest breaking the circular dependency)
- Warning messages explaining the circular nature

**Priority**: Medium

**Current Solution**: Manual intervention required - user must analyze conflicts and update packages manually

---

### 13. Cascading Conflict Chains

**Scenario**: A conflict chain where Package A → B → C → D, and updating D requires updating C, B, and A.

**Example**:
- Update `package-d:4.0` requires `package-c:^3.0`
- Update `package-c:3.0` requires `package-b:^2.0`
- Update `package-b:2.0` requires `package-a:^1.5`
- All are currently at older versions

**Current Behavior**: 
- ✅ Transitive dependencies are suggested (one level deep)
- ⚠️ **Partial solution** - Only immediate transitive dependencies are suggested
- ❌ No automatic detection of the full chain depth (A → B → C → D)
- ❌ No optimization to update all packages in the chain at once
- ❌ No visualization of the complete dependency chain

**What's Needed**:
- Recursive traversal to detect full dependency chains
- Optimized command generation for multi-level chains
- Better visualization of dependency chains

**Priority**: Medium-High

**Current Solution**: User must manually execute commands multiple times, one level at a time

---

### 14. Abandoned Package Conflicts

**Scenario**: A package is abandoned and conflicts with newer versions of dependencies, but no compatible version exists.

**Example**:
- `abandoned/package:1.0.0` requires `old/dependency:^2.0`
- `old/dependency` is at version 5.0.0, no longer supports ^2.0
- `abandoned/package` has no newer version

**Current Behavior**: 
- ✅ **Detection implemented** - Package abandonment status is detected via Packagist API
- ✅ **Warning shown** - Update is filtered with conflict message AND abandoned warning
- ✅ **Replacement suggested** - If Packagist provides a replacement, it's shown in the warning
- ⚠️ **Partial solution** - Warning is shown but no automatic migration path
- ❌ No migration path recommendations beyond replacement suggestion

**Implementation Details**:
- Uses Packagist API (`packagist.org/packages/{package}.json`) to check `abandoned` field
- Warning format: `(⚠️ Package is abandoned, replaced by: new-package/name)` if replacement exists
- Warning format: `(⚠️ Package is abandoned)` if no replacement is specified
- Integrated into conflict detection flow

**What's Still Needed**:
- Migration path recommendations (step-by-step guide)
- Automated replacement suggestions (beyond Packagist's replacement field)
- Alternative package discovery

**Priority**: Low-Medium (Partially implemented ✅)

**Current Solution**: ✅ Automatic detection and warning - user can see replacement package if available

---

### 15. Repository-Specific Conflicts

**Scenario**: Conflicts that only exist in private repositories or specific repository configurations.

**Current Behavior**: 
- ✅ Basic support for any Composer repository
- ❌ **No solution provided** - Conflicts from repository priorities are not detected
- ❌ No special handling for repository-specific constraints
- ❌ No detection of repository priority conflicts

**What's Needed**:
- Better support for repository-specific version constraints
- Detection of conflicts between repositories
- Suggestions when conflicts might be resolved by adjusting repository priorities

**Priority**: Low

**Current Solution**: Manual intervention - user must adjust repository priorities in `composer.json`

---

### 16. Batch Update Optimization

**Scenario**: Optimizing multiple package updates to minimize dependency resolution steps.

**Example**:
- 10 packages need updates
- Some updates depend on others
- Optimal strategy: group updates to minimize resolver runs

**Current Behavior**: 
- ✅ Suggests all compatible updates together (basic grouping)
- ⚠️ **Suboptimal solution** - All packages in one command may be inefficient
- ❌ No optimization for update order
- ❌ No batching strategy to minimize resolver calls
- ❌ No analysis of dependency graph to optimize execution

**What's Needed**:
- Dependency graph analysis
- Optimal update ordering algorithm
- Batching strategy to group updates efficiently

**Priority**: Medium

**Current Solution**: Works but may be slower - all packages updated in one command regardless of dependencies

---

### 17. Pre-Installation Conflict Prediction

**Scenario**: Predicting conflicts before actually running `composer require`, using dry-run or simulation.

**Current Behavior**: 
- ✅ Uses `composer show` to check package requirements (good approximation)
- ⚠️ **Partial solution** - May miss edge cases that only appear during actual resolution
- ❌ No actual dry-run of `composer require`
- ❌ Some conflicts may only be detected when Composer actually resolves dependencies
- ❌ False positives/negatives possible

**What's Needed**:
- Integration with Composer's dependency resolver
- Dry-run simulation of updates (`composer require --dry-run`)
- More accurate conflict prediction

**Priority**: High (would improve accuracy significantly)

**Current Solution**: Good approximation but not 100% accurate - some conflicts may only appear during actual `composer require`

---

### 18. Alternative Package Suggestions

**Scenario**: When a package update is blocked by conflicts, suggest alternative packages that might work.

**Example**:
- `package-a:2.0` conflicts with `dependency-x`
- Alternative `package-b:2.0` might work with `dependency-x`

**Current Behavior**: 
- ✅ **Alternative package detection** - **Now implemented**
  - Automatically searches for alternative packages when conflicts exist
  - Uses Packagist API to find similar packages based on keywords
  - Shows alternatives when packages are abandoned without replacement
  - Shows alternatives when no fallback version is available
  - Example: `💡 Alternative packages: - new-package/name (recommended replacement) - alternative/pkg (similar functionality)`
- ✅ Migration path suggestions via Packagist API search
- ✅ Package discovery functionality

**Implementation Details**:
- `AlternativePackageFinder::findAlternatives()` function searches for alternatives
- Checks abandoned status first (if abandoned with replacement, shows replacement)
- Searches Packagist for similar packages using keywords extracted from package name
- Returns top 3 most relevant alternatives
- Output includes alternative packages section when available

**What's Still Needed**:
- Cost-benefit analysis of alternatives
- Version compatibility checking for alternatives
- Migration guide generation

**Priority**: Low (Partially implemented ✅)

**Current Solution**: ✅ Alternative packages are automatically suggested when conflicts exist

---

### 19. Conflict Resolution Strategies

**Scenario**: Providing multiple resolution strategies when conflicts are detected.

**Example**:
- Conflict: `package-a:2.0` conflicts with `dependency-x:1.0`
- Strategy 1: Update `dependency-x` to `2.0` (if compatible) ✅ Supported
- Strategy 2: Keep `package-a:1.9` (if compatible with `dependency-x:1.0`) ✅ **Now supported**
- Strategy 3: Remove `dependency-x` (if not critical) ❌ Not supported

**Current Behavior**: 
- ✅ Suggests transitive dependency updates (Strategy 1) - **Implemented**
- ✅ **Dependent package update detection (Strategy 1b)** - **Now implemented**
  - When a package conflicts with a dependent package, automatically checks if the dependent package has a newer version that supports the proposed update
  - If found, suggests updating both packages together
  - Example: `composer require --with-all-dependencies package-a:2.0 dependent-package:3.0`
- ✅ **Fallback version suggestions (Strategy 2)** - **Implemented**
  - Automatically searches for compatible older versions when primary update conflicts
  - Verifies fallback versions satisfy all conflicting dependencies
  - Shows "Alternative solutions" section in output
  - Example: `💡 Alternative solutions: - package-a:1.9.5 (compatible with conflicting dependencies)`
- ❌ No dependency removal suggestions (Strategy 3)
- ⚠️ **Partial implementation** - Three strategies available (transitive updates, dependent updates, fallback versions), removal suggestions still missing

**Implementation Details**:
- `VersionResolver::findCompatibleDependentVersions()` searches for newer versions of conflicting dependent packages
  - Checks if dependent packages have versions that support the proposed update
  - If found, adds them to transitive updates to be included in the same command
- `findFallbackVersion()` function searches for compatible older versions
- Verifies fallback satisfies all conflicting constraints
- Verifies fallback's own requirements don't conflict with installed packages
- Output includes both dependent updates and fallback suggestions when available

**What's Still Needed**:
- Dependency removal suggestions (Strategy 3)
- Multiple strategy comparison
- User choice of preferred strategy

**Priority**: Medium-High (Partially implemented ✅)

**Current Solution**: ✅ Three strategies available - transitive updates, dependent package updates, and fallback versions

---

## Case #20: Edge Cases - No Compatible Versions Available

**Scenario**: A package update conflicts with dependent packages, but no compatible versions of the dependent packages exist that support the proposed update.

**Example Cases**:
1. `zircote/swagger-php:6.0.2` conflicts with `nelmio/api-doc-bundle` requiring `^4.11.1 || ^5.0`
   - No version of `nelmio/api-doc-bundle` exists that supports `zircote/swagger-php:^6.0`
   - All available versions (up to 5.9.2) require `^4.11.1 || ^5.0`

2. `phpdocumentor/reflection-docblock:6.0.1` conflicts with `a2lix/auto-form-bundle:1.0.0` requiring `^5.6`
   - No version of `a2lix/auto-form-bundle` exists that supports `phpdocumentor/reflection-docblock:^6.0`
   - Latest version (1.0.0) requires `^5.6`

**Current Behavior**:
- ✅ System automatically searches for compatible versions of dependent packages
- ✅ System checks all available versions of conflicting dependents
- ✅ If no compatible versions found, explains why no automatic solution is available
- ✅ Suggests fallback versions of the original package (if available)
- ✅ Suggests alternative packages (if available)
- ✅ Provides maintainer contact information for manual resolution

**What Happens**:
1. System detects conflict with dependent package
2. System searches for newer versions of dependent package that support the proposed update
3. If no compatible versions found:
   - Package is filtered (not included in update commands)
   - Clear explanation is shown: "No compatible version of {dependent-package} found that supports {package}:{version}"
   - Fallback version suggestions are shown (if available)
   - Alternative package suggestions are shown (if available)
   - Maintainer contact information is provided (if available)

**Output Example**:
```
⚠️  Filtered by dependency conflicts:
     - zircote/swagger-php:6.0.2 (prod) (conflicts with 1 package: nelmio/api-doc-bundle requires zircote/swagger-php ^4.11.1 || ^5.0)
     ℹ️  No compatible version of nelmio/api-doc-bundle found that supports zircote/swagger-php:6.0.2
     ℹ️  All available versions of nelmio/api-doc-bundle require zircote/swagger-php:^4.11.1 || ^5.0

💡 Alternative solutions:
     - zircote/swagger-php:5.7.8 (compatible with conflicting dependencies)

💡 Alternative packages:
     - [alternative suggestions if available]

📧 Contact maintainers:
     - [maintainer contact information if available]
```

**Why This Happens**:
- Package maintainers haven't released versions that support the newer dependency versions yet
- Breaking changes in the dependency require significant updates to the dependent package
- Dependent package may be abandoned or in maintenance mode
- Version compatibility matrix hasn't been updated by maintainers

**What You Can Do**:
1. **Use fallback versions**: Use the suggested fallback version of the original package (e.g., `zircote/swagger-php:5.7.8` instead of `6.0.2`)
2. **Wait for updates**: Monitor the dependent package for new releases that support the newer version
3. **Contact maintainers**: Use the provided contact information to request compatibility updates
4. **Consider alternatives**: Evaluate alternative packages that may have better compatibility
5. **Manual resolution**: Manually update dependencies if you have the resources to handle potential breaking changes

**Status**: ✅ Fully supported with clear explanations and actionable suggestions

**Priority**: High (User experience improvement)

---

## Summary

### Fully Supported (15 cases)
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
11. Wildcard version constraints (dependency checking for `*`, `^`, `~` constraints)
12. Abandoned package detection (with warnings and replacement suggestions)
13. Alternative package suggestions (automatic detection and suggestions when conflicts exist)
14. Maintainer contact suggestions (automatic detection and actionable steps when no automatic solution is available)
15. Conflict impact analysis (automatic analysis of which packages would be affected by resolving conflicts)

### Partially Supported (2 cases)
14. Abandoned package conflicts - Detection and warnings ✅, migration paths ✅ (alternative packages, maintainer contact)
15. Conflict resolution strategies - Transitive updates ✅, fallback versions ✅, removal suggestions ❌

### Not Yet Supported (4 cases)
**❌ No automatic solution provided - requires manual intervention:**

17. **Circular dependency conflicts** - No detection or resolution strategy
18. **Cascading conflict chains** - Only one-level deep (optimization needed)
19. **Repository-specific conflicts** - No repository priority conflict detection
20. **Batch update optimization** - No update order optimization (suboptimal)
21. **Pre-installation conflict prediction** - Approximate only (needs Composer resolver)

---

## Recommendations

### High Priority
- **Pre-installation conflict prediction** (#17): Would significantly improve accuracy by using Composer's resolver
- **Cascading conflict chains** (#13): Better handling of deep dependency chains
- **Conflict resolution strategies** (#19): Multiple strategies for better user experience

### Medium Priority
- **Batch update optimization** (#16): More efficient update commands
- **Wildcard constraints dependency checking** (#11): Complete the partial support

### Low Priority
- **Circular dependency conflicts** (#12): Less common but useful
- **Abandoned package conflicts** (#14): Edge case (partially supported with alternative packages)
- **Repository-specific conflicts** (#15): Edge case

---

## Testing Recommendations

To ensure comprehensive coverage, test cases should be created for:

1. ✅ All currently supported cases (10 cases)
2. ⚠️ Edge cases in partially supported scenarios
3. 📝 Mock/test scenarios for not-yet-supported cases (for future development)

---

---

## Manual Intervention: Contacting Package Maintainers

Some scenarios **cannot be resolved automatically** and require **contacting package maintainers** to request dependency updates:

### When Manual Contact is Required

1. **Incompatible Version Requirements**
   - Two packages require incompatible versions of the same dependency
   - Example: Package A requires `dependency-x:^1.0`, Package B requires `dependency-x:^2.0`
   - No version satisfies both constraints
   - **Action**: Contact maintainer(s) of Package A or B to update their version constraints

2. **Stale Packages**
   - Package hasn't been updated in >2 years
   - Requires very old dependencies (e.g., Symfony 2.x when 6.x is available)
   - **Action**: Contact maintainer to request dependency updates or find alternative package

3. **Abandoned Packages Without Replacement**
   - Package is marked as abandoned
   - No replacement package is suggested
   - **Action**: Contact original maintainer or search for community fork

4. **Conflicting Major Versions**
   - Constraints on different major versions with no overlap
   - Example: Package A allows `^1.0|^2.0`, Package B requires `^3.0`
   - **Action**: Contact maintainer(s) to update version constraints

**Current Behavior**: 
- ✅ **Maintainer contact suggestions** - **Now implemented**
  - Automatically detects scenarios where manual intervention is needed
  - Extracts maintainer information from Packagist API (name, email, homepage)
  - Generates repository issue URLs for GitHub, GitLab, and Bitbucket
  - Detects stale packages (>2 years without updates)
  - Provides actionable steps for manual resolution
  - Example: `⚠️ No automatic solution available - Contact package maintainer(s): John Doe (john@example.com)`
- ✅ Automatic detection of incompatible constraints and major version conflicts
- ✅ Stale package warnings with actionable suggestions

**Implementation Details**:
- `MaintainerContactFinder::getMaintainerInfo()` function extracts maintainer information
- `MaintainerContactFinder::shouldSuggestContact()` determines when to suggest contact
- `MaintainerContactFinder::generateIssueUrl()` creates repository issue URLs
- Output includes maintainer contact section when no automatic solution is available

---

## Implementation Roadmap

For a detailed action plan to implement the not-yet-supported cases, ordered by complexity and feasibility, see [Implementation Roadmap](IMPLEMENTATION_ROADMAP.md).

### Translation Requirements

**⚠️ Important**: All new features implemented must include translations for all 31 supported languages. See the [Translation Requirements](IMPLEMENTATION_ROADMAP.md#translation-requirements) section in the Implementation Roadmap for details.

**Translation Support:** 
- 31 languages fully supported
- See [CONFIGURATION.md](CONFIGURATION.md#language-configuration-internationalization) for complete language list and configuration
- See [I18N_STRATEGY.md](I18N_STRATEGY.md) for translation implementation details

---

*Last updated: 2026-01-16*
*Document version: 1.0*
