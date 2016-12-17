ALTER TABLE `actions`
CHANGE `type` `type` enum('debt_new','debt_update','debt_delete','debt_restore','debt_mark_as_paid','debt_mark_as_unpaid','registered','error', 'logged_in') COLLATE 'utf8_bin' NOT NULL AFTER `id`;

