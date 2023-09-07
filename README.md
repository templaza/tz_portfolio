![GitHub Release Date](https://img.shields.io/github/release-date/templaza/tz_portfolio_plus)
![GitHub commit activity](https://img.shields.io/github/commit-activity/m/templaza/tz_portfolio_plus)
[![GitHub release](https://img.shields.io/github/release/templaza/tz_portfolio_plus.svg)](https://github.com/templaza/tz_portfolio_plus/releases)
![GitHub](https://img.shields.io/github/license/templaza/tz_portfolio_plus)
![Follow Us](https://img.shields.io/twitter/follow/templazavn?style=social)

# TZ Portfolio+ | Joomla 3.x

TZ Portfolio is a really good portfolio developing extensions for Joomla that allows users to display, style and manage their portfolio easily.

Plus it has all layouts and multi-categories you would need for a portfolio with two basic views: Portfolio and Single Article view.

Documentation: http://tzportfolio.com/document.html

Demo: http://demo.tzportfolio.com/

*** Changelog ***

07/09/2023 - 2.6.7

    - Fix issue of language files
    - Fix issue load add-on xml file
    - Fix issue load multiple tags in article edit form
    - Fix issue image file does not exist of tzmedia field
    - Fix issue infinitescroll when disable isotope script    
    - Added option Search word from introtext

22/05/2023 - 2.6.6

    - Fix issue image watermark with php8
    - Fix issue disable Secondary Category when Main Category changed in article form
    - Update joomla 4 list fancy select layout for fields

03/04/2023 - 2.6.5
	
	- Fix issue extrafield value in article of version 2.6.4

30/03/2023 - 2.6.4
	
	- Fix issue Deprecated with php8.2:
	    + Compatible with php8.2
	- Fix issue save article on front-end
	- Fix issue of edit layout in back-end
	- Fix issue option Layout style for modules
	- Fix issue load add-on's xml file in back-end
	- Fix issue image preview of tzmedia field in back-end
	- Fix issue display svg image of extrafield on front-end
	- Fix issue show article's Finish Publishing option in back-end
	- Fix issue js Joomla.loadingLayer is not a function of tzextrafieldtypes
	- Fix issue sortable of views: articles, addons, categories,... in back-end
	- Added Date for Ordering option with Unpublished for Portfolio, Date Articles, Search views
	- Update enable or disable link tag option
	- Update options Submission - Cancel Redirect for create article view
	- Update order by random for views: Portfolio, Date Articles, Search and TZ Portfolio Plus Portfolio module

16/02/2023 - 2.6.3
	
	- Fix issue remove image files of image add-on
	- Fix issue undefined function setError of form controller library
	- Fix issue filter by empty category of module TZ Portfolio Plus Filter when this module refer to portfolio view
	- Update tzfont design
	- Update Dropdown List Addon
	- Update image for extrafield
	- Update TZ Portfolio Plus Filter:
	    + Update label style
    	+ Update Button Style
    	+ Update Filter style

02/11/2022 - 2.6.2
	
	- Fix issue with about author in article view
	- Fix issue Article Listing Columns in date view
	- Fix issue option layout type in portfolio menu

09/09/2022 - 2.6.1
	
	- Comparetible with php 8.x
	- Fix issue file url is not string
	- Fix issue multifield value is null
	- Fix deprecated warning of tzcheckbox
	- Fix issue $extension is null
	- Update calendar field for Article

14/06/2022 - 2.6.0
	
	- Fix issue class "JError" not found on front-end
	- Fix issue Field 'description' doesn't have a default when create tag in back-end. 
	- Fix issue the image file does not exist in back-end.
	- Fix issue Class 'TZ_Portfolio_PlusFrontHelper' not found of module TZ Portfolio Plus Portfolio.
	- Fix issue sortable items on mobile of add-ons (image gallery, grid gallery...) used jQuery ui sortable on mobile.

17/05/2022 - 2.5.9
	
	- Fix issue js window load event with jQuery v3.x
	- Fix issue remove preset

06/05/2022 - 2.5.8
	
	- Fix issue display message article alias if it exists.
	- Fix issue lightbox preview with image detail of add-on image.
	- Fix issue self url.
	- Fix issue load preset in backend.
	- Fix issue title tag is "h2" when disable page heading in single article view

13/04/2022 - 2.5.7
	
	- Fix issue save html of textarea custom field.
	- Fix issue load module and load position.
	- Fix issue search add-on, style to install/update in back-end
	- Update order add-ons, styles need update first in install/update list

19/03/2022 - 2.5.6
	
	- Fix issue font, css when setup.
    - Fix issue require add-ons on front-end (Don't use JSearch add-on)
    - Fix issue introguide with Joomla 4
    - Fix issue sef url of Single article menu item with Joomla 4
    - Update permission for each items in admin dashboard

09/02/2022 - 2.5.5
	
	- Fix issue "Call to a member function loadFile()" when add/edit article.

28/01/2022 - 2.5.4
	
	- Fix issue email icon of portfolio view with Joomla 4.
	- Fix issue array key "text/javascript" in tz_portfolio_plus.php helper file in joomla 4.
	- Fix issue style of module TZ Portfolio Plus with bootstrap 5 (remove class row in div masonry tag).

18/11/2021 - 2.5.3
	
	- Fix 'getErrorMsg' is deprecated in Joomla 4.
	- Fix issue search in field modals (article, category).
	- Fix issue choose category in create article menu with Joomla 4.0.4
	- Fix issue and update color picker (spectrum) js v1.8.1.

15/10/2021 - 2.5.2
	
	- Fix error email icon in elegant's portfolio view with joomla 4.
	- Fix issue the image file does not exist of image add-on.

06/10/2021 - 2.5.1
	
	- Fix error Form::loadForm could not load file with Joomla 4.
	- Fix issue module's parameters with joomla 3.9.x 
	- Fix issue save tag's title.

25/08/2021 - 2.5.0
	
	- Compatible with joomla 4.

24/06/2021 - 2.4.9
	
	- Fix issue with Image Properties
	- Fix error class not found of create article view

17/05/2021 - 2.4.8
	
	- Update style for tzfont, update image addon
	- Remove override Image Addon in elegant style
	- Fix error router when article like alias
	- Remove unnecessary space
	- Update getimagesize of add-on image
	- Update image url of add-on image

09/03/2021 - 2.4.7
	
	- Fix error get_magic_quotes_gpc() in php7.4.
	- Update language, Fix issue with responsive box, Add new feature for responsive box.

25/02/2021 - 2.4.6
	
	- Fix error load add-on content data.
	- Fix error load data by object type of tzgallery field.
	- Fix error assign multiple articles when filter in layout.
	- Fix error config of layout builder when the style after install.

03/02/2021 - 2.4.5
	
	- Fix error add-on language.
	- Fix error mediatype add-on conflict with content add-on.

27/01/2021 - 2.4.4
	
	- Fix error awesome font
	- Fix error field languages.
	- Fix error can not save add-on content data.

26/01/2021 - 2.4.3
	
	- Fix error related articles by tag.
	- Fix warning can not load add-ons from server in back-end.
	- Fix error load mootools in article form.
	- Fix error social line breaks(Update style System v1.2 and Elegant v2.9).
	- Added layout grid for categories view (List All Categories - Grid).
	- Added option "Show Image" for TZ Portfolio Plus Portfolio and TZ Portfolio Plus Articles modules.
	- Added option "Category Order" for TZ Portfolio Plus Portfolio module.
	- Added fields: tzicon, tzfont, tzmargin, tzpadding.
	- Added media files to install in package.
	- Update bootstrap v4.5.3
	- Update Fontawesome v5.15.1

10/06/2020 - 2.4.2
	
	- Fix error don't load layout list of Layout style.
	- Fix error installed extension in extension view.
	- Fix error default config of layout builder when install style.
	- Fix error show category's title, image, description in portfolio view when this view choose 1 category.
	- Added option "Browser Page Title" for article.
	- Added lightbox for article of image addon.
	- Added option "Enable Lazyload" to views: Portfolio, Date Articles, List All Categories, Search, Single Article.
	- Update elegant style (update lightbox for article of image addon).
	- Update lightbox jquery library v3.5.7

17/03/2020 - 2.4.1
	
	- Active domain for personal license.
	- Fix error active pro when install or update.
	- Fix some language of setup wizard

07/03/2020 - 2.4.0
	
	- Update image lazyload.
	- Added button Get Free license in TZ Portfolio Dashboard.

30/01/2020 - 2.3.9
	
	- Fix error rtl of masonry.
	- Fix error with breadcrumb.
	- Added options for module TZ Portfolio Plus Tags: "Show Article Counter", "Show Tag All", "Tag All Text", "Choose Menu".
	- Added article order by Priority for module TZ Portfolio Plus Portfolio.
	- Optimize code generate css from scss.
	- Optimize feedblog of dashboard in admin.
	- Optimize load list items from our server of Add-on, Style, Extensions in back-end.

13/12/2019 - 2.3.8
	
	- Fix issue: language strings didn't show value for addons and styles properly in back-end.

09/12/2019 - 2.3.7
	
	- Fix error delete of user's presets which created when install/update style.
	- Create trigger: onTPAddonIsLoaded for dev (this event helps developer to create custom type for single layout builder from a plugin).
	- Update keep user's language of add-on and style when install/update them.
	- Optimize code generate single layout builder in front-end.
	- Optimize code of addon user profile.
	- Update Elegant style v2.7: Update style for filter dropdown (portfolio view).

16/11/2019 - 2.3.6
		
	- Fix error show category info in portfolio view.
	- Allow create color params for style used by SASS.
	- Allow filter articles by secondary categories in back-end.
	- Update elegant style v2.6:
		+ Change style for elegant.
		+ Update options allowing to show category info in portfolio view.

05/11/2019 - 2.3.5
	
	- Fix error basic.scss net::ERR_ABORTED 404 (Not Found)
	- Fix error trying to get property of non-object in administrator\components\com_tz_portfolio_plus\libraries\helper\modulehelper.php
	- Create some triggers: onTPContentBeforePrepare, onTPExtraFieldPreapare, onTPContentAfterPrepare, onTPAddOnProcess... for add-on dev.
	- Add options allowing to show category info in portfolio view.

21/10/2019 - 2.3.4
	
	- Fix error url with sh404 component.
	- Fix error remove add-on and style when install or update it
	- Added 2 files css again: all.min.css and v4-shims.min.css
	- Optimize front-end's js

30/09/2019 - 2.3.3
	
	- Fix error styles of v2.3.2

27/09/2019 - 2.3.2
	
	- Fix error warning of feed portfolio view.
	- Fix error when install component with Joomla 3.8.x
	- Fix warning countable of addon in back-end.
	- Fix error show vote of v2.3.1
	- Fix error offset css of layout builder with bootstrap 4.	
	- Use SASS instead of CSS.
	- Compatible with Joomla 4 Alpha 11.

18/09/2019 - 2.3.1
	
	- Fix error Can't save user profile information.
	- Fix error Can not read property 'setAttribute' of null at Object.onLoad popper.min.js
	- Remove google+ in User profile manager.
	- Fix issue menu-alias/category-alias/article-alias:
		+ Added options "Remove category" and "Prefix Portfolio URLs" in global config.
	- Set default config is bootstrap 4.
	- Update Elegant style with Bootstrap 4.
	- Improve UX on TZ Portfolio+ is Free message in back-end.
	- Remove style, addon folder before them install or update completed.
	- Add Extensions Manager view in the back-end
	- Add Notification when addon or style has new version.
	- Add pop-up confirm install Sample data on Install Process.

10/08/2019 - 2.3.0
	
	- Fix error js of ajax button or Ajax infinitescroll when disable filter.
	- Fix error display white page of portfolio view on IE 11.
	- Fix error load ajax of portfolio view when search key word from module TZ Portfolio Plus Filter.
	- Fix error overload of edit layout in back-end.
	- Changed style active pro version in back-end.
	- Added delete license in dashboard with activated pro version.
	- Added bootstrap v4.3.1 library (customized - Added parent class "tpp-bootstrap").
	- Added option "Enable Bootstrap Library" in modules: TZ Portfolio Plus Categories, TZ Portfolio Plus Portfolio, TZ Portfolio Plus Tags, TZ Portfolio Plus Archived Articles.

30/07/2019 - 2.2.9
	
	- Fix error don't set margin of layout.
	- Fix error display about author.
	- Fix error save add-on option in module.
	- Fix error redirect to panel in edit layout.
	- Fix error front-end link when installed component.
	- Fix error icon move of row block in Single Layout Builder.
	- Update limited content to 50.
	- Add watermark for image add-on.
	- Add notice "Under Contruction" if install process is not finished.
	- Load default.json data when template configure is null.
	- Allow download/upload preset in template preset.
	- Add update notice in panel:
	    + Added plugin Quickicon - TZ Portfolio Plus.
	- Added some lanugages of setup to language file.

26/06/2019 - 2.2.8

	- Fix error cache of portfolio view with filter fields of version 2.2.7.
	- Fix language of installation wizard.
	- Fix error don't display image of v2.2.7
	- Fix error 1054 Unknown column 'asset_id' in 'field list' when update old version to v2.2.7
	- Added some languages of installation wizard to language file.

11/06/2019 - 2.2.7

	- Fix error cache of portfolio view with filter fields.
	- Fix error ajax load by a category.
	- Both Free and Pro version are included in 1 package.
	- Added installation wizard feature.

28/05/2019 - 2.2.6

	- Fix error load ajax when filter extrafields.
	- Fix error with multi-level categories filtering.
	- Fix error of core js (filter click) with module TZ Portfolio Plus Portfolio.

20/05/2019 - 2.2.5

	- Improving Filter selection with All category and sub-category.
	- Load ajax only load article of selected category or selected tag.
	- Update style Elegant v2.0:
		+ Improving Filter selection with All category and sub-category.
		+ Added option "Filter Style" - style for filter of portfolio view. It has 2 values: Default, Drop-down selection.

10/05/2019 - 2.2.4

	- Fix issue with feed data loading (latest blog feed).
	- Fix issue with default style override.
	- Update the latest awesome font v5.8.2 (overrided awesome font with tp prefix).
	- Added option allow user install sample data with new user.
	- Added option "Remember Recent Article" in global config.
	- Added intro guide for some views: dashboard, add-ons, styles, install add-on, install style in admin.

25/04/2019 - 2.2.3

	- Fix warning when creating a menu.
	- Fix error edit state of article in front-end.
    - Compatible with Joomla 4 Alpha 8.
	- Added latest blog feed for dashboard in admin.
	- Added option "Date Format" for the layouts.

17/04/2019 - 2.2.2

	- Fix error add tag from edit form on front-end.
	- Fix error display &amp; of article title on browser title.
	- Update elegant style v1.9 (update styles for module TZ Portfolio Plus Carousel).

03/04/2019 - 2.2.1

	- Fix error can not vote when the site uses self url.

29/03/2019 - 2.2.0

	- Fix error duplicate of related articles by tag.
	- Fix error order of addons and styles online install.

27/03/2019 - 2.1.9

	- Fix error can't voted in article view.
	- Change limited articles to 30 in free version.
	- Add feature related article by Tag
	  (added option "Related Article By" in Article View Options. Its default is "Tag").

14/03/2019 - 2.1.8

	- Fix error choose article of "Single Article" menu item type with Joomla 3.9.4.
	- Optimize core package file.

11/03/2019 - 2.1.7

	- Fix error display "view demo" button in Install Addon view.
	- Change View display in Menu select from "Articles" to "My Articles".

05/03/2019 - 2.1.6

	- Fix error TZ_Portfolio_PlusPluginHelper::loadLanguage() when update online from v2.1.4
    - Fix error edit ACL permission with Joomla 4 Alpha 6
    - Fix error category display all category (parent and child) on same level. in the back-end.
    - Add dm_uploader jQuery library to core.

27/02/2019 - 2.1.5

	- Fix error save preset's thumbnail of style.
	- Fix error edit form and enter tag of edit form in front-end.
	- Fix error language conflict of Joomla Content Vote.
	- Fix some styles of add-ons and styles online.
	- Added demo link for addons and styles online.
	- Added "View All Portfolios" button link for module TZ Portfolio Plus Portfolio.
	- Change language files of add-ons and styles (with prefix "plg_" to "tp_addon_" and "tpl_" to "tp_style_").
	- Opitimize packages.

20/02/2019 - 2.1.4

	- Fix error of sort category filter in portfolio view.
	- Added fancybox v3.5.6 library in core.
	- Added the latest awesome font v5.2.7 (overrided awesome font with tp prefix).
	- Apply awesome font html to some icons of vote add-on (use awesome font).
	- Change the words from "Style" to "Layout" and "Template" to "Style".
	- Compatible with Joomla 4 Alpha 6.

12/02/2019 - 2.1.3

	- Fix error one-click installation of add-ons and templates on live site.

15/01/2019 - 2.1.2

	- Fix error when Cookie return 'undefined' on Portfolio page.
	- Fix notice when homeid not set.

04/01/2019 - 2.1.1

	- Fix pagination error in back-end.
	- Fix error "Show all categories".
	- Optimize function of auto-tracking the recently-read article.

24/12/2018 - 2.1.0

	- Fix error sort filter with tag.
	- Add feature display/hidden "No more pages display" when all items was loaded.
	- Allow admin modify text "No more pages display" in admin
	- Add feature move to latest item position after go back from detail page.

03/12/2018 - 2.0.9

	- Fix error style of extrafield on front-end of version 2.0.8

29/11/2018 - 2.0.8

	- Fix error ItemId of module TZ Portfolio Plus Filter.
    - Fix error of image when upload image and tick to "Check this box to delete...."
    - Fix error javascript of module TZ Portfolio Plus Portfolio when use load module of joomla in description.
    - Update feature approve or reject articles.
		+ Added new views myarticles to manage articles in front-end.
        + Added "Approve Article" permission in global permission.
		+ Added new status for articles: Draft, Pending, Under Review.

15/10/2018 - 2.0.7

	- Fix error save information of add-on (image, video's thumbnail...) on front-end.
    - Fix error connect to external server when edit options of add-on.
    - Fix error warning lost catid of module TZ Portfolio Plus Portfolio.
    - Fix error chose image from extra field's value when create new field in back-end (error from addon extrafield TZ Vehicle of Autoshowroom Template).

06/09/2018 - 2.0.6

	- Fix error style of permission of add or edit form.
    - Fix error duplicate template styles when the style assign to multiple articles in back-end.
    - Remove option token key of free version.

25/08/2018 - 2.0.5

	- Fix error style of extrafield on front-end.
    - Fix error sef url with author when use "User ID And Alias Separator" is "Use a dash (-)" and disable option "Use User Alias".
    - Fix error sef url with tag when use "Tag ID And Alias Separator" is "Use a dash (-)" and disable option "Use Tag Alias".
    - Update feature remove category ID from url (Added option "Remove Category ID" at Advanced Options in global config).
    - Optimize some sef urls.

22/08/2018 - 2.0.4

	- Fix error style of layout builder when disable bootstrap library.
    - Fix error lost value of extrafield when change access in backend.
    - Fix error Cannot declare class JHtmlIcon.
    - Update Elegant template v1.7 - update style support layout builder.
    - Update feature the alias of category must be different from Prefix Users (Author) URLs, Prefix Tags URLs and Prefix Date URLs when create or edit category.

09/08/2018 - 2.0.3

	- Fix error with Joomla 4 Alpha.
    - Set limit create article of free version on front-end.
    - Remove notice release 2 versions in dashboard of pro version.

09/08/2018 - 2.0.2

	- Fix error conflict with Acy SMS component (can't login, logout in back-end) 
    - Fix error of addon vote when delete article.
    - Fix error vote of module TZ Portfolio Plus Portfolio.
    - Added code  remove voted of articles in vote add-on when delete article.
    - Enable Save & Close button in add and edit article
    - Release 2 version: free & pro

27/07/2018 - 2.0.1

	- Fix error Class 'TZ_Portfolio_PlusHelperCategories' not found of version 2.0.0

26/07/2018 - 2.0.0

	​- Remove Tag Articles and User Articles views (Replace by filter of Portfolio view).
	- Optimize codes.
	- Added notification for vote.
	- Added code filter for portfolio view when module TZ Portfolio Plus chose menu item is Portfolio view.
	- Support for Joomla 4 Alpha.

19/07/2018 - 1.3.0

	- Fix error priority of v1.2.9.

12/07/2018 - 1.2.9

	- Added priority order for Articles.
	- Added trigger onAddOnAfterSave for addon when saved article.
	- Remove mootools libraries from portfolio, date, tags, users view.

04/07/2018 - 1.2.8

	- Fix error override codes of addon from Template site.
	
04/07/2018 - 1.2.7

	- Fix error articles count of module TZ Portfolio Plus Categories when some articles unpublished.
	- Fix error create menu with TZ Portfolio Plus views of Joomla 3.8.10
	
27/04/2018 - 1.2.6

	- Fix error when upload image from server of Image addon.
	- Fix error can't save params in add or edit field (backend).
	- Fix error getItem() function of template in libraries.
	- Added field addon_id for table tz_portfolio_plus_addon_meta.
	- Added tables/tag_content_map.php file in admin.
	- Added Project Link type of article view in template style.
	- Optimize code of file path models/article.php in admin.
	- Optimize code of file path models/tag.php in admin.
	- Optimize code of file path models/addon.php in admin.
	- Optimize code of file path helpers/tags.php in admin.

19/03/2018 - 1.2.5

	- Fix error: Use of undefined constant COM_TZ_PORTFOLIO_PLUS_ACL_SECTIONS
	- Remove lightbox feature of core.
	- Remove Cloud Zoom of Image Addon.
	- Remove styles of charity addon and added styles for social addon in elegant template.
	- Move option "Show Original Gif Image" of image Addon from category edit to addon's global config.
	- Optimize multiplefield xml field.
	- Update code of function getListQuery() in portfolio model.
	- Update "Upload" text to "Install" of button upload in addons view and templates view (back-end).
	- Added upload button in layout upload of view addon and template.
	- Added install addons and templates from server.
	- Added option "Show Image" of image add-on for category listing and article view.
	- Added option "Enable Search Link" (Insert search link for value of field in front-end.) for field.
	- Added option "Show Image" for tag articles listing, user article listing and data article listings in global and category, article.

09/02/2018 - 1.2.4

	- Fix error of view date.
	- Update elegant template v1.5: support styles for TZ Portfolio+ Carousel module.

26/01/2018 - 1.2.3

	- Fix error function TZ_Portfolio_PlusModuleHelper::getLayoutPath.
	- Fix error when ajax load in portfolio view of v1.2.2

18/01/2018 - 1.2.2

	- Fix error vote of module TZ Portfolio Plus Portfolio.
	- Fix error edit acl when click button "Edit" in back-end.
	- Fix warning in modal tag when create menu with menu item type is "Tag Articles".
	- Fix error TZ Portfolio Plus's template assign for modules.
	- Fix error sort article by ordering in front-end.
	- Fix error language in preset of version 1.2.1
	- Added sort article by ordering reverse.
	- Added function generate image url with size (for developer).
	

11/01/2018 - 1.2.1

	- Fix warning when updating from version 1.1.8.
	- Fix error "save tags" when creating or editing article in version 1.2.0
	- Fix error "override module's html" (Add template assignment into module).
	- Remove module TZ Portfolio Plus Articles in core (Extracted to independent package).
	- Add new module TZ Portfolio Plus Portfolio (This module displays articles informing portfolio).
	- Add new view type for some addons (create view types in "Menu Item/Menu Item Type").


06/01/2018 - 1.2.0

	- Fix error sort articles by order.
	- Fix and update permissions.
	- Update some style of views in back-end.
	- Update elegant template v1.3
	- Added ACL view in back-end.
	- Support for addon can create permissions.

27/11/2017 - 1.1.8

	- Fix error load articles with Ajax Infinite Scroll in Portfolio view.
	- Fix error masonry and categories filter display in portfolio layout of module TZ Portfolio Plus Articles.
	- Fix error display articles of sub categories in portfolio view.
	- Update feature: Can override module of portfolio+ in portfolio plus's template.

14/11/2017 - 1.1.7

	- Fix warning in plugin libraries back-end file.
	- Fix error filter in module TZ Portfolio Plus Articles.

13/10/2017 - 1.1.6

	- Removed Brasil and Vietnamese Language from the main package (Compressed to Brasil and Vietnamese language package).
	- Added options: show title, show introtext... in "Article View Options" for category and article edit form (Used for article view in front-end).
	- Added option filter type and added categories filter of layout portfolio in module TZ Portfolio Plus Articles.
	- [backend] Fix error language of sort table by in fields manager.


09/10/2017 - 1.1.5

	- [backend] Disable "Save & Close" button of edit article form.
	- [backend] Fix warning when disable all add-on of addon list view in backend.
	- [frontend] Update create and edit article in front-end.
	- [frontend] Fix error display icon font of elegant style.

13/09/2017 - 1.1.4

	- [backend] Fix issue addon display
	- [frontend] Update elegant template version 1.1 in default.

07/09/2017 - 1.1.3

	- [frontend] Update Image Addon v1.0.8 - Accept gif image
	- [backend] Fix error can not add extrafield
	- [language] Update Brasil and Vietnamese Language

31/08/2017 - 1.1.2

	- [frontend] Fix error notice when show all categories in filter
	- [backend] Fix style of Layout Builder in version 1.1.1

30/08/2017 - 1.1.1

	- [backend] Fix error link with Styles in Dashboard

29/08/2017 - 1.1.0

	- [frontend] Fix warning when user logged in at Date View
	- [frontend] Add project link in project detail
	- [frontend] Add new default style - Elegant
	- [frontend] Fix error with addon attachment
	- [backend] Add Dashboard View
	- [backend] Fix style at Template
	- [backend] Fix problem of Main Category and Sub-Categories
	- [backend] New look on all view
	- [backend] Change input type of Social link at User edit.
	- [backend] Fix problem with trash feature
	
11/08/2017 - 1.0.7
	
	- Fix error Layout default_extrafields not found in view users.
	- Fix error placeholder for text and textarea search addons.
	- Added parameters from extrafields for article and category edit form if extrafield support xml files.
	- Fix error addon_task submit.
	
30/12/2016 - 1.0.6
	
	- Fix error in router when the addon has have router (Fix in router.php file)
	- Fix error when create group field first. (Fix of file models/group.php file in admin).
	- Added filter module.
	- Added options: List View (Show this field in article list view)
		,  Details View (Show this field in article details view), Advanced Search (This field is searchable in advanced search mode)
		in extrafield.
	- Added search view.
	- Display extrafield info in portfolio view, search view.
	- Fix error sort article by ordering and by category order. (The script in file tz_portfolio_plus.js changed code).
	- Fix error sort categories in back-end.
	- Added option "Filter Secondary Category"
	- Added sort extrafield by group field.
	- Display Categories Assignment & Total fields for Field Groups in back-end.
	
11/11/2016 - 1.0.5
	
	- Fix error when install in Joomla v3.2.7
	- Fix error can't use isotope layout (fitRows, straighDown)
	- Update return link for addon edit when addon data created option link.
	- Fix error assign categories in group field with first save group field.
	- Remove block html when columns don't display html.
	- Add return link when save & close add-on.
	- Change error message of addon to display in alert box.
	- Fix error JHtml Icon in view portfolio.
	- Fix error default value of textarea extrafield addon.
	- Change language in view template and template style.
	- Fix error validate in module mod_tz_portfolio_plus_categories.
	- Remove navigation of core in Single layout builder (change to addon).
	- Fix error sort template style in template styles view.
	
09/15/2016 - 1.0.4
	
	- Fix some warning when install first component.
	- Fix error option "Layout" in Template Style with joomla 3.6.2
	- Fix warning: added function postDeleteHook to path file libraries/controller/admin.php
	- Update basic styles for default template of component, modules: TZ Portfolio Plus Articles, TZ Portfolio Plus Tags and TZ Portfolio Plus Categories.
	
06/09/2016 - 1.0.3
	
	- Fix error upload image from server with add-on image of v1.0.2
	
06/07/2016 - 1.0.2
	
	- Fix error select tags with layout portfolio of module mod_tz_portfolio_plus_articles.
	- Fix error upload image with add-ons: image, image gallery of v1.0.1
	- New feature: Preset for template style.
	
05/27/2016 - 1.0.1
	
	- Fix errors with view categories:
		+ Can't display categories.
		+ Insert option category image in global and view.
	- Fix validate html with users view.
	- Fix enter key when choose tags and disable autocommplete when put tags in view create article with joomla 3.5.x
	- Feature: run sql and script.php file for addon as component.

04/11/2016 - 1.0.0
	
	- Release version 1.0.0
