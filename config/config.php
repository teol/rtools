<?php
include_once __DIR__ . '/../vendor/autoload.php';

if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'rmtools.dev5.int.rtblw.com')
{
	//prod
	define('DBHOST', 'localhost');
	define('DBUSER', 'root');
	define('DBPWD', 'root');
	define('DBNAME', 'njamet_dedup');

	define('FTP_PATH', '/mail.rpubm.com/dedup/ref_files');
	define('FTP_URL_FILES', 'http://mail5.rpubm5.com/dedup/ref_files');
}
else
{
	//dev
	define('DBHOST', 'localhost');
	define('DBUSER', 'root');
	define('DBPWD', 'root');
	define('DBNAME', 'njamet_dedup_dev');

	define('FTP_PATH', '/mail.rpubm.com/dedup/delete_dis_pls');
	define('FTP_URL_FILES', 'http://mail5.rpubm5.com/dedup/delete_dis_pls');
}

define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 25);
define('SMTP_AUTHMODE', false);
define('SMTP_PERSIST', true);
define('SMTP_MAIL_FROM', 'contact@eperflex.com');

define('FTP_HOST', 'ftp.rpublishing.fr');
define('FTP_USER', 'rpub-performance');
define('FTP_PASSWD', 'Twyewyecet0');
