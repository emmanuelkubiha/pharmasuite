# 🛍️ STORESUITE

Système complet de gestion de commerce de détail développé en PHP.

## 📋 Description

STORESUITE est une solution de point de vente (POS) et de gestion d'inventaire conçue pour les commerces de détail. Le système offre une interface moderne et intuitive en français pour gérer efficacement les ventes, les stocks, les clients et la facturation.

## ✨ Fonctionnalités principales

- **💰 Gestion des ventes** : Interface de caisse professionnelle avec calcul automatique de TVA (16%)
- **📦 Gestion des stocks** : Suivi en temps réel, alertes de stock faible, mouvements de stock
- **👥 Gestion des clients** : Base de données clients complète avec historique d'achats
- **📄 Facturation** : Génération automatique de factures professionnelles
- **📊 Rapports** : Statistiques de ventes, tableaux de bord, exports PDF/Excel
- **👤 Gestion des utilisateurs** : Système de rôles (Admin/Vendeur) avec permissions
- **🎨 Personnalisation** : Logo, couleurs, informations de la boutique

## 🚀 Technologies utilisées

- **Backend** : PHP 7.4+ avec PDO
- **Frontend** : Bootstrap 5, JavaScript (Vanilla)
- **Base de données** : MySQL/MariaDB
- **Serveur** : XAMPP (Apache + MySQL)

## 📦 Installation

### Prérequis

- XAMPP (ou tout serveur Apache + MySQL + PHP 7.4+)
- Navigateur web moderne (Chrome, Firefox, Edge)

### Étapes d'installation

1. **Cloner le repository**
   ```bash
   git clone https://github.com/votre-username/STORESuite.git
   cd STORESuite
   ```

2. **Créer la base de données**
   - Ouvrir phpMyAdmin : `http://localhost/phpmyadmin`
   - Créer une nouvelle base de données nommée `storesuite`
   - Importer le fichier SQL : `database/storesuite.sql`

3. **Configurer les accès**
   - Copier `config/config.example.php` vers `config/config.php`
   - Modifier les paramètres de connexion MySQL si nécessaire

4. **Configuration initiale**
   - Accéder à : `http://localhost/STORESuite/setup.php`
   - Suivre l'assistant de configuration
   - Créer le compte administrateur

5. **Connexion**
   - Accéder à : `http://localhost/STORESuite/`
   - Se connecter avec les identifiants créés

## 🏗️ Structure du projet

```
STORESuite/
├── ajax/                    # Endpoints AJAX pour opérations backend
│   ├── categories.php
│   ├── clients.php
│   ├── produits.php
│   ├── utilisateurs.php
│   └── valider_vente.php
├── assets/                  # Ressources frontend
│   ├── css/                # Feuilles de style
│   ├── js/                 # Scripts JavaScript
│   └── img/                # Images statiques
├── config/                  # Configuration système
│   ├── config.php          # Configuration générale
│   └── database.php        # Connexion BDD et helpers
├── database/               # Migrations SQL
├── uploads/                # Fichiers uploadés (logos, images)
├── protection_pages.php    # Middleware d'authentification
├── header.php             # En-tête commun
├── footer.php             # Pied de page commun
├── vente_professionnel.php # Interface de vente
├── listes.php             # Gestion produits/clients/catégories
├── facture.php            # Gestion des factures
├── rapports.php           # Rapports et statistiques
└── parametres.php         # Configuration de la boutique
```

## 🔒 Sécurité

- Requêtes préparées PDO (protection contre les injections SQL)
- Échappement XSS sur toutes les sorties avec fonction `e()`
- Système de sessions sécurisées avec timeout (2h)
- Authentification requise sur toutes les pages protégées
- Logs d'activité pour audit
- Gestion des permissions par rôle

## 📚 Documentation

Pour les développeurs souhaitant contribuer ou étendre le système :

- [Guide IA](.github/copilot-instructions.md) - Instructions pour les agents IA
- [Corrections appliquées](CORRECTIONS_APPLIQUEES.md) - Historique des corrections
- [État du projet](ETAT_PROJET_9_JAN_2026.md) - État actuel et roadmap
- [Session de travail](SESSION_TRAVAIL.md) - Notes de développement

## 🛠️ Développement

### Conventions de code

- **Langue** : Français pour les variables, UI, messages et commentaires
- **Style** : PSR-12 pour PHP, Bootstrap utilities pour CSS
- **Modals** : Utiliser `showConfirmModal()` et `showAlertModal()` (jamais `alert()` ou `confirm()`)
- **Base de données** : Utiliser les fonctions helper (`db_query()`, `db_insert()`, etc.)

### Ajouter une fonctionnalité

1. Créer la page PHP à la racine avec `require_once('protection_pages.php');`
2. Créer l'endpoint AJAX dans `ajax/` si nécessaire
3. Utiliser les transactions pour les opérations multi-tables
4. Documenter les changements dans les fichiers markdown

## 🐛 Problèmes connus

Consultez [SESSION_TRAVAIL.md](SESSION_TRAVAIL.md) pour la liste des problèmes connus et leurs solutions.

## 📝 Licence

Ce projet est sous licence MIT - voir le fichier [LICENSE](LICENSE) pour plus de détails.

## 👥 Contribution

Les contributions sont les bienvenues ! N'hésitez pas à :

1. Fork le projet
2. Créer une branche pour votre fonctionnalité (`git checkout -b feature/AmazingFeature`)
3. Commit vos changements (`git commit -m 'Add some AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une Pull Request

## 📧 Contact

Pour toute question ou suggestion, n'hésitez pas à ouvrir une issue sur GitHub.

---

**⚠️ Note de sécurité** : Ne jamais commit le fichier `config/config.php` avec de vraies credentials. Utilisez des variables d'environnement en production.
