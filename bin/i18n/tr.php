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
    'packages_passed_check' => 'Bağımlılık kontrolünü geçen paketler:',
    'none' => '(yok)',
    'conflicts_with' => 'ile çakışıyor:',
    
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
];

