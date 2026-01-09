# 📚 Git Commands - Guide Complet STORESuite

## 🔧 PRINCIPES À RESPECTER À CHAQUE FOIS

### 1. **AVANT DE COMMENCER** (chaque session)
```bash
git status                    # Vérifier l'état local
git pull origin main          # Récupérer les dernières modifications
```

### 2. **PENDANT LE DÉVELOPPEMENT**
```bash
git status                    # Voir ce qui a changé
git diff <fichier>            # Voir les modifications d'un fichier
git add .                     # Ajouter tous les fichiers modifiés
git commit -m "Description"   # Commiter avec un message clair
```

### 3. **APRÈS CHAQUE CHANGEMENT IMPORTANT**
```bash
git push origin main          # Pousser vers GitHub immédiatement
git log --oneline -5          # Vérifier que le commit est bien enregistré
```

---

## ⚙️ CONFIG DE BASE (une fois par machine)

```bash
git config --global user.name "Emmanuel Kubiha"
git config --global user.email "ton-email@example.com"

# Vérifier la config
git config --global --list
```

---

## 📖 CYCLE CLASSIQUE (à faire à chaque fois)

```bash
# 1. Vérifier l'état
git status

# 2. Récupérer les modifications distantes
git pull origin main

# 3. Faire des changements à tes fichiers (dans l'éditeur)

# 4. Voir les changements
git status                    # Fichiers modifiés
git diff                      # Détail des modifications

# 5. Ajouter les fichiers au commit
git add .                     # Tous les fichiers
# ou
git add <fichier>             # Un fichier spécifique

# 6. Vérifier ce qui va être commité
git status                    # Vérifie les fichiers "staged"

# 7. Créer le commit
git commit -m "Description courte et claire"

# 8. Pousser sur GitHub
git push origin main

# 9. Vérifier que tout est OK
git status                    # Devrait dire "nothing to commit"
git log --oneline -1          # Voir le dernier commit
```

---

## 🌿 BRANCHING (Créer des branches de travail)

### Créer une branche pour une feature
```bash
git checkout -b feature/nom-du-feature    # Créer et basculer
git branch                                 # Voir les branches locales
git push -u origin feature/nom-du-feature  # Pousser la branche
```

### Basculer entre branches
```bash
git checkout main                         # Revenir à main
git checkout feature/mon-feature          # Aller à une autre branche
```

### Fusionner une branche
```bash
git checkout main                         # Aller sur main
git pull origin main                      # Mettre à jour
git merge feature/mon-feature             # Fusionner la feature
git push origin main                      # Pousser la fusion
```

### Supprimer une branche
```bash
git branch -d feature/mon-feature         # Local
git push origin --delete feature/mon-feature  # Distant
```

---

## 📊 COMPARER LES DIFFÉRENCES

### Avant de commit
```bash
git diff                                  # Non-staged vs Local
git diff --cached                         # Staged vs Dernière version
git diff <branche1> <branche2>            # Entre deux branches
git diff HEAD~1 HEAD                      # Entre le dernier et l'avant-dernier commit
```

### Voir les fichiers modifiés
```bash
git status                                # Résumé
git status -s                             # Format court
```

### Historique avec détails
```bash
git log                                   # Historique complet
git log --oneline                         # Résumé (une ligne par commit)
git log --oneline -10                     # Derniers 10 commits
git log --graph --oneline --all --decorate  # Graphique des branches
git log -p                                # Avec les différences
git show <hash-commit>                    # Voir un commit spécifique
```

---

## ⚠️ ANNULER DES COMMITS

### Annuler les changements locaux (AVANT commit)
```bash
git restore <fichier>                     # Annuler un fichier non-staged
git restore --staged <fichier>            # Retirer du stage
git restore --staged .                    # Retirer tous les fichiers du stage
git restore .                             # Annuler TOUS les changements
```

### Annuler un commit (APRÈS commit, AVANT push)
```bash
git reset HEAD~1                          # Annuler le dernier commit (garde les fichiers modifiés)
git reset --soft HEAD~1                   # Annuler mais garder les changements staged
git reset --hard HEAD~1                   # Annuler complètement (DANGER!)
```

