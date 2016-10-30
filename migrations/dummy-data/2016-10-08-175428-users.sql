INSERT INTO `users` (`email`, `facebook_id`, `facebook_token`, `registration_type`)
VALUES ('petrkubes1997@gmail.com', '', '','self');
INSERT INTO `users` (`email`, `facebook_id`, `facebook_token`, `registration_type`)
VALUES ('test@gmail.com', '', '', 'self');


INSERT INTO `currencies` (`symbol`)
VALUES ('Kč');
INSERT INTO `currencies` (`symbol`)
VALUES ('$');
INSERT INTO `currencies` (`symbol`)
VALUES ('€');
INSERT INTO `currencies` (`symbol`)
VALUES ('£');

INSERT INTO `debts` (`creditor_id`, `debtor_id`, `currency_id`, `amount`, `note`, `paid`, `deleted`, `created_at`)
VALUES ('1', '2', '1', '50', 'Ahoj', '0', '0', now());
