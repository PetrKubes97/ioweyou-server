ALTER TABLE `debts`
ADD `interval_set_at` datetime NULL AFTER `manager_id`,
ADD `interval_minutes` int unsigned NULL AFTER `interval_set_at`,
ADD `interval_run_at` datetime NULL AFTER `interval_minutes`,
ADD `interval_type` enum('add', 'create') NULL AFTER `interval_run_at`;
