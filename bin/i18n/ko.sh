#!/bin/bash
# Korean translations
#
# This file contains Korean translations for bash scripts
# Format: declare -A TRANSLATIONS_KO=([key]='value' ...)

declare -A TRANSLATIONS_KO=(
    # Main messages
    ['loading_config']='구성 로드 중...'
    ['checking_outdated']='오래된 패키지 확인 중...'
    ['processing']='패키지 처리 중...'
    ['processing_php']='PHP 스크립트로 패키지 처리 중...'
    ['running']='실행 중...'
    ['update_completed']='업데이트 완료.'
    ['no_outdated']='오래된 직접 종속성이 없습니다.'

    # Configuration
    ['found_config']='구성 파일 찾음: '
    ['no_config']='구성 파일을 찾을 수 없습니다 (기본값 사용)'

    # Errors
    ['composer_not_found']='Composer가 설치되어 있지 않거나 PATH에 없습니다.'
    ['composer_json_not_found']='현재 디렉토리에서 composer.json을 찾을 수 없습니다.'
    ['processor_not_found']='vendor 또는 스크립트 디렉토리에서 process-updates.php를 찾을 수 없습니다.'
    ['please_install']='실행: composer install'
    ['unknown_option']='알 수 없는 옵션:'
    ['use_help']='사용 정보는 --help 또는 -h를 사용하세요.'

    # Debug messages
    ['debug_prefix']='디버그: '
    ['debug_current_dir']='현재 디렉토리:'
    ['debug_searching_config']='구성 파일 검색 중:'
    ['debug_composer_executed']='composer outdated 명령 실행됨'
    ['debug_json_length']='OUTDATED_JSON 길이:'
    ['debug_empty_json']='Composer outdated가 빈 JSON을 반환했습니다'
    ['debug_passing_to_php']='PHP 스크립트로 전달 중:'
    ['debug_output_length']='PHP 스크립트 출력 길이:'
    ['debug_processor_found']='PHP 프로세서 찾음:'
)

