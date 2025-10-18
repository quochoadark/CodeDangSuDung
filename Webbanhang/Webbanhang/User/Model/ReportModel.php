<?php

class ReportModel {
    // Thuộc tính (Properties)
    private $report_id;
    private $thoigian;
    private $tongdoanhthu;
    private $report_type;
    private $ngaytao;


    public function __construct(
        ?int $report_id,
        string $thoigian,
        float $tongdoanhthu,
        string $report_type,
        string $ngaytao
    ) {
        $this->report_id = $report_id;
        $this->thoigian = $thoigian;
        $this->tongdoanhthu = $tongdoanhthu;
        $this->report_type = $report_type;
        $this->ngaytao = $ngaytao;
    }

    public function getReportId(): ?int {
        return $this->report_id;
    }

    public function getThoiGian(): string {
        return $this->thoigian;
    }

    public function getTongDoanhThu(): float {
        return $this->tongdoanhthu;
    }

    public function getReportType(): string {
        return $this->report_type;
    }

    public function getNgayTao(): string {
        return $this->ngaytao;
    }
    public function setReportId(int $report_id): void {
        $this->report_id = $report_id;
    }

    public function setThoiGian(string $thoigian): void {
        $this->thoigian = $thoigian;
    }

    public function setTongDoanhThu(float $tongdoanhthu): void {
        $this->tongdoanhthu = $tongdoanhthu;
    }

    public function setReportType(string $report_type): void {
        $this->report_type = $report_type;
    }

    // Thường ngày tạo (ngaytao) không được set lại sau khi tạo,
    // nhưng vẫn tạo setter nếu cần thiết cho một số logic nghiệp vụ.
    public function setNgayTao(string $ngaytao): void {
        $this->ngaytao = $ngaytao;
    }
}

?>