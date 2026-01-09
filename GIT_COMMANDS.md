# Git : commandes courantes pour pousser le projet

## Config de base (une fois par machine)
- git config --global user.name "Votre Nom"
- git config --global user.email "vous@example.com"

## Cycle classique
- git status                       # Voir les fichiers modifies / en attente
- git add .                        # Ajouter tous les fichiers modifies au commit
- git commit -m "Votre message"    # Creer un commit avec message
- git push origin main             # Pousser le commit sur la branche main distante

## Recuperer / synchroniser
- git pull origin main             # Recuperer les dernieres modifications

## Branches
- git branch                       # Lister les branches locales
- git checkout -b feature/nom      # Creer et basculer sur une nouvelle branche
- git checkout main                # Revenir sur main
- git push origin feature/nom      # Pousser une branche feature

## Inspection
- git log --oneline --graph --decorate --all   # Historique resumee
- git diff                                   # Voir les differences non ajoutees
- git diff --cached                          # Voir les differences ajoutees (staged)

## Remettre au propre (local)
- git restore <fichier>           # Annuler changements non stages d'un fichier
- git restore --staged <fichier>  # Retirer un fichier du stage

## Cloner un depot distant
- git clone <url_du_repo>

Astuce : toujours faire `git status` avant et apres vos commandes pour verifier l'etat du depot.
