-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: webnangcao
-- ------------------------------------------------------
-- Server version	8.0.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `baocaodoanhthu`
--

DROP TABLE IF EXISTS `baocaodoanhthu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `baocaodoanhthu` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `thoigian` varchar(50) DEFAULT NULL,
  `tongdoanhthu` decimal(12,2) DEFAULT NULL,
  `report_type` varchar(10) NOT NULL COMMENT 'Loại báo cáo: month (tháng) hoặc year (năm)',
  `ngaytao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `baocaodoanhthu`
--

LOCK TABLES `baocaodoanhthu` WRITE;
/*!40000 ALTER TABLE `baocaodoanhthu` DISABLE KEYS */;
INSERT INTO `baocaodoanhthu` VALUES (1,'2025-10',410300000.00,'month','2025-10-12 09:47:30'),(2,'2025-08',0.00,'month','2025-10-10 17:02:26'),(3,'2025',320200000.00,'year','2025-10-11 20:39:57');
/*!40000 ALTER TABLE `baocaodoanhthu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chitietdonhang`
--

DROP TABLE IF EXISTS `chitietdonhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chitietdonhang` (
  `order_detail_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `soluong` int DEFAULT NULL,
  `gia` decimal(10,2) DEFAULT NULL,
  `gia_goc` decimal(18,2) DEFAULT NULL,
  `giam_gia_sp` decimal(18,2) DEFAULT NULL,
  PRIMARY KEY (`order_detail_id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `chitietdonhang_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `donhang` (`order_id`),
  CONSTRAINT `chitietdonhang_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chitietdonhang`
--

LOCK TABLES `chitietdonhang` WRITE;
/*!40000 ALTER TABLE `chitietdonhang` DISABLE KEYS */;
INSERT INTO `chitietdonhang` VALUES (62,68,44,1,85000000.00,85000000.00,0.00),(63,69,44,1,85000000.00,85000000.00,0.00),(64,70,44,1,85000000.00,85000000.00,0.00),(65,71,42,1,65000000.00,65000000.00,0.00),(66,72,31,1,20000000.00,20000000.00,0.00),(67,73,43,1,70000000.00,70000000.00,0.00),(68,74,44,1,85000000.00,85000000.00,0.00);
/*!40000 ALTER TABLE `chitietdonhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chucvu`
--

DROP TABLE IF EXISTS `chucvu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `chucvu` (
  `id_chucvu` int NOT NULL AUTO_INCREMENT,
  `ten_chucvu` varchar(100) NOT NULL,
  PRIMARY KEY (`id_chucvu`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chucvu`
--

LOCK TABLES `chucvu` WRITE;
/*!40000 ALTER TABLE `chucvu` DISABLE KEYS */;
INSERT INTO `chucvu` VALUES (1,'Nhân viên bán hàng'),(2,'Admin');
/*!40000 ALTER TABLE `chucvu` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `danhgiasanpham`
--

DROP TABLE IF EXISTS `danhgiasanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `danhgiasanpham` (
  `review_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `danhgia` int DEFAULT NULL,
  `binhluan` text,
  `ngaytao` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `danhgiasanpham_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoidung` (`user_id`),
  CONSTRAINT `danhgiasanpham_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `danhgiasanpham`
--

LOCK TABLES `danhgiasanpham` WRITE;
/*!40000 ALTER TABLE `danhgiasanpham` DISABLE KEYS */;
INSERT INTO `danhgiasanpham` VALUES (8,6,44,3,'áddas','2025-10-11 06:39:31'),(9,6,44,4,'đâs','2025-10-11 06:43:06'),(10,6,44,4,'đá','2025-10-11 07:12:59'),(11,7,43,4,'đá','2025-10-11 08:52:28');
/*!40000 ALTER TABLE `danhgiasanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `danhmucsanpham`
--

DROP TABLE IF EXISTS `danhmucsanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `danhmucsanpham` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `tendanhmuc` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `danhmucsanpham`
--

LOCK TABLES `danhmucsanpham` WRITE;
/*!40000 ALTER TABLE `danhmucsanpham` DISABLE KEYS */;
INSERT INTO `danhmucsanpham` VALUES (1,'Văn phòng'),(2,'Gaming'),(3,'Học tập'),(4,'Cao cấp');
/*!40000 ALTER TABLE `danhmucsanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donhang`
--

DROP TABLE IF EXISTS `donhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donhang` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `tongtien` decimal(15,2) DEFAULT NULL,
  `ngaytao` datetime DEFAULT CURRENT_TIMESTAMP,
  `voucher_id` int DEFAULT NULL,
  `giam_gia` decimal(18,2) DEFAULT '0.00',
  `trangthai` int DEFAULT NULL COMMENT 'Khóa ngoại liên kết tới trangthaidonhang.trangthai_id',
  PRIMARY KEY (`order_id`),
  KEY `user_id` (`user_id`),
  KEY `fk_donhang_trangthai` (`trangthai`),
  CONSTRAINT `donhang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoidung` (`user_id`),
  CONSTRAINT `fk_donhang_trangthai` FOREIGN KEY (`trangthai`) REFERENCES `trangthaidonhang` (`trangthai_id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donhang`
--

LOCK TABLES `donhang` WRITE;
/*!40000 ALTER TABLE `donhang` DISABLE KEYS */;
INSERT INTO `donhang` VALUES (68,6,85050000.00,'2025-10-10 16:54:04',NULL,0.00,1),(69,7,85050000.00,'2025-10-10 18:41:48',NULL,0.00,1),(70,6,85050000.00,'2025-10-10 20:13:16',NULL,0.00,5),(71,6,65050000.00,'2025-10-10 20:48:58',NULL,0.00,1),(72,6,20050000.00,'2025-10-12 08:15:11',NULL,0.00,1),(73,6,70050000.00,'2025-10-12 08:15:20',NULL,0.00,1),(74,6,85050000.00,'2025-10-12 09:46:01',NULL,0.00,1);
/*!40000 ALTER TABLE `donhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `giohang`
--

DROP TABLE IF EXISTS `giohang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `giohang` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `soluong` int DEFAULT NULL,
  `ngaythem` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `giohang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoidung` (`user_id`),
  CONSTRAINT `giohang_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `giohang`
--

LOCK TABLES `giohang` WRITE;
/*!40000 ALTER TABLE `giohang` DISABLE KEYS */;
/*!40000 ALTER TABLE `giohang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `hangkhachhang`
--

DROP TABLE IF EXISTS `hangkhachhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hangkhachhang` (
  `tier_id` int NOT NULL AUTO_INCREMENT,
  `tenhang` varchar(50) DEFAULT NULL,
  `giatien` decimal(15,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`tier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `hangkhachhang`
--

LOCK TABLES `hangkhachhang` WRITE;
/*!40000 ALTER TABLE `hangkhachhang` DISABLE KEYS */;
INSERT INTO `hangkhachhang` VALUES (1,'Diamond',500000000.00),(2,'Gold',300000000.00),(3,'Silver',100000000.00),(4,'Bronze',0.00);
/*!40000 ALTER TABLE `hangkhachhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `khuyenmai_sanpham`
--

DROP TABLE IF EXISTS `khuyenmai_sanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `khuyenmai_sanpham` (
  `promo_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int DEFAULT NULL COMMENT 'NULL nếu áp dụng cho toàn bộ sản phẩm',
  `mota` varchar(255) NOT NULL,
  `giam` decimal(10,2) NOT NULL COMMENT 'Giá trị giảm (tiền mặt) hoặc tỷ lệ (0.XX)',
  `ngaybatdau` date NOT NULL,
  `ngayketthuc` date NOT NULL,
  PRIMARY KEY (`promo_id`),
  KEY `fk_product_promo` (`product_id`),
  CONSTRAINT `fk_product_promo` FOREIGN KEY (`product_id`) REFERENCES `sanpham` (`product_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `khuyenmai_sanpham`
--

LOCK TABLES `khuyenmai_sanpham` WRITE;
/*!40000 ALTER TABLE `khuyenmai_sanpham` DISABLE KEYS */;
INSERT INTO `khuyenmai_sanpham` VALUES (1,15,'Giảm giá ra mắt sản phẩm mới',0.15,'2025-10-01','2025-10-31'),(2,16,'Giảm sâu cuối mùa',50000.00,'2025-09-15','2025-09-22'),(3,17,'Khuyến mãi toàn cửa hàng',0.10,'2025-10-10','2025-10-12'),(4,33,'test',2000000.00,'2025-10-07','2025-10-31');
/*!40000 ALTER TABLE `khuyenmai_sanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lichsudonhang`
--

DROP TABLE IF EXISTS `lichsudonhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `lichsudonhang` (
  `history_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `ngaycapnhat` datetime DEFAULT CURRENT_TIMESTAMP,
  `trangthai` int NOT NULL,
  PRIMARY KEY (`history_id`),
  KEY `fk_lsdh_trangthai` (`trangthai`),
  KEY `fk_lsdh_order` (`order_id`),
  CONSTRAINT `fk_lsdh_order` FOREIGN KEY (`order_id`) REFERENCES `donhang` (`order_id`),
  CONSTRAINT `fk_lsdh_trangthai` FOREIGN KEY (`trangthai`) REFERENCES `trangthaidonhang` (`trangthai_id`),
  CONSTRAINT `lichsudonhang_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `donhang` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lichsudonhang`
--

LOCK TABLES `lichsudonhang` WRITE;
/*!40000 ALTER TABLE `lichsudonhang` DISABLE KEYS */;
INSERT INTO `lichsudonhang` VALUES (17,68,'2025-10-10 16:54:04',1),(18,69,'2025-10-10 18:41:48',1),(19,70,'2025-10-10 20:13:16',1),(20,71,'2025-10-10 20:48:58',1),(21,72,'2025-10-12 08:15:11',1),(22,73,'2025-10-12 08:15:20',1),(23,74,'2025-10-12 09:46:01',1),(24,70,'2025-10-12 09:46:11',5);
/*!40000 ALTER TABLE `lichsudonhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `makhuyenmai`
--

DROP TABLE IF EXISTS `makhuyenmai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `makhuyenmai` (
  `voucher_id` int NOT NULL AUTO_INCREMENT,
  `makhuyenmai` varchar(50) DEFAULT NULL,
  `giam` decimal(10,2) DEFAULT NULL,
  `ngayhethan` date DEFAULT NULL,
  `soluong` int DEFAULT '0',
  `luotsudung` int DEFAULT '0',
  PRIMARY KEY (`voucher_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `makhuyenmai`
--

LOCK TABLES `makhuyenmai` WRITE;
/*!40000 ALTER TABLE `makhuyenmai` DISABLE KEYS */;
INSERT INTO `makhuyenmai` VALUES (4,'KHUYENMAI10',100000.00,'2025-10-31',50,0),(5,'GIAM5PHANTRAM',0.05,'2025-12-31',NULL,0),(6,'FREESHIP50K',50000.00,'2025-09-30',100,0),(7,'GIAM20K',20000.00,'2025-10-07',19,1);
/*!40000 ALTER TABLE `makhuyenmai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nguoidung`
--

DROP TABLE IF EXISTS `nguoidung`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nguoidung` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `hoten` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `matkhau` varchar(255) DEFAULT NULL,
  `reset_token_hash` varchar(64) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `dienthoai` varchar(20) DEFAULT NULL,
  `diachi` varchar(255) DEFAULT NULL,
  `tier_id` int DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT 'hoatdong',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `tier_id` (`tier_id`),
  CONSTRAINT `nguoidung_ibfk_1` FOREIGN KEY (`tier_id`) REFERENCES `hangkhachhang` (`tier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nguoidung`
--

LOCK TABLES `nguoidung` WRITE;
/*!40000 ALTER TABLE `nguoidung` DISABLE KEYS */;
INSERT INTO `nguoidung` VALUES (6,'nguyena','nguyenvana@gmail.com','$2y$10$xxydgUvm9GZ0fRxTJLzBMu3eyeY4AXm6a9IBbQmTyZJKzfafgTVWy',NULL,NULL,'123','111',1,'1'),(7,'nguyenvanb','nguyenvanb@gmail.com','$2y$10$x5l.rG4hGknXJuAni21KOusxRbYpq.6zYPSg3UIwz3HGDaNRJ1VQi',NULL,NULL,'123','123',1,'1'),(27,'nguyenvanc','nguyenvanc@gmail.com','$2y$10$1hizJn3Kv5Jv6x7xz9OQA.9zz8/MNtKqHYLlu2yFHuVzUGVd.Lbv.',NULL,NULL,'123','123',4,'1'),(42,'test','tranvanhung07096@gmail.com','$2y$10$5riz95wrEW4XlMXl4jUBwe2fLAjV5GOSB7BnxgFmexjstIP0WwC6O',NULL,NULL,'0866405282','123',4,'1'),(43,'Nguyen Bang','luoncaobang39@gmail.com','$2y$10$oukgz8eK4snhXwfyqTge3u5XfkxSyDCPXVa.a7Pk/16jvpWv1v43W',NULL,NULL,'123456789','123123',2,'1'),(44,'Trần Quốc Hòa','tranquochoan349@gmail.com','$2y$10$DYKaIKhNZHa6/cbu5dC8beNjdNSws6wQ.KP8vyiBUWH7mkFuMKBZu',NULL,NULL,'0866405282','118/1 lầu 2',4,'1');
/*!40000 ALTER TABLE `nguoidung` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nhanvien`
--

DROP TABLE IF EXISTS `nhanvien`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nhanvien` (
  `staff_id` int NOT NULL AUTO_INCREMENT,
  `hoten` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `matkhau` varchar(255) DEFAULT NULL,
  `remember_token_hash` varchar(64) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `dienthoai` varchar(20) DEFAULT NULL,
  `trangthai` varchar(20) DEFAULT 'hoatdong',
  `id_chucvu` int DEFAULT NULL,
  PRIMARY KEY (`staff_id`),
  KEY `fk_nhanvien_chucvu` (`id_chucvu`),
  CONSTRAINT `fk_nhanvien_chucvu` FOREIGN KEY (`id_chucvu`) REFERENCES `chucvu` (`id_chucvu`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nhanvien`
--

LOCK TABLES `nhanvien` WRITE;
/*!40000 ALTER TABLE `nhanvien` DISABLE KEYS */;
INSERT INTO `nhanvien` VALUES (14,'Admin','tranquochoan349@gmail.com','$2y$10$.HBmR8BiTwv.7XmFPWTrkeR12VGbVOdi1XrwR6WZTZgAURnH.jlJG','82e3a0eb01d8bedb440e94fb0abc6dfd17ea01d65aaf5cc0635c7ec8fed75c3a','2025-11-11 03:52:03','123','1',2),(19,'test','tranvanhung07096@gmail.com','$2y$10$4.7ahmqW6hXh/4e6YKHc9.zdAYye8UZCJmEvUpJ9rVv2vKocgrDae','99876be30515e50ed83e07d81e3542f8ce56d55d163e612ba483e4bd4849fb36','2025-11-10 07:26:27','123123','1',1),(20,'abc','abc@gmail.com','$2y$10$H2UJ9NOGZLzPZfh8j5vc4.8kJ0o3SvvyoiAsyG5KCrCFYT1ib8urS',NULL,NULL,'123123','1',1);
/*!40000 ALTER TABLE `nhanvien` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sanpham`
--

DROP TABLE IF EXISTS `sanpham`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sanpham` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `tensanpham` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `gia` decimal(10,2) DEFAULT NULL,
  `tonkho` int DEFAULT NULL,
  `mota` text,
  `ngaytao` datetime DEFAULT CURRENT_TIMESTAMP,
  `img` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `sanpham_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `danhmucsanpham` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sanpham`
--

LOCK TABLES `sanpham` WRITE;
/*!40000 ALTER TABLE `sanpham` DISABLE KEYS */;
INSERT INTO `sanpham` VALUES (13,'HP 15s',3,8500000.00,10,'CPU: Intel Core i5-1135G7 (thế hệ 11)\r\nRAM: 8GB DDR4\r\nỔ cứng: 256GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics\r\nMàn hình: 15.6 inch Full HD IPS\r\nM.Hình: 16\" Full HD 165Hz','2025-09-17 00:00:00','HP15s.png'),(15,'Acer Aspire 3',3,9000000.00,10,'CPU: Intel Core i5-1235U (thế hệ 12)\r\nRAM: 8GB DDR4\r\nỔ cứng: 256GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics\r\nMàn hình: 15.6 inch Full HD\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','Acer_Aspire_3.png'),(16,'Asus Vivobook Go 15',3,10000000.00,10,'CPU: Intel Core i3-N305\r\nRAM: 8GB DDR4\r\nỔ cứng: 512GB SSD PCIe 3.0\r\nCard đồ họa: Intel UHD Graphics\r\nMàn hình: 15.6 inch Full HD\r\nHệ điều hành: Windows 11 Pro','2025-09-17 00:00:00','Asus_Vivobook_Go_15.png'),(17,'Dell Inspiron 15 3000 series',3,11000000.00,10,'CPU: Intel Core i5-1135G7 (thế hệ 11)\r\nRAM: 16GB DDR4\r\nỔ cứng: 512GB SSD\r\nCard đồ họa: Intel Iris Xe Graphics\r\nMàn hình: 15.6 inch Full HD\r\nHệ điều hành: Windows 10','2025-09-17 00:00:00','Dell_Inspiron_15_3000_series.png'),(18,'Lenovo IdeaPad Slim 3',3,12000000.00,10,'PU: Intel Core i5-1235U (thế hệ 12)\r\nRAM: 8GB DDR4\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics\r\nMàn hình: 15.6 inch Full HD\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','Lenovo_IdeaPad_Slim_3.png'),(19,'Acer Swift Go 14',3,20000000.00,10,'CPU: Intel Core Ultra 5 125H\r\nRAM: 8GB LPDDR5\r\nỔ cứng: 512GB SSD\r\nCard đồ họa: Intel Arc Graphics\r\nMàn hình: 14 inch OLED 2880 x 1800\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','Acer_Swift_Go_14.png'),(20,'Lenovo Yoga Slim 7',3,25000000.00,10,'CPU: Intel Core i5-1135G7 (thế hệ 11)\r\nRAM: 8GB DDR4\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics\r\nMàn hình: 14 inch Full HD cảm ứng\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','Lenovo_Yoga_Slim_7.png'),(21,'Apple MacBook Air M2',3,29000000.00,10,'CPU: Apple M2 (8-core CPU, 10-core GPU)\r\nRAM: 8GB Unified Memory\r\nỔ cứng: 512GB SSD\r\nCard đồ họa: 10-core GPU tích hợp\r\nMàn hình: 13.6 inch Liquid Retina\r\nHệ điều hành: macOS','2025-09-17 00:00:00','Apple_MacBook_Air_M2.png'),(22,'Dell Vostro 3000 series',1,12000000.00,10,'CPU: Intel Core i5-1235U (10 nhân, thế hệ 12)\r\nRAM: 8GB DDR4\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics tích hợp\r\nMàn hình: 15.6 inch Full HD (1920×1080)\r\nHệ điều hành: Windows 11 Pro','2025-09-17 00:00:00','Dell_Vostro_3000_series.png'),(23,'Lenovo ThinkBook 14',1,14000000.00,10,'CPU: Intel Core i5-1335U (thế hệ 13)\r\nRAM: 16GB DDR4\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics tích hợp\r\nMàn hình: 14 inch Full HD+ (1920×1200)\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','Lenovo_ThinkBook_14.png'),(24,'HP Pavilion 14',1,16000000.00,10,'CPU: Intel Core i5-1235U (thế hệ 12)\r\nRAM: 8GB DDR4\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics tích hợp\r\nMàn hình: 14 inch Full HD+ (1920×1200)\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','HP_Pavilion_14.png'),(25,'Dell Latitude 3000 series',1,20000000.00,10,'CPU: Intel Core i5-1235U (thế hệ 12)\r\nRAM: 8GB DDR4\r\nỔ cứng: 256GB SSD NVMe\r\nCard đồ họa: Intel UHD/Iris Xe Graphics tích hợp\r\nMàn hình: 15.6 inch Full HD (1920×1080)\r\nHệ điều hành: Windows 11 Pro','2025-09-17 00:00:00','Dell_Latitude_3000_series.png'),(26,'Asus Zenbook 14 OLED',1,22000000.00,10,'CPU: Intel Core i5-1340P (thế hệ 13)\r\nRAM: 16GB LPDDR5\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics tích hợp\r\nMàn hình: 14 inch OLED 2.8K (2880×1800)\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','Asus_Zenbook_14_OLED.png'),(27,'Microsoft Surface Laptop Go 3',1,25000000.00,10,'CPU: Intel Core i5-1235U (thế hệ 12)\r\nRAM: 8GB LPDDR5\r\nỔ cứng: 256GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics tích hợp\r\nMàn hình: 12.4 inch PixelSense (1536×1024) cảm ứng\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','Microsoft_Surface_Laptop_Go_3.png'),(28,'LG Gram 14',1,35000000.00,10,'CPU: Intel Core i5-1340P (thế hệ 13)\r\nRAM: 16GB LPDDR5\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics tích hợp\r\nMàn hình: 14 inch WUXGA (1920×1200)\r\nHệ điều hành: Windows 11 Home','2025-09-17 00:00:00','LG_Gram_14.png'),(29,'Lenovo ThinkPad X1 Carbon Gen 10',1,45000000.00,10,'CPU: Intel Core i7-1260P (thế hệ 12)\r\nRAM: 16GB LPDDR5\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics tích hợp\r\nMàn hình: 14 inch 2.2K (2240×1400) IPS\r\nHệ điều hành: Windows 11 Pro','2025-09-17 00:00:00','Lenovo_ThinkPad_X1_Carbon_Gen_10.png'),(30,'MSI Thin GF63',2,17000000.00,10,'CPU: Intel Core i5-12450H (thế hệ 12)\r\nRAM: 8GB DDR4\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 4050 6GB rời\r\nMàn hình: 15.6 inch FHD (1920×1080) 144Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','MSI_Thin_GF63.png'),(31,'Asus TUF Gaming F15',2,20000000.00,9,'CPU: Intel Core i7-12700H (thế hệ 12)\r\nRAM: 16GB DDR4\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 3060 6GB rời\r\nMàn hình: 15.6 inch FHD (1920×1080) 144Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Asus_TUF_Gaming_F15.png'),(33,'Acer Nitro 5',2,22000000.00,9,'CPU: Intel Core i7-12700H (thế hệ 12)\r\nRAM: 16GB DDR4\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 3050Ti 4GB rời\r\nMàn hình: 15.6 inch FHD (1920×1080) 144Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Acer_Nitro_5.png'),(34,'Dell G15',2,25000000.00,10,'CPU: Intel Core i7-12700H (thế hệ 12)\r\nRAM: 16GB DDR5\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 3060 6GB rời\r\nMàn hình: 15.6 inch FHD (1920×1080) 165Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Dell_G15.png'),(35,'Lenovo LOQ 15',2,26000000.00,10,'CPU: Intel Core i7-13620H (thế hệ 13)\r\nRAM: 16GB DDR5\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 4050 6GB rời\r\nMàn hình: 15.6 inch FHD (1920×1080) 144Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Lenovo_LOQ_15.png'),(36,'Lenovo Legion Slim 5',2,30000000.00,10,'CPU: AMD Ryzen 7 7840HS\r\nRAM: 16GB DDR5\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 4060 8GB rời\r\nMàn hình: 15.6 inch WQHD+ (2560×1600) 165Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Lenovo_Legion_Slim_5.png'),(37,'Asus ROG Zephyrus G14',2,40000000.00,10,'CPU: AMD Ryzen 9 7940HS\r\nRAM: 16GB DDR5\r\nỔ cứng: 1TB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 4060 8GB rời\r\nMàn hình: 14 inch QHD+ (2560×1600) 165Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Asus_ROG_Zephyrus_G14.png'),(38,'MSI Stealth 16',2,45000000.00,10,'CPU: Intel Core i7-13700H (thế hệ 13)\r\nRAM: 16GB DDR5\r\nỔ cứng: 1TB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 4060 8GB rời\r\nMàn hình: 16 inch QHD+ (2560×1600) 240Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','MSI_Stealth_16.png'),(39,'Lenovo Yoga Slim 9i',4,50000000.00,10,'CPU: Intel Core i7-1280P (thế hệ 12)\r\nRAM: 16GB LPDDR5\r\nỔ cứng: 1TB SSD NVMe\r\nCard đồ họa: Intel Iris Xe Graphics tích hợp\r\nMàn hình: 14 inch 4K OLED (3840×2400) cảm ứng\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Lenovo_Yoga_Slim_9i.png'),(40,'Razer Blade 14',4,55000000.00,10,'CPU: AMD Ryzen 9 7940HS\r\nRAM: 16GB DDR5\r\nỔ cứng: 1TB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 4070 8GB rời\r\nMàn hình: 14 inch QHD+ (2560×1600) 240Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Razer_Blade_14.png'),(41,'Microsoft Surface Laptop Studio',4,60000000.00,10,'CPU: Intel Core i7-11370H (thế hệ 11)\r\nRAM: 16GB LPDDR4x\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 3050 Ti rời\r\nMàn hình: 14.4 inch PixelSense Flow 2400×1600 120Hz cảm ứng\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Microsoft_Surface_Laptop_Studio.png'),(42,'Dell XPS 17',4,65000000.00,8,'CPU: Intel Core i7-12700H (thế hệ 12)\r\nRAM: 16GB DDR5\r\nỔ cứng: 512GB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 3050 4GB rời\r\nMàn hình: 17 inch UHD+ (3840×2400) cảm ứng\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Dell_XPS_17.png'),(43,'Apple MacBook Pro 16 M2 Pro',4,70000000.00,9,'CPU: Apple M2 Pro (12-core CPU, 19-core GPU)\r\nRAM: 16GB Unified Memory\r\nỔ cứng: 512GB SSD\r\nCard đồ họa: 19-core GPU tích hợp\r\nMàn hình: 16.2 inch Liquid Retina XDR (3456×2234)\r\nHệ điều hành: macOS Ventura','2025-09-18 00:00:00','Apple_MacBook_Pro_16_M2_Pro.png'),(44,'Asus ROG Zephyrus Duo 16s',4,85000000.00,6,'CPU: AMD Ryzen 9 7945HX\r\nRAM: 32GB DDR5\r\nỔ cứng: 1TB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 4090 16GB rời\r\nMàn hình: 16 inch Mini-LED QHD+ (2560×1600) 240Hz + màn hình phụ ScreenPad Plus 14″ 4K\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','Asus_ROG_Zephyrus_Duo_16.png'),(46,'Dell Alienware m18',4,90000000.00,8,'CPU: Intel Core i9-13980HX (thế hệ 13)\r\nRAM: 32GB DDR5\r\nỔ cứng: 1TB SSD NVMe\r\nCard đồ họa: NVIDIA GeForce RTX 4090 16GB rời\r\nMàn hình: 18 inch QHD+ (2560×1600) 165Hz IPS\r\nHệ điều hành: Windows 11 Home','2025-09-18 00:00:00','1Dell_Alienware_m18.png'),(47,'Asus ROG Mothership GZ700',4,99000000.00,10,'CPU: Intel Core i9-9980HK (thế hệ 9)\r\nRAM: 64GB DDR4\r\nỔ cứng: 2×512GB SSD NVMe RAID0\r\nCard đồ họa: NVIDIA GeForce RTX 2080 8GB rời\r\nMàn hình: 17.3 inch FHD (1920×1080) 144Hz IPS\r\nHệ điều hành: Windows 10 Pro','2025-09-23 00:00:00','180_trieu.png');
/*!40000 ALTER TABLE `sanpham` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thanhtoan`
--

DROP TABLE IF EXISTS `thanhtoan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `thanhtoan` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `phuongthuc` varchar(50) DEFAULT NULL,
  `trangthai` varchar(50) DEFAULT NULL,
  `ngaythanhtoan` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`payment_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `thanhtoan_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `donhang` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thanhtoan`
--

LOCK TABLES `thanhtoan` WRITE;
/*!40000 ALTER TABLE `thanhtoan` DISABLE KEYS */;
INSERT INTO `thanhtoan` VALUES (47,68,'COD','Pending','2025-10-10 16:54:04'),(48,69,'COD','Pending','2025-10-10 18:41:48'),(49,70,'COD','Pending','2025-10-10 20:13:16'),(50,71,'COD','Pending','2025-10-10 20:48:58'),(51,72,'COD','Pending','2025-10-12 08:15:11'),(52,73,'COD','Pending','2025-10-12 08:15:20'),(53,74,'COD','Pending','2025-10-12 09:46:01');
/*!40000 ALTER TABLE `thanhtoan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tinnhan`
--

DROP TABLE IF EXISTS `tinnhan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tinnhan` (
  `message_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `staff_id` int DEFAULT NULL,
  `noidung` text,
  `ngaygui` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `user_id` (`user_id`),
  KEY `staff_id` (`staff_id`),
  CONSTRAINT `tinnhan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `nguoidung` (`user_id`),
  CONSTRAINT `tinnhan_ibfk_2` FOREIGN KEY (`staff_id`) REFERENCES `nhanvien` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tinnhan`
--

LOCK TABLES `tinnhan` WRITE;
/*!40000 ALTER TABLE `tinnhan` DISABLE KEYS */;
/*!40000 ALTER TABLE `tinnhan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trangthaidonhang`
--

DROP TABLE IF EXISTS `trangthaidonhang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trangthaidonhang` (
  `trangthai_id` int NOT NULL,
  `ten_trangthai` varchar(100) NOT NULL,
  `mo_ta` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`trangthai_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trangthaidonhang`
--

LOCK TABLES `trangthaidonhang` WRITE;
/*!40000 ALTER TABLE `trangthaidonhang` DISABLE KEYS */;
INSERT INTO `trangthaidonhang` VALUES (1,'Chờ xác nhận','Đơn hàng mới được tạo, chờ bộ phận bán hàng kiểm tra.'),(2,'Đã xác nhận','Đơn hàng đã được xác nhận và đang chuẩn bị lấy hàng.'),(3,'Đang giao hàng','Đơn hàng đã bàn giao cho đơn vị vận chuyển.'),(4,'Đã giao hàng','Khách hàng đã nhận và thanh toán thành công (hoặc đã trả tiền trước).'),(5,'Đã hủy','Đơn hàng bị hủy bởi khách hàng hoặc quản trị viên.'),(6,'Hoàn hàng','Đơn hàng đã được hoàn trả lại (ví dụ: bị bom hàng).');
/*!40000 ALTER TABLE `trangthaidonhang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vanchuyen`
--

DROP TABLE IF EXISTS `vanchuyen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vanchuyen` (
  `shipping_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `trangthai` varchar(50) DEFAULT 'dangchuanbi',
  `ngaysua` datetime DEFAULT CURRENT_TIMESTAMP,
  `receiver_name` varchar(255) NOT NULL,
  `receiver_phone` varchar(20) DEFAULT NULL,
  `receiver_address` text NOT NULL,
  `notes` text,
  `phuongthuctt` varchar(50) NOT NULL,
  PRIMARY KEY (`shipping_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `vanchuyen_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `donhang` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vanchuyen`
--

LOCK TABLES `vanchuyen` WRITE;
/*!40000 ALTER TABLE `vanchuyen` DISABLE KEYS */;
INSERT INTO `vanchuyen` VALUES (55,68,'dangchuanbi','2025-10-10 16:54:04','nguyena','123','111','','COD'),(56,69,'dangchuanbi','2025-10-10 18:41:48','nguyenvanb','123','123','','COD'),(57,70,'dangchuanbi','2025-10-10 20:13:16','nguyena','123','111','','COD'),(58,71,'dangchuanbi','2025-10-10 20:48:58','nguyena','123','111','','COD'),(59,72,'dangchuanbi','2025-10-12 08:15:11','nguyena','123','111','','COD'),(60,73,'dangchuanbi','2025-10-12 08:15:20','nguyena','123','111','','COD'),(61,74,'dangchuanbi','2025-10-12 09:46:01','nguyena','123','111','','COD');
/*!40000 ALTER TABLE `vanchuyen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'webnangcao'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-12 10:22:24
