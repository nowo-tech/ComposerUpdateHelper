<?php
/**
 * Turkish translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Güncellenecek paket yok',
    'all_up_to_date' => 'tüm paketler güncel',
    'all_have_conflicts' => 'tüm eski paketlerin bağımlılık çakışmaları var',
    'all_ignored' => 'tüm eski paketler yok sayılıyor',
    'all_ignored_or_conflicts' => 'tüm eski paketler yok sayılıyor veya bağımlılık çakışmaları var',
    
    // Commands
    'suggested_commands' => 'Önerilen komutlar:',
    'suggested_commands_conflicts' => 'Bağımlılık çakışmalarını çözmek için önerilen komutlar:',
    'suggested_commands_grouped' => 'Önerilen komutlar (birlikte yüklemeyi deneyin - Composer çakışmaları daha iyi çözebilir):',
    'grouped_install_explanation' => '(Birden fazla paketi birlikte yüklemek bazen Composer\'ın çakışmaları çözmesine yardımcı olur)',
    'grouped_install_warning' => '(Not: Güncellenemeyen yüklü paketlerle çakışmalar varsa, bu hala başarısız olabilir)',
    'copy_command_hint' => '(Select the command to copy)',
    'packages_need_maintainer_update' => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped' => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer' => '💡 Consider contacting the maintainer of %s',
    'repository_url' => '📦 Repository: %s',
    'maintainers' => '👤 Maintainers: %s',
    'includes_transitive' => '(Çakışmaları çözmek için gerekli geçişli bağımlılıkları içerir)',
    'update_transitive_first' => '(Önce bu geçişli bağımlılıkları güncelleyin, ardından filtrelenmiş paketleri güncellemeyi tekrar deneyin)',
    
    // Framework and packages
    'detected_framework' => 'Algılanan çerçeve kısıtlamaları:',
    'ignored_packages_prod' => 'Yok sayılan paketler (prod):',
    'ignored_packages_dev' => 'Yok sayılan paketler (dev):',
    'dependency_analysis' => 'Bağımlılık kontrolü analizi:',
    'all_outdated_before' => 'Tüm eski paketler (bağımlılık kontrolünden önce):',
    'filtered_by_conflicts' => 'Bağımlılık çakışmalarına göre filtrelendi:',
    'suggested_transitive' => 'Çakışmaları çözmek için önerilen geçişli bağımlılık güncellemeleri:',
    'no_compatible_dependent_versions' => 'Bağımlı paketlerin uyumlu sürümleri bulunamadı:',
    'no_compatible_version_explanation' => '     - {depPackage}: {requiredBy} destekleyen sürüm bulunamadı',
    'latest_checked_constraint' => '       (Son kontrol edilen sürüm gerektirir: {constraint})',
    'all_versions_require' => '       (Tüm mevcut sürümler gerektirir: {constraint})',
    'packages_passed_check' => 'Bağımlılık kontrolünü geçen paketler:',
    'none' => '(yok)',
    'conflicts_with' => 'ile çakışıyor:',
    'package_abandoned' => 'Paket terk edildi',
    'abandoned_packages_section' => 'Terk edilmiş paketler bulundu:',
    'all_installed_abandoned_section' => 'Yüklü tüm terk edilmiş paketler:',
    'replaced_by' => 'değiştirildi: %s',
    'alternative_solutions' => 'Alternatif çözümler:',
    'compatible_with_conflicts' => 'çakışan bağımlılıklarla uyumlu',
    'alternative_packages' => 'Alternatif paketler:',
    'recommended_replacement' => 'önerilen değiştirme',
    'similar_functionality' => 'benzer işlevsellik',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Toplam eski paket: %d',
    'debug_require_packages' => 'require paketler: %d',
    'debug_require_dev_packages' => 'require-dev paketler: %d',
    'debug_detected_symfony' => 'Algılanan Symfony kısıtlaması: %s (extra.symfony.require\'dan)',
    'debug_processing_package' => 'Paket işleniyor: %s (yüklü: %s, en son: %s)',
    'debug_action_ignored' => 'Eylem: YOK SAYILDI (yok sayma listesinde ve dahil etme listesinde değil)',
    'debug_action_skipped' => 'Eylem: ATLANDI (bağımlılık kısıtlamaları nedeniyle uyumlu sürüm bulunamadı)',
    'debug_action_added' => 'Eylem: %s bağımlılıklarına EKLENDI: %s',
    'debug_no_compatible_version' => '%s için uyumlu sürüm bulunamadı (önerilen: %s)',
    
    // Release info
    'release_info' => 'Sürüm Bilgisi',
    'release_changelog' => 'Değişiklik Günlüğü',
    'release_view_on_github' => 'GitHub\'da Görüntüle',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Bağımlılık çakışmaları kontrol ediliyor...',
    'checking_abandoned_packages' => '⏳ Terk edilmiş paketler kontrol ediliyor...',
    'checking_all_abandoned_packages' => '⏳ Yüklü tüm paketlerin terk edilme durumu kontrol ediliyor...',
    'searching_fallback_versions' => '⏳ Yedek sürümler aranıyor...',
    'searching_alternative_packages' => '⏳ Alternatif paketler aranıyor...',
    'checking_maintainer_info' => '⏳ Bakımcı bilgileri kontrol ediliyor...',
    
    // Impact analysis
    'impact_analysis' => 'Etki analizi: {package} paketini {version} sürümüne güncellemek şunları etkiler:',
    'impact_analysis_saved' => '✅ Etki analizi kaydedildi: %s',
    'found_outdated_packages' => '%d eski paket bulundu',
];

