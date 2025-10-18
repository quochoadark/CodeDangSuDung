<?php

class OrderStatusModel {

    private $trangthai_id;

    private $ten_trangthai;

    private $mo_ta;

    public function __construct($trangthai_id = null, $ten_trangthai = null, $mo_ta = null) {
        $this->trangthai_id = $trangthai_id;
        $this->ten_trangthai = $ten_trangthai;
        $this->mo_ta = $mo_ta;
    }


    public function getTrangthaiId() {
        return $this->trangthai_id;
    }

    public function getTenTrangthai() {
        return $this->ten_trangthai;
    }

    public function getMoTa() {
        return $this->mo_ta;
    }

    public function setTrangthaiId(int $trangthai_id) {
        $this->trangthai_id = $trangthai_id;
    }

    public function setTenTrangthai(string $ten_trangthai) {
        $this->ten_trangthai = $ten_trangthai;
    }

    public function setMoTa(string $mo_ta) {
        $this->mo_ta = $mo_ta;
    }
}