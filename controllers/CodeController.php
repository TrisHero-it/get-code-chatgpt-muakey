<?php

class CodeController
{
    const TOKEN = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE3NDM2Njk3MTQsInJvbGVzIjpbIlJPTEVfVVNFUiJdLCJhZGRyZXNzIjoibWFuaGJpZ2F5QHB0Y3QubmV0IiwiaWQiOiI2N2VlM2IxYzQ0ZDU2Y2E0MTEwNzMwZDciLCJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIvYWNjb3VudHMvNjdlZTNiMWM0NGQ1NmNhNDExMDczMGQ3Il19fQ.a_i4rTY3qxmMuATGGhTmsulBluWqLukWrs1-s6_kN7zvXw40U8dlYqks8bj0p8SNmiF8Jqs5YQ_ykpUPR-azlg";

    const ACCOUNTS = [
        '2lenore@ptct.net' => 'a0~|3CtfIb',
        "88daniela@ptct.net" => "?zj!v\8#Ve",
        "8639labour@ptct.net" => "f[(%@D[R'L",
        "2543warm@indigobook.com" => "F0y}DkHt42",
        "9768clarey@indigobook.com" => "UcK[Ntz.lz",
        "936carroll@ptct.net" => "#h1,EhK?yO",
        "2gabi@ptct.net" => "T%cv5/,^PQ",
        "reginadouble@ptct.net" => "HHYyz\v6x#",
        "726golda@ptct.net" => "^5g[sM&]5e",
        "819twyla@ptct.net" => "Owp9y67Ma|",
        "babbie50@ptct.net" => "babbie50@ptct.net",
        "481level@ptct.net" => "f%wDUgchm~",
        "aquamarineharriott@ptct.net" => "=65HG%ui<\ ",
        "rubina24@ptct.net" => "4)arh'r8Sp",
        "1240competent@ptct.net" => 'IJS:^b"X:R',
        "4549jeana@ptct.net" => ")3vcqHkN'3",
        "8591pink@ptct.net" => "%PI+]7P#4;",
        "jonisbetter@ptct.net" => '$U*[t*fD8,',
        "2653fit@ptct.net" => "2653fit@ptct.net",
        "disgustedlorianne@ptct.net" => ":*J~ndFppu",
        "6devi@ptct.net" => "=CD=:3,.CH",
        "charmianelectrical@ptct.net" => "VhNaqG/q&f",
        "calypso840@ptct.net" => 'vPMA/cQ\("',
        "1745nice@ptct.net" => '\o];n$p%65',
        "448disturbing@ptct.net" => 'Y9\(=p{*SP',
        '9444easy@chefalicious.com' => 'QsFo4pdP{/'
    ];

    const API_URL = "https://api.mail.tm/";
    public function index()
    {
        $multiHandle = curl_multi_init();  // Khởi tạo handle multi
        if (isset($_GET['email'])) {
            if (isset(self::ACCOUNTS[$_GET['email']])) {
                $data = [
                    'address' => $_GET['email'],
                    'password' => trim(self::ACCOUNTS[$_GET['email']])
                ];
                $token = $this->getToken($data);
                $token = json_decode($token)->token ?? self::TOKEN;
            } else {
                $token = self::TOKEN;
            }
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
}
