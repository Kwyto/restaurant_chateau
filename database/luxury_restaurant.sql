-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 01:43 PM
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
-- Database: `luxury_restaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `membership_required` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `description`, `discount_value`, `expiration_date`, `membership_required`) VALUES
(1, 'WELCOME10', 'Welcome Discount', 10.00, '2025-07-08', 'none'),
(2, 'BDAY25', 'Birthday Special', 25.00, '2025-08-07', NULL),
(3, 'GOLD15', 'Gold Member Exclusive', 15.00, '2025-09-06', 'gold'),
(5, 'SUMMER10', 'Summer Discount', 10.00, '2025-07-23', NULL),
(6, 'PLAT30', 'Platinum Exclusive - 30% Off', 30.00, '2025-09-06', 'platinum'),
(7, 'GOLD20', 'Gold Member Special - 20% Off', 20.00, '2025-08-07', 'gold'),
(8, 'Kupon USU', 'Kupon Anak USU', 69.00, '2025-06-30', NULL),
(17, 'kupon baruu', 'halo member', 50.00, '2025-06-26', NULL),
(18, 'kupon musim panas', 'kupon musim panas', 50.00, '2025-06-26', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` int(11) NOT NULL,
  `level` varchar(50) NOT NULL,
  `discount_percent` int(11) DEFAULT 0,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` varchar(50) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `name`, `description`, `price`, `category`, `image_path`, `is_featured`, `created_at`) VALUES
(1, 'Black Truffle Pastaaa', 'Handmade pasta with fresh black truffle and parmesan cream sauce', 85.00, 'main', 'items/black-truffle-pastaaa.jpg', 1, '2025-06-07 21:11:07'),
(2, 'A5 Wagyu Beef', 'Japanese A5 Wagyu with truffle mashed potatoes and seasonal vegetables', 220.00, 'main', 'items\\A5-WAGYU.jpg', 1, '2025-06-07 21:11:07'),
(3, 'Butter Poached Lobster', 'Maine lobster with saffron risotto and herb butter sauce', 95.00, 'main', 'items\\butter-poached.jpg', 1, '2025-06-07 21:11:07'),
(4, 'Foie Gras Terrine', 'House-made foie gras terrine with brioche and fruit compote', 65.00, 'starter', 'items\\foie-gras.jpg', 0, '2025-06-07 21:11:07'),
(5, 'Caviar Service', '30g of premium Beluga caviar with traditional accompaniments', 350.00, 'starter', 'items\\caviar-service.jpg', 0, '2025-06-07 21:11:07'),
(6, 'Oysters Rockefeller', 'Fresh Blue Point oysters with spinach, herbs and hollandaise', 45.00, 'starter', 'items\\oysters.jpg', 0, '2025-06-07 21:11:07'),
(7, 'Tuna Tartare', 'Yellowfin tuna with avocado, citrus and sesame crisp', 38.00, 'starter', 'items\\tuna-tartare.jpg', 1, '2025-06-07 21:11:07'),
(8, 'Burrata Caprese', 'Imported burrata with heirloom tomatoes and basil oil', 32.00, 'starter', 'items\\burrata.jpg', 1, '2025-06-07 21:11:07'),
(9, 'Escargot Bourguignon', 'Classic French snails in garlic herb butter', 28.00, 'starter', 'items\\escargot.jpg', 0, '2025-06-07 21:11:07'),
(10, 'Beef Carpaccio', 'Thinly sliced raw beef with arugula and parmesan', 42.00, 'starter', 'items\\beef-carpaccio.jpg', 0, '2025-06-07 21:11:07'),
(11, 'French Onion Soup', 'Caramelized onions with gruyere cheese and herb croutons', 18.00, 'soup', 'items\\french-onion.jpg', 1, '2025-06-07 21:11:07'),
(12, 'Lobster Bisque', 'Rich cream soup with Maine lobster and cognac', 24.00, 'soup', 'items\\lobster-bisque.jpg', 0, '2025-06-07 21:11:07'),
(13, 'Mushroom Velouté', 'Wild mushroom cream soup with truffle oil', 20.00, 'soup', 'items\\mushroom-veloute.jpg', 1, '2025-06-07 21:11:07'),
(14, 'Tomato Gazpacho', 'Chilled Spanish tomato soup with cucumber and herbs', 16.00, 'soup', 'items\\tomato-gazpach.jpg', 1, '2025-06-07 21:11:07'),
(15, 'Pumpkin Soup', 'Roasted pumpkin with coconut cream and toasted seeds', 19.00, 'soup', 'items\\pumpkin.jpg', 1, '2025-06-07 21:11:07'),
(16, 'Caesar Salad', 'Romaine lettuce with house-made dressing and parmesan', 22.00, 'salad', 'items\\caesar-salad.jpg', 1, '2025-06-07 21:11:07'),
(17, 'Arugula Salad', 'Baby arugula with pear, walnuts and blue cheese', 26.00, 'salad', 'items\\arugula-salad.jpg', 1, '2025-06-07 21:11:07'),
(18, 'Quinoa Bowl', 'Organic quinoa with roasted vegetables and tahini dressing', 28.00, 'salad', 'items\\quinoa.jpg', 1, '2025-06-07 21:11:07'),
(19, 'Nicoise Salad', 'Mixed greens with tuna, olives, eggs and anchovies', 34.00, 'salad', 'items\\nicoise-salad.jpg', 0, '2025-06-07 21:11:07'),
(20, 'Beet Salad', 'Roasted beets with goat cheese and candied pecans', 24.00, 'salad', 'items\\beet-salad.jpg', 1, '2025-06-07 21:11:07'),
(21, 'Duck Confit', 'Slow-cooked duck leg with orange glaze and wild rice', 58.00, 'main', 'items\\duck-confit.jpg', 0, '2025-06-07 21:11:07'),
(22, 'Rack of Lamb', 'Herb-crusted lamb with ratatouille and red wine jus', 72.00, 'main', 'items\\rack-lamb.jpg', 0, '2025-06-07 21:11:07'),
(23, 'Chilean Sea Bass', 'Pan-seared with lemon butter and asparagus', 68.00, 'main', 'items\\chilean-sea.jpg', 0, '2025-06-07 21:11:07'),
(24, 'Osso Buco', 'Braised veal shank with saffron risotto', 65.00, 'main', 'items\\osso-buco.jpg', 0, '2025-06-07 21:11:07'),
(25, 'Grilled Salmon', 'Atlantic salmon with quinoa and roasted vegetables', 48.00, 'main', 'items\\grilled-salmon.jpg', 1, '2025-06-07 21:11:07'),
(26, 'Mushroom Risotto', 'Arborio rice with wild mushrooms and truffle oil', 42.00, 'main', 'items\\mushroom-risotto.jpg', 1, '2025-06-07 21:11:07'),
(27, 'Coq au Vin', 'Classic French chicken braised in red wine', 52.00, 'main', 'items\\coq-au.jpg', 0, '2025-06-07 21:11:07'),
(28, 'Vegetable Wellington', 'Puff pastry with roasted vegetables and herbs', 38.00, 'main', 'items\\vegetable-wellington.jpg', 1, '2025-06-07 21:11:07'),
(29, 'Linguine alle Vongole', 'Fresh clams with white wine and garlic', 36.00, 'pasta', 'items\\linguine-alle.jpg', 0, '2025-06-07 21:11:07'),
(30, 'Penne Arrabbiata', 'Spicy tomato sauce with herbs and chili', 28.00, 'pasta', 'items\\penne-arrabbiata.jpg', 1, '2025-06-07 21:11:07'),
(31, 'Fettuccine Alfredo', 'Classic cream sauce with parmesan cheese', 32.00, 'pasta', 'items\\fettuce-alfredo.jpg', 1, '2025-06-07 21:11:07'),
(32, 'Spaghetti Carbonara', 'Eggs, pancetta and pecorino romano', 34.00, 'pasta', 'items\\spaghetti-carbonara.jpg', 0, '2025-06-07 21:11:07'),
(33, 'Gnocchi Gorgonzola', 'Potato dumplings with blue cheese sauce', 30.00, 'pasta', 'items\\gnocchi-gorgonzola.jpg', 1, '2025-06-07 21:11:07'),
(34, 'Crème Brûlée', 'Vanilla custard with caramelized sugar crust', 16.00, 'dessert', 'items\\creme-brulee.jpg', 1, '2025-06-07 21:11:07'),
(35, 'Chocolate Soufflé', 'Dark chocolate soufflé with vanilla ice cream', 22.00, 'dessert', 'items\\chocolate-souffle.jpg', 1, '2025-06-07 21:11:07'),
(36, 'Tiramisu', 'Classic Italian coffee-flavored dessert', 18.00, 'dessert', 'items\\tiramisu.jpg', 1, '2025-06-07 21:11:07'),
(37, 'Tarte Tatin', 'Upside-down apple tart with vanilla bean ice cream', 20.00, 'dessert', 'items/tarte-tatin.jpg', 1, '2025-06-07 21:11:07'),
(38, 'Panna Cotta', 'Vanilla cream with berry compote', 14.00, 'dessert', 'items\\panna-cotta.jpg', 1, '2025-06-07 21:11:07'),
(39, 'Lemon Tart', 'Citrus curd tart with meringue and candied lemon', 17.00, 'dessert', 'items\\lemon-tart.jpg', 1, '2025-06-07 21:11:07'),
(40, 'Chocolate Lava Cake', 'Warm chocolate cake with molten center', 19.00, 'dessert', 'items\\chocolate-lava.jpg', 1, '2025-06-07 21:11:07'),
(41, 'French Press Coffee', 'Single origin Ethiopian beans', 8.00, 'beverage', 'items\\french-press.jpg', 1, '2025-06-07 21:11:07'),
(42, 'Earl Grey Tea', 'Premium Ceylon tea with bergamot', 6.00, 'beverage', 'items\\earl-grey.jpg', 1, '2025-06-07 21:11:07'),
(43, 'Fresh Orange Juice', 'Squeezed daily from Valencia oranges', 12.00, 'beverage', 'items\\fresh-orange.jpg', 1, '2025-06-07 21:11:07'),
(44, 'Sparkling Water', 'San Pellegrino or Perrier', 7.00, 'beverage', 'items\\sparkling-water.jpg', 1, '2025-06-07 21:11:07'),
(45, 'House Wine', 'Selected red or white wine by the glass', 18.00, 'beverage', 'items\\house-wine.jpg', 1, '2025-06-07 21:11:07'),
(46, 'Craft Beer', 'Local brewery selection', 14.00, 'beverage', 'items\\craft-beer.jpg', 1, '2025-06-07 21:11:07'),
(47, 'Signature Cocktail', 'Chef\'s special mixed drink', 22.00, 'beverage', 'items\\signature-cocktail.jpg', 1, '2025-06-07 21:11:07'),
(48, 'Truffle Fries', 'Hand-cut fries with truffle oil and parmesan', 16.00, 'side', 'items\\truffle-fries.jpg', 1, '2025-06-07 21:11:07'),
(49, 'Grilled Asparagus', 'Fresh asparagus with lemon and sea salt', 14.00, 'side', 'items\\grilled-asparagus.jpg', 1, '2025-06-07 21:11:07'),
(50, 'Garlic Mashed Potatoes', 'Yukon gold potatoes with roasted garlic', 12.00, 'side', 'items\\garlic-mashed.jpg', 1, '2025-06-07 21:11:07'),
(51, 'Sautéed Spinach', 'Baby spinach with garlic and olive oil', 13.00, 'side', 'items\\sauteed-spinach.jpg', 1, '2025-06-07 21:11:07'),
(52, 'Wild Rice Pilaf', 'Mixed wild rice with herbs and almonds', 15.00, 'side', 'items\\wild-rice.jpg', 1, '2025-06-07 21:11:07'),
(53, 'Roasted Brussels Sprouts', 'With bacon and balsamic glaze', 14.00, 'side', 'items\\roasted-brussels.jpg', 0, '2025-06-07 21:11:07'),
(54, 'Tasting Menu', 'Seven-course chef\'s selection with wine pairing', 180.00, 'special', 'items\\tasting-menu.jpg', 1, '2025-06-07 21:11:07'),
(55, 'Surf and Turf', 'Lobster tail and filet mignon with seasonal vegetables', 125.00, 'special', 'items\\surf-tear.jpg', 0, '2025-06-07 21:11:07'),
(56, 'Whole Roasted Fish', 'Daily catch prepared Mediterranean style', 85.00, 'special', 'items\\whole-rosted.jpg', 0, '2025-06-07 21:11:07'),
(57, 'Seasonal Tasting Platter', 'Selection of seasonal appetizers and small plates', 65.00, 'appetizer', 'items/seasonal-tasting-platter.jpg', 1, '2025-06-07 21:11:07');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('credit_card','paypal','bank_transfer','apple_pay','crypto','google_pay') DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `reservation_id`, `amount`, `payment_method`, `payment_date`, `created_at`) VALUES
(4, 4, 12, 38.50, '', '2025-06-16 11:15:55', '2025-06-16 11:15:55'),
(5, 4, 13, 44.00, '', '2025-06-16 11:41:43', '2025-06-16 11:41:43'),
(6, 4, 16, 429.00, 'paypal', '2025-06-16 14:50:27', '2025-06-16 14:50:27'),
(7, 4, 17, 126.50, 'paypal', '2025-06-16 14:51:32', '2025-06-16 14:51:32'),
(8, 4, 18, 126.50, 'apple_pay', '2025-06-16 14:53:40', '2025-06-16 14:53:40'),
(9, 4, 19, 423.50, 'apple_pay', '2025-06-16 15:20:44', '2025-06-16 15:20:44'),
(10, 4, 21, 27.50, 'apple_pay', '2025-06-16 16:16:49', '2025-06-16 16:16:49'),
(11, 8, 22, 118.80, 'apple_pay', '2025-06-16 17:17:22', '2025-06-16 17:17:22'),
(12, 9, 23, 440.00, 'apple_pay', '2025-06-17 00:36:59', '2025-06-17 00:36:59'),
(13, 9, 24, 440.00, 'apple_pay', '2025-06-17 00:46:19', '2025-06-17 00:46:19'),
(14, 9, 25, 489.50, 'apple_pay', '2025-06-17 00:46:37', '2025-06-17 00:46:37'),
(15, 9, 26, 85.80, 'apple_pay', '2025-06-17 00:46:57', '2025-06-17 00:46:57'),
(16, 9, 27, 96.80, 'apple_pay', '2025-06-17 00:52:00', '2025-06-17 00:52:00'),
(17, 9, 28, 81.40, 'apple_pay', '2025-06-17 00:52:29', '2025-06-17 00:52:29'),
(18, 9, 29, 77.00, 'apple_pay', '2025-06-17 00:52:50', '2025-06-17 00:52:50'),
(19, 9, 30, 123.20, 'apple_pay', '2025-06-17 00:53:37', '2025-06-17 00:53:37'),
(20, 9, 31, 72.60, 'apple_pay', '2025-06-17 00:54:15', '2025-06-17 00:54:15'),
(21, 9, 32, 72.60, 'apple_pay', '2025-06-17 00:54:39', '2025-06-17 00:54:39'),
(22, 9, 33, 407.00, 'apple_pay', '2025-06-17 00:55:39', '2025-06-17 00:55:39'),
(23, 9, 36, 38.50, 'apple_pay', '2025-06-17 01:04:40', '2025-06-17 01:04:40'),
(24, 9, 37, 440.00, 'apple_pay', '2025-06-17 01:24:01', '2025-06-17 01:24:01'),
(25, 9, 38, 577.50, 'apple_pay', '2025-06-17 01:33:12', '2025-06-17 01:33:12'),
(26, 9, 39, 264.00, 'apple_pay', '2025-06-17 01:34:27', '2025-06-17 01:34:27'),
(27, 9, 40, 561.00, 'apple_pay', '2025-06-17 02:59:27', '2025-06-17 02:59:27'),
(28, 9, 41, 335.50, 'apple_pay', '2025-06-17 03:13:14', '2025-06-17 03:13:14'),
(29, 11, 44, 501.60, 'apple_pay', '2025-06-17 05:04:46', '2025-06-17 05:04:46'),
(30, 12, 45, 48.40, 'apple_pay', '2025-06-22 13:08:42', '2025-06-22 13:08:42');

-- --------------------------------------------------------

--
-- Table structure for table `pickup_services`
--

CREATE TABLE `pickup_services` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `pickup_location` text NOT NULL,
  `pickup_time` time NOT NULL,
  `vehicle_type` varchar(50) NOT NULL,
  `vehicle_price` decimal(10,2) NOT NULL,
  `distance` decimal(5,2) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `reservation_id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `guests` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `table_number` int(11) NOT NULL,
  `special_occasion` varchar(50) DEFAULT NULL,
  `pickup_service` tinyint(1) DEFAULT 0,
  `pickup_location` text DEFAULT NULL,
  `pickup_time` time DEFAULT NULL,
  `vehicle_type` varchar(50) DEFAULT NULL,
  `pickup_cost` decimal(10,2) DEFAULT 0.00,
  `food_cost` decimal(10,2) DEFAULT 0.00,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `reservation_id`, `user_id`, `guests`, `reservation_date`, `reservation_time`, `table_number`, `special_occasion`, `pickup_service`, `pickup_location`, `pickup_time`, `vehicle_type`, `pickup_cost`, `food_cost`, `tax_amount`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 'RSV-0002', 4, 4, '2025-06-16', '19:30:00', 0, 'none', 1, NULL, '00:00:00', '0', 0.00, 45.00, 9.50, 104.50, 'completed', '2025-06-15 04:23:42', '2025-06-16 09:31:49'),
