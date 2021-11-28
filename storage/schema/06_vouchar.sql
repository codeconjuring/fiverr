ALTER TABLE `transaction_types` CHANGE `name` `name` ENUM('Deposit','Withdrawal','Transferred','Received','Exchange_From','Exchange_To','Request_From','Request_To','Payment_Sent','Payment_Received','Crypto_Sent','Crypto_Received','Order_Product','Order_Received','Referral_Award','Voucher_Created','Voucher_Activated') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;


INSERT INTO `transaction_types` (`id`, `name`) VALUES ('18', 'Voucher_Created'), ('19', 'Voucher_Activated');

CREATE TABLE IF NOT EXISTS `vouchers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `activator_id` int(10) UNSIGNED DEFAULT NULL,
  `currency_id` int(10) UNSIGNED DEFAULT NULL,
  `uuid` varchar(13) DEFAULT NULL COMMENT 'Unique ID (For Each Voucher)',
  `charge_percentage` double(10,2) DEFAULT '0.00',
  `charge_fixed` double(10,2) DEFAULT '0.00',
  `amount` double(10,2) DEFAULT '0.00',
  `code` varchar(50) DEFAULT NULL,
  `redeemed` enum('No','Yes') NOT NULL DEFAULT 'No',
  `status` enum('Pending','Success','Refund','Blocked') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `vouchers_user_id_index` (`user_id`),
  KEY `vouchers_activator_id_index` (`activator_id`),
  KEY `vouchers_currency_id_index` (`currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


ALTER TABLE `vouchers`
  ADD CONSTRAINT `vouchers_activator_id_foreign` FOREIGN KEY (`activator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vouchers_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `vouchers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  

INSERT INTO `permissions` (`id`, `group`, `name`, `display_name`, `description`, `user_type`, `created_at`, `updated_at`) VALUES
('204', 'Voucher', 'view_voucher', 'View Voucher', 'View Voucher', 'Admin', '2018-07-07 00:48:12', '2018-07-07 00:48:12'),
('205', 'Voucher', 'add_voucher', NULL, NULL, 'Admin', '2018-07-19 02:23:45', '2018-07-07 00:48:12'),
('206', 'Voucher', 'edit_voucher', 'Edit Voucher', 'Edit Voucher', 'Admin', '2018-07-07 00:48:12', '2018-07-07 00:48:12'),
('207', 'Voucher', 'delete_voucher', NULL, NULL, 'Admin', '2018-07-19 02:23:45', '2018-07-07 00:48:12'),
('208', 'Voucher', 'manage_voucher', 'Manage Voucher', 'Manage Voucher', 'User', '2018-07-19 02:23:50', '2018-07-19 02:23:50');


INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES
(1, 204),
(1, 205),
(1, 206),
(1, 207),
(3, 208);



INSERT INTO `email_templates` (`language_id`, `temp_id`, `subject`, `body`, `lang`, `type`) VALUES
	(1, 41, 'Notice of Voucher Activation!', 'Hi {user_id},\r\n                                <br><br>\r\n                                Voucher # {uuid} has been activated by {activator_id}.\r\n                                <br><br><b><u><i>\r\n                                Here’s a brief overview of the Voucher Activation:</i></u></b>\r\n                                <br><br>Voucher # {uuid} was activated at {created_at}.\r\n                                <br><br><b><u>Amount:</u></b> {amount}\r\n                                <br><br><b><u>Code:</u></b> {code}\r\n                                <br><br>If you have any questions, please feel free to reply to this email.\r\n                                <br><br>Regards,\r\n                                <br><b>{soft_name}</b>\r\n                                ', 'en', 'email'),
	(2, 41, '', '', 'ar', 'email'),
	(3, 41, '', '', 'fr', 'email'),
	(4, 41, '', '', 'pt', 'email'),
	(5, 41, '', '', 'ru', 'email'),
	(6, 41, '', '', 'es', 'email'),
	(7, 41, '', '', 'tr', 'email'),
	(8, 41, '', '', 'ch', 'email'),
	(1, 42, 'Status of Transaction #{uuid} has been updated!', 'Hi {activator_id},\r\n\r\n                                <br><br><b>\r\n                                Transaction of Voucher #{uuid} has been updated to {status} by system administrator!</b>\r\n\r\n                                <br><br>\r\n                                <u><i>Voucher Code:</i></u> {code}\r\n\r\n                                <br><br>\r\n                                {amount} is {added/subtracted} {from/to} your account.\r\n\r\n                                <br><br>If you have any questions, please feel free to reply to this email.\r\n\r\n                                <br><br>Regards,\r\n                                <br><b>{soft_name}</b>\r\n                                ', 'en', 'email'),
	(2, 42, '', '', 'ar', 'email'),
	(3, 42, '', '', 'fr', 'email'),
	(4, 42, '', '', 'pt', 'email'),
	(5, 42, '', '', 'ru', 'email'),
	(6, 42, '', '', 'es', 'email'),
	(7, 42, '', '', 'tr', 'email'),
	(8, 42, '', '', 'ch', 'email');

	INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES
	('vouchers', 'Voucher', 'Voucher', ''),
	('voucher/add', 'Voucher Add', 'Voucher Add', ''),
	('voucher/activate_code', 'Voucher Activation', 'Voucher Activation', ''),
	('voucher/store', 'Voucher', 'Voucher', ''),
	('voucher/activated', 'Voucher', 'Voucher', '');
