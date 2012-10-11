<?php

/* Scan for all scripts at the updateDatabase subdirectory.
 * If found the script at the propriatary format with set variables $g_majorDb, $g_minorDb, $g_buildDb and defined function 'update_to_$g_majorDb_$g_minorDb_$g_buildDb()'
 * will execute the specified function if the current database major version is equal to $g_majorDb
 * and minor.build database version is less than $g_minorDb.$g_buildDb.
 * 
 * It's strongly recommended to name the scripts like 'MAJOR_YYYYMMDD_BUILD' to ensure the correct order of script execution.
 * The script must not contain any PHP code outside function 'update_to_$g_majorDb_$g_minorDb_$g_buildDb()'.
 * 
 * The signature of update_to_$g_majorDb_$g_minorDb_$g_buildDb() funtion:
 * boolean update_to_$g_majorDb_$g_minorDb_$g_buildDb(DB_Sql $dbh);
 * 
 * $dbh handle must be used for any database changes inside 'update_to_$g_majorDb_$g_minorDb_$g_buildDb()'
*/

set_time_limit(0);
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

function test_autoload($className) {
    $fname = str_replace('_', '/', $className) . '.php';
    $result = include($fname);
    return $result;
}


spl_autoload_register("test_autoload");


/** Zend_Application */
require_once 'Zend/Application.php';


$getopt = new Zend_Console_Getopt(array(
    'env|e-s'    => 'Application environment for which to create database (defaults to development)',
    'help|h'     => 'Help -- usage message',
));
try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    echo $e->getUsageMessage();
    return false;
}

if ($getopt->getOption('h')) {
    echo $getopt->getUsageMessage();
    return true;
}

$env      = $getopt->getOption('e');


// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (null === $env) ? 'production' : $env);

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV, 
    APPLICATION_PATH . '/configs/app.ini'
);
$application->bootstrap();
error_reporting (E_ALL ^ E_NOTICE);

function askProceed() {
    $in = defined('STDIN') ? STDIN : fopen('php://stdin', 'r');
    fflush($in);
    printf("Proceed(Y/N)[n] ?");
   $str = trim(fgets($in));
    if(strtolower($str) == 'y') {
        return 'y';
    }
    else {
        return 'n';
    }
}

$db = Zend_Registry::get('dbAdapter');

$updateDir = dirname(__FILE__).'/updateDatabase';
$files = scandir($updateDir);
if(is_array($files)) {
    foreach ($files as $file) {
        if ($file != "." && $file != ".." && !is_dir($file)) {
            printf("Checking %s\n", $file);
            $parts = pathinfo($file);
            if(strcasecmp($parts['extension'], 'php')) {
                continue;
            }
            unset($g_majorDb);
            unset($g_minorDb);
            unset($g_buildDb);
            include $updateDir."/".$file;
            if(!isset($g_majorDb) || !isset($g_minorDb) || !isset($g_buildDb)) {
                printf("ERROR: File %s doesn't contain some of version variables\n", $file);
                continue;
            }
            $funcName = sprintf("update_to_%s_%s_%s", $g_majorDb, $g_minorDb, $g_buildDb);
            if(!function_exists($funcName)) {
                printf("ERROR: File %s doesn't contain defined function %s\n", $file, $funcName);
                continue;
            }
            
            try {
                $getVersion = sprintf("select * from db_version;");
                $stmt = $db->query($getVersion);
                $result = $stmt->fetch(Zend_Db::FETCH_OBJ);
                // START fix PHP-PDO-MySQL problem with statements (see http://bugs.php.net/35793 especially comment "[2006-10-16 23:15 UTC] michal dot vrchota at seznam dot cz")
                $stmt->closeCursor();
                $stmt = null;
                // END fix
                if(!is_null($result)) {
                    $currentMajor = $result->major;
                    $currentMinor = $result->minor;
                    $currentBuild = $result->build;
                     
                    if($currentMajor != $g_majorDb) {
                        continue;
                    }
                    if($currentMinor > $g_minorDb) {
                        continue;
                    }
                    if($currentMinor == $g_minorDb && $currentBuild >= $g_buildDb) {
                        continue;
                    }
                     
                    printf("Updating db to version %s.%s.%s (%s) ... ", $g_majorDb, $g_minorDb, $g_buildDb, $file);
                    $db->beginTransaction();
                    try {
                        $ret = call_user_func($funcName, $db);
                        if(false == $ret) {
                            throw new Exception(sprintf("%s failed", $funcName));
                        }
                        $changeVersion = sprintf("update db_version set major=%d,minor=%d,build=%d;", $g_majorDb, $g_minorDb, $g_buildDb);
                        $db->query($changeVersion);
                        $db->commit();
                        printf("SUCCEDED\n");
                        continue;
                    }
                    catch(Exception $ex) {
                        $db->rollBack();
                        printf("FAILED\n");
                        if(askProceed() == 'n') {
                            break;
                        }
                        else {
                            continue;
                        }
                    }
                }
                else {
                    printf("ERROR: Can't get database version. Script %s hasn't been processed.\n\t%s\n", $file, $dbh->Error);
                    if(askProceed() == 'n') {
                        break;
                    }
                    else {
                        continue;
                    }
                }

            }
            catch(Exception $ex) {
                printf($ex->getMessage());
            }
        }
    }
}

