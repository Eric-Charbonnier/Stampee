<?php

/**
 * Classe de l'entité Utilisateur
 *
 */
class Utilisateur
{
  private $utilisateur_id;
  private $utilisateur_prenom;
  private $utilisateur_nom;
  private $utilisateur_courriel;
  private $utilisateur_mdp;
  private $Role_role_id;


  const ROLE_UTILISATEUR = "utilisateur";
  const ROLE_ADMINISTRATEUR = "administrateur";
  const ROLE_VENDEUR = "vendeur";

  private $erreurs = array();

  /**
   * Constructeur de la classe
   * @param array $proprietes, tableau associatif des propriétés 
   *
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

  // Getters explicites nécessaires au moteur de templates TWIG
  public function getUtilisateur_id()
  {
    return $this->utilisateur_id;
  }
  public function getUtilisateur_nom()
  {
    return $this->utilisateur_nom;
  }
  public function getUtilisateur_prenom()
  {
    return $this->utilisateur_prenom;
  }
  public function getUtilisateur_courriel()
  {
    return $this->utilisateur_courriel;
  }
  public function getUtilisateur_mdp()
  {
    return $this->utilisateur_mdp;
  }
  public function getRole_role_id()
  {
    return $this->Role_role_id;
  }
  public function getErreurs()
  {
    return $this->erreurs;
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
   * Mutateur de la propriété utilisateur_id 
   * @param int $utilisateur_id
   * @return $this
   */
  public function setUtilisateur_id($utilisateur_id)
  {
    unset($this->erreurs['utilisateur_id']);
    $regExp = '/^[1-9]\d*$/';
    if (!preg_match($regExp, $utilisateur_id)) {
      $this->erreurs['utilisateur_id'] = "Numéro d'utilisateur incorrect.";
    }
    $this->utilisateur_id = $utilisateur_id;
    return $this;
  }

  /**
   * Mutateur de la propriété utilisateur_prenom 
   * @param string $utilisateur_prenom
   * @return $this
   */
  public function setUtilisateur_prenom($utilisateur_prenom)
  {
    unset($this->erreurs['utilisateur_prenom']);
    $utilisateur_prenom = trim($utilisateur_prenom);
    // $regExp = '/^[a-zÀ-ÖØ-öø-ÿ]{2,}( [a-zÀ-ÖØ-öø-ÿ]{2,})*$/i';
    $regExp = '/^\p{L}{2,}( \p{L}{2,})*$/ui'; // regexp équivalente à la précédente
    if (!preg_match($regExp, $utilisateur_prenom)) {
      $this->erreurs['utilisateur_prenom'] = "Au moins 2 caractères alphabétiques.";
    }
    $this->utilisateur_prenom = $utilisateur_prenom;
    return $this;
  }

  /**
   * Mutateur de la propriété utilisateur_nom 
   * @param string $utilisateur_nom
   * @return $this
   */
  public function setUtilisateur_nom($utilisateur_nom)
  {
    unset($this->erreurs['utilisateur_nom']);
    $utilisateur_nom = trim($utilisateur_nom);
    $regExp = '/^[a-zÀ-ÖØ-öø-ÿ]{2,}( [a-zÀ-ÖØ-öø-ÿ]{2,})*$/i';
    // $regExp = '/^\p{L}{2,}( \p{L}{2,})*$/ui'; // regexp équivalente à la précédente
    if (!preg_match($regExp, $utilisateur_nom)) {
      $this->erreurs['utilisateur_nom'] = "Au moins 2 caractères alphabétiques.";
    }
    $this->utilisateur_nom = $utilisateur_nom;
    return $this;
  }


  /**
   * Mutateur de la propriété utilisateur_courriel
   * @param string $utilisateur_courriel
   * @return $this
   */
  public function setUtilisateur_courriel($utilisateur_courriel)
  {
    unset($this->erreurs['utilisateur_courriel']);
    $utilisateur_courriel = trim($utilisateur_courriel);

    if (!filter_var($utilisateur_courriel, FILTER_VALIDATE_EMAIL)) {
      $this->erreurs['utilisateur_courriel'] = "Adresse courriel invalide";
    }
    $this->utilisateur_courriel = $utilisateur_courriel;
    return $this;
  }

  /**
   * Mutateur de la propriété Role_role_id
   * @param string $Role_role_id
   * @return $this
   */
  public function setRole_role_id($Role_role_id)
  {
    $this->Role_role_id = $Role_role_id;
  }


  /**
   * Mutateur de la propriété Role_role_nom
   * @param string $Role_role_id
   * @return $this
   */
  public function setRole_nom($role_nom)
  {
    $this->role_nom = $role_nom;
  }


  /**
   * Mutateur de la propriété utilisateur_mdp
   * @param string $utilisateur__mdp
   * @return $this
   */
  public function setUtilisateur_mdp($utilisateur_mdp)
  {
    unset($this->erreurs['utilisateur_mdp']);
    $utilisateur_mdp = trim($utilisateur_mdp);
    if ($utilisateur_mdp == "" || strlen($utilisateur_mdp < 5)) {
      $this->erreurs['utilisateur_mdp'] = "Le mot de passe doit contenir au moins 5 caractères.";
    }
    $this->utilisateur_mdp = $utilisateur_mdp;
  }

  /**
   * Mutateur de la propriété utilisateur_mdp
   * @param string $utilisateur__mdp
   * @return $this
   */
  // public function setUtilisateur_mdp($utilisateur_mdp)
  // {
  //   unset($this->erreurs['utilisateur_mdp']);
  //   $utilisateur_mdp = trim($utilisateur_mdp);
  //   $regExp = '^\S*(?=\S{6,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$';
  //   if (!preg_match($regExp, $utilisateur_mdp)) {
  //     $this->erreurs['utilisateur_mdp'] = "Le mot de passe doit contenir au moins 4 caractères, majuscule, minuscule et chiffre.";
  //   }
  //   $this->utilisateur_mdp = $utilisateur_mdp;
  // }
  //   ^: anchored to beginning of string
  // \S*: any set of characters
  // (?=\S{8,}): of at least length 4
  // (?=\S*[a-z]): containing at least one lowercase letter
  // (?=\S*[A-Z]): and at least one uppercase letter
  // (?=\S*[\d]): and at least one number
  // $: anchored to the end of the string
  // To include special characters, just add (?=\S*[\W]), which is non-word characters.

  /**
   * Génération d'un mot de passe aléatoire dans la propriété utilisateur_mdp
   * @return $this
   */
  public function genererMdp()
  {
    $char = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $mdp = substr(str_shuffle($char), 0, 10);
    $this->utilisateur_mdp = $mdp;
    return $this;
  }
}
