Postfix Mallog Parser And Importer Written in PHP
=================================================
This is a simple command-line utility that when used will import the statuses of the Postfix messages queued on your email server. 

The application can be used to parse the lines of the mail.log file and import them into a database storage - the currently supported database storages are MongoDB or MySQL/MariaDB.

**Usage: `php ./maillog-importer.php /var/log/mail.log`**

Please have a quick look at the files in the "config" directory before using this tool.
