CREATE TABLE `{TABLE_NAME}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' NOT NULL,
  `value` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `allow_client_pull` tinyint(3) unsigned NOT NULL DEFAULT '0' NOT NULL,
  `remark` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
