# 🪪 Identité numérique

Ce projet à but pédagogique est développé pour le *Master de méthodes informatiques appliquées à la gestion des entreprises* par Brahim LAMJARAD, Emre ERSOY, Ons HAMDI, Abdenour ACHOURI, Imane EL MOUNTASSER, Yanis ALLOUCHE, Robin CLAIR et Yannis BAILI.

Il utilise le framework **[Symfony](https://symfony.com/) PHP** pour toute l'infrastructure serveur, l'architecture client utilise les langages **JavaScript**, **CSS** (via préprocesseur **SASS**) et **HTML** (via génération du moteur de modèles **TWIG**).

## Fonctionnalités prévues du front-office

- <ins>Espace d'authentification</ins> : selon le rôle de l'utilisateur, ce dernier est redirigé vers son espace dédié à son rôle (étudiant ou enseignant).
- <ins>Créer un cours</ins> : l'enseignant créé un cours en sélectionnant la formation, la matière, le groupe de TD/TP et le type de cours.
- <ins>Génération d'un QR Code</ins> : génération du QR Code lorsqu'un enseignant appuie sur « Générer le QR Code ».
- <ins>Création d'un UUID</ins> (*Identifiant Unique Universel*) : lorsque l'enseignant créé un cours, ce dernier est inséré dans l'URL du QR Code généré pour des fins de contrôles par l'équipe pédagogique ou par l'administration universitaire.
- <ins>Consulter la liste des étudiants présents/absents dans le cours actuel</ins> : la liste des personnes présentes/absentes affiche les étudiants ayant validé ou non leur présence au cours.
- <ins>Clôturer l'appel</ins> : le QR Code est automatiquement révoqué lorsque l'enseignant met fin au cours, rendant impossible aux étudiants la possibilité de se mettre présent une fois le cours terminé.
- <ins>Scanner le QR Code</ins> : lorsqu'un étudiant scanne le QR Code, il est redirigé vers son espace utilisateur avec un message de confirmation.
- <ins>Télécharger l'attestation du présence</ins> : l'étudiant est capable de télécharger un certificat de présence avec les informations du cours. Un jeton de validation est généré dans l'attestation de présence.*
- <ins>Consulter mes présences et absences</ins> : un étudiant est capable de télécharger les certificats de présences des anciens cours mais aussi de savoir les absences enregistrées aux cours.
- <ins>Justifier une absence</ins> : il est possible de téléverser un justificatif pour une absence et de l'enregistrer dans le système de fichiers du serveur.

> *Cette partie est davantage détaillée dans le dernier point de ce fichier.

## Fonctionnalités prévues du back-office

- <ins>Gérer les utilisateurs</ins>	: créer, modifier ou supprimer un utilisateur.
- <ins>Gérer les formations</ins> : créer, modifier ou supprimer une formation.
- <ins>Gérer les matières</ins> : créer, modifier ou supprimer une matière.
- <ins>Gérer les absences</ins> : supprimer une absence, changer le statut d'une absence en la rendant justifiée ou non.
- <ins>Gérer les présences</ins> : ajouter un étudiant dans la table des présences.
- <ins>Clôturer l'appel</ins> : clôturer une séance si le l'enseignant a oublié de le faire précédemment.

## Mécanisme de sécurisation pour les certifications de présence des étudiants

1. Un enseignant fait l'appel et génère un QR code, un premier UUID (*Identifiant Unique Universel*, https://fr.wikipedia.org/wiki/Universally_unique_identifier) est généré seulement à des fins de lisibilité pour les utilisateurs finaux afin d'identifier de manière unique le cours créé, il est ensuite inséré dans la base de données « en clair » (**sans aucun chiffrement**).
2. Un étudiant quelconque scanne ce même QR code avec son téléphone (l'URL du QR code contient l'UUID précédemment créé), il se fait rediriger vers son espace utilisateur pour se faire marquer présent, un deuxième UUID est généré pour identifier de manière unique la présence d'un étudiant vis-à-vis de ce cours. Une fois créé, cet identifiant est chiffré par une clé publique*, envoyé au client pour la troisième étape et il est enfin enregistré dans la base de données tel quel (**sans aucun chiffrement**), cet UUID chiffré est alors considéré comme un « jeton ».
3. Une fois signalé comme présent au cours, l'étudiant est redirigé vers l'accueil de son compte et dispose d'un moyen pour télécharger son certificat de présence (**sous format PDF**), ce certificat fournit des renseignements sur le cours et sur l'étudiant. Un QR code est également présent à l'intérieur de ce document à des fins de contrôles pour l'équipe pédagogique ou par l'administration (**l'URL de QR code contient le jeton numérique créé à l'étape précédente**).
4. Si le QR code de contrôle est scanné, le jeton est envoyé au serveur et procède à un déchiffrement avec la clé privée précédemment créé, si le jeton retourne un résultat et si le jeton correspond à l'identifiant de présence créé dans l'étape 2, alors cela signifie que l'étudiant était réellement présent au cours (**les informations du cours et de l'étudiants sont alors envoyées**).

> Avant d'effectuer toute opération et si cela est nécessaire, une paire de clé (publique et privée) sont générées automatique avec la méthode de hachage « SHA-512 » et ayant une longueur de 4096 bits.