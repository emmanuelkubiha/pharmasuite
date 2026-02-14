# 📊 Guide Import/Export de Données - PharmaSuite

## Vue d'ensemble

PharmaSuite dispose maintenant d'un système complet d'import et d'export de données pour faciliter :
- L'importation massive de produits depuis Excel
- L'exportation de données pour analyse et sauvegarde
- La migration de données depuis d'autres systèmes

## 📍 Accès

**Menu Paramètres → Section "Import / Export de Données"**

Accessible uniquement aux administrateurs.

---

## 📥 IMPORT DE DONNÉES

### Fichiers supportés
- **.xlsx** : Excel moderne (nécessite PhpSpreadsheet)
- **.xls** : Excel ancien (nécessite PhpSpreadsheet)
- **.csv** : Fichier CSV (séparateur `;` ou `,`)

**💡 Recommandation :** Utilisez le format CSV pour éviter les dépendances de bibliothèques.

### Types d'import disponibles

#### 1. Produits / Médicaments

**Format requis :**
```csv
nom_produit;code_barre;categorie;prix_achat;prix_vente;quantite_stock;seuil_alerte
Paracétamol 500mg;3700123456789;Médicaments;100;150;50;10
Amoxicilline 1g;3700987654321;Antibiotiques;300;450;30;5
```

**Colonnes acceptées :**
- `nom_produit`, `Nom`, `nom` → Nom du produit (obligatoire)
- `code_barre`, `Code`, `code` → Code-barres (optionnel)
- `categorie`, `Catégorie`, `categorie_nom` → Nom de la catégorie
- `prix_achat`, `Prix Achat`, `prix achat` → Prix d'achat unitaire
- `prix_vente`, `Prix Vente`, `prix vente` → Prix de vente (obligatoire)
- `quantite_stock`, `Quantité`, `quantite`, `Stock` → Quantité initiale
- `seuil_alerte`, `Seuil`, `seuil` → Seuil d'alerte de stock

**⚙️ Comportement :**
- Si le produit existe (même code-barre ou nom), il sera mis à jour
- La quantité s'ajoute au stock existant (pas de remplacement)
- Les catégories inexistantes sont créées automatiquement
- Un mouvement de stock est enregistré pour chaque entrée

#### 2. Clients

**Format requis :**
```csv
nom_client;telephone;email;adresse
Jean Dupont;+243999123456;jean@example.com;123 Av Lumumba, Kinshasa
```

**Colonnes acceptées :**
- `nom_client`, `Nom`, `nom` → Nom du client (obligatoire)
- `telephone`, `Téléphone`, `tel` → Numéro de téléphone
- `email`, `Email` → Email du client
- `adresse`, `Adresse` → Adresse complète

#### 3. Fournisseurs

**Format requis :**
```csv
nom_fournisseur;telephone;email;adresse
Pharma Distribution;+243997111222;contact@pharmadist.cd;Zone Industrielle
```

**Colonnes acceptées :**
- `nom_fournisseur`, `Nom`, `nom` → Nom du fournisseur (obligatoire)
- `telephone`, `Téléphone`, `tel` → Numéro de téléphone
- `email`, `Email` → Email
- `adresse`, `Adresse` → Adresse complète

### Procédure d'import

1. **Télécharger un modèle** (section "Modèles Excel à télécharger")
2. **Remplir le fichier** avec vos données
3. **Sélectionner le type** d'import (Produits, Clients, Fournisseurs)
4. **Choisir le fichier** (.csv, .xlsx, .xls)
5. **Cliquer sur "Importer maintenant"**
6. **Consulter le résultat** : nombre d'ajouts, mises à jour, erreurs

### 🔍 Détection intelligente

Le système détecte automatiquement :
- Les séparateurs CSV (`;` ou `,`)
- Le format d'encodage (UTF-8 avec ou sans BOM)
- Les variantes de noms de colonnes

### ⚠️ Gestion des erreurs

