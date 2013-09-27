#!/usr/bin/env php
<?php
require __DIR__ . '/base/load.php';

// A simple helper function
function println($txt = '')
{
    echo $txt . PHP_EOL;
}

$maillogFile = Config('Main')->defaultMaillogFile;
// Print some help or set some settings
if (!empty($argv[1])) {
    if ($argv[1] == '--help' || $argv[1] == '-h') {
        println('Usage: php maillog-parser.php [path_to_maillog_file]');
        println('This script is used to prase maillog data and write it to Db.');
        exit(0);
    }
    elseif (is_file($argv[1]) && is_readable($argv[1])) {
        $maillogFile = $argv[1];
    }
    else {
        println('Invalid usage, try --help');
        exit(1);
    }
}

try {
    // Create database writer
    $dbWriterClass = 'MaillogImporter\\Writer\\' . Config('Main')->dbWriterClass;
    $dbWriter = new $dbWriterClass();
    // Create file importer and import data via the database writer
    $fileImporter = new MaillogImporter\FileImporter($maillogFile);
    $fileImporter->import($dbWriter);
    // Just an informational message
    println('OK');
    exit(0);
}
catch (\Exception $ex) {
    throw $ex;
    exit(1);
}