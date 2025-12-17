<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - htfas://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - htfas://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\Administrator\Service\HTML;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Mail\MailHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Mails\Administrator\Helper\MailsHelper;
use Joomla\Registry\Registry;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\RouteHelper;

class Icon{
    public function create($category, $params, $options = array())
    {
        $uri = Uri::getInstance();

        $url = 'index.php?option=com_tz_portfolio&task=article.add&return='.base64_encode($uri).'&a_id=0&catid=' . $category->id;

        if ($params->get('show_cat_icons', 1)) {

            $icon	= 'fas fa-plus-circle';
            if(count($options) && isset($options['icon'])){
                $icon	= $options['icon'];
                unset($options['icon']);
            }
            $text = '<i class="'.$icon.'"></i> ' . Text::_('JNEW') . '&#160;';
        } else {
            $text = Text::_('JNEW').'&#160;';
        }

        $button = HTMLHelper::_('link', Route::_($url), $text, 'class="btn btn-primary"');

        $output = '<span class="hasTip dropdown-item" title="'.Text::_('COM_TZ_PORTFOLIO_CREATE_ARTICLE').'">'.$button.'</span>';
        return $output;
    }

    public function email($article, $params, $attribs = array())
    {
        $mailto_file    = JPATH_SITE . '/components/com_mailto/helpers/mailto.php';
        if(!file_exists($mailto_file)) {
            return '';
        }
        require_once $mailto_file;

        if(!function_exists('MailToHelper')){
            return '';
        }

        $uri	= Uri::getInstance();
        $base	= $uri->toString(array('scheme', 'host', 'port'));
        $template = Factory::getApplication()->getTemplate();

        $link   = RouteHelper::getArticleRoute($article -> slug,$article -> catid);

        $link	= $base . Route::_($link,false);

        $url	= 'index.php?option=com_mailto&amp;tmpl=component&amp;template='.$template.'&amp;link='
            .MailToHelper::addLink($link);

        $status = 'width=400,height=350,menubar=yes,resizable=yes';

        if ($params->get('show_cat_icons', 1)) {

            $icon	= 'far fa-envelope';
            if(count($attribs) && isset($attribs['icon'])){
                $icon	= $attribs['icon'];
                unset($attribs['icon']);
            }
            $text = '<i class="'. $icon .'"></i> ' . Text::_('JGLOBAL_EMAIL');
        } else {
            $text = Text::_('JGLOBAL_EMAIL');
        }

        $attribs['title']	= Text::_('JGLOBAL_EMAIL');

        $class  = 'dropdown-item';
        if(isset($attribs['class'])){
            $class  .= ' '.$attribs['class'];
        }
        $attribs['class']   = $class;
        $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";

        $output = HTMLHelper::_('link', Route::_($url), $text, $attribs);
        return $output;
    }

