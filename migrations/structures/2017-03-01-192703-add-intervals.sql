ALTER TABLE `debts`
ADD `interval_minutes` int unsigned NULL AFTER `manager_id`,
ADD `interval_run_at` datetime NULL AFTER `interval_minutes`,
ADD `interval_type` enum('add', 'create') NULL AFTER `interval_run_at`;
