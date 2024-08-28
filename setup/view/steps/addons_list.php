<?php
/*------------------------------------------------------------------------

# TZ Portfolio Plus Extension

# ------------------------------------------------------------------------

# Author:    DuongTVTemPlaza

# Copyright: Copyright (C) 2011-2019 TZ Portfolio.com. All Rights Reserved.

# @License - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL

# Website: http://www.tzportfolio.com

# Technical Support:  Forum - https://www.tzportfolio.com/help/forum.html

# Family website: http://www.templaza.com

# Family Support: Forum - https://www.templaza.com/Forums.html

-------------------------------------------------------------------------*/

// no direct access
defined('_JEXEC') or die;

$unchecked  = false;
$lang       = JFactory::getLanguage();
?>
<script type="text/javascript">
$('[data-select-all]').on('change', function() {

	var parent = $(this).closest('li');
	var checkbox = parent.find('[data-checkbox]').not(":disabled");
	var selected = $(this).is(':checked');

	checkbox.prop('checked', selected);
});

$('[data-checkbox-module]').on('click', function() {
	var selected = $(this).is(':checked');
	if (! selected) {
		$('#module-all').prop('checked', false);
	} else {
		// find if there is any unchecked item or not.
		var parent = $(this).parents('[data-tab]');
		var unchecked = parent.find('[data-checkbox-module]').not(":checked");

		if (unchecked.length == 0) {
			$('#module-all').prop('checked', true);
		}
	}
});

$('[data-checkbox-plugin]').on('click', function() {
	var selected = $(this).is(':checked');
	if (! selected) {
		$('#plugin-all').prop('checked', false);
	} else {
		// find if there is any unchecked item or not.
		var parent = $(this).parents('[data-tab]');
		var unchecked = parent.find('[data-checkbox-plugin]').not(":checked");

		if (unchecked.length == 0) {
			$('#plugin-all').prop('checked', true);
		}
	}
});

$('[data-checkbox-addon]').on('click', function() {
	var selected = $(this).is(':checked');
	if (! selected) {
		$('#addon-all').prop('checked', false);
	} else {
		// find if there is any unchecked item or not.
		var parent = $(this).parents('[data-tab]');
		var unchecked = parent.find('[data-checkbox-addon]').not(":checked");

		if (unchecked.length == 0) {
			$('#addon-all').prop('checked', true);
		}
	}
});

$('[data-checkbox-style]').on('click', function() {
	var selected = $(this).is(':checked');
	if (! selected) {
		$('#style-all').prop('checked', false);
	} else {
		// find if there is any unchecked item or not.
		var parent = $(this).parents('[data-tab]');
		var unchecked = parent.find('[data-checkbox-style]').not(":checked");

		if (unchecked.length == 0) {
			$('#style-all').prop('checked', true);
		}
	}
});

</script>

