
--
-- Table structure for table `#__tz_portfolio_plus_tags`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) NOT NULL DEFAULT '',
  `published` tinyint(4) NOT NULL DEFAULT '1',
  `description` text NULL,
  `params` text NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__tz_portfolio_plus_tag_content_map`
--

CREATE TABLE IF NOT EXISTS `#__tz_portfolio_plus_tag_content_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tagsid` int(11) NOT NULL DEFAULT '0',
  `contentid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------