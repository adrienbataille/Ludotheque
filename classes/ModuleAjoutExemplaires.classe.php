<?php
/**
* Classe du module "AjoutExemplaires"
* Le module AjoutVersions permet la gestion des verions, c'est à dire :
*   - La création des occurences des Versions
*/

// Inclusions
require_once("classes/Module.classe.php");

//Constantes
define("MODULE_AJOUT_EXEMPLAIRES", RACINE_SITE . "module.php?idModule=AjoutExemplaires");

/**
 * Module Ajout Exemplaire
 * @author Adrien Bataille
 * @version 0.1
 * @package module
 */



/* REMARQUES :
Je pense que Date fin de vie n'esst pas un champs possible à l'insertion dès la premiere insertion, mais seulement à la modification
*/



class ModuleAjoutExemplaires extends Module
{

	// Attributs
	private $idJeu = 0;
	private $idVersion = 0;
	private $idExemplaire = 0;
	private $nomJeu = "";
	private $nomVersion = "";
	private $codeBarre = "";
	private $descriptionExemplaire = "";
	private $prixMDJT;
	private $dateAchat;
	private $dateFinVie;
	private $etatExemplaire = 0;
	private $lieuReel;
	private $lieuTempo = null;
	private $listeLangueRegle = Array();
	// On stocke les erreurs qui pourront arriver
	private $traitementFormulaire = false;
	private $erreurPrixMdjt = false;
	private $erreurEtat = false;
	private $erreurLieuReel = false;
	private $erreurExemplaire = false;
	private $erreurLoadExemplaire = false;
	private $erreurCodeBarre = false;
    
	// Methodes

    /**
    * Le constructeur du module Mon Profil
    */
    public function __construct($jeu, $version, $exemplaire)
    {
        // On utilise le constructeur de la classe mère
		parent::__construct();		
		
		// On a besoin d'un accès à la base - On utilise la fonction statique prévue
		$this->maBase = AccesAuxDonneesDev::recupAccesDonneesDev();
		
		// On traite le formulaire, le cas échéant
		$this->traiteFormulaire();
		
		// $jeu
		if(intval($version))
		{
			$maVersion = $this->maBase->recupVersion($version);
			if($maVersion == null || $maVersion[0] == null || $maVersion == false)
				$this->erreurLoadExemplaire = true;
			else
			{
				$this->idVersion = $maVersion[0][ID_VERSION];
				$this->idJeu = $maVersion[0][ID_JEU];
				
				$nomJeu = $this->maBase->recupNomJeu($this->idJeu);
				$this->nomJeu = $nomJeu[$this->idJeu][0][NOM_JEU];
				
				$nomVersion = $this->maBase->recupNameVersion($this->idVersion);
				$this->nomVersion = $nomVersion[0][NOM_VERSION];
			}
		}
			
			
		if(intval($exemplaire))
		{
			$monExemplaire = $this->maBase->recupExemplaire($exemplaire);
				
			if($monExemplaire == null || $monExemplaire == false)
				$this->erreurLoadExemplaire = true;
			else
			{
				$this->idExemplaire = $monExemplaire[0][ID_EXEMPLAIRE];
				$this->prixMDJT = $monExemplaire[0][PRIX_MDJT];
				$this->dateAchat = $monExemplaire[0][DATE_ACHAT];
				$this->dateFinVie = $monExemplaire[0][DATE_FIN_VIE];
				$this->etatExemplaire = $monExemplaire[0][ID_ETAT_EXEMPLAIRE];
				$this->lieuReel = $monExemplaire[0][ID_LIEU_REEL];
				$this->lieuTempo = $monExemplaire[0][ID_LIEU_TEMPO];
				
				$maVersion = $this->maBase->recupVersion($monExemplaire[0][ID_VERSION]);
				$this->idVersion = $maVersion[0][ID_VERSION];
				
				$this->descriptionExemplaire = $monExemplaire[0][DESCRIPTION_EXEMPLAIRE];
				
				//$monJeu = $this->maBase->recupJeu($maVersion[0][ID_JEU]);
				//$this->idJeu = $monJeu[0][ID_JEU];
				$this->idJeu = $maVersion[0][ID_JEU];
				
				$mesLanguesRegles = $this->maBase->recupReglesLangues($this->idExemplaire);
				foreach($mesLanguesRegles as $langueRegle)
					$this->listeLangueRegle[] = $langueRegle[ID_LANGUE];
			}
		}
		
		
		// On affiche le contenu du module
    	if($this->erreurLoadExemplaire)
    		$this->ajouteLigne("<p class='erreurForm'>" . $this->convertiTexte("Attention, tentative de piratage !!") . "</p>");
    	else
		{
			// On affiche le formulaire d'ajout des informations propres à un jeux
			$this->afficheFormulaire();	
		}	
    }
	
	
	 /**
	 * Fonction de nettoyage des chaine de caractères
     * Prends en paramètre la chaine à filtrer, et la taille max de cette chaine
	 */
	private function filtreChaine($uneChaine,$uneTaille)
	{
		// On supprime les \ rajouté par les Magic Quotes. Si il y a lieu
		if (get_magic_quotes_gpc() == 1)
		{
			$uneChaine = stripslashes($uneChaine);
		}
		// On supprime les balises HTML s'il y en avait
		$resultat = strip_tags($uneChaine);
		// On vérifie sa taille
		$resultat = substr($resultat,0,$uneTaille);
		return $resultat;
	}
    
	
		/**
	* Fonction de conversion des dates
	* Converti les dates stockée en date affichable
	* AAAA-MM-JJ -> jj/mm/aaaa
	*/
	private function dateBaseToAffichage($uneDate)
	{
		$annee = substr($uneDate,0,4);
		$mois = substr($uneDate,5,2);
		$jour = substr($uneDate,8,2);
		$date = $jour . "/" . $mois . "/" . $annee;
		
		if($uneDate == null)
			return "";
		else
			return $date;
	}
	
