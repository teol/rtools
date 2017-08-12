<?
require_once __DIR__ . '/config/define.php';
require_once __DIR__ . '/config/whoops.php';
require_once __DIR__ . '/libs/dbMan.php';
require_once __DIR__ . '/libs/Dedup.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>R'M Tools</title>
    <?php require_once 'components/header-metas.html'; ?>
</head>
<body>

<?php require_once 'components/header.html'; ?>

<div class="row">
<aside class="large-3 columns">
    <div class="panel">
        <h5>Annonce</h5>
        <p>R'M Tools est en version beta.</p>
        <p>
            N'hésitez donc pas à m'<a href="mailto:nicolas.jamet@rentabiliweb.com">envoyer un mail</a>,<br/>
            Ou sur Skype, <br/>
            Ou directement dans le bureau des devs...<br /><br />
            si vous êtes courageux.<br/>
        </p>
    </div>
</aside>
<div class="large-9 columns" role="content">
    <section id="update" class="panel">
        <h4>Dernière mise à jour du 09 Décembre 2014</h4>
        <ul>
            <li>Ajout du tool <i>Linkor</i></li>
        </ul>
    </section>
    <section class="app-grid">
        <ul class="small-block-grid-3">
            <li>
                <a href="apps/dedup/dedup.php">
                    <article>
                        <img src="/img/dedup.png">
                        <h5>Dedup</h5>
                    </article>
                </a>
            </li>
            <li>
                <a href="apps/sanitizekitmail/sanitizeKitmail.php">
                    <article>
                        <img src="/img/sanitizekitmail.png">
                        <h5>Sanitize Kitmail</h5>
                    </article>
                </a>
            </li>
            <li>
                <a href="apps/linkor/linkor.php">
                    <article>
                        <img id="linkor" src="/img/linkor.png" />
                        <h5>Linkor</h5>
                    </article>
                </a>
            </li>
        </ul>
    </section>
</div>

<?php require_once 'components/footer.html'; ?>
<?php require_once 'components/footer-metas.html'; ?>
    <script>var app = "display";</script>
    <script src="/js/display.js"></script>
</body>
</html>