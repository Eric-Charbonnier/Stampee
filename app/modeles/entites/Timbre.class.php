<?php

/**
 * Classe de l'entité Timbre
 *
 */
class Timbre
{
  private $timbre_id;
  private $timbre_titre;
  private $timbre_description;
  private $timbre_condition;
  private $timbre_date;
  private $timbre_type;
  private $timbre_prix;
  private $timbre_dimension;
  private $enchere_debut;
  private $enchere_fin;


  private $erreurs = [];

  const ANNEE_PREMIER_TIMBRE = 1895;
  const DUREE_MIN = 1;
  const DUREE_MAX = 600;
  const STATUT_INVISIBLE = 0;
  const STATUT_VISIBLE   = 1; // en cours
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
   * Mutateur de la propriété timbre_id 
   * @param int $timbre_id
   * @return $this
   */
  public function setTimbre_id($timbre_id)
  {
    unset($this->erreurs['timbre_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_id)) {
      $this->erreurs['timbre_id'] = 'Numéro de timbre incorrect.';
    }
    $this->timbre_id = $timbre_id;
    return $this;
  }

  /**
   * Mutateur de la propriété timbre_titre 
   * @param string $timbre_titre
   * @return $this
   */
  public function setTimbre_titre($timbre_titre)
  {
    unset($this->erreurs['timbre_titre']);
    $timbre_titre = trim($timbre_titre);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $timbre_titre)) {
      $this->erreurs['timbre_titre'] = 'Au moins un caractère.';
    }
    $this->timbre_titre = mb_strtoupper($timbre_titre);
    return $this;
  }
  
    /**
   * Mutateur de la propriété timbre_condition 
   * @param string $timbre_condition
   * @return $this
   */
  public function setTimbre_condition($timbre_condition)
  {
    unset($this->erreurs['timbre_condition']);
    $timbre_condition = trim($timbre_condition);
    $regExp = '/^.+$/';
    if (!preg_match($regExp, $timbre_condition)) {
      $this->erreurs['timbre_condition'] = 'Au moins un caractère.';
    }
    $this->timbre_condition = mb_strtoupper($timbre_condition);
    return $this;
  }

    /**
   * Mutateur de la propriété timbre_description
   * @param string $timbre_description
   * @return $this
   */
  public function setTimbre_description($timbre_description)
  {
    unset($this->erreurs['timbre_description']);
    $timbre_description = trim($timbre_description);
    $regExp = '/^\S+(\s+\S+){4,}$/';
    if (!preg_match($regExp, $timbre_description)) {
      $this->erreurs['timbre_description'] = 'Au moins 5 mots.';
    }
    $this->timbre_description = $timbre_description;
    return $this;
  }

    /**
   * Mutateur de la propriété timbre_enchere_debut 
   * @param int $timbre_enchere_debut
   * @return $this
   */
  public function setTimbre_enchere_debut($timbre_enchere_debut)
  {
    unset($this->erreurs['timbre_enchere_debut']);
    if (
      !preg_match('/^\d+$/', $timbre_enchere_debut) ||
      $timbre_enchere_debut < self::ANNEE_PREMIER_TIMBRE  ||
      $timbre_enchere_debut > date("Y")
    ) {
      $this->erreurs['timbre_enchere_debut'] = "Entre " . self::ANNEE_PREMIER_TIMBRE . " et l'année en cours.";
    }
    $this->timbre_enchere_debut = $timbre_enchere_debut;
    return $this;
  }

  /**
   * Mutateur de la propriété timbre_enchere_fin 
   * @param int $timbre_enchere_fin, en minutes
   * @return $this
   */
  public function setTimbre_enchere_fin($timbre_enchere_fin)
  {
    unset($this->erreurs['timbre_enchere_fin']);
    if (!preg_match('/^[1-9]\d*$/', $timbre_enchere_fin) || $timbre_enchere_fin < self::DUREE_MIN || $timbre_enchere_fin > self::DUREE_MAX) {
      $this->erreurs['timbre_enchere_fin'] = "Entre " . self::DUREE_MIN . " et " . self::DUREE_MAX . ".";
    }
    $this->timbre_enchere_fin = $timbre_enchere_fin;
    return $this;
  }





  /**
   * Mutateur de la propriété timbre_image
   * @param string $timbre_image
   * @return $this
   */
  public function setTimbre_image($timbre_image)
  {
    unset($this->erreurs['timbre_image']);
    $timbre_image = trim($timbre_image);
    $regExp = '/^.+\.jpg$/';
    if (!preg_match($regExp, $timbre_image)) {
      $this->erreurs['timbre_image'] = "Vous devez téléverser un fichier de type jpg.";
    }
    $this->timbre_image = $timbre_image;
    return $this;
  }


  /**
   * Mutateur de la propriété timbre_statut
   * @param int $timbre_statut
   * @return $this
   */
  public function setTimbre_statut($timbre_statut)
  {
    unset($this->erreurs['timbre_statut']);
    if (
      $timbre_statut != Timbre::STATUT_INVISIBLE &&
      $timbre_statut != Timbre::STATUT_VISIBLE   &&
      $timbre_statut != Timbre::STATUT_ARCHIVE
    ) {
      $this->erreurs['timbre_statut'] = 'Statut incorrect.';
    }
    $this->timbre_statut = $timbre_statut;
    return $this;
  }

  /**
   * Mutateur de la propriété timbre_genre_id 
   * @param int $timbre_genre_id
   * @return $this
   */
  public function setTimbre_genre_id($timbre_genre_id)
  {
    unset($this->erreurs['timbre_genre_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $timbre_genre_id)) {
      $this->erreurs['timbre_genre_id'] = 'Numéro de genre incorrect.';
    }
    $this->timbre_genre_id = $timbre_genre_id;
    return $this;
  }
}
