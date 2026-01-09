# 🧪 GUIDE DE TEST - STORE SUITE

## ✅ CHECKLIST COMPLÈTE DES TESTS

### 📝 PRÉPARATION (5 min)

1. **Démarrer XAMPP**
   - [ ] Apache démarré
   - [ ] MySQL démarré

2. **Exécuter le script SQL**
   - [ ] Ouvrir phpMyAdmin : `http://localhost/phpmyadmin`
   - [ ] Sélectionner votre base de données
   - [ ] Onglet SQL
   - [ ] Copier/coller le contenu de `migration_tva.sql`
   - [ ] Cliquer "Exécuter"
   - [ ] Vérifier message "Migration terminée avec succès !"

3. **Vérifier la structure**
   ```sql
   -- Exécuter ces commandes dans phpMyAdmin
   DESCRIBE utilisateurs;  -- Doit avoir 'nom_complet'
   DESCRIBE ventes;        -- Doit avoir 'montant_ht' et 'montant_tva'
   DESCRIBE details_vente; -- Doit exister
   ```

---

### 👤 TEST 1 : UTILISATEURS (2 min)

**URL :** `http://localhost/STORESuite/utilisateurs.php`

1. **Créer un utilisateur**
   - [ ] Cliquer "Nouvel utilisateur"
   - [ ] Remplir : Nom = "Test Vendeur"
   - [ ] Login = "testvendeur"
   - [ ] Email = "test@example.com"
   - [ ] Mot de passe = "123456"
   - [ ] Rôle = "Vendeur"
   - [ ] Cliquer "Enregistrer"
   - [ ] **RÉSULTAT ATTENDU :** Modal de succès (PAS d'erreur SQL)
   - [ ] **RÉSULTAT ATTENDU :** L'utilisateur apparaît dans la liste

2. **Modifier un utilisateur**
   - [ ] Cliquer sur "Modifier" (icône crayon)
   - [ ] Changer le nom en "Test Vendeur Modifié"
   - [ ] Sauvegarder
   - [ ] **RÉSULTAT ATTENDU :** Modal de succès

3. **Supprimer un utilisateur (test)**
   - [ ] Cliquer "Supprimer" (icône poubelle)
   - [ ] Modal de confirmation apparaît
   - [ ] Annuler (ne pas supprimer)

---

### 📊 TEST 2 : TABLEAU DE BORD (2 min)

**URL :** `http://localhost/STORESuite/tableau_de_bord.php`

1. **Vérifier l'affichage**
   - [ ] La page s'affiche sans erreur
   - [ ] Les 4 cartes statistiques sont visibles :
     - CA Aujourd'hui
     - CA Ce mois
     - Alertes stock
     - Panier moyen
   - [ ] Le graphique des 7 derniers jours s'affiche
   - [ ] La liste "Top 5 produits" s'affiche
   - [ ] La liste "Alertes stock" s'affiche (même vide)
   - [ ] La liste "Dernières ventes" s'affiche (même vide)

---

### 🛒 TEST 3 : NOUVELLE PAGE VENTE (10 min) ⭐ PRINCIPAL

**URL :** `http://localhost/STORESuite/vente_professionnel.php`

#### 3.1 Interface de base
- [ ] La page s'affiche correctement
- [ ] Les produits sont visibles à gauche
- [ ] Le panier vide est visible à droite
- [ ] Le message "Panier vide" s'affiche

#### 3.2 Recherche produit
- [ ] Taper un nom de produit dans "Rechercher..."
- [ ] **RÉSULTAT ATTENDU :** Les produits sont filtrés en temps réel

#### 3.3 Ajouter un produit (FONCTIONNALITÉ CLÉ)
1. **Cliquer sur un produit**
   - [ ] Un modal s'ouvre
   - [ ] Le nom du produit est affiché
   - [ ] Le stock disponible est affiché
   - [ ] Quantité = 1 par défaut
   - [ ] Prix = prix catalogue par défaut

2. **Modifier la quantité**
   - [ ] Changer à 3
   - [ ] **RÉSULTAT ATTENDU :** Le sous-total se met à jour automatiquement
   - [ ] Exemple : Si prix = 100, sous-total doit être 300

3. **Modifier le prix** ⭐
   - [ ] Changer le prix (ex: mettre 150 au lieu de 100)
   - [ ] **RÉSULTAT ATTENDU :** Le sous-total se recalcule
   - [ ] Exemple : Si qté = 3 et prix = 150, sous-total = 450

4. **Valider l'ajout**
   - [ ] Cliquer "Ajouter au panier"
   - [ ] **RÉSULTAT ATTENDU :** Modal de succès apparaît
   - [ ] **RÉSULTAT ATTENDU :** Le produit apparaît dans le panier
   - [ ] **RÉSULTAT ATTENDU :** Le compteur "Panier (X)" se met à jour

#### 3.4 Vérifier le calcul TVA 16% ⭐⭐
- [ ] Regarder en bas du panier les 3 lignes :
  - **Total HT** : Somme des produits
  - **TVA (16%)** : 16% du Total HT
  - **Total TTC** : HT + TVA

**Exemple de calcul à vérifier :**
```
Produit 1 : 3 × 150 = 450
Produit 2 : 2 × 200 = 400
─────────────────────────
Total HT  : 850.00
TVA (16%) : 136.00  (850 × 0.16)
─────────────────────────
Total TTC : 986.00  (850 + 136)
```

- [ ] **VÉRIFIER :** TVA = Total HT × 0.16
- [ ] **VÉRIFIER :** Total TTC = Total HT + TVA

#### 3.5 Modifier dans le panier
1. **Modifier la quantité inline**
   - [ ] Changer la quantité dans le panier (champ "Qté")
   - [ ] **RÉSULTAT ATTENDU :** Le total se recalcule
   - [ ] **RÉSULTAT ATTENDU :** La TVA se recalcule

2. **Modifier le prix inline**
   - [ ] Changer le prix dans le panier (champ "Prix")
   - [ ] **RÉSULTAT ATTENDU :** Le total se recalcule
   - [ ] **RÉSULTAT ATTENDU :** Un badge "Prix modifié" apparaît

3. **Supprimer un article**
   - [ ] Cliquer sur l'icône "×" (supprimer)
   - [ ] Modal de confirmation apparaît
   - [ ] Confirmer la suppression
   - [ ] **RÉSULTAT ATTENDU :** L'article disparaît du panier
   - [ ] **RÉSULTAT ATTENDU :** Les totaux se recalculent

#### 3.6 Valider la vente ⭐⭐⭐
1. **Sélectionner un client (optionnel)**
   - [ ] Choisir un client dans la liste déroulante
   - [ ] OU laisser "Vente au comptoir"

2. **Choisir le mode de paiement**
   - [ ] Sélectionner : Espèces / Carte / Mobile Money / Chèque

3. **Cliquer "Valider la vente"**
   - [ ] Modal de confirmation s'affiche avec le total TTC
   - [ ] Confirmer
   - [ ] **RÉSULTAT ATTENDU :** Modal "Vente validée !" apparaît
   - [ ] **RÉSULTAT ATTENDU :** Numéro de facture affiché (ex: FAC-20260109-1234)
   - [ ] **RÉSULTAT ATTENDU :** Une nouvelle fenêtre s'ouvre avec la facture

4. **Vérifier la facture imprimée**
   - [ ] La facture s'affiche dans la nouvelle fenêtre
   - [ ] Le logo de la boutique est visible (si configuré)
   - [ ] Les informations de la boutique sont affichées
   - [ ] Le numéro de facture est correct
   - [ ] La liste des produits est correcte (nom, qté, prix, total)
   - [ ] **VÉRIFIER :** Total HT est affiché
   - [ ] **VÉRIFIER :** TVA (16%) est affichée
   - [ ] **VÉRIFIER :** Total TTC est affiché et correct
   - [ ] Le mode de paiement est indiqué
   - [ ] Le vendeur est indiqué

5. **Après validation**
   - [ ] **RÉSULTAT ATTENDU :** Le panier est vidé automatiquement
   - [ ] **RÉSULTAT ATTENDU :** Le compteur retourne à 0

#### 3.7 Vider le panier
- [ ] Ajouter des produits au panier
- [ ] Cliquer "Vider" (bouton rouge en haut)
- [ ] Modal de confirmation apparaît
- [ ] Confirmer
- [ ] **RÉSULTAT ATTENDU :** Le panier est vidé

---

### 📋 TEST 4 : VÉRIFICATION BASE DE DONNÉES (3 min)

**Ouvrir phpMyAdmin et vérifier :**

1. **Table ventes**
   ```sql
   SELECT * FROM ventes ORDER BY date_vente DESC LIMIT 1;
   ```
   - [ ] La vente de test est enregistrée
   - [ ] `numero_facture` est présent (ex: FAC-20260109-1234)
   - [ ] `montant_ht` est rempli et correct
   - [ ] `montant_tva` est rempli et = 16% du HT
   - [ ] `montant_total` = HT + TVA
   - [ ] `mode_paiement` est correct
   - [ ] `statut` = 'validee'
   - [ ] `id_vendeur` correspond à votre utilisateur

2. **Table details_vente**
   ```sql
   SELECT * FROM details_vente WHERE id_vente = [ID_DE_LA_VENTE_TEST];
   ```
   - [ ] Tous les produits du panier sont enregistrés
   - [ ] Les quantités sont correctes
   - [ ] Les prix unitaires sont corrects (même si modifiés)
   - [ ] Les sous-totaux sont corrects

3. **Table produits (stock déduit)**
   ```sql
   SELECT nom_produit, quantite_stock FROM produits WHERE id_produit = [ID_PRODUIT_VENDU];
   ```
   - [ ] Le stock a été déduit
   - [ ] Exemple : Si stock avant = 50 et vendu = 3, stock après = 47

4. **Table mouvements (traçabilité)**
   ```sql
   SELECT * FROM mouvements WHERE motif LIKE '%FAC-%' ORDER BY date_mouvement DESC LIMIT 5;
   ```
   - [ ] Des mouvements de type 'sortie' ont été créés
   - [ ] Un mouvement par produit vendu
   - [ ] Le motif contient le numéro de facture
   - [ ] Les quantités correspondent

---

### 🐛 TEST 5 : GESTION DES ERREURS (5 min)

#### 5.1 Stock insuffisant
1. **Trouver un produit avec peu de stock (ex: stock = 2)**
2. **Essayer d'ajouter une quantité supérieure (ex: 5)**
   - [ ] **RÉSULTAT ATTENDU :** Message d'erreur : "La quantité doit être entre 1 et 2"

#### 5.2 Prix invalide
1. **Ajouter un produit**
2. **Mettre un prix négatif ou 0**
   - [ ] **RÉSULTAT ATTENDU :** Message d'erreur : "Le prix doit être supérieur à 0"

#### 5.3 Panier vide
1. **Vider le panier**
2. **Cliquer "Valider la vente"**
   - [ ] **RÉSULTAT ATTENDU :** Message d'erreur : "Ajoutez des produits avant de valider"

---

## 📊 TABLEAU DE RÉSULTATS

Cochez chaque test réussi :

| Test | Description | Statut |
|------|-------------|--------|
| 1.1  | Créer utilisateur | ⬜ |
| 1.2  | Modifier utilisateur | ⬜ |
| 2.1  | Affichage tableau de bord | ⬜ |
| 3.1  | Interface vente | ⬜ |
| 3.2  | Recherche produit | ⬜ |
| 3.3  | Ajouter produit avec modal | ⬜ |
| 3.4  | **Modification prix et quantité** | ⬜ |
| 3.5  | **Calcul TVA 16%** | ⬜ |
| 3.6  | Modification inline panier | ⬜ |
| 3.7  | **Validation vente** | ⬜ |
| 3.8  | **Impression facture avec TVA** | ⬜ |
| 4.1  | Vérification BDD ventes | ⬜ |
| 4.2  | Vérification BDD détails | ⬜ |
| 4.3  | Stock déduit | ⬜ |
| 4.4  | Mouvements créés | ⬜ |
| 5.1  | Gestion erreur stock | ⬜ |

---

## ✅ RÉSULTAT ATTENDU FINAL

Si tous les tests passent :
- ✅ Pas d'erreur SQL sur les utilisateurs
- ✅ Le tableau de bord s'affiche correctement
- ✅ La page de vente fonctionne parfaitement
- ✅ Le prix et la quantité sont modifiables
- ✅ La TVA 16% est calculée et affichée
- ✅ La vente s'enregistre correctement en BDD
- ✅ La facture s'imprime avec la TVA
- ✅ Le stock est déduit automatiquement
- ✅ Les mouvements sont tracés

---

## 🆘 EN CAS DE PROBLÈME

### Problème : Erreur SQL "nom_utilisateur"
**Solution :**
```sql
ALTER TABLE utilisateurs CHANGE nom_utilisateur nom_complet VARCHAR(100) NOT NULL;
```

### Problème : Modal ne s'affiche pas
**Solution :** Vérifier que `assets/js/modals.js` est bien chargé
```html
<!-- Dans header.php, doit contenir : -->
<script src="assets/js/modals.js"></script>
```

### Problème : TVA ne s'affiche pas
**Solution :** Exécuter `migration_tva.sql`

### Problème : Facture ne s'imprime pas
**Solution :** Vérifier que `facture_impression.php` utilise les bons noms de colonnes

---

**BON TEST ! 🚀**

Temps total estimé : 20-30 minutes
