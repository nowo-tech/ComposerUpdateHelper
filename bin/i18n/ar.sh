#!/bin/bash
# Arabic translations
#
# This file contains Arabic translations for bash scripts
# Format: declare -A TRANSLATIONS_AR=([key]='value' ...)

declare -A TRANSLATIONS_AR=(
    # Main messages
    ['loading_config']='جارٍ تحميل الإعدادات...'
    ['checking_outdated']='جارٍ التحقق من الحزم القديمة...'
    ['processing']='جارٍ معالجة الحزم...'
    ['processing_php']='جارٍ معالجة الحزم باستخدام سكريبت PHP...'
    ['running']='جارٍ التشغيل...'
    ['update_completed']='اكتمل التحديث.'
    ['no_outdated']='لا توجد تبعيات مباشرة قديمة.'

    # Configuration
    ['found_config']='تم العثور على ملف الإعدادات: '
    ['no_config']='لم يتم العثور على ملف الإعدادات (استخدام القيم الافتراضية)'

    # Errors
    ['composer_not_found']='Composer غير مثبت أو غير موجود في PATH.'
    ['composer_json_not_found']='لم يتم العثور على composer.json في الدليل الحالي.'
    ['processor_not_found']='تعذر العثور على process-updates.php في vendor أو دليل السكريبتات.'
    ['please_install']='قم بتشغيل: composer install'
    ['unknown_option']='خيار غير معروف:'
    ['use_help']='استخدم --help أو -h للحصول على معلومات الاستخدام.'

    # Debug messages
    ['debug_prefix']='تصحيح: '
    ['debug_current_dir']='الدليل الحالي:'
    ['debug_searching_config']='جارٍ البحث عن ملفات الإعدادات:'
    ['debug_composer_executed']='تم تنفيذ أمر composer outdated'
    ['debug_json_length']='طول OUTDATED_JSON:'
    ['debug_empty_json']='أعاد composer outdated JSON فارغ'
    ['debug_passing_to_php']='جارٍ تمرير إلى سكريبت PHP:'
    ['debug_output_length']='طول إخراج سكريبت PHP:'
    ['debug_processor_found']='تم العثور على معالج PHP في:'
)

