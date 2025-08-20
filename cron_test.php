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
 * 6. You can also test this script using SSH by navigating to the directory containing this script and running: `php cron_test.php`
 * 
 * Note: When running from CLI, __DIR__ should be used instead of DOCUMENT_ROOT.
 * 
 * Example Cron Job Command:
 * /usr/bin/php /home/yourusername/public_html/cron_test.php
 * 
 * Author: Ecenica
 * Version: 2.0 - Added error handling and logging
 */

/**
 * Log error messages to an error log file
 * @param string $message Error message to log
 * @param Exception|null $exception Optional exception object
 */
function logError($message, $exception = null) {
    try {
        $publicHtmlPath = __DIR__;
        $errorLogPath = $publicHtmlPath . '/cron/error_log.txt';
        
        // Ensure the cron directory exists
        $cronDir = dirname($errorLogPath);
        if (!file_exists($cronDir)) {
            mkdir($cronDir, 0777, true);
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] ERROR: $message";
        
        if ($exception) {
            $logEntry .= " | Exception: " . $exception->getMessage();
            $logEntry .= " | File: " . $exception->getFile();
            $logEntry .= " | Line: " . $exception->getLine();
            $logEntry .= " | Trace: " . $exception->getTraceAsString();
        }
        
        $logEntry .= PHP_EOL;
        
        // Try to write to error log
        if (file_put_contents($errorLogPath, $logEntry, FILE_APPEND | LOCK_EX) === false) {
            // If we can't write to file, try to output to error log
            error_log("Cron Script Error: $message");
        }
    } catch (Exception $logException) {
        // Last resort - use PHP's error_log function
        error_log("Cron Script Error (logging failed): $message");
        error_log("Logging Exception: " . $logException->getMessage());
    }
}

try {
    // Get the path to the public_html directory when running from CLI
    $publicHtmlPath = __DIR__;

    // Specify the subdirectory name
    $subdirectory = '/cron/';

    // Set the folder path where the timestamp will be stored
    $folderPath = $publicHtmlPath . $subdirectory;

    // Check if the folder exists, if not, create it
    if (!file_exists($folderPath)) {
        try {
            if (!mkdir($folderPath, 0777, true)) {
                throw new Exception("Failed to create directory: $folderPath");
            }
        } catch (Exception $e) {
            logError("Directory creation failed for path: $folderPath", $e);
            throw $e;
        }
    }

    // Verify directory is writable
    if (!is_writable($folderPath)) {
        $error = "Directory is not writable: $folderPath";
        logError($error);
        throw new Exception($error);
    }

    // Set the file path where the timestamp will be stored
    $filePath = $folderPath . 'cron_test.txt';

    try {
        // Open the file (create it if it doesn't exist) in append mode
        $file = fopen($filePath, 'a');
        
        if ($file === false) {
            throw new Exception("Failed to open file: $filePath");
        }

        // Lock the file for writing
        if (!flock($file, LOCK_EX)) {
            throw new Exception("Failed to lock file: $filePath");
        }

        // Write the current timestamp to the file
        $timestamp = date('Y-m-d H:i:s');
        $bytesWritten = fwrite($file, $timestamp . PHP_EOL);
        
        if ($bytesWritten === false) {
            throw new Exception("Failed to write to file: $filePath");
        }

        // Release the lock
        flock($file, LOCK_UN);

        // Close the file
        fclose($file);

        // Log successful execution
        $successMessage = "[$timestamp] SUCCESS: Cron job executed successfully. Timestamp written to $filePath";
        echo $successMessage . PHP_EOL;
        
        // Also log success to a separate success log
        $successLogPath = $folderPath . 'success_log.txt';
        file_put_contents($successLogPath, $successMessage . PHP_EOL, FILE_APPEND | LOCK_EX);

    } catch (Exception $e) {
        // Close file if it was opened
        if (isset($file) && is_resource($file)) {
            fclose($file);
        }
        
        logError("File operation failed for: $filePath", $e);
        throw $e;
    }

} catch (Exception $e) {
    // Main exception handler
    $errorMessage = "Cron script execution failed: " . $e->getMessage();
    logError($errorMessage, $e);
    
    // Output error for cron job logs
    echo "ERROR: $errorMessage" . PHP_EOL;
    
    // Exit with error code
    exit(1);
} catch (Error $e) {
    // Handle PHP Fatal Errors
    $errorMessage = "PHP Fatal Error in cron script: " . $e->getMessage();
    logError($errorMessage, $e);
    
    echo "FATAL ERROR: $errorMessage" . PHP_EOL;
    exit(1);
}

// Script completed successfully
exit(0);
?>
