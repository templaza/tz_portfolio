<?php
/*------------------------------------------------------------------------

# TZ Portfolio Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2024 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

namespace TemPlaza\Component\TZ_Portfolio\AddOn\Content\Vote\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Registry\Registry;
use TemPlaza\Component\TZ_Portfolio\AddOn\Content\Vote\Helper\VoteHelper;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\AddOn\AddOnController;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\ArticleHelper;
use TemPlaza\Component\TZ_Portfolio\Site\Helper\CategoriesHelper;

defined('_JEXEC') or die;

class VoteController extends AddOnController {

    public function getModel($name = 'vote', $prefix = '', $config = array('ignore_request' => true))
    {
        $cid            = $this -> input -> getInt('cid');
        if($article    = ArticleHelper::getArticleById($cid)) {

            $app = Factory::getApplication('site');
            $params = $app->getParams('com_tz_portfolio');

            $artParams = new Registry();
            if ($article->attribs) {
                $artParams->loadString($article->attribs);
            }
            $category = CategoriesHelper::getMainCategoriesByArticleId($cid);

            $catParams = new Registry();
            if ($category && is_array($category)) {
                $category = $category[0];
            }
            if ($category && $category->params) {
                $catParams->loadString($category->params);
            }

            $addonParams = new Registry();
            if ($this->addon->params) {
                $addonParams->loadString($this->addon->params);
            }

            $params->merge($addonParams);
            $params->merge($catParams);
            $params->merge($artParams);

            $article->params = $params;

            $this->article = $article;
            $this->trigger_params = $params;

            $_config = array('article' => $article, 'trigger_params' => $params, 'addon' => $this->addon);
            $config = array_merge($config, $_config);
        }
        $config['base_path']    = $this->basePath;

//        var_dump($name);
//        var_dump($prefix);
//        var_dump($this->model_prefix);

//        if (empty($name)) {
//            $name = $this->context;
//        }
//        var_dump($this->name);
//        var_dump('$this->task');
//        var_dump($this->task);
        return parent::getModel($name, $prefix, $config);
    }
    
    public function vote(){

        $result     = true;
        $message    = '';
        $html       = '';
        $dataReturn = new \stdClass();
        $input      = $this -> input;
        $app        = Factory::getApplication();

        // Get current Ip
        $currip = $input -> server -> get('REMOTE_ADDR', '', 'string');

        $cid            = $input -> getInt('cid');
        $user_rating    = $input -> getInt('user_rating');

        if(($votesdb = VoteHelper::getVoteByArticleId($cid)) && $votesdb -> lastip == $currip){

            // You are voted
            $message    = Text::_('PLG_CONTENT_VOTE_RATED');
        }else {
            if($model  = $this -> getModel()){

                $message  = Text::_('PLG_CONTENT_VOTE_THANKS');

                if(!$model -> save(array('cid' => $cid, 'user_rating' => $user_rating))){
                    $message    = $model -> getError();
                    $result     = false;
                }
                else{

                    if($item = $model -> getItem()){
                        $dataReturn -> rating_sum   = $item -> rating_sum;
                        $dataReturn -> rating_count = $item -> rating_count;
                    }
                }
            }
        }

        echo new JsonResponse($dataReturn, $message, !$result);

        $app -> close();
    }
}