<?
    require_once __DIR__ . '/../../config/define.php';
    require_once __DIR__ . '/../../config/whoops.php';
    require_once __DIR__ . '/../../libs/dbMan.php';
    require_once __DIR__ . '/../../libs/Dedup.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>R'M Tools | Linkor</title>
    <link rel="stylesheet" href="linkor.css"/>
    <?php require_once '../../components/header-metas.html'; ?>
    <link rel="stylesheet" href="/modules/highlight/styles/monokai_sublime.css"/>
<body>

<header class="contain-to-grid fixed">
    <nav class="top-bar" data-topbar data-options="sticky_on: large">
        <ul class="title-area">
            <li class="name"><h1><a href="/index.php">RMTools</a></h1></li>
            <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
        </ul>
        <section class="top-bar-section">
            <ul class="right">
                <li class="divider"></li>
                <li><a id="infoButton">Help me !</a></li>
                <li class="divider"></li>
                <li app="dedup"><a href="/apps/dedup/dedup.php">Dedup</a></li>
                <li app="sanitizekit"><a href="/apps/sanitizekitmail/sanitizeKitmail.php">Sanitize Kitmail</a></li>
                <li app="linkor"><a href="/apps/linkor/linkor.php">Linkor</a></li>
            </ul>
            <ul class="left" id="anchors">
                <li class="divider"></li>
                <li><a href="#">top &uarr;</a></li>
                <li class="divider"></li>
                <li><a href="#linksBlock">Liens</a></li>
                <li><a href="#options">Options</a></li>
                <li><a href="#copy">Code</a></li>
            </ul>
        </section>
    </nav>
</header>

<!-- Main Page Content and Sidebar -->
<div class="row">
    <!-- Main Blog Content -->
    <div class="large-12 columns" role="content">

        <section id="update" class="panel">
            <h5>Dernière mise à jour du 06 Février 2015</h5>
            <h6>Bugfixes</h6>
            <ul>
                <li>bugfix sur la suppression des liens.</li>
                <li>bugfix sur l'urlencode dans les redirections.</li>
            </ul>
            <h5>Dernière mise à jour du 23 Janvier 2015</h5>
            <h6>Features</h6>
            <ul>
                <li>Suppression des liens dans le kit.</li>
                <li>Suppression de l'ancienne url de redirection présente dans le kitmail.</li>
                <li>Ajouter le tracking sur tous les liens directement grâce aux options.</li>
                <li>Ajouter un lien par defaut (home ?) qui remplacera tous les liens vides ou marquer d'un "#"</li>
            </ul>
            <h6>Bugfixes</h6>
            <ul>
                <li>Bugfix : les variables (%url%) et les liens possédant déjà le tracking ne sont pas affectés. </li>
            </ul>
        </section>
        <fieldset id="linkorBlock">
            <legend>Insérer votre kit</legend>
            <article class="info">
                <p>Mettre le kit ici ! Il faut le coller là, là !</p>
            </article>

            <textarea id="kitmail" cols="30" rows="10" autofocus="autofocus"></textarea>
            <button id="reset" class="right button tiny">Effacer</button>

        </fieldset>
        <section id="togglable">
            <fieldset id="linksBlock">

                <legend>Les liens</legend>
                <article class="info">
                    <p>Les liens sont ici !</p>
                </article>

                <div class="row">
                    <div class="large-12 columns">
                        <code id="linkored"></code>
                    </div>
                </div>

                <fieldset id="options">
                    <legend>Options</legend>
                    <article>
                        <h5>Trackings / URL</h5>
                        <div class="row">
                            <div class="row collapse">
                                <div class="small-3 large-2 columns">
                                    <span class="prefix">Url :</span>
                                </div>
                                <div class="small-6 large-7 pull-3 columns">
                                    <input id="tracking_url" type="text" placeholder="Enter your tracking...">
                                </div>
                            </div>

                            <div class="row collapse">
                                <div class="small-3 large-2 columns">
                                    <span class="prefix">Variables : </span>
                                </div>
                                <div class="small-6 large-7 pull-3 columns">
                                    <input id="tracking_variables" type="text" placeholder="Enter your tracking...">
                                </div>
                            </div>

                            <div class="row collapse">
                                <div class="small-3 large-2 columns">
                                    <span class="prefix">Url par défaut : </span>
                                </div>
                                <div class="small-6 large-7 pull-3 columns">
                                    <input id="defaultUrl" type="text" placeholder="Enter your url...">
                                </div>
                            </div>
                        </div>

                        <h5>Suppression trackings / liens</h5>
                        <div class="row">
                            <div class="row">
                                <div class="small-12 large-6 columns">
                                    <div class="row collapse">
                                        <div class="small-10 large-10 columns">
                                            <span class="prefix">Supprimer la redirection. </span>
                                        </div>
                                        <div class="small-2  large-2 columns">
                                            <a href="#" id="delete_trackingURL" class="button postfix alert">valider</a>
                                        </div>
                                    </div>
                                    <div class="row collapse">
                                        <div class="small-10 large-10 columns">
                                            <span class="prefix">Supprimer les variables. </span>
                                        </div>
                                        <div class="small-2  large-2 columns">
                                            <a href="#" id="delete_trackingVAR" class="button postfix alert">valider</a>
                                        </div>
                                    </div>
                                    <div class="row collapse">
                                        <div class="small-10 large-10 columns">
                                            <span class="prefix">Supprimer la redirection et les variables. </span>
                                        </div>
                                        <div class="small-2  large-2 columns">
                                            <a href="#" id="delete_trackings" class="button postfix alert">valider</a>
                                        </div>
                                    </div>
                                    <div class="row collapse">
                                        <div class="small-10 large-10 columns">
                                            <span class="prefix">Supprimer les href. </span>
                                        </div>
                                        <div class="small-2  large-2 columns">
                                            <a href="#" id="deleteHref" class="button postfix alert">valider</a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                    </article>
                </fieldset>

            </fieldset>
            <button id="copy" class="right button small expand">Copier</button>
            <render></render>
        </section>

    </div>
</div>

<?php require_once '../../components/footer.html'; ?>
<?php require_once '../../components/footer-metas.html'; ?>
<script type="text/javascript" src="/modules/highlight/highlight.pack.js"></script>
<script type="text/javascript">var app = "linkor";</script>
<script type="text/javascript" src="/js/RMUtils.js"></script>
<script type="text/javascript" src="../../js/LinkorJS.js"></script>
<script type="text/javascript" src="/js/url.min.js"></script>
<script type="text/javascript" src="linkor.js"></script>
<script type="text/javascript" src="/js/display.js"></script>

</body>
</html>