<div id="modules" class="addons-list" data-tab>

	<ul class="list-reset">

        <?php if(isset($data -> modules) && $data -> modules){ ?>
		<li>
			<div class="checkbox check-all">
				<input type="checkbox" id="module-all" data-select-all checked="checked"<?php echo $install -> moduleDisabled?' disabled':''; ?>/>
				<label for="module-all">
					<div><?php echo JText::_('COM_TZ_PORTFOLIO_SETUP_INSTALL_MODULES'); ?></div>
				</label>
			</div>
            <ul class="list-reset">
                <?php
                foreach ($data->modules as $module) {
                    $lang -> load($module -> title, $modulesExtractPath.'/'.$module -> title);
                    ?>
                    <li>
                        <div class="checkbox">
                            <input type="checkbox" id="module-<?php echo $module->element; ?>" value="<?php
                            echo $module->element;?>" <?php echo $module->checked ? 'checked="checked"' : ''
                            ?> data-checkbox data-checkbox-module <?php echo $module->disabled ? 'disabled':''; ?> />
                            <label for="module-<?php echo $module->element; ?>">
                                <?php echo JText::_($module->title);?>
                            </label>
                        </div>
                    </li>
                    <?php if (!$module->checked) { ?>
                        <?php $unchecked = true; ?>
                    <?php } ?>
                <?php } ?>
            </ul>
		</li>
        <?php } ?>

        <?php if(isset($data -> plugins) && $data -> plugins){ ?>
		<li>
			<div class="checkbox check-all">
				<input type="checkbox" id="plugin-all" data-select-all checked="checked"<?php echo $install -> pluginDisabled?' disabled':''; ?>/>
				<label for="plugin-all">
					<div><?php echo JText::_('COM_TZ_PORTFOLIO_SETUP_INSTALL_PLUGINS'); ?></div>
				</label>
			</div>
            <ul class="list-reset">
                <?php foreach ($data->plugins as $plugin) {
                        $lang -> load($plugin -> title, $pluginsExtractPath.'/'.$plugin -> group.'/'.$plugin -> element);
                        ?>
                        <li>
                            <div class="checkbox">
                                <input type="checkbox" id="plugin-<?php echo $plugin->group . '-' . $plugin->element;
                                ?>" value="<?php echo $plugin->element;?>" data-group="<?php echo $plugin->group;
                                ?>" checked="checked" data-checkbox data-checkbox-plugin <?php echo $plugin->disabled ? 'disabled':''; ?>/>
                                <label for="plugin-<?php echo $plugin->group . '-' . $plugin->element; ?>">
                                    <?php echo JText::_($plugin->title); ?>
                                </label>
                            </div>
                        </li>
                    <?php } ?>
            </ul>
		</li>
        <?php } ?>

        <?php if(isset($data -> styles) && $data -> styles && count($data -> styles)){ ?>
            <li>
                <div class="checkbox check-all">
                    <input type="checkbox" id="style-all" data-select-all checked="checked"<?php echo $install -> styleDisabled?' disabled':''; ?>/>
                    <label for="style-all">
                        <div><?php echo JText::_('COM_TZ_PORTFOLIO_SETUP_INSTALL_STYLES'); ?></div>
                    </label>
                </div>
                <ul class="list-reset">
                    <?php
                    foreach ($data->styles as $style) {
                        $lang -> load('tp_addon_'.$style -> title, $stylesExtractPath.'/'.$style -> title);
                        ?>
                        <li>
                            <div class="checkbox">
                                <input type="checkbox" id="style-<?php echo $style->element; ?>" value="<?php
                                echo $style->element;?>" <?php echo $style->checked ? 'checked="checked"' : ''
                                ?> data-checkbox data-checkbox-style <?php echo $style->disabled ? 'disabled':''; ?> />
                                <label for="style-<?php echo $style->element; ?>">
                                    <?php echo JText::_($style->title);?>
                                </label>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

        <?php if(isset($data -> addons) && $data -> addons){ ?>
		<li>
			<div class="checkbox check-all">
				<input type="checkbox" id="addon-all" data-select-all checked="checked"<?php echo $install -> addonDisabled?' disabled':''; ?>/>
				<label for="addon-all">
					<div><?php echo JText::_('COM_TZ_PORTFOLIO_SETUP_INSTALL_ADDONS'); ?></div>
				</label>
			</div>
            <ul class="list-reset">
                <?php
                    foreach ($data->addons as $addon) {
                        $lang -> load('tp_addon_'.$addon -> group.'_'.$addon -> element,
                            $addonsExtractPath.'/'.$addon -> group.'/'.$addon -> element);
                        ?>
                        <li>
                            <div class="checkbox">
                                <input type="checkbox" id="addon-<?php echo $addon->group . '-' . $addon->element;
                                ?>" value="<?php echo $addon->element;?>" data-group="<?php echo $addon->group;
                                ?>" checked="checked" data-checkbox data-checkbox-addon <?php echo $addon->disabled ? 'disabled':''; ?>/>
                                <label for="addon-<?php echo $addon->group . '-' . $addon->element; ?>">
                                    <?php echo JText::_($addon->title); ?>
                                </label>
                            </div>
                        </li>
                    <?php } ?>
            </ul>
		</li>
        <?php } ?>
	</ul>
</div>

<?php if ($unchecked) { ?>
<script type="text/javascript">
$('[data-select-all]').prop('checked', false);
</script>
<?php } ?>
