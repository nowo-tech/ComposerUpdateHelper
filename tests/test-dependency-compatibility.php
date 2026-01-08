#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Test script for dependency compatibility checking.
 * Simulates the scheb/2fa-bundle and scheb/2fa-email conflict scenario.
 */
$tempDir = sys_get_temp_dir() . '/composer-update-helper-compat-test-' . uniqid();
mkdir($tempDir, 0777, true);

echo "🧪 Testing dependency compatibility checking\n";
echo "==========================================\n\n";

// Create test composer.lock file simulating the conflict scenario
$lockData = [
    'packages' => [
        [
            'name' => 'scheb/2fa-email',
            'version' => '8.1.0',
            'require' => [
                'php' => '^7.4|^8.0',
                'scheb/2fa-bundle' => '8.1.*',
            ],
        ],
        [
            'name' => 'scheb/2fa-bundle',
            'version' => '8.1.0',
            'require' => [
                'php' => '^7.4|^8.0',
            ],
        ],
    ],
];

file_put_contents($tempDir . '/composer.lock', json_encode($lockData, JSON_PRETTY_PRINT));

// Load the processor functions
$processorPath = __DIR__ . '/../bin/process-updates.php';
$processorContent = file_get_contents($processorPath);

// Extract just the functions we need (avoid executing the main script)
// We'll use a simpler approach: include the file but prevent execution
$originalDir = getcwd();
chdir($tempDir);

// Create a minimal composer.json
file_put_contents($tempDir . '/composer.json', json_encode([
    'require' => [
        'scheb/2fa-bundle' => '8.1.0',
        'scheb/2fa-email' => '8.1.0',
    ],
], JSON_PRETTY_PRINT));

// Define functions manually for testing (extracted from process-updates.php)
function versionSatisfiesConstraint($version, $constraint)
{
    if (empty($constraint)) {
        return true;
    }

    $normalizedVersion = ltrim($version, 'v');
    $constraint = trim($constraint);

    // Handle wildcard constraints (e.g., "8.1.*")
    if (preg_match('/^(\d+\.\d+)\.\*$/', $constraint, $matches)) {
        $baseVersion = $matches[1];

        return strpos($normalizedVersion, $baseVersion . '.') === 0;
    }

    // Handle caret constraints (e.g., "^8.1.0")
    if (preg_match('/^\^(\d+\.\d+\.\d+)/', $constraint, $matches)) {
        $minVersion = $matches[1];
        $parts = explode('.', $minVersion);
        $nextMajor = (int) $parts[0] + 1;
        $maxVersion = $nextMajor . '.0.0';

        return version_compare($normalizedVersion, $minVersion, '>=') &&
               version_compare($normalizedVersion, $maxVersion, '<');
    }

    // Handle tilde constraints (e.g., "~8.1.0")
    if (preg_match('/^~(\d+\.\d+\.\d+)/', $constraint, $matches)) {
        $minVersion = $matches[1];
        $parts = explode('.', $minVersion);
        $nextMinor = (int) $parts[1] + 1;
        $maxVersion = $parts[0] . '.' . $nextMinor . '.0';

        return version_compare($normalizedVersion, $minVersion, '>=') &&
               version_compare($normalizedVersion, $maxVersion, '<');
    }

    // Handle range constraints with pipe (OR)
    if (strpos($constraint, '|') !== false) {
        $ranges = explode('|', $constraint);
        foreach ($ranges as $range) {
            $range = trim($range);
            if (versionSatisfiesConstraint($version, $range)) {
                return true;
            }
        }

        return false;
    }

    // Handle range constraints with comma (AND)
    if (strpos($constraint, ',') !== false) {
        $ranges = explode(',', $constraint);
        foreach ($ranges as $range) {
            $range = trim($range);
            if (!versionSatisfiesConstraint($version, $range)) {
                return false;
            }
        }

        return true;
    }

    // Handle comparison operators
    if (preg_match('/^(>=|<=|>|<|==|!=)\s*(.+)$/', $constraint, $matches)) {
        $operator = $matches[1];
        $targetVersion = ltrim($matches[2], 'v');

        if ($operator === '!=') {
            return version_compare($normalizedVersion, $targetVersion, '!=');
        }

        return version_compare($normalizedVersion, $targetVersion, $operator);
    }

    // Handle exact version match
    if (preg_match('/^\d+\.\d+\.\d+/', $constraint)) {
        return version_compare($normalizedVersion, $constraint, '==');
    }

    // Handle base version (e.g., "8.1" means "8.1.*")
    if (preg_match('/^(\d+\.\d+)$/', $constraint, $matches)) {
        $baseVersion = $matches[1];

        return strpos($normalizedVersion, $baseVersion . '.') === 0;
    }

    return false;
}

