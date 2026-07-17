<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\LoginActivity;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * Parse OS, Browser and Device from a User Agent string.
     */
    public static function parseUserAgent(?string $userAgent): array
    {
        if (empty($userAgent)) {
            return [
                'os' => 'Unknown OS',
                'browser' => 'Unknown Browser',
                'device' => 'Unknown Device',
            ];
        }

        // 1. Detect OS
        $os = 'Unknown OS';
        $osPatterns = [
            '/windows nt 10/i'      => 'Windows 10/11',
            '/windows nt 6\.3/i'     => 'Windows 8.1',
            '/windows nt 6\.2/i'     => 'Windows 8',
            '/windows nt 6\.1/i'     => 'Windows 7',
            '/macintosh|mac os x/i' => 'macOS',
            '/linux/i'              => 'Linux',
            '/ubuntu/i'             => 'Ubuntu',
            '/iphone/i'             => 'iOS (iPhone)',
            '/ipad/i'               => 'iOS (iPad)',
            '/android/i'            => 'Android',
        ];
        foreach ($osPatterns as $pattern => $value) {
            if (preg_match($pattern, $userAgent)) {
                $os = $value;
                break;
            }
        }

        // 2. Detect Browser
        $browser = 'Unknown Browser';
        $browserPatterns = [
            '/edge/i'    => 'Edge',
            '/chrome/i'  => 'Chrome',
            '/firefox/i' => 'Firefox',
            '/safari/i'  => 'Safari',
            '/opera|opr/i' => 'Opera',
            '/msie|trident/i' => 'Internet Explorer',
        ];
        foreach ($browserPatterns as $pattern => $value) {
            if (preg_match($pattern, $userAgent)) {
                $browser = $value;
                break;
            }
        }
        // Fix Chrome/Safari confusion
        if ($browser === 'Safari' && preg_match('/chrome/i', $userAgent)) {
            $browser = 'Chrome';
        }

        // 3. Detect Device
        $device = 'Desktop';
        if (preg_match('/mobile|phone|ipod/i', $userAgent)) {
            $device = 'Mobile';
        } elseif (preg_match('/tablet|ipad|playbook|silk/i', $userAgent)) {
            $device = 'Tablet';
        }

        return compact('os', 'browser', 'device');
    }

    /**
     * Log a login activity.
     */
    public static function logLoginActivity(
        bool $success,
        ?string $email = null,
        ?int $userId = null,
        string $type = 'login',
        ?string $failureReason = null
    ): void {
        $ip = request()->ip();
        $userAgent = request()->userAgent();
        $uaParsed = self::parseUserAgent($userAgent);

        LoginActivity::create([
            'user_id' => $userId,
            'email' => $email,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'browser' => $uaParsed['browser'],
            'operating_system' => $uaParsed['os'],
            'device' => $uaParsed['device'],
            'success' => $success,
            'failure_reason' => $failureReason,
            'type' => $type,
        ]);
    }

    /**
     * Log an audit log action.
     */
    public static function log(
        string $action,
        ?string $affectedTable = null,
        ?int $affectedId = null,
        ?array $changes = null
    ): void {
        $userId = Auth::id();
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        AuditLog::create([
            'user_id' => $userId,
            'action' => $action,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'affected_table' => $affectedTable,
            'affected_id' => $affectedId,
            'changes' => $changes,
        ]);
    }
}
