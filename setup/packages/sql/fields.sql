

--
-- Table structure for table `#__tz_portfolio_plus_fieldgroups`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_fieldgroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `name` varchar(255) NOT NULL DEFAULT '',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `field_ordering_type` tinyint(4) NOT NULL DEFAULT '0',
  `description` text NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `created_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `access` INT(10) UNSIGNED NOT NULL DEFAULT '1',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_fields`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL DEFAULT '',
  `value` text NULL,
  `default_value` text NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `advanced_search` tinyint(4) NOT NULL DEFAULT '0',
  `list_view` tinyint(4) NOT NULL DEFAULT '0',
  `detail_view` tinyint(4) NOT NULL DEFAULT '1',
  `params` text NULL,
  `description` text NULL,
  `access` INT(10) UNSIGNED NOT NULL DEFAULT '1',
  `asset_id` INT UNSIGNED NOT NULL DEFAULT '1',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_field_content_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_field_content_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contentid` int(11) NOT NULL DEFAULT '0',
  `fieldsid` int(11) NOT NULL DEFAULT '0',
  `value` text NULL,
  `images` text NULL,
  `imagetitle` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_field_fieldgroup_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_field_fieldgroup_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fieldsid` int(11) NOT NULL DEFAULT '0',
  `groupid` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------