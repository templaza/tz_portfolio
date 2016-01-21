<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# author    DuongTVTemPlaza

# copyright Copyright (C) 2015 templaza.com. All Rights Reserved.

# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Websites: http://www.templaza.com

# Technical Support:  Forum - http://templaza.com/Forum

-------------------------------------------------------------------------*/

// No direct access
defined('_JEXEC') or die;

// Base this model on the backend version.
require_once JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/controllers/article.php';

class TZ_Portfolio_PlusControllerForm extends TZ_Portfolio_PlusControllerArticle
{
    function listsfields(){
        $model  = $this -> getModel('Form');
        $data = $model -> listsfields();
        echo $data;
        die();
    }

    function deleteAttachment(){
        $model  = $this -> getModel('Form');
        $model -> deleteAttachment();
        //echo $data;
        die();
    }

    function selectgroup(){
        $model  = $this -> getModel('Form');
        $data   = $model -> selectgroup();
        echo $data;
        die();
    }

    function getThumb(){
        $model  = $this -> getModel('Form');
        $data   = $model -> getThumb();
        echo $data;
        die();
    }
}
?>