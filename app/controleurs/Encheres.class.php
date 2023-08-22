<?php

/**
 * Classe Contrôleur des requêtes de l'application Enchere
 */

class Encheres extends Routeur
{

  private $entite;
  private $action;
  private $enchere_id;
  private $enchere_debut;
  private $enchere_fin;
  private $Utilisateur_utilisateur_id;
  private $timbre_id;

  private $oEncheres;

  private $methodes = [
    'enchere' => [
      'l' => 'listerEncheres',
      'a' => 'ajouterEnchere',
      'm' => 'modifierEnchere',
      's' => 'supprimerEnchere',
      'r' => 'rechercherEnchere',
      'f' => 'filtrerEnchere'
    ],
    'timbre' => [
      'l' => 'listerTimbres',
      'a' => 'ajouterTimbre',
      'm' => 'modifierTimbre',
      's' => 'supprimerTimbre'
    ],
    'mise' => [
      'l' => 'listerMises',
      'a' => 'ajouterMise',
      'm' => 'modifierMise',
      's' => 'supprimerMise'
    ]
  ];

  private $classRetour = "fait";
  private $messageRetourAction = "";

  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */
  public function __construct()
  {
    $this->entite             = $_GET['entite']               ?? 'enchere';
    $this->action             = $_GET['action']               ?? 'l';
    $this->enchere_id         = $_GET['enchere_id']           ?? null;
    $this->timbre_id          = $_GET['timbre_id']            ?? null;
    $this->enchere_debut      = $_GET['enchere_debut']        ?? null;
    $this->enchere_fin        = $_GET['enchere_fin']          ?? null;
    $this->oRequetesSQL       = new RequetesSQL;
  }


  /**
   * Gérer l'interface enchère
   */
  public function gererAdmin()
  {
    // echo 'je suis dans gerer admin';
    // if (isset($_SESSION['oUtilisateur'])) {
    //   $this->oUtilisateur = $_SESSION['oUtilisateur'];
    if (isset($this->methodes[$this->entite])) {
      if (isset($this->methodes[$this->entite][$this->action])) {
        $methode = $this->methodes[$this->entite][$this->action];
        $this->$methode();
        // echo "connecté";
      } else {
        throw new Exception("L'action $this->action de l'entité $this->entite n'existe pas.");
      }
    } else {
      throw new Exception("L'entité $this->entite n'existe pas.");
    }
    // } else {
    //   // echo 'non connecté';
    //   $this->connecter();
    // }
    // }
  }
  /**
   * Ajouter un enchere
   */
  public function ajouterEnchere()
  {
    $nom_fichier = $_FILES['userfile']['name'];
    $fichier = $_FILES['userfile']['tmp_name'];
    $taille = $_FILES['userfile']['size'];

    $url_image = "assets/img/upload/" . $nom_fichier;
    if (move_uploaded_file($fichier, $url_image)) {

      $enchere  = [];
      $erreurs = [];
      $enchere_debut = $_POST['enchere_debut'];
      $enchere_fin = $_POST['enchere_fin'];
      $id = $_POST['utilisateur_id'];


      if (count($_POST) !== 0) {
        // retour de saisie du formulaire
        $enchere = $_POST;
        // echo '<pre>' . print_r($enchere) . '</pre>';
        $oEncheres = new Encheres($enchere); // création d'un objet Enchere pour contrôler la saisie
        // $erreurs = $oEncheres->erreurs;
        // if (count($erreurs) === 0) { // aucune erreur de saisie -> requête SQL d'ajout
        // $oEncheres->genererMdp();
        $enchere_id = $this->oRequetesSQL->ajouterEnchere([
          'enchere_debut'                 => $enchere_debut,
          'enchere_fin'                   => $enchere_fin,
          'Utilisateur_utilisateur_id'    => $id
        ]);

        $timbre_id = $this->oRequetesSQL->ajouterTimbre([
          'timbre_lot'                    => $_POST['timbre_lot'],
          'timbre_titre'                  => $_POST['timbre_titre'],
          'timbre_description'            => $_POST['timbre_description'],
          'timbre_condition'              => $_POST['timbre_condition'],
          'timbre_date'                   => $_POST['timbre_date'],
          'timbre_prix'                   => $_POST['timbre_prix'],
          'timbre_pays'                   => $_POST['timbre_pays'],
          'Enchere_enchere_id'            => $enchere_id
        ]);

        $image_id = $this->oRequetesSQL->ajouterImage([
          'image_url'                    => $url_image,
          'Timbre_timbre_id'             => $timbre_id,
          'Enchere_enchere_id'            => $enchere_id
        ]);
      }
    }
    $timbres = $this->oRequetesSQL->getEncheres();
    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }

    (new Vue)->generer(
      'vCatalogue',
      array(
        'oEncheres'    => $this->oEncheres,
        'titre'        => 'Catalogue',
        'enchere'      => $enchere,
        'timbres'      => $timbres,
        'utilisateur'  => $utilisateur,
        'erreurs'      => $erreurs
      ),
      'gabarit-frontend'
    );
  }

  /**
   * Ajouter une mise
   */
  public function ajouterMise()
  {

    // if (count($_POST) !== 0) {
    //   $utilisateur = $this->oRequetesSQL->connecter($_POST);
    //   if ($utilisateur !== false) {
    //     $_SESSION['oUtilisateur'] = new Utilisateur($utilisateur);
    //     $this->oUtilisateur = $_SESSION['oUtilisateur'];
    //   } else {
    //     $messageErreurConnexion = "Courriel ou mot de passe incorrect.";
    //   }
    // }

    // echo '<pre>' . print_r($_POST) . '</pre>';
    $erreurs = [];
    if (count($_POST) !== 0) {
      $mise = $this->oRequetesSQL->getMiseEnchere($_POST["enchere_id"]);
      $timbre = $this->oRequetesSQL->getEnchere($_POST["enchere_id"]);


      // if (isset($mise['mise_montant'])) {
      //   $derniereMise = $mise['mise_montant'];
      // } else {
      //   $derniereMise = "";
      // }

      // $derniereMise = ($mise['mise_montant']);
      $messageErreurMise = "";
      $messageBonneMise = "";
      if ($_POST['mise_montant'] <= $timbre['timbre_prix']) {
        $messageErreurMise = "Mise trop basse par rapport au prix planché";
      } elseif ((isset($mise['mise_montant']) && $_POST['mise_montant'] <= $mise['mise_montant'])) {
          $messageErreurMise = "Mise trop basse par rapport à la mise précédente";
        } else {
          $messageBonneMise = "✅ Félicitations, votre enchère a été placée";
          $mise_date = date("Y-m-d h:i:s");
          $mise_id = $this->oRequetesSQL->ajouterMise([
            'mise_date'                     => $mise_date,
            'mise_montant'                  => $_POST['mise_montant'],
            'Utilisateur_utilisateur_id'    => $_POST['utilisateur_id'],
            'Enchere_enchere_id'            => $_POST['enchere_id']
          ]);
        }
      
      $timbre = $this->oRequetesSQL->getEnchere($_POST["enchere_id"]);
      $mise = $this->oRequetesSQL->getMiseEnchere($_POST["enchere_id"]);
      $miseCount = $this->oRequetesSQL->getMiseEnchereCount($_POST["enchere_id"]);
      $enchere = $this->oRequetesSQL->getEnchere($_POST['enchere_id']);
      $memeCategorie = $this->oRequetesSQL->getMemeCategorie($enchere['timbre_pays']);



      if (isset($_SESSION['oUtilisateur'])) {
        $utilisateur = $_SESSION['oUtilisateur'];
      } else {
        $utilisateur = null;
      }

      (new Vue)->generer(
        'vEnchere',
        array(
          'titre'         => 'Enchere',
          'timbre'        => $timbre,
          'utilisateur'   => $utilisateur,
          'mise'          => $mise,
          'miseCount'     => $miseCount,
          'erreurs'       => $erreurs,
          'memeCategorie' => $memeCategorie,
          'messageErreurMise' => $messageErreurMise,
          'messageBonneMise' => $messageBonneMise
        ),
        'gabarit-frontend'
      );
    }
  }

  /**
   * Lister les utilisateurs
   */
  public function listerEncheres()
  {
    // if ($this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_ADMINISTRATEUR && $this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_VENDEUR)
    //   throw new Exception(Routeur::FORBIDDEN);

    $encheres = $this->oRequetesSQL->getEncheres();

    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }

    (new Vue)->generer(
      'vCatalogue',
      array(
        'oEncheres'        => $this->oEncheres,
        'titre'               => 'Gestion des encheres',
        'encheres'        => $encheres,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }

  /**
   * rechercher une enchère
   */
  public function rechercherEnchere()
  {
    // echo "rechercher enchere";
    $inputRechercher = $_POST['inputRechercher'];

    if ($_POST != "") {
      $encheresResultat = $this->oRequetesSQL->rechercherEnchere($inputRechercher);
      // $timbres = $this->oRequetesSQL->getEncheres();
      // print_r($encheresResultat);

      if (isset($_SESSION['oUtilisateur'])) {
        $utilisateur = $_SESSION['oUtilisateur'];
      } else {
        $utilisateur = null;
      }

      (new Vue)->generer(
        'vCatalogue',
        array(
          'timbres'           => $encheresResultat,
          'utilisateur'       => $utilisateur,
        ),
        'gabarit-frontend'
      );
    }
  }


  /**
   * filtrer une enchère
   */
  public function filtrerEnchere()
  {
    $inputCondition = $_POST['inputCondition'] ?? null;
    $inputPays = $_POST['inputPays'] ?? null;
    $inputPrix =  $_POST['inputPrix'] ?? null;
    // print_r($inputCondition);
    // print_r($inputPays);
    // print_r($inputPrix);

    if ($inputCondition != "") {
      $resultat = $this->oRequetesSQL->filtrerEnchere($inputCondition);
      // print_r($resultat);
      // echo " je suis dans la requete";
    }

    if ($inputPays != "") {
      $resultat = $this->oRequetesSQL->filtrerEnchere($inputPays);
    }

    if ($inputPrix != "") {
      $resultat = $this->oRequetesSQL->appliquerTri($inputPrix);
    }
    // if ($_POST != "") {
    //   $resultat = $this->oRequetesSQL->filtrerEnchere($inputCondition);
    // }
    // $timbres = $this->oRequetesSQL->getEncheres();



    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }

    (new Vue)->generer(
      'vCatalogue',
      array(
        'timbres'           => $resultat,
        'utilisateur'       => $utilisateur,
      ),
      'gabarit-frontend'
    );
  }
}
