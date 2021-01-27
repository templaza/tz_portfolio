
--
-- Table structure for table `#__tz_portfolio_plus_categories`
--
CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `images` text NULL,
  `template_id` int(10) unsigned NOT NULL DEFAULT '0',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `params` text NULL,
  `metadesc` varchar(1024) NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `cat_idx` (`extension`,`published`,`access`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_path` (`path`),
  KEY `idx_left_right` (`lft`,`rgt`),
  KEY `idx_alias` (`alias`),
  KEY `idx_language` (`language`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Dumping data for table `#__tz_portfolio_plus_categories`
--
INSERT IGNORE INTO `#__tz_portfolio_plus_categories` (`id`, `groupid`, `images`, `template_id`, `asset_id`, `parent_id`, `lft`, `rgt`, `level`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `metadesc`, `metakey`, `metadata`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hits`, `language`, `version`) VALUES
(1, 0, '', 0, 0, 0, 0, 3, 0, '', 'system', 'ROOT', 'root', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{}', '', '', '', 0, '2011-01-01 00:00:01', 0, '0000-00-00 00:00:00', 0, '*', 1),
(2, 0, '', 0, 0, 1, 1, 2, 1, 'uncategorised', 'com_tz_portfolio_plus', 'Uncategorised', 'uncategorised', '', '', 1, 0, '0000-00-00 00:00:00', 1, '{"inheritFrom":"0","category_layout":"","image":"","show_cat_title":"1","cat_link_titles":"1","show_cat_intro":"1","show_cat_category":"0","cat_link_category":"1","show_cat_parent_category":"0","cat_link_parent_category":"1","show_cat_author":"0","cat_link_author":"1","show_cat_create_date":"0","show_cat_modify_date":"0","show_cat_publish_date":"0","show_cat_readmore":"1","show_cat_hits":"0","show_cat_tags":"0","show_cat_icons":"1","show_cat_print_icon":"0","show_cat_email_icon":"0","show_icons":"1","show_print_icon":"1","show_email_icon":"1","show_noauth":"0","link_category":"1","link_parent_category":"1","show_gender_user":"1","show_email_user":"1","show_url_user":"1","show_description_user":"1","show_related_article":"1","related_limit":"5","show_related_heading":"1","related_heading":"","show_related_title":"1","show_related_featured":"1","related_orderby":"rdate","mt_show_cat_image_hover":"","mt_cat_image_size":"","mt_image_size":"","mt_show_image_hover":"","mt_image_use_cloud":"","mt_image_related_show_image":"","mt_image_related_size":"","show_cat_vote":""}', '', '', '{"author":"","robots":""}', 0, '2015-12-12 14:42:28', 0, '2015-12-12 14:42:28', 0, '*', 1);

--
-- Table structure for table `#__tz_portfolio_plus_content`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `introtext` mediumtext NULL,
  `fulltext` mediumtext NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `status` TINYINT(4) NOT NULL DEFAULT '0' COMMENT 'Store old state to restore state',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text NULL,
  `urls` text NULL,
  `attribs` text NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text NULL,
  `metadesc` text NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `metadata` text NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
  `language` char(7) NOT NULL DEFAULT '' COMMENT 'The language code for the article.',
  `xreference` varchar(50) NOT NULL DEFAULT '' COMMENT 'A reference to enable linkages to external data sets.',
  `type` varchar(25) NOT NULL DEFAULT '',
  `media` text NULL,
  `template_id` int(11) NOT NULL DEFAULT '0',
  `priority` INT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_access` (`access`),
  KEY `idx_checkout` (`checked_out`),
  KEY `idx_state` (`state`),
  KEY `idx_createdby` (`created_by`),
  KEY `idx_featured_catid` (`featured`),
  KEY `idx_language` (`language`),
  KEY `idx_xreference` (`xreference`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_content_category_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content_category_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contentid` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `main` tinyint(4) NOT NULL COMMENT 'Main Category',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_content_featured_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content_featured_map` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Table structure for table `#__tz_portfolio_plus_content_rating`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content_rating` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT '',
  `rating_sum` int(11) NOT NULL DEFAULT '0',
  `rating_count` int(11) NOT NULL DEFAULT '0',
  KEY `extravote_idx` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_content_rejected`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_content_rejected` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'FK to the #__tz_portfolio_plus_content table.',
  `created` datetime NOT NULL,
  `created_by` int(11) UNSIGNED NOT NULL COMMENT 'FK to the #__users table.',
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;