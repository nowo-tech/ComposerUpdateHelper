<?php

declare(strict_types=1);

/**
 * Shared HTTP defaults for Packagist/GitHub lookups (REQ-RUNTIME-001).
 *
 * @author Héctor Franco Aceituno <hectorfranco@nowo.tech>
 */
final class HttpClientDefaults
{
    /** Default total HTTP timeout in seconds for Packagist/GitHub file_get_contents calls. */
    public const TIMEOUT_SECONDS = 5;

    public const USER_AGENT = 'Composer Update Helper';

    /**
     * @param list<string> $extraHeaders Extra HTTP header lines (e.g. Accept)
     *
     * @return resource
     */
    public static function streamContext(array $extraHeaders = [])
    {
        $http = [
            'timeout'    => self::TIMEOUT_SECONDS,
            'user_agent' => self::USER_AGENT,
        ];
        if ($extraHeaders !== []) {
            $http['header'] = implode("\r\n", $extraHeaders);
        }

        return stream_context_create(['http' => $http]);
    }
}
