CREATE TABLE `{TABLE_NAME}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platform` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sha1` char(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint(10) unsigned NOT NULL,
  `deep` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `deep` (`deep`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
