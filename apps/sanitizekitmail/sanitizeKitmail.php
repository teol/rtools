<?
require_once __DIR__ . '/../../config/define.php';
require_once __DIR__ . '/../../config/whoops.php';
require_once __DIR__ . '/../../libs/dbMan.php';
require_once __DIR__ . '/../../libs/Dedup.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>R'M Tools | Sanitize Kitmail</title>
	<link rel="stylesheet" href="sanitizeKitmail.css"/>
    <?php require_once '../../components/header-metas.html'; ?>
	<link rel="stylesheet" href="/modules/highlight/styles/monokai_sublime.css"/>
<body>

<?php include '../../components/header.html'; ?>

<!-- Main Page Content and Sidebar -->
<div class="row">
<!-- Sidebar -->
<aside class="large-3 columns">
	<div class="panel">
		<h5>Annonce</h5>
		<br />

		(╯°□°）╯︵ ┻━┻)
		<br />
		<br />
		<p>&uarr; Meilleure annonce de tous les temps.</p>
	</div>
</aside>
<!-- End Sidebar -->

<!-- Main Blog Content -->
<div class="large-9 columns" role="content">

	<section id="update" class="panel">
		<h5>Dernière mise à jour du 21 Janvier 2015</h5>
		<h6>Bugfixes</h6>
		<ul>
			<li>Fix dans la récupération des variables dans le kit.</li>
		</ul>
	</section>
	<fieldset id="sanitizeBlock">
		<legend>Kitmail à sanitizer</legend>
		<article class="info">
			<p>Mettre le kit ici ! Il faut le coller là, là !</p>
		</article>

		<textarea id="toSanitize" cols="30" rows="10" autofocus="autofocus"></textarea>
		<button id="resetToSanitize" class="right button tiny">Effacer</button>

	</fieldset>
	<fieldset id="variablesBlock">
		<legend>Variables présentes dans le kit</legend>
		<article class="info">
			<p>Vous pouvez retrouver ici les variables présentes dans le kitmail.</p>
		</article>
	</fieldset>
	<fieldset id="sanitized">

		<button id="deleteHref" class="button tiny">Delete aHref</button>
		<button id="copy-minified" class="button tiny"><span class="button-txt">Copier version minifiée</span></button>

		<div class="row">
			<div class="small-5 columns">
				<label for="renameAlt" class="right inline">Renomer toutes les balises `alt` du kit :</label>
			</div>
			<div class="small-7 columns">
				<input type="text" id="renameAlt" name="alt" placeholder="alt value">
			</div>
		</div>


		<legend>Sanitized Kitmail</legend>
		<article class="info">
			<p>Le kit réencodé est ici !</p>
		</article>

        <div class="row">
            <div class="large-12 columns">
				<code id="minified"></code>
            </div>
        </div>

        <h5>Version Sanitizée.</h5>
        <div class="row">
			<div class="large-12 columns">
				<button id="copy-nominified" class="button postfix tiny"><span class="button-txt">Copy no-minified</span></button>
				<code id="nominified"></code>
            </div>
		</div>

    </fieldset>
</div>
</div>

<?php require_once '../../components/footer.html'; ?>
<?php require_once '../../components/footer-metas.html'; ?>
<script type="text/javascript" src="/modules/highlight/highlight.pack.js"></script>
<script type="text/javascript">var app = "sanitizekit";</script>
<script type="text/javascript" src="/js/RMUtils.js"></script>
<script type="text/javascript" src="../../js/SanitizerJS.js"></script>
<script type="text/javascript" src="sanitizeKitmail.js"></script>
<script type="text/javascript" src="/js/display.js"></script>

</body>
</html>