<?php

/**
 * @author bluelovers
 * @copyright 2012
 *
 * @see 01-Templating-In-Five-Minutes.markdown
 */

require_once './Bootstrap.php';

/**
 * %name% - In a path pattern, the %name% placeholder represents the logical template name.
 * %renderer% - The path patterns can also contain the %renderer% placeholder, which represent the renderer name
 *
 * In the above example, the loader will first look in the templates/ sub-directory of the current directory, and if no file is found, it will try the templates/default/ directory before giving up.
 *
 * @example $loader = new sfTemplateLoaderFilesystem(dirname(__FILE__).'/templates/%name%.%renderer%');
 * @example $loader = new sfTemplateLoaderFilesystem(array(
  dirname(__FILE__).'/templates/%name%.php',
  dirname(__FILE__).'/templates/default/%name%.php',
));
 */
$loader = new sfTemplateLoaderCompilable(dirname(__FILE__).'/templates/%name%.php');

$cacheDir = dirname(__FILE__).'/cache/';

$debugger = new sfTemplateDebugger();

$loader->setDebugger($debugger);

$loader = new sfTemplateLoaderCache($loader, $cacheDir);

$loader->setDebugger($debugger);

$engine = new sfTemplateEngine($loader);

echo $engine->render('index', array('name' => 'Fabien'));