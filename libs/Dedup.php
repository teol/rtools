<?php
    /**
     * Created by JetBrains PhpStorm.
     * User: Neodern
     * Date: 10/09/13
     * Time: 14:55
     * To change this template use File | Settings | File Templates.
     */

    require_once __DIR__ . '/../config/define.php';
    require_once __DIR__ . '/dbMan.php';
    require_once __DIR__ . '/benchmarker.php';
    require_once __DIR__ . '/Utils.php';

    class Dedup
    {

        /**
         * @var DbMan
         */
        public $link;

        /**
         * @var string
         */
        public $base;

        /**
         * @var string
         */
        public $table;

        /**
         * @var bool
         */
        public $isUpper;

        /**
         * @var string
         */
        public $upperOrLower;

        /**
         * @var string
         */
        public $key;

        /**
         * @var string
         */
        public $delimiter;

        /**
         * @var string
         */
        public $file;

        /**
         * @var int
         */
        public $concat_field1;

        /**
         * @var int
         */
        public $concat_field2;

        /**
         * @var int
         */
        public $email_field;

        /**
         * @var resource
         */
        public $file_fd;

        /**
         * @var bool
         */
        public $concate;

        /**
         * @var bool
         */
        public $field2IsCp;

        /**
         * An other constructor ...
         *
         * @param array  $dedupData
         * @param string $file_
         */
        public function __construct(array $dedupData, $file_ = '')
        {

			foreach ($dedupData as $key => $value)
				$this->$key = ($value == "NULL" || $value == "") ? null : $value;

			$this->link          = dbMan::get_instance();
            $this->file          = $this->link->real_escape_string($file_);
            $this->table         = strtolower($this->link->real_escape_string($this->table));
            $this->key           = $this->key ? $this->link->real_escape_string($this->key) : null;
			$this->concate		 = $this->concate === "on" ? true : false;
			if ($this->email_field === NULL)
				$this->email_field = 0;
        }

        /**
         * Check for a valid CSV, with email column, etc.
         * Create the table if CSV is valid.
         *
         * @param $field1
         * @param $field2
         *
         * @return bool
         */
        public function checkCSVFields($field1, $field2)
        {
            if (($this->file_fd = fopen($this->file, "r")) !== false)
            {
                $first_line = fgetcsv($this->file_fd, $this->delimiter);
				if (!(!strpos($first_line[0], $this->delimiter) && (filter_var($first_line[0], FILTER_VALIDATE_EMAIL))))
				{
					$first_line = explode($this->delimiter, $first_line[0]);
					if (($this->email_field = array_search('email', array_map('strtolower', $first_line))) === false)
					{
						echo "<p class='alert radius label'>Aucune colonne 'email' trouvée.</p>";
						return false;
					}
				}
				if (!empty($field1) && !($this->concat_field1 = array_search($field1, $first_line)))
                    echo "<p class='alert radius label'>Colonne '$field1' introuvable.</p>";
                else if (!empty($field2) && !($this->concat_field2 = array_search($field2, $first_line)))
                    echo "<p class='alert radius label'>Colonne '$field2' introuvable.</p>";
                else if ((!empty($field1) && empty($field2)) || (empty($field1) && !empty($field2)))
                    echo "<p class='alert radius label'>Aucun ou tous les champs de concaténations doivent être remplis.</p>";
                else
                {
                    $this->link->query('
                        CREATE TABLE IF NOT EXISTS `%s_%s_ref`
                        (email VARCHAR(255), md5 CHAR(32), INDEX(md5))
                    ', array($this->base, $this->table));

                    return true;
                }
            }

            return false;
        }


        /**
         * Format key will all options.
         * Options are the same as CSV.
         *
         * @return bool
         */
        private function sanitizeKey()
        {
            Utils::echoIfDebug("key : " . $this->key);
            $this->key = Utils::stripAccents($this->key);
            Utils::echoIfDebug("no accent : " . $this->key);
            $this->key = str_replace(array("'", ' '), '', $this->key);
            Utils::echoIfDebug("no special char : " . $this->key);
            $this->key = $this->upperOrLower == 'uppercase' ? strtoupper($this->key) : (($this->upperOrLower == 'lowercase') ? (strtolower($this->key)) : $this->key);
            Utils::echoIfDebug("maj/min : " . $this->key);
            $this->key = $this->isUpper ? strtoupper(md5($this->key)) : md5($this->key);
            Utils::echoIfDebug("md5 : " . $this->key);

            return true;
        }

        /**
         * @param $tableSQL
         *
         * @return int
         */
        public function downloadMd5($tableSQL)
        {
            @mkdir(EXT_FILES . "/" . $tableSQL, 0777, true);

            $chemin = EXT_FILES . "/" . $tableSQL . '/' . $tableSQL;
            if (is_file($chemin . '.tmp'))
                unlink($chemin . '.tmp');

            $isCreated = $this->link->query('
                SELECT (md5)
                INTO OUTFILE "%s.tmp' . '"
                FROM `%s_ref`
            ', array($chemin, $tableSQL));

            if (!$isCreated)
                die (CREATING_FILE_ERR . $this->link->error);

            $nb = $this->link->affected_rows;
            if ($nb == 0)
                unlink($chemin);
            else
            {
                $file = fopen($chemin . '.csv', 'w');
                if (!$file)
                    die('Impossible de créer le fichier : ' . $chemin . '.csv');

                if ($this->key)
                {
                    $this->sanitizeKey();
                    if (!fwrite($file, $this->key . "\n"))
                        die (__ERR_WRITE_KEY__);
                }

                if (!fwrite($file, file_get_contents($chemin . '.tmp')))
                    die (__ERR_WRITE_CONTENT__);
                else
                {
                    unlink($chemin . '.tmp');
                    fclose($file);
                }
            }

            return $nb;
        }

        /**
         *
         */
        public function importFileIntoDb()
        {
            $i = 0;
			$q = 'INSERT IGNORE INTO `' . $this->base . '_' . $this->table . '_ref` VALUES ';
			ini_set("auto_detect_line_endings", true);

			for (;($line = fgets($this->file_fd)) !== false;)
			{
				$line = trim($line);
				$data = explode($this->delimiter, $line);
				if ($this->concate && (empty($data[$this->concat_field1]) || empty($data[$this->concat_field2])))
					continue;
				if ($this->concate)
                    $field2 = $this->field2IsCp ? Utils::addZero($data[$this->concat_field2]) : $data[$this->concat_field2];
                if ($this->field2IsCp && !isset($field2))
					continue;

                $hash = $this->concate ? ($data[$this->concat_field1] . $field2) : $data[$this->email_field];
                $hash = Utils::stripSpecialChars($hash, $this->table);
                $hash = Utils::stripAccents($hash);
                $hash = $this->upperOrLower == 'uppercase' ? strtoupper($hash) : (($this->upperOrLower == 'lowercase') ? (strtolower($hash)) : $hash);
                $hash = $this->isUpper ? strtoupper(md5($hash)) : md5($hash);

                $q .= "\n('" . $this->link->real_escape_string($data[$this->email_field]) . "', '" . $hash . "'),";
                ++$i;
                if (!($i % 50))
                {
                    $q = substr($q, 0, -1); // removing last comma
                    $this->link->query($q); // actually performing the query into db
                    $q = 'INSERT IGNORE INTO `' . $this->base . '_' . $this->table . '_ref` VALUES ';
                }
            }
            fclose($this->file_fd);
            $q = substr($q, 0, -1); // removing last comma
			$this->link->query($q); // actually performing the query into db
		}

        /**
         * @param $tableSQL
         */
        public function importMD5IntoDb($tableSQL)
        {
            chmod($this->file, 0777);

            $this->link->query('CREATE TABLE IF NOT EXISTS `' . $tableSQL . '_md5` (md5 char(32),INDEX (md5))');
            $this->link->query('
                LOAD DATA INFILE "%s"
                INTO TABLE `%s_md5`
                FIELDS TERMINATED BY ";"
                LINES STARTING BY ""
                TERMINATED BY "\n"
                (md5)
            ', array($this->file, $tableSQL));

        }

        /**
         * @param $tableSQL
         *
         * @return int
         */
        public function compareMd5Hash($tableSQL)
        {
            @mkdir(EXT_FILES . '/' . $tableSQL, 0777, true);

            $chemin = EXT_FILES . '/' . $tableSQL . '/mails_existants_' . $tableSQL . '.csv';
            if (is_file($chemin))
                unlink($chemin);

            $isCreated = $this->link->query("
                    SELECT DISTINCT concat(email,';')
                    INTO OUTFILE '%s'
                    FROM `%s_ref`
                    JOIN `%s_md5` USING (md5)
                ", array($chemin, $tableSQL, $tableSQL)
            );

            if (!$isCreated)
                die (CREATING_FILE_ERR . $this->link->error);

            $nb = $this->link->affected_rows;
            if ($nb == 0)
                unlink($chemin);

            return $nb;
        }

        public static function getDedupTables()
        {
            $tables = DbMan::get_instance()->multiResQuery('SHOW TABLES');
            $tables = Utils::sanitizeArray($tables);

            $result = array();
            foreach ($tables as $table)
            {
                $key = explode('_', $table);
                if (!isset($result[$key[0]][$key[1]]))
                    $result[$key[0]][$key[1]] = '';
                $result[$key[0]][$key[1]] .= '+';
            }

            return $result;
        }

        /**
         *
         */
        public function displayDedup()
        {
            Utils::echoIfDebug('file : ' . $this->file);
            Utils::echoIfDebug('base : ' . $this->base);
            Utils::echoIfDebug('table : ' . $this->table);
            if ($this->isUpper === true)
                Utils::echoIfDebug('isUpper : true', 'displayDedup');
            else
                Utils::echoIfDebug('isUpper : false', 'displayDedup');
            Utils::echoIfDebug('key : ' . $this->key, 'displayDedup');
            Utils::echoIfDebug('colonne email : ' . $this->email_field, 'displayDedup');
            Utils::echoIfDebug('colonne concat 1 : ' . $this->concat_field1, 'displayDedup');
            Utils::echoIfDebug('colonne concat 2 : ' . $this->concat_field2, 'displayDedup');
        }
    }