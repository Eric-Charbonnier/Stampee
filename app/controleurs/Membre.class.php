<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */

class Membre extends Routeur
{

  private $entite;
  private $action;
  private $utilisateur_id;
  private $timbre_id;

  private $oUtilisateur;

  private $methodes = [
    'enchereMembre' => [
      'l' => 'listerEncheresEnCours',
      'm' => 'modifierEnchereEnCours',
      's' => 'supprimerEnchereEnCours'
    ],
    'favoris' => [
      'l' => 'listerEncheresEnCours',
      'm' => 'modifierEnchereEnCours',
      's' => 'supprimerEnchereEnCours'
    ],
    'enchere' => [
      'l' => 'afficherModifierEnchere',
      'a' => 'ajouterEnchere',
      'm' => 'modifierEnchere',
      's' => 'supprimerEnchere',
    ],
    'mise' => [
      'l' => 'listerMises',
      'a' => 'ajouterMise',
      'm' => 'modifierMise',
      's' => 'supprimerMise'
    ],
    'utilisateur' => [
      'l' => 'afficherModifierMembre',
      'a' => 'ajouterUtilisateur',
      'm' => 'modifierUtilisateur',
      's' => 'supprimerUtilisateur',
      'c' => 'connecter',
      'd' => 'deconnecter',
      'g' => 'genererUnNouveauMdp',
      // 'g' => 'testEnvoiCourriel'
    ],
  ];


  /**
   * Constructeur qui initialise le contexte du contrôleur  
   */
  public function __construct()
  {
    $this->entite             = $_GET['entite']         ?? 'utilisateur';
    $this->action             = $_GET['action']         ?? 'l';
    $this->utilisateur_id     = $_GET['utilisateur_id'] ?? null;
    $this->timbre_id          = $_GET['timbre_id']      ?? null;
    $this->oRequetesSQL       = new RequetesSQL;
  }

  /**
   * Gérer l'interface d'administration 
   */
  public function gererAdmin()
  {
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
    //   $this->connecter();
    // }
  }


  public function afficherModifierMembre()
  {

    $utilisateur  = $this->oRequetesSQL->getUtilisateur($_GET['utilisateur_id']);


    (new Vue)->generer(
      "vModifierRegister",
      array(
        'titre' => "Modifier Membre - Enchères Stampee",
        'utilisateur' => $utilisateur
      ),
      "gabarit-frontend"
    );
  }





  /**
   * Ajouter un utilisateur
   */
  public function modifierUtilisateur()
  {

    // print_r($_POST);

    $utilisateur  = [];
    $erreurs = [];
    if (count($_POST) !== 0) {
      $utilisateur = $_POST;
      $oUtilisateur = new Utilisateur($utilisateur); // création d'un objet Utilisateur pour contrôler la saisie
      $erreurs = $oUtilisateur->erreurs;
      // print_r($erreurs);

      if (count($erreurs) === 0) { // aucune erreur de saisie -> requête SQL d'ajout
        $utilisateur_id = $this->oRequetesSQL->modifierUtilisateur([
          'utilisateur_id'     => $oUtilisateur->utilisateur_id,
          'utilisateur_nom'    => $oUtilisateur->utilisateur_nom,
          'utilisateur_prenom' => $oUtilisateur->utilisateur_prenom,
          'utilisateur_courriel' => $oUtilisateur->utilisateur_courriel,
          'Role_role_id' => $oUtilisateur->Role_role_id,
          'utilisateur_mdp' => $oUtilisateur->utilisateur_mdp
        ]);
        $_SESSION['oUtilisateur'] = $oUtilisateur;
      }
    }
    if (count($erreurs) === 0) {

      $utilisateur  = $this->oRequetesSQL->getUtilisateur($_POST['utilisateur_id']);
      $encheres = $this->oRequetesSQL->getEnchereEnCoursParUserId($_POST['utilisateur_id']);
      $mises = $this->oRequetesSQL->getMiseParUserId($_POST['utilisateur_id']);

      (new Vue)->generer(
        "vProfil",
        array(
          'encheres' => $encheres,
          'mises' => $mises,
          'utilisateur' => $utilisateur
        ),
        "gabarit-frontend"
      );
    } else {
      (new Vue)->generer(
        "vModifierRegister",
        array(
          'utilisateur' => $utilisateur,
          'erreurs' => $erreurs
        ),
        "gabarit-frontend"
      );
    }
  }



  public function supprimerMise()
  {
    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }
    // $encheres = $this->oRequetesSQL->getEnchereEnCoursParUserId($_GET['utilisateur_id']);
    // $mises = $this->oRequetesSQL->getMiseParUserId($_GET['mise_id']);


