<?php

declare(strict_types=1);

/**
 * Autoloader for lib classes
 * Loads all library classes used by process-updates.php
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */

// Load all library classes
require_once __DIR__ . '/ConfigLoader.php';
require_once __DIR__ . '/Utils.php';
require_once __DIR__ . '/FrameworkDetector.php';
require_once __DIR__ . '/DependencyAnalyzer.php';
require_once __DIR__ . '/PackageInfoProvider.php';
require_once __DIR__ . '/AbandonedPackageDetector.php';
require_once __DIR__ . '/FallbackVersionFinder.php';
require_once __DIR__ . '/AlternativePackageFinder.php';
require_once __DIR__ . '/VersionResolver.php';
require_once __DIR__ . '/OutputFormatter.php';
require_once __DIR__ . '/MaintainerContactFinder.php';
require_once __DIR__ . '/ImpactAnalyzer.php';