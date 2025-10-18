<?php

class SaleProductModel {

    private $promo_id;
    private $product_id;
    private $mota;
    private $giam;
    private $ngaybatdau;
    private $ngayketthuc;

    public function __construct(
        $promo_id = null,
        $product_id = null,
        $mota = null,
        $giam = null,
        $ngaybatdau = null,
        $ngayketthuc = null
    ) {
        $this->promo_id = $promo_id;
        $this->product_id = $product_id;
        $this->mota = $mota;
        $this->giam = $giam;
        $this->ngaybatdau = $ngaybatdau;
        $this->ngayketthuc = $ngayketthuc;
    }

    // GETTERS
    public function getPromoId() {
        return $this->promo_id;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getMoTa() {
        return $this->mota;
    }

    public function getGiam() {
        return $this->giam;
    }

    public function getNgayBatDau() {
        return $this->ngaybatdau;
    }

    public function getNgayKetThuc() {
        return $this->ngayketthuc;
    }

    // SETTERS
    public function setPromoId(int $promo_id) {
        $this->promo_id = $promo_id;
    }

    public function setProductId(int $product_id) {
        $this->product_id = $product_id;
    }

    public function setMoTa(string $mota) {
        $this->mota = $mota;
    }

    public function setGiam(float $giam) {
        $this->giam = $giam;
    }

    public function setNgayBatDau(string $ngaybatdau) {
        $this->ngaybatdau = $ngaybatdau;
    }

    public function setNgayKetThuc(string $ngayketthuc) {
        $this->ngayketthuc = $ngayketthuc;
    }
}