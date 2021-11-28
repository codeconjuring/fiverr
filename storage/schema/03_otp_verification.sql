ALTER TABLE `users` ADD `otp_code` VARCHAR(10) NULL DEFAULT NULL AFTER `status`, ADD `otp_verified` TINYINT(1) NULL DEFAULT '1' AFTER `otp_code`;

INSERT INTO `preferences` (`category`, `field`, `value`) VALUES ('preference', 'otp_verification_via', 'phone'), ('preference', 'verification_otp', 'Disabled');

INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('otp-verification', 'Otp Verification', 'Otp Verification', '');