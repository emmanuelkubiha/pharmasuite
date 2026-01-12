# Configuration STORESUITE sur MAMP (Mac)

## Vue d'ensemble
Guide de configuration de STORESUITE sur MAMP (Mac) après transition depuis Windows/XAMPP.

## Installation MAMP

### 1. Télécharger et installer MAMP
- Site : https://www.mamp.info/en/downloads/
- Téléchargez **MAMP** (pas MAMP PRO sauf si vous avez les fonds)
- Installez dans `/Applications/MAMP/`

### 2. Démarrer MAMP
- Ouvrez l'application MAMP
- Cliquez sur **"Start Servers"**
- Attendez que les lumières deviennent vertes (Apache + MySQL)

## Configuration des chemins

### Chemin racine des projets
- **Chemin par défaut MAMP** : `/Applications/MAMP/htdocs/`
- **URL locale** : `http://localhost:8888/` (port 8888 par défaut)

### Créer le dossier STORESuite
```bash
# Via Terminal
mkdir -p /Applications/MAMP/htdocs/STORESuite
```

### Copier les fichiers du projet
- Copiez tous les fichiers STORESuite dans `/Applications/MAMP/htdocs/STORESuite/`
- Assurez-vous que la structure est identique :
  ```
  /Applications/MAMP/htdocs/STORESuite/
  ├── config/
  ├── ajax/
  ├── assets/
  ├── database/
  ├── uploads/
  ├── login.php
  ├── accueil.php
  └── ... autres fichiers
  ```

## Configuration de la base de données

### 1. Ouvrir phpMyAdmin
- Cliquez sur **"Open WebStart page"** dans MAMP
- Sélectionnez **"phpMyAdmin"**

### 2. Créer la base de données
- Cliquez sur **"New"**
- Créez une base de données nommée : `storesuite`
- Charset : `utf8mb4_unicode_ci`

### 3. Importer le SQL
- Allez dans l'onglet **"Import"**
- Téléversez le fichier : `database/storesuite.sql`
- Cliquez sur **"Go"** pour importer

### 4. Vérifier les utilisateurs MySQL
- Accédez à **User accounts** dans phpMyAdmin
- Vérifiez que l'utilisateur `root` sans mot de passe existe
- Sinon, créez-le avec les permissions sur `storesuite.*`

## Configuration du fichier config.php

### Option 1 : Utiliser la configuration locale MAMP
Créez `config/config.php` avec ces paramètres :

```php
<?php
// ============================================================================
// CONFIGURATION - STORESUITE (LOCAL MAMP)
// ============================================================================

// Base de données
define('DB_HOST', 'localhost');      // Port 3306 par défaut sur MAMP
define('DB_NAME', 'storesuite');
define('DB_USER', 'root');
define('DB_PASS', 'root');           // MAMP utilise 'root' par défaut

// Application
define('BASE_URL', 'http://localhost:8888/STORESuite/');
define('DEVISE', 'USD');

// Sécurité
define('SECRET_KEY', 'F7k9mP2nX#wL4v@Q8rT$y5jB0hGc3fDe1AZ7bM4sJ6pY9w');
define('SESSION_LIFETIME', 7200); // 2 heures

// Mode debug (true pour développement, false pour production)
define('DEBUG_MODE', true);

// Niveaux d'accès
define('NIVEAU_USER', 1);
define('NIVEAU_MANAGER', 2);
define('NIVEAU_ADMIN', 3);

// Fuseau horaire
date_default_timezone_set('Africa/Lubumbashi');

// Session
session_start();
session_set_cookie_params(['lifetime' => SESSION_LIFETIME]);
?>
```

### Option 2 : Copier depuis config.php existant
- Si vous avez déjà un `config.php` du projet Windows
- Modifiez juste les paramètres DB :
  ```php
  define('DB_HOST', 'localhost');
  define('DB_USER', 'root');
  define('DB_PASS', 'root');  // MAMP par défaut
  define('BASE_URL', 'http://localhost:8888/STORESuite/');
  ```

## Configuration des permissions

### Donner les permissions d'écriture
```bash
# Dans Terminal, naviguez jusqu'à STORESuite
cd /Applications/MAMP/htdocs/STORESuite/

# Donner les permissions 755 aux dossiers uploads
chmod -R 755 uploads/
chmod -R 755 uploads/logos/
chmod -R 755 uploads/produits/
chmod -R 755 uploads/utilisateurs/

# Donner les permissions 644 aux fichiers PHP
find . -name "*.php" -type f -exec chmod 644 {} \;
```

## Port Apache sur MAMP

### Vérifier le port
- MAMP par défaut utilise le port **8888**
- URL locale : `http://localhost:8888/STORESuite/`

