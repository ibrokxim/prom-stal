<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RequestController extends Controller
{
    public function sendRequestToTelegram(Request $request)
    {
        $request->validate([
            'product' => 'required',
            'count' => 'required',
            'phone' => 'required',
            'email' => 'required',
        ]);

        $product = $request->input('product');
        $count = $request->input('count');
        $phone = $request->input('phone');
        $email = $request->input('email');

        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

        $message = "Новый заказ с сайта:\nТовар: $product\nКоличество: $count\nНомер: $phone\nEmail: $email";

        $response = Http::get("https://api.telegram.org/bot$token/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Заявка успешно отправлена!');
        } else {
            \Log::error('Ошибка при отправке заявки: ' . $response->body());
            return redirect()->back()->with('error', 'Ошибка при отправке заявки!');
        }
    }
}
