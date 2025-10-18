<?php

class ReviewProductModel {

    private $review_id;
    private $user_id;
    private $product_id;
    private $danhgia;
    private $binhluan;
    private $ngaytao;

    public function __construct(
        $review_id = null,
        $user_id = null,
        $product_id = null,
        $danhgia = null,
        $binhluan = null,
        $ngaytao = null
    ) {
        $this->review_id = $review_id;
        $this->user_id = $user_id;
        $this->product_id = $product_id;
        $this->danhgia = $danhgia;
        $this->binhluan = $binhluan;
        $this->ngaytao = $ngaytao;
    }

    // GETTERS
    public function getReviewId() {
        return $this->review_id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getProductId() {
        return $this->product_id;
    }

    public function getDanhGia() {
        return $this->danhgia;
    }

    public function getBinhLuan() {
        return $this->binhluan;
    }

    public function getNgayTao() {
        return $this->ngaytao;
    }

    // SETTERS
    public function setReviewId(int $review_id) {
        $this->review_id = $review_id;
    }

    public function setUserId(int $user_id) {
        $this->user_id = $user_id;
    }

    public function setProductId(int $product_id) {
        $this->product_id = $product_id;
    }

    public function setDanhGia(int $danhgia) {
        $this->danhgia = $danhgia;
    }

    public function setBinhLuan(string $binhluan) {
        $this->binhluan = $binhluan;
    }

    public function setNgayTao(string $ngaytao) {
        $this->ngaytao = $ngaytao;
    }
}