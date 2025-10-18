<?php

class CategoryModel {

    private $category_id;
    private $tendanhmuc;

    public function __construct(
        $category_id = null,
        $tendanhmuc = null
    ) {
        $this->category_id = $category_id;
        $this->tendanhmuc = $tendanhmuc;
    }

    // GETTERS
    public function getCategoryId() {
        return $this->category_id;
    }

    public function getTenDanhMuc() {
        return $this->tendanhmuc;
    }

    // SETTERS
    public function setCategoryId(int $category_id) {
        $this->category_id = $category_id;
    }

    public function setTenDanhMuc(string $tendanhmuc) {
        $this->tendanhmuc = $tendanhmuc;
    }
}