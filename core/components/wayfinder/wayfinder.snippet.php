<?php
/**
 * Wayfinder Snippet to build site navigation menus
 *
 * Totally refactored from original DropMenu nav builder to make it easier to
 * create custom navigation by using chunks as output templates. By using
 * templates, many of the paramaters are no longer needed for flexible output
 * including tables, unordered- or ordered-lists (ULs or OLs), definition lists
 * (DLs) or in any other format you desire.
 *
 * @version 2.1.1-beta5
 * @author Garry Nutting (collabpad.com)
 * @author Kyle Jaebker (muddydogpaws.com)
 * @author Ryan Thrash (modx.com)
 * @author Shaun McCormick (modx.com)
 * @author Jason Coward (modx.com)
 *
 * @example [[Wayfinder? &startId=`0`]]
 *
 * @var modX $modx
 * @var array $scriptProperties
 * 
 * @package wayfinder
 */
$wayfinder_base = $modx->getOption('wayfinder.core_path',$scriptProperties,$modx->getOption('core_path').'components/wayfinder/');

/* include a custom config file if specified */
if (isset($scriptProperties['config'])) {
    $scriptProperties['config'] = str_replace('../','',$scriptProperties['config']);
    $scriptProperties['config'] = $wayfinder_base.'configs/'.$scriptProperties['config'].'.config.php';
} else {
    $scriptProperties['config'] = $wayfinder_base.'configs/default.config.php';
}
if (file_exists($scriptProperties['config'])) {
    include $scriptProperties['config'];
}

/* include wayfinder class */
include_once $wayfinder_base.'wayfinder.class.php';
if (!$modx->loadClass('Wayfinder',$wayfinder_base,true,true)) {
    return 'error: Wayfinder class not found';
}
$wf = new Wayfinder($modx,$scriptProperties);

/* get user class definitions
 * TODO: eventually move these into config parameters */
$wf->_css = array(
    'first' => isset($firstClass) ? $firstClass : '',
    'last' => isset($lastClass) ? $lastClass : 'last',
    'here' => isset($hereClass) ? $hereClass : 'active',
    'parent' => isset($parentClass) ? $parentClass : '',
    'row' => isset($rowClass) ? $rowClass : '',
    'outer' => isset($outerClass) ? $outerClass : '',
    'inner' => isset($innerClass) ? $innerClass : '',
    'level' => isset($levelClass) ? $levelClass: '',
    'self' => isset($selfClass) ? $selfClass : '',
    'weblink' => isset($webLinkClass) ? $webLinkClass : ''
);

/* get user templates
 * TODO: eventually move these into config parameters */
$wf->_templates = array(
    'outerTpl' => isset($outerTpl) ? $outerTpl : '',
    'rowTpl' => isset($rowTpl) ? $rowTpl : '',
    'parentRowTpl' => isset($parentRowTpl) ? $parentRowTpl : '',
    'parentRowHereTpl' => isset($parentRowHereTpl) ? $parentRowHereTpl : '',
    'hereTpl' => isset($hereTpl) ? $hereTpl : '',
    'innerTpl' => isset($innerTpl) ? $innerTpl : '',
    'innerRowTpl' => isset($innerRowTpl) ? $innerRowTpl : '',
    'innerHereTpl' => isset($innerHereTpl) ? $innerHereTpl : '',
    'activeParentRowTpl' => isset($activeParentRowTpl) ? $activeParentRowTpl : '',
    'categoryFoldersTpl' => isset($categoryFoldersTpl) ? $categoryFoldersTpl : '',
    'startItemTpl' => isset($startItemTpl) ? $startItemTpl : ''
);

/* process Wayfinder */
$output = $wf->run();
if ($wf->_config['debug']) {
    $output .= $wf->renderDebugOutput();
}

/* output results */
if ($wf->_config['ph']) {
    $modx->setPlaceholder($wf->_config['ph'],$output);
} else {
    return $output;
}