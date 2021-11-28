-- Referral settings
  INSERT INTO `settings` (`name`, `value`, `type`) VALUES ('min_referral_amount', '300', 'referral');
  INSERT INTO `settings` (`name`, `value`, `type`) VALUES ('is_referral_enabled', 'yes', 'referral');
  INSERT INTO `settings` (`name`, `value`, `type`) VALUES ('referral_currency', '1', 'referral');


  -- referral_levels
  CREATE TABLE `referral_levels` (
  `id` int(10) UNSIGNED NOT NULL,
  `currency_id` int(10) UNSIGNED DEFAULT NULL,
  `level` varchar(255) NOT NULL,
  `amount` decimal(20,8) DEFAULT '0.00000000',
  `priority` int(11) NOT NULL,
  `status` char(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `referral_levels`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referral_levels_currency_id_index` (`currency_id`),
  ADD KEY `referral_levels_level_index` (`level`),
  ADD KEY `referral_levels_priority_index` (`priority`);
  ALTER TABLE `referral_levels`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  ALTER TABLE `referral_levels`
  ADD CONSTRAINT `referral_levels_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

  ALTER TABLE `referral_levels` CHANGE `priority` `priority` INT(11) NOT NULL DEFAULT '1';


  -- referral_codes
  CREATE TABLE `referral_codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `status` char(6) NOT NULL,
  `valid_from` timestamp NULL DEFAULT NULL,
  `valid_to` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `referral_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referral_codes_user_id_index` (`user_id`);
  ALTER TABLE `referral_codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  ALTER TABLE `referral_codes`
  ADD CONSTRAINT `referral_codes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

  -- referrals
  CREATE TABLE `referrals` (
  `id` int(10) UNSIGNED NOT NULL,
  `referred_by` int(10) UNSIGNED DEFAULT NULL,
  `referred_to` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referrals_referred_by_index` (`referred_by`),
  ADD KEY `referrals_referred_to_index` (`referred_to`);
  ALTER TABLE `referrals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_referred_by_foreign` FOREIGN KEY (`referred_by`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `referrals_referred_to_foreign` FOREIGN KEY (`referred_to`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

  -- referral_awards
  CREATE TABLE `referral_awards` (
  `id` int(10) UNSIGNED NOT NULL,
  `referral_id` int(10) UNSIGNED DEFAULT NULL,
  `referral_level_id` int(10) UNSIGNED DEFAULT NULL,
  `referral_code_id` int(10) UNSIGNED DEFAULT NULL,
  `awarded_user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'awarded user ID',
  `referred_to` int(10) UNSIGNED DEFAULT NULL COMMENT 'referred to user ID',
  `awarded_amount` decimal(20,8) DEFAULT '0.00000000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `referral_awards`
  ADD PRIMARY KEY (`id`),
  ADD KEY `referral_awards_referral_level_id_index` (`referral_level_id`),
  ADD KEY `referral_awards_referral_code_id_index` (`referral_code_id`),
  ADD KEY `referral_awards_awarded_user_id_index` (`awarded_user_id`),
  ADD KEY `referral_awards_referred_to_index` (`referred_to`),
  ADD KEY `referral_awards_referral_id_index` (`referral_id`);

  ALTER TABLE `referral_awards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

  ALTER TABLE `referral_awards`
  ADD CONSTRAINT `referral_awards_awarded_user_id_foreign` FOREIGN KEY (`awarded_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `referral_awards_referral_code_id_foreign` FOREIGN KEY (`referral_code_id`) REFERENCES `referral_codes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `referral_awards_referral_level_id_foreign` FOREIGN KEY (`referral_level_id`) REFERENCES `referral_levels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `referral_awards_referred_id_foreign` FOREIGN KEY (`referral_id`) REFERENCES `referrals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `referral_awards_referred_to_foreign` FOREIGN KEY (`referred_to`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

  -- metas

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('refer-friend', 'Referr a friend', 'Referr a friend', '');

  -- transaction_types

  ALTER TABLE `transaction_types` CHANGE `name` `name` ENUM('Deposit','Withdrawal','Transferred','Received','Exchange_From','Exchange_To','Request_From','Request_To','Payment_Sent','Payment_Received','Crypto_Sent','Crypto_Received', 'Order_Product', 'Order_Received', 'Referral_Award') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

  INSERT INTO `transaction_types` (`id`, `name`) VALUES ('17', 'Referral_Award');

  -- templates - both Email & SMS - for referral award

  INSERT INTO `email_templates` (`temp_id`, `subject`, `body`, `lang`, `type`, `language_id`) VALUES
  (20, 'Notice of Referral Award!', 'Hi {referred_by_user},\r\n                                <br><br>\r\n                                Congratulations! <b>You have been awarded referral amount of {amount}</b>.\r\n                                <br><br>If you have any questions, please feel free to reply to this email.\r\n                                <br><br>Regards,\r\n                                <br><b>{soft_name}</b>\r\n                                ', 'en', 'email', 1),
  (20, '', '', 'ar', 'email', 2),
  (20, '', '', 'fr', 'email', 3),
  (20, '', '', 'pt', 'email', 4),
  (20, '', '', 'ru', 'email', 5),
  (20, '', '', 'es', 'email', 6),
  (20, '', '', 'tr', 'email', 7),
  (20, '', '', 'ch', 'email', 8),
  (19, 'Notice of Referral Award!', 'Hi {referred_by_user},\n                                <br><br>\n                                Congratulations! <b>You have been awarded referral amount of {amount}</b>.\n                                <br><br>\n                                Regards,\n                                <br><b>{soft_name}</b>\n                                ', 'en', 'sms', 1),
  (19, '', '', 'ar', 'sms', 2),
  (19, '', '', 'fr', 'sms', 3),
  (19, '', '', 'pt', 'sms', 4),
  (19, '', '', 'ru', 'sms', 5),
  (19, '', '', 'es', 'sms', 6),
  (19, '', '', 'tr', 'sms', 7),
  (19, '', '', 'ch', 'sms', 8);

  -- permissions

  INSERT INTO `permissions` (`id`, `group`, `name`, `display_name`, `description`, `user_type`, `created_at`, `updated_at`) VALUES
  (196, 'Referral Settings', 'view_referral_settings', 'View Referral Settings', 'View Referral Settings', 'Admin', '2019-08-05 03:33:47', '2019-08-05 03:33:47'),
  (197, 'Referral Settings', 'add_referral_settings', NULL, NULL, 'Admin', '2019-08-05 03:33:47', '2019-08-05 03:33:47'),
  (198, 'Referral Settings', 'edit_referral_settings', 'Edit Referral Settings', 'Edit Referral Settings', 'Admin', '2019-08-05 03:33:47', '2019-08-05 03:33:47'),
  (199, 'Referral Settings', 'delete_referral_settings', NULL, NULL, 'Admin', '2019-08-05 03:33:47', '2019-08-05 03:33:47'),
  (200, 'Referral Award', 'view_referral_award', 'View Referral Award', 'View Referral Award', 'Admin', '2019-08-05 03:33:47', '2019-08-05 03:33:47'),
  (201, 'Referral Award', 'add_referral_award', NULL, NULL, 'Admin', '2019-08-05 03:33:47', '2019-08-05 03:33:47'),
  (202, 'Referral Award', 'edit_referral_award', NULL, NULL, 'Admin', '2019-08-05 03:33:47', '2019-08-05 03:33:47'),
  (203, 'Referral Award', 'delete_referral_award', NULL, NULL, 'Admin', '2019-08-05 03:33:47', '2019-08-05 03:33:47');

  -- permission_role
  INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
  (196, 1),
  (197, 1),
  (198, 1),
  (199, 1),
  (200, 1),
  (201, 1),
  (202, 1),
  (203, 1);


  INSERT INTO `referral_levels` (`currency_id`, `level`, `amount`, `priority`, `status`) VALUES
  (1, 'Level 1', '2.00000000', 1, 'Active'),
  (1, 'Level 2', '2.50000000', 2, 'Active'),
  (1, 'Level 3', '3.00000000', 3, 'Active'),
  (1, 'Level 4', '3.50000000', 4, 'Active'),
  (1, 'Level 5', '4.00000000', 5, 'Active');