<?php

class CodeController
{
    const TOKEN = [
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM0MDcyMjYsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibXVha2V5aXNyZWFsQHB0Y3QubmV0IiwiaWQiOiI2N2VhMmU4Yzc4ZTVmMjQzYjIwNjhlNjciLCJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIvYWNjb3VudHMvNjdlYTJlOGM3OGU1ZjI0M2IyMDY4ZTY3Il19fQ._TjMAOEYgeC_FIQO0V7CmBRkmHBJNaiDhxBS4EHjtE2fGEDe4rAS9lYbBTmif1NTcrm59Y9_N6zZqUfP3llXQQ",
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM0NzMwNDYsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibXVha2V5Y2FjdTEyMDFAcHRjdC5uZXQiLCJpZCI6IjY3ZTlmNzI4Zjk5Y2NlMzhlMjA1YjUxZiIsIm1lcmN1cmUiOnsic3Vic2NyaWJlIjpbIi9hY2NvdW50cy82N2U5ZjcyOGY5OWNjZTM4ZTIwNWI1MWYiXX19.sYGhw5M79uh_WvInSbS5zT-PUH93RnyJtORqWKxtsVsRR_4I9IhMzijCZz6LqqmabmcoZ_iZmLS3xQFW8R8InA",
    ];

    const API_URL = "https://api.mail.tm/";
    public function index()
    {
        $multiHandle = curl_multi_init();  // Khởi tạo handle multi
        $curlHandles = [];

        foreach (self::TOKEN as $token) {
            $url = self::API_URL . "messages";

            // Tạo cURL cho mỗi token
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$token}"
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_multi_add_handle($multiHandle, $ch);  // Thêm handle vào multi handle
            $curlHandles[] = $ch;
        }

        // Thực thi song song
        do {
            $status = curl_multi_exec($multiHandle, $active);
        } while ($status === CURLM_CALL_MULTI_PERFORM || $active);

        // Lấy kết quả
        $results = [];

        foreach ($curlHandles as $ch) {
            $results[] = curl_multi_getcontent($ch);  // Lấy kết quả của mỗi request
            curl_multi_remove_handle($multiHandle, $ch);  // Xóa handle
            curl_close($ch);  // Đóng handle cURL
        }

        $results = array_merge(json_decode($results[0], true)['hydra:member'], json_decode($results[1], true)['hydra:member']);
        usort($results, function ($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });
        curl_multi_close($multiHandle);  // Đóng multi handle

        require_once "views/index.php";
    }
}
