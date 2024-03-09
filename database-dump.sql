-- Adminer 4.8.1 MySQL 8.0.33 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `airports`;
CREATE TABLE `airports` (
                            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                            `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `iata` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `airports` (`id`, `city`, `name`, `iata`, `created_at`, `updated_at`) VALUES
                                                                                      (1,	'Astana',	'Astana',	'NQZ',	'2024-03-09 09:16:23',	'2024-03-09 09:16:23'),
                                                                                      (2,	'Almaty',	'Almaty',	'ALA',	'2024-03-09 09:16:23',	'2024-03-09 09:16:23'),
                                                                                      (3,	'Oral',	'Oral',	'URA',	'2024-03-09 09:16:23',	'2024-03-09 09:16:23');

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
                            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                            `flight_from` bigint unsigned NOT NULL,
                            `flight_back` bigint unsigned NOT NULL,
                            `date_from` date NOT NULL,
                            `date_back` date NOT NULL,
                            `code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            KEY `bookings_flight_from_foreign` (`flight_from`),
                            KEY `bookings_flight_back_foreign` (`flight_back`),
                            CONSTRAINT `bookings_flight_back_foreign` FOREIGN KEY (`flight_back`) REFERENCES `flights` (`id`),
                            CONSTRAINT `bookings_flight_from_foreign` FOREIGN KEY (`flight_from`) REFERENCES `flights` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `flights`;
CREATE TABLE `flights` (
                           `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                           `flight_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `from_id` bigint unsigned NOT NULL,
                           `to_id` bigint unsigned NOT NULL,
                           `time_from` time NOT NULL,
                           `time_to` time NOT NULL,
                           `cost` int NOT NULL,
                           `places_count` int NOT NULL DEFAULT '10',
                           `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           PRIMARY KEY (`id`),
                           KEY `flights_from_id_foreign` (`from_id`),
                           KEY `flights_to_id_foreign` (`to_id`),
                           CONSTRAINT `flights_from_id_foreign` FOREIGN KEY (`from_id`) REFERENCES `airports` (`id`),
                           CONSTRAINT `flights_to_id_foreign` FOREIGN KEY (`to_id`) REFERENCES `airports` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `flights` (`id`, `flight_code`, `from_id`, `to_id`, `time_from`, `time_to`, `cost`, `places_count`, `created_at`, `updated_at`) VALUES
                                                                                                                                                (1,	'F1',	1,	2,	'10:30:00',	'12:45:00',	10000,	188,	'2024-03-09 09:16:23',	'2024-03-09 09:16:23'),
                                                                                                                                                (2,	'F2',	2,	1,	'18:45:00',	'19:30:00',	10000,	188,	'2024-03-09 09:16:23',	'2024-03-09 09:16:23'),
                                                                                                                                                (3,	'F3',	3,	1,	'09:10:00',	'14:20:00',	3000,	188,	'2024-03-09 09:16:23',	'2024-03-09 09:16:23'),
                                                                                                                                                (4,	'F4',	3,	2,	'13:15:00',	'16:40:00',	8000,	188,	'2024-03-09 09:16:23',	'2024-03-09 09:16:23'),
                                                                                                                                                (5,	'F5',	2,	3,	'19:15:00',	'21:40:00',	8000,	188,	'2024-03-09 09:16:23',	'2024-03-09 09:16:23');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
                              `id` int unsigned NOT NULL AUTO_INCREMENT,
                              `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `batch` int NOT NULL,
                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
                                                          (1,	'1_create_users_table',	1),
                                                          (2,	'2019_12_14_000001_create_personal_access_tokens_table',	1),
                                                          (3,	'2_create_airports_table',	1),
                                                          (4,	'3_create_flights_table',	1),
                                                          (5,	'4_create_bookings_table',	1),
                                                          (6,	'5_create_passengers_table',	1);

DROP TABLE IF EXISTS `passengers`;
CREATE TABLE `passengers` (
                              `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                              `booking_id` bigint unsigned NOT NULL,
                              `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `birth_date` date NOT NULL,
                              `document_number` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
                              `place_from` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `place_back` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                              `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                              PRIMARY KEY (`id`),
                              KEY `passengers_booking_id_foreign` (`booking_id`),
                              CONSTRAINT `passengers_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
                                          `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                                          `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `tokenable_id` bigint unsigned NOT NULL,
                                          `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
                                          `abilities` text COLLATE utf8mb4_unicode_ci,
                                          `last_used_at` timestamp NULL DEFAULT NULL,
                                          `expires_at` timestamp NULL DEFAULT NULL,
                                          `created_at` timestamp NULL DEFAULT NULL,
                                          `updated_at` timestamp NULL DEFAULT NULL,
                                          PRIMARY KEY (`id`),
                                          UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
                                          KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                         `id` bigint unsigned NOT NULL AUTO_INCREMENT,
                         `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `document_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                         `api_token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                         `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2024-03-09 09:16:27
