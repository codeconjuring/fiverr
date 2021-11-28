INSERT INTO `payment_methods` (`name`, `status`) VALUES ('CIB', 'Active');

ALTER TABLE `currencies` ADD `number` VARCHAR(11) NULL DEFAULT NULL AFTER `code`;

INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('deposit/cib-deposit-success', 'Success', 'Success', '');