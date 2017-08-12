<?php
require_once __DIR__ . '/../config/define.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../libs/Dedup.php';
require_once __DIR__ . '/../libs/dbMan.php';
require_once __DIR__ . '/../libs/Utils.php';

/**
 * @param $dedupData
 * @param $files
 *
 * @return bool
 */
function checkData($dedupData, $files)
{
    $wlExt =  array('csv','txt');

    if (!isset($files))
    {
        echo "<p class='alert radius label'>Le fichier n\'est pas accessible et a surement été rejeté par le serveur. :(</p>";
        return false;
    }
    $ext = pathinfo($files['file']['name'], PATHINFO_EXTENSION);
    if (empty($files['file']['tmp_name']) && !isset($dedupData['extraire_md5']))
        echo '<p class="alert radius label">Indiquer un fichier à uploader.</p>';
    else if (!in_array($ext, $wlExt))
    {
        echo '<p class="alert radius label">Le fichier de ref doit être au format csv ou txt.</p>';
        echo '<p class="alert radius label">Vous ne passerez pas !</p><img src="/img/you_shall_not_pass.jpg" />';
    }
    else if (empty($dedupData['base']) || empty($dedupData['table']))
        echo '<p class="alert radius label">Indiquer une base et une table.</p>';
    else if (preg_match("/[^a-z0-9]/i", $dedupData['base']) === 1)
    {
        echo '<p class="alert radius label">Nom incorrect pour la base.</p>';
        echo '<p class="alert radius label">Pas d\accents, pas d\'espaces. :-)</p><img class="center" src="/img/lolnop.jpg" />';
    }
    else if (preg_match("/[^a-z0-9]/i", $dedupData['table']) === 1)
    {
        echo '<p class="alert radius label">Nom incorrect pour la table.</p>';
        echo '<p class="alert radius label">Pas d\accents, pas d\'espaces. :-)</p><img class="center" src="/img/lolnop.jpg" />';
    }
    else if (empty($dedupData['delimiter']))
        echo '<p class="alert radius label">Indiquer un delimiter.</p>';
    else if (empty($dedupData['receiver']) && filter_var($dedupData['receiver'], FILTER_VALIDATE_EMAIL))
        echo '<p class="alert radius label">Indiquer une adresse mail pour l\'envoi du dédup.</p>';
    else
        return true;

    return false;
}


// Très important pour la création des dossiers !
umask(0);

if (!checkData($_POST, $_FILES))
    return false;

$tables = dbman::get_instance()->multiResQuery("SHOW TABLES FROM " . DBNAME);
$tables = Utils::sanitizeArray($tables);

$dedup    = new Dedup($_POST, $_FILES['file']['tmp_name']);
$table    = $dedup->base . '_' . $dedup->table;
$receiver = (!empty($_POST['receiver']) && filter_var($_POST['receiver'], FILTER_VALIDATE_EMAIL)) ? $_POST['receiver'] : false;

if (isset($_POST['envoyer_ref']) || isset($_POST['extraire_md5']))
{
    if (isset($_POST['envoyer_ref']))
    {
        if (in_array($table . '_ref', $tables))
            dbMan::get_instance()->query("TRUNCATE TABLE " . $table . '_ref');
        if (in_array($table . '_md5', $tables))
            dbMan::get_instance()->query("TRUNCATE TABLE " . $table . '_md5');
        $ret = $dedup->checkCSVFields($_POST['concat_field1'], $_POST['concat_field2']);
        if ($ret)
            $dedup->importFileIntoDb();
        else
            die ('Error on : checkCSVFields');
    }
    if ((isset($_POST['extraire_md5']) && in_array($table . '_ref', $tables)) || isset($_POST['envoyer_ref']))
    {
        $nb = $dedup->downloadMd5($table);
        if ($nb === 0)
            echo "<p class='alert radius label'>Aucune adresse insérée.</p>";
        else
            echo "<p>Nombre d'adresses insérées : $nb.</p>";

        Utils::do7z(EXT_FILES . '/' . $table . '/' . $table);
        Utils::doTxt(EXT_FILES . '/' . $table . '/' . $table);

        $url        = EXT_FILES_URL . '/' . $table . '/' . $table;
        $filesOnFtp = Utils::sendDedupViaFTP($table, true);
        if ($receiver)
            Utils::sendMail($filesOnFtp, true, $table, $receiver);

        echo '<div class="row">
            <div class="button-bar large-6 small-centered columns">
                <a class="success button" download="' . $table . '.csv" href="' . $url . '.csv">CSV</a>
                <a class="success button" download="' . $table . '.7z"  href="' . $url . '.txt.7z">7z</a>
                <a class="success button" download="' . $table . '.txt" href="' . $url . '.csv">TXT</a>
            </div>
          </div>';
        $dedup->displayDedup();
    }
}
else if (isset($_POST['no_webservice']))
{
    echo "<h1>NIY</h1>";
    echo "<pre>";var_dump($_POST, $_FILES);return;
}
else
{
    if (isset($_POST['comparaison']))
    {
        if (in_array($table . '_md5', $tables))
            dbMan::get_instance()->query("DROP TABLE " . $table . '_md5');

        if (in_array($table . '_ref', $tables))
        {
            $dedup->importMD5IntoDb($table);
            $nbEmails = $dedup->compareMd5Hash($table);
            if ($nbEmails == 0)
                echo '<p class="alert radius label">Aucune adresse trouvé.</p>';
            else
            {
                Utils::doTxt(EXT_FILES . "/$table/mails_existants_$table");
                $filesOnFtp = Utils::sendDedupViaFTP($table, false);
                if ($receiver)
                    Utils::sendMail($filesOnFtp, false, $table, $receiver, $nbEmails);

                echo "
                    <p class='success radius label'>Nombre d'adresse trouvé : $nbEmails</p>
                    <a class='success button radius' download='mails_existants_$table.txt' href='" . EXT_FILES_URL . "/$table/mails_existants_$table.txt'>download file</a>
                ";
            }
        }
        else
            echo '<p class="alert radius label">La table de référence \'' . $table . '_ref\' n\'existe pas.</p>';
    }
}
