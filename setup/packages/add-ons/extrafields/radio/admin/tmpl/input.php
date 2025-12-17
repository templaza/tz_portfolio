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

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;

/* @var \TemPlaza\Component\TZ_Portfolio\AddOn\Extrafields\Radio\Extension\Radio $this */
$html = "";
if ($options)
{
    $switcherLabel      = null;
//    $isAdmin            = Factory::getApplication() -> isClient('administrator');
    $switcher           = $this -> params -> get('switcher', 0) && count($options) <= 2;
    $number_columns     = $this -> params -> get("number_columns", 0);
    $bootstrap_style    = $this -> params -> get('bootstrap_style',1);
    $versionCompare     = version_compare(JVERSION, '4.0', 'ge');

    $class  = 'radio ';
    $attrib = '';

    if($switcher){
        $class .= 'switcher';
    }
?>

<div id="<?php echo $this -> getId(); ?>" class="<?php echo $class; ?>"<?php echo $attrib;?>>
    <?php
    $labelAttribs = array(
            'class' => 'form-check-label'
    );

    $labelAttrib    = !empty($labelAttribs)?' '.ArrayHelper::toString($labelAttribs):'';

    ?>

    <?php foreach($options as $key => $option): ?>
        <?php
    //        $classAttrib    = $this -> getAttribute('class', '', 'input');

        if ($option->text == strtoupper($option->text))
        {
            $text = Text::_($option->text);
        }
        else
        {
            $text = $option->text;
        }
        $text = htmlspecialchars($text, ENT_COMPAT, 'UTF-8');

        $this->setAttribute("value", htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8'), "input");

    //        if($switcher && $key == 0){
    //            $this -> setAttribute("class", "active", "input");
    //        }else{
    //            $this -> setAttribute("class", "", "input");
    //        }

    //        if($isAdmin){
    //            $classAttrib    .= '';
    //        }

        if (($value && $option->value === $value) || ($switcher && !$value && $key == 0))
        {
            $this->setAttribute("checked", "checked", "input");
        }
        else
        {
            $this->setAttribute("checked", null, "input");
        }

        if ((isset($option->disabled) && $option->disabled))
        {
            $this->setAttribute("disabled", "disabled", "input");
        }
        else
        {
            $this->setAttribute("disabled", null, "input");
        }
        ?>

        <?php if(!$switcher){ ?>
            <div class="form-check">
        <?php }?>
        <input id="<?php echo $this->getId() . $key; ?>" name="<?php echo $this->getName(); ?>" <?php
        echo $this->getAttribute(null, null, "input"); ?> />
        <label<?php echo $labelAttrib; ?> for="<?php echo $this -> getId() . $key;?>"><?php echo $text;?></label>
        <?php if(!$switcher){ ?>
            </div>
        <?php }?>
    <?php endforeach; ?>
    <?php if($switcher){ ?>
    <div class="toggle-outside"><span class="toggle-inside"></span></div>
    <?php } ?>
</div>

<?php
}