<?php
header('Content-type', "application/json");

require_once __DIR__ . '/../libs/Link.php';

$links = json_decode($_POST['data']['links']);
$tracking = $_POST['data']['tracking'];

$Link = new \Eperflex\Email\Message\Link();

$trackedLinks = array();
foreach ($links as $link)
{
    if (strpos($link, 'http') !== false)
        $trackedLinks[] = $Link->trackURL($link, $tracking);
    else
        $trackedLinks[] = $link;
}

die(json_encode($trackedLinks));