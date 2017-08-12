<?php
    require_once __DIR__ . '/../config/define.php';

    class Utils
    {
        /**
         * Generate a password with varaible length en strength
         * @static
         * @param int $length
         * @param int $strength
         * @return string
         */
        public static function generatePassword($length = 9, $strength = 0)
        {
            $vowels     = 'aeuy';
            $consonants = 'bdghjmnpqrstvz';
            if ($strength & 1)
                $consonants .= 'BDGHJLMNPQRSTVWXZ';
            if ($strength & 2)
                $vowels .= "AEUY";
            if ($strength & 4)
                $consonants .= '23456789';
            if ($strength & 8)
                $consonants .= '@#$%';

            $password = '';
            $alt      = time() % 2;
            for ($i = 0; $i < $length; $i++)
            {
                if ($alt == 1)
                {
                    $password .= $consonants[(rand() % strlen($consonants))];
                    $alt = 0;
                }
                else
                {
                    $password .= $vowels[(rand() % strlen($vowels))];
                    $alt = 1;
                }
            }
            return $password;
        }

        /**
         * Push a message into a log file
         * @static
         * @param string $name    the name of the log file
         * @param string $title   the title og the error
         * @param string $message the message error
         */
        public static function logError($name, $title, $message)
        {
            @mkdir(LOG_FOLDER, 0755, true);
            file_put_contents(LOG_FOLDER . $name . "-" . date("d-m-Y"), '"' . date("c") . '";"' . $title . '";"' . $message . '"' . "\n", FILE_APPEND);
        }

        /**
         * Hash a password according to the methode used in the bdd
         * @static
         * @param string $pwd the password to be hashed
         * @return string
         */
        public static function getHashedPassword($pwd)
        {
            $pwd = md5("2x509" . $pwd . "a53g41t");
            return md5(substr($pwd, 2, 10) . substr($pwd, 20, 10));
        }

        /**
         * @static
         * @return bool
         */
        public static function isInternetExplorer()
        {
            return (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false));
        }

        /**
         * @param $string
         *
         * @return bool
         */
        public static function isJson($string)
        {
            return is_string($string) && is_object(json_decode($string)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
        }

        /**
         * @param $link
         *
         * @return bool
         */
        public static function isEncoded($link)
        {
            return !!preg_match('~%[0-9A-F]{2}~i', $link);
        }

        /**
         * @param $json
         *
         * @return bool
         */
        public static function isJsonEmpty($json)
        {
            $array = json_decode($json);
            foreach ($array as $key => $value)
            {
                if (!empty($value))
                    return false;
            }
            return true;
        }

        /**
         * @param $string
         *
         * @return string
         */
        public static function stripAccents($string)
        {
            $string = str_replace(
                array(
                    'à', 'â', 'ä', 'á', 'ã', 'å',
                    'î', 'ï', 'ì', 'í',
                    'ô', 'ö', 'ò', 'ó', 'õ', 'ø',
                    'ù', 'û', 'ü', 'ú',
                    'é', 'è', 'ê', 'ë',
                    'ç', 'ÿ', 'ñ',
                ),
                array(
                    'a', 'a', 'a', 'a', 'a', 'a',
                    'i', 'i', 'i', 'i',
                    'o', 'o', 'o', 'o', 'o', 'o',
                    'u', 'u', 'u', 'u',
                    'e', 'e', 'e', 'e',
                    'c', 'y', 'n',
                ),
                $string
            );

			return $string;
        }

		/**
		 * @param $string
		 * @param $table
		 *
		 * @return mixed
		 */
		public static function stripSpecialChars($string, $table)
		{
			if ($table === "allianz")
				return str_replace(array("'", "-", " "), '', $string);
			return str_replace(array("'", " "), '', $string);
		}

        /**
         * @param $string
         *
         * @return mixed
         */
        public static function addZero($string)
        {
            $len = strlen($string);
            if ($len == 1 || $len == 3)
                return null;
            while (strlen($string) < 5)
            {
                if ($len == 2)
                    $string = $string . '0';
                else if ($len == 4)
                    $string = '0' . $string;
            }
            return $string;
        }

        /**
         * @param $array
         *
         * @return array
         */
        public static function sanitizeArray($array)
        {
            $sanitized = array();
            foreach ($array as $value)
            {
                foreach ($value as $value2)
                {
                    $sanitized[] = $value2;
                }
            }
            return $sanitized;
        }

		/**
		 * @param $path
		 *
		 * @return string
		 */
		public static function doTxt($path)
		{
			return copy("$path.csv", "$path.txt");
		}

        /**
         * @param $path
         *
         * @return string
         */
        public static function do7z($path)
        {
            if (file_exists($path . ".txt.7z"))
                unlink($path . ".txt.7z");
            copy("$path.csv", "$path.txt");
            shell_exec("p7zip $path.txt");
            return file_exists("$path.txt.7z");
        }

		/**
		 * @param $url
		 *
		 * @return resource
		 */
		public static function URLopen($url)
		{
			// i'm a Fake user_agent
			ini_set('user_agent', 'MSIE 4\.0b2;');
			return fopen($url, 'r');
		}

		/**
		 * @param      $string
		 * @param null $functionName
		 */
		public static function echoIfDebug($string, $functionName = null)
		{
			if (inProduction)
				Utils::logError("debug", $functionName, $string);
			else
				echo $string . "<br />";
		}


        /**
         * @param $filename
         * @param $isConversion
         *
         * @return bool
         */
        public static function sendDedupViaFTP($filename, $isConversion)
        {
            $conn_id      = ftp_connect(FTP_HOST);
            $login_result = ftp_login($conn_id, FTP_USER, FTP_PASSWD);
            if (!$conn_id || !$login_result)
                return false;

            if (!@ftp_chdir($conn_id, FTP_PATH))
                if (!@ftp_mkdir($conn_id, FTP_PATH))
                    if (!@ftp_chdir($conn_id, FTP_PATH))
                        return false;

            ftp_pasv($conn_id, true);

            if ($isConversion)
            {
                $path = EXT_FILES . '/' . $filename . '/' . $filename;
                $isSent = ftp_put($conn_id, './' . $filename . '.txt', $path . '.txt', FTP_ASCII);
                $isSent = ftp_put($conn_id, './' . $filename . '.csv', $path . '.csv', FTP_ASCII) && $isSent;
                $isSent = ftp_put($conn_id, './' . $filename . '.txt.7z', $path . '.txt.7z', FTP_BINARY) && $isSent;
            }
            else
            {
                $path = EXT_FILES . '/' . $filename . '/mails_existants_' . $filename;
                $isSent = ftp_put($conn_id, './mails_existants_' . $filename . '.txt',  $path . '.txt', FTP_ASCII);
                $isSent = ftp_put($conn_id, './mails_existants_' . $filename . '.csv', $path . '.csv', FTP_ASCII) && $isSent;
            }

            ftp_close($conn_id);

            return $isSent;
        }

		/**
		 * @param      $filesOnFtp
		 * @param      $isConversion
		 * @param      $filename
		 * @param      $receiver
		 * @param null $nbMatched
		 */
		public static function sendMail($filesOnFtp, $isConversion, $filename, $receiver, $nbMatched = null)
        {
            $subject = "Dedup - Vos fichiers sont prets.";

            $headers = "MIME-Version: 1.0\n";
            $headers .= "From: \"DedupApp - RMarketing\" <no-reply@eperflex.com>\n";
            $headers .= "Content-type: multipart/mixed;\n";

            $limite = '_parties_' . md5(uniqid(rand()));
            $headers .= " boundary=\"----=$limite\"\n\n";

            $intro = "------=$limite\n";
            $intro .= "Content-type: text/html; charset=\"utf-8\"\n\n";

			$split = explode('_', $filename);
            $firstname = explode(".", $receiver);
            $firstname = ucfirst($firstname[0]);

            $intro .= "<html><body><p>Bonjour $firstname,</p>";
            $content = "
                            <p>Votre dédup a été réalisé avec succès.</p>
                            <p>Base : $split[0]<br />Table : $split[1]</p>
                            <p>Voici les liens vers vos fichiers : </p>
                        ";

            if ($nbMatched)
				$content .= "<p>Nombre d'adresses matchées : $nbMatched.</p>";
			if ($isConversion && $filesOnFtp)
            {
                $content .= "<a href='" . FTP_URL_FILES . "/" . $filename . ".txt'>Fichier au format txt</a><br />";
                $content .= "<a href='" . FTP_URL_FILES . "/" . $filename . ".csv'>Fichier au format csv</a><br />";
                $content .= "<a href='" . FTP_URL_FILES . "/" . $filename . ".txt.7z'>Fichier au format 7z</a>";
            }
            else if (!$isConversion)
            {
                $content .= "<a href='" . FTP_URL_FILES . "/mails_existants_" . $filename . ".txt'>Fichier au format txt</a><br />";
                $content .= "<a href='" . FTP_URL_FILES . "/mails_existants_" . $filename . ".csv'>Fichier au format csv</a><br />";
            }
            else
                $content = "<p>Une erreur s'est produite lors de l'upload de vos fichiers sur le ftp.</p>";

            $end = "<br /><br /><p>--<br />Dedup<br /><i>Just another awsome app.</i></p></body></html>";
            $texte = $intro . $content . $end;

			if (inProduction === false)
				$receiver = "nicolas.jamet@rentabiliweb.com";

			mail($receiver, $subject, $texte, $headers);
        }
    }
