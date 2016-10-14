INSERT INTO `users` (`email`, `facebook_id`, `facebook_token`)
VALUES ('petrkubes1997@gmail.com', '', '');
INSERT INTO `users` (`email`, `facebook_id`, `facebook_token`)
VALUES ('test@gmail.com', '', '');


INSERT INTO `currencies` (`symbol`)
VALUES ('Kč');

INSERT INTO `loans` (`creditor_id`, `debtor_id`, `currency_id`, `amount`, `note`, `paid`, `deleted`, `created_at`)
VALUES ('1', '2', '1', '50', 'Ahoj', '0', '0', now());