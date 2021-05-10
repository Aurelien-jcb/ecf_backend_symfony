# Exercice ECF

Ce projet est réalisé en PHP avec le framework Symfony

## Installation : 
Pour lancer le projet il faudra : 
    - installer Symfony & Composer
    - cloner le repository
    - lancer les commandes suivantes afin d'installer les dépendances nécessaire
         - composer install 
         - npm install
    - urls d'accès (exemples)
        -  https://localhost:8000/ 
        - connexion https://localhost:8000/login
        - promotions https://localhost:8000/school-year
        - projets https://localhost:8000/project
        - utilisateurs https://localhost:8000/user

## Cahier des charges
- Création de la base de donnée
    Utilisation d'un install script (/mkdb.sh)

- Création de la structure de la base de donnée
    - php bin/console make:entity
        $ Nom de la table (ex: Projet)
        $ Ajouter un champs (ex: name)
        $ type de champs (default String)
        $ Longueur de champs (max pour un string: 190)
        $ Optionnel (ex: no)

- Création des realtions entre les tables
    $ php bin/console make:entity [nom] (ex: User)
        $ propriété ? (ex: projects)
        $ Type ? (ex: relation, ManyToMany,ManyToOne etc)
        $ Relation avec quelle entité ? (ex: Project)
        $ Type de relation si pas indiqué précédement (ex: ManyToMany,ManyToOne etc)
        $ Bidirectionnalité ? (si marche dans les 2 sens oui)
        $ Nom du nouveau champs (ex: users)

- Structure de la base de donnée
    - user
        - id
        - email, 
        - roles, 
        - password,
        - firstname,
        - lastname,
        - phone,
        - school_year_id : clé étrangère qui pointe vers school_year.id

    - school_year
        - id
        - name,
        - date_start,
        - date_end

    - project
        - id,
        - name,
        - description

    - project_user
        - project_id : clé étrangère qui pointe vers project.id
        - user_id : clé étrangère qui pointe vers user.id