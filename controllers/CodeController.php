<?php

class CodeController
{
    const TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM0MDcyMjYsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibXVha2V5aXNyZWFsQHB0Y3QubmV0IiwiaWQiOiI2N2VhMmU4Yzc4ZTVmMjQzYjIwNjhlNjciLCJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIvYWNjb3VudHMvNjdlYTJlOGM3OGU1ZjI0M2IyMDY4ZTY3Il19fQ._TjMAOEYgeC_FIQO0V7CmBRkmHBJNaiDhxBS4EHjtE2fGEDe4rAS9lYbBTmif1NTcrm59Y9_N6zZqUfP3llXQQ";
    const API_URL = "https://api.mail.tm/";
    public function index()
    {
        $api = self::API_URL . 'messages';
        $ch = curl_init($api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . self::TOKEN,
            "Content-Type: application/json"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);
        $response = $response['hydra:member'];

        require_once "views/index.php";
    }
}
