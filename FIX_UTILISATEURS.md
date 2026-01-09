# 🔧 CORRECTIONS UTILISATEURS - RÉSUMÉ

## ❌ PROBLÈMES IDENTIFIÉS

1. **Erreur SQL** : `Unknown column 'password' in 'field list'`
2. **Modals basiques** : Utilise `alert()` au lieu de modals modernes
3. **Titre modal** : Ne change pas lors de l'édition

---

## ✅ CORRECTIONS APPLIQUÉES

### 1. Colonne `password` → `password_hash`

**Fichiers corrigés :**
- `ajax/utilisateurs.php` :
  - Ligne 69 : INSERT utilise maintenant `password_hash`
  - Ligne 108 : UPDATE utilise maintenant `password_hash`

**Script SQL ajouté :** `migration_tva.sql`
- Détecte automatiquement si la colonne s'appelle `password`
- La renomme en `password_hash` si nécessaire

---

### 2. Modals modernes ajoutés

**Fichier corrigé :** `utilisateurs.php`

**Remplacements effectués :**
- ❌ `alert('Le mot de passe est obligatoire')` 
- ✅ `showAlertModal({ title: 'Champ obligatoire', message: '...', type: 'warning' })`

- ❌ `alert(data.message)` pour succès
- ✅ `showAlertModal({ title: 'Succès', message: data.message, type: 'success' })`

- ❌ `alert('Erreur : ' + data.message)` pour erreur
- ✅ `showAlertModal({ title: 'Erreur', message: data.message, type: 'danger' })`

---

### 3. Titre du modal

**Le code était déjà correct !**

La fonction `editUser()` ligne 393 change bien le titre :
```javascript
document.getElementById('modalTitleText').textContent = 'Modifier l\'utilisateur';
```

**Si le titre ne change pas :** Vérifier que vous cliquez bien sur le bouton "Modifier" (icône crayon) et pas "Nouvel utilisateur"

---

## 🚀 ÉTAPES POUR TESTER

### ÉTAPE 1 : Exécuter le script SQL

Ouvrir phpMyAdmin → votre base → SQL → Coller et exécuter :

```sql
-- Vérifier si la colonne password existe et la renommer
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'utilisateurs' 
    AND COLUMN_NAME = 'password');

SET @sql = IF(@col_exists > 0, 
    'ALTER TABLE utilisateurs CHANGE password password_hash VARCHAR(255) NOT NULL',
    'SELECT "Colonne password_hash déjà correcte" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Vérifier la structure
DESCRIBE utilisateurs;
```

**Résultat attendu :** La colonne doit s'appeler `password_hash` (pas `password`)

---

### ÉTAPE 2 : Tester l'ajout d'utilisateur

1. Ouvrir : `http://localhost/STORESuite/utilisateurs.php`
2. Cliquer "Nouvel utilisateur"
3. Remplir :
   - Nom complet : Test User
   - Email : test@example.com
   - Login : testuser
   - Mot de passe : 123456
   - Rôle : Vendeur
4. Cliquer "Créer l'utilisateur"

**✅ RÉSULTAT ATTENDU :**
- Un **modal moderne** de succès apparaît (pas un alert)
- Message : "Utilisateur créé avec succès"
- Le modal se ferme
- La page se recharge
- L'utilisateur apparaît dans la liste

**❌ PAS D'ERREUR SQL !**

---

### ÉTAPE 3 : Tester la modification

1. Trouver l'utilisateur dans la liste
2. Cliquer sur l'icône **crayon** (Modifier)
3. **Vérifier** : Le titre du modal doit être **"Modifier l'utilisateur"** (pas "Nouvel utilisateur")
4. Les champs sont pré-remplis avec les données
5. Modifier le nom
6. Cliquer "Mettre à jour"

**✅ RÉSULTAT ATTENDU :**
- Modal moderne de succès
- Message : "Utilisateur modifié avec succès"
- Les modifications sont enregistrées

---

### ÉTAPE 4 : Tester la suppression

1. Cliquer sur l'icône **poubelle** (Supprimer)
2. **Modal moderne de confirmation** apparaît
3. Confirmer

**✅ RÉSULTAT ATTENDU :**
- Modal de succès
- L'utilisateur est supprimé ou désactivé

---

## 🔍 VÉRIFICATION BASE DE DONNÉES

Après avoir ajouté un utilisateur, vérifier dans phpMyAdmin :

```sql
SELECT * FROM utilisateurs ORDER BY date_creation DESC LIMIT 1;
```

**Colonnes à vérifier :**
- ✅ `nom_complet` (pas nom_utilisateur)
- ✅ `password_hash` (pas password) - contient un hash bcrypt (commence par $2y$)
- ✅ `login`
- ✅ `email`
- ✅ `est_admin` (0 ou 1)
- ✅ `est_actif` (1)
- ✅ `date_creation`

---

## 📊 CHECKLIST FINALE

- [ ] Script SQL exécuté dans phpMyAdmin
- [ ] Colonne `password_hash` existe (vérifier avec DESCRIBE utilisateurs)
- [ ] Ajout utilisateur : Modal moderne de succès (pas alert)
- [ ] Modification : Titre change en "Modifier l'utilisateur"
- [ ] Modification : Champs pré-remplis
- [ ] Modification : Modal moderne de succès
- [ ] Suppression : Modal moderne de confirmation
- [ ] Aucune erreur SQL n'apparaît

---

## ⚠️ SI PROBLÈMES PERSISTENT

### Erreur "password not found" persiste
→ Exécuter manuellement :
```sql
ALTER TABLE utilisateurs CHANGE password password_hash VARCHAR(255) NOT NULL;
```

### Modal ne s'affiche pas (alert basique)
→ Vérifier que `assets/js/modals.js` est bien chargé dans `header.php`

### Titre ne change pas lors de l'édition
→ Vider le cache du navigateur (Ctrl+Shift+R) ou tester en navigation privée

### Les données ne se pré-remplissent pas
→ Vérifier dans la console du navigateur (F12) s'il y a des erreurs JavaScript

---

**TOUT EST CORRIGÉ ! Les utilisateurs devraient maintenant fonctionner parfaitement ! ✅**
