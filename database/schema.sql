CREATE DATABASE IF NOT EXISTS `dukcapil_sop`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `dukcapil_sop`;

CREATE TABLE IF NOT EXISTS `admin` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sop` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `judul_sop` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT NOT NULL,
  `tanggal_input` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `file_pdf` VARCHAR(255) DEFAULT NULL,
  `gambar` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sop` (`judul_sop`, `deskripsi`) VALUES
('Kartu Keluarga', 'Informasi persyaratan dan alur pelayanan Kartu Keluarga.'),
('KTP Elektronik', 'Informasi persyaratan dan alur pelayanan KTP elektronik.'),
('Akta Kelahiran', 'Informasi persyaratan dan alur pelayanan Akta Kelahiran.');
