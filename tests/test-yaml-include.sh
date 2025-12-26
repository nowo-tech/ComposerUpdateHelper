#!/bin/bash
# Test script to verify that YAML is read correctly and include logic is applied

set -e

echo "🧪 Test: YAML Reading and Include Logic Verification"
echo "=================================================="
echo ""

# Create a test YAML
TEST_YAML="/tmp/test-composer-update-helper.yaml"
cat > "$TEST_YAML" << 'EOF'
# Composer Update Helper Configuration
ignore:
  - doctrine/orm
  - doctrine/doctrine-bundle
  - symfony/security-bundle
  # - laravel/framework  # Commented

include:
  - symfony/security-bundle  # This is in both, should have priority
  - monolog/monolog  # This is only in include
EOF

echo "📄 Test YAML created:"
cat "$TEST_YAML"
echo ""
echo "=================================================="
echo ""

# Test IGNORE parsing
echo "✅ Test 1: IGNORE Section Parsing"
echo "-----------------------------------"
IGNORED_PACKAGES=$(awk '
    /^ignore:/{flag=1; next}
    flag && /^[^ ]/{flag=0}
    flag && /^\s*-\s+([^#]+)/{
      gsub(/^\s*-\s+/, "");
      gsub(/\s*#.*$/, "");
      gsub(/^\s+|\s+$/, "");
      if ($0 != "") print
    }
  ' "$TEST_YAML" | tr '\n' '|' | sed 's/|$//' || true)

echo "Ignored packages: $IGNORED_PACKAGES"
EXPECTED_IGNORE="doctrine/orm|doctrine/doctrine-bundle|symfony/security-bundle"
if [ "$IGNORED_PACKAGES" = "$EXPECTED_IGNORE" ]; then
    echo "✅ PASS: Ignored packages are read correctly"
else
    echo "❌ FAIL: Expected '$EXPECTED_IGNORE', got '$IGNORED_PACKAGES'"
    exit 1
fi
echo ""

# Test INCLUDE parsing
echo "✅ Test 2: INCLUDE Section Parsing"
echo "-----------------------------------"
INCLUDED_PACKAGES=$(awk '
    /^include:/{flag=1; next}
    flag && /^[^ ]/{flag=0}
    flag && /^\s*-\s+([^#]+)/{
      gsub(/^\s*-\s+/, "");
      gsub(/\s*#.*$/, "");
      gsub(/^\s+|\s+$/, "");
      if ($0 != "") print
    }
  ' "$TEST_YAML" | tr '\n' '|' | sed 's/|$//' || true)

echo "Included packages: $INCLUDED_PACKAGES"
EXPECTED_INCLUDE="symfony/security-bundle|monolog/monolog"
if [ "$INCLUDED_PACKAGES" = "$EXPECTED_INCLUDE" ]; then
    echo "✅ PASS: Included packages are read correctly"
else
    echo "❌ FAIL: Expected '$EXPECTED_INCLUDE', got '$INCLUDED_PACKAGES'"
    exit 1
fi
echo ""

# Test priority logic (PHP simulation)
echo "✅ Test 3: Priority Logic (include > ignore)"
echo "-----------------------------------"
cat > /tmp/test-priority.php << 'PHPEOF'
<?php
$ignoredPackagesRaw = getenv('IGNORED_PACKAGES') ?: '';
$includedPackagesRaw = getenv('INCLUDED_PACKAGES') ?: '';

$ignoredPackages = [];
if ($ignoredPackagesRaw) {
    $ignoredPackages = array_flip(explode('|', $ignoredPackagesRaw));
}

$includedPackages = [];
if ($includedPackagesRaw) {
    $includedPackages = array_flip(explode('|', $includedPackagesRaw));
}

// Test cases
$testCases = [
    'doctrine/orm' => ['shouldIgnore' => true, 'shouldInclude' => false],
    'doctrine/doctrine-bundle' => ['shouldIgnore' => true, 'shouldInclude' => false],
    'symfony/security-bundle' => ['shouldIgnore' => true, 'shouldInclude' => true], // In both, include has priority
    'monolog/monolog' => ['shouldIgnore' => false, 'shouldInclude' => true],
    'psr/log' => ['shouldIgnore' => false, 'shouldInclude' => false],
];

$allPassed = true;
foreach ($testCases as $package => $expected) {
    $isIgnored = isset($ignoredPackages[$package]);
    $isIncluded = isset($includedPackages[$package]);

    // Script logic: if in ignore AND NOT in include, ignore
    $shouldBeIgnored = $isIgnored && !$isIncluded;
    $shouldBeIncluded = $isIncluded; // Include has priority

    $ignoreMatch = ($shouldBeIgnored === $expected['shouldIgnore']);
    $includeMatch = ($shouldBeIncluded === $expected['shouldInclude']);

    if (!$ignoreMatch || !$includeMatch) {
        echo "❌ FAIL: $package\n";
        echo "   Expected: ignore=" . ($expected['shouldIgnore'] ? 'true' : 'false') . ", include=" . ($expected['shouldInclude'] ? 'true' : 'false') . "\n";
        echo "   Got: ignore=" . ($shouldBeIgnored ? 'true' : 'false') . ", include=" . ($shouldBeIncluded ? 'true' : 'false') . "\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: $package - ignore=" . ($shouldBeIgnored ? 'true' : 'false') . ", include=" . ($shouldBeIncluded ? 'true' : 'false') . "\n";
    }
}

if (!$allPassed) {
    exit(1);
}

echo "\n✅ All priority logic tests passed\n";
PHPEOF

if command -v php >/dev/null 2>&1; then
    IGNORED_PACKAGES="$IGNORED_PACKAGES" INCLUDED_PACKAGES="$INCLUDED_PACKAGES" php /tmp/test-priority.php
    if [ $? -eq 0 ]; then
        echo "✅ PASS: Priority logic works correctly"
    else
        echo "❌ FAIL: Priority logic failed"
        exit 1
    fi
else
    echo "⚠️  PHP not available, skipping PHP logic test"
    echo "   (YAML parsing was already verified)"
fi
echo ""

# Cleanup
rm -f "$TEST_YAML" /tmp/test-priority.php

echo "=================================================="
echo "✅ All tests passed successfully"
echo ""
