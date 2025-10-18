<?php

class OrderDetailModel {
    private $order_detail_id;
    private $order_id;
    private $product_id;
    private $soluong;
    private $gia;
    private $gia_goc;
    private $giam_gia_sp;

    public function __construct($order_detail_id, $order_id, $product_id, $soluong, $gia, $gia_goc, $giam_gia_sp) {
        $this->order_detail_id = $order_detail_id;
        $this->order_id = $order_id;
        $this->product_id = $product_id;
        $this->soluong = $soluong;
        $this->gia = $gia;
        $this->gia_goc = $gia_goc;
        $this->giam_gia_sp = $giam_gia_sp;
    }

    // Getters
    public function getOrderDetailId() { return $this->order_detail_id; }
    public function getOrderId() { return $this->order_id; }
    public function getProductId() { return $this->product_id; }
    public function getSoLuong() { return $this->soluong; }
    public function getGia() { return $this->gia; }
    public function getGiaGoc() { return $this->gia_goc; }
    public function getGiamGiaSp() { return $this->giam_gia_sp; }

    // Setters
    public function setOrderDetailId($order_detail_id) { $this->order_detail_id = $order_detail_id; }
    public function setOrderId($order_id) { $this->order_id = $order_id; }
    public function setProductId($product_id) { $this->product_id = $product_id; }
    public function setSoLuong($soluong) { $this->soluong = $soluong; }
    public function setGia($gia) { $this->gia = $gia; }
    public function setGiaGoc($gia_goc) { $this->gia_goc = $gia_goc; }
    public function setGiamGiaSp($giam_gia_sp) { $this->giam_gia_sp = $giam_gia_sp; }
}