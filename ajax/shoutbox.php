<?php

// Cette page est apelle uniquement par l'objet javaxcript XMLHTTPRequest
// Author: Etienne Glossi
// Contact: etienne.glossi@iut-valence.fr
// Site web: www.raren.eu

/* Variables Globales pour la connection � la base de donnees */
$host = "localhost"; /* adresse du serveur MySQL*/
$user = "root"; /*Nom d'utilisateur pour se connecter*/
$password = "root"; /*et son mot de passe */
$database = "test_shoutbox"; /*nom de la base de donnee*/
/* ------------------------------------------------------------------------- */

/* Debug ? */
$debug = 0;
/* ----------- */

/* Variable Globale pour indiquer le statut de la connexion � la base de donn�e - NE PAS MODIFIER */
$status = array("Connexion au serveur de la base de donn�e refus�e", "Echec � la Connexion au serveur de la base de donn�e", "Base de donn�es introuvable !", "Connexion � la base de donn�e r�ussie", "Interrogation de la base de donn�e impossible. Requ�te SQL invalide: ");
/* ---------------------------------------------------------------------------------------------------------------------------*/

/* Eviter la mise en cache du fichier */
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("last-modified: ".date(DATE_RFC822));
/* --------------------------------------------*/

/* Autres en-t�tes */
header('Content-type: text/xml');
header('Content-Disposition: attachment; filename="messageShoutBox-'.date("dmY\-His").'.xml"');
/* ------------------- */


// Classe pour stocker les information d'�xecution du script dans un fichier texte
class log{
	var $file; //variable de classe ?
	function log($name){
		$this->file = fopen($name, "a"); //ouverture du fichier
	}
	function close(){
		fclose($this->file); //fermeture du fichier et enregistre les modifications
	}
	function ecrire($texte){
		$texte = date("j/n/Y @ G:i:s")."  > ".$texte."\n";
		fwrite($this->file, $texte); //�criture dans le fichier
	}
}

// Classe pour gerer les dates des messages
class MyDate{
	var $date;
	function MyDate($date){
		$this->date = $date;
	}
	function str(){
		$today = date("d/m");

		$date = explode(" ", $this->date);
		$heure = explode(":", $date[1]);
		$date = explode("-", $date[0]);

		if($today == $date[2]."/".$date[1]){
			$date = $heure[0].":".$heure[1];
		}
		else {
			$date = $date[2]."/".$date[1]." ".$heure[0].":".$heure[1];
		}

		return $date;
	}
}

//fonction qui permet la connecion � la base de donn�e et � l'�xecution d'une requ�te SQL.
function requestToDatabase($SQL)
{
	global $user, $password, $status, $database, $host;	//on importe les variables dont cette du status pour permet de retourner un message d'erreur

	if ($id = mysql_connect($host, $user, $password))	//on peut ensuite se connecter au serveur de la base de donn�e
	{
		if (mysql_select_db($database))	//on selection la base de donn�e
		{
			$state = $status[3];	//ici le status nous indique que la connexion est r�ussie
			$result = mysql_query($SQL);	//on interroge la base de donn�e � l'aide de la requ�te SQL et on met le r�sultat dans la variable $result

			if ($result == false) return $status[4].$SQL;	//si la requ�te SQL ne fonctionne pas, $result renvoie faux et dans ce cas on retourne un mesage d'erreur en pr�cisant que la requ�te SQL est invalide
			else return $result;	//on retourne le r�sultat pour pouvoir l'utiliser
		}
		else $state = $status[2];	//la base de donn�e est introuvable
	}

	else $state = $status[1];	//la connexion au serveur de la base de donn�e est impossible

	return $state; //on retourne le message d'erreur
}

function newMessage($pseudo, $message, $sexe="inconnu"){ //ajout d'un message dans la base de donnee
	if($debug) global $log;

	$pseudo = htmlentities($pseudo);
	$message = htmlentities($message);
	$ip = getenv("REMOTE_ADDR");

	$param = "INSERT INTO `shoutbox` (`pseudonyme`, `message`, `ip`, `sexe`) VALUES ('".$pseudo."', '".$message."', '".$ip."', '".$sexe."');";
	if($debug){$log->ecrire("Action 'shoot'. Execution de requestToDatabase avec parametre: $param");}

	$query = requestToDatabase($param); //insertion du message dans la base de donn�e
	if($debug){$log->ecrire("Re�u: $query");}
}

//fonction pour creer un fichier XML qui contiendra les messages du chat !
function afficheMessage(){
	if($debug) global $log;

	$param = "SELECT * FROM `shoutbox` ORDER BY `id` ASC;";
	if($debug){$log->ecrire("Action 'update'. Execution de requestToDatabase avec parametre: $param");}

	$query = requestToDatabase($param); //recuperation des messages de la base de donn�e
	if($debug){$log->ecrire("Re�u: $query");}

	$skip = 0; //on initialise le nombre de message � sauter

	if(isset($_GET['nbMessage'])){
		$skip = ($_GET['nbMessage']);
		if ($skip == mysql_num_rows($query)){ //pas de nouveau message
			header("HTTP/1.1 304 No New Messages");
			return;
		}
	}

	//Nouveau(x) messages() on construit le fichier .xml en incluant uniquement ces nouveaux messages
	echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\" ?>\n<messageShoutBox nb='".mysql_num_rows($query)."'>\n";

	while($row = mysql_fetch_array($query)){
		if($skip != 0) {
			$skip--;
			continue;
		}
		$pseudo = $row['pseudonyme'];
		$dateMessage = new MyDate($row['dateMessage']);
		$message = $row['message'];
		$sexe = $row['sexe'];

		echo "\t<$pseudo sexe='$sexe' date='".$dateMessage->str()."'><![CDATA[$message]]></$pseudo>\n";
	}

	echo "</messageShoutBox>";
}


/*******************************************      MAIN      *******************************************/
if(!isset($_GET['action'])) $action="update";
else $action=$_GET['action'];

if($debug){$log = new log("log/shoutbox.log");} //creation du fichier log

switch($action){
	case "shoot":
		{
			if(!isset($_GET['data']) and !isset($_GET['pseudo'])){
				exit;
			}

			if(isset($_GET['sexe'])) newMessage($_GET['pseudo'], $_GET['data'], $_GET['sexe']);
			else newMessage($_GET['pseudo'], $_GET['data']);

			break;
		}
	case "update":
		{
			afficheMessage();
			break;
		}
}
if($debug){$log->close();} //fermeture du log
?>
