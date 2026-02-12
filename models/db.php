<?php
require_once "env.php";
// tạo kết nối từ project php sang mysql
class db
{
    function getConnect()
    {
        $connect = new PDO(
            "mysql:host=" . DBHOST
                . ";dbname=" . DBNAME
                . ";charset=" . DBCHARSET,
            DBUSER,
            DBPASS
        );
        return $connect;
    }



    // nếu như dùng để lấy danh sách thì sẽ truyền true còn truyền false thì
    //sẽ chạy được các câi truy vấn như thêm sửa xóa
    function getData($query, $getAll = true)
    {
        $conn = $this->getConnect();
        $stmt = $conn->prepare($query);
        $stmt->execute();
        if ($getAll) {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function getData2($query, $getAll = true)
    {
        $conn = $this->getConnect2();
        $stmt = $conn->prepare($query);
        $stmt->execute();
        if ($getAll) {
            return $stmt->fetchAll();
        }
        return $stmt->fetch();
    }

    function getConnect2()
    {
        $connect = new PDO(
            "mysql:host=" . DBHOST2
                . ";dbname=" . DBNAME2
                . ";charset=" . DBCHARSET2,
            DBUSER2,
            DBPASS2
        );
        return $connect;
    }

    function getData3($query, $getAll = true)
    {
        $conn = $this->getConnect3();
        $stmt = $conn->prepare($query);
        $stmt->execute();
        if ($getAll) {
            return $stmt->fetchAll();
        }
        return $stmt->fetch();
    }

    function getConnect3()
    {
        $connect = new PDO(
            "mysql:host=" . DBHOST3
                . ";dbname=" . DBNAME3
                . ";charset=" . DBCHARSET3,
            DBUSER3,
            DBPASS3
        );
        return $connect;
    }

    function getData4($query, $getAll = true)
    {
        $conn = $this->getConnect4();
        $stmt = $conn->prepare($query);
        $stmt->execute();
        if ($getAll) {
            return $stmt->fetchAll();
        }
        return $stmt->fetch();
    }

    function getConnect4()
    {
        $connect = new PDO(
            "mysql:host=" . DBHOST4
                . ";dbname=" . DBNAME4
                . ";charset=" . DBCHARSET4,
            DBUSER4,
            DBPASS4
        );
        return $connect;
    }
}
