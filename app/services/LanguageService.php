<?php

namespace App\Services;

class LanguageService {

    protected array $data = [];

    function __construct()
    {
        $storagePath = storage_path('app/access-lang.json');
        $this->data = json_decode(file_get_contents($storagePath), true);
    }

    public function is_lang(string $code)
    {
        return in_array(strtolower($code), $this->data);
    }
}