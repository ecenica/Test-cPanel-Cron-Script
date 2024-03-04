<?php
/**
 * Cron Test Script for Ecenica cPanel
 * 
 * This script is used to test a cron job in Ecenica's cPanel.
 * It writes a timestamp to a file every time it is executed.
 * 
 * Instructions:
 * 1. Place this script in your Ecenica cPanel's public_html directory.
 * 2. Set up a cron job in Ecenica cPanel to execute this script at regular intervals.
 * 3. If using `DOCUMENT_ROOT` for the file path, consider using `wget` as a cPanel command in the cron job.
 * 4. Ensure that the 'cron' folder exists in your public_html directory and is writable.
 * 5. If using `wget`, ensure that .htaccess rules allow access to the script.
 * 
 * Note: When running from CLI, __DIR__ should be used instead of DOCUMENT_ROOT.
 * 
 * Example Cron Job Command:
 * /usr/bin/php /home/yourusername/public_html/cron_test.php
 * 
 * Author: Ecenica
 * Version: 1.1
 */

// Get the path to the public_html directory when running from CLI
$publicHtmlPath = __DIR__;

// Specify the subdirectory name
$subdirectory = '/cron/';

// Set the folder path where the timestamp will be stored
$folderPath = $publicHtmlPath . $subdirectory;

// Check if the folder exists, if not, create it
if (!file_exists($folderPath)) {
    mkdir($folderPath, 0777, true);
}

// Set the file path where the timestamp will be stored
$filePath = $folderPath . 'cron_test.txt';

// Open the file (create it if it doesn't exist) in append mode
$file = fopen($filePath, 'a');

// Write the current timestamp to the file
fwrite($file, date('Y-m-d H:i:s') . PHP_EOL);

// Close the file
fclose($file);

// Output a message to indicate script execution
echo "Cron job executed successfully. Timestamp written to $filePath";
?>