    /**
     * Display an edit icon for the article.
     *
     * This icon will not display in a popup window, nor if the article is trashed.
     * Edit access checks must be performed in the calling code.
     *
     * @param	object	$article	The article in question.
     * @param	object	$params		The article parameters
     * @param	array	$attribs	Not used??
     *
     * @return	string	The HTML for the article edit icon.
     * @since	1.6
     */
    public function edit($article, $params, $attribs = array())
    {
        $user	= Factory::getUser();
        $userId	= $user->get('id');
        $uri	= Uri::getInstance();

        // Ignore if in a popup window.
        if ($params && $params->get('popup')) {
            return;
        }

        // Ignore if the state is negative (trashed).
        if ($article->state < 0) {
            return;
        }

        // Show checked_out icon if the article is checked out by a different user
        if (property_exists($article, 'checked_out') && property_exists($article, 'checked_out_time') && $article->checked_out > 0 && $article->checked_out != $user->get('id')) {
            $checkoutUser = Factory::getUser($article->checked_out);
            
            $button     = HTMLHelper::_('image', 'system/checked_out.png', null, null, true);
            $date       = HTMLHelper::_('date', $article->checked_out_time);
            $tooltip    = Text::_('JLIB_HTML_CHECKED_OUT').' :: '.Text::sprintf('COM_TZ_PORTFOLIO_CHECKED_OUT_BY',
                    $checkoutUser->name).' <br /> '.$date;
            
            return '<span class="hasTip" title="'.htmlspecialchars($tooltip, ENT_COMPAT, 'UTF-8').'">'.$button.'</span>';
        }

        $tmpl   = Factory::getApplication() -> input -> getCmd('tmpl',null);
        if($tmpl){
            $tmpl   = '&tmpl=component';
        }

//        $url	= 'index.php?option=com_tz_portfolio&amp;task=article.edit&amp;a_id='
//            .$article->id.'&amp;return='.base64_encode($uri)
//            .$tmpl;
        $url	= 'index.php?option=com_tz_portfolio&task=article.edit&a_id='
            .$article->id.'&return='.base64_encode($uri)
            .$tmpl;

        if ($article->state == 0) {
            $overlib = Text::_('JUNPUBLISHED');
        }
        else {
            $overlib = Text::_('JPUBLISHED');
        }

        $date = HTMLHelper::_('date', $article->created);
        $author = $article->created_by_alias ? $article->created_by_alias : $article->author;
        $author = !empty($author)?htmlspecialchars($author, ENT_COMPAT, 'UTF-8'):$author;

        $overlib .= '&lt;br /&gt;';
        $overlib .= $date;
        $overlib .= '&lt;br /&gt;';
        $overlib .= Text::sprintf('COM_TZ_PORTFOLIO_WRITTEN_BY', $author);

        $icon	= $article->state ? ' fas fa-edit' : 'far fa-eye-slash';
        if($article->state && count($attribs) && isset($attribs['icon'])){
            $icon	= $attribs['icon'];
            unset($attribs['icon']);
        }
        if(count($attribs) && isset($attribs['icon_close'])){
            $icon	= $attribs['icon_close'];
            unset($attribs['icon_close']);
        }

        $text = '<i class="hasTip '.$icon.'" title="'.Text::_('COM_TZ_PORTFOLIO_EDIT_ITEM').' :: '.$overlib.'"></i> '.Text::_('JGLOBAL_EDIT');

        $class  = 'dropdown-item';
        if(isset($attribs['class'])){
            $class  .= ' '.$attribs['class'];
        }
        $attribs['class']   = $class;

        $output = HTMLHelper::_('link', Route::_($url), $text, $attribs);

        return $output;
    }


    public function print_popup($article, $params, $attribs = array())
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $request = $input->request;

        $url    = RouteHelper::getArticleRoute($article -> slug,$article -> catid);

        $url .= '&amp;tmpl=component&amp;print=1&amp;layout=default&amp;page='.@ $request->limitstart;

        $status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=640,height=480,directories=no,location=no';

        // checks template image directory for image, if non found default are loaded
        if ($params->get('show_cat_icons', 1)) {
            $icon	= 'fas fa-print';
            if(count($attribs) && isset($attribs['icon'])){
                $icon	= $attribs['icon'];
                unset($attribs['icon']);
            }
            $text = '<i class="'.$icon.'"></i> '.Text::_('JGLOBAL_PRINT');
        } else {
            $text = Text::_('JGLOBAL_PRINT');
        }

        $attribs['title']	= Text::_('JGLOBAL_PRINT');
        $attribs['onclick'] = "window.open(this.href,'win2','".$status."'); return false;";
        $attribs['rel']		= 'nofollow';

        $class  = 'dropdown-item';
        if(isset($attribs['class'])){
            $class  .= ' '.$attribs['class'];
        }
        $attribs['class']   = $class;

        return HTMLHelper::_('link', Route::_($url), $text, $attribs);
    }

    public function print_screen($article, $params, $attribs = array())
    {
        // checks template image directory for image, if non found default are loaded
        if ($params->get('show_cat_icons', 1)) {
            $icon	= 'fas fa-print';
            if(count($attribs) && isset($attribs['icon'])){
                $icon	= $attribs['icon'];
                unset($attribs['icon']);
            }
            $text = $text = '<i class="'.$icon.'"></i> '.Text::_('JGLOBAL_PRINT');
        } else {
            $text = Text::_('JGLOBAL_PRINT');
        }

        return '<a href="#" onclick="window.print();return false;">' . $text . '</a>';
    }

//    public function getIcon($code) {
////        jimport('joomla.filesytem.file');
//        $json   =   file_get_contents(JPATH_ROOT.DIRECTORY_SEPARATOR.'media'.DIRECTORY_SEPARATOR.'tz_portfolio'.DIRECTORY_SEPARATOR.'icomoon'.DIRECTORY_SEPARATOR.'selection.json');
//        $data   =   json_decode($json);
//        $icons  =   $data->icons;
//        foreach ($icons as $icon) {
//            if ($icon->properties->code == $code) {
//                return $icon->properties->name;
//            }
//        }
//        return '';
//    }
}