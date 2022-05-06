--
-- Table structure for table `#__tz_portfolio_plus_addon_data`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_addon_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `extension_id` int(11) NOT NULL,
  `element` varchar(255) NOT NULL,
  `value` longtext NULL,
  `content_id` int(11) NULL,
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `asset_id` INT UNSIGNED NOT NULL DEFAULT '0',
  `created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `modified` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT UNSIGNED NOT NULL DEFAULT '0',
  `checked_out` INT NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` INT(10) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_addon_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `addon_id` INT UNSIGNED NOT NULL DEFAULT '0',
  `data_id` int(11) NOT NULL DEFAULT '0',
  `meta_id` int(11) NOT NULL DEFAULT '0',
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` longtext NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `#__tz_portfolio_plus_extensions`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_extensions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `type` varchar(100) NOT NULL DEFAULT '',
  `element` varchar(100) NOT NULL DEFAULT '',
  `folder` varchar(100) NOT NULL DEFAULT '',
  `protected` tinyint(3) NOT NULL DEFAULT '0',
  `manifest_cache` text NULL,
  `params` text NULL,
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `access` int(10) NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20;

-- --------------------------------------------------------

--
-- Dumping data for table `#__tz_portfolio_plus_extensions`
--
INSERT IGNORE INTO `#__tz_portfolio_plus_extensions` (`id`, `name`, `type`, `element`, `folder`, `protected`, `manifest_cache`, `params`, `checked_out`, `checked_out_time`, `published`, `access`, `ordering`) VALUES
(1, 'system', 'tz_portfolio_plus-template', 'system', '', 1, '{"name":"system","type":"tz_portfolio_plus-template","creationDate":"July 17th 2015","author":"DuongTVTemplaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"info@templaza.com","authorUrl":"","version":"1.0","description":"TZ_PORTFOLIO_PLUS_TPL_XML_DESCRIPTION","group":"","filename":"template"}', '', 0, '0000-00-00 00:00:00', 1, 1, 0),
(2, 'plg_content_vote', 'tz_portfolio_plus-plugin', 'vote', 'content', 1, '{"name":"plg_content_vote","type":"tz_portfolio_plus-plugin","creationDate":"Aug, 09th 2012","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 Open Source Matters. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com","version":"1.0.3","description":"PLG_CONTENT_VOTE_XML_DESCRIPTION","group":"","filename":"vote","special":0}', '{"show_cat_vote":"0","show_cat_counter":"1","cat_unrated":"1","show_counter":"1","unrated":"1"}', 0, '2016-01-07 10:03:01', 1, 1, 0),
(3, 'plg_mediatype_image', 'tz_portfolio_plus-plugin', 'image', 'mediatype', 1, '{"name":"plg_mediatype_image","type":"tz_portfolio_plus-plugin","creationDate":"September 17th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_MEDIATYPE_IMAGE_XML_DESCRIPTION","group":"","filename":"image","special":0}', '{"image_file_size":"10","image_file_type":"bmp,gif,jpg,jpeg,png,BMP,GIF,JPG,JPEG,PNG","image_mime_type":"image\\/jpeg,image\\/gif,image\\/png,image\\/bmp","image_size":["{\\"title\\":\\"XSmall\\",\\"width\\":\\"100\\",\\"image_name_prefix\\":\\"xs\\"}","{\\"title\\":\\"Small\\",\\"width\\":\\"200\\",\\"image_name_prefix\\":\\"s\\"}","{\\"title\\":\\"Medium\\",\\"width\\":\\"400\\",\\"image_name_prefix\\":\\"m\\"}","{\\"title\\":\\"Large\\",\\"width\\":\\"600\\",\\"image_name_prefix\\":\\"l\\"}","{\\"title\\":\\"XLarge\\",\\"width\\":\\"900\\",\\"image_name_prefix\\":\\"xl\\"}"],"mt_image_show_feed_image":"1","mt_image_feed_size":"o","mt_show_cat_image_hover":"1","mt_cat_image_size":"o","mt_image_size":"o","mt_show_image_hover":"1","mt_image_use_cloud":"0","mt_image_related_show_image":"1","mt_image_related_size":"o","mt_image_cloud_size":"o","mt_image_cloud_position":"inside","mt_image_cloud_softfocus":"0","mt_image_cloud_show_title":"1","mt_image_cloud_width":"","mt_image_cloud_height":"","mt_image_cloud_adjustX":"0","mt_image_cloud_adjustY":"0","mt_image_cloud_tint":"","mt_image_cloud_tint_opacity":"0.5","mt_image_cloud_len_opacity":"0.5","mt_image_cloud_smoothmove":"3","mt_image_cloud_title_opacity":"0.5"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(4, 'plg_extrafields_text', 'tz_portfolio_plus-plugin', 'text', 'extrafields', 1, '{"name":"plg_extrafields_text","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_TEXT_XML_DESCRIPTION","group":"","filename":"text","special":0}', '{"suggestion":"0"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(5, 'plg_extrafields_textarea', 'tz_portfolio_plus-plugin', 'textarea', 'extrafields', 1, '{"name":"plg_extrafields_textarea","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_TEXTAREA_XML_DESCRIPTION","group":"","filename":"textarea","special":0}', '{"cols":"50","rows":"5","use_editor_back_end":"0","use_editor_front_end":"0","groups_can_use_frontend_editor":"1"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(6, 'plg_extrafields_checkboxes', 'tz_portfolio_plus-plugin', 'checkboxes', 'extrafields', 1, '{"name":"plg_extrafields_checkboxes","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_CHECKBOXES_XML_DESCRIPTION","group":"","filename":"checkboxes","special":0}', '{"number_columns":"0"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(7, 'plg_extrafields_dropdownlist', 'tz_portfolio_plus-plugin', 'dropdownlist', 'extrafields', 1, '{"name":"plg_extrafields_dropdownlist","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_DROPDOWNLIST_XML_DESCRIPTION","group":"","filename":"dropdownlist","special":0}', '{"size":"5"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(8, 'plg_extrafields_multipleselect', 'tz_portfolio_plus-plugin', 'multipleselect', 'extrafields', 1, '{"name":"plg_extrafields_multipleselect","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_MULTIPLESELECT_XML_DESCRIPTION","group":"","filename":"multipleselect","special":0}', '{"size":"5"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(9, 'plg_extrafields_radio', 'tz_portfolio_plus-plugin', 'radio', 'extrafields', 1, '{"name":"plg_extrafields_radio","type":"tz_portfolio_plus-plugin","creationDate":"October 20th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_EXTRAFIELDS_RADIO_XML_DESCRIPTION","group":"","filename":"radio","special":0}', '{"bootstrap_style":"1","number_columns":"0"}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(10, 'plg_user_profile', 'tz_portfolio_plus-plugin', 'profile', 'user', 1, '{"name":"plg_user_profile","type":"tz_portfolio_plus-plugin","creationDate":"September 16th 2015","author":"DuongTVTemPlaza","copyright":"Copyright (C) 2015 TemPlaza. All rights reserved.","authorEmail":"support@templaza.com","authorUrl":"www.templaza.com\\/","version":"1.0.3","description":"PLG_USER_PROFILE_XML_DESCRIPTION","group":"","filename":"profile","special":0}', '{}', 0, '0000-00-00 00:00:00', 1, 1, 0),
(11, 'elegant', 'tz_portfolio_plus-template', 'elegant', '', 0, '{"name":"elegant","type":"tz_portfolio_plus-template","creationDate":"August 17th 2017","author":"Sonny","copyright":"Copyright (C) 2017 TemPlaza. All rights reserved.","authorEmail":"sonlv@templaza.com","authorUrl":"www.templaza.com","version":"1.0","description":"TZ_PORTFOLIO_PLUS_TPL_XML_DESCRIPTION","group":"","filename":"template"}', '{"use_single_layout_builder":"1","load_style":"1"}', 0, '0000-00-00 00:00:00', 1, 1, 0);

--
-- Table structure for table `#__tz_portfolio_plus_templates`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `home` char(7) NOT NULL DEFAULT '0',
  `protected` tinyint(3) NOT NULL DEFAULT '0',
  `layout` text NULL,
  `params` text NULL,
  `preset` VARCHAR( 255 ) NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------