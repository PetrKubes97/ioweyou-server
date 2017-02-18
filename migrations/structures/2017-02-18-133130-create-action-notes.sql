CREATE TABLE `actions_messages` (
  `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `action_id` int(11) NOT NULL,
  `message` enum(
    'debt_new',
    'debt_marked_as_paid',
    'debt_marked_as_unpaid',
    'debt_deleted',
    'debt_restored',
    'debt_friend_name_changed',
    'debt_amount_changed',
    'debt_currency_changed',
    'debt_thing_name_changed',
    'debt_money_to_thing',
    'debt_thing_to_money',
    'debt_note_changed',
    'debt_creditor_debtor_switched',
    'debt_permission_changed') NOT NULL,
  FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`)
);
