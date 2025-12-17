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

namespace TemPlaza\Component\TZ_Portfolio\Site\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioUri;
use TemPlaza\Component\TZ_Portfolio\Administrator\Library\TZ_PortfolioTemplate;

defined('_JEXEC') or die;

/**
 * Content Component Controller
 *
 * @since  1.5
 */
class DisplayController extends BaseController
{
    /**
     * @param   array                         $config   An optional associative array of configuration settings.
     *                                                  Recognized key values include 'name', 'default_task', 'model_path', and
     *                                                  'view_path' (this list is not meant to be comprehensive).
     * @param   MVCFactoryInterface|null      $factory  The factory.
     * @param   CMSApplication|null           $app      The Application for the dispatcher
     * @param   \Joomla\CMS\Input\Input|null  $input    The Input object for the request
     *
     */
//    public function __construct($config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
//    {
//        $this->input = Factory::getApplication()->getInput();
//
//        // Article frontpage Editor pagebreak proxying:
//        if ($this->input->get('view') === 'article' && $this->input->get('layout') === 'pagebreak') {
//            $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
//        } elseif ($this->input->get('view') === 'articles' && $this->input->get('layout') === 'modal') {
//            // Article frontpage Editor article proxying:
//            $config['base_path'] = JPATH_COMPONENT_ADMINISTRATOR;
//        }
//
//        parent::__construct($config, $factory, $app, $input);
//    }

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached.
     * @param   boolean  $urlparams  An array of safe URL parameters and their variable types.
     *                   @see        \Joomla\CMS\Filter\InputFilter::clean() for valid values.
     *
     * @return  DisplayController  This object to support chaining.
     *
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = false)
    {
        $app    = Factory::getApplication();
        $input  = $app -> getInput();

        $format = $input -> getWord('format');
        $vName  = $input -> get('view');

        if($vName == 'portfolio' && $format == 'json'){
            $page   = $input -> getInt('page');
            $params = ComponentHelper::getParams('com_tz_portfolio');

            $Itemid     = $input -> getInt('Itemid');
            $menu       = $app -> getMenu();
            $menuParams = $menu -> getParams($Itemid);

            $params -> merge($menuParams);

            $limit  = (int) $params -> get('tz_article_limit', 10);

            $offset = $limit * ($page - 1);

            $input -> set('limitstart', $offset);
        }

        if($format != 'ajax' && $format != 'json'){
            $doc    = Factory::getDocument();
            $wa     = $doc->getWebAssetManager();

            $wa->useScript('core')
                ->useScript('jquery');

            $params = ComponentHelper::getParams('com_tz_portfolio');

            if($params -> get('enable_uikit', 1)) {
                $wa->useScript('com_tz_portfolio.uikit');
                $wa->useScript('com_tz_portfolio.uikiticon');
                $wa->useStyle('com_tz_portfolio.uikit');
            }
        }

//        $cachable = true;
//
//        /**
//         * Set the default view name and format from the Request.
//         * Note we are using a_id to avoid collisions with the router and the return page.
//         * Frontend is a bit messier than the backend.
//         */
//        $id    = $this->input->getInt('a_id');
//        $vName = $this->input->getCmd('view', 'categories');
//        $this->input->set('view', $vName);
//
        $user = $this->app->getIdentity();
//
//        if (
//            $user->get('id')
//            || ($this->input->getMethod() === 'POST'
//            && (($vName === 'category' && $this->input->get('layout') !== 'blog') || $vName === 'archive'))
//        ) {
//            $cachable = false;
//        }
//
//        $safeurlparams = [
//            'catid'            => 'INT',
//            'id'               => 'INT',
//            'cid'              => 'ARRAY',
//            'year'             => 'INT',
//            'month'            => 'INT',
//            'limit'            => 'UINT',
//            'limitstart'       => 'UINT',
//            'showall'          => 'INT',
//            'return'           => 'BASE64',
//            'filter'           => 'STRING',
//            'filter_order'     => 'CMD',
//            'filter_order_Dir' => 'CMD',
//            'filter-search'    => 'STRING',
//            'print'            => 'BOOLEAN',
//            'lang'             => 'CMD',
//            'Itemid'           => 'INT', ];
//
//        // Check for edit form.
//        if ($vName === 'form' && !$this->checkEditId('com_content.edit.article', $id)) {
//            // Somehow the person just went to the form - we don't allow that.
//            throw new \Exception(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 403);
//        }

        // Check for edit form.
        if ($vName == 'myarticles') {
            if(!$user || ($user && !$user -> get('id'))){
                $link   = Route::_('index.php?option=com_users&view=login');
                $this -> setRedirect(str_replace('&amp;', '&', $link), Text::_('JERROR_ALERTNOAUTHOR'), 'error');
                return false;
            }
        }
//
//        if ($vName === 'article') {
//            // Get/Create the model
//            if ($model = $this->getModel($vName)) {
//                if (ComponentHelper::getParams('com_content')->get('record_hits', 1) == 1) {
//                    $model->hit();
//                }
//            }
//        }

        parent::display($cachable, $urlparams);


        $document = Factory::getApplication() -> getDocument();
        $viewType = $document->getType();
        $viewName = $this->input->get('view', $this->default_view);
        $viewLayout = $this->input->get('layout', 'default', 'string');

        $view = $this->getView($viewName, $viewType, '', array('base_path' => $this->basePath, 'layout' => $viewLayout));

        $this -> parseDocument($view);

        return $this;
    }


    public function parseDocument(&$view = null){
        if($view){
            if(isset($view -> document)){
                if($template = TZ_PortfolioTemplate::getTemplate(true)) {
                    if(Folder::exists(COM_TZ_PORTFOLIO_STYLE_PATH.DIRECTORY_SEPARATOR.$template -> template)) {

                        $app		= Factory::getApplication('site');
                        $params     = $app -> getParams();

                        $docOptions['template']     = $template->template;
                        $docOptions['file']         = 'template.php';
                        $docOptions['params']       = $template->params;
                        $docOptions['directory']    = COM_TZ_PORTFOLIO_PATH_SITE . DIRECTORY_SEPARATOR . 'templates';

//                        // Add template.css file if it has have in template
//                        if(!$params -> get('enable_bootstrap',1) || ($params -> get('enable_bootstrap',1)
//                                && $params -> get('bootstrapversion', 4) == 3)){
//                            $view->document -> addStyleSheet(TZ_Portfolio_PlusUri::base(true).'/css/tzportfolioplus.min.css',
//                                array('version' => 'auto'));
//                        }
                        $legacyPath = COM_TZ_PORTFOLIO_STYLE_PATH . DIRECTORY_SEPARATOR . $template -> template
                            . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'template.css';

                        /* @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
                        $wa = $view -> document -> getWebAssetManager();

                        if((TZ_PortfolioTemplate::getSassDirByStyle($template -> template)
                                || (!TZ_PortfolioTemplate::getSassDirByStyle($template -> template)
                                    && TZ_PortfolioTemplate::getSassDirCore()))
                            && !File::exists($legacyPath)
                            && $cssRelativePath = TZ_PortfolioTemplate::getCssStyleName($template -> template,
                                $params, $docOptions['params'] -> get('colors', array()), $view -> document)){


                            $wa -> registerAndUseStyle('com_tz_portfolio.style.'.$template -> template,
                                COM_TZ_PORTFOLIO_MEDIA_BASE.'/css/'.$cssRelativePath,
                                array('version' => 'auto'));

//                            $view->document->addStyleSheet(TZ_PortfolioUri::base(true)
//                                . '/css/'.$cssRelativePath, array('version' => 'auto'));
                        }/*elseif (File::exists($legacyPath)) {
                            $view->document->addStyleSheet(TZ_PortfolioUri::base(true) . '/templates/'
                                . $template -> template . '/css/template.css', array('version' => 'auto'));
                        }*/

                        // Parse document of view to require template.php(in tz portfolio template) file.
                        $view->document->parse($docOptions);
                    }

                    return true;
                }
            }
            return false;
        }
        return false;
    }
}