(2, 'RSV-0003', 4, 2, '2025-06-16', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-15 04:36:48', '2025-06-16 09:31:49'),
(3, 'RSV-0001', 4, 5, '0000-00-00', '19:30:00', 0, 'anniversary', 1, NULL, '00:00:00', '0', 125000.00, 350.00, 12540.00, 137940.00, 'confirmed', '2025-06-15 05:18:18', '2025-06-16 09:31:49'),
(4, 'RSV-0004', 4, 2, '0000-00-00', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-15 11:31:09', '2025-06-16 09:31:49'),
(5, 'RSV-0005', 4, 2, '0000-00-00', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-15 11:31:15', '2025-06-16 09:31:49'),
(6, '', 4, 2, '2025-06-17', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-16 11:07:48', '2025-06-16 11:07:48'),
(7, '', 4, 2, '2025-06-17', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-16 11:09:02', '2025-06-16 11:09:02'),
(8, '', 4, 2, '2025-06-17', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-16 11:09:15', '2025-06-16 11:09:15'),
(9, '', 4, 2, '2025-06-17', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-16 11:09:18', '2025-06-16 11:09:18'),
(10, '', 4, 2, '2025-06-17', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-16 11:09:20', '2025-06-16 11:09:20'),
(11, '', 4, 2, '2025-06-17', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-16 11:09:21', '2025-06-16 11:09:21'),
(12, '', 4, 2, '2025-06-17', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 38.50, 'completed', '2025-06-16 11:11:42', '2025-06-16 11:15:55'),
(13, '', 4, 4, '2025-06-17', '19:30:00', 8, 'birthday', 0, NULL, NULL, NULL, 0.00, 1080.00, 113.00, 44.00, 'completed', '2025-06-16 11:41:23', '2025-06-16 11:41:43'),
(14, '', 4, 4, '2025-06-17', '19:30:00', 0, 'birthday', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 55.00, 'pending', '2025-06-16 11:52:30', '2025-06-16 11:52:30'),
(15, '', 4, 2, '2025-06-17', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 350.00, 40.00, 440.00, 'cancelled', '2025-06-16 12:30:39', '2025-06-16 12:30:42'),
(16, '', 4, 3, '2025-06-17', '19:30:00', 3, 'anniversary', 0, NULL, NULL, NULL, 0.00, 350.00, 40.00, 429.00, 'completed', '2025-06-16 14:49:30', '2025-06-16 14:50:27'),
(17, '', 4, 3, '2025-06-17', '19:30:00', 5, 'business', 0, NULL, NULL, NULL, 0.00, 65.00, 11.50, 126.50, 'completed', '2025-06-16 14:51:17', '2025-06-16 14:51:32'),
(18, '', 4, 3, '2025-06-17', '19:30:00', 4, 'engagement', 0, NULL, NULL, NULL, 0.00, 65.00, 11.50, 126.50, 'completed', '2025-06-16 14:52:05', '2025-06-16 14:53:40'),
(19, '', 4, 4, '2025-06-19', '19:30:00', 7, 'business', 0, NULL, NULL, NULL, 0.00, 350.00, 40.00, 423.50, 'completed', '2025-06-16 15:20:01', '2025-06-16 15:20:44'),
(20, '', 4, 2, '2025-06-17', '19:30:00', 0, 'birthday', 0, NULL, NULL, NULL, 0.00, 45.00, 9.50, 104.50, 'pending', '2025-06-16 15:21:25', '2025-06-16 15:21:25'),
(21, '', 4, 2, '2025-06-17', '19:30:00', 5, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 27.50, 'completed', '2025-06-16 16:16:35', '2025-06-16 16:16:49'),
(22, '', 8, 3, '2025-06-20', '21:30:00', 3, 'engagement', 0, NULL, NULL, NULL, 0.00, 83.00, 13.30, 118.80, 'completed', '2025-06-16 17:17:03', '2025-06-16 17:17:22'),
(23, '', 9, 5, '2025-06-19', '20:30:00', 4, 'anniversary', 0, NULL, NULL, NULL, 0.00, 350.00, 40.00, 440.00, 'completed', '2025-06-17 00:36:44', '2025-06-17 00:36:59'),
(24, '', 9, 2, '2025-06-18', '19:30:00', 1, 'none', 0, NULL, NULL, NULL, 0.00, 350.00, 40.00, 440.00, 'completed', '2025-06-17 00:46:14', '2025-06-17 00:46:19'),
(25, '', 9, 2, '2025-06-18', '19:30:00', 2, 'anniversary', 0, NULL, NULL, NULL, 0.00, 395.00, 44.50, 489.50, 'completed', '2025-06-17 00:46:32', '2025-06-17 00:46:37'),
(26, '', 9, 2, '2025-06-18', '19:30:00', 3, 'business', 0, NULL, NULL, NULL, 0.00, 28.00, 7.80, 85.80, 'completed', '2025-06-17 00:46:52', '2025-06-17 00:46:57'),
(27, '', 9, 2, '2025-06-18', '19:30:00', 3, 'none', 0, NULL, NULL, NULL, 0.00, 38.00, 8.80, 96.80, 'completed', '2025-06-17 00:51:55', '2025-06-17 00:52:00'),
(28, '', 9, 2, '2025-06-18', '19:30:00', 4, 'none', 0, NULL, NULL, NULL, 0.00, 24.00, 7.40, 81.40, 'completed', '2025-06-17 00:52:15', '2025-06-17 00:52:29'),
(29, '', 9, 2, '2025-06-18', '19:30:00', 9, 'business', 0, NULL, NULL, NULL, 0.00, 20.00, 7.00, 77.00, 'completed', '2025-06-17 00:52:44', '2025-06-17 00:52:50'),
(30, '', 9, 2, '2025-06-18', '19:30:00', 1, 'engagement', 0, NULL, NULL, NULL, 0.00, 62.00, 11.20, 123.20, 'completed', '2025-06-17 00:53:32', '2025-06-17 00:53:37'),
(31, '', 9, 2, '2025-06-18', '19:30:00', 7, 'none', 0, NULL, NULL, NULL, 0.00, 16.00, 6.60, 72.60, 'completed', '2025-06-17 00:54:07', '2025-06-17 00:54:15'),
(32, '', 9, 2, '2025-06-18', '19:30:00', 8, 'none', 0, NULL, NULL, NULL, 0.00, 16.00, 6.60, 72.60, 'completed', '2025-06-17 00:54:32', '2025-06-17 00:54:39'),
(33, '', 9, 2, '2025-06-18', '19:30:00', 2, 'birthday', 0, NULL, NULL, NULL, 0.00, 370.00, 42.00, 407.00, 'completed', '2025-06-17 00:55:19', '2025-06-17 00:55:39'),
(34, '', 9, 2, '2025-06-18', '19:30:00', 0, 'none', 0, NULL, NULL, NULL, 0.00, 65.00, 11.50, 126.50, 'pending', '2025-06-17 00:56:26', '2025-06-17 00:56:26'),
(35, '', 9, 2, '2025-06-18', '19:30:00', 0, 'birthday', 0, NULL, NULL, NULL, 0.00, 350.00, 40.00, 440.00, 'cancelled', '2025-06-17 01:03:59', '2025-06-17 01:04:02'),
(36, '', 9, 2, '2025-06-18', '19:30:00', 5, 'none', 0, NULL, NULL, NULL, 0.00, 0.00, 5.00, 38.50, 'completed', '2025-06-17 01:04:05', '2025-06-17 01:04:40'),
(37, '', 9, 2, '2025-06-18', '19:30:00', 3, 'engagement', 0, NULL, NULL, NULL, 0.00, 350.00, 40.00, 440.00, 'completed', '2025-06-17 01:23:30', '2025-06-17 01:24:01'),
(38, '', 9, 2, '2025-06-18', '19:30:00', 2, 'none', 1, NULL, '17:30:00', '0', 125.00, 350.00, 52.50, 577.50, 'completed', '2025-06-17 01:33:06', '2025-06-17 01:33:12'),
(39, '', 9, 2, '2025-06-18', '19:30:00', 3, 'engagement', 1, NULL, '21:00:00', '0', 125.00, 65.00, 24.00, 264.00, 'completed', '2025-06-17 01:34:19', '2025-06-17 01:34:27'),
(40, '', 9, 2, '2025-06-18', '19:30:00', 3, 'none', 0, NULL, NULL, NULL, 0.00, 460.00, 51.00, 561.00, 'completed', '2025-06-17 02:59:14', '2025-06-17 02:59:27'),
(41, '', 9, 2, '2025-06-18', '19:30:00', 3, 'birthday', 1, NULL, '21:00:00', '0', 125.00, 130.00, 30.50, 335.50, 'completed', '2025-06-17 03:13:06', '2025-06-17 03:13:14'),
(42, '', 11, 2, '2025-06-18', '19:30:00', 0, 'none', 1, NULL, '16:00:00', '0', 125.00, 415.00, 59.00, 649.00, 'pending', '2025-06-17 04:59:30', '2025-06-17 04:59:30'),
(43, '', 11, 2, '2025-06-20', '19:30:00', 0, 'birthday', 1, NULL, '19:30:00', '0', 250.00, 364.00, 66.40, 730.40, 'pending', '2025-06-17 05:02:43', '2025-06-17 05:02:43'),
(44, '', 11, 2, '2025-06-18', '19:30:00', 9, 'birthday', 1, NULL, '16:00:00', '0', 125.00, 350.00, 52.50, 501.60, 'completed', '2025-06-17 05:04:04', '2025-06-17 05:04:46'),
(45, '', 12, 4, '2025-06-26', '19:30:00', 3, 'business', 0, NULL, NULL, NULL, 0.00, 63.00, 11.30, 48.40, 'completed', '2025-06-22 13:07:49', '2025-06-22 13:08:42');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_items`
--

CREATE TABLE `reservation_items` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_items`
--

INSERT INTO `reservation_items` (`id`, `reservation_id`, `menu_item_id`, `quantity`, `price`) VALUES
(1, 13, 4, 1, 65.00),
(2, 13, 2, 1, 220.00),
(3, 13, 3, 1, 95.00),
(4, 13, 5, 2, 350.00),
(5, 15, 5, 1, 350.00),
(6, 16, 5, 1, 350.00),
(7, 17, 4, 1, 65.00),
(8, 18, 4, 1, 65.00),
(9, 19, 5, 1, 350.00),
(10, 20, 6, 1, 45.00),
(11, 22, 7, 1, 38.00),
(12, 22, 6, 1, 45.00),
(13, 23, 5, 1, 350.00),
(14, 24, 5, 1, 350.00),
(15, 25, 5, 1, 350.00),
(16, 25, 6, 1, 45.00),
(17, 26, 9, 1, 28.00),
(18, 27, 7, 1, 38.00),
(19, 28, 12, 1, 24.00),
(20, 29, 13, 1, 20.00),
(21, 30, 19, 1, 34.00),
(22, 30, 18, 1, 28.00),
(23, 31, 14, 1, 16.00),
(24, 32, 14, 1, 16.00),
(25, 33, 5, 1, 350.00),
(26, 33, 13, 1, 20.00),
(27, 34, 4, 1, 65.00),
(28, 35, 5, 1, 350.00),
(29, 37, 5, 1, 350.00),
(30, 38, 5, 1, 350.00),
(31, 39, 4, 1, 65.00),
(32, 40, 4, 1, 65.00),
(33, 40, 5, 1, 350.00),
(34, 40, 6, 1, 45.00),
(35, 41, 4, 2, 65.00),
(36, 42, 4, 1, 65.00),
(37, 42, 5, 1, 350.00),
(38, 43, 5, 1, 350.00),
(39, 43, 49, 1, 14.00),
(40, 44, 5, 1, 350.00),
(41, 45, 6, 1, 45.00),
(42, 45, 36, 1, 18.00);

-- --------------------------------------------------------

--
-- Table structure for table `seat_selection`
--

CREATE TABLE `seat_selection` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(7, 4, 5, 'An absolutely exquisite dining experience from start to finish. The ambiance was refined yet inviting, the service impeccable, and every dish a masterpiece of flavor and presentation. The chef\'s tasting menu with wine pairings took us on an unforgettable culinary journey. This is what fine dining should aspire to be', '2025-06-15 12:28:43'),
(8, 4, 4, 'Pelayanan cukup baik.', '2025-06-15 12:30:09'),
(9, 8, 5, 'tess', '2025-06-16 17:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text NOT NULL,
  `date_of_birth` date NOT NULL,
  `membership_level` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `favorite_cuisines` text DEFAULT NULL,
  `preferred_seating` varchar(50) DEFAULT NULL,
  `dietary_restrictions` text DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `role` enum('user','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `gender`, `phone`, `address`, `date_of_birth`, `membership_level`, `created_at`, `updated_at`, `favorite_cuisines`, `preferred_seating`, `dietary_restrictions`, `profile_photo`, `role`) VALUES
(1, 'Bagas', 'Dwi', 'mbagasdwiss@gmail.com', '$2y$10$xDZ/dxEt7STxE475bQG/ZummPZbXIDzHynwRyU1yiVr.SOb5Jc6MK', 'male', NULL, '', '0000-00-00', NULL, '2025-06-07 14:40:33', '2025-06-07 14:40:33', NULL, NULL, NULL, NULL, 'user'),
(2, 'arief', 'wiguna', 'dwwd@gmail.com', '$2y$10$bZ3qP4jPzfodrEKtL/M8S.GDk6VuZUuVYVULhdlDXy38.J6BYYU92', 'male', NULL, '', '0000-00-00', NULL, '2025-06-08 08:47:24', '2025-06-08 08:47:24', NULL, NULL, NULL, NULL, 'user'),
(3, 'rziky', 'kyooo', 'arief@gmail.com', '$2y$10$reYsSNHbGk4zDFOX8uZzA.pJu40VHBlWfQHqY7FasiFRvFNSKAJCe', 'male', NULL, '', '0000-00-00', 'silver', '2025-06-08 09:00:41', '2025-06-08 11:48:49', NULL, NULL, NULL, NULL, 'user'),
(4, 'ikki', 'skibidi', 'ikki@gmail.com', '$2y$10$o4kIfvCt2sjLFWDtpQCDxOgClMeRZjUx5rvH4bgH2jHio53apMSbG', 'female', '081535371461', 'jalan binjaii', '2000-05-06', 'gold', '2025-06-08 09:18:35', '2025-06-16 17:06:44', 'French,Italian,Japanese', 'window', 'vegan', '/restaurant_chateau/uploads/profiles/profile_4_1750093604.png', 'user'),
(5, 'ngetes', 'tes', 'tes@gmail.com', '$2y$10$quNytboNv8VVWhI2WAwKSejW6c4UiadzFdQTgEQnKFcH3YzO/Y.wS', 'female', NULL, '', '0000-00-00', 'silver', '2025-06-16 15:45:23', '2025-06-16 15:46:06', NULL, NULL, NULL, NULL, 'user'),
(6, 'nopal', 'sigma', 'nopal123@gmail.com', '$2y$10$qXdFKrnnND0SApgFglpDQekqf5ZvHBMr0LMZkc1lwL4VOlQc0qqme', 'male', NULL, '', '0000-00-00', 'silver', '2025-06-16 15:53:49', '2025-06-16 15:54:42', NULL, NULL, NULL, NULL, 'user'),
(7, 'arippp', 'roppp', 'admin@gmail.com', '$2y$10$WSyd.ESnA92bT9X0e0z4F.e8O/Hkz/CwLjhY6pa4yd5DSYLGbKuyy', 'male', NULL, '', '0000-00-00', NULL, '2025-06-16 16:42:51', '2025-06-16 16:43:07', NULL, NULL, NULL, NULL, 'admin'),
(8, '122', '212', 'satu@gmail.com', '$2y$10$9Y13iZDl1fZ.vy.4KDS3veydlfq2XuXOk6IHjgDWs0Jyd46FcpDhu', 'male', '0895612277600', 'halooo', '2000-05-05', 'silver', '2025-06-16 16:47:20', '2025-06-16 17:15:05', 'French,Italian,Japanese,Steakhouse', 'window', 'vegetarian,dairy', '/restaurant_chateau/uploads/profiles/profile_8_1750094105.png', 'user'),
(9, 'bagas', 'bagas', 'bagas@gmail.com', '$2y$10$mxwAnY22kOA9VYMlb0ZvbuZGDQUINbAOT0ikWgF5eD2qAWRm0mSHC', 'male', NULL, '', '0000-00-00', 'gold', '2025-06-17 00:14:43', '2025-06-17 02:04:14', NULL, NULL, NULL, '/restaurant_chateau/uploads/profiles/profile_9_1750125854.png', 'user'),
(10, 'halo', 'lol', 'admin2@gmail.com', '$2y$10$/2rAdNvoJL5gjSWhQAHGv.nq85Wz2OsbHLdEtUKmCMRFUKU9GfTZq', 'male', NULL, '', '0000-00-00', 'silver', '2025-06-17 02:06:24', '2025-06-17 02:56:19', NULL, NULL, NULL, NULL, 'admin'),
(11, 'maulana', 'Al', 'maulana@gmail.com', '$2y$10$FLmak5znlXG7a9al8BLo5OrN7combgAaDaMdXL0TeB8/KiNtXeNt6', 'male', '', '', '2005-05-05', NULL, '2025-06-17 04:48:17', '2025-06-17 04:48:49', NULL, NULL, NULL, NULL, 'user'),
(12, 'ariefff', 'wigunaa', 'aip@gmail.com', '$2y$10$2R7ZsAna2AnX4jsMuJMt4O0Uj.0KKxUHdFNrkeCaCRclMcryrYok6', 'male', NULL, '', '0000-00-00', 'silver', '2025-06-22 13:05:09', '2025-06-22 13:06:17', 'French,Italian', 'window', 'vegetarian', NULL, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `user_coupons`
--

CREATE TABLE `user_coupons` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_coupons`
--

INSERT INTO `user_coupons` (`id`, `user_id`, `coupon_id`, `used`) VALUES
(2, 4, 3, 1),
(7, 4, 1, 1),
(8, 4, 5, 1),
(10, 4, 2, 1),
(11, 4, 7, 0),
(12, 8, 2, 1),
(13, 8, 3, 0),
(17, 11, 8, 1),
(18, 12, 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_memberships`
--

CREATE TABLE `user_memberships` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `membership_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `pickup_services`
--
ALTER TABLE `pickup_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservation_items`
--
ALTER TABLE `reservation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `seat_selection`
--
ALTER TABLE `seat_selection`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_coupons`
--
ALTER TABLE `user_coupons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `coupon_id` (`coupon_id`);

--
-- Indexes for table `user_memberships`
--
ALTER TABLE `user_memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `membership_id` (`membership_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `pickup_services`
--
ALTER TABLE `pickup_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `reservation_items`
--
ALTER TABLE `reservation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `seat_selection`
--
ALTER TABLE `seat_selection`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `user_coupons`
--
ALTER TABLE `user_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_memberships`
--
ALTER TABLE `user_memberships`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `pickup_services`
--
ALTER TABLE `pickup_services`
  ADD CONSTRAINT `pickup_services_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservation_items`
--
ALTER TABLE `reservation_items`
  ADD CONSTRAINT `fk_reservation_items_menu` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`),
  ADD CONSTRAINT `fk_reservation_items_reservation` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seat_selection`
--
ALTER TABLE `seat_selection`
  ADD CONSTRAINT `seat_selection_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_coupons`
--
ALTER TABLE `user_coupons`
  ADD CONSTRAINT `user_coupons_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_coupons_ibfk_2` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`);

--
-- Constraints for table `user_memberships`
--
ALTER TABLE `user_memberships`
  ADD CONSTRAINT `user_memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_memberships_ibfk_2` FOREIGN KEY (`membership_id`) REFERENCES `memberships` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
