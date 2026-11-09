<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://tzportfolio.com

# Technical Support:  Forum - https://github.com/templaza/tz_portfolio/issues/

# Family website: http://templaza.com

# Family Support: Forum - https://templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

class TZ_Portfolio_PlusSetupString{
    public static function jsPlusAddSlashes($s)
    {
        $o="";
        $l=strlen($s);
        for($i=0;$i<$l;$i++)
        {
            $c=$s[$i];
            switch($c)
            {
                case '<': $o.='\\x3C'; break;
                case '>': $o.='\\x3E'; break;
                case '\'': $o.='\\\''; break;
                case '\\': $o.='\\\\'; break;
                case '"':  $o.='\\"'; break;
                case "\n": $o.='\\n'; break;
                case "\r": $o.='\\r'; break;
                default:
                    $o.=$c;
            }
        }
        return $o;
    }
}