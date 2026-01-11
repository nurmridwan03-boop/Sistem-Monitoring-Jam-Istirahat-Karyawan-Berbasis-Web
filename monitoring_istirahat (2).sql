-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 15 Des 2025 pada 05.54
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `monitoring_istirahat`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `fcm_tokens`
--

CREATE TABLE `fcm_tokens` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` text NOT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `istirahat`
--

CREATE TABLE `istirahat` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `waktu_mulai` datetime NOT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `durasi` int(11) NOT NULL,
  `status` enum('belum','sedang','selesai') DEFAULT 'belum',
  `otomatis_selesai` tinyint(1) DEFAULT 0,
  `catatan` varchar(255) DEFAULT NULL,
  `role` enum('admin','karyawan') DEFAULT 'karyawan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `istirahat`
--

INSERT INTO `istirahat` (`id`, `user_id`, `waktu_mulai`, `waktu_selesai`, `durasi`, `status`, `otomatis_selesai`, `catatan`, `role`) VALUES
(89, 1, '2025-12-14 02:00:17', '2025-12-14 02:03:03', 1, 'selesai', 1, '', 'karyawan'),
(113, 1, '2025-12-14 04:19:06', '2025-12-14 04:20:09', 1, 'selesai', 1, '', 'karyawan'),
(114, 13, '0000-00-00 00:00:00', NULL, 0, NULL, 1, 'tes ting', 'karyawan'),
(115, 2, '2025-12-14 04:50:43', '2025-12-14 04:51:44', 1, 'selesai', 1, '', 'karyawan'),
(116, 1, '2025-12-14 05:17:36', '2025-12-14 05:17:40', 120, 'selesai', 0, '', 'karyawan'),
(117, 123, '0000-00-00 00:00:00', NULL, 0, NULL, 0, '', 'karyawan'),
(118, 3, '2025-12-14 05:50:00', '2025-12-14 05:51:00', 1, 'selesai', 1, '', 'karyawan'),
(119, 123, '2025-12-14 06:05:09', '2025-12-14 06:06:10', 1, 'selesai', 1, 'percobaan', 'karyawan'),
(120, 51, '2025-12-14 06:11:49', '2025-12-14 06:12:49', 1, 'selesai', 1, 'coba', 'karyawan'),
(121, 1, '2025-12-14 13:36:20', '2025-12-14 14:36:22', 60, 'selesai', 1, '', 'karyawan'),
(122, 1, '2025-12-15 00:53:06', '2025-12-15 01:01:41', 60, 'selesai', 0, '', 'karyawan'),
(123, 1, '2025-12-15 01:10:36', '2025-12-15 01:10:55', 60, 'selesai', 0, '', 'karyawan'),
(124, 1, '2025-12-15 01:11:03', '2025-12-15 01:12:03', 1, 'selesai', 1, '', 'karyawan'),
(125, 13, '0000-00-00 00:00:00', NULL, 0, NULL, 0, '', 'karyawan'),
(126, 1, '2025-12-15 01:26:34', '2025-12-15 01:27:34', 1, 'selesai', 1, '', 'karyawan'),
(127, 123, '2025-12-15 01:28:05', '2025-12-15 01:29:06', 1, 'selesai', 1, '', 'karyawan'),
(128, 51, '2025-12-15 01:37:52', '2025-12-15 01:38:53', 1, 'selesai', 1, '', 'karyawan'),
(129, 1, '2025-12-15 02:45:28', '2025-12-15 02:46:30', 1, 'selesai', 1, '', 'karyawan'),
(130, 1, '2025-12-15 03:37:43', '2025-12-15 03:37:46', 60, 'selesai', 0, '', 'karyawan'),
(131, 1, '2025-12-15 03:37:53', '2025-12-15 03:38:59', 1, 'selesai', 1, '', 'karyawan'),
(132, 1, '2025-12-15 04:20:23', '2025-12-15 04:21:31', 1, 'selesai', 1, '', 'karyawan'),
(133, 3, '2025-12-15 04:22:39', '2025-12-15 04:23:41', 1, 'selesai', 1, '', 'karyawan'),
(134, 13, '2025-12-15 04:24:27', '2025-12-15 04:25:28', 1, 'selesai', 1, '', 'karyawan'),
(135, 11213, '2025-12-15 04:26:04', '2025-12-15 04:27:05', 1, 'selesai', 1, '', 'karyawan'),
(136, 1, '2025-12-15 04:28:31', '2025-12-15 04:29:31', 1, 'selesai', 1, '', 'karyawan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `pesan` varchar(255) NOT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_baca` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `id_karyawan`, `pesan`, `waktu`, `status_baca`) VALUES
(96, 1, 'Waktu istirahat Anda telah berakhir.', '2025-12-13 21:11:09', 1),
(97, 13, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-13 21:12:39', 1),
(98, 51, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-13 21:13:19', 1),
(99, 1, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-13 21:20:09', 1),
(100, 13, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-13 21:43:36', 1),
(101, 2, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-13 21:51:44', 0),
(102, 1, 'Waktu istirahat Anda telah berakhir.', '2025-12-13 22:17:41', 1),
(103, 3, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-13 22:51:00', 1),
(104, 123, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-13 23:06:10', 1),
(105, 51, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-13 23:12:50', 1),
(106, 1, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 07:36:26', 1),
(107, 1, 'Waktu istirahat Anda telah berakhir.', '2025-12-14 18:01:41', 1),
(108, 1, 'Waktu istirahat Anda telah berakhir.', '2025-12-14 18:10:56', 1),
(109, 1, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 18:12:03', 1),
(110, 1, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 18:27:34', 1),
(111, 123, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 18:29:06', 1),
(112, 51, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 18:38:54', 0),
(113, 1, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 19:46:30', 0),
(114, 1, 'Waktu istirahat Anda telah berakhir.', '2025-12-14 20:37:46', 0),
(115, 1, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 20:38:59', 0),
(116, 1, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 21:21:31', 0),
(117, 3, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 21:23:41', 0),
(118, 13, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 21:25:28', 0),
(119, 11213, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 21:27:05', 0),
(120, 1, 'Waktu istirahat Anda telah habis (selesai otomatis).', '2025-12-14 21:29:31', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_istirahat`
--

CREATE TABLE `riwayat_istirahat` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `waktu_mulai` datetime DEFAULT NULL,
  `waktu_selesai` datetime DEFAULT NULL,
  `durasi` int(11) DEFAULT NULL COMMENT 'Durasi dalam menit',
  `status` varchar(20) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `otomatis_selesai` tinyint(1) DEFAULT 0,
  `waktu_reset` datetime DEFAULT current_timestamp() COMMENT 'Waktu ketika status direset'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `riwayat_istirahat`
--

INSERT INTO `riwayat_istirahat` (`id`, `user_id`, `waktu_mulai`, `waktu_selesai`, `durasi`, `status`, `catatan`, `otomatis_selesai`, `waktu_reset`) VALUES
(18, 1, '2025-12-14 03:30:06', '2025-12-14 03:30:08', 60, 'selesai', NULL, 0, '2025-12-14 03:40:32'),
(19, 2, '2025-12-14 03:39:26', '2025-12-14 03:40:26', 1, 'selesai', NULL, 1, '2025-12-14 03:40:32'),
(20, 3, '2025-12-14 03:05:57', '2025-12-14 03:07:00', 1, 'selesai', NULL, 1, '2025-12-14 03:40:32'),
(21, 13, '2025-12-14 03:31:49', '2025-12-14 03:32:53', 1, 'selesai', NULL, 1, '2025-12-14 03:40:33'),
(22, 51, '2025-12-14 03:06:59', '2025-12-14 03:10:20', 1, 'selesai', NULL, 1, '2025-12-14 03:40:33'),
(23, 123, '2025-12-14 03:31:20', '2025-12-14 03:32:33', 1, 'selesai', NULL, 1, '2025-12-14 03:40:33'),
(24, 11213, '2025-12-14 03:32:28', '2025-12-14 03:38:40', 1, 'selesai', NULL, 1, '2025-12-14 03:40:33'),
(25, 1, '2025-12-14 04:11:01', '2025-12-14 04:11:09', 1, 'selesai', NULL, 0, '2025-12-14 04:13:55'),
(26, 13, '2025-12-14 04:11:36', '2025-12-14 04:12:39', 1, 'selesai', NULL, 1, '2025-12-14 04:13:55'),
(27, 51, '2025-12-14 04:12:19', '2025-12-14 04:13:19', 1, 'selesai', NULL, 1, '2025-12-14 04:13:56');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `umur` int(3) DEFAULT NULL,
  `divisi` varchar(100) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `nomerHP` varchar(20) DEFAULT NULL,
  `role` enum('admin','karyawan') NOT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `jenis_kelamin`, `umur`, `divisi`, `nama`, `nomerHP`, `role`, `alamat`, `foto`, `email`) VALUES
(1, 'admin', '123', 'Laki-laki', 23, 'svp', 'Nur Muhammad Ridwan', '', 'admin', 'jl bhakti 1', '1765435354_aiden char 1x1.jpg', 'nurmridwan03@gmail.com'),
(2, 'kiki', '12345', 'Laki-laki', 23, 'bartender', 'kiki', '', 'karyawan', 'di situ', '1765387273_aiden char 21x1.jpg', 'idanrwan@gmail.com'),
(3, 'habibi', '12345', 'Perempuan', 18, 'waiters', 'habibi', '083878631417', 'karyawan', 'jl bakti 1', '1765428952_wallpaper miku2.jpg', 'nurmridwan03@gmail.com'),
(13, 'bintang', '12345', 'Laki-laki', 20, 'bartender', 'bintang', '', 'karyawan', 'gang keramat', '1765663957_Designer (7).jpeg', 'tes@gmail.co'),
(51, 'rahmat', '12345', 'Laki-laki', 22, 'waiters', 'rahmat', '083878631410', 'karyawan', 'gang kelinci', '1765647656_wallpaper miku.jpg', 'nut@gmail.com'),
(52, 'p12', '12345', 'Laki-laki', 45, 'Runner', 'p', '083878631417', 'karyawan', 'p', '', 'tes@gmail.co'),
(123, 'amel', '12345', 'Perempuan', 24, 'pleting', 'amel', '', 'karyawan', 'jlbhakti 1', '1765385980_cd3b0c05cb6908d0f63deea072efcdd0.jpg', 'idanrwan@gmail.com'),
(11213, 'wisnu', '12345', 'Perempuan', 12, 'pleting', 'Wisnu', '08387363000', 'karyawan', 'jhswui', '1765391887_aiden char 1x1.jpg', 'idanrwan@gmail.com');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `fcm_tokens`
--
ALTER TABLE `fcm_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_token` (`user_id`,`token`(255)),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indeks untuk tabel `istirahat`
--
ALTER TABLE `istirahat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_karyawan` (`id_karyawan`);

--
-- Indeks untuk tabel `riwayat_istirahat`
--
ALTER TABLE `riwayat_istirahat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_waktu_reset` (`waktu_reset`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `fcm_tokens`
--
ALTER TABLE `fcm_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `istirahat`
--
ALTER TABLE `istirahat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT untuk tabel `riwayat_istirahat`
--
ALTER TABLE `riwayat_istirahat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221012;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `fcm_tokens`
--
ALTER TABLE `fcm_tokens`
  ADD CONSTRAINT `fcm_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `istirahat`
--
ALTER TABLE `istirahat`
  ADD CONSTRAINT `istirahat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`id_karyawan`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
