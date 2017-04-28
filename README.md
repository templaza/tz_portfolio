# TZ Portfolio+ - Joomla 3.x

TZ Portfolio is a really good portfolio developing extensions for Joomla that allows users to display, style and manage their portfolio easily.

Plus it has all layouts and multi-categories you would need for a portfolio with two basic views: Portfolio and Single Article view.

Documentation: http://tzportfolio.com/document.html

Demo: http://demo.tzportfolio.com/

*** Changelog ***
	
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