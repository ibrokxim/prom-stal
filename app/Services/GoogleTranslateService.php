<?php

namespace App\Services;

use Google\Cloud\Translate\V2\TranslateClient;

class GoogleTranslateService
{
    protected $translateClient;

    public function __construct()
    {
        $this->translateClient = new TranslateClient([
            'key' => env('GOOGLE_TRANSLATE_API_KEY')
        ]);
    }

    public function translateText($text, $targetLanguage)
    {
        return $this->translateClient->translate($text, [
            'target' => $targetLanguage
        ]);
    }

}
