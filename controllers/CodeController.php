<?php

class CodeController
{
    const TOKEN = [
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM2Njk3MTQsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibWFuaGJpZ2F5QHB0Y3QubmV0IiwiaWQiOiI2N2VlM2IxYzQ0ZDU2Y2E0MTEwNzMwZDciLCJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIvYWNjb3VudHMvNjdlZTNiMWM0NGQ1NmNhNDExMDczMGQ3Il19fQ.a_i4rTY3qxmMuATGGhTmsulBluWqLukWrs1-s6_kN7zvXw40U8dlYqks8bj0p8SNmiF8Jqs5YQ_ykpUPR-azlg",
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM2NzE0NTAsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibXVha2V5Nzc3Nzc3QHB0Y3QubmV0IiwiaWQiOiI2N2ViODA1OWU3NTQwYmQ5ZTEwMmNmM2YiLCJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIvYWNjb3VudHMvNjdlYjgwNTllNzU0MGJkOWUxMDJjZjNmIl19fQ.P9yqNlUx606L76uDu43yhWVAups4EhZdPf-0aH3Pdke0z9zX6FVVX2SPiORupkfVaog2T0sQxH67aGii2ypttQ",
        "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM2NzYyMDQsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibXVha2V5Nzg5MDFAcHRjdC5uZXQiLCJpZCI6IjY3ZWI4MjI3ZTc1NDBiZDllMTAyY2Y2OSIsIm1lcmN1cmUiOnsic3Vic2NyaWJlIjpbIi9hY2NvdW50cy82N2ViODIyN2U3NTQwYmQ5ZTEwMmNmNjkiXX19.fFoT7TvFmXsn4bM2CweOw8zW9n-P72mbtW3E3BA2A6UP4lY-NvTNb30pVxRgyAR8fIQn3WZSrcw1fiSu4xbbIw"
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
        $arr = [];
        foreach ($results as $result) {
            $arr = array_merge($arr, json_decode($result, true)['hydra:member']);
        }
        $results = $arr;
        usort($results, function ($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });
        curl_multi_close($multiHandle);  // Đóng multi handle

        require_once "views/index.php";
    }
}
