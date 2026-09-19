-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 24, 2026 at 07:49 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mitztianpc_wired_internet_services`
--

-- --------------------------------------------------------

--
-- Table structure for table `inquiries_tbl`
--

CREATE TABLE `inquiries_tbl` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `contact_number` int(50) NOT NULL,
  `date_received` date NOT NULL,
  `status` varchar(50) NOT NULL,
  `description` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inquiries_tbl`
--

INSERT INTO `inquiries_tbl` (`id`, `full_name`, `email_address`, `contact_number`, `date_received`, `status`, `description`) VALUES
(1, 'Dianna Braga', 'diennabraga@gmail.com', 2147483647, '0000-00-00', '', 'ang hirap ha'),
(2, 'Dianna Braga', 'diennabraga@gmail.com', 434343, '2026-06-23', 'Ongoing', 'ang hirap ha'),
(3, 'Noel Bonde Jr.', 'noel@gmail.com', 2147483647, '0000-00-00', 'Ongoing', 'hehe wataniceee'),
(4, 'Noel Bonde Jr.', 'noel@gmail.com', 2147483647, '0000-00-00', 'Pending', 'hehe wataniceee'),
(5, 'rr', 'dc@gmail.com', 926648253, '0000-00-00', '', 'hrhr'),
(6, 'Dianna Braga', 'diennabraga@gmail.com', 2147483647, '2026-06-23', '', 'ok'),
(7, 'Heart Joy Abainza', 'hearty@gmail.com', 920043843, '2026-06-23', '', '22'),
(8, 'Heart Joy Abainza', 'hearty@gmail.com', 1234353, '2026-06-23', 'Pending', 'magkano 50 mbps'),
(9, 'Noel Bonde Jr.', 'noel@gmail.com', 123456678, '0000-00-00', 'Pending', 'No\'ng isilang ka sa mundong ito, laking tuwa ng magulang mo\r\nAt ang kamay nila ang \'yong ilaw\r\nAt ang nanay at tatay mo\'y \'di malaman ang gagawin\r\nMinamasdan pati pagtulog mo\r\nAt sa gabi\'y napupuyat ang iyong nanay sa pagtimpla ng gatas mo\r\nAt sa umaga nama\'y kalong ka ng iyong amang tuwang-tuwa sa iyo\r\nNgayon nga ay malaki ka na ang nais mo\'y maging malaya\r\n\'Di man sila payag, walang magagawa\r\nIkaw nga ay biglang nagbago, naging matigas ang iyong ulo\r\nAt ang payo nila\'y sinuway mo'),
(10, 'Noel Bonde Jr.', 'noel@gmail.com', 123456678, '0000-00-00', 'Pending', 'No\'ng isilang ka sa mundong ito, laking tuwa ng magulang mo\r\nAt ang kamay nila ang \'yong ilaw\r\nAt ang nanay at tatay mo\'y \'di malaman ang gagawin\r\nMinamasdan pati pagtulog mo\r\nAt sa gabi\'y napupuyat ang iyong nanay sa pagtimpla ng gatas mo\r\nAt sa umaga nama\'y kalong ka ng iyong amang tuwang-tuwa sa iyo\r\nNgayon nga ay malaki ka na ang nais mo\'y maging malaya\r\n\'Di man sila payag, walang magagawa\r\nIkaw nga ay biglang nagbago, naging matigas ang iyong ulo\r\nAt ang payo nila\'y sinuway mo'),
(11, 'Noel Bonde Jr.', 'noel@gmail.com', 926648253, '0000-00-00', 'Pending', 'No\'ng isilang ka sa mundong ito, laking tuwa ng magulang mo\r\nAt ang kamay nila ang \'yong ilaw\r\nAt ang nanay at tatay mo\'y \'di malaman ang gagawin\r\nMinamasdan pati pagtulog mo\r\nAt sa gabi\'y napupuyat ang iyong nanay sa pagtimpla ng gatas mo\r\nAt sa umaga nama\'y kalong ka ng iyong amang tuwang-tuwa sa iyo\r\nNgayon nga ay malaki ka na ang nais mo\'y maging malaya\r\n\'Di man sila payag, walang magagawa\r\nIkaw nga ay biglang nagbago, naging matigas ang iyong ulo\r\nAt ang payo nila\'y sinuway mo'),
(12, 'Noel Bonde Jr.', 'noel@gmail.com', 926648253, '0000-00-00', 'Pending', 'No\'ng isilang ka sa mundong ito, laking tuwa ng magulang mo\r\nAt ang kamay nila ang \'yong ilaw\r\nAt ang nanay at tatay mo\'y \'di malaman ang gagawin\r\nMinamasdan pati pagtulog mo\r\nAt sa gabi\'y napupuyat ang iyong nanay sa pagtimpla ng gatas mo\r\nAt sa umaga nama\'y kalong ka ng iyong amang tuwang-tuwa sa iyo\r\nNgayon nga ay malaki ka na ang nais mo\'y maging malaya\r\n\'Di man sila payag, walang magagawa\r\nIkaw nga ay biglang nagbago, naging matigas ang iyong ulo\r\nAt ang payo nila\'y sinuway mo'),
(13, 'dian', 'diana@gmail.com', 2147483647, '2026-06-23', 'Pending', 'No\'ng isilang ka sa mundong ito, laking tuwa ng magulang mo\r\nAt ang kamay nila ang \'yong ilaw\r\nAt ang nanay at tatay mo\'y \'di malaman ang gagawin\r\nMinamasdan pati pagtulog mo\r\nAt sa gabi\'y napupuyat ang iyong nanay sa pagtimpla ng gatas mo\r\nAt sa umaga nama\'y kalong ka ng iyong amang tuwang-tuwa sa iyo\r\nNgayon nga ay malaki ka na ang nais mo\'y maging malaya\r\n\'Di man sila payag, walang magagawa\r\nIkaw nga ay biglang nagbago, naging matigas ang iyong ulo\r\nAt ang payo nila\'y sinuway mo'),
(14, 'dian', 'diana@gmail.com', 2147483647, '2026-06-23', 'Ongoing', 'No\'ng isilang ka sa mundong ito, laking tuwa ng magulang mo\r\nAt ang kamay nila ang \'yong ilaw\r\nAt ang nanay at tatay mo\'y \'di malaman ang gagawin\r\nMinamasdan pati pagtulog mo\r\nAt sa gabi\'y napupuyat ang iyong nanay sa pagtimpla ng gatas mo\r\nAt sa umaga nama\'y kalong ka ng iyong amang tuwang-tuwa sa iyo\r\nNgayon nga ay malaki ka na ang nais mo\'y maging malaya\r\n\'Di man sila payag, walang magagawa\r\nIkaw nga ay biglang nagbago, naging matigas ang iyong ulo\r\nAt ang payo nila\'y sinuway mo'),
(15, 'Heart Joy Abainza', 'hearty@gmail.com', 123, '2026-06-23', 'Pending', 'hello'),
(16, 'Heart Joy Abainza', 'hearty@gmail.com', 123, '2026-06-23', 'Pending', 'hello'),
(17, 'Noel Bonde Jr.', 'noel@gmail.com', 2147483647, '2026-06-23', 'Pending', 'It all came so easy\r\nAll the lovin\' you gave me\r\nThe feelings we shared\r\nAnd I still can remember\r\nHow your touch was so tender\r\nIt told me you cared\r\nWe had a once in a lifetime\r\nBut I just couldn\'t see\r\nUntil it was gone\r\nA second once in a lifetime\r\nMay be too much to ask\r\nBut I swear from now on\r\nIf ever you\'re in my arms again\r\nThis time I\'ll love you much better\r\nIf ever you\'re in my arms again\r\nThis time I\'ll hold you forever\r\nThis time we\'ll never end\r\nNow, I\'m seein\' clearly\r\nHow I still need you near me\r\nI still love you so\r\nThere\'s something between us\r\nThat won\'t ever leave us\r\nThere\'s no letting go'),
(18, 'Dianna Braga', 'diennabraga@gmail.com', 2147483647, '2026-06-23', 'Ongoing', 'ang bagal ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inquiries_tbl`
--
ALTER TABLE `inquiries_tbl`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inquiries_tbl`
--
ALTER TABLE `inquiries_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
