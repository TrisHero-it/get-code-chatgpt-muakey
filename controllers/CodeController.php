<?php
require_once "models/Account.php";

class CodeController extends Account
{
    const TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM2Njk3MTQsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibWFuaGJpZ2F5QHB0Y3QubmV0IiwiaWQiOiI2N2VlM2IxYzQ0ZDU2Y2E0MTEwNzMwZDciLCJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIvYWNjb3VudHMvNjdlZTNiMWM0NGQ1NmNhNDExMDczMGQ3Il19fQ.a_i4rTY3qxmMuATGGhTmsulBluWqLukWrs1-s6_kN7zvXw40U8dlYqks8bj0p8SNmiF8Jqs5YQ_ykpUPR-azlg";

    const API_URL = "https://api.mail.tm/";
    public function index()
    {
        $multiHandle = curl_multi_init();  // Khởi tạo handle multi
        if (isset($_GET['email'])) {
            $account = new Account();
            $accountCapcut = $account->getAccountByEmailAndType(strtolower($_GET['email']), 'CapCut');
            $account = $account->getAccountByEmailAndType(strtolower($_GET['email']), 'Netflix');
            if ($account != null) {
                $data = [
                    'address' => $account['email'],
                    'password' => $account['password']
                ];
                $token = $this->getToken($data);
            }
            $token = isset($token) ? json_decode($token)->token : self::TOKEN;
        } else {
            $token = self::TOKEN;
        }
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
