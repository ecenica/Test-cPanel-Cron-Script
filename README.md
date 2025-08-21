# Ecenica cPanel Cron Script

## Overview
This project provides a robust PHP script and test suite for verifying cron job execution on Ecenica Hosting's cPanel platform. It is designed to help users ensure their scheduled tasks run reliably, with clear logging and easy local simulation.

## Features
- **Class-based cron job script** for maintainability and extensibility
- **Dedicated logs directory** for clean log management
- **Automated test script** to simulate cron runs locally
- **Success and error logging** for easy troubleshooting
- **Best practices** for file organisation and version control

## Ecenica Hosting
Ecenica Hosting offers reliable, secure, and user-friendly web hosting solutions. With cPanel access, you can easily manage your websites, databases, and scheduled tasks. Ecenica's support and infrastructure ensure your sites and cron jobs run smoothly, making it an excellent choice for businesses and developers seeking hassle-free hosting.

### Why cPanel?
cPanel is an established, secure, and industry-leading platform trusted by millions of users worldwide. Far from being out of date, cPanel continues to evolve with modern security features, a user-friendly interface, and robust support for web technologies. Its tried-and-tested reliability makes it the preferred choice for hosting professionals and businesses who value stability, security, and ease of use.

## Getting Started
1. **Clone the repository** to your Ecenica cPanel account or local machine.
2. **Place `Cron.php` in your public_html directory**.
3. **Set up a cron job** in cPanel to run `Cron.php` at your desired schedule:
   ```
   /usr/bin/php /home/yourusername/public_html/Cron.php
   ```
4. **Check your cron emails** for success and error messages.
5. **Run local tests** using the script in `tests/CronTest.php`:
   ```
   php tests/CronTest.php
   ```

## Project Structure
```
Cron.php           # Main cron job script
logs/              # All log files (success, error)
cron/              # Output files from cron job
.tests/
  CronTest.php     # Test script for local simulation
```

## Version Control
A `.gitignore` is included to keep logs, cron output, and system files out of version control.

## License
MIT

## Support
For help with Ecenica Hosting or this script, visit [ecenica.com](https://www.ecenica.com/) or contact their support team.
