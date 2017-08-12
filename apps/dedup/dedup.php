<?
require_once __DIR__ . '/../../config/define.php';
require_once __DIR__ . '/../../config/whoops.php';
require_once __DIR__ . '/../../libs/dbMan.php';
require_once __DIR__ . '/../../libs/Dedup.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>R'M Tools | DedupApp</title>
	<link href="dedup.css" rel="stylesheet" media="screen"/>
	<?php require_once '../../components/header-metas.html'; ?>
</head>
<body>

<?php include '../../components/header.html'; ?>

<div class="row">
<aside class="large-3 columns">
	<h5>Bases</h5>
	<article class="info">
		<p>
			Cliquez sur une base, pour voir les tables créées.<br/>
			Cliquez sur une table pour auto-complete les champs d'informations ! <br/><br/>
			'+' : La conversion a déjà été réalisé. <br/>
			'++' : La conversion et la comparaison.
		</p>
	</article>
	<dl class="accordion" data-accordion>
		<?php $bases = Dedup::getDedupTables(); ?>
		<?php $i = 1; ?>
		<?php foreach ($bases as $base => $tables): ?>
			<dd>
				<a class="completeBase" href="<?php echo "#panel$i" ?>"><?php echo $base ?></a>

				<div id="<?php echo "panel$i" ?>" class="content">
					<?php foreach ($tables as $table => $id): ?>
						<p>
							<a href="#" id="<?php echo $base . "_" . $table ?>"
							   class="autoComplete"><?php echo $table; ?></a>
							<span style="float:right"><?php echo $id; ?></span>
						</p>
						<hr/>
					<?php endforeach; ?>
				</div>
			</dd>
			<?php $i++; ?>
		<?php endforeach; ?>
	</dl>
	<hr/>
	<div class="panel">
		<h5>Annonce</h5>

		<br/>

		<p>DedupApp est encore en version beta.</p>

		<p>
			Besoin d'une nouvelle base ? <br/>
			Une question, <br/>
			une suggestion, <br/>
			un bug ? <br/>
		</p>
		&rarr; <a href="mailto:nicolas.jamet@rentabiliweb.com">Envoyer moi un mail !</a>
	</div>
</aside>
<!-- End Sidebar -->