	/**
	* Fonction de conversion des dates
	* Converti les dates affichée en date stockable en base
	* jj/mm/aaaa -> AAAA-MM-JJ
	*/
	private function dateAffichageToBase($uneDate)
	{
		$jour = substr($uneDate,0,2);
		$mois = substr($uneDate,3,2);
		$annee = substr($uneDate,6,4);
		$date = $annee . "-" . $mois . "-" . $jour;
		
		if($uneDate == null)
			return "";
		else
			return $date;
	}
	
	/**
	* Fonction de vérification d'une date au format d'affichage
	*/
	private function verifDateAffichee($uneDate)
	{
		if (preg_match('#^([0-9]{2})([/-])([0-9]{2})\2([0-9]{4})$#', $uneDate))
		{
			return checkdate(substr($uneDate,3,2), substr($uneDate,0,2), substr($uneDate,6,4));
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Fonction récupérant les informations des jeux dans la requête POST
	 */
	private function recuperationInformationsFormulaire()
	{
		// Nettoyage du nom du jeu
		$this->nomJeu = $this->filtreChaine($_POST[NOM_JEU], TAILLE_CHAMPS_COURT);
		// Nettoyage du l'id du jeu
		$this->idJeu = $this->filtreChaine($_POST[ID_JEU], TAILLE_CHAMPS_COURT);
		
		// Nettoyage du nom de la version du jeu
		$this->nomVersion = $this->filtreChaine($_POST[NOM_VERSION], TAILLE_CHAMPS_COURT);
		// Nettoyage de l'id de la version du jeu
		$this->idVersion = $this->filtreChaine($_POST[ID_VERSION], TAILLE_CHAMPS_COURT);
		
		// Nettoyage de l'id de la version du jeu
		$this->idExemplaire = $this->filtreChaine($_POST[ID_EXEMPLAIRE], TAILLE_CHAMPS_COURT);
		
		// Nettoyage du code barre de l'exemplaire du jeu
		$this->codeBarre = $this->filtreChaine($_POST[CODE_BARRE], TAILLE_CHAMPS_COURT);

		// Nettoyage de la Description
		$this->descriptionExemplaire = $this->filtreChaine($_POST[DESCRIPTION_EXEMPLAIRE], TAILLE_CHAMPS_LONG);
		
		// Nettoyage du Prix MDJT
		$this->prixMDJT = floatval($this->filtreChaine($_POST[PRIX_MDJT], TAILLE_CHAMPS_COURT));
	
		// Vérification de la date achat
		if ($this->verifDateAffichee($_POST[DATE_ACHAT]))
			$this->dateAchat = $this->dateAffichageToBase($_POST[DATE_ACHAT]);
			
		// Vérification de la date de fin de vie
		if ($this->verifDateAffichee($_POST[DATE_FIN_VIE]))
			$this->dateFinVie = $this->dateAffichageToBase($_POST[DATE_FIN_VIE]);
			
		// Nettoyage de l'état de l'exemplaire
		$this->etatExemplaire = $this->filtreChaine($_POST[ID_ETAT_EXEMPLAIRE], TAILLE_CHAMPS_LONG);
		
		// Nettoyage du lieu de stockage réel
		$this->lieuReel = $this->filtreChaine($_POST[ID_LIEU_REEL], TAILLE_CHAMPS_COURT);
		
		// Nettoyage du lieu de stockage temporaire
		$this->lieuTempo = $this->filtreChaine($_POST[ID_LIEU_TEMPO], TAILLE_CHAMPS_LONG);
		
		// Nettoyage de la liste des langues choisies pour les régles du jeu de l'exemplaire
		if($_POST[NOM_LANGUE] != null)
			foreach($_POST[NOM_LANGUE] as $langueRegle)
				$this->listeLangueRegle[] = $this->filtreChaine($langueRegle, TAILLE_CHAMPS_COURT);
	}
	
    public function afficheFormulaire()
    {//var_dump($this);
		if($this->erreurExemplaire)
			$this->ajouteLigne("<p class='erreurForm'>" . $this->convertiTexte("Une erreur est survenue lors de l'ajout de votre exemplaire, veuillez réessayer ou contacter l'administrateur") . "</p>");

		$this->ouvreBloc("<form method='post' action='" . MODULE_AJOUT_EXEMPLAIRES . "' id='formProfil'>");
		
		
		// Premier fieldset : Version de l'exemplaire
		$this->ouvreBloc("<fieldset>");	
		$this->ajouteLigne("<legend>" . $this->convertiTexte("Informations du jeu") . "</legend>");
		$this->ouvreBloc("<ol>");
		
		// Jeu de l'exemplaire
		$this->ouvreBloc("<li>");	
		if(($this->nomJeu == false || $this->nomJeu == null) && ($this->idJeu != 0 && $this->idJeu != null && $this->idJeu != false)) {
			$namePlay = $this->maBase->recupNomJeu($this->idjeu);
			$this->nomJeu = $namePlay[$this->idJeu][0][NOM_JEU];
		}
			
		$this->ajouteLigne("<label for='" . NOM_JEU . "'>" . $this->convertiTexte("Jeu associé *") . "</label>");
		if($this->idJeu == 0)
			$this->ouvreBloc("<input id='" . NOM_JEU . "' name='" . NOM_JEU . "' />");
		else
			$this->ouvreBloc("<input id='" . NOM_JEU . "' name='" . NOM_JEU . "' value='". $this->nomJeu . "' disabled='disabled' />");
		
		if($this->idJeu != 0)
			$this->ajouteLigne("<input type='hidden' id='" . ID_JEU . "' name='" . ID_JEU . "' value='" . $this->idJeu . "' />");
		
		$this->fermeBloc("</li>");
		
		// Si on choisit un jeu auparavant
		if($this->idJeu != 0)
		{
			// Version de l'exemplaire du jeu
			$listeVersion = $this->maBase->recupNomVersion($this->idJeu);
			
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . NOM_VERSION . "'>" . $this->convertiTexte("Version du jeu *") . "</label>");
			
			if($this->idVersion != 0)
				$this->ouvreBloc("<input id='" . NOM_VERSION . "' name='" . NOM_VERSION . "' value='" . $this->nomVersion . "' disabled='disabled' />");
			else
				$this->ouvreBloc("<input id='" . NOM_VERSION . "' name='" . NOM_VERSION . "' />");
		
			if($this->idVersion != 0)
				$this->ajouteLigne("<input type='hidden' name='" . ID_VERSION . "' value='" . $this->idVersion . "' />");

			$this->fermeBloc("</li>");
		}
		
		$this->fermeBloc("</ol>");
		
		$this->fermeBloc("</fieldset>");
		
		// On teste si on a choisit un jeu et un exemplaire ou non
		// Si on n'a pas encore choisit un jeu, on affiche le bouton pour valider le choix du nom d'un jeu
		if($this->idJeu == 0)
		{
			// Bouton pour valider le choix du jeu
			$this->ouvreBloc("<fieldset>");	
			$this->ajouteLigne("<input type='hidden' name='validerNomJeu' value='true' />");
			$this->ajouteLigne("<button type='submit' name='ValiderNomJeu' value='true'>" . $this->convertiTexte("Valider le nom du jeu") . "</button>");
			$this->fermeBloc("</fieldset>");
			
		}
		// Si on n'a pas encore choisit une version d'un jeu, on affiche le bouton pour valider le choix du nom d'une version
		elseif($this->idVersion == 0)
		{
			// Si le jeu n'a aucune version, on propose à l'utilisateur d'en ajouter une
			if($listeVersion == null)
			{
				$this->ouvreBloc("<ul id='menu_gestion_jeux'>");
		
				$this->ouvreBloc("<li>");
				$this->ajouteLigne("<a href='" . MODULE_AJOUT_VERSIONS . "&idJeu=" . $this->idJeu . "' title='" . $this->convertiTexte("Ajouter une version à ce jeu") . "'>" . $this->convertiTexte("Ajouter une version à ce jeu") . "</a>");
				$this->fermeBloc("</li>");
				
				$this->fermeBloc("</ul>");
			} else {
				// Bouton pour valide le choix de la version
				$this->ouvreBloc("<fieldset>");	
				$this->ajouteLigne("<input type='hidden' name='validerNomVersion' value='true' />");
				$this->ajouteLigne("<button type='submit' name='ValiderNomVersion' value='true'>" . $this->convertiTexte("Valider le nom de la version") . "</button>");
				$this->fermeBloc("</fieldset>");
			}
		}
		// Si on a choisit un nom de jeu et de version, on affiche un bouton pour modifier ce choix et le reste du formulaire
		else
		{
			// Bouton pour remettre à zéro le nom de jeu et de la version choisie
			$this->ouvreBloc("<fieldset>");	
			$this->ajouteLigne("<input type='hidden' name='modifierNom' value='true' />");
			$this->ajouteLigne("<button type='submit' name='ModifierNom' value='true'>" . $this->convertiTexte("Modifier le nom du jeu") . "</button>");
			$this->fermeBloc("</fieldset>");
			
			// Troisième fieldset : Informations sur l'exemplaire
			$this->ouvreBloc("<fieldset>");
			$this->ajouteLigne("<legend>" . $this->convertiTexte("Informations sur l'exemplaire") . "</legend>");
			$this->ouvreBloc("<ol>");
			
			// id de l'exemplaire
			$this->ouvreBloc("<li style='display:none;'>");
			$this->ajouteLigne("<input type='hidden' id='" . ID_EXEMPLAIRE ."' name='"  . ID_EXEMPLAIRE . "' value='" . $this->idExemplaire . "' />");
			$this->fermeBloc("</li>");
			
			// Code Barre
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . CODE_BARRE . "'>" . $this->convertiTexte("Code barre *") . "</label>");
			$this->ajouteLigne("<input type='text' id='" . CODE_BARRE . "'name='" . CODE_BARRE . "' value='" . $this->convertiTexte($this->codeBarre) . "' required='required' />");
			if($this->erreurCodeBarre)
				$this->ajouteLigne("<p class='erreurForm'>" . $this->convertiTexte(ERREUR_CHAMP_REQUIS) . "</p>");
			$this->fermeBloc("</li>");
			
			// Description
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . DESCRIPTION_EXEMPLAIRE . "'>" . $this->convertiTexte("Description") . "</label>");
			$this->ajouteLigne("<textarea rows='3' id='" . DESCRIPTION_EXEMPLAIRE . "'name='" . DESCRIPTION_EXEMPLAIRE . "'>" . $this->convertiTexte($this->descriptionExemplaire) . "</textarea>");
			$this->fermeBloc("</li>");
			
			 // Prix mdjt
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . PRIX_MDJT . "'>" . $this->convertiTexte("Prix actuel *") . "</label>");
			$this->ajouteLigne("<input type='text' id='" . PRIX_MDJT ."' name='"  . PRIX_MDJT . "' value='" . $this->convertiTexte($this->prixMDJT) . "' required='required' />");
			if($this->erreurPrixMdjt)
				$this->ajouteLigne("<p class='erreurForm'>" . $this->convertiTexte("Ce champ doit être remplit") . "</p>");
			$this->fermeBloc("</li>");
			
