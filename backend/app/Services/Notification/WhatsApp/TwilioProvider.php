<?php

namespace App\Services\Notification\WhatsApp;

class TwilioProvider implements WhatsAppProviderInterface
{
    public function send(string $to, string $message): bool
    {
        $sid = config('langkahkecil.whatsapp.providers.twilio.sid');
        $token = config('langkahkecil.whatsapp.providers.twilio.token');
        $from = config('langkahkecil.whatsapp.providers.twilio.from');

        if (!$sid || !$token || !$from) {
            \Log::warning('[Twilio] SID/Token/From not configured');
            return false;
        }

        $phone = $this->normalizePhone($to);

        try {
            $response = \Http::withBasicAuth($sid, $token)
                ->asForm()
                ->timeout(30)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                    'From' => "whatsapp:{$from}",
                    'To' => "whatsapp:{$phone}",
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                return true;
            }

            \Log::warning('[Twilio] Send failed', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Throwable $e) {
            \Log::warning('[Twilio] Exception: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizePhone(string $to): string
    {
        $phone = preg_replace('/[^0-9]/', '', $to);
        if (str_starts_with($phone, '0')) {
            $phone = '+62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        return $phone;
    }
}
