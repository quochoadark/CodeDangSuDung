<?php

class OrderHistoryModel {
    private $history_id;
    private $order_id;
    private $ngaycapnhat;
    private $trangthai;

    public function __construct($history_id, $order_id, $ngaycapnhat, $trangthai) {
        $this->history_id = $history_id;
        $this->order_id = $order_id;
        $this->ngaycapnhat = $ngaycapnhat;
        $this->trangthai = $trangthai;
    }

    // Getters
    public function getHistoryId() { return $this->history_id; }
    public function getOrderId() { return $this->order_id; }
    public function getNgayCapNhat() { return $this->ngaycapnhat; }
    public function getTrangThai() { return $this->trangthai; }

    // Setters
    public function setHistoryId($history_id) { $this->history_id = $history_id; }
    public function setOrderId($order_id) { $this->order_id = $order_id; }
    public function setNgayCapNhat($ngaycapnhat) { $this->ngaycapnhat = $ngaycapnhat; }
    public function setTrangThai($trangthai) { $this->trangthai = $trangthai; }
}