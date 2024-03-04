<?php
/**
 * Cron Test Script for Ecenica cPanel
 * 
 * This script is used to test a cron job in Ecenica's cPanel.
 * It writes a timestamp to a file every time it is executed.
 * 
 * Instructions:
 * 1. Place this script in your Ecenica cPanel's public_html directory in a subdirectory called 'cron'
 * 2. Set up a cron job in Ecenica cPanel to execute this script at regular intervals.
 * 3. Ensure that the path to the cron_test.txt file is accessible and writable by the script.
 * 
 * Example Cron Job Command:
 * /usr/bin/php /home/yourusername/public_html/cron_test.php
 * 
 * Author: Ecenica
 * Version: 1.0
 */

// Get the path to the public_html directory
$publicHtmlPath = $_SERVER['DOCUMENT_ROOT'];

// Specify the subdirectory name
$subdirectory = '/cron/';

// Set the file path where the timestamp will be stored
$filePath = $publicHtmlPath . $subdirectory . 'cron_test.txt';

// Open the file (create it if it doesn't exist) in append mode
$file = fopen($filePath, 'a');

// Write the current timestamp to the file
fwrite($file, date('Y-m-d H:i:s') . PHP_EOL);

// Close the file
fclose($file);

// Output a message to indicate script execution
echo "Cron job executed successfully. Timestamp written to $filePath";
?>
