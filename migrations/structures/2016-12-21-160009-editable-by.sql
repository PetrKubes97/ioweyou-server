ALTER TABLE `debts`
ADD `manager_id` int(11) unsigned NULL AFTER `thing_name`,
ADD FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`);
