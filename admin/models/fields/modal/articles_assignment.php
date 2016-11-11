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

defined('JPATH_BASE') or die;

JLoader::import('defines',JPATH_ADMINISTRATOR.'/components/com_tz_portfolio_plus/includes');
JFormHelper::loadFieldClass('checkboxes');

/**
 * Supports a modal article picker.
 */
class JFormFieldModal_Articles_Assignment extends JFormFieldCheckboxes
{
    /**
     * The form field type.
     *
     * @var		string
     * @since	1.6
     */
    protected $type = 'Modal_Articles_Assignment';

//    function __construct($form ){
//        JFactory::getLanguage() -> load('com_tz_portfolio_plus');
//    }

    /**
     * Method to get the field input markup.
     *
     * @return	string	The field input markup.
     * @since	1.6
     */
    protected function getInput()
    {
        $allowEdit		= ((string) $this->element['edit'] == 'true') ? true : false;
        $allowClear		= ((string) $this->element['clear'] != 'false') ? true : false;

        // Load the modal behavior script.
        JHtml::_('behavior.modal', 'a.modal');

        // Build the script.
        $script = array();
        $script[] = '	function '.$this->id.'Remove(obj) {';
        $script[] = '	    obj.parentNode.parentNode.parentNode.removeChild(obj.parentNode.parentNode);';
        $script[] = '		var tztable = document.getElementById("'.$this->id.'_table");';
        $script[] = '		var tbody = tztable.getElementsByTagName("tbody");';
        $script[] = '		if(!tbody[0].innerHTML.trim().length){
                                var tzclear = document.getElementById("' . $this->id . '_clear");
                                tzclear.setAttribute("class",tzclear.getAttribute("class")+" hidden");
                            }';
        $script[] = '	};';

        $script[] = '	function jSelectArticle_'.$this->id.'(ids, titles, categories) {';
        $script[] = '		var tztable = document.getElementById("'.$this->id.'_table");';
        $script[] = '		var tbody = tztable.getElementsByTagName("tbody");';
        $script[] = '		var parser = new DOMParser();';
        $script[] = '		if(ids.length){';
        $script[] = '		for(var i = 0; i < ids.length; i++){
                                var tr = document.createElement("tr");

                                var td = document.createElement("td");
                                td.innerHTML = titles[i];
                                tr.appendChild(td);';

        $script[] =            'td = td.cloneNode(true);
                                td.innerHTML = categories[i];
                                tr.appendChild(td);
                                tbody[0].appendChild(tr);';

        $script[] = '           td = td.cloneNode(true);
                                td.innerHTML = "<a href=\"javascript:\" class=\"btn\" onclick=\"'.$this->id.'Remove(this);\"><i class=\"icon-remove\"></i> '.JText::_('JTOOLBAR_REMOVE').'</a>";';
        // Edit article button
        if ($allowEdit)
        {
            $script[]   =       'td.className = "btn-group";';
            $script[]   =       'td.style     = "display: table-cell; position: inherit;";';
            $script[]   =       'td.innerHTML = "<a class=\"btn btn-small\" target=\"_blank\" href=\"index.php?option=com_tz_portfolio_plus&task=article.edit&id="+ids[i]+"\"><span class=\"icon-edit\"></span> ' . JText::_('JACTION_EDIT') . '</a> <a href=\"javascript:\" class=\"btn btn-small\" onclick=\"'.$this->id.'Remove(this);\"><i class=\"icon-remove\"></i> '.JText::_('JTOOLBAR_REMOVE').'</a>"';
        }
        $script[] =            'tr.appendChild(td);';


        $script[] =            'td = td.cloneNode(true);
                                td.className ="";
                                td.innerHTML = ids[i]+"<input type=\"hidden\" name=\"'.$this -> name.'\"'
            .' id=\"'.$this -> id.'\" value=\""+ids[i]+"\">";
                                tr.appendChild(td);

