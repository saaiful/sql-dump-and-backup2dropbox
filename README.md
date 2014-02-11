sql-dump-and-backup2dropbox
===========================

This is a Database Dumper in php and mysql with PDO. This script is for dumpaning database and to move that in Dropbox.

Usage
===========================
Fill this Configuration part and run 'backup.php'.
```
/*Database Configuration
==========================================*/
$dbhost = ""; // Databade host
$dbuser = ""; // Databade username
$dbpass = ""; // Databade password
$dbname = ""; // Databade name
/*=======================================*/
/*Dropbox Configuration
=========================================*/
$dropbox_folder_path = ""; //file saving path in dropbox
$dropbox_user =""; //dropbox email
$dropbox_pass = ""; //dropbox password
/*======================================*/
```

Note
====
*Dropbox PHP class (DropboxUploader.php) comes form https://github.com/jakajancar/DropboxUploader<br>
*Go Daddy certificate (certificate.cer) comes from http://curl.haxx.se/ca/cacert.pem

