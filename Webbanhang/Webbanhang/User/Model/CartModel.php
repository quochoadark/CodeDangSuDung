<?php

class CartModel {
    private $cart_id;

    private $user_id;

    private $product_id;


    private $soluong;


    private $ngaythem;


    public function __construct(
        $cart_id = null,
        $user_id = null,
        $product_id = null,
        $soluong = null,
        $ngaythem = null
    ) {
        $this->cart_id = $cart_id;
        $this->user_id = $user_id;
        $this->product_id = $product_id;
        $this->soluong = $soluong;
        $this->ngaythem = $ngaythem;
    }


    public function getCartId() {
        return $this->cart_id;
    }


    public function getUserId() {
        return $this->user_id;
    }


    public function getProductId() {
        return $this->product_id;
    }


    public function getSoluong() {
        return $this->soluong;
    }


    public function getNgaythem() {
        return $this->ngaythem;
    }

    // --- Setters ---

    public function setCartId($cart_id) {
        $this->cart_id = $cart_id;
        return $this;
    }

    public function setUserId($user_id) {
        $this->user_id = $user_id;
        return $this;
    }


    public function setProductId($product_id) {
        $this->product_id = $product_id;
        return $this;
    }

    public function setSoluong($soluong) {
        $this->soluong = $soluong;
        return $this;
    }


    public function setNgaythem($ngaythem) {
        $this->ngaythem = $ngaythem;
        return $this;
    }
}