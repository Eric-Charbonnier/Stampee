DROP TABLE IF EXISTS utilisateur;

--
-- Structure de la table utilisateur
--

CREATE TABLE utilisateur (
  utilisateur_id       int UNSIGNED NOT NULL AUTO_INCREMENT,
  utilisateur_nom      varchar(255) NOT NULL,
  utilisateur_prenom   varchar(255) NOT NULL,
  utilisateur_courriel varchar(255) NOT NULL UNIQUE,
  utilisateur_mdp      varchar(255) NOT NULL,
  utilisateur_profil   varchar(255) NOT NULL,
  PRIMARY KEY (utilisateur_id)
) ENGINE=InnoDB  DEFAULT CHARSET=UTF8;

INSERT INTO utilisateur VALUES
(1, "Jouhannet", "Charles", "cjouhannet@cmaisonneuve.qc.ca", SHA2("a1b2c3d4e5", 512), "administrateur"),
(2, "Tremblay",  "Jean",    "jean.tremblay@site1.ca",        SHA2("f1g2h3i4j5", 512), "editeur"),
(3, "Legrand",   "Jacques", "jacques.legrand@site2.ca",      SHA2("k1l2m3n4o5", 512), "utilisateur");

INSERT INTO Role VALUES
(1, "administrateur"),
(2, "membre"),
(3, "visiteur");


INSERT INTO Membre VALUES
(1, "bobby", "111aaa", "Marley", "Bob", "2006-12-30", "25 rue des oiseaux", "bob@marley.com", "1");

INSERT INTO Livraison VALUES
(1, "standard"),
(2, "express");

INSERT INTO Paiement VALUES
(1, "visa"),
(2, "paypal"),
(3, "cheque");

INSERT INTO Vendeur VALUES
(1, "Julien", "Leguern", "julien@leguern.com", "juju", "2525 rue des poissons");

INSERT INTO Timbre VALUES
(1, "Bluenose", "Ce timbre canadien est dans le domaine public parce qu'il a plus de 50 ans. Bluenose avec un bon centrage et une belle gomme sans charnière, certifié par l'AIEP", "bonne", "1925-10-10", "livret", "1060", "6x4", "2022-12-01", "2022-12-07", "1");


INSERT INTO Timbre VALUES
(2, "123", "blacknose", "à venir", "bonne", "1946-10-10", "32", "Espagne", 1),
(3, "456", "whitenose", "à venir", "neuf", "1999-09-10", "10", "France", 1),
(4, "789", "pinknose", "à venir", "usage", "1984-03-25", "99", "Italie", 1);


INSERT INTO Mise VALUES
(2, "123", "blacknose", "à venir", "bonne", "1946-11", "10", "France", 1);


INSERT INTO image VALUES
(5, "assets/img/upload/thumbnail-enchere-3.jpg", 5, 57),
(6, "assets/img/upload/timbre1.jpg", 6, 58),
(7, "assets/img/upload/china-1894-9c.jpg", 7, 59),
(8, "assets/img/upload/astra.jpg", 8, 60),
(9, "assets/img/upload/timbre3.jpg", 10, 62),
(10, "assets/img/upload/timbre5.jpg", 11, 63),
(11, "assets/img/upload/04.jpg",12, 64),
(12, "assets/img/upload/08.jpg", 13, 65),
(13, "assets/img/upload/013.jpg", 14, 66),
(14, "assets/img/upload/07", 15, 67);




