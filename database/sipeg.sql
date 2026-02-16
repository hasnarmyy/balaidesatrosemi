-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 10, 2026 at 08:28 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipeg`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `connection` text COLLATE utf8mb4_general_ci NOT NULL,
  `queue` text COLLATE utf8mb4_general_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_general_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_09_17_185637_create_sessions_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `payload` longtext COLLATE utf8mb4_general_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('HHMhV7mdqiFx33nkU8i2VjdysSa6RRTbfpeyJbwj', 9, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNVlDRVJjUGE3OXZ1bVRkTXRVSXF4Y3lxdHA3b2o1aG56RGszclBkRiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wZWdhd2FpL2xhcG9yYW4tdHBwIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6OTtzOjEwOiJtYXN1a191c2VyIjtiOjE7fQ==', 1770698016),
('sugFFZsO00fjNqGmEWmtc4Jn5ddPXBcbvBXihAsd', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36 Edg/144.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZVAzbHNNM05nbXdPdXIxUDJMQ0x5RTNSaEprWnNhNnRQMmNyUXhPZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9jZXRhay1hYnNlbi1sZW1idXIvMjAyNi8yIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjExOiJtYXN1a19hZG1pbiI7YjoxO30=', 1770699043);

-- --------------------------------------------------------

--
-- Table structure for table `tb_jabatan`
--

CREATE TABLE `tb_jabatan` (
  `id_jabatan` int NOT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `salary` double NOT NULL,
  `overtime` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_jabatan`
--

INSERT INTO `tb_jabatan` (`id_jabatan`, `jabatan`, `salary`, `overtime`) VALUES
(1, 'Staff', 520000, 45009),
(2, 'Manager', 850000, 12500),
(18, 'Developer', 850500, 58000),
(19, 'Sekretaris', 18900, 8800),
(20, 'Kepala Desa', 150000, 10500),
(21, 'Aparatur Desa', 85002, 12300);

-- --------------------------------------------------------

--
-- Table structure for table `tb_lembur`
--

