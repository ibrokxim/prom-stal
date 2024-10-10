<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FeedbackController extends Controller
{
    public function submitForm(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'name' => 'required',
        ]);

        $phone = $request->input('phone');
        $name = $request->input('name');

        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

        $message = "Новая заявка с сайта:\nИмя: $name\nТелефон: $phone";

        $response = Http::get("https://api.telegram.org/bot$token/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Заявка успешно отправлена!');
        } else {
            return redirect()->back()->with('error', 'Ошибка при отправке заявки!');
        }
    }
    public function submitFormWithComment(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'name' => 'required',
            'message' => 'required'
        ]);

        $name = $request->input('name');
        $phone = $request->input('phone');
        $comment = $request->input('message');

        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

        $message = "Новая заявка с сайта:\nИмя: $name\nТелефон: $phone\nКомментарий: $comment";

        $response = Http::get("https://api.telegram.org/bot$token/sendMessage", [
            'chat_id' => $chat_id,
            'text' => $message
        ]);

        if ($response->successful()) {
            return redirect()->back()->with('success', 'Заявка успешно отправлена!');
        } else {
            return redirect()->back()->with('error', 'Ошибка при отправке заявки!');
        }
    }
}
