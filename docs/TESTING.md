# Testing Documentation

This document provides comprehensive information about the test suite for Composer Update Helper, including tests for the new features implemented in Phase 1.

## Test Structure

The test suite is organized into several test classes:

- **`DependencyCompatibilityTest.php`**: Tests for dependency compatibility checking, conflict detection, and new features (abandoned packages, fallback versions)
- **`InstallerTest.php`**: Tests for installation and migration functionality
- **`PluginTest.php`**: Tests for Composer plugin integration
- **`ScriptTest.php`**: Tests for shell script functionality

## Running Tests

### All Tests

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run tests with specific PHPUnit options
vendor/bin/phpunit
```

### Specific Test Classes

```bash
# Run only dependency compatibility tests
vendor/bin/phpunit tests/DependencyCompatibilityTest.php

# Run with verbose output
vendor/bin/phpunit --verbose tests/DependencyCompatibilityTest.php

# Run specific test method
vendor/bin/phpunit --filter testIsPackageAbandoned tests/DependencyCompatibilityTest.php
```

## Test Coverage

The test suite maintains **99% code coverage** requirement. Current coverage is **99.20%**.

### Coverage Report

```bash
# Generate coverage report
composer test-coverage

# Open coverage report in browser
open coverage/index.html  # macOS
xdg-open coverage/index.html  # Linux
```

## New Feature Tests (Phase 1)

### Abandoned Package Detection Tests

#### Test: `testIsPackageAbandoned()`

**Purpose**: Verify abandoned package detection logic.

**What it tests**:
- Function exists and is callable
- Graceful error handling when API is unavailable
- Integration with conflict detection flow

**Test Data**:
- Uses actual Packagist API structure
- Tests both abandoned and non-abandoned packages

**Expected Behavior**:
- Returns `null` on API errors (graceful degradation)
- Returns array with `abandoned` (bool) and `replacement` (string|null) on success
- Integrates correctly with conflict detection output

#### Test: `testIsPackageAbandonedWithReplacement()`

**Purpose**: Verify abandoned package detection with replacement package.

**What it tests**:
- Correct parsing of Packagist's `abandoned` field
- Handling of replacement package information
- Handling of abandoned packages without replacements

**Test Data**:
```php
$abandonedInfo = [
    'abandoned' => true,
    'replacement' => 'new/package-name'
];
```

**Expected Behavior**:
- Correctly identifies abandoned status
- Extracts replacement package name when available
- Handles `null` replacement gracefully

### Fallback Version Tests

#### Test: `testFindFallbackVersionLogic()`

**Purpose**: Verify fallback version search logic.

**What it tests**:
- Version constraint satisfaction for fallback versions
- Correct identification of compatible older versions
- Rejection of incompatible versions

**Test Scenario**:
- Target version: `2.0.0`
- Conflicting constraint: `^1.5`
- Expected fallback: `1.9.5` or similar (satisfies `^1.5`)

**Test Cases**:
- ✅ `1.9.5` satisfies `^1.5` (valid fallback)
- ✅ `1.8.0` satisfies `^1.5` (valid fallback)
- ✅ `1.5.0` satisfies `^1.5` (valid fallback)
- ❌ `2.0.0` does NOT satisfy `^1.5` (needs fallback)
- ❌ `1.4.0` does NOT satisfy `^1.5` (too old)

**Expected Behavior**:
- Correctly identifies versions that satisfy conflicting constraints
- Rejects versions that don't satisfy constraints
- Finds highest compatible version below target

#### Test: `testFindFallbackVersionWithMultipleConstraints()`

**Purpose**: Verify fallback version satisfies ALL conflicting constraints.

**What it tests**:
- Handling multiple conflicting constraints
- Version must satisfy all constraints simultaneously
- Rejection if version satisfies only some constraints

**Test Scenario**:
- Constraint 1: `^1.5`
- Constraint 2: `^1.6`
- Expected fallback: `>= 1.6.0` and `< 2.0.0`

**Test Cases**:
- ✅ `1.6.5` satisfies both `^1.5` and `^1.6` (valid fallback)
- ❌ `1.5.5` satisfies `^1.5` but NOT `^1.6` (invalid fallback)
- ❌ `2.0.0` satisfies neither (invalid fallback)

**Expected Behavior**:
- Fallback must satisfy ALL conflicting constraints
- Versions satisfying only some constraints are rejected

#### Test: `testFindFallbackVersionIsLowerThanTarget()`

**Purpose**: Verify fallback versions are always older than target.

**What it tests**:
- Fallback version must be `< target version`
- Versions `>= target` are not valid fallbacks
- Version comparison logic

**Test Data**:
- Target: `2.0.0`
- Valid fallbacks: `1.9.9`, `1.8.0`, `1.5.0`, `1.0.0`
- Invalid fallbacks: `2.0.0`, `2.0.1`, `3.0.0`

**Expected Behavior**:
- Only versions less than target are considered
- Version comparison uses proper semver logic

#### Test: `testFindFallbackVersionEdgeCases()`

**Purpose**: Verify fallback version search handles edge cases.

**What it tests**:
- Empty conflicting dependents (should not search)
- Descending version order (finds highest compatible first)
- No fallback found scenario

**Test Cases**:
- Empty conflicts array → no search performed
- Version sorting → descending order (highest first)
- No compatible version → returns `null`

**Expected Behavior**:
- Graceful handling of edge cases
- Correct search order (highest compatible version first)
- Proper null handling when no fallback exists

## Integration Tests

### Testing Abandoned Package Detection in Output

To test abandoned package detection in the actual output:

1. Use a known abandoned package (e.g., `symfony/polyfill-php54`)
2. Create a conflict scenario where the package is filtered
3. Verify output contains abandoned warning
4. Verify replacement package is shown if available

**Example Test Command**:
```bash
# Set up project with abandoned package
composer require symfony/polyfill-php54:^1.0

