<?php
/**
 * @copyright	Copyright (c) 2013 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die;
?>
<div class="tpButton">
    <a href="<?php echo $this->button['link']; ?>">
        <?php echo JHtml::_('image', $this->button['image'], null, null, false); ?>
        <div>
            <?php echo $this->button['text']; ?>
        </div>
    </a>
</div>


