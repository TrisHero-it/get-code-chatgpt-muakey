<?php

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once "models/Account.php";

class AccountController extends Account
{
    public function index()
    {
        $account = new Account();
        $accounts = $account->index();
        require_once "views/accounts/index.php";
    }

    public function add()
    {
        require_once "views/accounts/add.php";
    }

    public function store()
    {
        $account = new Account();
        if ($_FILES['excel_file']['name'] != '') {
            $fileTmpPath = $_FILES['excel_file']['tmp_name'];

            $spreadsheet = IOFactory::load($fileTmpPath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            foreach ($data as $item) {
                if ($item[0] == "Email") {
                    continue;
                }
                if ($item[0] != "") {
                    $account->insert($item[0], $item[1], "Netflix");
                }
            }
        } else {
            $account->insert($_POST['email'], $_POST['password'], $_POST['type']);
        }
        header("Location: ?act=list");
    }

    public function delete($id)
    {
        $account = new Account();
        $account->delete($id);
        header("Location: ?act=list");
    }

    public function exportExcel()
    {
        // 1️⃣ Khởi tạo file Excel mới
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $fileName = "excel-code-muakey.xlsx";

        // 2️⃣ Ghi tiêu đề cột
        $sheet->setCellValue('A1', 'Email');
        $sheet->setCellValue('B1', 'Password');

        // 4️⃣ Tự động co giãn độ rộng cột
        foreach (range('A', 'B') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 5️⃣ Ghi ra file
        $writer = new Xlsx($spreadsheet);

        ob_end_clean(); // Xóa buffer để tránh ký tự thừa
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
