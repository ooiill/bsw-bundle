CREATE TABLE `{TABLE_NAME}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(10) unsigned NOT NULL DEFAULT '0',
  `route_name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `icon` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `javascript` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `json_params` json DEFAULT NULL,
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
