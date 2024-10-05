<?php

namespace App\Http\Middleware;

use App\Models\IpWhiteList;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckIpWhiteList
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $ipAddress = $request->ip();

        $Ipwhitelists = IpWhiteList::where('ip_address', $ipAddress)->first();

        if(!$Ipwhitelists){
            $this->sendTelegramNotification($ipAddress);
            return response()->json([
                'status' => 'error',
                'message' => 'Access denied. You are not authorized to access this resource.',
                'error' => 'ACCESS_DENIED'
            ], 403);
        }

        return $next($request);
    }

    private function sendTelegramNotification($ip){
        $message = "⚠️ Unauthorized IP Detected: ". $ip;

        $response = Http::post("https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN') . "/sendMessage", [
            'chat_id' => env('TELEGRAM_CHAT_ID'),
            'text' => $message
        ]);

        if ($response->failed()) {
            Log::error("Failed to send Telegram message: " . $response->body());
        }
    }
}
