<?php

declare(strict_types=1);

namespace NowoTech\ComposerUpdateHelper\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Test suite for dependency compatibility checking.
 * Tests the functions that verify package dependencies to prevent conflicts.
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 *
 * @see    https://github.com/HecFranco
 */
final class DependencyCompatibilityTest extends TestCase
{
    private string $tempDir;
    private string $processorPath;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/composer-update-helper-compat-test-' . uniqid();
        $this->processorPath = dirname(__DIR__) . '/bin/process-updates.php';

        mkdir($this->tempDir, 0777, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->tempDir);
    }

    public function testVersionSatisfiesWildcardConstraint(): void
    {
        // Extract and test the function logic
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.0', '8.1.*'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.5', '8.1.*'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.10', '8.1.*'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.2.0', '8.1.*'));
        $this->assertFalse($this->versionSatisfiesConstraint('9.0.0', '8.1.*'));
    }

    public function testVersionSatisfiesCaretConstraint(): void
    {
        // Test caret constraint "^8.1.0" (>=8.1.0 <9.0.0)
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.0', '^8.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.5', '^8.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.9.9', '^8.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('9.0.0', '^8.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('7.9.9', '^8.1.0'));
    }

    public function testVersionSatisfiesCaretConstraintWithVPrefix(): void
    {
        // Test caret constraint with 'v' prefix "^v7.1.0" should be treated as "^7.1.0" (>=7.1.0 <8.0.0)
        $this->assertTrue($this->versionSatisfiesConstraint('7.1.0', '^v7.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('7.4.0', '^v7.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('7.9.9', '^v7.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.0.0', '^v7.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('6.9.9', '^v7.1.0'));
        // Test partial versions
        $this->assertTrue($this->versionSatisfiesConstraint('7.4.0', '^v7.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('7.4.0', '^v7'));
    }

    public function testVersionSatisfiesTildeConstraint(): void
    {
        // Test tilde constraint "~8.1.0" (>=8.1.0 <8.2.0)
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.0', '~8.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.5', '~8.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.99', '~8.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.2.0', '~8.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('9.0.0', '~8.1.0'));
    }

    public function testVersionSatisfiesTildeConstraintWithVPrefix(): void
    {
        // Test tilde constraint with 'v' prefix "~v7.1.0" should be treated as "~7.1.0" (>=7.1.0 <7.2.0)
        $this->assertTrue($this->versionSatisfiesConstraint('7.1.0', '~v7.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('7.1.5', '~v7.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('7.1.99', '~v7.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('7.2.0', '~v7.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.0.0', '~v7.1.0'));
    }

    public function testVersionSatisfiesRangeConstraint(): void
    {
        // Test range constraint ">=8.1.0,<8.2.0"
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.0', '>=8.1.0,<8.2.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.5', '>=8.1.0,<8.2.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.2.0', '>=8.1.0,<8.2.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.0.9', '>=8.1.0,<8.2.0'));
    }

    public function testGetPackageConstraintsFromLock(): void
    {
        // Create a test composer.lock file
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

        file_put_contents($this->tempDir . '/composer.lock', json_encode($lockData, JSON_PRETTY_PRINT));

        // Change to temp directory to test
        $originalDir = getcwd();
        chdir($this->tempDir);

        try {
            $constraints = $this->getPackageConstraintsFromLock('scheb/2fa-bundle');
            $this->assertArrayHasKey('scheb/2fa-email', $constraints);
            $this->assertEquals('8.1.*', $constraints['scheb/2fa-email']);
        } finally {
            chdir($originalDir);
        }
    }

    public function testFindCompatibleVersionWithConflict(): void
    {
        // Create a test composer.lock file with conflict scenario
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

        file_put_contents($this->tempDir . '/composer.lock', json_encode($lockData, JSON_PRETTY_PRINT));

        // Create a mock composer.json
        file_put_contents($this->tempDir . '/composer.json', json_encode([
            'require' => [
                'scheb/2fa-bundle' => '8.1.0',
                'scheb/2fa-email' => '8.1.0',
            ],
        ], JSON_PRETTY_PRINT));

        $originalDir = getcwd();
        chdir($this->tempDir);

        try {
            // Test that the function correctly identifies the constraint
            $constraints = $this->getPackageConstraintsFromLock('scheb/2fa-bundle');
            $this->assertArrayHasKey('scheb/2fa-email', $constraints);
            $this->assertEquals('8.1.*', $constraints['scheb/2fa-email']);

            // Test that 8.2.0 does NOT satisfy 8.1.*
            $this->assertFalse($this->versionSatisfiesConstraint('8.2.0', '8.1.*'));

            // Test that 8.1.5 DOES satisfy 8.1.*
            $this->assertTrue($this->versionSatisfiesConstraint('8.1.5', '8.1.*'));
        } finally {
            chdir($originalDir);
        }
    }

    public function testFindCompatibleVersionWithNoDependents(): void
    {
        // Create a test composer.lock file with no dependents
        $lockData = [
            'packages' => [
                [
                    'name' => 'some/package',
                    'version' => '1.0.0',
                    'require' => [
                        'php' => '^7.4|^8.0',
                    ],
                ],
            ],
        ];

        file_put_contents($this->tempDir . '/composer.lock', json_encode($lockData, JSON_PRETTY_PRINT));

        $originalDir = getcwd();
        chdir($this->tempDir);

        try {
            $constraints = $this->getPackageConstraintsFromLock('some/other-package');
            $this->assertEmpty($constraints);
        } finally {
            chdir($originalDir);
        }
    }

    public function testVersionSatisfiesExactVersion(): void
    {
        // Test exact version match
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.0', '8.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.1.1', '8.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.2.0', '8.1.0'));
    }

    public function testVersionSatisfiesComparisonOperators(): void
    {
        // Test >= operator
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.5', '>=8.1.0'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.0', '>=8.1.0'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.0.9', '>=8.1.0'));

        // Test <= operator
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.0', '<=8.1.5'));
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.5', '<=8.1.5'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.1.6', '<=8.1.5'));

        // Test > operator
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.6', '>8.1.5'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.1.5', '>8.1.5'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.1.4', '>8.1.5'));

        // Test < operator
        $this->assertTrue($this->versionSatisfiesConstraint('8.1.4', '<8.1.5'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.1.5', '<8.1.5'));
        $this->assertFalse($this->versionSatisfiesConstraint('8.1.6', '<8.1.5'));
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $fileinfo) {
            $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
            $todo($fileinfo->getRealPath());
        }

        rmdir($dir);
    }

    /**
     * Copy of versionSatisfiesConstraint from process-updates.php for testing.
     */
    private function versionSatisfiesConstraint(string $version, string $constraint): bool
    {
        if (empty($constraint)) {
            return true;
        }

        $normalizedVersion = ltrim($version, 'v');
        $constraint = trim($constraint);

        // Handle constraints that start with 'v' followed by version (e.g., "v8.2.0" means exactly "8.2.0")
        if (preg_match('/^v(\d+\.\d+\.\d+)$/', $constraint, $matches)) {
            return version_compare($normalizedVersion, $matches[1], '==');
        }

        // Handle wildcard constraints (e.g., "8.1.*")
        if (preg_match('/^(\d+\.\d+)\.\*$/', $constraint, $matches)) {
            $baseVersion = $matches[1];

            return strpos($normalizedVersion, $baseVersion . '.') === 0;
        }

        // Handle range constraints with comma (AND) or pipe (OR)
        // Note: Composer uses both single | and double || for OR
        // IMPORTANT: This must be checked BEFORE caret/tilde constraints because
        // constraints like "^2.5|^3" need to be split first
        if (strpos($constraint, '||') !== false || strpos($constraint, '|') !== false) {
            // OR operator: any range must be satisfied
            // Split by || first, then by | to handle both formats
            $ranges = preg_split('/\s*\|\|\s*|\s*\|\s*/', $constraint);
            foreach ($ranges as $range) {
                $range = trim($range);
                if (empty($range)) {
                    continue;
                }
                // Recursively check each range
                if ($this->versionSatisfiesConstraint($version, $range)) {
                    return true;
                }
            }

            return false;
        }

        // Handle caret constraints (e.g., "^8.1.0" or "^v7.1.0")
        // Also handle "^v7.1.0" which should be treated as "^7.1.0" (ignore the 'v' prefix)
        if (preg_match('/^\^v?(\d+)(?:\.(\d+))?(?:\.(\d+))?/', $constraint, $matches)) {
            $major = (int) $matches[1];
            // Check if minor and patch are captured (not just empty strings)
            $minor = (isset($matches[2]) && $matches[2] !== '') ? (int) $matches[2] : 0;
            $patch = (isset($matches[3]) && $matches[3] !== '') ? (int) $matches[3] : 0;

            $minVersion = $major . '.' . $minor . '.' . $patch;
            $nextMajor = $major + 1;
            $maxVersion = $nextMajor . '.0.0';

            return version_compare($normalizedVersion, $minVersion, '>=') &&
                   version_compare($normalizedVersion, $maxVersion, '<');
        }

        // Handle tilde constraints (e.g., "~8.1.0" or "~v7.1.0")
        // Also handle "~v7.1.0" which should be treated as "~7.1.0" (ignore the 'v' prefix)
        if (preg_match('/^~v?(\d+)(?:\.(\d+))?(?:\.(\d+))?/', $constraint, $matches)) {
            $major = (int) $matches[1];
            $minor = isset($matches[2]) && $matches[2] !== '' ? (int) $matches[2] : 0;
            $patch = isset($matches[3]) && $matches[3] !== '' ? (int) $matches[3] : 0;

            $minVersion = $major . '.' . $minor . '.' . $patch;

            // If only major version specified (e.g., "~1"), next version is major+1.0.0
            // If major.minor specified (e.g., "~1.0"), next version is major.minor+1.0
            // If major.minor.patch specified (e.g., "~1.0.0"), next version is major.minor+1.0
            if (!isset($matches[2]) || $matches[2] === '') {
                // Only major: ~1 means >=1.0.0 <2.0.0
                $nextMajor = $major + 1;
                $maxVersion = $nextMajor . '.0.0';
            } else {
                // Major.minor or major.minor.patch: ~1.0 means >=1.0.0 <2.0.0, ~1.0.0 means >=1.0.0 <1.1.0
                if (!isset($matches[3]) || $matches[3] === '') {
                    // Major.minor: ~1.0 means >=1.0.0 <2.0.0
                    $nextMajor = $major + 1;
                    $maxVersion = $nextMajor . '.0.0';
                } else {
                    // Major.minor.patch: ~1.0.0 means >=1.0.0 <1.1.0
                    $nextMinor = $minor + 1;
                    $maxVersion = $major . '.' . $nextMinor . '.0';
                }
            }

            return version_compare($normalizedVersion, $minVersion, '>=') &&
                   version_compare($normalizedVersion, $maxVersion, '<');
        }

        // Handle range constraints with comma (AND)
        if (strpos($constraint, ',') !== false) {
            $ranges = explode(',', $constraint);
            foreach ($ranges as $range) {
                $range = trim($range);
                if (!$this->versionSatisfiesConstraint($version, $range)) {
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

    /**
     * Copy of getPackageConstraintsFromLock from process-updates.php for testing.
     */
    private function getPackageConstraintsFromLock(string $packageName): array
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

    public function testReadConfigValue(): void
    {
        // Create a test YAML file
        $yamlFile = $this->tempDir . '/test-config.yaml';
        $yamlContent = <<<'YAML'
            # Test configuration
            check-dependencies: true
            some-number: 42
            some-string: hello world
            another-boolean: false
            # Commented value
            # ignored-value: test
            YAML;
        file_put_contents($yamlFile, $yamlContent);

        // Test reading boolean true
        $this->assertTrue($this->readConfigValue($yamlFile, 'check-dependencies', false));

        // Test reading boolean false
        $this->assertFalse($this->readConfigValue($yamlFile, 'another-boolean', true));

        // Test reading numeric value
        $this->assertEquals(42, $this->readConfigValue($yamlFile, 'some-number', 0));

        // Test reading string value
        $this->assertEquals('hello world', $this->readConfigValue($yamlFile, 'some-string', ''));

        // Test reading non-existent key (should return default)
        $this->assertEquals('default', $this->readConfigValue($yamlFile, 'non-existent', 'default'));

        // Test reading from non-existent file (should return default)
        $this->assertEquals('default', $this->readConfigValue('/non-existent/file.yaml', 'key', 'default'));
    }

    /**
     * Copy of readConfigValue from process-updates.php for testing.
     * 
     * @param string $yamlPath
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    private function readConfigValue(string $yamlPath, string $key, $default = null)
    {
        if (!file_exists($yamlPath)) {
            return $default;
        }

        $content = file_get_contents($yamlPath);
        if ($content === false) {
            return $default;
        }

        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $trimmedLine = trim($line);

            // Skip empty lines and pure comment lines
            if (empty($trimmedLine) || strpos($trimmedLine, '#') === 0) {
                continue;
            }

            // Check for key: value pattern
            if (preg_match('/^' . preg_quote($key, '/') . ':\s*(.+)$/', $trimmedLine, $matches)) {
                $value = trim($matches[1]);
                // Handle boolean values
                if (strtolower($value) === 'true') {
                    return true;
                }
                if (strtolower($value) === 'false') {
                    return false;
                }
                // Handle numeric values
                if (is_numeric($value)) {
                    return $value + 0; // Convert to int or float
                }

                // Return as string
                return $value;
            }
        }

        return $default;
    }

    public function testNormalizeVersion(): void
    {
        // Test normalizeVersion function
        $this->assertEquals('8.1.0', $this->normalizeVersion('8.1.0'));
        $this->assertEquals('8.1.0', $this->normalizeVersion('v8.1.0'));
        $this->assertEquals('7.4.0', $this->normalizeVersion('v7.4.0'));
        $this->assertNull($this->normalizeVersion(null));
        $this->assertEquals('1.0.0', $this->normalizeVersion('v1.0.0'));
        $this->assertEquals('2.5.3', $this->normalizeVersion('2.5.3'));
    }

    public function testFormatPackageList(): void
    {
        // Test formatPackageList function
        $packages = ['package1:1.0.0', 'package2:2.0.0'];
        $result = $this->formatPackageList($packages, '(prod)');

        $this->assertCount(2, $result);
        $this->assertEquals('     - package1:1.0.0 (prod)', $result[0]);
        $this->assertEquals('     - package2:2.0.0 (prod)', $result[1]);

        // Test with custom indent
        $result2 = $this->formatPackageList($packages, '(dev)', '  ');
        $this->assertEquals('  - package1:1.0.0 (dev)', $result2[0]);
    }

    public function testBuildComposerCommand(): void
    {
        // Test buildComposerCommand function
        $packages = ['package1:1.0.0', 'package2:2.0.0'];

        // Test prod command
        $prodCommand = $this->buildComposerCommand($packages, false);
        $this->assertNotNull($prodCommand);
        $this->assertStringContainsString('composer require', $prodCommand);
        $this->assertStringContainsString('--with-all-dependencies', $prodCommand);
        $this->assertStringContainsString('package1:1.0.0', $prodCommand);
        $this->assertStringNotContainsString('--dev', $prodCommand);

        // Test dev command
        $devCommand = $this->buildComposerCommand($packages, true);
        $this->assertNotNull($devCommand);
        $this->assertStringContainsString('composer require --dev', $devCommand);
        $this->assertStringContainsString('--with-all-dependencies', $devCommand);

        // Test empty array
        $emptyCommand = $this->buildComposerCommand([], false);
        $this->assertNull($emptyCommand);
    }

    public function testAddPackageToArray(): void
    {
        // Test addPackageToArray function
        $prod = [];
        $dev = [];
        $devSet = ['dev-package' => true];

        // Add prod package
        $this->addPackageToArray('prod-package', '1.0.0', $devSet, $prod, $dev, false);
        $this->assertCount(1, $prod);
        $this->assertCount(0, $dev);
        $this->assertEquals('prod-package:1.0.0', $prod[0]);

        // Add dev package
        $this->addPackageToArray('dev-package', '2.0.0', $devSet, $prod, $dev, false);
        $this->assertCount(1, $prod);
        $this->assertCount(1, $dev);
        $this->assertEquals('dev-package:2.0.0', $dev[0]);
    }

    /**
     * Copy of normalizeVersion from process-updates.php for testing.
     */
    private function normalizeVersion(?string $version): ?string
    {
        if ($version === null) {
            return null;
        }

        return ltrim($version, 'v');
    }

    /**
     * Copy of formatPackageList from process-updates.php for testing.
     */
    private function formatPackageList(array $packages, string $label, string $indent = '     '): array
    {
        $output = [];
        foreach ($packages as $pkg) {
            $output[] = $indent . '- ' . $pkg . ' ' . $label;
        }

        return $output;
    }

    /**
     * Copy of buildComposerCommand from process-updates.php for testing.
     */
    private function buildComposerCommand(array $packages, bool $isDev = false): ?string
    {
        if (empty($packages)) {
            return null;
        }

        $baseCommand = $isDev ? 'composer require --dev' : 'composer require';

        return $baseCommand . ' --with-all-dependencies ' . implode(' ', $packages);
    }

    /**
     * Copy of addPackageToArray from process-updates.php for testing.
     */
    private function addPackageToArray(string $name, string $constraint, array $devSet, array &$prod, array &$dev, bool $debug = false): void
    {
        $packageString = $name . ':' . $constraint;
        if (isset($devSet[$name])) {
            $dev[] = $packageString;
        } else {
            $prod[] = $packageString;
        }
    }

    public function testFindCompatibleVersionRejectsVersionNotSatisfyingDependentConstraints(): void
    {
        // Test the scenario where a proposed version doesn't satisfy dependent package constraints
        // This is the case reported: phpdocumentor/reflection-docblock:6.0.0 doesn't satisfy ^5.6 and ^5.0
        $lockData = [
            'packages' => [
                [
                    'name' => 'a2lix/auto-form-bundle',
                    'version' => '1.0.0',
                    'require' => [
                        'php' => '^7.4|^8.0',
                        'phpdocumentor/reflection-docblock' => '^5.6',
                    ],
                ],
                [
                    'name' => 'nelmio/api-doc-bundle',
                    'version' => '5.9.0',
                    'require' => [
                        'php' => '^7.4|^8.0',
                        'phpdocumentor/reflection-docblock' => '^5.0',
                    ],
                ],
                [
                    'name' => 'phpdocumentor/reflection-docblock',
                    'version' => '5.6.6',
                    'require' => [
                        'php' => '^7.4|^8.0',
                    ],
                ],
            ],
        ];

        file_put_contents($this->tempDir . '/composer.lock', json_encode($lockData, JSON_PRETTY_PRINT));

        // Create a mock composer.json
        file_put_contents($this->tempDir . '/composer.json', json_encode([
            'require' => [
                'phpdocumentor/reflection-docblock' => '5.6.6',
                'a2lix/auto-form-bundle' => '1.0.0',
                'nelmio/api-doc-bundle' => '5.9.0',
            ],
        ], JSON_PRETTY_PRINT));

        $originalDir = getcwd();
        chdir($this->tempDir);

        try {
            // Test that getPackageConstraintsFromLock correctly finds dependent packages
            $constraints = $this->getPackageConstraintsFromLock('phpdocumentor/reflection-docblock');
            $this->assertArrayHasKey('a2lix/auto-form-bundle', $constraints);
            $this->assertArrayHasKey('nelmio/api-doc-bundle', $constraints);
            $this->assertEquals('^5.6', $constraints['a2lix/auto-form-bundle']);
            $this->assertEquals('^5.0', $constraints['nelmio/api-doc-bundle']);

            // Test that 6.0.0 does NOT satisfy ^5.6
            $this->assertFalse($this->versionSatisfiesConstraint('6.0.0', '^5.6'));

            // Test that 6.0.0 does NOT satisfy ^5.0
            $this->assertFalse($this->versionSatisfiesConstraint('6.0.0', '^5.0'));

            // Test that 5.6.6 DOES satisfy both ^5.6 and ^5.0
            $this->assertTrue($this->versionSatisfiesConstraint('5.6.6', '^5.6'));
            $this->assertTrue($this->versionSatisfiesConstraint('5.6.6', '^5.0'));

            // Test that 5.9.0 DOES satisfy both ^5.6 and ^5.0
            $this->assertTrue($this->versionSatisfiesConstraint('5.9.0', '^5.6'));
            $this->assertTrue($this->versionSatisfiesConstraint('5.9.0', '^5.0'));
        } finally {
            chdir($originalDir);
        }
    }

    public function testGetPackageConstraintsFromLockFindsRequireDev(): void
    {
        // Test that getPackageConstraintsFromLock also finds dependencies in require-dev
        $lockData = [
            'packages' => [
                [
                    'name' => 'some/prod-package',
                    'version' => '1.0.0',
                    'require' => [
                        'php' => '^7.4|^8.0',
                        'target/package' => '^2.0',
                    ],
                ],
            ],
            'packages-dev' => [
                [
                    'name' => 'some/dev-package',
                    'version' => '1.0.0',
                    'require' => [
                        'php' => '^7.4|^8.0',
                        'target/package' => '^3.0',
                    ],
                ],
                [
                    'name' => 'another/dev-package',
                    'version' => '2.0.0',
                    'require-dev' => [
                        'target/package' => '^2.5',
                    ],
                ],
            ],
        ];

        file_put_contents($this->tempDir . '/composer.lock', json_encode($lockData, JSON_PRETTY_PRINT));

        $originalDir = getcwd();
        chdir($this->tempDir);

        try {
            $constraints = $this->getPackageConstraintsFromLock('target/package');

            // Should find dependencies from both require and require-dev
            $this->assertArrayHasKey('some/prod-package', $constraints);
            $this->assertEquals('^2.0', $constraints['some/prod-package']);

            $this->assertArrayHasKey('some/dev-package', $constraints);
            $this->assertEquals('^3.0', $constraints['some/dev-package']);

            $this->assertArrayHasKey('another/dev-package', $constraints);
            $this->assertEquals('^2.5', $constraints['another/dev-package']);

            $this->assertCount(3, $constraints);
        } finally {
            chdir($originalDir);
        }
    }
}
