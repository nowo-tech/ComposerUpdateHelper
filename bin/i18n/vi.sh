#!/bin/bash
# Vietnamese translations
#
# This file contains Vietnamese translations for bash scripts
# Format: declare -A TRANSLATIONS_VI=([key]='value' ...)

declare -A TRANSLATIONS_VI=(
    # Main messages
    ['loading_config']='Đang tải cấu hình...'
    ['checking_outdated']='Đang kiểm tra gói lỗi thời...'
    ['processing']='Đang xử lý gói...'
    ['processing_php']='Đang xử lý gói bằng script PHP...'
    ['running']='Đang chạy...'
    ['update_completed']='Cập nhật hoàn tất.'
    ['no_outdated']='Không có phụ thuộc trực tiếp lỗi thời.'

    # Configuration
    ['found_config']='Đã tìm thấy tệp cấu hình: '
    ['no_config']='Không tìm thấy tệp cấu hình (sử dụng giá trị mặc định)'

    # Errors
    ['composer_not_found']='Composer chưa được cài đặt hoặc không có trong PATH.'
    ['composer_json_not_found']='Không tìm thấy composer.json trong thư mục hiện tại.'
    ['processor_not_found']='Không thể tìm thấy process-updates.php trong vendor hoặc thư mục script.'
    ['please_install']='Chạy: composer install'
    ['unknown_option']='Tùy chọn không xác định:'
    ['use_help']='Sử dụng --help hoặc -h để biết thông tin sử dụng.'

    # Debug messages
    ['debug_prefix']='DEBUG: '
    ['debug_current_dir']='Thư mục hiện tại:'
    ['debug_searching_config']='Đang tìm kiếm tệp cấu hình:'
    ['debug_composer_executed']='Lệnh composer outdated đã thực thi'
    ['debug_json_length']='Độ dài OUTDATED_JSON:'
    ['debug_empty_json']='Composer outdated trả về JSON trống'
    ['debug_passing_to_php']='Đang chuyển đến script PHP:'
    ['debug_output_length']='Độ dài đầu ra script PHP:'
    ['debug_processor_found']='Bộ xử lý PHP được tìm thấy tại:'
)

