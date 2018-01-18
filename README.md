# TZ Portfolio+ | Joomla 3.x

TZ Portfolio is a really good portfolio developing extensions for Joomla that allows users to display, style and manage their portfolio easily.

Plus it has all layouts and multi-categories you would need for a portfolio with two basic views: Portfolio and Single Article view.

Documentation: http://tzportfolio.com/document.html

Demo: http://demo.tzportfolio.com/

*** Changelog ***

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