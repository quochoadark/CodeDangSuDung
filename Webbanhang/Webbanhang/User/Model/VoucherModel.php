<?php

class VoucherModel {

    private $voucher_id;
    private $makhuyenmai;
    private $giam;
    private $ngayhethan;
    private $soluong;
    private $luotsudung;

    public function __construct(
        $voucher_id = null,
        $makhuyenmai = null,
        $giam = null,
        $ngayhethan = null,
        $soluong = null,
        $luotsudung = null
    ) {
        $this->voucher_id = $voucher_id;
        $this->makhuyenmai = $makhuyenmai;
        $this->giam = $giam;
        $this->ngayhethan = $ngayhethan;
        $this->soluong = $soluong;
        $this->luotsudung = $luotsudung;
    }

    // GETTERS
    public function getVoucherId() {
        return $this->voucher_id;
    }

    public function getMaKhuyenMai() {
        return $this->makhuyenmai;
    }

    public function getGiam() {
        return $this->giam;
    }

    public function getNgayHetHan() {
        return $this->ngayhethan;
    }

    public function getSoLuong() {
        return $this->soluong;
    }

    public function getLuotSuDung() {
        return $this->luotsudung;
    }

    // SETTERS
    public function setVoucherId(int $voucher_id) {
        $this->voucher_id = $voucher_id;
    }

    public function setMaKhuyenMai(string $makhuyenmai) {
        $this->makhuyenmai = $makhuyenmai;
    }

    public function setGiam(float $giam) {
        $this->giam = $giam;
    }

    public function setNgayHetHan(?string $ngayhethan) {
        $this->ngayhethan = $ngayhethan;
    }

    public function setSoLuong(?int $soluong) {
        $this->soluong = $soluong;
    }

    public function setLuotSuDung(?int $luotsudung) {
        $this->luotsudung = $luotsudung;
    }
}