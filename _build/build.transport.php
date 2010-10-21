<?php
/**
 * Build script for wayfinder
 *
 * @package wayfinder
 * @subpackage build
 * @version 2.1.1-beta5
 */
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
/* get rid of time limit */
set_time_limit(0);
$root = dirname(dirname(__FILE__)).'/';
$sources= array (
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'docs' => $root . 'core/components/wayfinder/docs/',
    'source_core' => $root . 'core/components/wayfinder',
);

/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');


$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage('wayfinder','2.1.1','rc2');
$builder->registerNamespace('wayfinder',false,true,'{core_path}components/wayfinder/');

/* get the source from the actual snippet in your database
 * [alternative] you could also manually create the object, grabbing the source
 * from a file
 */
$c= $modx->newObject('modSnippet');
$c->set('id',1);
$c->set('name', 'Wayfinder');
$c->set('description', 'Wayfinder for MODx Revolution 2.0.0-beta-5 and later.');
$c->set('snippet', file_get_contents($sources['source_core'] . '/wayfinder.snippet.php'));
$c->set('category', 0);
$properties = include $sources['data'].'properties.inc.php';
$c->setProperties($properties, true);

$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
);
$vehicle = $builder->createVehicle($c, $attributes);

$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$builder->putVehicle($vehicle);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
));

$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();