### Changer le port (optionnel)
- Ouvrez MAMP
- Onglet **"Ports"**
- Modifiez Apache Port (ex: 80 si vous préférez)
- Cliquez sur **"OK"** et redémarrez les serveurs

## Test de connexion

### 1. Vérifier la configuration
- Allez à : `http://localhost:8888/STORESuite/diagnostic.php`
- Vérifiez que la connexion MySQL fonctionne

### 2. Tester la page de login
- Allez à : `http://localhost:8888/STORESuite/login.php`
- Identifiants par défaut : `admin` / `admin`

### 3. Vérifier les logs
- Si erreur, consultez les logs Apache :
  ```bash
  tail -f /Applications/MAMP/logs/apache_error.log
  ```

## Configuration du .htaccess pour MAMP

Le fichier `.htaccess` a les lignes HTTPS commentées pour localhost. Sur MAMP, elles doivent rester commentées :

```apache
# À la ligne ~89-93 du .htaccess :
# <IfModule mod_rewrite.c>
#     RewriteCond %{HTTPS} off
#     RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
# </IfModule>
```

Ceci est correct pour le développement local.

## Synchronisation Git

### Cloner le projet depuis GitHub
```bash
# Naviguez dans le répertoire
cd /Applications/MAMP/htdocs/

# Clonez le repository
git clone https://github.com/VOTRE_USERNAME/STORESuite.git

# Naviguez dedans
cd STORESuite/

# Vérifiez les fichiers
ls -la
```

### Configuration Git sur Mac
```bash
# Configurer Git avec votre identité
git config --global user.name "Votre Nom"
git config --global user.email "votre@email.com"

# Configurer l'authentification SSH (recommandé)
# Voir le guide : GUIDE_GIT_NOUVELLE_MACHINE.md
```

## Problèmes courants et solutions

### Erreur "Cannot connect to MySQL"
**Cause** : Le serveur MySQL de MAMP ne démarre pas
**Solution** :
```bash
# Vérifiez si le port 3306 est utilisé
lsof -i :3306

# Si oui, tuez le processus
kill -9 <PID>

# Redémarrez MAMP
```

### Erreur "Document root is not accessible"
**Cause** : Les permissions sur le dossier sont incorrectes
**Solution** :
```bash
# Donnez les permissions au dossier STORESuite
chmod 755 /Applications/MAMP/htdocs/STORESuite/
```

### Le site se charge lentement
**Cause** : MAMP est configuré avec des paramètres par défaut faibles
**Solution** :
- Augmentez la RAM disponible pour MAMP
- Ouvrez MAMP → Onglet "Server"
- Augmentez les ressources allouées

### Erreur "Session path is not writable"
**Cause** : Le dossier de sessions n'est pas accessible en écriture
**Solution** :
```bash
# Vérifiez le chemin de session par défaut
grep -r "session.save_path" /Applications/MAMP/conf/

# Donnez les permissions
chmod 755 /Applications/MAMP/tmp/php/
```

## Backup de la base de données MAMP

### Exporter la base de données
```bash
# Via phpMyAdmin : Select database → Export → Download SQL file

# Ou via Terminal
mysqldump -u root -proot storesuite > ~/Desktop/storesuite_backup.sql
```

### Importer une sauvegarde
```bash
# Via Terminal
mysql -u root -proot storesuite < ~/Desktop/storesuite_backup.sql
```

## Passages de Windows à Mac

### Fichiers à adapter
- `config/config.php` : Paramètres DB et BASE_URL
- `.htaccess` : Vérifiez que mod_rewrite est activé dans Apache
- Chemins absolus : Remplacez `C:\xampp\...` par `/Applications/MAMP/...`

### Vérifier les droits de fichiers
```bash
# Listez les permissions actuelles
ls -la /Applications/MAMP/htdocs/STORESuite/

# Chaque dossier d'upload doit avoir 755
# Chaque fichier .php doit avoir 644
```

## Documentation supplémentaire

- Voir aussi : `ETAT_DEPLOIEMENT_12_JAN.md` pour l'état du projet
- Voir aussi : `GUIDE_GIT_NOUVELLE_MACHINE.md` pour la configuration Git
- Voir aussi : `DEPLOIEMENT_STORESUITE_SHOP.md` pour le déploiement Hostinger

## Version MAMP utilisée
- **MAMP** : Version 6.5+ (recommandé)
- **PHP** : 8.0 ou supérieur
- **MySQL** : 5.7 ou 8.0
- **Apache** : 2.4+

---

**Dernière mise à jour** : 12 janvier 2026
**État du projet** : Prêt pour déploiement (localhost ✓, production en attente)
