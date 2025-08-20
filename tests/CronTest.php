<?php
// Simple test script for Cron class
require_once __DIR__ . '/../Cron.php';

function assertFileContains($file, $expected) {
    if (!file_exists($file)) {
        echo "FAIL: File $file does not exist\n";
        return false;
    }
    $contents = file_get_contents($file);
    if (strpos($contents, $expected) === false) {
        echo "FAIL: File $file does not contain expected string\n";
        return false;
    }
    echo "PASS: File $file contains expected string\n";
    return true;
}

function assertFileEmptyOrNotExists($file) {
    if (!file_exists($file)) {
        echo "PASS: File $file does not exist (no errors logged)\n";
        return true;
    }
    $contents = file_get_contents($file);
    if (trim($contents) === '') {
        echo "PASS: File $file is empty (no errors logged)\n";
        return true;
    }
    echo "FAIL: File $file is not empty (unexpected errors logged)\n";
    return false;
}

function runCronTest() {
    // Remove old test files
    $cronDir = __DIR__ . '/../cron/';
    $logsDir = __DIR__ . '/../logs/';
    @unlink($cronDir . 'cron_test.txt');
    @unlink($logsDir . 'success_log.txt');
    @unlink($logsDir . 'error_log.txt');
    if (file_exists($cronDir)) {
        @rmdir($cronDir);
    }
    if (file_exists($logsDir)) {
        // Optionally remove logsDir if empty
        $files = scandir($logsDir);
        if (count($files) <= 2) { // only . and ..
            @rmdir($logsDir);
        }
    }

    // Run the cron job
    $cron = new Cron();
    ob_start();
    $cron->run();
    $output = ob_get_clean();
    echo "Cron output: $output\n";

    // Check success log
    $successLog = $logsDir . 'success_log.txt';
    assertFileContains($successLog, 'SUCCESS: Cron job executed successfully');
    // Check timestamp file
    $timestampFile = $cronDir . 'cron_test.txt';
    assertFileContains($timestampFile, date('Y-m-d'));
    // Check error log
    $errorLog = $logsDir . 'error_log.txt';
    assertFileEmptyOrNotExists($errorLog);
}

runCronTest();
