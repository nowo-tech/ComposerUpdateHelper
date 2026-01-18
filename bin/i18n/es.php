<?php
/**
 * Spanish translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'No hay paquetes para actualizar',
    'all_up_to_date' => 'todos los paquetes están actualizados',
    'all_have_conflicts' => 'todos los paquetes desactualizados tienen conflictos de dependencias',
    'all_ignored' => 'todos los paquetes desactualizados están ignorados',
    'all_ignored_or_conflicts' => 'todos los paquetes desactualizados están ignorados o tienen conflictos de dependencias',
    
    // Commands
    'suggested_commands' => 'Comandos sugeridos:',
    'suggested_commands_conflicts' => 'Comandos sugeridos para resolver conflictos de dependencias:',
    'includes_transitive' => '(Incluye dependencias transitivas necesarias para resolver conflictos)',
    'update_transitive_first' => '(Actualiza estas dependencias transitivas primero, luego reintenta actualizar los paquetes filtrados)',
    
    // Framework and packages
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
    'package_abandoned' => 'El paquete está abandonado',
    'replaced_by' => 'reemplazado por: %s',
    'alternative_solutions' => 'Soluciones alternativas:',
    'compatible_with_conflicts' => 'compatible con dependencias en conflicto',
    'alternative_packages' => 'Paquetes alternativos:',
    'recommended_replacement' => 'reemplazo recomendado',
    'similar_functionality' => 'funcionalidad similar',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Total outdated packages: %d',
    'debug_require_packages' => 'require packages: %d',
    'debug_require_dev_packages' => 'require-dev packages: %d',
    'debug_detected_symfony' => 'Detected Symfony constraint: %s (from extra.symfony.require)',
    'debug_processing_package' => 'Processing package: %s (installed: %s, latest: %s)',
    'debug_action_ignored' => 'Action: IGNORED (in ignore list and not in include list)',
    'debug_action_skipped' => 'Action: SKIPPED (no compatible version found due to dependency constraints)',
    'debug_action_added' => 'Action: ADDED to %s dependencies: %s',
    'debug_no_compatible_version' => 'No compatible version found for %s (proposed: %s)',
    
    // Release info
    'release_info' => 'Información de Versión',
    'release_changelog' => 'Registro de Cambios',
    'release_view_on_github' => 'Ver en GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Verificando conflictos de dependencias...',
    'checking_abandoned_packages' => '⏳ Verificando paquetes abandonados...',
    'searching_fallback_versions' => '⏳ Buscando versiones fallback...',
    'searching_alternative_packages' => '⏳ Buscando paquetes alternativos...',
    'checking_maintainer_info' => '⏳ Verificando información de mantenedores...',
    
    // Impact analysis
    'impact_analysis' => 'Análisis de impacto: Actualizar {package} a {version} afectaría a:',
    
    // Package count
    'found_outdated_packages' => 'Se encontraron %d paquete(s) desactualizado(s)',
];

