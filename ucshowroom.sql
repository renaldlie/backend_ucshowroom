-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2023 at 07:02 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ucshowroom`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id_customer` int(16) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `notelp` varchar(255) NOT NULL,
  `id_card` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`id_customer`, `nama`, `notelp`, `id_card`, `img`) VALUES
(3, 'Bdasd', '312312312312', 'asdasdasdasd', ''),
(4, 'Babidasdas', '312312312312', 'asdasdasdasd', ''),
(5, 'sadas', '312312312312', 'asdasdasdasd', ''),
(6, 'Babiasdasd', '312312312312', 'asdasdasdasd', ''),
(7, 'Halo', 'jalan-jalan', '123456', ''),
(8, 'Halo', '0182312321', '123456', ''),
(9, 'Halo', '0182312321', '123456', ''),
(15, 'beelzebub', '08123', 'hdklskshshd', ''),
(16, 'nknibib', 'GG vhbh ', 'vhvhvub', ''),
(17, 'nknibib', 'GG vhbh ', 'vhvhvub', ''),
(18, 'rewre', 'rewrewr', 'rewrewrew', ''),
(19, '', '', '', ''),
(20, 'dsadsa', 'dsadasd', 'dsadasd', ''),
(21, 'dsadsa', 'dsadasd', 'dsadasd', ''),
(22, 'dsadsa', 'dsadasd', 'dsadasd', ''),
(23, 'dsadsa', 'dsadasd', 'dsadasd', ''),
(24, 'dsadsa', 'dsadasd', 'dsadasd', ''),
(25, 'dsadsa', 'dsadasd', 'dsadasd', ''),
(26, 'dsadsa', 'dsadasd', 'dsadasd', ''),
(27, 'dsadsa', 'dsadasd', 'dsadasd', ''),
(28, '12321', '321312', '3123123', ''),
(29, 'ewqe', 'eqwewq', 'eqweqwe', ''),
(30, '423432', '423423', '42344234', '');

-- --------------------------------------------------------

--
-- Table structure for table `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` int(11) NOT NULL,
  `jenis_kendaraan` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `jenis_kendaraan`) VALUES
(1, 'Mobil'),
(2, 'Motor'),
(3, 'Truck');

-- --------------------------------------------------------

--
-- Table structure for table `mobil`
--

CREATE TABLE `mobil` (
  `id_mobil` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `model` varchar(255) NOT NULL,
  `tahun` int(11) NOT NULL,
  `jumlah_penumpang` varchar(255) NOT NULL,
  `manufaktur` varchar(255) NOT NULL,
  `harga` float NOT NULL,
  `bahanbakar` varchar(255) NOT NULL,
  `luasbagasi` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mobil`
--

INSERT INTO `mobil` (`id_mobil`, `id_kendaraan`, `model`, `tahun`, `jumlah_penumpang`, `manufaktur`, `harga`, `bahanbakar`, `luasbagasi`) VALUES
(1, 1, 'Pajero', 2020, '4', 'Mitsubishi', 500000000, 'Solar', '500m2'),
(2, 1, 'Xenia', 2022, '6', 'Daihatsu', 150000000, 'Pertamax', '500m2'),
(3, 1, 'Ferrari v2', 2018, '2', 'Ferrari', 10000000000, 'Turbo', '250m2\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `motor`
--

CREATE TABLE `motor` (
  `id_motor` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `model` varchar(255) NOT NULL,
  `tahun` int(11) NOT NULL,
  `manufaktur` varchar(255) NOT NULL,
  `harga` int(11) NOT NULL,
  `jumlah_penumpang` int(11) NOT NULL,
  `ukuran_bagasi` decimal(10,2) DEFAULT NULL,
  `kapasitas_bensin` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `motor`
--

INSERT INTO `motor` (`id_motor`, `id_kendaraan`, `model`, `tahun`, `manufaktur`, `harga`, `jumlah_penumpang`, `ukuran_bagasi`, `kapasitas_bensin`) VALUES
(1, 1, 'CBR', 2023, 'Honda', 34000000, 2, 25.00, 24.00),
(2, 1, 'CBR', 2023, 'Honda', 34000000, 2, 25.00, 24.00);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `id_customer` int(11) DEFAULT NULL,
  `id_kendaraan` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_customer`, `id_kendaraan`, `jumlah`, `total`) VALUES
(2, 3, 5, 10, 200000.00);

-- --------------------------------------------------------

--
-- Table structure for table `truck`
--

CREATE TABLE `truck` (
  `id_truck` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `model` varchar(255) NOT NULL,
  `tahun` int(11) NOT NULL,
  `jumlah_penumnpang` int(11) NOT NULL,
  `manufaktur` varchar(255) NOT NULL,
  `harga` float NOT NULL,
  `roda_ban` int(11) DEFAULT NULL,
  `luas_kargo` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `truck`
--

INSERT INTO `truck` (`id_truck`, `id_kendaraan`, `model`, `tahun`, `jumlah_penumnpang`, `manufaktur`, `harga`, `roda_ban`, `luas_kargo`) VALUES
(1, 3, 'Carry', 2021, 4, 'Suzuki', 312312000, 4, 200.00),
(3, 3, 'GREAT ', 2020, 4, 'HINO', 35000000, 8, 1000.00),
(4, 3, 'GREAT ', 2020, 4, 'HINO', 35000000, 8, 1000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id_customer`);

--
-- Indexes for table `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`);

--
-- Indexes for table `mobil`
--
ALTER TABLE `mobil`
  ADD PRIMARY KEY (`id_mobil`),
  ADD KEY `id_kendaraan` (`id_kendaraan`);

--
-- Indexes for table `motor`
--
ALTER TABLE `motor`
  ADD PRIMARY KEY (`id_motor`),
  ADD KEY `motor_ibfk_1` (`id_kendaraan`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`);

--
-- Indexes for table `truck`
--
ALTER TABLE `truck`
  ADD PRIMARY KEY (`id_truck`),
  ADD KEY `fk_truck_kendaraan` (`id_kendaraan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id_customer` int(16) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `mobil`
--
ALTER TABLE `mobil`
  MODIFY `id_mobil` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `motor`
--
ALTER TABLE `motor`
  MODIFY `id_motor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `truck`
--
ALTER TABLE `truck`
  MODIFY `id_truck` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mobil`
--
ALTER TABLE `mobil`
  ADD CONSTRAINT `mobil_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`);

--
-- Constraints for table `motor`
--
ALTER TABLE `motor`
  ADD CONSTRAINT `motor_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`);

--
-- Constraints for table `truck`
--
ALTER TABLE `truck`
  ADD CONSTRAINT `fk_truck_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
