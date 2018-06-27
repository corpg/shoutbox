<!-- Page HTML realisée par Etienne-->
<!--  etienne.glossi@iut-valence.fr -->

<!-- Importation des modules PHP -->
<?php 
include("script/phpFunc.php");
include(languageSite()."/langue.php");
?>

<!-- Page XTML begin here :) -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr">
<head>
	<title><?php echo "$nomSite";?></title>
	<meta http-equiv="Content-Type" content="text/xhtml; charset=iso-8859-1" />
	<link rel="shortcut icon" href="faviconSite.ico" />
	
	<!-- on importe le style CSS du site -->
	<meta http-equiv="content-style-type" content="text/css" />
	<link title="style" type="text/css" rel="stylesheet" href="style.css" />
	<?php
	if (preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) //si le navigateur est Internet Explorer
		echo "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"shoutbox/style_IE.css\" />";
	else
		echo "<link title=\"style\" type=\"text/css\" rel=\"stylesheet\" href=\"shoutbox/style.css\" />";
	?>
	
	<!-- on importe les script javascript -->
	<script type="text/javascript" src="<?php echo $langue['location'].'/langue.js';?>"></script>
	<!--<script type="text/javascript" src="script/jquery-1.2.3.js"></script>-->
	<script type="text/javascript" src="script/javascript.js"></script>
	<script type="text/javascript" src="shoutbox/shoutbox.js"></script>
</head>



<body>
	<!-- En tete de la page -->
	<div id="header">
					<!-- le style bug si présent dans le fichier .css-->
		<?php include('div/header.php');?>
	</div>
	
	<!-- menu -->
	<div id="menu">
		<?php include('div/menu.php');?>
		<p>&nbsp;</p>
	</div>
	
	
	<!-- contenu de la page -->
	<div id="content" style="text-align: center;">
		<div class="shoutbox" id="shoutbox" style="margin: auto;">
		
			<!-- Affichage des messages -->
			<div id="listMessages">
			</div>
					
			<!-- Formulaire d'ajout de messages -->
			<div id="addMessage">
			<form id="form_shoot" onsubmit="shoot(this)" action="javascript:doNothing();">
				<div id="input_pseudo">Pseudo: <input type="text" value="" id="pseudo" maxlength="11" title="Entrez votre pseudonyme"/></div>
				<!-- E-mail (facultatif): <input type="text" value="" id="email" /> -->
				<div id="input_message">Message: <input type="text" value="" id="message" name="<?php echo rand();?>" title="Entrez votre message" /></div> <!-- l'astuce qui tue XD -->
				<div id="boutons"><input type="reset" value="Rafraîchir" class="boutton" /><input type="submit" value="Shoot !" class="boutton" /></div>
				<div id="input_sexe"><input type="radio" class="radio" name="sexe" value="femme" />F<input class="radio" name="sexe" type="radio" value="homme" />H</div>
			</form>
			</div>
			<script type="text/javascript">
			if(canUseAjax()){
				updateShoutbox(10);
			}
			</script>
			
		</div>
		<!-- fin de la shoutbox -->
	</div>
		
	<!-- Pied de page -->
	<div id="bottom">
		<p>&nbsp;</p>
		<?php echo $copyright;?><p />

	<!--<a href="http://jigsaw.w3.org/css-validator/"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss-blue" alt="CSS Valide !" /></a>-->
    
	</div>

	
</body>
</html>