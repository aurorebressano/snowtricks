# Snowtricks

Prérequis

    PHP 8.1
    Symfony CLI

Installation et configuration

    Télécharger ou cloner le repository (en ligne de commande: 'git clone https://github.com/aurorebressano/snowtricks.git')
    Dupliquer et renommer le fichier .env en .env.local et modifier les infos nécessaires (notamment APP_ENV, APP_SECRET, DATABASE_URL)
    En ligne de commande, jouer:
    ->'symfony composer install --optimize-autoloader' (Installer les dépendances nécessaires à l'exécution de l'application)
    ->'symfony console doctrine:migrations:migrate --no-interaction'
    ->'symfony console doctrine:fixtures:load --no-interaction'
    ->'symfony server:start' (Mettre en route le serveur local)
    ->'symfony open:local' (ouvrir le site dans le navigateur)

    
Identifiants de connexion à des fins de test

Utilisateur admin:
    Login: 'admin@admin.fr'
    Password: 'test'
Utilisateur lambda:
    Login: 'test@gmail.com'
    Password: 'test'
    
