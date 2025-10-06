-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- ホスト: localhost
-- 生成日時: 2025 年 10 月 06 日 10:33
-- サーバのバージョン： 10.4.28-MariaDB
-- PHP のバージョン: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- データベース: `FarmPlan`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `calc_snapshots`
--

CREATE TABLE `calc_snapshots` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `harvest_weight_kg` decimal(12,2) NOT NULL,
  `feed_required_kg` decimal(12,2) NOT NULL,
  `feed_cost_usd` decimal(12,2) NOT NULL,
  `fry_cost_usd` decimal(12,2) NOT NULL,
  `revenue_usd` decimal(12,2) NOT NULL,
  `profit_usd` decimal(12,2) NOT NULL,
  `margin_pct` decimal(6,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `calc_snapshots`
--

INSERT INTO `calc_snapshots` (`id`, `plan_id`, `harvest_weight_kg`, `feed_required_kg`, `feed_cost_usd`, `fry_cost_usd`, `revenue_usd`, `profit_usd`, `margin_pct`, `created_at`) VALUES
(1, 1, 15000.00, 34050.00, 18089.06, 1125.00, 18750.00, -464.06, -2.50, '2025-10-05 00:05:59'),
(2, 3, 15000.00, 34500.00, 16473.75, 1125.00, 18750.00, 1151.25, 6.10, '2025-10-06 07:10:53'),
(3, 7, 15000.00, 31500.00, 16242.19, 2250.00, 18750.00, 257.81, 1.40, '2025-10-06 07:26:00'),
(4, 7, 15000.00, 31500.00, 16242.19, 2250.00, 18750.00, 257.81, 1.40, '2025-10-06 07:26:09');

-- --------------------------------------------------------

--
-- テーブルの構造 `market_prices`
--

CREATE TABLE `market_prices` (
  `id` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `species_id` int(10) UNSIGNED NOT NULL,
  `price_khrkg` int(10) UNSIGNED NOT NULL,
  `source` varchar(120) DEFAULT NULL,
  `noted_on` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- テーブルの構造 `plans`
--

CREATE TABLE `plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `pond_id` int(10) UNSIGNED NOT NULL,
  `species_id` int(10) UNSIGNED NOT NULL,
  `target_size_kg` decimal(6,3) NOT NULL,
  `target_harvest_date` date DEFAULT NULL,
  `sell_price_khrkg` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `plans`
--

INSERT INTO `plans` (`id`, `user_id`, `pond_id`, `species_id`, `target_size_kg`, `target_harvest_date`, `sell_price_khrkg`, `region_id`, `created_at`) VALUES
(1, 1, 1, 1, 0.500, '2025-02-22', 5000, 1, '2025-10-04 22:52:46'),
(2, 1, 3, 1, 0.500, '2025-02-21', 5000, 1, '2025-10-05 00:17:00'),
(3, 1, 4, 1, 0.500, '2025-02-21', 5000, 1, '2025-10-06 07:08:23'),
(4, 1, 5, 1, 0.500, '2025-02-21', 5000, 1, '2025-10-06 07:09:38'),
(5, 1, 6, 1, 0.500, '2025-02-21', 5000, 1, '2025-10-06 07:15:22'),
(6, 1, 7, 1, 0.500, '2025-02-21', 5000, 1, '2025-10-06 07:21:01'),
(7, 1, 7, 1, 0.500, '2025-02-21', 5000, 1, '2025-10-06 07:21:06');

-- --------------------------------------------------------

--
-- テーブルの構造 `plan_feed_mix`
--

CREATE TABLE `plan_feed_mix` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `hf_ratio_pct` tinyint(3) UNSIGNED NOT NULL,
  `hf_blend_price_khrkg` int(10) UNSIGNED NOT NULL,
  `hf_blend_cp_pct` decimal(5,2) NOT NULL,
  `cf_price_khrkg` int(10) UNSIGNED NOT NULL,
  `cf_cp_pct` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `plan_feed_mix`
--

INSERT INTO `plan_feed_mix` (`id`, `plan_id`, `hf_ratio_pct`, `hf_blend_price_khrkg`, `hf_blend_cp_pct`, `cf_price_khrkg`, `cf_cp_pct`, `created_at`) VALUES
(1, 1, 50, 1050, 15.40, 3200, 18.00, '2025-10-05 00:04:55'),
(2, 3, 60, 1050, 15.00, 3200, 18.00, '2025-10-06 07:10:45'),
(3, 7, 50, 925, 17.50, 3200, 15.00, '2025-10-06 07:25:57');

-- --------------------------------------------------------

--
-- テーブルの構造 `plan_feed_recipe_items`
--

CREATE TABLE `plan_feed_recipe_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `ingredient` varchar(120) NOT NULL,
  `ratio_pct` decimal(6,2) NOT NULL,
  `unit_price_khr` int(10) UNSIGNED NOT NULL,
  `cp_pct` decimal(5,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `plan_feed_recipe_items`
--

INSERT INTO `plan_feed_recipe_items` (`id`, `plan_id`, `ingredient`, `ratio_pct`, `unit_price_khr`, `cp_pct`, `created_at`) VALUES
(1, 1, 'Rice Bran', 95.00, 800, 10.00, '2025-10-05 00:03:13'),
(2, 1, 'Fish', 5.00, 1000, 20.00, '2025-10-05 00:04:15'),
(3, 3, 'Rice Bran', 95.00, 800, 10.00, '2025-10-06 07:09:57'),
(4, 3, 'Fish', 5.00, 1000, 20.00, '2025-10-06 07:10:08'),
(5, 5, 'Rice Bran', 30.00, 800, 10.00, '2025-10-06 07:15:35'),
(6, 7, 'Rice Bran', 40.00, 700, 10.00, '2025-10-06 07:21:19'),
(7, 7, 'Fish', 60.00, 1000, 20.00, '2025-10-06 07:21:34'),
(8, 7, 'Fish', 60.00, 1000, 20.00, '2025-10-06 07:24:35');

-- --------------------------------------------------------

--
-- テーブルの構造 `plan_fingerlings`
--

CREATE TABLE `plan_fingerlings` (
  `id` int(10) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `unit_price_khr` int(10) UNSIGNED NOT NULL,
  `stocking_number` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `plan_fingerlings`
--

INSERT INTO `plan_fingerlings` (`id`, `plan_id`, `unit_price_khr`, `stocking_number`, `created_at`) VALUES
(1, 1, 150, 30000, '2025-10-04 23:44:00'),
(2, 2, 150, 30000, '2025-10-05 00:17:00'),
(3, 3, 150, 30000, '2025-10-06 07:08:23'),
(4, 4, 200, 10000, '2025-10-06 07:09:38'),
(5, 5, 150, 30000, '2025-10-06 07:15:22'),
(6, 6, 150, 30000, '2025-10-06 07:21:01'),
(7, 7, 300, 30000, '2025-10-06 07:21:06');

-- --------------------------------------------------------

--
-- テーブルの構造 `ponds`
--

CREATE TABLE `ponds` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `number` int(10) UNSIGNED NOT NULL,
  `location` varchar(120) DEFAULT NULL,
  `length_m` decimal(8,2) NOT NULL,
  `width_m` decimal(8,2) NOT NULL,
  `depth_m` decimal(8,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `ponds`
--

INSERT INTO `ponds` (`id`, `user_id`, `number`, `location`, `length_m`, `width_m`, `depth_m`, `created_at`) VALUES
(1, 1, 1, 'Takeo', 30.00, 50.00, 2.50, '2025-10-04 22:09:24'),
(2, 1, 2, 'Takeo', 20.00, 20.00, 1.50, '2025-10-04 22:09:24'),
(3, 1, 1, 'Takeo', 30.00, 50.00, 2.50, '2025-10-05 00:16:49'),
(4, 1, 2, 'Takeo', 30.00, 50.00, 2.50, '2025-10-06 07:08:19'),
(5, 1, 1, 'Takeo', 30.00, 50.00, 2.50, '2025-10-06 07:09:25'),
(6, 1, 1, 'Takeo', 30.00, 50.00, 2.50, '2025-10-06 07:15:20'),
(7, 1, 1, 'Takeo', 20.00, 30.00, 2.00, '2025-10-06 07:20:57');

-- --------------------------------------------------------

--
-- テーブルの構造 `regions`
--

CREATE TABLE `regions` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(80) NOT NULL,
  `name_km` varchar(120) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `regions`
--

INSERT INTO `regions` (`id`, `name_en`, `name_km`, `created_at`) VALUES
(1, 'Takeo', 'តាកែវ', '2025-10-04 22:08:19'),
(2, 'Phnom Penh', 'ភ្នំពេញ', '2025-10-04 22:08:19'),
(3, 'Kandal', 'កណ្តាល', '2025-10-04 22:08:19');

-- --------------------------------------------------------

--
-- テーブルの構造 `species`
--

CREATE TABLE `species` (
  `id` int(10) UNSIGNED NOT NULL,
  `name_en` varchar(80) NOT NULL,
  `name_km` varchar(120) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `species`
--

INSERT INTO `species` (`id`, `name_en`, `name_km`, `created_at`) VALUES
(1, 'Catfish', 'ត្រីពោធិ៍', '2025-10-04 22:08:50'),
(2, 'Pangasius', 'ត្រីបាសា', '2025-10-04 22:08:50'),
(3, 'Red Tilapia', 'ត្រីទിലាបៀក្រហម', '2025-10-04 22:08:50'),
(4, 'Black Tilapia', 'ត្រីទിലាបៀខ្មៅ', '2025-10-04 22:08:50'),
(5, 'Snakehead', 'ត្រីចេក', '2025-10-04 22:08:50'),
(6, 'Frog', 'កង្កែប', '2025-10-04 22:08:50');

-- --------------------------------------------------------

--
-- テーブルの構造 `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- テーブルのデータのダンプ `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'demo', 'demo123', '2025-10-04 22:07:00');

--
-- ダンプしたテーブルのインデックス
--

--
-- テーブルのインデックス `calc_snapshots`
--
ALTER TABLE `calc_snapshots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_snap_plan` (`plan_id`);

--
-- テーブルのインデックス `market_prices`
--
ALTER TABLE `market_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mkt_region` (`region_id`),
  ADD KEY `fk_mkt_species` (`species_id`);

--
-- テーブルのインデックス `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_plans_users` (`user_id`),
  ADD KEY `fk_plans_ponds` (`pond_id`),
  ADD KEY `fk_plans_species` (`species_id`),
  ADD KEY `fk_plans_region` (`region_id`);

--
-- テーブルのインデックス `plan_feed_mix`
--
ALTER TABLE `plan_feed_mix`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_mix_plan` (`plan_id`);

--
-- テーブルのインデックス `plan_feed_recipe_items`
--
ALTER TABLE `plan_feed_recipe_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_recipe_plan` (`plan_id`);

--
-- テーブルのインデックス `plan_fingerlings`
--
ALTER TABLE `plan_fingerlings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pf_plan` (`plan_id`);

--
-- テーブルのインデックス `ponds`
--
ALTER TABLE `ponds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ponds_users` (`user_id`);

--
-- テーブルのインデックス `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `species`
--
ALTER TABLE `species`
  ADD PRIMARY KEY (`id`);

--
-- テーブルのインデックス `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- ダンプしたテーブルの AUTO_INCREMENT
--

--
-- テーブルの AUTO_INCREMENT `calc_snapshots`
--
ALTER TABLE `calc_snapshots`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- テーブルの AUTO_INCREMENT `market_prices`
--
ALTER TABLE `market_prices`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- テーブルの AUTO_INCREMENT `plans`
--
ALTER TABLE `plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `plan_feed_mix`
--
ALTER TABLE `plan_feed_mix`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `plan_feed_recipe_items`
--
ALTER TABLE `plan_feed_recipe_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- テーブルの AUTO_INCREMENT `plan_fingerlings`
--
ALTER TABLE `plan_fingerlings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `ponds`
--
ALTER TABLE `ponds`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- テーブルの AUTO_INCREMENT `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- テーブルの AUTO_INCREMENT `species`
--
ALTER TABLE `species`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- テーブルの AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- ダンプしたテーブルの制約
--

--
-- テーブルの制約 `calc_snapshots`
--
ALTER TABLE `calc_snapshots`
  ADD CONSTRAINT `fk_snap_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `market_prices`
--
ALTER TABLE `market_prices`
  ADD CONSTRAINT `fk_mkt_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_mkt_species` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `plans`
--
ALTER TABLE `plans`
  ADD CONSTRAINT `fk_plans_ponds` FOREIGN KEY (`pond_id`) REFERENCES `ponds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_plans_region` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`),
  ADD CONSTRAINT `fk_plans_species` FOREIGN KEY (`species_id`) REFERENCES `species` (`id`),
  ADD CONSTRAINT `fk_plans_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `plan_feed_mix`
--
ALTER TABLE `plan_feed_mix`
  ADD CONSTRAINT `fk_mix_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `plan_feed_recipe_items`
--
ALTER TABLE `plan_feed_recipe_items`
  ADD CONSTRAINT `fk_recipe_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `plan_fingerlings`
--
ALTER TABLE `plan_fingerlings`
  ADD CONSTRAINT `fk_pf_plan` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE CASCADE;

--
-- テーブルの制約 `ponds`
--
ALTER TABLE `ponds`
  ADD CONSTRAINT `fk_ponds_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
