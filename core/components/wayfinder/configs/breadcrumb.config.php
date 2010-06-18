<?php
$hideSubMenus = 1;

if ($modx->getOption('site_start') == $modx->resource->get('id')) {
	$homeLink = '';
} else {
	$homeLink = "<a href=\"{$modx->getOption('site_url')}\" title=\"home\">Home</a> &raquo; ";
}

$outerTpl = "@CODE:<div id=\"breadcrumbnav\">
	{$homeLink}[[+wf.wrapper]]
</div>";

$innerTpl = '@CODE:[[+wf.wrapper]]';

$rowTpl = '@CODE: ';

$activeParentRowTpl = '@CODE:<a href="[[+wf.link]]" title="[[+wf.title]]">[[+wf.linktext]]</a> &raquo; [[+wf.wrapper]]';

$hereTpl = '@CODE:[[+wf.linktext]]';