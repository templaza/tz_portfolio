# TZ Portfolio+ | Joomla 3.x

TZ Portfolio is a really good portfolio developing extensions for Joomla that allows users to display, style and manage their portfolio easily.

Plus it has all layouts and multi-categories you would need for a portfolio with two basic views: Portfolio and Single Article view.

Documentation: http://tzportfolio.com/document.html

Demo: http://demo.tzportfolio.com/

*** Changelog ***

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
