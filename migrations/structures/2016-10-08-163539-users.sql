CREATE TABLE IF NOT EXISTS  `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `email` varchar(255) NOT NULL UNIQUE ,
  `facebook_id` varchar(128) NOT NULL,
  `facebook_token` text NOT NULL
) ENGINE='InnoDB';

CREATE TABLE IF NOT EXISTS `user_x_user` (
  `user1_id` int(11) unsigned NOT NULL,
  `user2_id` int(11) unsigned NOT NULL,
  FOREIGN KEY (`user1_id`) REFERENCES `user` (`id`),
  FOREIGN KEY (`user2_id`) REFERENCES `user` (`id`)
) ENGINE='InnoDB';

CREATE TABLE IF NOT EXISTS `currency` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `symbol` varchar(15) NOT NULL UNIQUE
) ENGINE='InnoDB';

CREATE TABLE IF NOT EXISTS `loan` (
  `id` int(11)  unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `creditor_id` int(11) unsigned NOT NULL,
  `debtor_id` int(11) unsigned NOT NULL,
  `currency` int NOT NULL,
  `amount` int(15) NOT NULL,
  `note` varchar(255) NOT NULL,
  `paid` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`creditor_id`) REFERENCES `user` (`id`),
  FOREIGN KEY (`debtor_id`) REFERENCES `user` (`id`)
) ENGINE='InnoDB';