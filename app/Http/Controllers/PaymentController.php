<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function choose()
    {
        return view('payments.choose');
    }

    public function createIntent(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|in:gcash,paymaya',
            'amount' => 'required|integer|min:100', // cents
        ]);

        $secret = env('PAYMONGO_SECRET_KEY');
        if (! $secret) {
            return back()->withErrors(['payment' => 'Online payment is currently disabled. Please choose over-the-counter.']);
        }

        $payload = [
            'data' => [
                'attributes' => [
                    'amount' => (int) $data['amount'],
                    'currency' => 'PHP',
                    'payment_method_allowed' => [$data['provider']],
                    'capture_type' => 'automatic',
                    'description' => 'Enrollment Fee',
                ],
            ],
        ];

        $auth = base64_encode($secret.':');
        $resp = Http::withHeaders(['Authorization' => 'Basic '.$auth])
            ->post('https://api.paymongo.com/v1/payment_intents', $payload);

        if (! $resp->successful()) {
            return back()->withErrors(['payment' => 'Failed to initialize payment. Please try again or choose over-the-counter.']);
        }

        $intent = $resp->json()['data']['id'] ?? null;
        // For demo, we store the intent reference and show a success page placeholder.
        $payment = Payment::create([
            'account_ID' => optional(auth()->guard('web')->user())->account_ID,
            'provider' => $data['provider'],
            'amount' => (int) $data['amount'],
            'currency' => 'PHP',
            'status' => 'pending',
            'reference' => $intent,
        ]);

        return view('payments.pending', compact('payment'));
    }

    public function webhook(Request $request)
    {
        // Validate signature (optional: PayMongo webhook signing)
        $payload = $request->json('data');
        $type = $request->json('type');

        if ($type === 'payment.paid') {
            $ref = $payload['id'] ?? null;
            if ($ref) {
                Payment::where('reference', $ref)->update(['status' => 'paid']);
            }
        }

        return response()->json(['ok' => true]);
    }
}
