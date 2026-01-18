<?php
/**
 * Thai translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => 'ไม่มีแพ็กเกจที่จะอัปเดต',
    'all_up_to_date' => 'แพ็กเกจทั้งหมดเป็นเวอร์ชันล่าสุด',
    'all_have_conflicts' => 'แพ็กเกจที่ล้าสมัยทั้งหมดมีข้อขัดแย้งของ dependencies',
    'all_ignored' => 'แพ็กเกจที่ล้าสมัยทั้งหมดถูกละเว้น',
    'all_ignored_or_conflicts' => 'แพ็กเกจที่ล้าสมัยทั้งหมดถูกละเว้นหรือมีข้อขัดแย้งของ dependencies',
    
    // Commands
    'suggested_commands' => 'คำสั่งที่แนะนำ:',
    'suggested_commands_conflicts' => 'คำสั่งที่แนะนำเพื่อแก้ไขข้อขัดแย้งของ dependencies:',
    'includes_transitive' => '(รวม dependencies แบบ transitive ที่จำเป็นในการแก้ไขข้อขัดแย้ง)',
    'update_transitive_first' => '(อัปเดต dependencies แบบ transitive เหล่านี้ก่อน แล้วลองอัปเดตแพ็กเกจที่กรองแล้วอีกครั้ง)',
    
    // Framework and packages
    'detected_framework' => 'ข้อจำกัดของ framework ที่ตรวจพบ:',
    'ignored_packages_prod' => 'แพ็กเกจที่ละเว้น (prod):',
    'ignored_packages_dev' => 'แพ็กเกจที่ละเว้น (dev):',
    'dependency_analysis' => 'การวิเคราะห์การตรวจสอบ dependencies:',
    'all_outdated_before' => 'แพ็กเกจที่ล้าสมัยทั้งหมด (ก่อนการตรวจสอบ dependencies):',
    'filtered_by_conflicts' => 'กรองตามข้อขัดแย้งของ dependencies:',
    'suggested_transitive' => 'การอัปเดต dependencies แบบ transitive ที่แนะนำเพื่อแก้ไขข้อขัดแย้ง:',
    'packages_passed_check' => 'แพ็กเกจที่ผ่านการตรวจสอบ dependencies:',
    'none' => '(ไม่มี)',
    'conflicts_with' => 'ขัดแย้งกับ:',
    'package_abandoned' => 'แพ็กเกจถูกทิ้ง',
    'replaced_by' => 'แทนที่ด้วย: %s',
    'alternative_solutions' => 'โซลูชันทางเลือก:',
    'compatible_with_conflicts' => 'เข้ากันได้กับ dependencies ที่ขัดแย้ง',
    'alternative_packages' => 'แพ็กเกจทางเลือก:',
    'recommended_replacement' => 'การแทนที่ที่แนะนำ',
    'similar_functionality' => 'ฟังก์ชันการทำงานที่คล้ายกัน',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => 'แพ็กเกจที่ล้าสมัยทั้งหมด: %d',
    'debug_require_packages' => 'แพ็กเกจ require: %d',
    'debug_require_dev_packages' => 'แพ็กเกจ require-dev: %d',
    'debug_detected_symfony' => 'ข้อจำกัดของ Symfony ที่ตรวจพบ: %s (จาก extra.symfony.require)',
    'debug_processing_package' => 'กำลังประมวลผลแพ็กเกจ: %s (ติดตั้งแล้ว: %s, ล่าสุด: %s)',
    'debug_action_ignored' => 'การดำเนินการ: ละเว้น (อยู่ในรายการละเว้นและไม่อยู่ในรายการรวม)',
    'debug_action_skipped' => 'การดำเนินการ: ข้าม (ไม่พบเวอร์ชันที่เข้ากันได้เนื่องจากข้อจำกัดของ dependencies)',
    'debug_action_added' => 'การดำเนินการ: เพิ่มไปยัง %s dependencies: %s',
    'debug_no_compatible_version' => 'ไม่พบเวอร์ชันที่เข้ากันได้สำหรับ %s (เสนอ: %s)',
    
    // Release info
    'release_info' => 'ข้อมูลการเผยแพร่',
    'release_changelog' => 'บันทึกการเปลี่ยนแปลง',
    'release_view_on_github' => 'ดูบน GitHub',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ กำลังตรวจสอบความขัดแย้งของ dependencies...',
    'checking_abandoned_packages' => '⏳ กำลังตรวจสอบแพ็กเกจที่ถูกทิ้ง...',
    'searching_fallback_versions' => '⏳ กำลังค้นหาเวอร์ชันสำรอง...',
    'searching_alternative_packages' => '⏳ กำลังค้นหาแพ็กเกจทางเลือก...',
    'checking_maintainer_info' => '⏳ กำลังตรวจสอบข้อมูลผู้ดูแล...',
    
    // Impact analysis
    'impact_analysis' => 'การวิเคราะห์ผลกระทบ: การอัปเดต {package} เป็น {version} จะส่งผลต่อ:',
    'found_outdated_packages' => 'พบแพ็กเกจที่ล้าสมัย %d รายการ',
];

