<?php

class OrderModel {
    private $order_id;
    private $user_id;
    private $tongtien;
    private $ngaytao;
    private $voucher_id;
    private $giam_gia;
    private $trangthai;

    public function __construct($order_id, $user_id, $tongtien, $ngaytao, $voucher_id, $giam_gia, $trangthai) {
        $this->order_id = $order_id;
        $this->user_id = $user_id;
        $this->tongtien = $tongtien;
        $this->ngaytao = $ngaytao;
        $this->voucher_id = $voucher_id;
        $this->giam_gia = $giam_gia;
        $this->trangthai = $trangthai;
    }

    // Getters
    public function getOrderId() { return $this->order_id; }
    public function getUserId() { return $this->user_id; }
    public function getTongTien() { return $this->tongtien; }
    public function getNgayTao() { return $this->ngaytao; }
    public function getVoucherId() { return $this->voucher_id; }
    public function getGiamGia() { return $this->giam_gia; }
    public function getTrangThai() { return $this->trangthai; }

    // Setters
    public function setOrderId($order_id) { $this->order_id = $order_id; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
    public function setTongTien($tongtien) { $this->tongtien = $tongtien; }
    public function setNgayTao($ngaytao) { $this->ngaytao = $ngaytao; }
    public function setVoucherId($voucher_id) { $this->voucher_id = $voucher_id; }
    public function setGiamGia($giam_gia) { $this->giam_gia = $giam_gia; }
    public function setTrangThai($trangthai) { $this->trangthai = $trangthai; }
}