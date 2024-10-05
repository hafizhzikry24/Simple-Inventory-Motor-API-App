<?php

namespace App\Http\Middleware;

use App\Models\IpWhiteList;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckIpWhiteList
{
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();


        if (Cache::has('notified_ip_' . $ipAddress)) {
            Log::info("IP $ipAddress sudah diberi notifikasi sebelumnya.");
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. You are not authorized to access this resource.',
                'error' => 'ACCESS_DENIED'
            ], 403);
        }

        $Ipwhitelists = IpWhiteList::where('ip_address', $ipAddress)->first();

        if (!$Ipwhitelists) {
            $this->sendTelegramNotification($ipAddress);

            Cache::put('notified_ip_' . $ipAddress, true, now()->addSeconds(3));
            Log::info("Notifikasi dikirim untuk IP: $ipAddress");

            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. You are not authorized to access this resource.',
                'error' => 'ACCESS_DENIED'
            ], 403);
        }

        return $next($request);
    }

    private function sendTelegramNotification($ip)
    {
        $messages = "⚠️ Unauthorized IP Detected: " . $ip;

        $response = Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => $messages
        ]);

        if ($response->failed()) {
            Log::error("Failed to send Telegram message: " . $response->body());
        }
    }
}
