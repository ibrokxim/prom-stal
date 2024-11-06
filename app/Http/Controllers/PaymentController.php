<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $invoiceId = $request->input('invoiceId');
        $amount = $request->input('amount');
        $user = Auth::user();

        $paymentObject = [
            'invoiceId' => $invoiceId,
            'invoiceIdAlt' => uniqid(),
            'backLink' => route('payment.success'),
            'failureBackLink' => route('payment.failure'),
            'postLink' => route('payment.notify'),
            'failurePostLink' => route('payment.failure', ['order_id' => $invoiceId]),
            'language' => 'rus',
            'description' => 'Оплата в интернет магазине',
            'accountId' => $user ? $user->id : 'guest',
            'terminal' => '67e34d63-102f-4bd1-898e-370781d0074d',
            'amount' => $amount,
            'data' => json_encode([
                'statement' => [
                    'name' => $user ? $user->name : 'Guest',
                    'invoiceID' => $invoiceId,
                ]
            ]),
            'currency' => 'KZT',
            'phone' => $user ? $user->phone : '77777777777',
            'name' => $user ? $user->name : 'Guest',
            'email' => $user ? $user->email : 'example@example.com',
            'cardSave' => true,
        ];

        return response()->json($paymentObject);
    }

    public function paymentSuccess()
    {
        return response()->json(['status' => 'success', 'message' => 'Payment successful']);
    }

    public function paymentFailure()
    {
        return response()->json(['status' => 'failure', 'message' => 'Payment failed']);
    }

    public function paymentNotify(Request $request)
    {
        return response()->json(['status' => 'notified']);
    }
}
