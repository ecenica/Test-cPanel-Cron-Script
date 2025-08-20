<?php
// Simple test script for Cron class
require_once __DIR__ . '/Cron.php';

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

function runCronTest() {
    // Remove old test files
    $cronDir = __DIR__ . '/cron/';
    @unlink($cronDir . 'cron_test.txt');
    @unlink($cronDir . 'success_log.txt');
    @unlink($cronDir . 'error_log.txt');
    if (file_exists($cronDir)) {
        @rmdir($cronDir);
    }

    // Run the cron job
    $cron = new Cron();
    ob_start();
    $cron->run();
    $output = ob_get_clean();
    echo "Cron output: $output\n";

    // Check success log
    $successLog = $cronDir . 'success_log.txt';
    assertFileContains($successLog, 'SUCCESS: Cron job executed successfully');
    // Check timestamp file
    $timestampFile = $cronDir . 'cron_test.txt';
    assertFileContains($timestampFile, date('Y-m-d'));
}

runCronTest();

