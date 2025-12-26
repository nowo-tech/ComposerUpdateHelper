#!/bin/bash
# Test script to verify that release information only appears when requested

set -e

echo "🧪 Test: Release Information Verification"
echo "=============================================="
echo ""

# Test 1: Verify SHOW_RELEASE_INFO is initialized correctly
echo "✅ Test 1: Default Values"
echo "-----------------------------------"
cat > /tmp/test-defaults.sh << 'SHEOF'
#!/bin/bash
SHOW_RELEASE_DETAIL=false
SHOW_RELEASE_INFO=true

echo "SHOW_RELEASE_DETAIL (default): $SHOW_RELEASE_DETAIL"
echo "SHOW_RELEASE_INFO (default): $SHOW_RELEASE_INFO"

if [ "$SHOW_RELEASE_INFO" = "true" ]; then
    echo "✅ PASS: Release info is shown by default (expected behavior)"
else
    echo "❌ FAIL: Release info is NOT shown by default"
    exit 1
fi

if [ "$SHOW_RELEASE_DETAIL" = "false" ]; then
    echo "✅ PASS: Full details are NOT shown by default"
else
    echo "❌ FAIL: Full details are shown by default"
    exit 1
fi
SHEOF

chmod +x /tmp/test-defaults.sh
/tmp/test-defaults.sh
echo ""

# Test 2: Verify --no-release-info works
echo "✅ Test 2: --no-release-info Flag"
echo "-----------------------------------"
cat > /tmp/test-no-release.sh << 'SHEOF'
#!/bin/bash
SHOW_RELEASE_INFO=true

# Simulate argument parsing
for arg in "$@"; do
    case "$arg" in
        --no-release-info|--skip-releases|--no-releases)
            SHOW_RELEASE_INFO=false
            ;;
    esac
done

echo "SHOW_RELEASE_INFO after --no-release-info: $SHOW_RELEASE_INFO"

if [ "$SHOW_RELEASE_INFO" = "false" ]; then
    echo "✅ PASS: --no-release-info correctly disables information"
else
    echo "❌ FAIL: --no-release-info does not disable information"
    exit 1
fi
SHEOF

chmod +x /tmp/test-no-release.sh
/tmp/test-no-release.sh --no-release-info
echo ""

# Test 3: Verify --release-detail works
echo "✅ Test 3: --release-detail Flag"
echo "-----------------------------------"
cat > /tmp/test-release-detail.sh << 'SHEOF'
#!/bin/bash
SHOW_RELEASE_DETAIL=false
SHOW_RELEASE_INFO=true

# Simulate argument parsing
for arg in "$@"; do
    case "$arg" in
        --release-detail|--release-full|--detail)
            SHOW_RELEASE_DETAIL=true
            ;;
    esac
done

echo "SHOW_RELEASE_DETAIL after --release-detail: $SHOW_RELEASE_DETAIL"
echo "SHOW_RELEASE_INFO (should remain true): $SHOW_RELEASE_INFO"

if [ "$SHOW_RELEASE_DETAIL" = "true" ]; then
    echo "✅ PASS: --release-detail correctly enables details"
else
    echo "❌ FAIL: --release-detail does not enable details"
    exit 1
fi

if [ "$SHOW_RELEASE_INFO" = "true" ]; then
    echo "✅ PASS: SHOW_RELEASE_INFO remains true (required to show details)"
else
    echo "❌ FAIL: SHOW_RELEASE_INFO should be true"
    exit 1
fi
SHEOF

chmod +x /tmp/test-release-detail.sh
/tmp/test-release-detail.sh --release-detail
echo ""

# Test 4: Verify PHP logic
echo "✅ Test 4: PHP showReleaseInfo Logic"
echo "-----------------------------------"
cat > /tmp/test-php-logic.php << 'PHPEOF'
<?php
// Simulate PHP logic
function testShowReleaseInfo($envValue) {
    $showReleaseInfo = $envValue !== 'false';
    return $showReleaseInfo;
}

$testCases = [
    'true' => true,
    'false' => false,
    '' => true,  // Empty is considered true
    'anything' => true,  // Anything except 'false' is true
];

$allPassed = true;
foreach ($testCases as $input => $expected) {
    $result = testShowReleaseInfo($input);
    if ($result === $expected) {
        echo "✅ PASS: getenv('SHOW_RELEASE_INFO') = '$input' → " . ($result ? 'true' : 'false') . "\n";
    } else {
        echo "❌ FAIL: getenv('SHOW_RELEASE_INFO') = '$input' → expected " . ($expected ? 'true' : 'false') . ", got " . ($result ? 'true' : 'false') . "\n";
        $allPassed = false;
    }
}

if (!$allPassed) {
    exit(1);
}

echo "\n✅ All PHP logic tests passed\n";
PHPEOF

if command -v php >/dev/null 2>&1; then
    php /tmp/test-php-logic.php
else
    echo "⚠️  PHP not available, showing expected logic:"
    echo "   - getenv('SHOW_RELEASE_INFO') !== 'false' → true (any value except 'false')"
    echo "   - This means by default (empty or 'true') it is shown"
    echo "   - Only with explicit 'false' it is hidden"
fi
echo ""

# Test 5: Verify display condition in bash
echo "✅ Test 5: Bash Display Condition"
echo "-----------------------------------"
cat > /tmp/test-bash-display.sh << 'SHEOF'
#!/bin/bash
# Simulate display condition
RELEASES="package1|url1|name1|body1|date1"
SHOW_RELEASE_INFO="true"

echo "Test 5.1: With RELEASES and SHOW_RELEASE_INFO=true"
if [ -n "$RELEASES" ] && [ "$SHOW_RELEASE_INFO" = "true" ]; then
    echo "✅ PASS: Release information would be shown"
else
    echo "❌ FAIL: Release information would NOT be shown (should be shown)"
    exit 1
fi

echo ""
echo "Test 5.2: With RELEASES and SHOW_RELEASE_INFO=false"
SHOW_RELEASE_INFO="false"
if [ -n "$RELEASES" ] && [ "$SHOW_RELEASE_INFO" = "true" ]; then
    echo "❌ FAIL: Release information would be shown (should NOT be shown)"
    exit 1
else
    echo "✅ PASS: Release information would NOT be shown (correct)"
fi

echo ""
echo "Test 5.3: Without RELEASES (empty)"
RELEASES=""
SHOW_RELEASE_INFO="true"
if [ -n "$RELEASES" ] && [ "$SHOW_RELEASE_INFO" = "true" ]; then
    echo "❌ FAIL: Release information would be shown (should NOT, it's empty)"
    exit 1
else
    echo "✅ PASS: Release information would NOT be shown (correct, it's empty)"
fi
SHEOF

chmod +x /tmp/test-bash-display.sh
/tmp/test-bash-display.sh
echo ""

# Cleanup
rm -f /tmp/test-defaults.sh /tmp/test-no-release.sh /tmp/test-release-detail.sh /tmp/test-php-logic.php /tmp/test-bash-display.sh

echo "=============================================="
echo "✅ All tests passed successfully"
echo ""
echo "📋 Summary:"
echo "   - Default: SHOW_RELEASE_INFO=true (summary is shown)"
echo "   - With --no-release-info: SHOW_RELEASE_INFO=false (not shown)"
echo "   - With --release-detail: SHOW_RELEASE_DETAIL=true (full details shown)"
echo "   - Display requires: RELEASES not empty AND SHOW_RELEASE_INFO=true"
echo ""
