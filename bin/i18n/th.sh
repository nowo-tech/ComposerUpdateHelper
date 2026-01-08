#!/bin/bash
# Thai translations
#
# This file contains Thai translations for bash scripts
# Format: declare -A TRANSLATIONS_TH=([key]='value' ...)

declare -A TRANSLATIONS_TH=(
    # Main messages
    ['loading_config']='กำลังโหลดการตั้งค่า...'
    ['checking_outdated']='กำลังตรวจสอบแพ็กเกจที่ล้าสมัย...'
    ['processing']='กำลังประมวลผลแพ็กเกจ...'
    ['processing_php']='กำลังประมวลผลแพ็กเกจด้วยสคริปต์ PHP...'
    ['running']='กำลังทำงาน...'
    ['update_completed']='การอัปเดตเสร็จสมบูรณ์'
    ['no_outdated']='ไม่มี dependencies โดยตรงที่ล้าสมัย'

    # Configuration
    ['found_config']='พบไฟล์การตั้งค่า: '
    ['no_config']='ไม่พบไฟล์การตั้งค่า (ใช้ค่าเริ่มต้น)'

    # Errors
    ['composer_not_found']='Composer ไม่ได้ติดตั้งหรือไม่อยู่ใน PATH'
    ['composer_json_not_found']='ไม่พบ composer.json ในไดเรกทอรีปัจจุบัน'
    ['processor_not_found']='ไม่สามารถหา process-updates.php ใน vendor หรือไดเรกทอรีสคริปต์'
    ['please_install']='รัน: composer install'
    ['unknown_option']='ตัวเลือกที่ไม่รู้จัก:'
    ['use_help']='ใช้ --help หรือ -h สำหรับข้อมูลการใช้งาน'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='ไดเรกทอรีปัจจุบัน:'
    ['debug_searching_config']='กำลังค้นหาไฟล์การตั้งค่า:'
    ['debug_composer_executed']='คำสั่ง composer outdated ถูกดำเนินการ'
    ['debug_json_length']='ความยาว OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated ส่งคืน JSON ว่าง'
    ['debug_passing_to_php']='กำลังส่งต่อไปยังสคริปต์ PHP:'
    ['debug_output_length']='ความยาวผลลัพธ์สคริปต์ PHP:'
    ['debug_processor_found']='พบโปรเซสเซอร์ PHP ที่:'
)

