<?php

/// Moodle Configuration File
/// FOR LOCAL DEVELOPMENT ONLY

$ROOT = substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['SCRIPT_NAME']));

// get git details
$GIT_HEAD = file_get_contents($ROOT.'/.git/HEAD');
$GIT_BRANCH = trim(substr($GIT_HEAD, 16));

$CFG            = new stdClass();
$CFG->dbtype    = 'postgres7';
$CFG->dbhost    = 'user=\'user\' password=\'password\' dbname=\''.$GIT_BRANCH.'\'';
$CFG->dbpersist =  false;
$CFG->prefix    = 'mdl_';

$CFG->wwwroot   = 'http://'.$_SERVER['SERVER_NAME'];
$CFG->dirroot   = $ROOT;
$CFG->dataroot  = dirname($ROOT).'/moodledata/';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode

$CFG->debug = 38911;
$CFG->debugdisplay = 0;

// Grab custom stuff
$CUSTOM = dirname(dirname($ROOT)).'/.configs/'.$GIT_BRANCH.'.php';
if (file_exists($CUSTOM)) {
    require_once $CUSTOM;
}

if (!empty($_GET['magicponies'])) {
    echo '<pre>';
    var_dump($CFG);
    echo '</pre>';
    die();
}

unset($CUSTOM, $ROOT, $DIRNAME, $GIT_HEAD, $GIT_BRANCH);

require_once("$CFG->dirroot/lib/setup.php");
