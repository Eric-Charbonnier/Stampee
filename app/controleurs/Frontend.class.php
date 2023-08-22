<?php

/**
 * Classe Contrôleur des requêtes de l'interface frontend
 * 
 */

class Frontend extends Routeur
{

  private $timbre_id;

  /**
   * Constructeur qui initialise des propriétés à partir du query string
   * et la propriété oRequetesSQL déclarée dans la classe Routeur
   * 
   */
  public function __construct()
  {
    $this->timbre_id = $_GET['timbre_id'] ?? null;
    $this->oRequetesSQL = new RequetesSQL;
  }


  /**
   * Lister la page d'accueil
   * 
   */
  public function afficherAccueil()
  {
    // $timbres = $this->oRequetesSQL->getEncheres();
    // $enchere = $this->oRequetesSQL->getEnchere();
    $vedettes = $this->oRequetesSQL->getVedette();
    // $miseCount = $this->oRequetesSQL->getMiseEnchereCount($_GET["timbre_id"]);

    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }
    // echo "<pre>";
    // print_r($vedette);

    (new Vue)->generer(
      "vAccueil",
      array(
        'titre'  => "Stampee - Accueil",
        // 'timbres' => $timbres,
        'vedettes' => $vedettes,
        // 'timbre'        => $enchere,
        'utilisateur' => $utilisateur
        // 'miseCount' =>  $miseCount
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Lister la page de catalogue
   * 
   */
  public function afficherCatalogue()
  {
    $encheres = $this->oRequetesSQL->getEncheres();

    // $miseCount = $this->oRequetesSQL->getMiseEnchereCount($_GET["timbre_id"]);
    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }
    (new Vue)->generer(
      "vCatalogue",
      array(
        'titre'  => "Stampee - Catalogue",
        'timbres' => $encheres,
        'utilisateur' => $utilisateur,
        // 'miseCount' =>  $miseCount
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Lister la page d'enchère'
   * 
   */
  public function afficherEnchere()
  {
    // print_r($_GET);
    $enchere = $this->oRequetesSQL->getEnchere($_GET['enchere_id']);
    $mise = $this->oRequetesSQL->getMiseEnchere($_GET['enchere_id']);
    $miseCount = $this->oRequetesSQL->getMiseEnchereCount($_GET['enchere_id']);
    $memeCategorie = $this->oRequetesSQL->getMemeCategorie($enchere['timbre_pays']);
    // echo $enchere['timbre_pays'];
    // echo '<pre>' . print_r($enchere) . '</pre>';
    
    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }

    (new Vue)->generer(
      "vEnchere",
      array(
        'titre'         => "Stampee - Enchere",
        'timbre'        => $enchere,
        'memeCategorie' => $memeCategorie,
        'utilisateur'   => $utilisateur,
        'mise'          => $mise,
        'miseCount'     =>  $miseCount
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Lister la page de register
   * 
   */
  public function afficherRegister()
  {
    // $timbres = $this->oRequetesSQL->getEncheres();
    (new Vue)->generer(
      "vRegister",
      array(
        'titre'  => "Stampee - Devenir membre",
        // 'timbres' => $timbres
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Lister la page de login
   * 
   */
  public function afficherLogin()
  {
    // $timbres = $this->oRequetesSQL->getEncheres();
    (new Vue)->generer(
      "vLogin",
      array(
        'titre'  => "Stampee - Comnnexion",
        // 'timbres' => $timbres
      ),
      "gabarit-frontend"
    );
  }

    /**
   * Lister la page de profil
   * 
   */
  public function afficherProfilMembre()
  {
    // print_r($_GET);

    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }
    
    $utilisateur  = $this->oRequetesSQL->getUtilisateur($_GET['utilisateur_id']);
    $encheres = $this->oRequetesSQL->getEnchereEnCoursParUserId($_GET['utilisateur_id']);
    $mises = $this->oRequetesSQL->getMiseParUserId($_GET['utilisateur_id']);
    (new Vue)->generer(
      "vProfil",
      array(
        'titre'  => "Stampee - Page profil",
        'utilisateur' => $utilisateur,
        'encheres' => $encheres,
        'mises' => $mises
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Lister la page Placer une enchère
   * 
   */
  public function afficherPlacerEnchere()
  {

    if (isset($_SESSION['oUtilisateur'])) {
      $utilisateur = $_SESSION['oUtilisateur'];
    } else {
      $utilisateur = null;
    }
    
    (new Vue)->generer(
      "vPlacerEnchere",
      array(
        'titre'  => "Stampee - Placer une enchère",
        'utilisateur' => $utilisateur
      ),
      "gabarit-frontend"
    );
  }










  /**
   * Lister les timbres diffusés prochainement
   * 
   */
  public function listerProchainement()
  {
    $timbres = $this->oRequetesSQL->getEncheres('prochainement');
    (new Vue)->generer(
      "vListeTimbres",
      array(
        'titre'  => "Prochainement",
        'timbres' => $timbres
      ),
      "gabarit-frontend"
    );
  }

  /**
   * Voir les informations d'un timbre
   * 
   */
  public function voirTimbre()
  {
    $timbre = false;
    if (!is_null($this->timbre_id)) {
      $timbre = $this->oRequetesSQL->getEnchere($this->timbre_id);
      $realisateurs = $this->oRequetesSQL->getRealisateursTimbre($this->timbre_id);
      $pays         = $this->oRequetesSQL->getPaysTimbre($this->timbre_id);
      $acteurs      = $this->oRequetesSQL->getActeursTimbre($this->timbre_id);

      // si affichage avec vTimbre2.twig
      // =============================
      // $seances      = $this->oRequetesSQL->getSeancesTimbre($this->timbre_id); 

      // si affichage avec vTimbre.twig
      // ============================
      $seancesTemp  = $this->oRequetesSQL->getSeancesTimbre($this->timbre_id);
      $seances = [];
      foreach ($seancesTemp as $seance) {
        $seances[$seance['seance_date']]['jour']     = $seance['seance_jour'];
        $seances[$seance['seance_date']]['heures'][] = $seance['seance_heure'];
      }
    }
    if (!$timbre) throw new Exception("Timbre inexistant.");

    (new Vue)->generer(
      "vTimbre",
      array(
        'titre'        => $timbre['timbre_titre'],
        'timbre'         => $timbre,
        'realisateurs' => $realisateurs,
        'pays'         => $pays,
        'acteurs'      => $acteurs,
        'seances'      => $seances
      ),
      "gabarit-frontend"
    );
  }
}
