<?php
require_once "models/Account.php";

class CodeController extends Account
{

    const API_URL = "https://api.mail.tm/";
    public function index()
    {
        $multiHandle = curl_multi_init();  // Khởi tạo handle multi
        $curlHandles = [];
        if (isset($_GET['email'])) {
            $account2 = new Account();
            $account = $account2->getAccountByEmailAndType(strtolower($_GET['email']), 'Netflix');
            if ($account != null) {
                $password = str_replace('\\', '\\\\', $account['password']);
                $password = str_replace('"', '\\"', $password);
                $data = [
                    'address' => $account['email'],
                    'password' => $password
                ];

                $token = $this->getToken($data);
                $token = isset($token) ? json_decode($token)->token : 1;
                $default = 0;
            } else {
                $default = 1;
                $token = [
                    "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NzY3Mzc0MDksInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibWluaHRyaTIwNEBkZWx0YWpvaG5zb25zLmNvbSIsImlkIjoiNjlkZTE1OWQ3OTgyNjA0ZGRmMGIxMjAzIiwibWVyY3VyZSI6eyJzdWJzY3JpYmUiOlsiL2FjY291bnRzLzY5ZGUxNTlkNzk4MjYwNGRkZjBiMTIwMyJdfX0.9iJMgTADZfCItCCmtwhyRXs5ouCVGXT8XLXvTYa8I0y6ND6AUO5GXKPTnjl-RYfuC-B7tbUPzSWwMZUaFdjnsQ",
                    "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NzY4MzEyNTEsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibmh1bmd0YW5iYTIzMTVAZGVsdGFqb2huc29ucy5jb20iLCJpZCI6IjY5ZTZmYWUyMDNlNmFkOTQ0ZjBkNDg1OSIsIm1lcmN1cmUiOnsic3Vic2NyaWJlIjpbIi9hY2NvdW50cy82OWU2ZmFlMjAzZTZhZDk0NGYwZDQ4NTkiXX19.2jveP38qRzz2jXSYs29qy8_-WIMu66h3AVP3llTLuYCqU4U1-Ne5LbtULfVljHnUn7ISHSs_bVrYCUzOEN0jkw"
                ];
            }
        } else {
            require_once "views/index.php";
            exit;
        }

        $url = self::API_URL . "messages";

        $tokens = is_array($token) ? $token : [$token];

        // Tạo cURL cho mỗi token (mỗi tool/token lấy messages riêng)
        foreach ($tokens as $t) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$t}"
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
            $decoded = json_decode($result, true);
            if (!is_array($decoded)) continue;
            if (!isset($decoded['hydra:member']) || !is_array($decoded['hydra:member'])) continue;
            $arr = array_merge($arr, $decoded['hydra:member']);
        }
        $results = $arr;

        usort($results, function ($a, $b) {
            return strtotime($b['createdAt']) - strtotime($a['createdAt']);
        });
        curl_multi_close($multiHandle);  // Đóng multi handle

        require_once "views/index.php";
    }

    public function getToken(array $accounts)
    {
        $url = 'https://api.mail.tm/token';

        // Dữ liệu cần gửi
        $data = [
            'address' => $accounts['address'],
            'password' => $accounts['password']
        ];

        // Khởi tạo CURL
        $ch = curl_init($url);

        // Thiết lập các tùy chọn
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        // Gửi dữ liệu JSON
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // Thực thi CURL và lấy kết quả
        $response = curl_exec($ch);

        // Đóng CURL
        curl_close($ch);
        return $response;
    }

    function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            // IP từ proxy
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            // IP forwarded từ proxy load balancer
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            // IP trực tiếp
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}
