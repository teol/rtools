<?php

if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] === 'rmtools.dev5.int.rtblw.com')
{
	define('inProduction', true);
	define("LOG_FOLDER", "/home/njamet/www/dedup/log/");
	define("LOG_FILE", "/home/njamet/www/dedup/log/production.log");

	define("PATH_TO_LIB", "/home/njamet/www/dedup/libs");
	define("BASE_URL", "http://rmtools.dev5.int.rtblw.com"); //url vers le bootstrap du front
	define("EXT_FILES", "/home/njamet/www/dedup/uploads"); //Chemin vers le dossier contenant les uploads
	define("EXT_FILES_URL", "http://rmtools.dev5.int.rtblw.com/uploads"); //url vers le dossier contenant les uploads
	define("PATH_TO_WHOOPS", "/home/njamet/www/dedup/modules/Whoops/src/Whoops");
}
else
{
	define('inProduction', false);
	define("LOG_FOLDER", "/home/njamet/www/dedup-dev/log/");
	define("LOG_FILE", "/home/njamet/www/dedup-dev/log/development.log");

	define("PATH_TO_LIB", "/home/njamet/www/dedup-dev/libs");
	define("BASE_URL", "http://dev.rmtools.dev5.int.rtblw.com"); //url vers le bootstrap du front
	define("EXT_FILES", "/home/njamet/www/dedup-dev/uploads"); //Chemin vers le dossier contenant les uploads
	define("EXT_FILES_URL", "http://dev.rmtools.dev5.int.rtblw.com/uploads"); //url vers le dossier contenant les uploads
	define("PATH_TO_WHOOPS", "/home/njamet/www/dedup-dev/modules/Whoops/src/Whoops");
}

define("PATH_TO_CONFIG", __DIR__);
define("ERROR_EMAIL", "nicolas.jamet@rentabiliweb.com");

//error message.
define("CREATING_FILE_ERR", "Erreur lors de la création du fichier : ");
define("__ERR_WRITE_KEY__", "Erreur lors de l'écriture de la clé dans le fichier<br />");
define("__ERR_WRITE_CONTENT__", "Erreur lors de la copie du content dans le fichier. <br />");