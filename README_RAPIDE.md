# 🚀 DÉMARRAGE RAPIDE - STORE SUITE

## 📌 CE QUI A ÉTÉ CORRIGÉ AUJOURD'HUI

✅ **Erreur SQL utilisateurs** → Corrigé (nom_utilisateur → nom_complet)  
✅ **Modals modernes** → Déjà implémentés et fonctionnels  
✅ **Tableau de bord** → Existe déjà et fonctionne  
✅ **Page vente professionnelle** → CRÉÉE avec prix/quantité modifiables + TVA 16%  
✅ **Validation vente avec TVA** → Backend créé  

---

## ⚡ 3 ÉTAPES POUR DÉMARRER

### ÉTAPE 1 : Base de données (2 min)
Ouvrir phpMyAdmin et exécuter **tout le contenu** de `migration_tva.sql` :

```sql
-- 1. Ajouter colonnes TVA
ALTER TABLE ventes 
ADD COLUMN IF NOT EXISTS montant_ht DECIMAL(10,2) DEFAULT 0 AFTER montant_total,
ADD COLUMN IF NOT EXISTS montant_tva DECIMAL(10,2) DEFAULT 0 AFTER montant_ht;

UPDATE ventes 
SET montant_ht = ROUND(montant_total / 1.16, 2),
    montant_tva = ROUND(montant_total - (montant_total / 1.16), 2)
WHERE montant_ht = 0;

-- 2. Corriger colonne password → password_hash
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
```

### ÉTAPE 2 : Tester la nouvelle page vente (5 min)
Ouvrir : `http://localhost/STORESuite/vente_professionnel.php`

1. Cliquer sur un produit
2. **Modifier le prix** (ex: mettre 150)
3. **Modifier la quantité** (ex: mettre 3)
4. Ajouter au panier
5. **Vérifier la TVA 16%** en bas du panier
6. Valider la vente
7. **La facture s'imprime avec la TVA !**

### ÉTAPE 3 : Remplacer l'ancienne page (optionnel)
Si tout fonctionne, renommer dans le dossier :
- `vente.php` → `vente_old.php` (backup)
- `vente_professionnel.php` → `vente.php` (activer)

---

## 📁 FICHIERS CRÉÉS

1. `vente_professionnel.php` - Nouvelle interface de vente
2. `ajax/valider_vente.php` - Validation avec TVA
3. `migration_tva.sql` - Script BDD
4. `CORRECTIONS_APPLIQUEES.md` - Documentation complète
5. `GUIDE_TEST.md` - Tests détaillés

---

## 🎯 FONCTIONNALITÉS PRINCIPALES

### ✨ Page Vente Professionnelle
- Recherche instantanée de produits
- **Modal d'ajout avec prix ET quantité modifiables**
- Panier dynamique avec modification inline
- **Calcul automatique TVA 16%**
- Affichage : Total HT / TVA / Total TTC
- Validation avec modals modernes
- Impression facture automatique

### 💰 Calcul TVA
```
Exemple :
Produit A : 3 × 150 = 450
Produit B : 2 × 200 = 400
────────────────────────
Total HT  : 850.00
TVA (16%) : 136.00
Total TTC : 986.00
```

---

## 📞 BESOIN D'AIDE ?

### Lire les guides détaillés :
- `CORRECTIONS_APPLIQUEES.md` - Tout ce qui a été fait
- `GUIDE_TEST.md` - Tests étape par étape
- `ETAT_PROJET_9_JAN_2026.md` - État complet du projet

### Erreur commune :
**"Column nom_utilisateur not found"**  
→ Exécuter :
```sql
ALTER TABLE utilisateurs CHANGE nom_utilisateur nom_complet VARCHAR(100) NOT NULL;
```

---

## ✅ CHECKLIST RAPIDE

- [ ] Script SQL exécuté
- [ ] `vente_professionnel.php` testé
- [ ] Prix modifiable ✓
- [ ] Quantité modifiable ✓
- [ ] TVA 16% affichée ✓
- [ ] Facture imprimée avec TVA ✓

---

**C'EST PRÊT ! BONNE UTILISATION ! 🎉**