			// Data achat
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . DATE_ACHAT . "'>" . $this->convertiTexte("Date Achat") . "</label>");
			$this->ajouteLigne("<input type='text' id='" . DATE_ACHAT ."' maxlength='10' name='" . DATE_ACHAT . "' value='" . $this->convertiTexte($this->dateBaseToAffichage($this->dateAchat)) . "' />");
			$this->fermeBloc("</li>");
			
					
			// Data fin de vie
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . DATE_FIN_VIE . "'>" . $this->convertiTexte("Date fin de vie") . "</label>");
			$this->ajouteLigne("<input type='text' id='" . DATE_FIN_VIE . "' maxlength='10' name='" . DATE_FIN_VIE . "' value='" . $this->dateFinVie . "' />");
			$this->fermeBloc("</li>");
			
			$this->fermeBloc("</ol>");
			$this->fermeBloc("</fieldset>");
			
			
			// Troisième fieldset : État de l'exemplaire
			$this->ouvreBloc("<fieldset>");	
			$this->ajouteLigne("<legend>" . $this->convertiTexte("État de l'exemplaire") . "</legend>");
			$this->ouvreBloc("<ol>");
			
			// État de l'exemplaire
			$etatExemplaire = $this->maBase->recupEtatExemplaire();
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . ID_ETAT_EXEMPLAIRE . "'>" . $this->convertiTexte("État") . "</label>");
			$this->ouvreBloc("<select id='" . ID_ETAT_EXEMPLAIRE . "' name='" . ID_ETAT_EXEMPLAIRE . "' required='required'>");
			foreach($etatExemplaire as $etat)
				$this->ajouteLigne("<option name='" . ID_ETAT_EXEMPLAIRE . "' value='" . $etat[ID_ETAT_EXEMPLAIRE] . "'>" . $this->convertiTexte($etat[NOM_ETAT]) . "</option>");
			$this->fermeBloc("</select>");
			if($this->erreurEtat)
				$this->ajouteLigne("<p class='erreurForm'>" . $this->convertiTexte("Ce champ doit être remplit") . "</p>");
			$this->fermeBloc("</li>");
			
			$this->fermeBloc("</ol>");
			$this->fermeBloc("</fieldset>");
			
			
			// Quatrième fieldset : Emplacement du stockage
			$this->ouvreBloc("<fieldset>");	
			$this->ajouteLigne("<legend>" . $this->convertiTexte("Emplacement de l'exemplaire") . "</legend>");
			$this->ouvreBloc("<ol>");
			
			// Lieu normal de stockage
			$lieuExemplaire = $this->maBase->recupLieu();
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . ID_LIEU_REEL . "'>" . $this->convertiTexte("Lieu normal de stockage") . "</label>");
			$this->ouvreBloc("<select id='" . ID_LIEU_REEL . "' name='" . ID_LIEU_REEL . "' required='required'>");
			foreach($lieuExemplaire as $lieu)
				$this->ajouteLigne("<option name='" . ID_LIEU_REEL . "' value='" . $lieu[ID_LIEU] . "'>" . $this->convertiTexte($lieu[NOM_LIEU]) . "</option>");
			$this->fermeBloc("</select>");
			if($this->erreurLieuReel)
				$this->ajouteLigne("<p class='erreurForm'>" . $this->convertiTexte("Ce champ doit être remplit") . "</p>");
			$this->fermeBloc("</li>");
			
			// Lieu de stockage temporaire
			$lieuExemplaire = $this->maBase->recupLieu();
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . ID_LIEU_TEMPO . "'>" . $this->convertiTexte("Lieu de stockage temporaire") . "</label>");
			$this->ouvreBloc("<select id='" . ID_LIEU_TEMPO . "' name='" . ID_LIEU_TEMPO . "' required='required'>");
			$this->ajouteLigne("<option value='null'></option>");
			foreach($lieuExemplaire as $lieu)
				$this->ajouteLigne("<option name='" . ID_LIEU_TEMPO . "' value='" . $lieu[ID_LIEU] . "'>" . $this->convertiTexte($lieu[NOM_LIEU]) . "</option>");
			$this->fermeBloc("</select>");
			$this->fermeBloc("</li>");
			
			$this->fermeBloc("</ol>");
			$this->fermeBloc("</fieldset>");
			
			
			// Cinquième fieldset : Langues régles du jeu
			$this->ouvreBloc("<fieldset>");	
			$this->ajouteLigne("<legend>" . $this->convertiTexte("Règles du jeu") . "</legend>");
			$this->ouvreBloc("<ol>");
			
			// Langues des régles du jeu
			$langueRegle = $this->maBase->recupLangue();
			$this->ouvreBloc("<li>");
			$this->ajouteLigne("<label for='" . NOM_LANGUE . "'>" . $this->convertiTexte("Langues des régles du jeu") . "</label>");
			$this->ouvreBloc("<ol id='listeItem'>");
			foreach($langueRegle as $langue)
				if(in_array($langue[ID_LANGUE], $this->listeLangueRegle))
					$this->ajouteLigne("<li class='item'><input type='checkbox' name='" . NOM_LANGUE . "[]' value='" . $this->convertiTexte($langue[ID_LANGUE]) . "' checked='checked'>" . $this->convertiTexte($langue[NOM_LANGUE]) . "</option></li>");
				else
					$this->ajouteLigne("<li class='item'><input type='checkbox' name='" . NOM_LANGUE . "[]' value='" . $this->convertiTexte($langue[ID_LANGUE]) . "'>" . $this->convertiTexte($langue[NOM_LANGUE]) . "</option></li>");
			$this->fermeBloc("</ol>");
			$this->fermeBloc("</li>");
			
			$this->fermeBloc("</ol>");
			$this->fermeBloc("</fieldset>");
			
			$this->ouvreBloc("<fieldset>");	
			$this->ajouteLigne("<input type='hidden' name='ajouterExemplaire' value='true' />");
			$this->ajouteLigne("<button type='submit' name='AjouterExemplaire' value='true'>" . $this->convertiTexte("Valider l'ajout de l'exemple") . "</button>");
			$this->fermeBloc("</fieldset>");
			
			$this->ouvreBloc("<fieldset>");	
			$this->ajouteLigne("<input type='hidden' name='ajouterAutreExemplaire' value='true' />");
			$this->ajouteLigne("<button type='submit' name='AjouterAutreExemplaire' value='true'>" . $this->convertiTexte("Valider et ajouter un autre exemplaire") . "</button>");
			$this->fermeBloc("</fieldset>");
		}
		
		$this->fermeBloc("</form>");
    }
	
	/*
	* Traitement formulaire
	*/
	
	private function traiteFormulaire()
	{
		// Y a-t-il effectivement un formulaire à traiter ?
		if ($_POST["AjouterExemplaire"] || $_POST["AjouterAutreExemplaire"])
		{
			// Traitement du formulaire
			$this->traitementFormulaire = true;
			
			$this->recuperationInformationsFormulaire();
			
			if($this->prixMDJT == 0)
				$this->erreurPrixMdjt = true;
				
			if($this->etatExemplaire == 0)
				$this->erreurEtat = true;
				
			if($this->lieuReel == 0)
				$this->erreurLieuReel = true;
				
			if(strcmp($this->codeBarre, "") == 0)
				$this->erreurCodeBarre = true;
			
			if(!$this->erreurPrixMdjt && !$this->erreurEtat && !$this->erreurLieuReel && !$this->erreurCodeBarre)
			{
				if($this->idExemplaire == 0)
					$this->idExemplaire = $this->maBase->InsertionTableExemplaire($this->codeBarre, $this->descriptionExemplaire, $this->prixMDJT, $this->dateAchat, $this->dateFinVie, $this->idVersion, $this->etatExemplaire, $this->lieuReel, $this->lieuTempo);
				else
					if(!$this->maBase->UpdateTableExemplaire($this->idExemplaire, $this->codeBarre, $this->descriptionExemplaire, $this->prixMDJT, $this->dateAchat, $this->dateFinVie, $this->idVersion, $this->etatExemplaire, $this->lieuReel, $this->lieuTempo))
						$this->erreurUpdate = true;
					else
						$this->maBase->DeleteTableLanguesRegles($this->idExemplaire);

				if($this->idExemplaire != null && !$this->erreurUpdate)
				{
					foreach($this->listeLangueRegle as $langueRegle)
						$this->maBase->InsertionTableLangueRegle($this->idExemplaire, $langueRegle);
						
					if($_POST["AjouterExemplaire"])
					{
						header("Location: " . MODULE_GESTION_JEUX . "&ajoutExemplaire=true&idExemplaire=".$this->idExempalire);
						exit;
					}
					else
					{
						$this->idJeu = 0;
						$this->idVersion = 0;
					}
				} else
					$this->erreurExemplaire = true;
			}

		} elseif ($_POST["ValiderNomJeu"])
		{
			$this->recuperationInformationsFormulaire();
			$this->idVersion = 0;
			
			$this->idJeu = $this->maBase->recupIdJeu($this->nomJeu);
		
		} elseif ($_POST["ValiderNomVersion"])
		{
			$this->recuperationInformationsFormulaire();
			
			$this->idVersion = $this->maBase->recupIdVersion($this->nomVersion);
		
		} elseif ($_POST["ModifierNom"])
		{
			$this->idJeu = 0;
			$this->idVersion = 0;
			
		}
	}	
    
}

?>
