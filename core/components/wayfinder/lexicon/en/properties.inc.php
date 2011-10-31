<?php
/**
 * @package wayfinder
 * @subpackage lexicon
 */
$_lang['prop_wayfinder.level_desc'] = 'Depth (number of levels) to build the menu from. 0 goes through all levels.';
$_lang['prop_wayfinder.includeDocs_desc'] = 'Acts as a filter and will limit the output to only the documents specified in this parameter. The startId is still required.';
$_lang['prop_wayfinder.excludeDocs_desc'] = 'Acts as a filter and will remove the documents specified in this parameter from the output. The startId is still required.';
$_lang['prop_wayfinder.contexts_desc'] = 'Specify the contexts for the Resources that will be loaded in this menu. Useful when used with startId at 0 to show all first-level items. Note: This will increase load times a bit, but if you set cacheResults to 1, that will offset the load time.';
$_lang['prop_wayfinder.cacheResults_desc'] = 'Cache the generated menu to the MODX Resource cache. Setting this to 1 will speed up the loading of your menus.';
$_lang['prop_wayfinder.cacheTime_desc'] = 'The number of seconds to store the cached menu, if cacheResults is 1. Set to 0 to store indefinitely until cache is manually cleared.';
$_lang['prop_wayfinder.ph_desc'] = 'To display send the output of Wayfinder to a placeholder set the ph parameter equal to the name of the desired placeholder. All output including the debugging (if on) will be sent to the placeholder specified.';
$_lang['prop_wayfinder.debug_desc'] = 'With the debug parameter set to 1, Wayfinder will output information on how each Resource was processed.';
$_lang['prop_wayfinder.ignoreHidden_desc'] = 'The ignoreHidden parameter allows Wayfinder to ignore the display in menu flag that can be set for each document. With this parameter set to 1, all Resources will be displayed regardless of the Display in Menu flag.';
$_lang['prop_wayfinder.hideSubMenus_desc'] = 'The hideSubMenus parameter will remove all non-active submenus from the Wayfinder output if set to 1. This parameter only works if multiple levels are being displayed.';
$_lang['prop_wayfinder.useWeblinkUrl_desc'] = ' If WebLinks are used in the output, Wayfinder will output the link specified in the WebLink instead of the normal MODx link. To use the standard display of WebLinks (like any other Resource) set this to 0.';
$_lang['prop_wayfinder.fullLink_desc'] = 'If set to 1, will display the entire, absolute URL in the link. (It is recommended to use scheme instead.)';
$_lang['prop_wayfinder.scheme_desc'] = 'Determines how URLs are generated for each link. Set to "abs" to show the absolute URL, "full" to show the full URL, and blank to use the relative URL. Defaults to relative.';
$_lang['prop_wayfinder.sortOrder_desc'] = 'Allows the menu to be sorted in either ascending or descending order.';
$_lang['prop_wayfinder.sortBy_desc'] = 'Sorts the output by any of the Resource fields on a level by level basis. This means that each submenu will be sorted independently of all other submenus at the same level. Random will sort the output differently every time the page is loaded if the snippet is called uncached.';
$_lang['prop_wayfinder.limit_desc'] = 'Causes Wayfinder to only process the number of items specified per level.';
$_lang['prop_wayfinder.cssTpl_desc'] = 'This parameter allows for a chunk containing a link to a style sheet or style information to be inserted into the head section of the generated page.';
$_lang['prop_wayfinder.jsTpl_desc'] = 'This parameter allows for a chunk containing some Javascript to be inserted into the head section of the generated page.';
$_lang['prop_wayfinder.rowIdPrefix_desc'] = 'If set, Wayfinder will replace the id placeholder with a unique id consisting of the specified prefix plus the Resource id.';
$_lang['prop_wayfinder.textOfLinks_desc'] = 'This field will be inserted into the linktext placeholder.';
$_lang['prop_wayfinder.titleOfLinks_desc'] = 'This field will be inserted into the linktitle placeholder.';
$_lang['prop_wayfinder.displayStart_desc'] = 'Show the document as referenced by startId in the menu.';
$_lang['prop_wayfinder.firstClass_desc'] = 'CSS class for the first item at a given menu level.';
$_lang['prop_wayfinder.lastClass_desc'] = 'CSS class for the last item at a given menu level.';
$_lang['prop_wayfinder.hereClass_desc'] = 'CSS class for the items showing where you are, all the way up the chain.';
$_lang['prop_wayfinder.parentClass_desc'] = 'CSS class for menu items that are a container and have children.';
$_lang['prop_wayfinder.rowClass_desc'] = 'CSS class denoting each output row.';
$_lang['prop_wayfinder.outerClass_desc'] = 'CSS class for the outer template.';
$_lang['prop_wayfinder.innerClass_desc'] = 'CSS class for the inner template.';
$_lang['prop_wayfinder.levelClass_desc'] = 'CSS class denoting every output row level. The level number will be added to the specified class (level1, level2, level3 etc if you specified "level").';
$_lang['prop_wayfinder.selfClass_desc'] = 'CSS class for the current item.';
$_lang['prop_wayfinder.webLinkClass_desc'] = 'CSS class for weblink items.';
$_lang['prop_wayfinder.outerTpl_desc'] = 'Name of the chunk containing the template for the outer most container; if not included, a string including "<ul>[[+wf.wrapper]]</ul>" is assumed.';
$_lang['prop_wayfinder.rowTpl_desc'] = 'Name of the chunk containing the template for the regular row items.';
$_lang['prop_wayfinder.parentRowTpl_desc'] = 'Name of the chunk containing the template for any Resource that is a container and has children. Remember the [wf.wrapper] placeholder to output the children documents.';
$_lang['prop_wayfinder.parentRowHereTpl_desc'] = 'Name of the chunk containing the template for the current Resource if it is a container and has children. Remember the [wf.wrapper] placeholder to output the children documents.';
$_lang['prop_wayfinder.hereTpl_desc'] = 'Name of the chunk containing the template for the current Resource.';
$_lang['prop_wayfinder.innerTpl_desc'] = 'Name of the chunk containing the template for each submenu. If no innerTpl is specified the outerTpl is used in its place.';
$_lang['prop_wayfinder.innerRowTpl_desc'] = 'Name of the chunk containing the template for the row items in a subfolder.';
$_lang['prop_wayfinder.innerHereTpl_desc'] = 'Name of the chunk containing the template for the current Resource if it is in a subfolder.';
$_lang['prop_wayfinder.activeParentRowTpl_desc'] = 'Name of the chunk containing the template for items that are containers, have children and are currently active in the tree.';
$_lang['prop_wayfinder.categoryFoldersTpl_desc'] = 'Name of the chunk containing the template for category folders. Category folders are determined by setting the template to blank or by setting the link attributes field to rel="category".';
$_lang['prop_wayfinder.startItemTpl_desc'] = 'Name of the chunk containing the template for the start item, if enabled via the &displayStart parameter. Note: the default template shows the start item but does not link it. If you do not need a link, a class can be applied to the default template using the parameter &firstClass=`className`.';
$_lang['prop_wayfinder.permissions_desc'] = 'Will check for a permission on the Resource. Defaults to "list" - set to blank to skip normal permissions checks.';
$_lang['prop_wayfinder.hereId_desc'] = 'Optional. If set, will change the "here" Resource to this ID. Defaults to the currently active Resource.';
$_lang['prop_wayfinder.where_desc'] = 'Optional. A JSON object for where conditions for all items selected in the menu.';
$_lang['prop_wayfinder.templates_desc'] = 'Optional. A comma-separated list of Template IDs to restrict selected Resources to.';
$_lang['prop_wayfinder.previewunpublished_desc'] = 'Optional. If set to Yes, if you are logged into the mgr and have the view_unpublished permission, it will allow previewing of unpublished resources in your menus in the front-end.';

$_lang['prop_wayfinder.ascending'] = 'Ascending';
$_lang['prop_wayfinder.descending'] = 'Descending';
$_lang['prop_wayfinder.absolute'] = 'Absolute';
$_lang['prop_wayfinder.relative'] = 'Relative';
$_lang['prop_wayfinder.full'] = 'Full';