# Run update helper (should show abandoned warning)
./generate-composer-require.sh --debug
```

### Testing Fallback Version Suggestions in Output

To test fallback version suggestions in the actual output:

1. Set up a package with version history (e.g., `doctrine/orm`)
2. Create a conflict where newer version doesn't satisfy constraints
3. Verify output contains "Alternative solutions" section
4. Verify fallback version is shown and is compatible

**Example Test Command**:
```bash
# Set up project with conflicting dependencies
# Run update helper (should show fallback suggestions)
./generate-composer-require.sh --debug
```

## Test Files Structure

```
tests/
├── DependencyCompatibilityTest.php    # New feature tests + existing compatibility tests
├── InstallerTest.php                  # Installation tests
├── PluginTest.php                     # Plugin integration tests
├── ScriptTest.php                     # Shell script tests
├── check-coverage.php                 # Coverage validation script
└── test-*.sh                          # Shell script tests
```

## Writing New Tests

### Test Class Structure

```php
<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests;

use PHPUnit\Framework\TestCase;

final class DependencyCompatibilityTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/composer-update-helper-test-' . uniqid();
        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }

    public function testNewFeature(): void
    {
        // Arrange: Set up test data
        
        // Act: Execute function being tested
        
        // Assert: Verify expected behavior
        $this->assertTrue($result);
    }
}
```

### Test Naming Convention

- Test methods must start with `test` prefix
- Use descriptive names: `testFeatureWithCondition()`
- Follow pattern: `testWhatIsBeingTestedUnderWhatConditions()`

### Best Practices

1. **Isolation**: Each test should be independent
2. **Cleanup**: Use `tearDown()` to clean temporary files
3. **Assertions**: Use specific assertions (`assertEquals` vs `assertTrue`)
4. **Documentation**: Add PHPDoc explaining what the test verifies
5. **Coverage**: Aim for 100% coverage of new code

## Continuous Integration

Tests run automatically on every push via GitHub Actions:

- ✅ PHP 7.4, 8.0, 8.1, 8.2, 8.3
- ✅ Code coverage report (99% minimum required)
- ✅ Code style checks (PSR-12)
- ✅ All tests must pass before merge

## Coverage Requirements

- **Minimum**: 99% code coverage
- **Current**: 99.20% (exceeds minimum)
- **Enforcement**: CI/CD pipeline fails if coverage drops below 99%

## Debugging Tests

### Verbose Output

```bash
vendor/bin/phpunit --verbose
```

### Stop on First Failure

```bash
vendor/bin/phpunit --stop-on-failure
```

### Debug Specific Test

```bash
# Run with PHP debugger
php -dxdebug.mode=debug vendor/bin/phpunit --filter testName
```

## Future Test Enhancements

Planned improvements to the test suite:

1. **Mocked API calls**: Mock Packagist API responses for faster, more reliable tests
2. **Integration test suite**: Full end-to-end tests with real Composer projects
3. **Performance tests**: Verify API calls are efficient and cached
4. **Edge case coverage**: Additional tests for unusual scenarios

---

*Last updated: 2026-01-16*
*Document version: 1.0*
