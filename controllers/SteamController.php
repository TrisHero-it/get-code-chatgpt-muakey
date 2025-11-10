<?php
require_once "models/Steam.php";
require_once "models/Order.php";
class SteamController extends Steam
{
    public function getOrders()
    {
        header('Content-Type: application/json; charset=utf-8');
        $steam = new Steam();
        $order = new Order();
        $money = $order->getTotalMoney();
        $orders = $steam->getOrders();
        foreach ($orders as $key => $order) {
            $orders[$key]['code_payment'] = $money['code'];
            $orders[$key]['balance'] = $money['balance'];
            $orders[$key]['id_code_payment'] = $money['id'];
        }
        echo json_encode($orders);
    }
}