                                tbody[0].appendChild(tr);

                            }';
        $script[] = '       }';
        if ($allowClear)
        {
            $script[] = '		var tzclear = document.getElementById("' . $this->id . '_clear");';
            $script[] = '		if(tzclear.getAttribute("class").match(/(.*?)\shidden\s?(.*?)/)){
                                    tzclear.setAttribute("class",tzclear.getAttribute("class").replace(/\shidden/,""));
                                };';
        }

        $script[] = '		SqueezeBox.close();';
        $script[] = '	}';

        // Clear button script
        static $scriptClear;

        if ($allowClear && !$scriptClear){

            $scriptClear = true;

            $script[] = '	function jClearArticle(id) {';
            $script[] = '	    var tztable = document.getElementById(id+"_table");';
            $script[] = '		var tbody = tztable.getElementsByTagName("tbody");';
            $script[] = '		tbody[0].innerHTML = "";';
            $script[] = '		jQuery("#"+id + "_clear").addClass("hidden");';
            $script[] = '		if (document.getElementById(id + "_edit")) {';
            $script[] = '			jQuery("#"+id + "_edit").addClass("hidden");';
            $script[] = '		}';
            $script[] = '		return false;';
            $script[] = '	}';
        }

        // Add the script to the document head.
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));


        // Setup variables for display.
        $html	= array();
        $link	= 'index.php?option=com_tz_portfolio_plus&amp;view=articles&amp;layout=modals&amp;tmpl=component&amp;function=jSelectArticle_'.$this->id;

        if (isset($this->element['language']))
        {
            $link .= '&amp;forcedLanguage=' . $this->element['language'];
        }

        if (empty($title)) {
            $title = JText::_('COM_TZ_PORTFOLIO_PLUS_SELECT_AN_ARTICLE');
        }
        $title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // The current user display field.
        // The current tag display field.
        $html[] = '<div class="input-append">';

        $title      = JText::_('COM_TZ_PORTFOLIO_PLUS_CHANGE_ARTICLES');
        $textLink   = '<i class="icon-copy"></i>&nbsp;'.JText::_('COM_TZ_PORTFOLIO_PLUS_FIELD_SELECT_ARTICLES');
        $class      = 'modal btn';

        // The active article id field.
        $value  = $this -> value;

        // The user select button.
        $html[] = '	<a class="modal btn" title="'.$title.'"'
            .' href="'.$link.'&amp;'.JSession::getFormToken().'=1" rel="{handler: \'iframe\', size: {x: 800, y: 450}}">'
            .$textLink.'</a>';

        // Clear article button
        if ($allowClear)
        {
            $html[] = '<a href="javascript:" id="' . $this->id . '_clear" class="btn' . ($value ? '' : ' hidden') . '" onclick="return jClearArticle(\'' . $this->id . '\')"><span class="icon-remove"></span> ' . JText::_('JCLEAR') . '</a>';
        }

        $html[] = '</div>';

        // class='required' for client side validation
        $class = '';
        if ($this->required) {
            $class = ' class="required modal-value"';
        }

        $html[] = $this ->_getHtml($this -> id,$value);

//        $html[] = '<input type="hidden" id="'.$this->id.'_id"'.$class.' name="'.$this->name.'" value="'.$value.'" />';

        return implode("\n", $html);
    }

    protected function _getHtml($id,$values = null){
    ?>
        <?php
        $tbody  = null;
        $old    = null;
        if($values){
            if($items = $this -> _getItems($values)){
                $allowEdit		= ((string) $this->element['edit'] == 'true') ? true : false;

                ob_start();
                foreach($items as $item){
                    ?>
                    <tr>
                        <td><?php echo $item -> title;?></td>
                        <td><?php echo $item -> category_title;?></td>
                        <td class="btn-group" style="display: table-cell; position: inherit;">
                            <?php if ($allowEdit){ ?>
                            <a class="btn btn-small" target="_blank"
                               href="index.php?option=com_tz_portfolio_plus&task=article.edit&id=<?php echo $item -> id;?>"><span class="icon-edit"></span> <?php echo JText::_('JACTION_EDIT')?></a>
                            <?php }?>
                            <a href="javascript:" class="btn btn-small"
                               onclick="<?php echo $id;?>Remove(this);"><i class="icon-remove"></i> <?php echo JText::_('JTOOLBAR_REMOVE');?></a>
                        </td>
                        <td>
                            <?php echo $item -> id;?>
                            <input type="hidden" name="<?php echo $this -> name;?>"
                                   value="<?php echo $item -> id;?>">
                        </td>
                    </tr>
                <?php
                }
                $tbody  = ob_get_contents();
                ob_end_clean();
            }
        }
        ?>
    <?php
        ob_start();
        ?>
        <div class="clearfix"></div>
        <div style="max-height: 330px; overflow-y: auto;">
            <table id="<?php echo $id.'_table';?>" class="table table-striped">
                <thead>
                    <tr>
                        <th><?php echo JText::_('JGLOBAL_TITLE');?></th>
                        <th><?php echo JText::_('JCATEGORY');?></th>
                        <th style="text-align:center; width: 18%;"><?php echo JText::_('JSTATUS');?></th>
                        <th style="width: 5%;"><?php echo JText::_('JGRID_HEADING_ID');?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php echo $tbody;?>
                </tbody>
            </table>
        </div>

        <?php
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    protected function _getItems($ids){
        if($ids){
            $db     = JFactory::getDbo();
            $query  = $db -> getQuery(true);
            $query -> select('a.id,a.title,c.title AS category_title');
            $query -> from('#__tz_portfolio_plus_content AS a');
            $query -> join('LEFT','#__tz_portfolio_plus_content_category_map AS m ON m.contentid = a.id');
            $query -> join('LEFT','#__tz_portfolio_plus_categories AS c ON c.id = m.catid');
            $query -> where('a.id IN('.$ids.')');
            $query -> group('id');
            $db -> setQuery($query);
            if($rows = $db -> loadObjectList()){
                return $rows;
            }
        }
        return false;
    }
}
