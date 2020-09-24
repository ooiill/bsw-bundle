CREATE TABLE `{TABLE_NAME}` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `command` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `condition` json DEFAULT NULL,
  `resource_need` tinyint unsigned NOT NULL DEFAULT '1',
  `done_percent` float unsigned NOT NULL DEFAULT '0',
  `telegram_receiver` varchar(512) NOT NULL DEFAULT '',
  `cron_type` tinyint(3) NOT NULL DEFAULT '1',
  `cron_date_format` varchar(32) NOT NULL DEFAULT 'd-H:i',
  `cron_date_value` varchar(32) NOT NULL DEFAULT '01-00:00',
  `cron_reuse` tinyint(3) NOT NULL DEFAULT '0',
  `file_attachment_id` int(10) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `state` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
