<?php
/**
 * Cron Script for Ecenica cPanel
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
 * 6. You can also test this script using SSH by navigating to the directory containing this script and running: `php Cron.php`
 *
 * Note: When running from CLI, __DIR__ should be used instead of DOCUMENT_ROOT.
 *
 * Example Cron Job Commands:
 *
 * /usr/bin/php /home/yourusername/public_html/Cron.php
 *
 * wget -q -O - https://example.com/Cron.php
 *
 * curl https://example.com/Cron.php
 *
 * Example Cron Job Commands with Logging:
 *
 * /usr/bin/php /home/yourusername/public_html/Cron.php >> /home/yourusername/logs/cron.log 2>&1
 *
 * wget -q -O - https://example.com/Cron.php >> /home/yourusername/cron.log 2>&1
 *
 * curl -s https://example.com/Cron.php >> /home/yourusername/cron.log 2>&1
 *
 * Author: Ecenica
 * Version: 2.3 - Updated examples
 */

class Cron {
    private $publicHtmlPath;
    private $subdirectory = '/cron/';
    private $folderPath;
    private $filePath;
    private $successLogPath;
    private $errorLogPath;
    private $logsPath;

    public function __construct() {
        $this->publicHtmlPath = __DIR__;
        $this->logsPath = $this->publicHtmlPath . '/logs/';
        $this->folderPath = $this->publicHtmlPath . $this->subdirectory;
        $this->filePath = $this->folderPath . 'cron_test.txt';
        $this->successLogPath = $this->logsPath . 'success_log.txt';
        $this->errorLogPath = $this->logsPath . 'error_log.txt';
    }

    /**
     * Log error messages to an error log file
     * @param string $message Error message to log
     * @param Exception|Error|null $exception Optional exception object
     */
    public static function logError($message, $exception = null) {
        try {
            $publicHtmlPath = __DIR__;
            $logsPath = $publicHtmlPath . '/logs/';
            $errorLogPath = $logsPath . 'error_log.txt';
            if (!file_exists($logsPath)) {
                mkdir($logsPath, 0777, true);
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
            if (file_put_contents($errorLogPath, $logEntry, FILE_APPEND | LOCK_EX) === false) {
                error_log("Cron Script Error: $message");
            }
        } catch (Exception $logException) {
            error_log("Cron Script Error (logging failed): $message");
            error_log("Logging Exception: " . $logException->getMessage());
        }
    }

    public function run() {
        try {
            // Check if the folder exists, if not, create it
            if (!file_exists($this->folderPath)) {
                try {
                    if (!mkdir($this->folderPath, 0777, true)) {
                        throw new Exception("Failed to create directory: {$this->folderPath}");
                    }
                } catch (Exception $e) {
                    self::logError("Directory creation failed for path: {$this->folderPath}", $e);
                    throw $e;
                }
            }
            // Verify directory is writable
            if (!is_writable($this->folderPath)) {
                $error = "Directory is not writable: {$this->folderPath}";
                self::logError($error);
                throw new Exception($error);
            }
            try {
                $file = fopen($this->filePath, 'a');
                if ($file === false) {
                    throw new Exception("Failed to open file: {$this->filePath}");
                }
                if (!flock($file, LOCK_EX)) {
                    throw new Exception("Failed to lock file: {$this->filePath}");
                }
                $timestamp = date('Y-m-d H:i:s');
                $bytesWritten = fwrite($file, $timestamp . PHP_EOL);
                if ($bytesWritten === false) {
                    throw new Exception("Failed to write to file: {$this->filePath}");
                }
                flock($file, LOCK_UN);
                fclose($file);
                $successMessage = "[$timestamp] SUCCESS: Cron job executed successfully. Timestamp written to {$this->filePath}";
                echo $successMessage . PHP_EOL;
                // Write to success log
                if (!file_exists($this->logsPath)) {
                    mkdir($this->logsPath, 0777, true);
                }
                file_put_contents($this->successLogPath, $successMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
            } catch (Exception $e) {
                if (isset($file) && is_resource($file)) {
                    fclose($file);
                }
                self::logError("File operation failed for: $this->filePath", $e);
                throw $e;
            }
        } catch (Exception $e) {
            $errorMessage = "Cron script execution failed: " . $e->getMessage();
            self::logError($errorMessage, $e);
            echo "ERROR: $errorMessage" . PHP_EOL;
            exit(1);
        } catch (Error $e) {
            $errorMessage = "PHP Fatal Error in cron script: " . $e->getMessage();
            self::logError($errorMessage, $e);
            echo "FATAL ERROR: $errorMessage" . PHP_EOL;
            exit(1);
        }
        exit(0);
    }
}

// Run the cron job
$cron = new Cron();
$cron->run();
?>
