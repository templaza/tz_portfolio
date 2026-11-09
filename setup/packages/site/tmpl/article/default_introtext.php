<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://tzportfolio.com

# Technical Support:  Forum - https://github.com/templaza/tz_portfolio/issues/

# Family website: http://templaza.com

# Family Support: Forum - https://templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die('Restricted access');

$params = $this -> item -> params;

?>
<?php
    //Call event onContentBeforeDisplay on plugin
    echo $this->item->event->beforeDisplayContent;

    if($params -> get('show_intro', 1)) {
        echo $this->item->introtext;
    }
?>