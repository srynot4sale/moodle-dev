<?php

/// Moodle Configuration File
/// FOR LOCAL DEVELOPMENT ONLY
///
/// Expects the following file structure:
///
/// /var/www/moodle/.configs/{$gitbranch}.php -- branch specific config files
/// /var/www/moodle/{$subdomain}/htdocs -- document root / moodle checkout
/// /var/www/moodle/{$subdomain}/moodledata -- moodle data directory
///
///
/// Also expects postgres databases to be named after the
/// associated git branch


$CFG = new stdClass();

// Get installations file path
$ROOT = substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['SCRIPT_NAME']));

// Get currently checked out git branch
$GIT_HEAD = file_get_contents($ROOT.'/.git/HEAD');
$GIT_BRANCH = trim(substr($GIT_HEAD, 16));


// Paths
$CFG->wwwroot   = 'http://'.$_SERVER['SERVER_NAME'];
$CFG->dirroot   = $ROOT;
$CFG->dataroot  = dirname($ROOT).'/moodledata/';
$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode

// Database connection
$CFG->dbtype    = 'postgres7';
$CFG->dbhost    = '';
$CFG->dbuser    = 'user';
$CFG->dbpass    = 'password';
$CFG->dbname    = "'{$GIT_BRANCH}'";
$CFG->dbpersist =  false;
$CFG->prefix    = 'mdl_';

// Debugging
$CFG->debug = 38911;
$CFG->debugdisplay = 0;

// Misc
$CFG->admin     = 'admin';


// Grab custom config file
// Create one per git branch, overwrite/add to $CFG from them
$CUSTOM = dirname(dirname($ROOT)).'/.configs/'.$GIT_BRANCH.'.php';
if (file_exists($CUSTOM)) {
    require_once $CUSTOM;
}

// Debugging variable to check your config
// ?magicponies=1
if (!empty($_GET['magicponies'])) {
    echo '<pre>';
    var_dump($CFG);
    echo '</pre>';
    die();
}

unset($CUSTOM, $ROOT, $DIRNAME, $GIT_HEAD, $GIT_BRANCH);

require_once("{$CFG->dirroot}/lib/setup.php");
