//Javascript pour faire fonctionner la shoutbox
//Script javascript réalisé par Etienne Glossi
// Contact: etienne.glossi@iut-valence.fr

/* Variable globale qui stocke le nombre de message */
var nbMessage = 0;
/* -------------------------------------------------------------- */


//Fonction pour créer un objet XMLHTTPRequest (base de la technologie Ajax)
function createXHR()
{
	//Firefox ou IE >= 7.0
	if (window.XMLHttpRequest) {
		var xhr = new XMLHttpRequest();		//on crée l'objet qui sert à appeler le fichier xml
		if (xhr.overrideMimeType) { //blocage Safari
			xhr.overrideMimeType("text/xml");
		}
	}
	// IE < 7.0
	else if (window.ActiveXObject) {
		try {
			var xhr = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (error) {
			var xhr = new ActiveXObject("Microsoft.XMLHTTP");
		}	
	}
	
	else {
		var xhr = false;
	}

	return xhr;
}

// Insertion du message dans la base de donnee
function shoot(form)
{	
	var pseudo = form.elements['pseudo'].value.toLowerCase();
	var message = form.elements['message'].value;
	var sexe = "inconnu";
	for(var i=0; i<form.elements['sexe'].length;i++){
		if(form.elements['sexe'][i].checked){
			sexe = form.elements['sexe'][i].value;
			break;
		}
	}
	
	if(pseudo == "" || pseudo == "admin" || pseudo == "inconnu" || pseudo == "inconnue" || pseudo == "moi"){
		alert("Entrez votre pseudo");
		document.getElementById('pseudo').value = "";
		return;
	}
	
	if(message.length >= 3) //Si le message est trop court, on ne le traite pas
	{
		xhr = createXHR();	
		xhr.open("GET", "shoutbox/shoutbox.php?action=shoot&pseudo=" + pseudo + "&sexe=" + sexe + "&data=" + message, true); //mode asynchrone
		xhr.send(null);
		
		//on reinitialise les champs
		document.getElementById('message').value = "";
		updateShoutbox(0);
	}
	else {
		alert("Message trop court !");
	}
}


//Mise a jour de la liste des messages de la shoutbox
function updateShoutbox(updateTime){

	xhr = createXHR();
	
	xhr.onreadystatechange = function() //des que le protocole change d'état
    { 
         if(xhr.readyState == 4) //quand le protocole est pret
         {
            if(xhr.status == 200) { //nouveaux messages
				var data = xhr.responseXML.lastChild;
				nbMessage = parseInt(data.getAttribute("nb")); //mise a jour du nombre de message

				var messageLst = data.childNodes;		
				for(var e=0; e<messageLst.length; e++){
					if(messageLst.item(e).nodeType == 3) continue; // Firefox rend les elements entre chaque noeud --  3 = TEXT_NODE
					//if(navigator.appName == "Microsoft Internet Explorer")
						document.getElementById("listMessages").innerHTML += formatMessage(messageLst.item(e));
					//else
					//	document.getElementById("listMessages").appendChild(formatMessage(messageLst.item(e)));
				}
				
				scroll(); //on deplace la scroolbar pour afficher le nouveau message
				
				delete xhr;
				delete messageLst;
				
				if(updateTime!=0) window.setTimeout("updateShoutbox(" + updateTime + ")", (updateTime * 1000));
			}
			else if(xhr.status == 304) { //Aucun nouveaux message
				delete xhr;
				if(updateTime!=0) window.setTimeout("updateShoutbox(" + updateTime + ")", (updateTime * 1000)); //on met a jour que lorsque qu'une réponse a été reçue.
			}
			else { //problème ?
				document.getElementById('listMessages').innerHTML = "<p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><span class='error'><center><u>Erreur à la récéption des données</u><br /><a href='javascript:updateShoutbox(0)'>Rafraîchir<a></center></span>";
				//alert("Error code " + xhr.status);
				delete xhr;
				if(updateTime!=0) window.setTimeout("updateShoutbox(" + updateTime + ")", (updateTime * 1000));
			}
		 }
    };
	
	xhr.open("GET", "shoutbox/shoutbox.php?action=update&nbMessage=" + nbMessage + "&rand=" + parseInt(Math.random() * 1000), true);
	xhr.send(null);
}


//Fonction pour hierarchisé l'affichage des messages sous la forme:
//<div><span class="homme/feme/inconnu" >pseudo</span><span class="date">date</span><span class="message">message</span></div>
function formatMessage(node){
	//on commence par recuperer toutes les infos
	var pseudo = node.nodeName + ":";
	var sexe = node.getAttribute("sexe");
	var date = node.getAttribute("date");
	var message = node.firstChild.data;
	
	/*if(navigator.appName == "Microsoft Internet Explorer"){
		var conteneur = "<div><span class='"+sexe+"'>"+pseudo+"</span><span class='date'>"+date+"</span><span class='message'>"+message+"</span></div>";
	}
	else {
		//on cree la hierarchie
		var conteneur = document.createElement("DIV"); //div principal
		
		// <span class="homme/feme/inconnu">pseudo</span>
		var span = document.createElement("SPAN");
		span.setAttribute("class", sexe);
		span.setAttribute("name", "pseudonyme");
		span.appendChild(document.createTextNode(pseudo));
		conteneur.appendChild(span);
		delete span
		
		// <span class="date">date</span>
		var span = document.createElement("SPAN");
		span.setAttribute("class", "date");
		span.appendChild(document.createTextNode(date));
		conteneur.appendChild(span);
		delete span
		
		// <span class="message">message</span>
		var span = document.createElement("SPAN");
		span.setAttribute("class", "message");
		span.appendChild(document.createTextNode(message));
		conteneur.appendChild(span);
		delete span
	}*/
	
	return "<div><span class='"+sexe+"'>"+pseudo+"</span><span class='date'>"+date+"</span><span class='message'>"+message+"</span></div>";
}

//Pour faire défiler automatiquement les messages
function scroll(){
	document.getElementById('listMessages').scrollTop = 9999;
	if(navigator.appName == "Microsoft Internet Explorer") document.getElementById('listMessages').scrollTop = 9999; //Vive IE !
}

//Permet de verifier que l'utiliosateur peut utiliser la shoutbox en ayant active javascript
function canUseAjax(){
	if(createXHR()){ //javascript actif
		return true;
	}
	else {
		//alert("Vous devez activer javascript pour utiliser la shoutbox !");
		document.getElementById("shoutbox").style.display = 'none';
		//document.getElementById("shoutbox").style.visibility = 'hidden';
		return false;
	}
}

//Do nothing
function doNothing(){
	return;
}