En cas d'erreur sur une ligne :
- L'import continue pour les autres lignes
- Les erreurs sont comptabilisées
- Le détail des erreurs est disponible dans la réponse

---

## 📤 EXPORT DE DONNÉES

### Formats disponibles
- **Excel (.xls)** : Compatible avec Microsoft Excel, LibreOffice
- **PDF** : Pour impression et archivage

### Types d'export

#### 1. Produits / Médicaments
Liste complète de tous les produits avec prix, stock, catégorie

#### 2. Ventes
Historique des ventes sur une période donnée

#### 3. Catégories
Liste des catégories de produits

#### 4. État du stock
Inventaire actuel de tous les produits

#### 5. Mouvements de stock
Historique des entrées/sorties de stock

#### 6. Alertes de stock
Produits sous le seuil d'alerte

### Procédure d'export

1. **Sélectionner le type** de données à exporter
2. **Choisir la période** (pour ventes et mouvements)
3. **Cliquer sur le bouton** Excel ou PDF
4. Le fichier se télécharge automatiquement

---

## 📋 MODÈLES EXCEL

Des modèles pré-formatés sont disponibles pour faciliter l'import :

- **Modèle Produits** : Structure complète pour produits/médicaments
- **Modèle Clients** : Format standard pour clients
- **Modèle Fournisseurs** : Format pour fournisseurs

Téléchargez-les, remplissez-les, et importez-les directement !

---

## 💡 CONSEILS ET BONNES PRATIQUES

### Pour l'import

1. **Testez avec peu de lignes d'abord** (5-10 produits) pour valider le format
2. **Utilisez Excel ou LibreOffice** pour éditer les CSV
3. **Enregistrez toujours en UTF-8** pour éviter les problèmes d'accents
4. **Vérifiez les prix** : utilisez des nombres sans symbole monétaire
5. **Codes-barres uniques** : évitez les doublons

### Pour l'export

1. **Exportez régulièrement** pour sauvegarder vos données
2. **Utilisez Excel** pour analyse (graphiques, tableaux croisés)
3. **Utilisez PDF** pour archivage et impression
4. **Précisez les dates** pour les rapports de ventes

### Migration depuis un autre système

Si vous migrez depuis un autre système :

1. **Exportez vos données** au format CSV/Excel
2. **Adaptez les colonnes** aux noms attendus par PharmaSuite
3. **Testez l'import** sur quelques lignes
4. **Importez par lots** (ex: 100-200 lignes à la fois)
5. **Vérifiez les résultats** après chaque import

---

## 🔧 DÉPANNAGE

### Erreur : "Bibliothèque PhpSpreadsheet non installée"

**Solution :** Convertissez votre fichier Excel en CSV
- Excel : "Enregistrer sous" → CSV (séparateur point-virgule)
- LibreOffice : "Enregistrer sous" → CSV (`;` comme séparateur)

### Erreur : "Aucune donnée trouvée"

**Causes possibles :**
- Fichier vide
- Première ligne manquante (en-têtes)
- Séparateur incorrect

**Solution :** Vérifiez que votre fichier contient des données et des en-têtes

### Import partiel (certaines lignes échouent)

**Causes possibles :**
- Champs obligatoires manquants (nom, prix)
- Format de prix incorrect (texte au lieu de nombre)
- Caractères spéciaux mal encodés

**Solution :** Consultez le rapport d'erreurs et corrigez les lignes concernées

### Accents incorrects

**Solution :** Assurez-vous que votre fichier CSV est encodé en UTF-8
- Dans Notepad++ : "Encodage" → "Convertir en UTF-8"
- Dans Excel : Utiliser "CSV UTF-8" lors de l'enregistrement

---

## 📞 SUPPORT

En cas de problème non résolu :
1. Vérifiez ce guide
2. Consultez les erreurs affichées
3. Testez avec un modèle vierge
4. Contactez le support technique

---

**Dernière mise à jour :** 14 février 2026  
**Version :** PharmaSuite v2.0
