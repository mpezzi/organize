<?php

define('ORGANIZER_BASE_PATH', dirname(__FILE__));
define('ORGANIZER_COMMAND', $GLOBALS['argv'][0]);
define('ORGANIZER_DIR', $GLOBALS['argv'][1]);
define('ORGANIZER_FILE', ORGANIZER_DIR .'/.organize');
define('ORGANIZER_LOG', ORGANIZER_DIR .'/.organize.log');
define('ORGANIZER_REQUEST_TIME', microtime(TRUE));

//echo ORGANIZER_BASE_PATH . "\n";
//echo ORGANIZER_COMMAND . "\n";
//echo ORGANIZER_DIR . "\n";
//echo ORGANIZER_FILE . "\n";

// Ensure organizer.txt exists in folder.
if ( file_exists(ORGANIZER_FILE) ) {
  $files = array();
  
  $lines = file(ORGANIZER_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ( $lines as $line ) {
    list($search, $dest) = explode(' => ', $line);
    $files[trim($search)] = trim($dest);
  }
  
  if ( count($files) > 0 && is_dir(ORGANIZER_DIR) ) {
    echo "---------------------------------\n";
    foreach ( $files as $search => $dest ) {
      echo $search ." => ". $dest ."\n";
    }
    echo "---------------------------------\n";
    
    if ( $handle = opendir(ORGANIZER_DIR) ) {
      while ( ($file = readdir($handle)) !== FALSE ) {
        if ( is_file($file) ) {
          foreach ( $files as $search => $dest ) {
            if ( preg_match($search, $file) && is_dir($dest) && !file_exists($dest . $file) ) {
              echo "Copying ". $file ." to ". $dest ."\n";
              if ( copy($file, $dest.$file) ) {
                organizer_log($file);
              }
            }
          }
        }
      }
      closedir($handle);
    }
  }
} else {
  echo ".organize file not found\n";
}

function organizer_log($s) {
  if ( !file_exists(ORGANIZER_LOG) )
    touch(ORGANIZER_LOG);
  
  $log = fopen(ORGANIZER_LOG, 'a');
  fwrite($log, $s ."\n");
  fclose($log);
}