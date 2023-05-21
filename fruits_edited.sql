-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2021 at 01:29 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fruits_edited`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `ID` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `ProductPrice` decimal(15,2) NOT NULL,
  `ProductSize` varchar(255) NOT NULL,
  `Client` int(11) NOT NULL DEFAULT 0,
  `Seller` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`ID`, `ProductName`, `ProductPrice`, `ProductSize`, `Client`, `Seller`) VALUES
(2, 'قهوة', '3.99', 'large', 12, 12),
(3, 'شاي', '4.99', 'large', 12, 12);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ID` int(11) NOT NULL,
  `ProductImage` varchar(255) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `ProductPrice` decimal(15,2) NOT NULL,
  `ProductSmallPrice` decimal(15,2) NOT NULL,
  `ProductLargePrice` decimal(15,2) NOT NULL,
  `ProductCategory` varchar(255) NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ID`, `ProductImage`, `ProductName`, `ProductPrice`, `ProductSmallPrice`, `ProductLargePrice`, `ProductCategory`, `UserID`) VALUES
(29, '7274_coffee.png', 'قهوة', '2.99', '1.99', '3.99', 'drinks', 12),
(30, '1948_tea.png', 'شاي', '3.99', '2.99', '4.99', 'drinks', 12),
(31, '8494_cocktail.png', 'كوكتيل', '5.99', '4.99', '6.99', 'drinks', 12),
(32, '7252_teamilk.png', 'شاي باللبن', '4.99', '3.99', '5.99', 'drinks', 12),
(33, '710_samosa.png', 'سمبوسة', '16.99', '15.99', '17.99', 'foods', 12),
(34, '5143_pastry.png', 'معجنات', '4.99', '3.99', '5.99', 'foods', 12),
(35, '5331_stuffedfood.png', 'محشي', '14.99', '13.99', '15.99', 'foods', 12),
(36, '9491_cheesecake.png', 'تشيز كيك بالفراولة', '17.99', '15.99', '19.99', 'foods', 12),
(37, '6038_kabsa.png', 'كبسة لحم', '35.99', '30.99', '40.99', 'foods', 12),
(38, '9788_kabsa2.png', 'كبسة دجاج', '25.99', '20.99', '30.99', 'foods', 12);

-- --------------------------------------------------------

--
-- Table structure for table `sellerinfo`
--

CREATE TABLE `sellerinfo` (
  `ID` int(11) NOT NULL,
  `PhoneNumber` varchar(255) NOT NULL,
  `Address` text NOT NULL,
  `Bio` text NOT NULL,
  `UserID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sellerinfo`
--

INSERT INTO `sellerinfo` (`ID`, `PhoneNumber`, `Address`, `Bio`, `UserID`) VALUES
(4, '281952535', 'fdhdfhfdh', 'fdhfdhhf', 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Username` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `GroupID` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Username`, `Email`, `Password`, `GroupID`) VALUES
(12, 'Seller', 'seller@gmail.com', '$2y$10$ujx74K8nwtzfweu4vaoqHelXMaN7taiRo9SxmZ4f2HZXpJ.85UIum', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Seller` (`Seller`),
  ADD KEY `Client` (`Client`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `sellerinfo`
--
ALTER TABLE `sellerinfo`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `sellerinfo`
--
ALTER TABLE `sellerinfo`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`Seller`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`Client`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sellerinfo`
--
ALTER TABLE `sellerinfo`
  ADD CONSTRAINT `sellerinfo_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
information_schemadbaccountsaccountsCHECK_CONSTRAINTSCHECK_CONSTRAINTS