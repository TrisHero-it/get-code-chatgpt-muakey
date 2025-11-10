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
            $orders[$key]['code_payment'] = $money['code'] ?? null;
            $orders[$key]['balance'] = $money['balance'] ?? 0;
            $orders[$key]['id_code_payment'] = $money['id'] ?? null;
        }
        echo json_encode($orders);
    }

    public function updateOrder($id, $status)
    {
        $steam = new Steam();
        $steam->updateOrder($id, $status);
    }
}
