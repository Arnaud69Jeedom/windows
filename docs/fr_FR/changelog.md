# Changelog plugin Gestion Ouvrants

>**IMPORTANT**
>
>Pour rappel s'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 10/06/2022

DEBUG : Recherche de la température maximum : max de la Température maxi et de la Température extérieure de la journée si elle est historisée. Permet de savoir que la journée a été chaude alors qu'en soirée, la température maxi donne le maxi de la nuit à venir

# 06/06/2022

Ajout d'une condition : Eviter par exemple les actions la nuit

# 04/06/2022

DEBUG : Pas de message pour fermer si on vient juste d'ouvrir
DEBUG : Pas de message pour fermer si on ouvre depuis longtemps mais qu'il fait plus frais dehors que dedans

# 12/05/2022

Gestion du COV (Composés Organiques Volatils)

# 11/05/2022

Amélioration de la gestion en été

# 14/04/2022

Ajout d'une température cible.

# 27/03/2022

DEBUG : Correction sur gestion de l'intersaison

# 20/03/2022

Réécriture pour la gestion des actions pour Hiver et Intersaison

# 10/03/2022

ADD : Gestion du niveau de CO2

# 08/03/2022

DEBUG : Consigne optionnelle

# 17/02/2022

Gestion de la température hivers et été par défaut si non renseigné (13°C et 25°C)

# 10/02/2022

Durée d'ouverture gérée en option. Durée = 0
Ajout de la raison dans les notifications & actions
Correction sur gestion de la saison intermédiaire

# 09/02/2022

Ajout du temps cumulé d'ouverture sur la journée (nécessite que l'état de la fenêtre soit historisé)

# 29/01/2022

Réecriture du code pour faciliter la maintenance
Execution des actions toutes les 5 minutes (non paramétrable)

# 06/01/2022

Correction sur la commande Etat

# 28/12/2021

Paramétrage en commun déplacé dans la configuration du plugin

# 02/12/2021

- Ajout de #message#  et de #temperature_indoor# avec unité

# 23/11/2021

- Gestion du temps cumulé d'aération d'une pièce dans la journée

# 16/11/2021

- Première version
