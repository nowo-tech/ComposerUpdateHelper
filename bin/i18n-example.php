<?php
/**
 * Example implementation of i18n (internationalization) for Composer Update Helper
 * 
 * This shows how we could detect the system language and translate messages.
 */

// Function to detect system language
function detectLanguage(): string
{
    // Try to get language from environment variables (in order of priority)
    $lang = getenv('LC_MESSAGES') ?: getenv('LC_ALL') ?: getenv('LANG') ?: 'en_US';
    
    // Extract language code (e.g., "es_ES.UTF-8" -> "es", "en_US.UTF-8" -> "en")
    $langCode = strtolower(substr($lang, 0, 2));
    
    // Supported languages
    $supported = ['en', 'es', 'fr', 'de', 'it', 'pt'];
    
    // Return detected language if supported, otherwise default to English
    return in_array($langCode, $supported, true) ? $langCode : 'en';
}

// Translation array
$translations = [
    'en' => [
        'no_packages_update' => 'No packages to update',
        'all_up_to_date' => 'all packages are up to date',
        'all_have_conflicts' => 'all outdated packages have dependency conflicts',
        'all_ignored' => 'all outdated packages are ignored',
        'all_ignored_or_conflicts' => 'all outdated packages are ignored or have dependency conflicts',
        'suggested_commands' => 'Suggested commands:',
        'suggested_commands_conflicts' => 'Suggested commands to resolve dependency conflicts:',
        'includes_transitive' => '(Includes transitive dependencies needed to resolve conflicts)',
        'update_transitive_first' => '(Update these transitive dependencies first, then retry updating the filtered packages)',
        'detected_framework' => 'Detected framework constraints:',
        'ignored_packages_prod' => 'Ignored packages (prod):',
        'ignored_packages_dev' => 'Ignored packages (dev):',
        'dependency_analysis' => 'Dependency checking analysis:',
        'all_outdated_before' => 'All outdated packages (before dependency check):',
        'filtered_by_conflicts' => 'Filtered by dependency conflicts:',
        'suggested_transitive' => 'Suggested transitive dependency updates to resolve conflicts:',
        'packages_passed_check' => 'Packages that passed dependency check:',
        'none' => '(none)',
        'conflicts_with' => 'conflicts with:',
    ],
    'es' => [
        'no_packages_update' => 'No hay paquetes para actualizar',
        'all_up_to_date' => 'todos los paquetes están actualizados',
        'all_have_conflicts' => 'todos los paquetes desactualizados tienen conflictos de dependencias',
        'all_ignored' => 'todos los paquetes desactualizados están ignorados',
        'all_ignored_or_conflicts' => 'todos los paquetes desactualizados están ignorados o tienen conflictos de dependencias',
        'suggested_commands' => 'Comandos sugeridos:',
        'suggested_commands_conflicts' => 'Comandos sugeridos para resolver conflictos de dependencias:',
        'includes_transitive' => '(Incluye dependencias transitivas necesarias para resolver conflictos)',
        'update_transitive_first' => '(Actualiza estas dependencias transitivas primero, luego reintenta actualizar los paquetes filtrados)',
        'detected_framework' => 'Restricciones de framework detectadas:',
        'ignored_packages_prod' => 'Paquetes ignorados (prod):',
        'ignored_packages_dev' => 'Paquetes ignorados (dev):',
        'dependency_analysis' => 'Análisis de verificación de dependencias:',
        'all_outdated_before' => 'Todos los paquetes desactualizados (antes de la verificación de dependencias):',
        'filtered_by_conflicts' => 'Filtrados por conflictos de dependencias:',
        'suggested_transitive' => 'Actualizaciones de dependencias transitivas sugeridas para resolver conflictos:',
        'packages_passed_check' => 'Paquetes que pasaron la verificación de dependencias:',
        'none' => '(ninguno)',
        'conflicts_with' => 'conflicta con:',
    ],
];

// Translation function
function t(string $key, ?string $lang = null): string
{
    global $translations;
    
    if ($lang === null) {
        $lang = detectLanguage();
    }
    
    // Return translation if available, otherwise return key
    return $translations[$lang][$key] ?? $translations['en'][$key] ?? $key;
}

// Example usage:
$lang = detectLanguage();
echo "Detected language: {$lang}\n\n";

echo "Examples:\n";
echo "  " . t('no_packages_update') . "\n";
echo "  " . t('suggested_commands') . "\n";
echo "  " . t('detected_framework') . "\n";
echo "  " . t('ignored_packages_prod') . "\n";

// Test with different languages
echo "\n--- Spanish ---\n";
echo "  " . t('no_packages_update', 'es') . "\n";
echo "  " . t('suggested_commands', 'es') . "\n";
echo "  " . t('detected_framework', 'es') . "\n";

