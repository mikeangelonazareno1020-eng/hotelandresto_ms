<?php

namespace App\Http\Middleware;

use App\Models\ApiDevice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class VerifyDeviceApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $provided = (string) $request->header('X-API-KEY', '');
        $uid = (string) $request->header('X-DEVICE-UID', '');

        if ($provided === '') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Lookup by UID if provided, else scan by hash match
        $device = null;
        if ($uid !== '') {
            $candidate = ApiDevice::where('uid', $uid)->where('is_active', true)->first();
            if ($candidate && Hash::check($provided, $candidate->api_key_hash)) {
                $device = $candidate;
            }
        } else {
            // Fallback: iterate small set (relatively small table expected)
            $candidate = ApiDevice::where('is_active', true)->get();
            foreach ($candidate as $row) {
                if (Hash::check($provided, $row->api_key_hash)) {
                    $device = $row; break;
                }
            }
        }

        if (!$device) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Update last seen
        $device->forceFill([
            'last_used_at' => now(),
            'last_ip' => $request->ip(),
        ])->save();

        // Attach for downstream use
        $request->attributes->set('api_device', $device);

        return $next($request);
    }
}

