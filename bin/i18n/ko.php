<?php
/**
 * Korean translations
 * 
 * @package ComposerUpdateHelper
 * @subpackage i18n
 */

return [
    // Main output messages
    'no_packages_update' => '업데이트할 패키지가 없습니다',
    'all_up_to_date' => '모든 패키지가 최신입니다',
    'all_have_conflicts' => '모든 오래된 패키지에 종속성 충돌이 있습니다',
    'all_ignored' => '모든 오래된 패키지가 무시됩니다',
    'all_ignored_or_conflicts' => '모든 오래된 패키지가 무시되거나 종속성 충돌이 있습니다',
    
    // Commands
    'suggested_commands' => '권장 명령:',
    'suggested_commands_conflicts' => '종속성 충돌을 해결하기 위한 권장 명령:',
    'suggested_commands_grouped' => '권장 명령 (함께 설치해 보세요 - Composer가 충돌을 더 잘 해결할 수 있습니다):',
    'grouped_install_explanation' => '(여러 패키지를 함께 설치하면 때때로 Composer가 충돌을 해결하는 데 도움이 됩니다)',
    'grouped_install_warning' => '(참고: 업데이트할 수 없는 설치된 패키지와 충돌이 있는 경우 여전히 실패할 수 있습니다)',
    'copy_command_hint' => '(Click to copy or select the command)',
    'packages_need_maintainer_update' => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped' => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer' => '💡 Consider contacting the maintainer of %s',
    'repository_url' => '📦 Repository: %s',
    'maintainers' => '👤 Maintainers: %s',
    'grouped_install_maintainer_needed' => '일부 설치된 패키지는 maintainer의 업데이트가 필요합니다:',
    'package_needs_update' => '%s: %s를 지원하려면 업데이트가 필요합니다 (필요: %s)',
    'grouped_install_warning' => '(Note: This may still fail if there are conflicts with installed packages that cannot be updated)',
    'copy_command_hint' => '(Click to copy or select the command)',
    'packages_need_maintainer_update' => '(The following packages need updates from their maintainers)',
    'package_needs_update_for_grouped' => '%s (installed: %s) needs update to support: %s (requires: %s)',
    'suggest_contact_maintainer' => '💡 Consider contacting the maintainer of %s',
    'repository_url' => '📦 Repository: %s',
    'maintainers' => '👤 Maintainers: %s',
    'includes_transitive' => '(충돌을 해결하는 데 필요한 전이 종속성 포함)',
    'update_transitive_first' => '(먼저 이러한 전이 종속성을 업데이트한 다음 필터링된 패키지 업데이트를 다시 시도하세요)',
    
    // Framework and packages
    'detected_framework' => '감지된 프레임워크 제약:',
    'ignored_packages_prod' => '무시된 패키지 (prod):',
    'ignored_packages_dev' => '무시된 패키지 (dev):',
    'dependency_analysis' => '종속성 확인 분석:',
    'all_outdated_before' => '모든 오래된 패키지 (종속성 확인 전):',
    'filtered_by_conflicts' => '종속성 충돌로 필터링됨:',
    'suggested_transitive' => '충돌을 해결하기 위한 권장 전이 종속성 업데이트:',
    'no_compatible_dependent_versions' => '호환 가능한 종속 패키지 버전을 찾을 수 없음:',
    'no_compatible_version_explanation' => '     - {depPackage}: {requiredBy}를 지원하는 버전을 찾을 수 없음',
    'latest_checked_constraint' => '       (최신 확인된 버전에는 다음이 필요함: {constraint})',
    'all_versions_require' => '       (사용 가능한 모든 버전에는 다음이 필요함: {constraint})',
    'packages_passed_check' => '종속성 확인을 통과한 패키지:',
    'none' => '(없음)',
    'conflicts_with' => '다음과 충돌:',
    'package_abandoned' => '패키지가 포기됨',
    'abandoned_packages_section' => '중단된 패키지 발견:',
    'all_installed_abandoned_section' => '설치된 모든 중단된 패키지:',
    'replaced_by' => '대체됨: %s',
    'alternative_solutions' => '대안 솔루션:',
    'compatible_with_conflicts' => '충돌하는 종속성과 호환',
    'alternative_packages' => '대체 패키지:',
    'recommended_replacement' => '권장 대체',
    'similar_functionality' => '유사한 기능',
    
    // Debug messages
    'debug_show_release_info' => 'showReleaseInfo = %s',
    'debug_check_dependencies' => 'checkDependencies = %s',
    'debug_ignored_count' => 'ignoredPackages count = %d',
    'debug_included_count' => 'includedPackages count = %d',
    'debug_ignored_list' => 'ignoredPackages list: %s',
    'debug_total_outdated' => '오래된 패키지 총계: %d',
    'debug_require_packages' => 'require 패키지: %d',
    'debug_require_dev_packages' => 'require-dev 패키지: %d',
    'debug_detected_symfony' => '감지된 Symfony 제약: %s (extra.symfony.require에서)',
    'debug_processing_package' => '패키지 처리 중: %s (설치됨: %s, 최신: %s)',
    'debug_action_ignored' => '작업: 무시됨 (무시 목록에 있고 포함 목록에 없음)',
    'debug_action_skipped' => '작업: 건너뜀 (종속성 제약으로 인해 호환 가능한 버전을 찾을 수 없음)',
    'debug_action_added' => '작업: %s 종속성에 추가됨: %s',
    'debug_no_compatible_version' => '%s에 대한 호환 가능한 버전을 찾을 수 없음 (제안: %s)',
    
    // Release info
    'release_info' => '릴리스 정보',
    'release_changelog' => '변경 로그',
    'release_view_on_github' => 'GitHub에서 보기',
    
    // Progress messages
    'checking_dependency_conflicts' => '⏳ 종속성 충돌 확인 중...',
    'checking_abandoned_packages' => '⏳ 중단된 패키지 확인 중...',
    'checking_all_abandoned_packages' => '⏳ 설치된 모든 패키지의 중단 상태 확인 중...',
    'searching_fallback_versions' => '⏳ 대체 버전 검색 중...',
    'searching_alternative_packages' => '⏳ 대체 패키지 검색 중...',
    'checking_maintainer_info' => '⏳ 유지 관리자 정보 확인 중...',
    
    // Impact analysis
    'impact_analysis' => '영향 분석: {package}를 {version}로 업데이트하면 다음에 영향을 줍니다:',
    'impact_analysis_saved' => '✅ 영향 분석 저장됨: %s',
    'found_outdated_packages' => '오래된 패키지 %d개 발견',
];

