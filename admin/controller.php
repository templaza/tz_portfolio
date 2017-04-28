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

class TZ_Portfolio_PlusController extends JControllerLegacy
{
	/**
	 * @var		string	The extension for which the categories apply.
	 * @since	1.6
	 */
	protected $extension;

    protected $input;

//    protected $plugin_views = false;

	/**
	 * Constructor.
	 *
	 * @param	array An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Guess the JText message prefix. Defaults to the option.
		if (empty($this->extension)) {
			$this->extension = $this -> input -> getCmd('extension', 'com_tz_portfolio_plus');
		}
	}

	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{

        // Get the document object.
        $document = JFactory::getDocument();
        $app    = JFactory::getApplication();

		$document -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/fonts/font-awesome-4.5.0/css/font-awesome.min.css');
		$document -> addStyleSheet(JURI::base(true).'/components/com_tz_portfolio_plus/css/style.min.css');

        // Set the default view name and format from the Request.
        $vName		= $this -> input -> get('view', 'articles');

        $vFormat	= $document->getType();
        $lName		= $this -> input -> get('layout', 'default');
        $id			= $this -> input -> getInt('id');

        // Check for addon_datas
        if($vName == 'addon_datas' && !$this -> input -> getInt('addon_id')){
            $response = 500;

            if ($app->get('sef_rewrite'))
            {
                $response = 404;
            }
            throw new Exception(JText::sprintf('JLIB_APPLICATION_ERROR_VIEW_NOT_FOUND', $vName,
                $vFormat, $this->getName() . 'View'), $response);
        }

        // Check for edit form.
        if ($vName == 'category' && $lName == 'edit' && !$this->checkEditId('com_tz_portfolio_plus.edit.category', $id)) {
            // Somehow the person just went to the form - we don't allow that.
            $this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(JRoute::_('index.php?option=com_tz_portfolio_plus&view=categories&extension='.$this->extension, false));

            return false;
        }

        // Get and render the view.
        if ($view = $this->getView($vName, $vFormat)) {
            // Get the model for the view.
            $model = $this->getModel($vName, 'TZ_Portfolio_PlusModel', array('name' => $vName . '.' . substr($this->extension, 4)));

            // Push the model into the view (as default).
            $view->setModel($model, true);

            $view->setLayout($lName);

            // Push document object into the view.
            $view->assignRef('document', $document);
            // Load the submenu.
            require_once JPATH_COMPONENT.'/helpers/categories.php';

            //CategoriesHelper::addSubmenu($model->getState('filter.extension'));
            $view->display();
        }

		return $this;
	}
    
}
