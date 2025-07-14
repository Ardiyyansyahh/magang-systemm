/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.11-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: db_magang
-- ------------------------------------------------------
-- Server version	10.11.11-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `dokumen_magang`
--

DROP TABLE IF EXISTS `dokumen_magang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dokumen_magang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int(11) NOT NULL,
  `judul` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NULL DEFAULT current_timestamp(),
  `komentar_dosen` text DEFAULT NULL,
  `status_verifikasi` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `mahasiswa_id` (`mahasiswa_id`),
  CONSTRAINT `dokumen_magang_ibfk_1` FOREIGN KEY (`mahasiswa_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dokumen_magang`
--

LOCK TABLES `dokumen_magang` WRITE;
/*!40000 ALTER TABLE `dokumen_magang` DISABLE KEYS */;
/*!40000 ALTER TABLE `dokumen_magang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporan_mingguan`
--

DROP TABLE IF EXISTS `laporan_mingguan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `laporan_mingguan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int(11) NOT NULL,
  `minggu_ke` int(11) NOT NULL,
  `isi_laporan` text NOT NULL,
  `nilai` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporan_mingguan`
--

LOCK TABLES `laporan_mingguan` WRITE;
/*!40000 ALTER TABLE `laporan_mingguan` DISABLE KEYS */;
INSERT INTO `laporan_mingguan` VALUES
(1,16,2,'bagaimana cara penangann masalah lingkungan',100,'nive juga yaa'),
(2,16,1,'sdafasdfsa',100,'amann'),
(3,1,3,'susah pakk',10,'tidak papa'),
(4,1,4,'ada belajar beblrapa ha;',NULL,NULL),
(5,1,6,'nice',NULL,NULL),
(6,22,1,'Lagi belajar pak',NULL,NULL),
(7,23,1,'lagi belajar ',70,'oke penjelasan sudah cuman ada beberapa yang kurang atau perlu di tambahkan'),
(8,26,1,'masalah perkerjaan',70,'apa yang di keluhkan'),
(9,26,2,'pemahanam tentang pereakitan ac',10,'sudah bagus kayanya'),
(10,26,3,'coba fix ga',100,'laporan sudah baik'),
(11,29,1,'belajar penyolderan',NULL,NULL),
(12,35,1,'belajar penyolderan',80,'Gambar nya kurang lengkp');
/*!40000 ALTER TABLE `laporan_mingguan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pendaftaran_magang`
--

DROP TABLE IF EXISTS `pendaftaran_magang`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pendaftaran_magang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int(11) NOT NULL,
  `perusahaan_id` int(11) DEFAULT NULL,
  `posisi` varchar(255) DEFAULT NULL,
  `dokumen` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Menunggu',
  `tanggal_pengajuan` date DEFAULT curdate(),
  `komentar` text DEFAULT NULL,
  `perusahaan` varchar(255) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pendaftaran_magang`
--

LOCK TABLES `pendaftaran_magang` WRITE;
/*!40000 ALTER TABLE `pendaftaran_magang` DISABLE KEYS */;
INSERT INTO `pendaftaran_magang` VALUES
(1,1,5,'cook','lamaran_6859e994141de.pdf','Disetujui','2025-06-24',NULL,'kfc','batam'),
(2,17,1,'qc','lamaran_685ab11e0b45b.pdf','Disetujui','2025-06-24',NULL,'PT Epson','muka kuning'),
(3,19,1,'data Engginer','lamaran_685cfbd0de6c3.pdf','Disetujui','2025-06-26',NULL,'PT Epson','Muka Kuning'),
(4,22,1,'admin','lamaran_685e5887a2493.pdf','','2025-06-27',NULL,'PT Epson','Muka Kuning'),
(5,23,NULL,'hr','lamaran_68652446e549d.pdf','Disetujui','2025-07-02',NULL,'epson','muka kunign'),
(6,24,1,'admin','lamaran_6865f386f3e6c.pdf','Menunggu','2025-07-03',NULL,NULL,NULL),
(7,25,6,'HR','lamaran_68661aa686aa7.pdf','Disetujui','2025-07-03',NULL,NULL,NULL),
(8,26,10,'admin','lamaran_686624774cb6e.pdf','Disetujui','2025-07-03',NULL,NULL,NULL),
(9,28,6,'Magang','lamaran_686f9fc50decc.pdf','Ditolak','2025-07-10',NULL,NULL,NULL),
(10,29,12,'Magang','lamaran_686fa4c14fa3a.pdf','Disetujui','2025-07-10',NULL,NULL,NULL),
(11,35,13,'Magang','lamaran_686faa61b4b67.pdf','Disetujui','2025-07-10',NULL,NULL,NULL);
/*!40000 ALTER TABLE `pendaftaran_magang` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perusahaan_mitra`
--

DROP TABLE IF EXISTS `perusahaan_mitra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `perusahaan_mitra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kontak` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perusahaan_mitra`
--

LOCK TABLES `perusahaan_mitra` WRITE;
/*!40000 ALTER TABLE `perusahaan_mitra` DISABLE KEYS */;
INSERT INTO `perusahaan_mitra` VALUES
(6,'EPSON','Muka Kuning','Epson@gmail.com'),
(7,'MC Dermott','Batu Ampar','Dermott@gmail.com'),
(8,'Phlips','Muka Kuning','philips@gmail.com'),
(9,'Shimano','Muka Kuning','Shimano@gmail.com'),
(10,'Panasonic','Batam Center','panasonic@gmail.com'),
(12,'simatelex','muka kuning','simatelex@gmail.com'),
(13,'hitech','tanjung uncang','hitech@gmail.com');
/*!40000 ALTER TABLE `perusahaan_mitra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` enum('mahasiswa','dosen','admin') NOT NULL,
  `nim` varchar(20) DEFAULT NULL,
  `angkatan` year(4) DEFAULT NULL,
  `fakultas` varchar(100) DEFAULT NULL,
  `bidang_keahlian` varchar(100) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `nim` (`nim`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(3,'admin',NULL,NULL,NULL,NULL,'Admin','admin@example.com','123','2025-06-23 17:36:32'),
(21,'dosen',NULL,NULL,'','','Dosen','Elbert@gmail.com','123','2025-06-26 10:00:52'),
(36,'mahasiswa','2302010104',2023,'Teknik',NULL,'Ardiyansyah','Ardiyansyah@gmial.com','123','2025-07-14 16:02:31'),
(37,'mahasiswa','230210089',2023,'Teknik',NULL,'fahrul riansyah','Fahrul@gmail.com','123','2025-07-14 16:07:20'),
(38,'mahasiswa','230210102',2023,'Teknik',NULL,'Ibnu Aziz','aziz@gmail.com','123','2025-07-14 16:12:00'),
(39,'mahasiswa','230210148',2023,'Teknik',NULL,'henra','Henra@gmail.com','123','2025-07-14 16:12:55');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-14 23:31:35