<!-- Main Blog Content -->
<div class="large-9 columns" role="content">

	<section id="update" class="panel">
		<h5>Dernière mise à jour du 5 Juin 2014</h5>
		<ul>
			<li>DedupApp n'a jamais été aussi beau, rapide, et performant.</li>
			<li>Refonte de l'interface et du coeur de l'application.</li>
			<li>Ceci est une révolution.</li>
		</ul>
	</section>
	<fieldset>
		<legend>1 - Etapes</legend>
		<article class="info">
			<p>
				Conversion : Encodage de votre fichier en md5. <br/>
				Extraire md5 : Retélécharge le fichier md5 si la conversion est déjà faite. <br/>
				Comparaison : Décodage des md5.
			</p>
		</article>
		<div class="row">
			<div class="large-12 columns" id="step-one">
				<a class="button radius small secondary" type="convert">Conversion</a>
				<a class="button radius small secondary" type="extract">Extraire md5</a>
				<a class="button radius small secondary" type="compare">Comparaison</a>
				<a class="button radius small secondary" type="nows" disabled>Comparaison sans Ws</a>
			</div>
		</div>
	</fieldset>

	<fieldset id="autoCompleteBlock">
		<legend>1.1 - Auto-complete</legend>
		<article class="info">
			<p>Remplissez les informatations et les options de bases pour les tables basiques.</p>
		</article>
		<section>
			<article>
				<a class="completion-button" data-dedup-completion="allianz">Allianz</a>
				<a class="completion-button" data-dedup-completion="edf">EDF</a>
				<a class="completion-button" data-dedup-completion="fortuneo">Fortunéo</a>
			</article>
			<article>
				<a class="completion-button" data-dedup-completion="hellobank">HelloBank</a>
				<a class="completion-button" data-dedup-completion="meetic">Meetic</a>
				<a class="completion-button" data-dedup-completion="savoirmaigrir">Savoir Maigrir</a>
			</article>
			<article>
				<a class="completion-button" data-dedup-completion="securitas">Sécuritas</a>
				<a class="completion-button" data-dedup-completion="sephora">Sephora</a>
			</article>
		</section>
	</fieldset>

	<form id="form" method="post">
		<fieldset id="informationsBlock">
			<legend>2 - Informations</legend>
			<article class="info">
				<p>Renseignez le nom de votre base, de la table ainsi qu'un email si vous souhaitez recevoir les liens
					directement dans votre boite.</p>
			</article>
			<div class="row">
				<div class="large-6 columns">
					<label for="base">Nom de la base</label>
					<select id="base" name="base">
						<option value></option>
						<option value="consoclient">Consoclient</option>
						<option value="onlinevoyance">Online Voyance</option>
						<option value="prpsycho">Pr Psycho</option>
						<option value="prformation">Pr Formation</option>
						<option value="r1e">R1E</option>
						<option value="skyrock">Skyrock</option>
						<option value="toox">Toox</option>
						<option value="tp">Top Privilège</option>
					</select>
				</div>
				<div class="large-6 columns">
					<label for="table">Nom de la table</label>
					<input type="text" id="table" name="table" placeholder="Ex: securitas">
				</div>
			</div>
			<div class="row">
				<div class="large-6 columns">
					<label for="receiver">Envoyer la dédup à l'adresse :</label>
					<select name="receiver" id="receiver">
						<option></option>
						<option value="yakare.diarra@rentabiliweb.com">Yakaré Diarra</option>
						<option value="veronique.marin@rentabiliweb.com">Veronique Marin</option>
						<option value="germain.degrais@rentabiliweb.com">Germain Degrais</option>
						<option value="michael.hanen@rentabiliweb.com">Michael Hanen</option>
						<option value="michael.drouhin@rentabiliweb.com">Michael Drouhin</option>
						<option value="nicolas.jamet@rentabiliweb.com">Nicolas Jamet</option>
					</select>
				</div>
			</div>
		</fieldset>

		<fieldset id="optionsBlock" class="large-7 columns">
			<legend>2.1 - Options</legend>
			<article class="info">
				<p>Paramétrez les différentes informations en suivant les indications de la documentation de dedup si
					vous n'avez pas l'auto-complete pour cette table en étape 1.1.</p>
			</article>
			<label for="uol">La chaine avant de passer en md5 doit être :</label>
			<select id="uol" name="upperOrLower">
				<option value="default"></option>
				<option value="uppercase">majuscule</option>
				<option value="lowercase">minuscule</option>
			</select>
			<br/>
			<label for="delimiter">Caractère séparant les champs dans le CSV.</label>
			<input style="float:left;margin-right:5px;" type="text" name="delimiter" id="delimiter" value=";"/>
			<br/>
		</fieldset>


		<fieldset id="concateBlock" class="large-5 columns">
			<legend>2.2 - Concaténation</legend>
			<article class="info">
				<p>Des champs à concaténer avant la transformation en md5 ? Cocher et renseigner le nom des champs du
					csv.</p>
			</article>
			<input style="float:left;margin-right:5px;" type="checkbox"
				   name="concate" id="concate"/>
			<label for="concate">Activer la concaténation ?</label>
			<br/><br/>

			<div id="concatBlock">
				<label for="concat_field1">champs 1</label>
				<input type="text" id="concat_field1" name="concat_field1">
				<label for="concat_field2">champs 2</label>
				<input type="text" id="concat_field2" name="concat_field2">
				<input style="float:left;margin-right:5px;" type="checkbox" name="field2IsCP" id="field2IsCP"/>
				<label for="field2IsCP">Le champ 2 est un code postal ?</label>
				<br/><br/>
			</div>
		</fieldset>

		<fieldset id="keyBlock" class="large-12 columns">
			<article class="info">
				<p>
					Insérez la clé de déduplication fournie dans la documentation. <br/>
					Si il n'y en a pas ... N'en mettez pas. :)
				</p>
			</article>
			<legend>3 - Clé de dédup</legend>
			<label for="key">Clé si existante :</label>
			<input type="text" id="key" name="key" value=""/>
		</fieldset>

		<fieldset id="outputBlock">
			<legend>4 - Output</legend>
			<article class="info">
				<p>Cochez tout simplement si le hash md5 doit être en majuscule.</p>
			</article>
			<input style="float:left;margin-right:5px;" type="checkbox" name="isUpper" id="isUpper"/>
			<label for="isUpper">Hash md5 en majuscule.</label>
		</fieldset>

		<fieldset class="large-12 columns" id="results">
			<legend>Résultats</legend>
			<article class="info">
				<p>
					Une fois les paramètres renseignés, choisissez le fichier à traiter ci-dessous.<br/>
					Cliquez ensuite sur le gros bouton bleu. <br/>
					Magic.
				</p>
			</article>
			<div class="progress">
				<span class="meter" style="width:0"></span>
			</div>
			<div id="answer">

			</div>
		</fieldset>

		<fieldset class="large-12 columns" id="upload">
			<legend>Upload et traitement</legend>
			<div class="row">
				<div class="xxlarge-12 columns">
					<label for="fichier_ref">Insérez le fichier de ref</label>
					<input type="file" name="file" id="file">
				</div>
			</div>
			<div class="row" id="partnerFileBlock">
				<div class="xxlarge-12 columns">
					<label for="partner_file">Insérez le fichier du partenaire.</label>
					<input type="file" name="partner_file" id="partner_file"/>
				</div>
			</div>
			<div class="row">
				<div class="large-12 columns">
					<input type="submit" id="sendForm" name="comparaison" value="Comparer" class="button large-12">
				</div>
			</div>
		</fieldset>
	</form>
</div>
</div>

<?php require_once '../../components/footer.html'; ?>
<?php require_once '../../components/footer-metas.html'; ?>
<script type="text/javascript"> var app = "dedup"; </script>
<script src="dedup.js"></script>
<script src="/js/display.js"></script>

</body>
</html>