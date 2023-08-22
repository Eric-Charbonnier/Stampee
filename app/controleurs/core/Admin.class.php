<?php

/**
 * Classe Contrôleur des requêtes de l'application admin
 */

class Admin extends Routeur
{

  private $entite;
  private $action;
  private $utilisateur_id;
  private $timbre_id;

  private $oUtilisateur;

  private $methodes = [
    'utilisateur' => [
      'l' => 'listerUtilisateurs',
      'a' => 'ajouterUtilisateur',
      'm' => 'modifierUtilisateur',
      's' => 'supprimerUtilisateur',
      'c' => 'connecter',
      'd' => 'deconnecter',
      'g' => 'genererUnNouveauMdp',

      // 'g' => 'testEnvoiCourriel'
    ],
    'timbre' => [
      'l' => 'listerTimbres',
      'a' => 'ajouterTimbre',
      'm' => 'modifierTimbre',
      's' => 'supprimerTimbre'
    ]
  ];

  private $classRetour = "fait";
  private $messageRetourAction = "";

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

  /**
   * Connecter un utilisateur
   */
  public function connecter()
  {
    $messageErreurConnexion = "";
    if (count($_POST) !== 0) {
      $utilisateur = $this->oRequetesSQL->connecter($_POST);
      if ($utilisateur !== false) {
        $_SESSION['oUtilisateur'] = new Utilisateur($utilisateur);
        $this->oUtilisateur = $_SESSION['oUtilisateur'];
      } else {
        $messageErreurConnexion = "Courriel ou mot de passe incorrect.";
      }
    }
    // print_r($utilisateur);

    if (isset($_SESSION['oUtilisateur'])) {
      $timbres = $this->oRequetesSQL->getEncheres();
      $vedettes = $this->oRequetesSQL->getEncheres();
      (new Vue)->generer(
        'vAccueil',
        array(
          'utilisateur'            => $utilisateur,
          'timbres'                => $timbres,
          'vedettes'               => $vedettes,
          'messageErreurConnexion' => $messageErreurConnexion
        ),
        'gabarit-frontend'
      );
    } else {
      (new Vue)->generer(
        'vLogin',
        array(
          'utilisateur'            => $utilisateur,
          'messageErreurConnexion' => $messageErreurConnexion
        ),
        'gabarit-frontend'
      );
    }

    // (new Vue)->generer(
    //   'vLogin',
    //   array(
    //     'titre'                  => 'STAMPEE - Login',
    //     'utilisateur'            => $utilisateur,
    //     'messageErreurConnexion' => $messageErreurConnexion
    //   ),
    //   'gabarit-frontend'
    // );
  }

  /**
   * Déconnecter un utilisateur
   */
  public function deconnecter()
  {
    unset($_SESSION);
    session_destroy();
    $timbres = $this->oRequetesSQL->getEncheres();
    $vedettes = $this->oRequetesSQL->getEncheres();
    (new Vue)->generer(
      'vAccueil',
      array(
        'timbres'                => $timbres,
        'vedettes'               => $vedettes,
      ),
      'gabarit-frontend'
    );
  }
  /**
   * generer mdp Test d'envoi de courriel en utilisant l'utilisateur connecté comme destinataire
   */
  public function genererUnNouveauMdp()
  {
    if ($this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_ADMINISTRATEUR && $this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_VENDEUR)
      throw new Exception(Routeur::FORBIDDEN);

    $aUtilisateur = $this->oRequetesSQL->getUtilisateur($this->utilisateur_id);
    // echo '<pre>' . var_export($this->oRequetesSQL->getUtilisateur($this->utilisateur_id), true) . '</pre>';

    $oUtilisateur = new Utilisateur($aUtilisateur);
    $oUtilisateur->genererMdp();

    $cle = $oUtilisateur->utilisateur_mdp;
    $retour = (new GestionCourriel)->envoyerMdp($oUtilisateur);

    if ($this->oRequetesSQL->genererUnNouveauMdp($this->utilisateur_id, $cle) && $retour && ENV === "DEV") {
      $this->messageRetourAction = "Renouvellement d'un nouveau mot de passe pour l'utilisateur 'id' numéro: $this->utilisateur_id, effectuée." . "Courriel envoyé<br>." . "<a href=\"$retour\">Message dans le fichier $retour</a>";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Renouvellement de mot de passe $this->utilisateur_id non effectué.";
    }
    $this->listerUtilisateurs();
  }

  /**
   * Lister les utilisateurs
   */
  public function listerUtilisateurs()
  {
    if ($this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_ADMINISTRATEUR && $this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_VENDEUR)
      throw new Exception(Routeur::FORBIDDEN);

    $utilisateurs = $this->oRequetesSQL->getUtilisateurs();

    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }

    (new Vue)->generer(
      'vAdminUtilisateurs',
      array(
        'oUtilisateur'        => $this->oUtilisateur,
        'titre'               => 'Gestion des utilisateurs',
        'utilisateurs'        => $utilisateurs,
        'classRetour'         => $this->classRetour,
        'messageRetourAction' => $this->messageRetourAction
      ),
      'gabarit-admin'
    );
  }

  /**
   * Ajouter un utilisateur
   */
  public function ajouterUtilisateur()
  {
    // if ($this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_ADMINISTRATEUR && $this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_VENDEUR)
    //   throw new Exception(Routeur::FORBIDDEN);
    // echo 'je suis dans ajouter utilisateur';
    // echo '<pre>' . var_export($_POST, true) . '</pre>';
    $utilisateur  = [];
    $erreurs = [];
    if (count($_POST) !== 0) {
      // retour de saisie du formulaire
      $utilisateur = $_POST;
      $oUtilisateur = new Utilisateur($utilisateur); // création d'un objet Utilisateur pour contrôler la saisie

      $erreurs = $oUtilisateur->erreurs;
      if (count($erreurs) === 0) { // aucune erreur de saisie -> requête SQL d'ajout
        // $oUtilisateur->genererMdp();
        $utilisateur_id = $this->oRequetesSQL->ajouterUtilisateur([
          // 'utilisateur_id'       => $oUtilisateur->utilisateur_id,
          'utilisateur_prenom'      => $oUtilisateur->utilisateur_prenom,
          'utilisateur_nom'         => $oUtilisateur->utilisateur_nom,
          'utilisateur_courriel'    => $oUtilisateur->utilisateur_courriel,
          'utilisateur_mdp'         => $oUtilisateur->utilisateur_mdp,
          'Role_role_id'             => $oUtilisateur->Role_role_id

        ]);
        // echo '<pre>' . print_r($utilisateur_id) . '</pre>';
      }
    }

    if (count($erreurs) === 0) {

      if (isset($_SESSION['oUtilisateur'])) {
        $utilisateur = $_SESSION['oUtilisateur'];
      } else {
        $utilisateur = null;
      }
      (new Vue)->generer(
        'vLogin',
        array(
          'oUtilisateur' => $this->oUtilisateur,
          'titre'        => 'Login',
          'utilisateur'  => $utilisateur,
          'erreurs'      => $erreurs
        ),
        'gabarit-frontend'
      );
    } else {
      (new Vue)->generer(
        'vRegister',
        array(
          'oUtilisateur' => $this->oUtilisateur,
          'erreurs'      => $erreurs
        ),
        'gabarit-frontend'
      );
    }
  }

  /**
   * Modifier un utilisateur identifié par sa clé dans la propriété utilisateur_id
   */
  public function modifierUtilisateur()
  {
    if ($this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_ADMINISTRATEUR && $this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_VENDEUR)
      throw new Exception(Routeur::FORBIDDEN);

    if (count($_POST) !== 0) {
      $utilisateur = $_POST;
      $oUtilisateur = new Utilisateur($utilisateur);
      $erreurs = $oUtilisateur->erreurs;
      if (count($erreurs) === 0) {
        if ($this->oRequetesSQL->modifierUtilisateur([
          'utilisateur_id'          => $oUtilisateur->utilisateur_id,
          'utilisateur_nom'         => $oUtilisateur->utilisateur_nom,
          'utilisateur_prenom'      => $oUtilisateur->utilisateur_prenom,
          'utilisateur_courriel'    => $oUtilisateur->utilisateur_courriel,
          'Role_role_id'      => $oUtilisateur->Role_role_id,
        ])) {
          $this->messageRetourAction = "Modification de l'utilisateur numéro $this->utilisateur_id effectuée.";
        } else {
          $this->classRetour = "erreur";
          $this->messageRetourAction = "modification de l'utilisateur numéro $this->utilisateur_id non effectuée.";
        }
        $this->listerUtilisateurs();
        exit;
      }
    } else {
      // chargement initial du formulaire  
      // initialisation des champs dans la vue formulaire avec les données SQL de cet utilisateur  
      $utilisateur  = $this->oRequetesSQL->getUtilisateur($this->utilisateur_id);
      $erreurs = [];
    }

    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }

    (new Vue)->generer(
      'vAdminUtilisateurModifier',
      array(
        'oUtilisateur' => $this->oUtilisateur,
        'titre'        => "Modifier l'utilisateur numéro $this->utilisateur_id",
        'utilisateur'       => $utilisateur,
        'erreurs'      => $erreurs
      ),
      'gabarit-admin'
    );
  }

  /**
   * Supprimer un utilisateur identifié par sa clé dans la propriété utilisateur_id
   */
  public function supprimerUtilisateur()
  {
    if ($this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_ADMINISTRATEUR && $this->oUtilisateur->Role_role_id !== Utilisateur::ROLE_VENDEUR)
      throw new Exception(Routeur::FORBIDDEN);

    if ($this->oRequetesSQL->supprimerUtilisateur($this->utilisateur_id)) {
      $this->messageRetourAction = "Suppression de l'utilisateur numéro $this->utilisateur_id effectuée.";
    } else {
      $this->classRetour = "erreur";
      $this->messageRetourAction = "Suppression de l'utilisateur numéro $this->utilisateur_id non effectuée.";
    }
    $this->listerUtilisateurs();
  }



  /**
   * Lister la page de page admin
   * 
   */
  public function afficherAdmin()
  {
    $timbres = $this->oRequetesSQL->getEncheres();
    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }
    (new Vue)->generer(
      "vAdmin",
      array(
        'titre'  => "Stampee - Admin",
        'timbres' => $timbres,
        'utilisateur' => $utilisateur,
        // 'miseCount' =>  $miseCount
      ),
      "vAdmin"
    );
  }
}