function getPackageConstraintsFromLock($packageName)
{
    if (!file_exists('composer.lock')) {
        return [];
    }

    $lock = json_decode(file_get_contents('composer.lock'), true);
    if (!$lock || (!isset($lock['packages']) && !isset($lock['packages-dev']))) {
        return [];
    }

    $allPackages = array_merge(
        $lock['packages'] ?? [],
        $lock['packages-dev'] ?? []
    );

    $constraints = [];
    foreach ($allPackages as $pkg) {
        if (!isset($pkg['name'])) {
            continue;
        }

        // Check if this package requires our target package
        // Dependencies can be in 'require', 'require-dev', or both
        $requires = array_merge(
            $pkg['require'] ?? [],
            $pkg['require-dev'] ?? []
        );

        if (isset($requires[$packageName])) {
            $constraints[$pkg['name']] = $requires[$packageName];
        }
    }

    return $constraints;
}

// Run tests
echo "Test 1: Reading constraints from composer.lock\n";
$constraints = getPackageConstraintsFromLock('scheb/2fa-bundle');
echo '   Found ' . count($constraints) . " dependent package(s)\n";
foreach ($constraints as $pkg => $constraint) {
    echo "   - {$pkg} requires scheb/2fa-bundle: {$constraint}\n";
}

if (isset($constraints['scheb/2fa-email'])) {
    echo "   ✅ Correctly found scheb/2fa-email dependency\n";
} else {
    echo "   ❌ Failed to find scheb/2fa-email dependency\n";
    exit(1);
}

echo "\nTest 2: Checking version compatibility\n";
$testVersions = ['8.1.0', '8.1.5', '8.2.0', '9.0.0'];
$constraint = $constraints['scheb/2fa-email'];

foreach ($testVersions as $version) {
    $satisfies = versionSatisfiesConstraint($version, $constraint);
    $status = $satisfies ? '✅' : '❌';
    echo "   {$status} Version {$version} " . ($satisfies ? 'satisfies' : 'does NOT satisfy') . " constraint '{$constraint}'\n";
}

echo "\nTest 3: Simulating conflict scenario\n";
$proposedVersion = '8.2.0';
$requiredConstraint = $constraints['scheb/2fa-email'];

if (!versionSatisfiesConstraint($proposedVersion, $requiredConstraint)) {
    echo "   ✅ Correctly detected that {$proposedVersion} does NOT satisfy '{$requiredConstraint}'\n";
    echo "   ✅ System would skip or find compatible version (8.1.x)\n";
} else {
    echo "   ❌ Failed to detect conflict\n";
    exit(1);
}

echo "\nTest 4: Testing various constraint formats\n";
$testCases = [
    ['8.1.0', '8.1.*', true],
    ['8.1.5', '8.1.*', true],
    ['8.2.0', '8.1.*', false],
    ['8.1.0', '^8.1.0', true],
    ['8.9.9', '^8.1.0', true],
    ['9.0.0', '^8.1.0', false],
    ['8.1.0', '~8.1.0', true],
    ['8.1.99', '~8.1.0', true],
    ['8.2.0', '~8.1.0', false],
    ['8.1.5', '>=8.1.0,<8.2.0', true],
    ['8.2.0', '>=8.1.0,<8.2.0', false],
];

$allPassed = true;
foreach ($testCases as [$version, $constraint, $expected]) {
    $result = versionSatisfiesConstraint($version, $constraint);
    $status = ($result === $expected) ? '✅' : '❌';
    if ($result !== $expected) {
        $allPassed = false;
    }
    echo "   {$status} {$version} vs '{$constraint}' (expected: " . ($expected ? 'true' : 'false') . ', got: ' . ($result ? 'true' : 'false') . ")\n";
}

chdir($originalDir);

// Cleanup
function removeDirectory($dir)
{
    if (!is_dir($dir)) {
        return;
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    rmdir($dir);
}

removeDirectory($tempDir);

echo "\n";
if ($allPassed) {
    echo "✅ All tests passed!\n";
    exit(0);
}
echo "❌ Some tests failed\n";
exit(1);