    $mise_id = $this->oRequetesSQL->supprimerMise($_GET['mise_id']);
    $encheres = $this->oRequetesSQL->getEnchereEnCoursParUserId($_GET['utilisateur_id']);
    $mises = $this->oRequetesSQL->getMiseParUserId($_GET['utilisateur_id']);

    (new Vue)->generer(
      "vProfil",
      array(
        'titre'  => "Stampee - Page profil",
        'utilisateur' => $utilisateur,
        'encheres' => $encheres,
        'mises' => $mises,
        'mise_id' => $mise_id,
        'utilisateur_id' => $_GET['utilisateur_id'],
      ),
      "gabarit-frontend"
    );
  }

  /**
   * 
   * Supprimer une enchere identifié par sa clé ID, ainsi que tous les timbres, images et mises reliés
   * 
   */
  public function supprimerEnchere()
  {
    $enchere_id = $_GET['enchere_id'];
    $utilisateur_id = $_GET['utilisateur_id'];
    $this->oRequetesSQL->supprimerImage($enchere_id);
    $this->oRequetesSQL->supprimerMiseParEnchereId($enchere_id);
    $this->oRequetesSQL->supprimerTimbre($enchere_id);
    $this->oRequetesSQL->supprimerEnchere($enchere_id);

    $encheres = $this->oRequetesSQL->getEnchereEnCoursParUserId($_GET['utilisateur_id']);
    $mises = $this->oRequetesSQL->getMiseParUserId($_GET['utilisateur_id']);
    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }
    (new Vue)->generer(
      "vProfil",
      array(
        'encheres' => $encheres,
        'mises' => $mises,
        'utilisateur' => $utilisateur
      ),
      "gabarit-frontend"
    );
  }








  /**
   * Lister la page Placer une enchère
   * 
   */
  public function afficherModifierEnchere()
  {
    $enchere = $this->oRequetesSQL->getEnchere($_GET['enchere_id']);
    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }

    (new Vue)->generer(
      "vModifierEnchere",
      array(
        'titre'  => "Stampee - Modifier une enchère",
        'utilisateur' => $utilisateur,
        'enchere' => $enchere
      ),
      "gabarit-frontend"
    );
  }


  /**
   * 
   * Supprimer une enchere identifié par sa clé ID, ainsi que tous les timbres, images et mises reliés
   * 
   */
  public function modifierEnchere()
  {
    $nom_fichier = $_FILES['userfile']['name'];
    $fichier = $_FILES['userfile']['tmp_name'];
    $taille = $_FILES['userfile']['size'];

    $url_image = "assets/img/upload/" . $nom_fichier;
    if (move_uploaded_file($fichier, $url_image)) {

      // print_r($_POST);
      // exit;

      if (count($_POST) !== 0) {

        $mise_date = date("Y-m-d h:i:s");
        $enchere = $this->oRequetesSQL->modifierEnchere([
          'enchere_debut'             => $_POST['enchere_debut'],
          'enchere_fin'               => $_POST['enchere_fin'],
          'Enchere_enchere_id'        => $_POST['enchere_id']
        ]);
        $timbre = $this->oRequetesSQL->modifierTimbre([
          'timbre_lot'                    => $_POST['timbre_lot'],
          'timbre_titre'                  => $_POST['timbre_titre'],
          'timbre_description'            => $_POST['timbre_description'],
          'timbre_condition'              => $_POST['timbre_condition'],
          'timbre_date'                   => $_POST['timbre_date'],
          'timbre_prix'                   => $_POST['timbre_prix'],
          'timbre_pays'                   => $_POST['timbre_pays'],
          'Enchere_enchere_id'            => $_POST['enchere_id']
        ]);
        $image = $this->oRequetesSQL->modifierImage([
          'image_url'                    => $url_image,
          'Enchere_enchere_id'           => $_POST['enchere_id']
        ]);
      }
      // $utilisateur_id = $_GET['utilisateur_id'];
      // $this->oRequetesSQL->modifierImage($enchere_id);
      // $this->oRequetesSQL->modifierMiseParEnchereId($enchere_id);

      // $modif = $this->oRequetesSQL->modifierEnchere($enchere_id);

      $encheres = $this->oRequetesSQL->getEnchereEnCoursParUserId($_POST['utilisateur_id']);
      $mises = $this->oRequetesSQL->getMiseParUserId($_POST['utilisateur_id']);
      if (isset($_SESSION['oUtilisateur'])) {
        $utilisateur = $_SESSION['oUtilisateur'];
      } else {
        $utilisateur = null;
      }
      (new Vue)->generer(
        "vProfil",
        array(
          'encheres' => $encheres,
          'utilisateur' => $utilisateur,
          'mises' => $mises
        ),
        "gabarit-frontend"
      );
    }
  }
}
