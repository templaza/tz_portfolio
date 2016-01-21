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

jimport('joomla.application.component.view');

class TZ_Portfolio_PlusViewLegacy extends JViewLegacy{
    protected $generateLayout   = null;
    protected $core_types       = array();

    public function generateLayout(&$article,&$params,$dispatcher){
        if($template   = TZ_Portfolio_PlusTemplate::getTemplate(true)){
            $tplparams  = $template -> params;
            if($tplparams -> get('use_single_layout_builder',1)){

                $core_types         = TZ_Portfolio_PlusPluginHelper::getCoreContentTypes();
                $this -> core_types = JArrayHelper::getColumn($core_types, 'value');

                $this->_generateLayout($article, $params, $dispatcher);
                return $this -> generateLayout;
            }
        }
        return false;
    }

    protected function _generateLayout(&$article,&$params, JEventDispatcher $dispatcher){
        if($template   = TZ_Portfolio_PlusTemplate::getTemplate(true)){
            $theme  = $template;
            $html   = null;

            if($theme){
                if($tplParams  = $theme -> layout){
                    foreach($tplParams as $tplItems){
                        $rows   = null;

                        $background = null;
                        $color      = null;
                        $margin     = null;
                        $padding    = null;

                        if($tplItems -> backgroundcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> backgroundcolor))){
                            $background  = 'background: '.$tplItems -> backgroundcolor.';';
                        }
                        if($tplItems -> textcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> textcolor))){
                            $color      =  'color: '.$tplItems -> textcolor.';';
                        }
                        if(isset($tplItems -> margin) && !empty($tplItems -> margin)){
                            $margin = 'margin: '.$tplItems -> margin.';';
                        }
                        if(isset($tplItems -> padding) && !empty($tplItems -> padding)){
                            $padding = 'padding: '.$tplItems -> padding.';';
                        }
                        if($background || $color || $margin || $padding){
                            $this -> document -> addStyleDeclaration('
                            #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).'{
                                '.$background.$color.$margin.$padding.'
                            }
                        ');
                        }
                        if($tplItems -> linkcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> linkcolor))){
                            $this -> document -> addStyleDeclaration('
                                #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).' a{
                                    color: '.$tplItems -> linkcolor.';
                                }
                            ');
                        }
                        if($tplItems -> linkhovercolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($tplItems -> linkhovercolor))){
                            $this -> document -> addStyleDeclaration('
                                #tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).' a:hover{
                                    color: '.$tplItems -> linkhovercolor.';
                                }
                            ');
                        }
                        $rows[] = '<div id="tz-portfolio-template-'.JApplication::stringURLSafe($tplItems -> name).'"'
                            .' class="'.($tplItems -> {"class"}?' '.$tplItems -> {"class"}:'')
                            .($tplItems -> responsive?' '.$tplItems -> responsive:'').'">';
                        if(isset($tplItems -> containertype) && $tplItems -> containertype){
                            $rows[] = '<div class="'.$tplItems -> containertype.'">';
                        }

                        $rows[] = '<div class="row">';
                        if($tplItems && isset($tplItems -> children)){
                            foreach($tplItems -> children as $children){
                                $html   = null;

                                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                                    $rows[] = '<div class="'
                                        .(!empty($children -> {"col-lg"})?'col-lg-'.$children -> {"col-lg"}:'')
                                        .(!empty($children -> {"col-md"})?' col-md-'.$children -> {"col-md"}:'')
                                        .(!empty($children -> {"col-sm"})?' col-sm-'.$children -> {"col-sm"}:'')
                                        .(!empty($children -> {"col-xs"})?' col-xs-'.$children -> {"col-xs"}:'')
                                        .(!empty($children -> {"col-lg-offset"})?' col-lg-offset-'.$children -> {"col-lg-offset"}:'')
                                        .(!empty($children -> {"col-md-offset"})?' col-md-offset-'.$children -> {"col-md-offset"}:'')
                                        .(!empty($children -> {"col-sm-offset"})?' col-sm-offset-'.$children -> {"col-sm-offset"}:'')
                                        .(!empty($children -> {"col-xs-offset"})?' col-xs-offset-'.$children -> {"col-xs-offset"}:'')
                                        .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                                        .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                                }

                                if($children -> type && $children -> type !='none'){
                                    if(in_array($children -> type, $this -> core_types)) {
                                        $html = $this->loadTemplate($children->type);
                                    }else{
                                        $plugin = $children -> type;
                                        $layout = null;
                                        if(strpos($children -> type, ':') != false){
                                            list($plugin, $layout)  = explode(':', $children -> type);
                                        }

                                        if($plugin_obj = TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                                            $className      = 'PlgTZ_Portfolio_PlusContent'.ucfirst($plugin);

                                            if(!class_exists($className)){
                                                TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                                            }
                                            if(class_exists($className)) {
                                                $registry   = new JRegistry($plugin_obj -> params);

                                                $plgClass   = new $className($dispatcher,array('type' => ($plugin_obj -> type)
                                                , 'name' => ($plugin_obj -> name), 'params' => $registry));

                                                if(method_exists($plgClass, 'onContentDisplayArticleView')) {
                                                    $html = $plgClass->onContentDisplayArticleView('com_tz_portfolio_plus.'
                                                        .$this -> getName(), $this->item, $this->item->params
                                                        , $this->state->get('list.offset'), $layout);
                                                }
                                            }
                                            if(is_array($html)) {
                                                $html = implode("\n", $html);
                                            }
                                        }
                                    }
                                    $html   = trim($html);
                                }

                                $rows[] = $html;

                                if( !empty($children -> children) and is_array($children -> children) ){
                                    $this -> _childrenLayout($rows,$children,$article,$params,$dispatcher);
                                }

                                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                                    $rows[] = '</div>'; // Close col tag
                                }
                            }
                        }

                        if(isset($tplItems -> containertype) && $tplItems -> containertype){
                            $rows[] = '</div>';
                        }
                        $rows[] = '</div>';
                        $rows[] = '</div>';
                        $this -> generateLayout .= implode("\n",$rows);
                    }
                }
            }
        }
    }

    protected function _childrenLayout(&$rows,$children,&$article,&$params,$dispatcher){
        foreach($children -> children as $children){
            $background = null;
            $color      = null;
            $margin     = null;
            $padding    = null;

            if($children -> backgroundcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> backgroundcolor))){
                $background  = 'background: '.$children -> backgroundcolor.';';
            }
            if($children -> textcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> textcolor))){
                $color      =  'color: '.$children -> textcolor.';';
            }
            if(isset($children -> margin) && !empty($children -> margin)){
                $margin = 'margin: '.$children -> margin.';';
            }
            if(isset($children -> padding) && !empty($children -> padding)){
                $padding = 'padding: '.$children -> padding.';';
            }
            if($background || $color){
                $this -> document -> addStyleDeclaration('
                    #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner{
                        '.$background.$color.$margin.$padding.'
                    }
                ');
            }
            if($children -> linkcolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> linkcolor))){
                $this -> document -> addStyleDeclaration('
                        #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner a{
                            color: '.$children -> linkcolor.';
                        }
                    ');
            }
            if($children -> linkhovercolor && !preg_match('/^rgba\([0-9]+\,\s+?[0-9]+\,\s+?[0-9]+\,\s+?0\)$/i',trim($children -> linkhovercolor))){
                $this -> document -> addStyleDeclaration('
                        #tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner a:hover{
                            color: '.$children -> linkhovercolor.';
                        }
                    ');
            }
            $rows[] = '<div id="tz-portfolio-template-'.JApplication::stringURLSafe($children -> name).'-inner" class="'
                .$children -> {"class"}.($children -> responsive?' '.$children -> responsive:'').'">';
            $rows[] = '<div class="row">';
            foreach($children -> children as $children){
                $html   = null;

                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                    $rows[] = '<div class="'
                        .(!empty($children -> {"col-lg"})?'col-lg-'.$children -> {"col-lg"}:'')
                        .(!empty($children -> {"col-md"})?' col-md-'.$children -> {"col-md"}:'')
                        .(!empty($children -> {"col-sm"})?' col-sm-'.$children -> {"col-sm"}:'')
                        .(!empty($children -> {"col-xs"})?' col-xs-'.$children -> {"col-xs"}:'')
                        .(!empty($children -> {"col-lg-offset"})?' col-lg-offset-'.$children -> {"col-lg-offset"}:'')
                        .(!empty($children -> {"col-md-offset"})?' col-md-offset-'.$children -> {"col-md-offset"}:'')
                        .(!empty($children -> {"col-sm-offset"})?' col-sm-offset-'.$children -> {"col-sm-offset"}:'')
                        .(!empty($children -> {"col-xs-offset"})?' col-xs-offset-'.$children -> {"col-xs-offset"}:'')
                        .(!empty($children -> {"customclass"})?' '.$children -> {"customclass"}:'')
                        .($children -> responsiveclass?' '.$children -> responsiveclass:'').'">';
                }

                if($children -> type && $children -> type !='none'){
                    if(in_array($children -> type, $this -> core_types)) {
                        $html = $this -> loadTemplate($children -> type);
                    }else{
                        $plugin = $children -> type;
                        $layout = null;
                        if(strpos($children -> type, ':') != false){
                            list($plugin, $layout)  = explode(':', $children -> type);
                        }

                        if($plugin_obj = TZ_Portfolio_PlusPluginHelper::getPlugin('content', $plugin)) {
                            $className      = 'PlgTZ_Portfolio_PlusContent'.ucfirst($plugin);

                            if(!class_exists($className)){
                                TZ_Portfolio_PlusPluginHelper::importPlugin('content', $plugin);
                            }
                            if(class_exists($className)) {
                                $registry   = new JRegistry($plugin_obj -> params);

                                $plgClass   = new $className($dispatcher,array('type' => ($plugin_obj -> type)
                                , 'name' => ($plugin_obj -> name), 'params' => $registry));

                                if(method_exists($plgClass, 'onContentDisplayArticleView')) {
                                    $html = $plgClass->onContentDisplayArticleView('com_tz_portfolio_plus.'.$this -> getName(),
                                        $this->item, $this->item->params, $this->state->get('list.offset'), $layout);
                                }
                            }
                            if(is_array($html)) {
                                $html = implode("\n", $html);
                            }
                        }
                    }
                    $html   = trim($html);
                }
                $rows[] = $html;

                if( !empty($children -> children) and is_array($children -> children) ){
                    $this -> _childrenLayout($rows,$children,$article,$params,$dispatcher);
                }

                if(!empty($children -> {"col-lg"}) || !empty($children -> {"col-md"})
                    || !empty($children -> {"col-sm"}) || !empty($children -> {"col-xs"})
                    || !empty($children -> {"col-lg-offset"}) || !empty($children -> {"col-md-offset"})
                    || !empty($children -> {"col-sm-offset"}) || !empty($children -> {"col-xs-offset"})
                    || !empty($children -> {"customclass"}) || $children -> responsiveclass){
                    $rows[] = '</div>'; // Close col tag
                }

            }
            $rows[] = '</div>';
            $rows[] = '</div>';
        }
        return;
    }
}