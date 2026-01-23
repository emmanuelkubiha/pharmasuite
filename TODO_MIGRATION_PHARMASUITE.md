
# TODO Migration PharmaSuite – Liste détaillée

## 1. Adaptation de la base de données
- [ ] Renommer la table `produits` en `medicaments`
- [ ] Renommer la table `clients` en `patients`
- [ ] Renommer toutes les colonnes et clés étrangères associées (`id_produit` → `id_medicament`, `nom_produit` → `nom_medicament`, etc.)
- [ ] Ajouter la table `lots_medicaments` :
	- id_lot (PK), id_medicament (FK), numero_lot, date_peremption, quantite, date_entree
- [ ] Ajouter les champs suivants à `medicaments` :
	- dosage (VARCHAR), conditionnement (VARCHAR), date_peremption (DATE, si gestion unique), code_medicament (ex-code_barre)
- [ ] Ajouter le champ `id_lot` dans `ventes_details` et `mouvements_stock` pour tracer le lot utilisé
- [ ] Adapter les vues et triggers pour la gestion par lot et la péremption
- [ ] Adapter les rôles utilisateurs dans `utilisateurs` :
	- vendeur → pharmacien(ne), admin/gerant inchangés
- [ ] Ajouter la table `depenses` :
	- id_depense, date_depense, montant, motif, utilisateur_id, caisse_id
- [ ] Ajouter la table `caisses` :
	- id_caisse, nom, solde_initial, solde_actuel
- [ ] Adapter les rapports pour permettre le filtrage par médicament, période, lot
- [ ] Ajouter une vue/tableau pour le suivi des péremptions (lots proches ou dépassés)

## 2. Adaptation des fichiers et logique PHP/JS
- [ ] Renommer toutes les occurrences de "produit" en "médicament" (fichiers, variables, UI, commentaires)
- [ ] Renommer toutes les occurrences de "client" en "patient"
- [ ] Adapter la gestion des ventes :
	- Lors de la vente, proposer par défaut le lot le plus ancien (date_entree la plus ancienne, non périmé)
	- Permettre la sélection manuelle d’un autre lot si besoin
- [ ] Adapter la gestion des mouvements de stock :
	- Intégrer la gestion des lots et de la péremption dans tous les mouvements (entrée, sortie, ajustement)
- [ ] Adapter la gestion des utilisateurs :
	- Rôles : pharmacien(ne), gérant, admin
- [ ] Ajouter/adapter les pages pour la gestion des dépenses et caisses (enregistrement, stats, suivi)
- [ ] Ajouter/adapter les pages pour le suivi des péremptions (onglet dédié, alertes, actions)
- [ ] Adapter tous les rapports :
	- Filtres par période, médicament, lot
	- Rapport de vente par médicament sur une période donnée
	- Statistiques de caisse/dépenses (entrées, sorties, solde)

## 3. Adaptation de l’interface utilisateur (UI)
- [ ] Transformer `index.php` en page vitrine :
	- Présentation de la pharmacie (nom, logo, slogan, description)
	- Sections : À propos, Nos services, Contact, Adresse, Horaires
	- Design moderne, responsive, couleurs dynamiques depuis la configuration (primaire/secondaire)
- [ ] Ajouter un footer avec un lien discret/onglet pour accéder au login (connexion)
- [ ] Adapter le design de toutes les pages pour utiliser les couleurs de la configuration (primaire/secondaire)
- [ ] Ajouter/adapter les onglets :
	- Suivi des péremptions
	- Dépenses/Caisse
	- Rapports par période, médicament, lot

## 4. Sécurité, conformité et logs
- [ ] Adapter la gestion des accès et permissions selon les nouveaux rôles (pharmacien(ne), gérant, admin)
- [ ] Vérifier la conformité des logs et de la traçabilité (dispensation, mouvements de stock, ventes, dépenses)

## 5. Documentation et migration
- [ ] Mettre à jour la documentation technique et utilisateur (noms, captures, workflows)
- [ ] Documenter les scripts de migration SQL (ALTER/CREATE)
- [ ] Documenter les nouveaux modules (lots, péremptions, caisse)

---

> Ce fichier TODO doit être suivi pour la migration et l’adaptation complète du système vers PharmaSuite (gestion pharmacie). Chaque étape doit être validée avant de passer à la suivante.
