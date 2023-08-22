<?php

/**
 * Classe de l'entité Enchere
 *
 */
class Enchere
{
  private $enchere_id;
  private $enchere_debut;
  private $enchere_fin;



  private $erreurs = [];

  const ANNEE_PREMIER_ENCHERE = 1895;
  const DUREE_MIN = 1;
  const DUREE_MAX = 600;
  const STATUT_INVISIBLE = 0;
  const STATUT_VISIBLE   = 1;
  const STATUT_ARCHIVE   = 2;

  /**
   * Constructeur de la classe 
   * @param array $proprietes, tableau associatif des propriétés 
   */
  public function __construct($proprietes = [])
  {
    $t = array_keys($proprietes);
    foreach ($t as $nom_propriete) {
      $this->__set($nom_propriete, $proprietes[$nom_propriete]);
    }
  }

  /**
   * Accesseur magique d'une propriété de l'objet
   * @param string $prop, nom de la propriété
   * @return property value
   */
  public function __get($prop)
  {
    return $this->$prop;
  }

  /**
   * Mutateur magique qui exécute le mutateur de la propriété en paramètre 
   * @param string $prop, nom de la propriété
   * @param $val, contenu de la propriété à mettre à jour
   */
  public function __set($prop, $val)
  {
    $setProperty = 'set' . ucfirst($prop);
    $this->$setProperty($val);
  }

  /**
   * Mutateur de la propriété enchere_id 
   * @param int $enchere_id
   * @return $this
   */
  public function setEnchere_id($enchere_id)
  {
    unset($this->erreurs['enchere_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $enchere_id)) {
      $this->erreurs['enchere_id'] = 'Numéro enchere incorrect.';
    }
    $this->enchere_id = $enchere_id;
    return $this;
  }

  /**
   * Mutateur de la propriété enchere_titre 
   * @param string $enchere_titre
   * @return $this
   */
  public function setEnchere_titre($enchere_titre)
  {
    unset($this->erreurs['enchere_titre']);
    $enchere_titre = trim($enchere_titre);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $enchere_titre)) {
      $this->erreurs['enchere_titre'] = 'Au moins un caractère.';
    }
    $this->enchere_titre = mb_strtoupper($enchere_titre);
    return $this;
  }
  
    /**
   * Mutateur de la propriété enchere_condition 
   * @param string $enchere_condition
   * @return $this
   */
  public function setEnchere_condition($enchere_condition)
  {
    unset($this->erreurs['enchere_condition']);
    $enchere_condition = trim($enchere_condition);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $enchere_condition)) {
      $this->erreurs['enchere_condition'] = 'Au moins un caractère.';
    }
    $this->enchere_condition = mb_strtoupper($enchere_condition);
    return $this;
  }

    /**
   * Mutateur de la propriété enchere_description
   * @param string $enchere_description
   * @return $this
   */
  public function setEnchere_description($enchere_description)
  {
    unset($this->erreurs['enchere_description']);
    $enchere_description = trim($enchere_description);
    $regExp = '/^\S+(\s+\S+){4,}$/';
    if (!preg_match($regExp, $enchere_description)) {
      $this->erreurs['enchere_description'] = 'Au moins 5 mots.';
    }
    $this->enchere_description = $enchere_description;
    return $this;
  }

    /**
   * Mutateur de la propriété enchere_enchere_debut 
   * @param int $enchere_enchere_debut
   * @return $this
   */
  public function setEnchere_enchere_debut($enchere_enchere_debut)
  {
    unset($this->erreurs['enchere_enchere_debut']);
    if (
      !preg_match('/^\d+$/', $enchere_enchere_debut) ||
      $enchere_enchere_debut < self::ANNEE_PREMIER_ENCHERE  ||
      $enchere_enchere_debut > date("Y")
    ) {
      $this->erreurs['enchere_enchere_debut'] = "Entre " . self::ANNEE_PREMIER_ENCHERE . " et l'année en cours.";
    }
    $this->enchere_enchere_debut = $enchere_enchere_debut;
    return $this;
  }

  /**
   * Mutateur de la propriété enchere_enchere_fin 
   * @param int $enchere_enchere_fin, en minutes
   * @return $this
   */
  public function setEnchere_enchere_fin($enchere_enchere_fin)
  {
    unset($this->erreurs['enchere_enchere_fin']);
    if (!preg_match('/^[1-9]\d*$/', $enchere_enchere_fin) || $enchere_enchere_fin < self::DUREE_MIN || $enchere_enchere_fin > self::DUREE_MAX) {
      $this->erreurs['enchere_enchere_fin'] = "Entre " . self::DUREE_MIN . " et " . self::DUREE_MAX . ".";
    }
    $this->enchere_enchere_fin = $enchere_enchere_fin;
    return $this;
  }





  /**
   * Mutateur de la propriété enchere_image
   * @param string $enchere_image
   * @return $this
   */
  public function setEnchere_image($enchere_image)
  {
    unset($this->erreurs['enchere_image']);
    $enchere_image = trim($enchere_image);
    $regExp = '/^.+\.jpg$/';
    if (!preg_match($regExp, $enchere_image)) {
      $this->erreurs['enchere_image'] = "Vous devez téléverser un fichier de type jpg.";
    }
    $this->enchere_image = $enchere_image;
    return $this;
  }


  /**
   * Mutateur de la propriété enchere_statut
   * @param int $enchere_statut
   * @return $this
   */
  public function setEnchere_statut($enchere_statut)
  {
    unset($this->erreurs['enchere_statut']);
    if (
      $enchere_statut != Enchere::STATUT_INVISIBLE &&
      $enchere_statut != Enchere::STATUT_VISIBLE   &&
      $enchere_statut != Enchere::STATUT_ARCHIVE
    ) {
      $this->erreurs['enchere_statut'] = 'Statut incorrect.';
    }
    $this->enchere_statut = $enchere_statut;
    return $this;
  }

  /**
   * Mutateur de la propriété enchere_genre_id 
   * @param int $enchere_genre_id
   * @return $this
   */
  public function setEnchere_genre_id($enchere_genre_id)
  {
    unset($this->erreurs['enchere_genre_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $enchere_genre_id)) {
      $this->erreurs['enchere_genre_id'] = 'Numéro de genre incorrect.';
    }
    $this->enchere_genre_id = $enchere_genre_id;
    return $this;
  }
}