CREATE TABLE `tb_lembur` (
  `id_lembur` int NOT NULL,
  `id_pegawai` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `date` date NOT NULL,
  `waktu_lembur` time NOT NULL,
  `status` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_lembur`
--

INSERT INTO `tb_lembur` (`id_lembur`, `id_pegawai`, `date`, `waktu_lembur`, `status`) VALUES
(30, 'P-009', '2025-12-09', '17:01:00', 1),
(31, 'P-009', '2025-12-22', '16:28:00', 1),
(32, 'P-010', '2025-12-22', '16:43:00', 1),
(33, 'P-012', '2025-12-22', '17:55:00', 1),
(34, 'P-014', '2025-12-23', '09:47:00', 1),
(35, 'P-008', '2025-12-23', '11:12:00', 1),
(37, 'P-007', '2025-12-29', '19:05:00', 1),
(38, 'P-009', '2025-12-30', '19:35:00', 1),
(39, 'P-014', '2026-01-03', '17:29:00', 1),
(40, 'P-008', '2026-01-10', '18:08:00', 1),
(41, 'P-009', '2026-01-10', '19:09:00', 1),
(43, 'P-008', '2026-01-14', '16:20:00', 1),
(44, 'P-013', '2026-02-06', '20:04:00', 1),
(45, 'P-015', '2026-02-06', '15:04:00', 1),
(46, 'P-015', '2026-02-08', '14:37:00', 1),
(47, 'P-009', '2026-02-10', '07:34:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_payroll`
--

CREATE TABLE `tb_payroll` (
  `id_payroll` int NOT NULL,
  `id_pegawai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_jabatan` int NOT NULL,
  `periode` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `gaji_bersih` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tb_payroll`
--

INSERT INTO `tb_payroll` (`id_payroll`, `id_pegawai`, `id_jabatan`, `periode`, `tanggal`, `keterangan`, `gaji_bersih`) VALUES
(16, 'P-009', 2, '2025-12-31 23:59:59', '2025-12-29', 'reward', 875990),
(17, 'P-008', 18, '2025-12-31 23:59:59', '2026-01-03', 'kerja bagus', 841500),
(19, 'P-009', 2, '2026-01-31 23:59:59', '2026-01-14', 'kerja bagus', 2542771),
(20, 'P-007', 1, '2026-02-28 23:59:59', '2026-02-06', 'baik', 510900),
(21, 'P-009', 2, '2026-02-28 23:59:59', '2026-02-06', 'kerja bagus', 1680750);

-- --------------------------------------------------------

--
-- Table structure for table `tb_payroll_detail`
--

CREATE TABLE `tb_payroll_detail` (
  `id_payroll_detail` int NOT NULL,
  `id_payroll` int NOT NULL,
  `potongan_absen` double NOT NULL DEFAULT '0',
  `gaji_pokok` double NOT NULL DEFAULT '0',
  `gaji_lembur` double NOT NULL DEFAULT '0',
  `bonus` double NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_payroll_detail`
--

INSERT INTO `tb_payroll_detail` (`id_payroll_detail`, `id_payroll`, `potongan_absen`, `gaji_pokok`, `gaji_lembur`, `bonus`) VALUES
(16, 16, 10000, 850000, 29490, 6500),
(17, 17, 10000, 850500, 0, 1000),
(19, 19, 20000, 2550000, 12771, 0),
(20, 20, 10000, 520000, 0, 900),
(21, 21, 20000, 1700000, 0, 750);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pegawai`
--

CREATE TABLE `tb_pegawai` (
  `id_pegawai` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `id_user` int NOT NULL,
  `id_jabatan` int NOT NULL,
  `nama_pegawai` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `jekel` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `pendidikan` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `status_kepegawaian` int NOT NULL,
  `agama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `no_hp` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ktp` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_masuk` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pegawai`
--

INSERT INTO `tb_pegawai` (`id_pegawai`, `id_user`, `id_jabatan`, `nama_pegawai`, `jekel`, `pendidikan`, `status_kepegawaian`, `agama`, `no_hp`, `alamat`, `foto`, `ktp`, `tanggal_masuk`) VALUES
('P-007', 7, 1, 'Tsukishima Kei', 'L', 'S1', 1, 'Protestan', '08123456789', 'Tokyo', 'pegawai/foto/Hd9zlreldAxNeUxUhYgBpUJGpNljRxXCbbfCz0WB.jpg', 'pegawai/ktp/WkFomwvrsCfYZgJtl75V4fSWpxScxI6JAj998HMK.jpg', '2025-12-04'),
('P-008', 8, 18, 'Gojo Satoru', 'L', 'Sarjana Komputer', 1, 'Islam', '089645723113', 'Shibuya', 'pegawai/t6f3nyzjtrHfe5Pyfc6gRjyNHRCkmI5BPikmCUHS.jpg', 'pegawai/ktp/9ogyl41GX3VRalC1G5fZIzWqM66XcHsYRogXpLBo.jpg', '2025-12-08'),
('P-009', 9, 2, 'Inumaki', 'L', 'SMA', 1, 'Islam', '083561928013', 'Japan', 'pegawai/foto/gLeWOIfhRGuu2Nccz6LwnNECfmnsUueEAiimBvfB.jpg', 'pegawai/ktp/zqLH9siDlxQjufaBwsyw8pCxuJRvNhZI0cAMFTjs.jpg', '2025-12-09'),
('P-010', 10, 19, 'Shimizu Kiyoko', 'P', 'S2', 1, 'Katolik', '09126837825', 'Jepang', 'pegawai/foto/mNijeZOiQ1JA4SH0S8iA2inFFnu5Jn73j8ufUXVr.png', 'pegawai/ktp/7KqXoZRlFcbrtNKigzfLlrVge2nVbbn8kbXCVBtU.jpg', '2025-12-22'),
('P-011', 11, 18, 'Nobara Kugisaki', 'P', 'S2', 1, 'Budha', '0874219076', 'Amerika', 'pegawai/foto/UnDWcVt6tbKp16ranJwkyQ5soSSaD4IEdEQ3yKQt.jpg', 'pegawai/ktp/QOZKHqVnDu8aqVemLXj49nb1VC4ch5rs2mfFlEwa.jpg', '2025-12-22'),
('P-012', 12, 1, 'Hinata Hyuga', 'P', 'S1', 1, 'Budha', '085284248299', 'USA', 'pegawai/foto/gNNdJrObUMrhxmy4fgZmnlLAssSsX6iehDxZLesU.jpg', 'pegawai/ktp/yq93L0AlQpoqRsuvXNTDlUT4f41kjf1dIFvd5SEB.jpg', '2025-12-22'),
('P-013', 13, 19, 'Shoyo', 'L', 'SMA', 1, 'Islam', '0917322542748', 'China', 'pegawai/foto/B4WH8CKquRn8mlqEFaBiRXU9xUUO9mVlNhHcKFIO.jpg', 'pegawai/ktp/bQEPkD8qFUMFELDkF9bMEvVf6VE1XsqDNNRN1E4z.jpg', '2025-12-22'),
('P-014', 14, 18, 'Kageyama Tobio', 'L', 'S1', 1, 'Hindu', '0123798268459', 'Indonesia', 'pegawai/foto/dcRlCymAbBmNyAhJh44ace1DnbhsVcmESPQGJEsk.jpg', 'pegawai/ktp/GKeL2BXwthiGeU2NH4VT3OgwbwoCjAISL0iDYKT4.jpg', '2025-12-22'),
('P-015', 15, 19, 'adit', 'L', 'S1', 1, 'Islam', '08927695667', 'Solo', 'pegawai/foto/9Tho8eJCS4ouULFDWD7FcBegIw8DD0FeKpBAxl17.jpg', 'pegawai/ktp/vi56iUlmuxFrlaOMJqglMzR8Bn6K5CAOewXx4rwI.jpg', '2022-01-10'),
('P-016', 16, 21, 'rofi mukhlis', 'L', 'SMA', 0, 'Budha', '08927695667124', 'Indonesia', 'pegawai/foto/YNtGUrLMuI02FyItM5wfYThDVRGkzpt46g9ar1Y4.jpg', 'pegawai/ktp/3dBHGCJsunoxIuWyVYHflq9mlydOYoq8NWhQoG4u.jpg', '2026-01-01'),
('P-017', 17, 19, 'jarwo', 'L', 'SMP', 0, 'Katolik', '098643592', 'Indonesia', 'pegawai/foto/jcpxy8tx3LHkC8zmzekE4wOblUqU8FRNmtbYMvAH.jpg', 'pegawai/ktp/H2D02mPam3wxod0zBus1bAXIVprVI3LSkvqo2sCo.jpg', '2026-02-01');

-- --------------------------------------------------------

--
-- Table structure for table `tb_presents`
--

CREATE TABLE `tb_presents` (
  `id_presents` int NOT NULL,
  `id_pegawai` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `keterangan` int NOT NULL,
  `foto_selfie_masuk` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `foto_selfie_pulang` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan_izin` text COLLATE utf8mb4_general_ci,
  `id_lembur` int DEFAULT NULL,
  `status` int DEFAULT '0',
  `jam_masuk` time DEFAULT NULL,
  `keterangan_msk` int DEFAULT NULL,
  `jam_pulang` time DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_presents`
--

INSERT INTO `tb_presents` (`id_presents`, `id_pegawai`, `tanggal`, `waktu`, `keterangan`, `foto_selfie_masuk`, `foto_selfie_pulang`, `keterangan_izin`, `id_lembur`, `status`, `jam_masuk`, `keterangan_msk`, `jam_pulang`, `latitude`, `longitude`) VALUES
(169, 'P-008', '2025-12-30', '21:47:42', 1, 'gambar/absensi/absen_masuk_P-008_1767106062.jpg', NULL, NULL, NULL, 1, '21:47:42', 1, NULL, '-7.5589565', '110.8523117'),
(170, 'P-008', '2025-12-30', '21:53:44', 2, NULL, 'gambar/absensi/absen_pulang_P-008_1767106424.jpg', NULL, NULL, 2, NULL, NULL, '21:53:44', '-7.5589565', '110.8523117'),
(171, 'P-009', '2025-12-30', '21:56:27', 1, 'gambar/absensi/absen_masuk_P-009_1767106587.jpg', NULL, NULL, NULL, 1, '21:56:27', 1, NULL, '-7.5589565', '110.8523117'),
(172, 'P-009', '2025-12-30', '21:56:33', 3, NULL, 'gambar/absensi/absen_lembur_P-009_1767106593.jpg', NULL, 38, 3, NULL, NULL, '21:56:33', '-7.5589565', '110.8523117'),
(173, 'P-007', '2026-01-03', '19:46:59', 1, 'gambar/absensi/absen_masuk_P-007_1767444419.jpg', NULL, NULL, NULL, 1, '19:46:59', 1, NULL, '-7.5754887', '110.8243272'),
(174, 'P-007', '2026-01-03', '19:47:29', 2, NULL, 'gambar/absensi/absen_pulang_P-007_1767444449.jpg', NULL, NULL, 2, NULL, NULL, '19:47:29', '-7.5754887', '110.8243272'),
(175, 'P-014', '2026-01-03', '20:34:36', 1, 'gambar/absensi/absen_masuk_P-014_1767447276.jpg', NULL, NULL, NULL, 1, '20:34:36', 1, NULL, '-7.5754887', '110.8243272'),
(176, 'P-014', '2026-01-03', '20:36:24', 3, NULL, 'gambar/absensi/absen_lembur_P-014_1767447384.jpg', NULL, 39, 3, NULL, NULL, '20:36:24', '-7.5754887', '110.8243272'),
(177, 'P-008', '2026-01-03', '20:50:43', 1, 'gambar/absensi/absen_masuk_P-008_1767448243.jpg', NULL, NULL, NULL, 1, '20:50:43', 1, NULL, '-7.5864808578782', '110.7489868218'),
(178, 'P-008', '2026-01-03', '20:50:55', 2, NULL, 'gambar/absensi/absen_pulang_P-008_1767448255.jpg', NULL, NULL, 2, NULL, NULL, '20:50:55', '-7.5864808578782', '110.7489868218'),
(179, 'P-009', '2026-01-04', '17:56:43', 1, 'gambar/absensi/absen_masuk_P-009_1767524203.jpg', NULL, NULL, NULL, 1, '17:56:43', 1, NULL, '-7.5866619', '110.7490429'),
(180, 'P-009', '2026-01-04', '17:56:55', 2, NULL, 'gambar/absensi/absen_pulang_P-009_1767524215.jpg', NULL, NULL, 2, NULL, NULL, '17:56:55', '-7.5866619', '110.7490429'),
(181, 'P-009', '2026-01-10', '20:10:09', 1, 'gambar/absensi/absen_masuk_P-009_1768050609.jpg', NULL, NULL, NULL, 1, '20:10:09', 1, NULL, '-7.5754887', '110.8243272'),
(182, 'P-009', '2026-01-10', '20:10:18', 3, NULL, 'gambar/absensi/absen_lembur_P-009_1768050618.jpg', NULL, 41, 3, NULL, NULL, '20:10:18', '-7.5754887', '110.8243272'),
(183, 'P-008', '2026-01-10', '20:17:30', 1, 'gambar/absensi/absen_masuk_P-008_1768051050.jpg', NULL, NULL, NULL, 1, '20:17:30', 1, NULL, '-7.5754887', '110.8243272'),
(184, 'P-008', '2026-01-10', '20:17:40', 3, NULL, 'gambar/absensi/absen_lembur_P-008_1768051060.jpg', NULL, 40, 3, NULL, NULL, '20:17:40', '-7.5754887', '110.8243272'),
(185, 'P-012', '2026-01-10', '20:54:45', 1, 'gambar/absensi/absen_masuk_P-012_1768053285.jpg', NULL, NULL, NULL, 1, '20:54:45', 1, NULL, '-7.5866936', '110.748963'),
(186, 'P-012', '2026-01-10', '20:55:37', 2, NULL, 'gambar/absensi/absen_pulang_P-012_1768053337.jpg', NULL, NULL, 2, NULL, NULL, '20:55:37', '-7.5866936', '110.748963'),
(187, 'P-009', '2026-01-14', '00:11:06', 1, 'gambar/absensi/absen_masuk_P-009_1768324266.jpg', NULL, NULL, NULL, 1, '00:11:06', 0, NULL, '-7.5754887', '110.8243272'),
(188, 'P-009', '2026-01-14', '00:11:20', 2, NULL, 'gambar/absensi/absen_pulang_P-009_1768324280.jpg', NULL, NULL, 2, NULL, NULL, '00:11:20', '-7.5754887', '110.8243272'),
(189, 'P-009', '2026-01-19', '20:17:53', 1, 'gambar/absensi/absen_masuk_P-009_1768828673.jpg', NULL, NULL, NULL, 0, '20:17:53', 1, NULL, '-7.5864808578782', '110.7489868218'),
(190, 'P-009', '2026-01-19', '20:18:03', 2, NULL, 'gambar/absensi/absen_pulang_P-009_1768828683.jpg', NULL, NULL, 0, NULL, NULL, '20:18:03', '-7.5864808578782', '110.7489868218'),
(192, 'P-009', '2026-02-06', '15:21:05', 1, 'gambar/absensi/absen_masuk_P-009_1770366065.jpg', NULL, NULL, NULL, 1, '15:21:05', 1, NULL, '-7.5864868248927', '110.74897440665'),
(193, 'P-009', '2026-02-06', '15:21:16', 2, NULL, 'gambar/absensi/absen_pulang_P-009_1770366076.jpg', NULL, NULL, 2, NULL, NULL, '15:21:16', '-7.5864868248927', '110.74897440665'),
(194, 'P-007', '2026-02-06', '15:21:51', 1, 'gambar/absensi/absen_masuk_P-007_1770366111.jpg', NULL, NULL, NULL, 1, '15:21:51', 1, NULL, '-7.5864868248927', '110.74897440665'),
(195, 'P-007', '2026-02-06', '15:21:59', 2, NULL, 'gambar/absensi/absen_pulang_P-007_1770366119.jpg', NULL, NULL, 2, NULL, NULL, '15:21:59', '-7.5864868248927', '110.74897440665'),
(196, 'P-009', '2026-02-09', '19:50:29', 1, 'gambar/absensi/absen_masuk_P-009_1770641429.jpg', NULL, NULL, NULL, 1, '19:50:29', 1, NULL, '-7.5864868248927', '110.74897440665'),
(197, 'P-009', '2026-02-09', '19:50:38', 2, NULL, 'gambar/absensi/absen_pulang_P-009_1770641438.jpg', NULL, NULL, 2, NULL, NULL, '19:50:38', '-7.5864868248927', '110.74897440665'),
(198, 'P-009', '2026-02-10', '11:37:47', 1, 'gambar/absensi/absen_masuk_P-009_1770698267.jpg', NULL, NULL, NULL, 1, '11:37:47', 1, NULL, '-7.5566845739743', '110.77096690786'),
(199, 'P-009', '2026-02-10', '11:43:25', 3, NULL, 'gambar/absensi/absen_lembur_P-009_1770698605.jpg', NULL, 47, 3, NULL, NULL, '11:43:25', '-7.5566845739743', '110.77096690786');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `kode` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `image` varchar(150) NOT NULL,
  `password` varchar(260) NOT NULL,
  `role_id` int NOT NULL,
  `is_active` int NOT NULL,
  `date_created` int NOT NULL,
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`kode`, `name`, `email`, `image`, `password`, `role_id`, `is_active`, `date_created`, `id`) VALUES
('0', 'SuperAdmin', 'admin@gmail.com', 'gambar/default.png', '$2y$10$0DAUTbhSyNNaco0etD6Ckeh4MaqAcBS/BZnsENlqwgBm7t7.eTlNy', 1, 1, 1601653500, 1),
('U-007', 'Tsukishima Kei', 'keitsuki@gmail.com', 'pegawai/foto/Hd9zlreldAxNeUxUhYgBpUJGpNljRxXCbbfCz0WB.jpg', '$2y$12$hgw5y447s4cJMNO0taLFmOEfzXnyFMJw6OQXYlqCgC4ZhEnhCR.e.', 2, 1, 1764798742, 7),
('U-008', 'Gojo Satoru', 'gojosatoru@gmail.com', 'pegawai/t6f3nyzjtrHfe5Pyfc6gRjyNHRCkmI5BPikmCUHS.jpg', '$2y$12$v5zDsD9yCnhNN.Aov.qZTep6WoF7vobDk/wUl9gRhELAMvnDX4Jte', 2, 1, 1765197351, 8),
('U-009', 'Inumaki', 'inumaki@gmail.com', 'pegawai/foto/gLeWOIfhRGuu2Nccz6LwnNECfmnsUueEAiimBvfB.jpg', '$2y$12$2PA0w0vJUZ9o3RS5IssTWe/8X/spOjVpsmeJSgFWhFV4nZbZtcZtC', 2, 1, 1765291992, 9),
('U-010', 'Shimizu Kiyoko', 'kiyoko@gmail.com', 'pegawai/foto/mNijeZOiQ1JA4SH0S8iA2inFFnu5Jn73j8ufUXVr.png', '$2y$12$JYH6G8e/7xjzbYv86OyLOO5j/HOIE/AtrMEmtUnv2SkZ23ZYz0WEy', 2, 1, 1766406781, 10),
('U-011', 'Nobara Kugisaki', 'nobara@gmail.com', 'pegawai/foto/UnDWcVt6tbKp16ranJwkyQ5soSSaD4IEdEQ3yKQt.jpg', '$2y$12$zeKiwTppl0Oy/cJ08j2y4ON8hO8bAt5l/Y9ScWzrb0WcHqgtF.yQq', 2, 1, 1766408030, 11),
('U-012', 'Hinata Hyuga', 'hinatahyuga@gmail.com', 'pegawai/foto/gNNdJrObUMrhxmy4fgZmnlLAssSsX6iehDxZLesU.jpg', '$2y$12$L3HGDoAD4EMooGKtDMODPOd7kCu5PQ9xaVf6w0JsChKyEfcOlEOeW', 2, 1, 1766415306, 12),
('U-013', 'Shoyo', 'shoyo@gmail.com', 'pegawai/foto/B4WH8CKquRn8mlqEFaBiRXU9xUUO9mVlNhHcKFIO.jpg', '$2y$12$tGEU2wLQa7B/qviHPVRaGOl8zTMnRilv96SA41ZIpFXwgyEUEt/OG', 2, 1, 1766415971, 13),
('U-014', 'Kageyama Tobio', 'kageyama@gmail.com', 'pegawai/foto/dcRlCymAbBmNyAhJh44ace1DnbhsVcmESPQGJEsk.jpg', '$2y$12$6Wjfv09LujyEnbcLnOsSIO/zlvCTa38YN.IZBf9P4OlvDaI20dmRy', 2, 1, 1766417701, 14),
('U-015', 'adit', 'adit@gmail.com', 'pegawai/foto/9Tho8eJCS4ouULFDWD7FcBegIw8DD0FeKpBAxl17.jpg', '$2y$12$wdPWCFKuhoc2aHRmAh5OHOArZ91EjLtAleGCorO5XhAbdxY5xvoY6', 2, 1, 1768052174, 15),
('U-016', 'rofi mukhlis', 'rofi@gmail.com', 'pegawai/foto/YNtGUrLMuI02FyItM5wfYThDVRGkzpt46g9ar1Y4.jpg', '$2y$12$PeGX9fgEar/Yq5BF/ouzcuRpqhXaLrK8.TNe572Lfgdmiz/OOAeki', 2, 0, 1770549840, 16),
('U-017', 'jarwo', 'jarwo@gmail.com', 'pegawai/foto/jcpxy8tx3LHkC8zmzekE4wOblUqU8FRNmtbYMvAH.jpg', '$2y$12$JrwfgUx.DEPe2p013snHrO9V7ZsEIQ9ng9uHIMheUM3.hZXgJhPe6', 2, 0, 1770552769, 17);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` int NOT NULL,
  `role` varchar(130) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `role`) VALUES
(1, 'admin'),
(2, 'petugas');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tb_jabatan`
--
ALTER TABLE `tb_jabatan`
  ADD PRIMARY KEY (`id_jabatan`);

--
-- Indexes for table `tb_lembur`
--
ALTER TABLE `tb_lembur`
  ADD PRIMARY KEY (`id_lembur`),
  ADD KEY `fk_lembur_pegawai` (`id_pegawai`);

--
-- Indexes for table `tb_payroll`
--
ALTER TABLE `tb_payroll`
  ADD PRIMARY KEY (`id_payroll`),
  ADD KEY `fk_payroll_pegawai` (`id_pegawai`),
  ADD KEY `fk_payroll_jabatan` (`id_jabatan`);

--
-- Indexes for table `tb_payroll_detail`
--
ALTER TABLE `tb_payroll_detail`
  ADD PRIMARY KEY (`id_payroll_detail`),
  ADD KEY `fk_payrolldetail_payroll` (`id_payroll`);

--
-- Indexes for table `tb_pegawai`
--
ALTER TABLE `tb_pegawai`
  ADD PRIMARY KEY (`id_pegawai`),
  ADD KEY `fk_pegawai_user` (`id_user`),
  ADD KEY `fk_jabatan` (`id_jabatan`);

--
-- Indexes for table `tb_presents`
--
ALTER TABLE `tb_presents`
  ADD PRIMARY KEY (`id_presents`),
  ADD KEY `idx_pegawai` (`id_pegawai`),
  ADD KEY `idx_lembur` (`id_lembur`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_kode` (`kode`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_jabatan`
--
ALTER TABLE `tb_jabatan`
  MODIFY `id_jabatan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tb_lembur`
--
ALTER TABLE `tb_lembur`
  MODIFY `id_lembur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `tb_payroll`
--
ALTER TABLE `tb_payroll`
  MODIFY `id_payroll` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tb_payroll_detail`
--
ALTER TABLE `tb_payroll_detail`
  MODIFY `id_payroll_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tb_presents`
--
ALTER TABLE `tb_presents`
  MODIFY `id_presents` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_lembur`
--
ALTER TABLE `tb_lembur`
  ADD CONSTRAINT `fk_lembur_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `tb_pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_payroll`
--
ALTER TABLE `tb_payroll`
  ADD CONSTRAINT `fk_payroll_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `tb_jabatan` (`id_jabatan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payroll_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `tb_pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_payroll_detail`
--
ALTER TABLE `tb_payroll_detail`
  ADD CONSTRAINT `fk_payrolldetail_payroll` FOREIGN KEY (`id_payroll`) REFERENCES `tb_payroll` (`id_payroll`) ON DELETE CASCADE;

--
-- Constraints for table `tb_pegawai`
--
ALTER TABLE `tb_pegawai`
  ADD CONSTRAINT `fk_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `tb_jabatan` (`id_jabatan`),
  ADD CONSTRAINT `fk_pegawai_jabatan` FOREIGN KEY (`id_jabatan`) REFERENCES `tb_jabatan` (`id_jabatan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pegawai_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_presents`
--
ALTER TABLE `tb_presents`
  ADD CONSTRAINT `fk_presents_lembur` FOREIGN KEY (`id_lembur`) REFERENCES `tb_lembur` (`id_lembur`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presents_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `tb_pegawai` (`id_pegawai`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
