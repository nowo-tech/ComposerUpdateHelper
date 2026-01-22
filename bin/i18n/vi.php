<?php
/**
 * Vietnamese translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'Không có gói nào để cập nhật',
    'all_up_to_date' => 'tất cả các gói đều đã cập nhật',
    'all_have_conflicts' => 'tất cả các gói lỗi thời đều có xung đột phụ thuộc',
    'all_ignored' => 'tất cả các gói lỗi thời đều bị bỏ qua',
    'all_ignored_or_conflicts' => 'tất cả các gói lỗi thời đều bị bỏ qua hoặc có xung đột phụ thuộc',
    
    // Commands
    'suggested_commands' => 'Lệnh được đề xuất:',
    'suggested_commands_conflicts' => 'Lệnh được đề xuất để giải quyết xung đột phụ thuộc:',
    'suggested_commands_grouped' => 'Lệnh được đề xuất (thử cài đặt cùng nhau - Composer có thể giải quyết xung đột tốt hơn):',
    'grouped_install_explanation' => '(Cài đặt nhiều gói cùng nhau đôi khi giúp Composer giải quyết xung đột)',
    'includes_transitive' => '(Bao gồm các phụ thuộc chuyển tiếp cần thiết để giải quyết xung đột)',
    'update_transitive_first' => '(Cập nhật các phụ thuộc chuyển tiếp này trước, sau đó thử lại cập nhật các gói đã lọc)',
    
    // Framework and packages
    'detected_framework' => 'Ràng buộc framework đã phát hiện:',
    'ignored_packages_prod' => 'Gói bị bỏ qua (prod):',
    'ignored_packages_dev' => 'Gói bị bỏ qua (dev):',
    'dependency_analysis' => 'Phân tích kiểm tra phụ thuộc:',
    'all_outdated_before' => 'Tất cả các gói lỗi thời (trước khi kiểm tra phụ thuộc):',
    'filtered_by_conflicts' => 'Đã lọc theo xung đột phụ thuộc:',
    'suggested_transitive' => 'Cập nhật phụ thuộc chuyển tiếp được đề xuất để giải quyết xung đột:',
    'no_compatible_dependent_versions' => 'Không tìm thấy phiên bản tương thích của các gói phụ thuộc:',
    'no_compatible_version_explanation' => '     - {depPackage}: Không tìm thấy phiên bản hỗ trợ {requiredBy}',
    'latest_checked_constraint' => '       (Phiên bản được kiểm tra gần đây nhất yêu cầu: {constraint})',
    'all_versions_require' => '       (Tất cả các phiên bản có sẵn yêu cầu: {constraint})',
    'packages_passed_check' => 'Gói đã vượt qua kiểm tra phụ thuộc:',
    'none' => '(không có)',
    'conflicts_with' => 'xung đột với:',
    'package_abandoned' => 'Gói đã bị bỏ rơi',
    'abandoned_packages_section' => 'Tìm thấy các gói đã bị bỏ rơi:',
    'all_installed_abandoned_section' => 'Tất cả các gói đã bị bỏ rơi đã cài đặt:',
    'replaced_by' => 'được thay thế bởi: %s',
    'alternative_solutions' => 'Giải pháp thay thế:',
    'compatible_with_conflicts' => 'tương thích với các phụ thuộc xung đột',
    'alternative_packages' => 'Gói thay thế:',
    'recommended_replacement' => 'thay thế được khuyến nghị',
    'similar_functionality' => 'chức năng tương tự',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'Tổng số gói lỗi thời: %d',
    'debug_require_packages' => 'gói require: %d',
    'debug_require_dev_packages' => 'gói require-dev: %d',
    'debug_detected_symfony' => 'Ràng buộc Symfony đã phát hiện: %s (từ extra.symfony.require)',
    'debug_processing_package' => 'Đang xử lý gói: %s (đã cài đặt: %s, mới nhất: %s)',
    'debug_action_ignored' => 'Hành động: BỎ QUA (trong danh sách bỏ qua và không trong danh sách bao gồm)',
    'debug_action_skipped' => 'Hành động: BỎ QUA (không tìm thấy phiên bản tương thích do ràng buộc phụ thuộc)',
    'debug_action_added' => 'Hành động: ĐÃ THÊM vào %s phụ thuộc: %s',
    'debug_no_compatible_version' => 'Không tìm thấy phiên bản tương thích cho %s (đề xuất: %s)',
    
    // Release info
    'release_info' => 'Thông tin phiên bản',
    'release_changelog' => 'Nhật ký thay đổi',
    'release_view_on_github' => 'Xem trên GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ Đang kiểm tra xung đột phụ thuộc...',
    'checking_abandoned_packages' => '⏳ Đang kiểm tra các gói bị bỏ hoang...',
    'checking_all_abandoned_packages' => '⏳ Đang kiểm tra tất cả các gói đã cài đặt để xem trạng thái bị bỏ rơi...',
    'searching_fallback_versions' => '⏳ Đang tìm kiếm phiên bản dự phòng...',
    'searching_alternative_packages' => '⏳ Đang tìm kiếm các gói thay thế...',
    'checking_maintainer_info' => '⏳ Đang kiểm tra thông tin người bảo trì...',
    
    // Impact analysis
    'impact_analysis' => 'Phân tích tác động: Cập nhật {package} lên {version} sẽ ảnh hưởng đến:',
    'impact_analysis_saved' => '✅ Phân tích tác động đã lưu vào: %s',
    'found_outdated_packages' => 'Tìm thấy %d gói lỗi thời',
];

