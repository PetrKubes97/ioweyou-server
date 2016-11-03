CREATE TABLE IF NOT EXISTS  `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `api_key` varchar(255) NULL,
  `email` varchar(255) NULL,
  `name` varchar(255),
  `facebook_id` varchar(128) NULL,
  `facebook_token` text NULL,
  `registration_type` ENUM('self', 'auto'),
  `registered_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE='InnoDB';

CREATE TABLE IF NOT EXISTS `currencies` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `symbol` varchar(15) NOT NULL UNIQUE
) ENGINE='InnoDB';

CREATE TABLE IF NOT EXISTS `debts` (
  `id` int(11)  UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `creditor_id` int(11) UNSIGNED NOT NULL,
  `debtor_id` int(11) UNSIGNED NOT NULL,
  `currency_id` int(11) UNSIGNED NOT NULL,
  `amount` int(15) NULL,
  `note` varchar(255) NULL,
  `paid` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `custom_friend_name` varchar(255) NULL,
  `thing_name` VARCHAR(255) NULL,
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`creditor_id`) REFERENCES `users` (`id`),
  CONSTRAINT `loans_ibfk_2` FOREIGN KEY (`debtor_id`) REFERENCES `users` (`id`),
  CONSTRAINT `loans_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`)
) ENGINE='InnoDB';


CREATE TABLE IF NOT EXISTS `friendships` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
  `user1_id` int(11) UNSIGNED NOT NULL,
  `user2_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `friendships_ibfk_1` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`),
  CONSTRAINT `friendships_ibfk_2` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`)
) ENGINE='InnoDB' DEFAULT CHARSET=utf8;


