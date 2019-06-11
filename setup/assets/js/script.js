
$(document).ready(function(){

    $(document.body).on('change', '.btn-group input:radio', function () {
        var $this = $(this);
        var $group = $this.closest('.btn-group');
        var name = $this.prop('name');
        var reversed = $group.hasClass('btn-group-reversed');

        $group.find('input:radio[name="' + name + '"]').each(function () {
            var $input = $(this);
            // Get the enclosing label
            var $label = $input.closest('label');
            var inputId = $input.attr('id');
            var inputVal = $input.val();
            var btnClass = 'primary';

            // Include any additional labels for this control
            if (inputId) {
                $label = $label.add($('label[for="' + inputId + '"]'));
            }

            if ($input.prop('checked')) {
                if (inputVal != '') {
                    btnClass = (inputVal == 0 ? !reversed : reversed) ? 'danger' : 'success';
                }

                $label.addClass('active btn-' + btnClass);
            } else {
                $label.removeClass('active btn-success btn-danger btn-primary');
            }
        });
    });

    $('.hasTooltip').tooltip();

    loading = $('[data-installation-loading]'),
        submit = $('[data-installation-submit]'),
        retry = $('[data-installation-retry]'),
        form = $('[data-installation-form]'),
        completed = $('[data-installation-completed]'),
        source = $('[data-source]'),
        installExtensions = $('[data-installation-install-addons]'),
        steps = $('[data-installation-steps]');
});
var tpp = {

    init: function() {
    },

    options: {
        "token_key": "<?php echo $input->get('token_key', '');?>",
        "path": null,
        "controller": "install"
    },
    ajaxUrl: "<?php echo JURI::root();?>administrator/index.php?option=com_tz_portfolio_plus&ajax=1",

    ajax: function(task, properties, callback) {

        var prop = $.extend(tpp.options, properties);

        var dfd = $.Deferred();

        $.ajax({
            type: "POST",
            url: tpp.ajaxUrl + "&task=" + prop.controller + "."  + task,
            data: prop
        }).done(function(result) {
            callback && callback.apply(this, [result]);

            dfd.resolve(result);
        });

        return dfd;
    },

    addons: {

        installModule: function(element, path) {

            return tpp.ajax('install', {
                "controller": "addons_installmodule",
                "path": path,
                "module": element
            });

        },

        installPlugin: function(plugin, path) {
            return tpp.ajax('install', {
                "controller": "addons_installplugin",
                "path": path,
                "element": plugin.element,
                "group": plugin.group
            });
        },

        installStyle: function(element, path) {

            return tpp.ajax('install', {
                "controller": "addons_installstyle",
                "path": path,
                "style": element
            });

        },

        installAddon: function(addon, path) {
            return tpp.ajax('install', {
                "controller": "addons_installaddon",
                "path": path,
                "element": addon.element,
                "group": addon.group
            });
        },

        runScript: function(script) {
            // Run the maintenace scripts
            return $.ajax({
                type: 'POST',
                url: tpp.ajaxUrl + '&controller=maintenance&task=execute',
                data: {
                    script: script
                }
            });
        },

        retrieveList: function() {

            var progress = $('[data-addons-progress]');
            var selection = $('[data-addons-container]');
            var syncProgress = $('[data-sync-progress]');

            // Show loading
            loading.removeClass('hide');

            // Hide submit
            submit.addClass('hide');

            tpp.ajax('lists', {"controller": "addons", "path": tpp.options.path}, function(result){

                // Hide the retrieving message
                $('[data-addons-retrieving]').addClass('hide');

                loading.addClass('hide');
                installExtensions.removeClass('hide');

                selection.html(result.html);

                // Get files for maintenance
                var scripts = result.scripts;
                var maintenanceMsg = result.maintenanceMsg;

                // Set the submit
                installExtensions.on('click', function() {

                    // Hide the container
                    selection.addClass('hide');

                    // Show the installation progress
                    progress.removeClass('hide');
                    syncProgress.removeClass('hide');

                    // Install the selected items
                    var modules = [];
                    var plugins = [];
                    var styles  = [];
                    var addons  = [];

                    $('[data-checkbox-module]:checked').each(function(i, el) {
                        modules.push($(el).val());
                    });

                    $('[data-checkbox-plugin]:checked').each(function(i, el) {
                        var plugin = {
                            "element": $(el).val(),
                            "group": $(el).data('group')
                        };

                        plugins.push(plugin);
                    });

                    $('[data-checkbox-style]:checked').each(function(i, el) {
                        styles.push($(el).val());
                    });

                    $('[data-checkbox-addon]:checked').each(function(i, el) {
                        var addon = {
                            "element": $(el).val(),
                            "group": $(el).data('group')
                        };

                        addons.push(addon);
                    });

                    var total = modules.length + plugins.length + styles.length + addons.length;
                    var each = 100 / total;
                    var progressBar = $('[data-progress-bar]');
                    var progressBarResult = $('[data-progress-bar-result]');

                    var totalScripts = scripts.length;
                    var eachScript = 100 / totalScripts;
                    var syncProgressBar = $('[data-sync-progress-bar]');
                    var syncProgressBarResult = $('[data-sync-progress-bar-result]');

                    // var runMaintenance = function() {
                    //
                    //     var frame = $('[data-progress-execscript]');
                    //
                    //     frame.addClass('active')
                    //         .removeClass('pending');
                    //
                    //     var item = $('<li>');
                    //     item.addClass('text-success').html(maintenanceMsg);
                    //
                    //     $('[data-progress-execscript-items]').append(item);
                    //
                    //     var scriptIndex = 0,
                    //         dfd = $.Deferred();
                    //
                    //     var runNextScript = function() {
                    //         if (scripts[scriptIndex] == undefined) {
                    //
                    //             $.ajax({
                    //                 type: 'POST',
                    //                 url: tpp.ajaxUrl + '&controller=maintenance&task=finalize'
                    //             }).done(function(result) {
                    //                 var item = $('<li>');
                    //                 item.addClass('text-success').html(result.message);
                    //                 $('[data-progress-execscript-items]').append(item);
                    //
                    //                 $('[data-progress-execscript]')
                    //                     .find('.progress-state')
                    //                     .html(result.stateMessage)
                    //                     .addClass('text-success')
                    //                     .removeClass('text-info');
                    //             });
                    //
                    //             dfd.resolve();
                    //             return;
                    //         }
                    //
                    //         tpp.addons
                    //             .runScript(scripts[scriptIndex])
                    //             .done(function(data) {
                    //                 scriptIndex++;
                    //
                    //                 // update the progress bar here
                    //                 var currentWidth = parseInt(syncProgressBar[0].style.width);
                    //                 var percentage = Math.round(currentWidth + eachScript);
                    //
                    //                 syncProgressBar.css('width', percentage + '%');
                    //                 syncProgressBarResult.html(percentage + '%');
                    //
                    //                 var item = $('<li>'),
                    //                     className = data.state ? 'text-success' : 'text-error';
                    //
                    //                 item.addClass(className).html(data.message);
                    //
                    //                 $('[data-progress-execscript-items]').append(item);
                    //
                    //                 runNextScript();
                    //             });
                    //
                    //     };
                    //
                    //     runNextScript();
                    //
                    //     return dfd;
                    // };

                    var installModules = function() {

                        var moduleIndex = 0,
                            dfd = $.Deferred();

                        var installNextModule = function() {
                            if (modules[moduleIndex] == undefined) {

                                dfd.resolve();
                                return;
                            }

                            tpp.addons
                                .installModule(modules[moduleIndex], result.modulePath)
                                .done(function(data) {
                                    moduleIndex++;

                                    var currentWidth = parseInt(progressBar[0].style.width);
                                    var percentage = Math.round(currentWidth + each);

                                    $('[data-progress-active-message]').html(data.message);

                                    progressBar.css('width', percentage + '%');
                                    progressBarResult.html(percentage + '%');

                                    installNextModule();
                                });
                        };

                        installNextModule();

                        return dfd;
                    };

                    var installPlugins = function() {

                        var pluginIndex = 0;
                        var dfd = $.Deferred();


                        var installNextPlugin = function() {

                            if (plugins[pluginIndex] == undefined) {

                                dfd.resolve();
                                return;
                            }

                            tpp.addons.installPlugin(plugins[pluginIndex], result.pluginPath)
                                .done(function(data) {

                                    pluginIndex++;

                                    var progressBarResult = $('[data-progress-bar-result]');
                                    var currentWidth = parseInt(progressBar[0].style.width);
                                    var percentage = Math.round(currentWidth + each) + '%';

                                    $('[data-progress-active-message]').html(data.message);

                                    // Update the width of the progress bar
                                    progressBar.css('width', percentage);

                                    // We need to update the progress bar here
                                    progressBarResult.html(percentage);

                                    installNextPlugin();
                                });
                        };

                        installNextPlugin();

                        return dfd;
                    };

                    var installStyles = function() {

                        var styleIndex = 0,
                            dfd = $.Deferred();

                        var installNextStyle = function() {
                            if (styles[styleIndex] == undefined) {

                                dfd.resolve();
                                return;
                            }

                            tpp.addons
                                .installStyle(styles[styleIndex], result.stylePath)
                                .done(function(data) {
                                    styleIndex++;

                                    var currentWidth = parseInt(progressBar[0].style.width);
                                    var percentage = Math.round(currentWidth + each);

                                    $('[data-progress-active-message]').html(data.message);

                                    progressBar.css('width', percentage + '%');
                                    progressBarResult.html(percentage + '%');

                                    installNextStyle();
                                });
                        };

                        installNextStyle();

                        return dfd;
                    };

                    var installAddons = function() {

                        var addonIndex = 0;
                        var dfd = $.Deferred();


                        var installNextAddon = function() {

                            if (addons[addonIndex] == undefined) {

                                dfd.resolve();
                                return;
                            }

                            tpp.addons.installAddon(addons[addonIndex], result.addonPath)
                                .done(function(data) {

                                    addonIndex++;

                                    var progressBarResult = $('[data-progress-bar-result]');
                                    var currentWidth = parseInt(progressBar[0].style.width);
                                    var percentage = Math.round(currentWidth + each) + '%';

                                    $('[data-progress-active-message]').html(data.message);

                                    // Update the width of the progress bar
                                    progressBar.css('width', percentage);

                                    // We need to update the progress bar here
                                    progressBarResult.html(percentage);

                                    installNextAddon();
                                });
                        };

                        installNextAddon();

                        return dfd;
                    };

                    // Show loading indicator
                    loading.removeClass('hide');
                    installExtensions.addClass('hide');

                    // Install Modules
                    installModules().done(function() {
                        installPlugins().done(function() {
                            installStyles().done(function() {
                                installAddons().done(function() {

                                    // Show complete
                                    $('[data-progress-active-message]').addClass('hide');
                                    $('[data-progress-complete-message]').removeClass('hide');
                                    $('[data-progress-bar]').css('width', '100%');
                                    $('[data-progress-bar-result]').html('100%');

                                    loading.addClass('hide');
                                    submit.removeClass('hide');

                                    submit.on('click', function() {
                                        form.submit();
                                    });

                                    // runMaintenance().done(function() {
                                    //
                                    //     // When everything is done, update the submit button
                                    //     loading.addClass('hide');
                                    //     submit.removeClass('hide');
                                    //
                                    //     $('[data-sync-progress-active-message]').addClass('hide');
                                    //     $('[data-sync-progress-complete-message]').removeClass('hide');
                                    //     $('[data-sync-progress-bar]').css('width', '100%');
                                    //     $('[data-sync-progress-bar-result]').html('100%');
                                    //
                                    //     submit.on('click', function() {
                                    //         form.submit();
                                    //     });
                                    // })
                                });
                            });
                        });
                    });
                });
            });
        }
    },

    installation: {
        path: null,

        showRetry: function(step) {

            steps.addClass('error');

            retry
                .data('retry-step', step)
                .removeClass('hide');

            // Hide the submit
            submit.addClass('hide');

            // Hide the loading
            loading.addClass('hide');
        },

        activePro: function() {

            tpp.installation.setActive('data-progress-active-pro');

            tpp.ajax('activepro', {
                "produce": "tz-portfolio-plus",
                "license": $("[data-license]").val()
            }, function(result) {

                // Update the progress
                tpp.installation.update('data-progress-active-pro', result, '10%');

                if (!result.state) {
                    tpp.installation.showRetry('active-pro');
                    return false;
                }

                // Set the path
                tpp.options.path = result.path;

                // Run the next command
                tpp.installation.extract();
            });
        },
        extract: function() {

            tpp.installation.setActive('data-progress-extract');

            tpp.ajax('extract', {}, function(result) {

                // Update the progress
                tpp.installation.update('data-progress-extract', result, '10%');

                if (!result.state) {
                    tpp.installation.showRetry('extract');
                    return false;
                }

                // Set the path
                tpp.options.path = result.path;

                // Run the next command
                tpp.installation.runSQL();
            });
        },

        download: function() {

            tpp.installation.setActive('data-progress-download');

            tpp.ajax('download', {}, function(result) {

                // Set the progress
                tpp.installation.update('data-progress-download', result, '10%');

                if (!result.state) {
                    tpp.installation.showRetry('download');
                    return false;
                }

                // Set the installation path
                tpp.options.path = result.path;

                tpp.installation.runSQL();
            });
        },

        runSQL: function() {

            // Install the SQL stuffs
            tpp.installation.setActive('data-progress-sql');

            tpp.ajax('initialize', {
                "controller": "install_sql",
                "sample_data": $("[data-sample-data]").val() }, function(result) {

                // Update the progress
                tpp.installation.update('data-progress-sql', result, '15%');

                if (!result.state) {
                    tpp.installation.showRetry('runSQL');
                    return false;
                }

                // Run the next command
                tpp.installation.installAdmin();
            });
        },

        installAdmin: function() {

            // Install the admin stuffs
            tpp.installation.setActive('data-progress-admin');

            // Run the ajax calls now
            tpp.ajax('initialize', {
                "controller": "install_copy",
                "type": "admin" }, function(result) {

                // Update the progress
                tpp.installation.update('data-progress-admin', result, '20%');

                if (!result.state) {
                    tpp.installation.showRetry('installAdmin');
                    return false;
                }

                tpp.installation.installSite();
            });
        },

        installSite : function() {

            // Install the admin stuffs
            tpp.installation.setActive('data-progress-site');

            tpp.ajax('initialize', {
                "controller": "install_copy",
                "type" : "site" }, function(result) {


                // Update the progress
                tpp.installation.update('data-progress-site', result, '25%');

                if (!result.state) {
                    tpp.installation.showRetry('installSite');
                    return false;
                }

                // tpp.installation.installLanguages();

                completed
                    .removeClass('hide')
                    .show();

                loading
                    .addClass('hide');

                submit
                    .removeClass('hide');

                submit.on('click', function() {

                    source.val(tpp.options.path);

                    form.submit();
                });
            });
        },

        syncDB: function() {

            // Synchronize the database
            tpp.installation.setActive('data-progress-syncdb');

            tpp.ajax('sync', {}, function(result) {
                tpp.installation.update('data-progress-syncdb', result, '45%');

                if (!result.state) {
                    tpp.installation.showRetry('syncDB');
                    return false;
                }

                tpp.installation.postInstall();
            });
        },

        postInstall : function() {

            // Perform post installation stuffs here
            tpp.installation.setActive('data-progress-postinstall');

            tpp.ajax('post', {}, function(result) {

                // Set the progress
                tpp.installation.update('data-progress-postinstall', result, '100%');

                if (!result.state) {
                    tpp.installation.showRetry('postInstall');
                    return false;
                }

                completed
                    .removeClass('hide')
                    .show();

                loading
                    .addClass('hide');

                submit
                    .removeClass('hide');

                submit.on('click', function() {

                    source.val(tpp.options.path);

                    form.submit();
                });

            });
        },

        update: function(element, obj, progress) {
            var className = obj.state ? ' text-primary' : ' text-warning',
                stateMessage = obj.state ? 'Success' : 'Failed';
            stateIcon = obj.state ? 'icon-checkmark text-primary' : 'icon-warning text-warning';

            // Update the icon
            $('[' + element + ']')
                .find('.progress-icon > i')
                .removeClass('loader-square ')
                .addClass(stateIcon);

            // Update the state
            $('[' + element + ']')
                .find('.progress-state')
                .html(stateMessage)
                .removeClass('text-info')
                .addClass(className);

            // Update the message
            $('[' + element + ']')
                .find('.notes')
                .html(obj.message)
                .removeClass('text-info')
                .addClass(className);

            $('[' + element + ']').removeClass('is-loading');
        },

        setActive: function(item) {
            $('[data-progress-active-message]').html($('[' + item + ']').find('.split__title').html() + ' ...');
            $('[' + item + ']').removeClass('pending').addClass('active is-loading');
            $('[' + item + ']').find('.progress-icon > i') .removeClass('icon-checkbox-unchecked') .addClass('loader-square');
        }
    }
}