<?php
/**
 * Portuguese translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Nenhum pacote para atualizar',
    'all_up_to_date' => 'todos os pacotes estão atualizados',
    'all_have_conflicts' => 'todos os pacotes desatualizados têm conflitos de dependências',
    'all_ignored' => 'todos os pacotes desatualizados estão ignorados',
    'all_ignored_or_conflicts' => 'todos os pacotes desatualizados estão ignorados ou têm conflitos de dependências',
    
    // Commands
    'suggested_commands' => 'Comandos sugeridos:',
    'suggested_commands_conflicts' => 'Comandos sugeridos para resolver conflitos de dependências:',
    'suggested_commands_grouped' => 'Comandos sugeridos (tente instalar juntos - Composer pode resolver conflitos melhor):',
    'grouped_install_explanation' => '(Instalar múltiplos pacotes juntos às vezes ajuda o Composer a resolver conflitos)',
    'grouped_install_warning' => '(Nota: Isso ainda pode falhar se houver conflitos com pacotes instalados que não podem ser atualizados)',
    'copy_command_hint' => '(Select the command to copy)',
    'packages_need_maintainer_update' => 'Os seguintes pacotes precisam de atualizações de seus mantenedores para suportar a instalação agrupada:',
    'package_needs_update_for_grouped' => '%s (instalado: %s) precisa de atualização para suportar: %s (requer: %s)',
    'suggest_contact_maintainer' => '💡 Considere entrar em contato com o mantenedor de %s para solicitar suporte para essas versões',
    'repository_url' => '📦 Repositório: %s',
    'maintainers' => '👤 Mantenedores: %s',
    'grouped_install_maintainer_needed' => 'Alguns pacotes instalados precisam de atualizações de seus mantenedores:',
    'package_needs_update' => '%s: Precisa de atualização para suportar %s (requer: %s)',
    'grouped_install_warning' => '(Note: This may still fail if there are conflicts with installed packages that cannot be updated)',
    'copy_command_hint' => '(Select the command to copy)',
    'includes_transitive' => '(Inclui dependências transitivas necessárias para resolver conflitos)',
    'update_transitive_first' => '(Atualize essas dependências transitivas primeiro, depois tente atualizar os pacotes filtrados)',
    
    // Framework and packages
    'detected_framework' => 'Restrições de framework detectadas:',
    'ignored_packages_prod' => 'Pacotes ignorados (prod):',
    'ignored_packages_dev' => 'Pacotes ignorados (dev):',
    'dependency_analysis' => 'Análise de verificação de dependências:',
    'all_outdated_before' => 'Todos os pacotes desatualizados (antes da verificação de dependências):',
    'filtered_by_conflicts' => 'Filtrados por conflitos de dependências:',
    'suggested_transitive' => 'Atualizações de dependências transitivas sugeridas para resolver conflitos:',
    'no_compatible_dependent_versions' => 'Nenhuma versão compatível de pacotes dependentes encontrada:',
    'no_compatible_version_explanation' => '     - {depPackage}: Nenhuma versão encontrada que suporte {requiredBy}',
    'latest_checked_constraint' => '       (A última versão verificada requer: {constraint})',
    'all_versions_require' => '       (Todas as versões disponíveis requerem: {constraint})',
    'packages_passed_check' => 'Pacotes que passaram na verificação de dependências:',
    'none' => '(nenhum)',
    'conflicts_with' => 'conflita com:',
    'package_abandoned' => 'Pacote está abandonado',
    'abandoned_packages_section' => 'Pacotes abandonados encontrados:',
    'all_installed_abandoned_section' => 'Todos os pacotes abandonados instalados:',
    'replaced_by' => 'substituído por: %s',
    'alternative_solutions' => 'Soluções alternativas:',
    'compatible_with_conflicts' => 'compatível com dependências em conflito',
    'alternative_packages' => 'Pacotes alternativos:',
    'recommended_replacement' => 'substituição recomendada',
    'similar_functionality' => 'funcionalidade similar',
    
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
    'release_info' => 'Informação de Versão',
    'release_changelog' => 'Registro de Alterações',
    'release_view_on_github' => 'Ver no GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Verificando conflitos de dependências...',
    'checking_abandoned_packages' => '⏳ Verificando pacotes abandonados...',
    'checking_all_abandoned_packages' => '⏳ Verificando todos os pacotes instalados para status de abandono...',
    'searching_fallback_versions' => '⏳ Procurando versões fallback...',
    'searching_alternative_packages' => '⏳ Procurando pacotes alternativos...',
    'checking_maintainer_info' => '⏳ Verificando informações do mantenedor...',
    
    // Impact analysis
    'impact_analysis' => 'Análise de impacto: Atualizar {package} para {version} afetaria:',
    'impact_analysis_saved' => '✅ Análise de impacto salva em: %s',
    'found_outdated_packages' => 'Encontrados %d pacote(s) desatualizado(s)',
];

