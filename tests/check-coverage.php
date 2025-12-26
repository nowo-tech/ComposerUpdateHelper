#!/usr/bin/env php
<?php

declare(strict_types=1);
/**
 * Script to validate code coverage is 90%
 * Same logic as CI/CD pipeline
 */
$coverageFile = __DIR__ . '/../coverage.xml';
if (!file_exists($coverageFile)) {
    echo "ERROR: coverage.xml file was not generated\n";
    echo "Run first: composer test-coverage\n";
    exit(1);
}

$coverage = simplexml_load_file($coverageFile);
if ($coverage === false) {
    echo "ERROR: Could not read coverage.xml\n";
    exit(1);
}

$metrics = $coverage->project->metrics;
$elements = (float) $metrics['elements'];
$coveredElements = (float) $metrics['coveredelements'];

if ($elements == 0) {
    echo "No elements to cover\n";
    exit(0);
}

$percentage = ($coveredElements / $elements) * 100;

echo "Coverage: {$coveredElements}/{$elements} (" . number_format($percentage, 2) . "%)\n";

if ($percentage < 90) {
    echo 'ERROR: Coverage must be 90%. Current: ' . number_format($percentage, 2) . "%\n";

    // Show which files are not fully covered
    echo "\n📋 Files with incomplete coverage:\n";
    foreach ($coverage->project->file as $file) {
        $fileMetrics = $file->metrics;
        $fileElements = (float) $fileMetrics['elements'];
        $fileCovered = (float) $fileMetrics['coveredelements'];

        if ($fileElements > 0) {
            $filePercentage = ($fileCovered / $fileElements) * 100;
            if ($filePercentage < 90) {
                echo sprintf(
                    "  - %s: %.2f%% (%d/%d)\n",
                    $file['name'],
                    $filePercentage,
                    (int) $fileCovered,
                    (int) $fileElements
                );
            }
        }
    }

    exit(1);
}

echo "✅ 90% coverage confirmed\n";
exit(0);
