<?php

//Sometimes, array checking php stuff dosen't work. So we need to run the actual problematic command.

$isShared = false;
$safeMode = false;

//Create an error handler for shell_exec
function execTest($errno, $errstr) {
  $isShared = true;
}


//set error handler
set_error_handler("execTest");

//Run a test command
shell_exec("echo"); //Run 'echo' to either succeed or trigger an error


//Try to find problems with followlocation now
if( ini_get('safe_mode') or strpos(ini_get('open_basedir'), DIRECTORY_SEPARATOR) != false ){
  $safeMode = true;
}

//Convert the 2 vars to strings
$converted_shared = ($isShared) ? 'true' : 'false';
$converted_safeMode = ($safemode) ? 'true' : 'false';

$arconverted = array ( $converted_shared, $converted_safeMode );


//Return the shared status, which you can explode with the seperator '::'
// It's in this format: "isSharedServer::isSafeMode'
echo( join("::", $arconverted) );
?>