### Annuler un commit qui a été pushé (APRÈS push)
```bash
git revert <hash-commit>                  # Crée un nouveau commit qui annule l'ancien (SAFE)
git revert HEAD                           # Annuler le dernier commit pushé
git push origin main                      # Pousser la "suppression"
```

### Voir les commits supprimés/perdus
```bash
git reflog                                # Voir tous les mouvements du HEAD
git reset --hard <hash-ancien-commit>     # Récupérer un commit "perdu"
```

---

## 🔄 PULL / PUSH (Synchronisation)

### Récupérer les modifications (sans fusionner)
```bash
git fetch origin                          # Télécharger sans fusionner
git fetch origin main                     # Télécharger une branche spécifique
```

### Récupérer et fusionner (combine fetch + merge)
```bash
git pull origin main                      # Récupérer et fusionner
git pull --rebase origin main             # Récupérer avec rebase (linéaire)
```

### Pousser les commits locaux
```bash
git push origin main                      # Pousser la branche main
git push origin <branche>                 # Pousser une autre branche
git push --all                            # Pousser toutes les branches
git push --tags                           # Pousser les tags/versions
```

### Forcer un push (DANGER!)
```bash
git push --force origin main              # Remplace l'historique distant (utiliser avec prudence!)
git push --force-with-lease               # Plus safe que --force
```

---

## 🔀 RÉSOUDRE LES CONFLITS

Quand deux modifications entrent en conflit :

```bash
git status                                # Voir les conflits
# Édite les fichiers pour résoudre les <<<<<<< ======= >>>>>>>
git add <fichier-résolu>
git commit -m "Résolution du conflit"
git push origin main
```

---

## 📱 CLONER LE PROJET SUR UNE AUTRE MACHINE

```bash
# Configuration initiale
git config --global user.name "Emmanuel Kubiha"
git config --global user.email "ton-email@example.com"

# Cloner le repo
git clone https://github.com/emmanuelkubiha/store-suite.git
cd store-suite

# Vérifier que tout est OK
git status
git log --oneline -5

# Récupérer les dernières modifications
git pull origin main
```

---

## 🔍 COMPARER DEUX MACHINES

### Version 1 : Via les logs
```bash
git log --oneline --all -20                # Voir l'historique
# Comparer les commits avec l'autre machine
```

### Version 2 : Via git diff
```bash
# Si l'autre machine a pushé
git pull origin main                      # Récupérer ses changements
git diff                                  # Voir les différences

# Si tu travailles sur une branche différente
git diff main feature/ma-branche
```

### Version 3 : Forcer la synchronisation complète
```bash
git fetch origin                          # Récupérer tout
git reset --hard origin/main              # Écraser local avec le distant (DANGER!)
```

---

## 📋 CHECKLIST AVANT DE PUSH

```bash
☐ git status                     # Aucun fichier "untracked" oublié?
☐ git diff --cached              # Vérifier les changements staged
☐ git commit -m "Message clair"  # Message descriptif
☐ git log --oneline -1           # Vérifier le commit
☐ git push origin main           # Pousser
☐ Vérifier sur GitHub.com        # Confirmer le push
```

---

## 🚨 ERREURS COURANTES

| Erreur | Cause | Solution |
|--------|-------|----------|
| `src refspec main does not match` | Pas de commits | Faire `git add .` puis `git commit` |
| `Updates were rejected` | Conflit avec le distant | Faire `git pull origin main` d'abord |
| `fatal: not a git repository` | Pas dans un dossier git | Faire `git init` ou `git clone` |
| `permission denied` | SSH key non configurée | Configurer la clé SSH ou utiliser HTTPS |

---

## ⚡ COMMANDES UTILES AVANCÉES

```bash
git stash                         # Sauvegarder temporairement les changements
git stash pop                     # Récupérer les changements stashés
git cherry-pick <hash>            # Appliquer un commit spécifique
git tag v1.0.0                    # Créer une version/tag
git blame <fichier>               # Voir qui a modifié chaque ligne
git clean -fd                     # Supprimer les fichiers non-tracked
```

---

## 📌 RÉSUMÉ RAPIDE

```bash
# Au démarrage
git pull origin main

# Pendant le dev
git status
git add .
git commit -m "Message"

# Avant de partir
git push origin main
```

**✅ À retenir : Status → Diff → Add → Commit → Push → Vérifier**
