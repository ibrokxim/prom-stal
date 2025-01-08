<?php

namespace App\Http\Controllers;

use App\Services\GoogleTranslateService;
use Illuminate\Http\Request;

class TranslateController extends Controller
{
    protected $translateService;

    public function __construct(GoogleTranslateService $translateService)
    {
        $this->translateService = $translateService;
    }

    public function translate(Request $request)
    {
        $text = $request->input('text');
        $targetLanguage = $request->input('target_language');

        $translation = $this->translateService->translateText($text, $targetLanguage);

        return response()->json([
            'original_text' => $text,
            'translated_text' => $translation['text'],
            'target_language' => $targetLanguage
        ]);
    }
}
