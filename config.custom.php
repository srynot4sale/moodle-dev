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

unset($CFG);
global $CFG;
$CFG = new stdClass();

// Get installations file path
$ROOT = substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['SCRIPT_NAME']));
$CUSTOM_CONFIGS = dirname(dirname($ROOT)).'/.configs';

// Get currently checked out git branch
$GIT_HEAD = file_get_contents($ROOT.'/.git/HEAD');
$GIT_BRANCH = trim(substr($GIT_HEAD, 16));

// Check if checked out git branch is not a real branch
if (!file_exists($ROOT.'/.git/refs/heads/'.$GIT_BRANCH)) {
    // Use default database instead (if set)
    if (file_exists($CUSTOM_CONFIGS.'/default_db')) {
        $GIT_BRANCH = trim(file_get_contents($CUSTOM_CONFIGS.'/default_db'));
    }
}

// Check Moodle version
$IS19 = magic_is_moodle19($ROOT);

// Paths
$CFG->wwwroot   = 'http://'.$_SERVER['SERVER_NAME'];
$CFG->dirroot   = $ROOT;
$CFG->dataroot  = dirname($ROOT).'/moodledata/';
$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode

// Database connection
$CFG->dbtype    = $IS19 ? 'postgres7' : 'pgsql';
$CFG->dbhost    = '';
$CFG->dbuser    = 'username';
$CFG->dbpass    = 'password';
$CFG->prefix    = 'mdl_';

// Debugging
$CFG->debug = 38911;
$CFG->debugdisplay = 0;

// Misc
$CFG->admin     = 'admin';

if ($IS19) {
    $CFG->dbname    = "'{$GIT_BRANCH}'";
    $CFG->dbpersist =  false;
} else {
    $CFG->dbname    = $GIT_BRANCH;
    $CFG->dblibrary = 'native';
    $CFG->dboptions = array('dbpersist' => 0, 'dbsocket' => 1);
}

// Grab custom config file
// Create one per git branch, overwrite/add to $CFG from them
$CUSTOM = $CUSTOM_CONFIGS.'/'.$GIT_BRANCH.'.php';
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

unset($CUSTOM, $CUSTOM_CONFIGS, $ROOT, $DIRNAME, $GIT_HEAD, $GIT_BRANCH, $IS19);


/**
 * Magic debug functions!
 */
function magic_is_moodle19($root) {
    $versionfile = file_get_contents($root.'/version.php');
    return strpos($versionfile, '$branch') === FALSE;
}


/**
 * Print debug backtrace (and optional var dump) to screen,
 * and also to a temporary file
 */
function magic_debug($var = 'magicstring') {
    print '<hr style="color: red;">';
    print '<table>';

    if ($var !== 'magicstring') {
        print '<tr><td><pre>';
        print magic_repr($var);
        print '</pre></td></tr>';
    }

    foreach (debug_backtrace() as $debug) {
        print '<tr><td>';
        print '<b>'.$debug['file'].'</b> on line '.$debug['line'].' in <b>';
        unset($debug['file']);
        unset($debug['line']);

        if (isset($debug['class'])) {
            print $debug['class'].'::'.$debug['function'];
            unset($debug['class']);
            unset($debug['function']);
        } else {
            print $debug['function'];
            unset($debug['function']);
        }
        print '</b>';

        if (!empty($debug) && !empty($debug['args'])) {
            print '<br /><pre>';
            print magic_repr($debug).'</pre>';
        }
        print '</td></tr>';
    }

    print '</table>';

    magic_log();
    print '<hr style="color: red;">';
}

/**
 * Print debug backtrace (and optional var dump) to
 * a temporary file
 */
function magic_log($var = 'magicstring') {
    $filename = tempnam('/tmp/', 'totara');
    $file = fopen($filename, 'w');
    fwrite($file, magic_repr(debug_backtrace()));

    if ($var !== 'magicstring') {
        fwrite($file, "\n".magic_repr($var));
    }

    fclose($file);
    chmod($filename, 0777);
    error_log('------- DEBUG OUTPUT: '.$filename.' -------');
}

/**
 * Return a string representation of a variable
 */
function magic_repr($var) {
    return var_export($var, true);
}

/**
 * Print a string representation of a variable to
 * the error log
 */
function magic_shortlog($var) {
    error_log(magic_repr($var));
}

/**
 * End of magic debug functions
 */


require_once("{$CFG->dirroot}/lib/setup.php");
