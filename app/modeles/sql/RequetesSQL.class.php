<?php

/**
 * Classe des requêtes SQL
 *
 */
class RequetesSQL extends RequetesPDO
{


  /* GESTION DES UTILISATEURS 
     ======================== */

  /**
   * Connecter un utilisateur
   * @param array $champs, tableau avec les champs utilisateur_courriel et utilisateur_mdp  
   * @return array|false ligne de la table, false sinon 
   */
  public function connecter($champs)
  {
    $this->sql = "
      SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, utilisateur_courriel, Role_role_id, role_nom
      FROM utilisateur
      INNER JOIN role ON Role_role_id = role_id
      WHERE utilisateur_courriel = :utilisateur_courriel AND utilisateur_mdp = SHA2(:utilisateur_mdp, 512)";
    return $this->getLignes($champs, RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération des timbres à l'affiche ou prochainement
   * @param  string $critere
   * @return array tableau des lignes produites par la select   
   */
  public function getEncheres()
  {
    $oAujourdhui = ENV === "DEV" ? new DateTime(MOCK_NOW) : new DateTime();
    $aujourdhui  = $oAujourdhui->format('Y-m-d');
    $dernierJour = $oAujourdhui->modify('+6 day')->format('Y-m-d');
    $this->sql = "
      SELECT timbre_id, timbre_lot, timbre_titre, timbre_description, timbre_condition,
              timbre_date, timbre_prix, timbre_pays, enchere_debut, enchere_fin, image_url, timbre.Enchere_enchere_id 
      FROM timbre
      INNER JOIN enchere ON timbre.Enchere_enchere_id = enchere_id
      INNER JOIN image ON timbre_id = Timbre_timbre_id";
    return $this->getLignes();
  }


  /**
   * Récupération d'un timbre
   * @param int $timbre_id, clé du timbre 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne  
   */
  public function getEnchere($enchere_id)
  {
    $this->sql = "
    SELECT timbre_id, timbre_lot, timbre_titre, timbre_description, timbre_condition,
    timbre_date, timbre_prix, timbre_pays, enchere_debut, enchere_fin, image_url, timbre.Enchere_enchere_id  
    FROM timbre
    INNER JOIN enchere ON timbre.Enchere_enchere_id = enchere_id
    INNER JOIN image ON Timbre_timbre_id = timbre_id
    WHERE enchere_id = :enchere_id";

    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération des timbres à l'affiche ou prochainement
   * @param  string $critere
   * @return array tableau des lignes produites par la select   
   */
  public function getMemeCategorie($timbre_pays)
  {

    $this->sql = "
      SELECT timbre_id, timbre_lot, timbre_titre, timbre_description, timbre_condition,
              timbre_date, timbre_prix, timbre_pays, enchere_debut, enchere_fin, image_url, timbre.Enchere_enchere_id 
      FROM timbre
      INNER JOIN enchere ON timbre.Enchere_enchere_id = enchere_id
      INNER JOIN image ON timbre_id = Timbre_timbre_id
      WHERE timbre_pays = :timbre_pays";
    return $this->getLignes(['timbre_pays' => $timbre_pays]);
  }


  /**
   * Récupération des timbres à l'affiche ou prochainement
   * @param  string $critere
   * @return array tableau des lignes produites par la select   
   */
  public function getVedette()
  {

    $this->sql = "
      SELECT timbre_id, timbre_lot, timbre_titre, timbre_description, timbre_condition,
              timbre_date, timbre_prix, timbre_pays, enchere_debut, enchere_fin, image_url, timbre.Enchere_enchere_id 
      FROM timbre
      INNER JOIN enchere ON timbre.Enchere_enchere_id = enchere_id
      INNER JOIN image ON timbre_id = Timbre_timbre_id
      WHERE timbre_prix  > 20";
    return $this->getLignes();
  }

  /**
   * Ajouter un enchere
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterEnchere($champs)
  {
    $this->sql = '
      INSERT INTO enchere SET enchere_debut = :enchere_debut, enchere_fin = :enchere_fin, Utilisateur_utilisateur_id = :Utilisateur_utilisateur_id';
    return $this->CUDLigne($champs);
  }


  /**
   * Ajouter un timbre
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterTimbre($champs)
  {
    $this->sql = '
      INSERT INTO timbre SET timbre_lot = :timbre_lot, timbre_titre = :timbre_titre, timbre_description = :timbre_description, timbre_condition = :timbre_condition, timbre_date = :timbre_date, timbre_prix = :timbre_prix, timbre_pays = :timbre_pays, Enchere_enchere_id = :Enchere_enchere_id';
    return $this->CUDLigne($champs);
  }

  /**
   * Ajouter une image
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterImage($champs)
  {
    $this->sql = '
      INSERT INTO image SET image_url = :image_url, Timbre_timbre_id = :Timbre_timbre_id, Enchere_enchere_id = :Enchere_enchere_id';
    return $this->CUDLigne($champs);
  }


  /**
   * Ajouter une mise
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterMise($champs)
  {
    $this->sql = '
      INSERT INTO mise SET mise_date = :mise_date, mise_montant = :mise_montant, Utilisateur_utilisateur_id = :Utilisateur_utilisateur_id, Enchere_enchere_id = :Enchere_enchere_id';
    return $this->CUDLigne($champs);
  }


  /**
   * Récupération d'une mise par enchere_id
   * @param int $enchere_id, clé du timbre 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne  
   */
  public function getMiseEnchere($enchere_id)
  {
    $this->sql = "
    SELECT mise_montant, mise_date, utilisateur_prenom, utilisateur_nom, Enchere_enchere_id
    FROM mise
    INNER JOIN utilisateur ON Utilisateur_utilisateur_id = utilisateur_id
    WHERE Enchere_enchere_id = :enchere_id
    ORDER BY mise_montant DESC";
    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération du count des mises
   * @param int $timbre_id, clé du timbre 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne  
   */
  public function getMiseEnchereCount($enchere_id)
  {
    $this->sql = "
    SELECT COUNT(mise_montant) AS totalMise
    FROM mise
    WHERE Enchere_enchere_id = :enchere_id";
    return $this->getLignes(['enchere_id' => $enchere_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Récupération des images à l'affiche ou prochainement
   * @param  string $critere
   * @return array tableau des lignes produites par la select   
   */
  public function getImage()
  {
    $this->sql = "
      SELECT image_id, timbre_lot, timbre_titre, timbre_description, timbre_condition,
              timbre_date, timbre_prix, timbre_pays, enchere_debut, enchere_fin
      FROM timbre
      INNER JOIN enchere ON Enchere_enchere_id = enchere_id";
    return $this->getLignes();
  }


  /**
   * Récupération d'un timbre
   * @param int $timbre_id, clé du timbre 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne  
   */
  public function getEnchereEnCoursParUserId($utilisateur_id)
  {
    $this->sql = "
    SELECT timbre_id, timbre_lot, timbre_titre, timbre_description, timbre_condition,
    timbre_date, timbre_prix, timbre_pays, enchere_debut, enchere_fin, image_url, timbre.Enchere_enchere_id 
    FROM timbre
    INNER JOIN enchere ON timbre.Enchere_enchere_id = enchere_id
    INNER JOIN image ON Timbre_timbre_id = timbre_id
    WHERE Utilisateur_utilisateur_id = :utilisateur_id";

    return $this->getLignes(['utilisateur_id' => $utilisateur_id]);
  }

  // $oAujourdhui = ENV === "DEV" ? new DateTime(MOCK_NOW) : new DateTime();
  // $aujourdhui  = $oAujourdhui->format('Y-m-d');


  // WHERE seance_date >='$aujourdhui' AND seance_date <= '$dernierJour')";



  /**
   * Récupération d'une mise par enchere_id
   * @param int $enchere_id, clé du timbre 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne  
   */
  public function getMiseParUserId($utilisateur_id)
  {
    $this->sql = "
    SELECT enchere_id, timbre_id, timbre_lot, timbre_titre, timbre_date, timbre_prix, enchere_debut, enchere_fin, mise_montant, mise_date, mise_id, mise.Utilisateur_utilisateur_id
    FROM enchere
    INNER JOIN timbre ON timbre.Enchere_enchere_id = enchere.enchere_id
    INNER JOIN mise ON mise.Enchere_enchere_id = enchere.enchere_id
    WHERE mise.Utilisateur_utilisateur_id = :utilisateur_id
    ORDER BY mise_date DESC";
    return $this->getLignes(['utilisateur_id' => $utilisateur_id]);
  }


  /**
   * Supprimer une mise depuis la page membre
   * @param int $utilisateur_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */
  public function supprimerMise($mise_id)
  {
    $this->sql = '
      DELETE FROM mise WHERE mise_id = :mise_id';
    return $this->CUDLigne(['mise_id' => $mise_id]);
  }







  /**
   * Supprimer une image reliée au enchere id
   * @param int $enchere_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */
  public function supprimerImage($enchere_id)
  {
    $this->sql = '
                    DELETE FROM image WHERE Enchere_enchere_id = :enchere_id';
    return $this->CUDLigne(['enchere_id' => $enchere_id]);
  }


  /**
   * Supprimer une mise reliée au enchere id
   * @param int $enchere_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */
  public function supprimerMiseParEnchereId($enchere_id)
  {
    $this->sql = '
            DELETE FROM mise WHERE Enchere_enchere_id = :enchere_id';
    return $this->CUDLigne(['enchere_id' => $enchere_id]);
  }


  /**
   * Supprimer un timbre relié à l'enchère à supprimer
   * @param int $timbre_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */
  public function supprimerTimbre($enchere_id)
  {
    $this->sql = '
                    DELETE FROM timbre WHERE Enchere_enchere_id = :enchere_id';
    return $this->CUDLigne(['enchere_id' => $enchere_id]);
  }

  /**
   * Supprimer une enchere
   * @param int $enchere_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */
  public function supprimerEnchere($enchere_id)
  {
    $this->sql = '
                    DELETE FROM enchere WHERE enchere_id = :enchere_id';
    return $this->CUDLigne(['enchere_id' => $enchere_id]);
  }



  /**
   * Modifier un timbre
   * @param array $champs tableau avec les champs à modifier et la clé enchère_id
   * @return boolean true si modification effectuée, false sinon
   */
  // public function modifierTimbre($champs)
  // {
  //   $this->sql = '
  //     UPDATE timbre SET timbre_lot = :timbre_lot, timbre_titre = :timbre_titre, timbre_description = :timbre_description, timbre_condition = :timbre_condition, timbre_date = :timbre_date, timbre_prix = :timbre_prix, timbre_pays = :timbre_pays, Enchere_enchere_id = :Enchere_enchere_id
  //     WHERE Enchere_enchere_id = :enchere_id';
  //   return $this->CUDLigne($champs);
  // }

  /**
   * modifier une enchere
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function modifierEnchere($champs)
  {
    $this->sql = '
      UPDATE enchere SET enchere_debut = :enchere_debut, enchere_fin = :enchere_fin
      WHERE enchere_id = :Enchere_enchere_id';
    return $this->CUDLigne($champs);
  }


  /**
   * modifier un timbre
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function modifierTimbre($champs)
  {
    $this->sql = '
      UPDATE timbre SET timbre_lot = :timbre_lot, timbre_titre = :timbre_titre, timbre_description = :timbre_description, timbre_condition = :timbre_condition, timbre_date = :timbre_date, timbre_prix = :timbre_prix, timbre_pays = :timbre_pays
      WHERE Enchere_enchere_id = :Enchere_enchere_id';
    return $this->CUDLigne($champs);
  }

  /**
   * modifier une image
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function modifierImage($champs)
  {
    $this->sql = '
      UPDATE image SET image_url = :image_url
      WHERE Enchere_enchere_id = :Enchere_enchere_id';
    return $this->CUDLigne($champs);
  }


  /**
   * rechercher une enchère
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function rechercherEnchere($inputRechercher)
  {
    $this->sql = " SELECT *
                  FROM timbre
                  INNER JOIN image ON Timbre_timbre_id = timbre_id
                  INNER JOIN enchere ON timbre.Enchere_enchere_id = enchere_id
                  WHERE timbre_titre LIKE '%$inputRechercher%'
                  OR timbre_description LIKE '%$inputRechercher%'
                  ";
    $sPDO = SingletonPDO::getInstance();
    $oPDOStatement = $sPDO->prepare($this->sql);
    $oPDOStatement->execute();
    $result = $oPDOStatement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  /**
   * filtrer une enchère
   * @param array $champs tableau des champs de l'enchere 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function filtrerEnchere($resultat)
  {
    $this->sql = " SELECT *
    FROM timbre
    INNER JOIN image ON Timbre_timbre_id = timbre_id
    INNER JOIN enchere ON timbre.Enchere_enchere_id = enchere_id
    WHERE timbre_pays LIKE '%$resultat%' OR timbre_condition LIKE '%$resultat%'";
    $sPDO = SingletonPDO::getInstance();
    $oPDOStatement = $sPDO->prepare($this->sql);
    $oPDOStatement->execute();
    $result = $oPDOStatement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  public function appliquerTri($resultatTri)
  {
    $this->sql = " SELECT *
                  FROM timbre 
                  INNER JOIN enchere ON timbre.Enchere_enchere_id = enchere_id
                  INNER JOIN image ON Timbre_timbre_id = timbre_id ";
    switch ($resultatTri) {
      case 'inputPrixASC':
        $this->sql .= " ORDER by timbre_prix ASC";
        break;
      case 'inputPrixDESC':
        $this->sql .= " ORDER by timbre_prix DESC";
        break;
    }

    $sPDO = SingletonPDO::getInstance();
    $oPDOStatement = $sPDO->prepare($this->sql);
    $oPDOStatement->execute();
    $result = $oPDOStatement->fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }





  /* GESTION DES UTILISATEURS 
     =================== */

  /**
   * Récupération de tous les utilisateurs de la table utilisateur
   * @return array tableau des lignes produites par la select
   */
  public function getUtilisateurs()
  {
    $this->sql = '
      SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, Role_role_id, utilisateur_courriel, utilisateur_mdp FROM utilisateur
      ORDER BY utilisateur_id ASC';
    return $this->getLignes();
  }

  /**
   * Récupération d'un utilisateur de la table utilisateur
   * @param int $utilisateur_id 
   * @return array|false tableau associatif de la ligne produite par la select, false si aucune ligne
   */
  public function getUtilisateur($utilisateur_id)
  {
    $this->sql = '
      SELECT utilisateur_id, utilisateur_nom, utilisateur_prenom, utilisateur_courriel, Role_role_id, utilisateur_mdp FROM utilisateur WHERE utilisateur_id = :utilisateur_id';
    return $this->getLignes(['utilisateur_id' => $utilisateur_id], RequetesPDO::UNE_SEULE_LIGNE);
  }

  /**
   * Ajouter un utilisateur
   * @param array $champs tableau des champs de l'utilisateur 
   * @return string|boolean clé primaire de la ligne ajoutée, false sinon
   */
  public function ajouterUtilisateur($champs)
  {
    $this->sql = '
      INSERT INTO utilisateur SET utilisateur_prenom = :utilisateur_prenom, utilisateur_nom = :utilisateur_nom, utilisateur_courriel = :utilisateur_courriel, utilisateur_mdp = SHA2(:utilisateur_mdp, 512), Role_role_id = :Role_role_id';
    return $this->CUDLigne($champs);
  }
  // utilisateur_mdp = SHA2(:utilisateur_mdp, 512)';
  /**
   * Modifier un utilisateur
   * @param array $champs tableau avec les champs à modifier et la clé utilisateur_id
   * @return boolean true si modification effectuée, false sinon
   */
  public function modifierUtilisateur($champs)
  {
    $this->sql = 'UPDATE utilisateur SET utilisateur_prenom = :utilisateur_prenom, utilisateur_nom = :utilisateur_nom, utilisateur_courriel = :utilisateur_courriel, utilisateur_mdp = SHA2(:utilisateur_mdp, 512), Role_role_id = :Role_role_id
      WHERE utilisateur_id = :utilisateur_id';
    return $this->CUDLigne($champs);
  }

  /**
   * Supprimer un utilisateur
   * @param int $utilisateur_id clé primaire
   * @return boolean true si suppression effectuée, false sinon
   */
  public function supprimerUtilisateur($utilisateur_id)
  {
    $this->sql = '
      DELETE FROM utilisateur WHERE utilisateur_id = :utilisateur_id'; // pour éviter une exception PDO s'il existe des timbres rattachés à cet utilisateur
    return $this->CUDLigne(['utilisateur_id' => $utilisateur_id]);
  }

  /**
   * Generer un nouveau mot de passe pour l'utilisateur
   * @param int $utilisateur_id clé primaire
   * @return boolean true si nouveau mot de passe généré effectué, false sinon
   */
  public function genererUnNouveauMdp($utilisateur_id, $mdp)
  {
    $this->sql = "
      UPDATE utilisateur
      SET utilisateur_mdp = SHA2('$mdp', 512)
      WHERE utilisateur_id = :utilisateur_id";
    return $this->CUDLigne(['utilisateur_id' => $utilisateur_id]);
  }
}
