--
-- Store Module
--
  -- Permissions
  INSERT INTO `permissions` (`id`, `group`, `name`, `display_name`, `description`, `user_type`, `created_at`, `updated_at`) VALUES
    (177, 'Store', 'view_store', 'View Store', 'View Store', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (178, 'Store', 'add_store', 'Add Store', 'Add Store', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (179, 'Store', 'edit_store', 'Edit Store', 'Edit Store', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (180, 'Store', 'delete_store', 'Delete Store', 'Delete Store', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (181, 'Product Category', 'view_product_category', 'View Product Category', 'View Product Category', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (182, 'Product Category', 'add_product_category', 'Add Product Category', 'Add Product Category', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (183, 'Product Category', 'edit_product_category', 'Edit Product Category', 'Edit Product Category', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (184, 'Product Category', 'delete_product_category', 'Delete Product Category', 'Delete Product Category', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (185, 'Product', 'view_product', 'View Product', 'View Product', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (186, 'Product', 'add_product', 'Add Product', 'Add Product', 'Admin', '2019-10-19 07:28:23', '2019-10-19 07:28:23'),
    (187, 'Product', 'edit_product', 'Edit Product', 'Edit Product', 'Admin', '2019-10-19 07:28:24', '2019-10-19 07:28:24'),
    (188, 'Product', 'delete_product', 'Delete Product', 'Delete Product', 'Admin', '2019-10-19 07:28:24', '2019-10-19 07:28:24'),
    (189, 'Address Book', 'view_Address_book', 'View Address Book', 'View Address Book', 'Admin', '2019-10-19 07:28:24', '2019-10-19 07:28:24'),
    (190, 'Address Book', 'add_Address_book', 'Add Address Book', 'Add Address Book', 'Admin', '2019-10-19 07:28:24', '2019-10-19 07:28:24'),
    (191, 'Address Book', 'edit_Address_book', 'Edit Address Book', 'Edit Address Book', 'Admin', '2019-10-19 07:28:24', '2019-10-19 07:28:24'),
    (192, 'Address Book', 'delete_Address_book', 'Delete Address Book', 'Delete Address Book', 'Admin', '2019-10-19 07:28:24', '2019-10-19 07:28:24'),
    (193, 'Store', 'manage_store', 'Manage Store', 'Manage Store', 'User', '2019-10-19 07:28:24', '2019-10-19 07:28:24'),
    (194, 'Product Category', 'manage_product_category', 'Manage Product Category', 'Manage Product Category', 'User', '2019-10-19 07:28:24', '2019-10-19 07:28:24'),
    (195, 'Product', 'manage_product', 'Manage Product', 'Manage Product', 'User', '2019-10-19 07:28:24', '2019-10-19 07:28:24');

  -- Permission Role
  INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES
    (1, 177),
    (1, 178),
    (1, 179),
    (1, 180),
    (1, 181),
    (1, 182),
    (1, 183),
    (1, 184),
    (1, 185),
    (1, 186),
    (1, 187),
    (1, 188),
    (1, 189),
    (1, 190),
    (1, 191),
    (1, 192),
    -- for user
    (2, 193),
    (2, 194),
    (2, 195),
    -- for merchant
    (3, 193),
    (3, 194),
    (3, 195);

  --
  -- Table structure for table `stores`
  --
  CREATE TABLE `stores` (
    `id` int(10) UNSIGNED NOT NULL,
    `store_code` varchar(10) NOT NULL COMMENT 'An auto-generated store code will be for each store. Alphanumeric characters only and must be upper case.',
    `user_id` int(10) UNSIGNED DEFAULT NULL,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `address_line_1` varchar(100) DEFAULT NULL,
    `address_line_2` varchar(100) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `state` varchar(50) DEFAULT NULL,
    `zip` varchar(20) DEFAULT NULL,
    `country` varchar(100) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `website` varchar(255) DEFAULT NULL,
    `photo` varchar(20) DEFAULT NULL,
    `is_verified` tinyint(4) DEFAULT NULL,
    `verified_by` varchar(191) DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'Active',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `stores`
  --
  ALTER TABLE `stores`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `stores_store_code_unique` (`store_code`),
    ADD KEY `stores_user_id_index` (`user_id`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `stores`
  --
  ALTER TABLE `stores`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

  --
  -- Constraints for dumped tables
  --

  --
  -- Constraints for table `stores`
  --
  ALTER TABLE `stores`
    ADD CONSTRAINT `stores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



  -- --------------------------------------------------------

  --
  -- Table structure for table `product_categories`
  --

  CREATE TABLE `product_categories` (
    `id` int(10) UNSIGNED NOT NULL,
    `store_id` int(10) UNSIGNED DEFAULT NULL,
    `name` varchar(100) NOT NULL,
    `slug` varchar(120) NOT NULL,
    `description` varchar(255) DEFAULT NULL,
    `photo` varchar(20) DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'Active',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `product_categories`
  --
  ALTER TABLE `product_categories`
    ADD PRIMARY KEY (`id`),
    ADD KEY `product_categories_store_id_index` (`store_id`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `product_categories`
  --
  ALTER TABLE `product_categories`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

  --
  -- Constraints for dumped tables
  --

  --
  -- Constraints for table `product_categories`
  --
  ALTER TABLE `product_categories`
    ADD CONSTRAINT `product_categories_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



  --
  -- Table structure for table `products`
  --

  CREATE TABLE `products` (
    `id` int(10) UNSIGNED NOT NULL,
    `store_id` int(10) UNSIGNED DEFAULT NULL,
    `product_category_id` int(10) UNSIGNED NOT NULL,
    `currency_id` int(10) UNSIGNED NOT NULL,
    `product_code` varchar(10) DEFAULT NULL COMMENT 'Unique for each product',
    `title` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `photo` varchar(50) DEFAULT NULL,
    `downloadable_file` text NOT NULL COMMENT 'For only product type file',
    `stock` int(11) DEFAULT NULL,
    `is_downloadable` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'For only product type file',
    `is_watermark_pdf` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'For only product type file',
    `price` decimal(8,2) UNSIGNED NOT NULL DEFAULT 0.00,
    `product_delivery_email_message` text NOT NULL COMMENT 'Product delivery email information',
    `code_separator` varchar(255) DEFAULT NULL COMMENT 'For only code/serial based products',
    `added_codes` varchar(255) DEFAULT NULL COMMENT 'For only code/serial base products',
    `codes_purchase_permission` tinyint(4) NOT NULL DEFAULT 0 COMMENT 'If yes then unlimited For only code/serial based products',
    `purchase_limit` varchar(191) DEFAULT NULL COMMENT '-1 for unlimited',
    `affiliate_permission` tinyint(4) NOT NULL DEFAULT 0,
    `affilicate_rate` decimal(8,2) DEFAULT NULL COMMENT 'Rate in percentage (%)',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `products`
  --
  ALTER TABLE `products`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `products_product_code_unique` (`product_code`),
    ADD KEY `products_store_id_index` (`store_id`),
    ADD KEY `products_product_category_id_index` (`product_category_id`),
    ADD KEY `products_currency_id_index` (`currency_id`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `products`
  --
  ALTER TABLE `products`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

  --
  -- Constraints for dumped tables
  --

  --
  -- Constraints for table `products`
  --
  ALTER TABLE `products`
    ADD CONSTRAINT `products_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `products_product_category_id_foreign` FOREIGN KEY (`product_category_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `products_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;



  --
  -- Table structure for table `orders`
  --

  CREATE TABLE `orders` (
    `id` int(10) UNSIGNED NOT NULL,
    `store_id` int(10) UNSIGNED NOT NULL,
    `product_id` int(10) UNSIGNED NOT NULL,
    `user_id` int(10) UNSIGNED NOT NULL,
    `currency_id` int(10) UNSIGNED NOT NULL,
    `order_id` varchar(191) NOT NULL,
    `order_date` date NOT NULL,
    `paid_amount` double(8,2) NOT NULL,
    `status` enum('Complete','Pending') NOT NULL DEFAULT 'Complete',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `orders`
  --
  ALTER TABLE `orders`
    ADD PRIMARY KEY (`id`),
    ADD KEY `orders_store_id_index` (`store_id`),
    ADD KEY `orders_product_id_index` (`product_id`),
    ADD KEY `orders_user_id_index` (`user_id`),
    ADD KEY `orders_currency_id_index` (`currency_id`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `orders`
  --
  ALTER TABLE `orders`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

  --
  -- Constraints for dumped tables
  --

  --
  -- Constraints for table `orders`
  --
  ALTER TABLE `orders`
    ADD CONSTRAINT `orders_currency_id_foreign` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `orders_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `orders_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;




  --
  -- Table structure for table `address_books`
  --

  CREATE TABLE `address_books` (
    `id` int(10) UNSIGNED NOT NULL,
    `user_id` int(10) UNSIGNED DEFAULT NULL,
    `formatted_address` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `address_line_1` varchar(100) DEFAULT NULL,
    `address_line_2` varchar(100) DEFAULT NULL,
    `city` varchar(100) DEFAULT NULL,
    `state` varchar(50) DEFAULT NULL,
    `zip` varchar(20) DEFAULT NULL,
    `country` varchar(100) DEFAULT NULL,
    `longitude` varchar(20) DEFAULT NULL,
    `latitude` varchar(20) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `phone` varchar(50) DEFAULT NULL,
    `fax` varchar(100) DEFAULT NULL,
    `website` varchar(255) DEFAULT NULL,
    `photo` varchar(20) DEFAULT NULL,
    `is_verified` tinyint(4) NOT NULL DEFAULT 0,
    `verified_materials` varchar(255) DEFAULT NULL,
    `address_source` varchar(255) DEFAULT NULL,
    `status` varchar(20) NOT NULL DEFAULT 'Active',
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  --
  -- Indexes for dumped tables
  --

  --
  -- Indexes for table `address_books`
  --
  ALTER TABLE `address_books`
    ADD PRIMARY KEY (`id`),
    ADD KEY `address_books_user_id_index` (`user_id`);

  --
  -- AUTO_INCREMENT for dumped tables
  --

  --
  -- AUTO_INCREMENT for table `address_books`
  --
  ALTER TABLE `address_books`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

  --
  -- Constraints for dumped tables
  --

  --
  -- Constraints for table `address_books`
  --
  ALTER TABLE `address_books`
    ADD CONSTRAINT `address_books_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;




  -- Transaction Types

  ALTER TABLE `transaction_types` CHANGE `name` `name` ENUM('Deposit','Withdrawal','Transferred','Received','Exchange_From','Exchange_To','Request_From','Request_To','Payment_Sent','Payment_Received','Crypto_Sent','Crypto_Received','Order_Product','Order_Received') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
  INSERT INTO `transaction_types` (`id`, `name`) VALUES ('15', 'Order_Product');
  INSERT INTO `transaction_types` (`id`, `name`) VALUES ('16', 'Order_Received');


  -- Metas

  -- Stores

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('stores', 'Stores', 'Store', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('stores/add', 'Add Store', 'Add Store', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('stores/edit/{id}', 'Edit Store', 'Edit Store', '');

  -- Product Categories

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('product-categories', 'Product Categories', 'Product Categories', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('product-categories/add', 'Add Product Category', 'Add Product Category', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('product-categories/edit/{id}', 'Edit Product Category', 'Edit Product Cateogry', '');

  -- Product

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('products', 'Products', 'Products', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('products/add', 'Add Product', 'Add Product', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('products/edit/{id}', 'Edit Product', 'Edit Product', '');


  -- Shop

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('owner-orders', 'My Orders', 'My Orders', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('customer-orders', 'Customers Orders', '', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shop', 'Welcome to Shop', '', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shop/product/{id}', 'Buy Product', 'Buy Product', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shop/product/buy/{id}', 'Buy Confirm', 'Buy Confirm', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shop/product/confirm/{id}', 'Purchase Success', 'Purchase Success', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shop/product-categories/{id}', 'Category Products', 'Category Products', '');


  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shop/search', 'Search Products', 'Search Products', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shop/{id}/{name}', 'User Store', 'User Store', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shop/{name}/{store_id}/{category_id}', 'User Store Category Product', 'User Store Category Product', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shipping-address', 'Shipping Address', 'Shipping Address', '');

  INSERT INTO `metas` (`url`, `title`, `description`, `keywords`) VALUES ('shipping-address/{id}', 'Customer Address', 'Customer Address', '');

