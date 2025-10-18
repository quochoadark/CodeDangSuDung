<?php

class ShippingModel {
    private $shipping_id;
    private $order_id;
    private $trangthai;
    private $ngaysua;
    private $receiver_name;
    private $receiver_phone;
    private $receiver_address;
    private $notes;
    private $phuongthuctt; // Phương thức thanh toán

    public function __construct($shipping_id, $order_id, $trangthai, $ngaysua, $receiver_name, $receiver_phone, $receiver_address, $notes, $phuongthuctt) {
        $this->shipping_id = $shipping_id;
        $this->order_id = $order_id;
        $this->trangthai = $trangthai;
        $this->ngaysua = $ngaysua;
        $this->receiver_name = $receiver_name;
        $this->receiver_phone = $receiver_phone;
        $this->receiver_address = $receiver_address;
        $this->notes = $notes;
        $this->phuongthuctt = $phuongthuctt;
    }

    // Getters
    public function getShippingId() { return $this->shipping_id; }
    public function getOrderId() { return $this->order_id; }
    public function getTrangThai() { return $this->trangthai; }
    public function getNgaySua() { return $this->ngaysua; }
    public function getReceiverName() { return $this->receiver_name; }
    public function getReceiverPhone() { return $this->receiver_phone; }
    public function getReceiverAddress() { return $this->receiver_address; }
    public function getNotes() { return $this->notes; }
    public function getPhuongThucTT() { return $this->phuongthuctt; }

    // Setters
    public function setShippingId($shipping_id) { $this->shipping_id = $shipping_id; }
    public function setOrderId($order_id) { $this->order_id = $order_id; }
    public function setTrangThai($trangthai) { $this->trangthai = $trangthai; }
    public function setNgaySua($ngaysua) { $this->ngaysua = $ngaysua; }
    public function setReceiverName($receiver_name) { $this->receiver_name = $receiver_name; }
    public function setReceiverPhone($receiver_phone) { $this->receiver_phone = $receiver_phone; }
    public function setReceiverAddress($receiver_address) { $this->receiver_address = $receiver_address; }
    public function setNotes($notes) { $this->notes = $notes; }
    public function setPhuongThucTT($phuongthuctt) { $this->phuongthuctt = $phuongthuctt; }
}