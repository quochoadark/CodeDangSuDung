<?php

class TierModel
{
    // 1. Đối tượng (Properties)
    private int $tier_id;
    private string $tenhang;
    private float $giatien;

    // 2. Hàm khởi tạo (Constructor)
    public function __construct(int $tier_id, string $tenhang, float $giatien)
    {
        $this->tier_id = $tier_id;
        $this->tenhang = $tenhang;
        $this->giatien = $giatien;
    }

    // 3. Getters
    public function getTierId(): int
    {
        return $this->tier_id;
    }

    public function getTenHang(): string
    {
        return $this->tenhang;
    }

    public function getGiaTien(): float
    {
        return $this->giatien;
    }

    // 4. Setters
    public function setTenHang(string $tenhang): void
    {
        $this->tenhang = $tenhang;
    }

    public function setGiaTien(float $giatien): void
    {
        $this->giatien = $giatien;
    }
}