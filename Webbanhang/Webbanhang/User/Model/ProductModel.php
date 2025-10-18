<?php

class ProductModel {

    private $product_id;
    private $tensanpham;
    private $category_id;
    private $gia;
    private $tonkho;
    private $mota;
    private $ngaytao;
    private $img;

    public function __construct(
        $product_id = null,
        $tensanpham = null,
        $category_id = null,
        $gia = null,
        $tonkho = null,
        $mota = null,
        $ngaytao = null,
        $img = null
    ) {
        $this->product_id = $product_id;
        $this->tensanpham = $tensanpham;
        $this->category_id = $category_id;
        $this->gia = $gia;
        $this->tonkho = $tonkho;
        $this->mota = $mota;
        $this->ngaytao = $ngaytao;
        $this->img = $img;
    }

    // GETTERS
    public function getProductId() {
        return $this->product_id;
    }

    public function getTenSanPham() {
        return $this->tensanpham;
    }

    public function getCategoryId() {
        return $this->category_id;
    }

    public function getGia() {
        return $this->gia;
    }

    public function getTonKho() {
        return $this->tonkho;
    }

    public function getMoTa() {
        return $this->mota;
    }

    public function getNgayTao() {
        return $this->ngaytao;
    }

    public function getImg() {
        return $this->img;
    }

    // SETTERS
    public function setProductId(int $product_id) {
        $this->product_id = $product_id;
    }

    public function setTenSanPham(string $tensanpham) {
        $this->tensanpham = $tensanpham;
    }

    public function setCategoryId(int $category_id) {
        $this->category_id = $category_id;
    }

    public function setGia(float $gia) {
        $this->gia = $gia;
    }

    public function setTonKho(int $tonkho) {
        $this->tonkho = $tonkho;
    }

    public function setMoTa(string $mota) {
        $this->mota = $mota;
    }

    public function setNgayTao(string $ngaytao) {
        $this->ngaytao = $ngaytao;
    }

    public function setImg(string $img) {
        $this->img = $img;
    }
}