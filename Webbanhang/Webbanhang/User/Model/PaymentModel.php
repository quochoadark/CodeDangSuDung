<?php

class PaymentModel {
    private $payment_id;
    private $order_id;
    private $phuongthuc;
    private $trangthai;
    private $ngaythanhtoan;

    public function __construct($payment_id, $order_id, $phuongthuc, $trangthai, $ngaythanhtoan) {
        $this->payment_id = $payment_id;
        $this->order_id = $order_id;
        $this->phuongthuc = $phuongthuc;
        $this->trangthai = $trangthai;
        $this->ngaythanhtoan = $ngaythanhtoan;
    }

    // Getters
    public function getPaymentId() { return $this->payment_id; }
    public function getOrderId() { return $this->order_id; }
    public function getPhuongThuc() { return $this->phuongthuc; }
    public function getTrangThai() { return $this->trangthai; }
    public function getNgayThanhToan() { return $this->ngaythanhtoan; }

    // Setters
    public function setPaymentId($payment_id) { $this->payment_id = $payment_id; }
    public function setOrderId($order_id) { $this->order_id = $order_id; }
    public function setPhuongThuc($phuongthuc) { $this->phuongthuc = $phuongthuc; }
    public function setTrangThai($trangthai) { $this->trangthai = $trangthai; }
    public function setNgayThanhToan($ngaythanhtoan) { $this->ngaythanhtoan = $ngaythanhtoan; }
}