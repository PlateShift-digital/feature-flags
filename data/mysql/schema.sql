CREATE TABLE `plate_shift_feature_flag` (
  `identifier` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `scope` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `feature_identifier_scope` (`identifier`,`scope`),
  KEY `feature_identifier_scope_index` (`identifier`,`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
