-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 19 mai 2024 à 20:14
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `fleet`
--

-- --------------------------------------------------------

--
-- Structure de la table `budgetallocation`
--

CREATE TABLE `budgetallocation` (
  `allocation_id` int(11) NOT NULL,
  `mission_id` int(11) NOT NULL,
  `budget_amount` decimal(10,2) NOT NULL,
  `expenses_budget` decimal(10,2) NOT NULL DEFAULT 0.00,
  `allocation_date` date NOT NULL,
  `allocated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `budgetallocation`
--

INSERT INTO `budgetallocation` (`allocation_id`, `mission_id`, `budget_amount`, `expenses_budget`, `allocation_date`, `allocated_by`) VALUES
(1, 1, 1000.00, 200.00, '2024-05-05', 2),
(2, 2, 1500.00, 500.00, '2024-05-06', 2);

-- --------------------------------------------------------

--
-- Structure de la table `communication`
--

CREATE TABLE `communication` (
  `message_id` int(11) NOT NULL,
  `mission_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `communication`
--

INSERT INTO `communication` (`message_id`, `mission_id`, `driver_id`, `message`, `timestamp`) VALUES
(1, 1, 1, 'Loading completed, starting delivery', '2024-05-15 07:00:00'),
(2, 2, 2, 'Picked up supplies, heading back', '2024-05-15 08:30:00'),
(3, NULL, 1, 'this is a test', '2024-05-18 22:58:01'),
(4, NULL, 2, 'rertert', '2024-05-19 16:47:06');

-- --------------------------------------------------------

--
-- Structure de la table `driver`
--

CREATE TABLE `driver` (
  `driver_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `license_type` enum('B','C1','C2','C3','E','F','G') NOT NULL,
  `license_number` varchar(100) NOT NULL,
  `vehicle_assigned` varchar(255) DEFAULT NULL,
  `driver_status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `driver`
--

INSERT INTO `driver` (`driver_id`, `user_id`, `license_type`, `license_number`, `vehicle_assigned`, `driver_status`) VALUES
(1, 1, 'C1', 'DL123456789', 'Ford Transit', 'active'),
(2, 2, 'C1', 'DL987654321', 'Mercedes Sprinter', 'active'),
(3, 16, 'B', 'D1234561', NULL, 'active'),
(4, 17, 'C1', 'D1234562', NULL, 'active'),
(5, 18, 'C2', 'D1234563', NULL, 'active'),
(6, 19, 'B', 'D1234564', NULL, 'active'),
(7, 20, 'C1', 'D1234565', NULL, 'active'),
(8, 21, 'C2', 'D1234566', NULL, 'active'),
(9, 22, 'B', 'D1234567', NULL, 'active');

-- --------------------------------------------------------

--
-- Structure de la table `eventreport`
--

CREATE TABLE `eventreport` (
  `report_id` int(11) NOT NULL,
  `event_type` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date` date NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `eventreport`
--

INSERT INTO `eventreport` (`report_id`, `event_type`, `description`, `date`, `user_id`) VALUES
(1, 'Login', 'User logged in successfully', '2024-05-15', 1),
(2, 'Logout', 'User logged out successfully', '2024-05-15', 1),
(3, 'Maintenance', 'tyeslkjdsa dsa', '2024-05-18', 1);

-- --------------------------------------------------------

--
-- Structure de la table `financialreports`
--

CREATE TABLE `financialreports` (
  `report_id` int(11) NOT NULL,
  `report_type` enum('income-statement','balance-sheet','cash-flow') NOT NULL,
  `generated_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `generated_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `financialreports`
--

INSERT INTO `financialreports` (`report_id`, `report_type`, `generated_date`, `generated_by`) VALUES
(1, 'income-statement', '2024-05-15 09:00:00', 2),
(2, 'balance-sheet', '2024-05-15 10:00:00', 2);

-- --------------------------------------------------------

--
-- Structure de la table `fuelexpenses`
--

CREATE TABLE `fuelexpenses` (
  `expense_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `expense_type` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `fuel_type` enum('diesel','gasoline','electric') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fuelexpenses`
--

INSERT INTO `fuelexpenses` (`expense_id`, `vehicle_id`, `expense_type`, `amount`, `date`, `fuel_type`) VALUES
(1, 1, 'Fuel Purchase', 50.00, '2024-05-01', 'diesel'),
(2, 2, 'Fuel Purchase', 70.00, '2024-05-10', 'gasoline');

-- --------------------------------------------------------

--
-- Structure de la table `maintenance`
--

CREATE TABLE `maintenance` (
  `maintenance_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `maintenance_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','completed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `maintenance`
--

INSERT INTO `maintenance` (`maintenance_id`, `vehicle_id`, `maintenance_date`, `description`, `status`) VALUES
(1, 1, '2024-05-01', 'Oil change and tire rotation', 'completed'),
(2, 2, '2024-05-10', 'Brake inspection and replacement', 'pending');

-- --------------------------------------------------------

--
-- Structure de la table `mission`
--

CREATE TABLE `mission` (
  `mission_id` int(11) NOT NULL,
  `mission_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('assigned','in_progress','completed','pending') NOT NULL,
  `progress` int(11) DEFAULT 0,
  `current_task` varchar(255) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `mission`
--

INSERT INTO `mission` (`mission_id`, `mission_name`, `description`, `status`, `progress`, `current_task`, `vehicle_id`, `driver_id`) VALUES
(1, 'test', 'Deliver goods to the main warehouse', 'assigned', 100, 'finished loading ', 1, 1),
(2, 'Pickup from Supplier', 'Pick up supplies from the vendor', 'in_progress', 50, 'On the way', 2, 2),
(6, 'Mission 1', 'Deliver goods to location A', 'assigned', 0, NULL, 7, 4),
(7, 'Mission 2', 'Deliver goods to location B', 'pending', 0, NULL, NULL, NULL),
(8, 'Mission 3', 'Pick up goods from location C', 'pending', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `position` enum('Driver','FinanceAdmin','MissionCoordinator','Fleet_Admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`user_id`, `name`, `email`, `password_hash`, `position`) VALUES
(1, 'John Doe', 'john.doe@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Driver'),
(2, 'Jane Smith', 'jane.smith@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'FinanceAdmin'),
(3, 'Tom Brown', 'tom.brown@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'MissionCoordinator'),
(4, 'Lucy White', 'lucy.white@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Fleet_Admin'),
(16, 'Charlie Brown', 'charlie.brown@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Driver'),
(17, 'Diana Prince', 'diana.prince@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Driver'),
(18, 'Bruce Wayne', 'bruce.wayne@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Driver'),
(19, 'Clark Kent', 'clark.kent@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Driver'),
(20, 'Peter Parker', 'peter.parker@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Driver'),
(21, 'Logan Howlett', 'logan.howlett@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Driver'),
(22, 'Natasha Romanoff', 'natasha.romanoff@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'Driver'),
(23, 'Tony Stark', 'tony.stark@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'MissionCoordinator'),
(24, 'Steve Rogers', 'steve.rogers@example.com', '$2y$10$LCSgZbbfXqkFx1OgQJKJyeSUOQSpJBIKKfJlqvBSec/nL/Jr1amQC', 'MissionCoordinator');

-- --------------------------------------------------------

--
-- Structure de la table `vehicle`
--

CREATE TABLE `vehicle` (
  `vehicle_id` int(11) NOT NULL,
  `vehicle_type` enum('car','truck','van','bus','SUV','Electric') NOT NULL,
  `model` varchar(100) NOT NULL,
  `year` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `assigned_driver_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicle`
--

INSERT INTO `vehicle` (`vehicle_id`, `vehicle_type`, `model`, `year`, `status`, `assigned_driver_id`) VALUES
(1, 'van', 'Ford Transit', 2018, 'active', 1),
(2, 'truck', 'Mercedes Sprinter', 2020, 'active', 2),
(3, 'car', 'Toyota Corolla', 2019, 'inactive', NULL),
(4, 'SUV', 'Honda CR-V', 2021, 'inactive', NULL),
(5, 'Electric', 'Tesla Model X', 2022, 'inactive', NULL),
(6, 'truck', 'Ford F-150', 2019, 'active', NULL),
(7, 'van', 'Mercedes Sprinter', 2020, 'active', NULL),
(8, 'car', 'Toyota Corolla', 2018, 'active', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `vehiclereport`
--

CREATE TABLE `vehiclereport` (
  `report_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `maintenance_status` enum('pending','completed','all') NOT NULL,
  `fuel_type` enum('diesel','gasoline','electric','all') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehiclereport`
--

INSERT INTO `vehiclereport` (`report_id`, `vehicle_id`, `start_date`, `end_date`, `maintenance_status`, `fuel_type`) VALUES
(1, 1, '2024-01-01', '2024-05-15', 'all', 'all'),
(2, 2, '2024-03-01', '2024-05-15', 'pending', 'diesel');

-- --------------------------------------------------------

--
-- Structure de la table `vehicle_expenses`
--

CREATE TABLE `vehicle_expenses` (
  `expense_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `expense_type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `category` enum('Fuel','Maintenance','Insurance','Other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `vehicle_expenses`
--

INSERT INTO `vehicle_expenses` (`expense_id`, `vehicle_id`, `expense_type`, `amount`, `expense_date`, `category`) VALUES
(1, 1, 'Fuel', 50.00, '2024-05-01', 'Fuel'),
(2, 2, 'Brake Pads', 200.00, '2024-05-10', 'Maintenance');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `budgetallocation`
--
ALTER TABLE `budgetallocation`
  ADD PRIMARY KEY (`allocation_id`),
  ADD KEY `mission_id` (`mission_id`),
  ADD KEY `allocated_by` (`allocated_by`);

--
-- Index pour la table `communication`
--
ALTER TABLE `communication`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `mission_id` (`mission_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Index pour la table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`driver_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `eventreport`
--
ALTER TABLE `eventreport`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `financialreports`
--
ALTER TABLE `financialreports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `generated_by` (`generated_by`);

--
-- Index pour la table `fuelexpenses`
--
ALTER TABLE `fuelexpenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Index pour la table `maintenance`
--
ALTER TABLE `maintenance`
  ADD PRIMARY KEY (`maintenance_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Index pour la table `mission`
--
ALTER TABLE `mission`
  ADD PRIMARY KEY (`mission_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `vehicle`
--
ALTER TABLE `vehicle`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD KEY `assigned_driver_id` (`assigned_driver_id`);

--
-- Index pour la table `vehiclereport`
--
ALTER TABLE `vehiclereport`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Index pour la table `vehicle_expenses`
--
ALTER TABLE `vehicle_expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `budgetallocation`
--
ALTER TABLE `budgetallocation`
  MODIFY `allocation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `communication`
--
ALTER TABLE `communication`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `driver`
--
ALTER TABLE `driver`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `eventreport`
--
ALTER TABLE `eventreport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `financialreports`
--
ALTER TABLE `financialreports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `fuelexpenses`
--
ALTER TABLE `fuelexpenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `maintenance`
--
ALTER TABLE `maintenance`
  MODIFY `maintenance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `mission`
--
ALTER TABLE `mission`
  MODIFY `mission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `vehicle`
--
ALTER TABLE `vehicle`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `vehiclereport`
--
ALTER TABLE `vehiclereport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `vehicle_expenses`
--
ALTER TABLE `vehicle_expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `budgetallocation`
--
ALTER TABLE `budgetallocation`
  ADD CONSTRAINT `budgetallocation_ibfk_1` FOREIGN KEY (`mission_id`) REFERENCES `mission` (`mission_id`),
  ADD CONSTRAINT `budgetallocation_ibfk_2` FOREIGN KEY (`allocated_by`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `communication`
--
ALTER TABLE `communication`
  ADD CONSTRAINT `communication_ibfk_1` FOREIGN KEY (`mission_id`) REFERENCES `mission` (`mission_id`),
  ADD CONSTRAINT `communication_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `driver`
--
ALTER TABLE `driver`
  ADD CONSTRAINT `driver_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `eventreport`
--
ALTER TABLE `eventreport`
  ADD CONSTRAINT `eventreport_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `financialreports`
--
ALTER TABLE `financialreports`
  ADD CONSTRAINT `financialreports_ibfk_1` FOREIGN KEY (`generated_by`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `fuelexpenses`
--
ALTER TABLE `fuelexpenses`
  ADD CONSTRAINT `fuelexpenses_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`);

--
-- Contraintes pour la table `maintenance`
--
ALTER TABLE `maintenance`
  ADD CONSTRAINT `maintenance_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`);

--
-- Contraintes pour la table `mission`
--
ALTER TABLE `mission`
  ADD CONSTRAINT `mission_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`),
  ADD CONSTRAINT `mission_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `vehicle`
--
ALTER TABLE `vehicle`
  ADD CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`assigned_driver_id`) REFERENCES `user` (`user_id`);

--
-- Contraintes pour la table `vehiclereport`
--
ALTER TABLE `vehiclereport`
  ADD CONSTRAINT `vehiclereport_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`);

--
-- Contraintes pour la table `vehicle_expenses`
--
ALTER TABLE `vehicle_expenses`
  ADD CONSTRAINT `vehicle_expenses_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
