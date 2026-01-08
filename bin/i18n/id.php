<?php
/**
 * Indonesian translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Tidak ada paket untuk diperbarui',
    'all_up_to_date' => 'semua paket sudah diperbarui',
    'all_have_conflicts' => 'semua paket usang memiliki konflik dependensi',
    'all_ignored' => 'semua paket usang diabaikan',
    'all_ignored_or_conflicts' => 'semua paket usang diabaikan atau memiliki konflik dependensi',
    
    // Commands
    'suggested_commands' => 'Perintah yang disarankan:',
    'suggested_commands_conflicts' => 'Perintah yang disarankan untuk menyelesaikan konflik dependensi:',
    'includes_transitive' => '(Termasuk dependensi transitif yang diperlukan untuk menyelesaikan konflik)',
    'update_transitive_first' => '(Perbarui dependensi transitif ini terlebih dahulu, lalu coba lagi memperbarui paket yang difilter)',
    
    // Framework and packages
    'detected_framework' => 'Kendala framework yang terdeteksi:',
    'ignored_packages_prod' => 'Paket yang diabaikan (prod):',
    'ignored_packages_dev' => 'Paket yang diabaikan (dev):',
    'dependency_analysis' => 'Analisis pemeriksaan dependensi:',
    'all_outdated_before' => 'Semua paket usang (sebelum pemeriksaan dependensi):',
    'filtered_by_conflicts' => 'Difilter berdasarkan konflik dependensi:',
    'suggested_transitive' => 'Pembaruan dependensi transitif yang disarankan untuk menyelesaikan konflik:',
    'packages_passed_check' => 'Paket yang lulus pemeriksaan dependensi:',
    'none' => '(tidak ada)',
    'conflicts_with' => 'berkonflik dengan:',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Total paket usang: %d',
    'debug_require_packages' => 'paket require: %d',
    'debug_require_dev_packages' => 'paket require-dev: %d',
    'debug_detected_symfony' => 'Kendala Symfony yang terdeteksi: %s (dari extra.symfony.require)',
    'debug_processing_package' => 'Memproses paket: %s (terpasang: %s, terbaru: %s)',
    'debug_action_ignored' => 'Tindakan: DIABAIKAN (dalam daftar abaikan dan tidak dalam daftar sertakan)',
    'debug_action_skipped' => 'Tindakan: DILEWATKAN (versi kompatibel tidak ditemukan karena kendala dependensi)',
    'debug_action_added' => 'Tindakan: DITAMBAHKAN ke %s dependensi: %s',
    'debug_no_compatible_version' => 'Versi kompatibel tidak ditemukan untuk %s (diusulkan: %s)',
    
    // Release info
    'release_info' => 'Informasi Rilis',
    'release_changelog' => 'Catatan Perubahan',
    'release_view_on_github' => 'Lihat di GitHub',
];

