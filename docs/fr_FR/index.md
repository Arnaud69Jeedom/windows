# Plugin template

Plugin permettant de créer une gestion des ouvrants et de déclencher des actions (commande ou scénario).

# Introduction

Pour éviter une augmentation de l'humidité relative, l'apparition de moisissures, de spores de champignons ou bien la rétention d'allergènes ou de produits chimiques polluants, les médecins recommandent d'aérer sa maison entre 15 et 30 minutes par jour. Mieux vaut le faire en plusieurs fois, en début ou en fin de jour, aux heures où la pollution extérieure est la plus faible.
Source: Futura Sciences

Il suffit d’ouvrir grand les fenêtres pendant 5 à 10 minutes par jour, le matin directement après votre réveil par exemple.
Certaines heures sont défavorables par rapport à la qualité de l’air, surtout en ville :
L’hiver, évitez d’ouvrir vos fenêtres entre 14h et 18h. Aérez votre logement entre 8h et 11h le matin ou entre 22h et minuit le soir.
Concernant l’été, évitez d’ouvrir vos fenêtres entre 11h et 17h. L'aération de votre logement est préférable entre 21h et 10h, quand l’air est le plus frais.
Source : CompteCO2

5 à 10 minutes, 2 à 3 fois par jour, suffisent pour faire entrer de l’air frais et sain et à évacuer l’air chaud et vicié. Sans perte de chaleur ! En si peu de temps, les murs n’ont pas le temps de refroidir, seul l’air circule. Mieux vaut ouvrir en grand 10 minutes que laisser une fenêtre ouverte en oscillo-battant toute la journée en hiver.
L’hiver, la pollution extérieure est au plus haut entre 14 h et 18h. Bref, évitez d’aérer à ce moment-là. Préférez le matin entre 8 h et 11 h ou le soir après 22 h, histoire de dormir dans un environnement sain.
Lorsqu’il fait plus chaud, comme en été, l’idéal est d’aérer entre 21h et 10h lorsque l’air est plus frais. On évite entre 11h et 17h.
Source : Engie 

# Règles retenues 

Les actions seront réalisées selone la saison.

HIVER :

    Fermer sur durée et temp. int < consigne
    Fermer si temp. int < temp. mini (consigne - seuil) quelque soit la durée

ETE :

    Ouvrir température ext < temp. int.
    Fermer sur durée

# Configuration du plugin

La configuration est très simple, après téléchargement du plugin, il vous suffit de l’activer et c’est tout.

# Configuration des équipements

La configuration des équipements Gestion Ouvrants est accessible à partir du menu Plugins puis Confort.

Une fois dessus vous retrouvez alors la liste de vos Ouvrants.

## Général

Vous retrouvez ici toute la configuration de votre équipement :

    Nom de l’équipement : nom de votre pièce.
    Objet parent : indique l’objet parent auquel appartient l’équipement.
    Catégorie : les catégories de l’équipement (il peut appartenir à plusieurs catégories).
    Activer : permet de rendre votre équipement actif.
    Visible : le rend visible sur le dashboard.
    
## Informations

Cet onglet récapitules les informations sur la nécessité d'aérer. Il donne aussi des conseils sur la durée et les horaires.

## Sondes

Listes des sondes pour suivre la santé de la pièce

    Température extérieure : Commande pour la température extérieure
    Température intérieure : Commande pour la température intérieure

    Présence : ce champ est optionnel
        Si le champs est non renseigné : le plugin vérifiera toujours l'état des ouvertures
        Si le champs est renseigne : le plugin vérifiera uniquement si la présence est à 1
    
    Durée hiver : durée souhaité d'aération en hiver (5 minutes par exemple)
    Durée été : durée souhaité d'aération en été (5 minutes par exemple)
    Notifier : permet d'envoyer une notification s'il faut ouvrir ou fermer une fenêtre

    Calcul sur température : ce champ est optionnel
        Le but est de garder la pièce dans une température acceptable. i.e : garder la pièce au alenture de la température de consigne d'un thermostat
        Consigne Thermostat : consigne du thermostat
        Seuil hiver : dépassement acceptable de la température de la pièce par rapport à la consigne
        Seuil été : A VOIR
    
    Température Saison : section optionnelle
        Le plugin cherche à réguler la température par rapport à la saison
        La saison est calculé par rapport à la date, ou mieux selon la température maxi selon la météo.
        En effet, il peut y avoir des périodes plus chaude en hiver, et la règle doit s'adapter pour profiter de ces moments plus chaud.

        Température maxi : Commande du plugin météo indiquant la température maximum du jour
        Température hiver (°C) : température minimum indiquant que le calcul passe sur le mode Hiver
        Température été (°C) : température minimum pour l'Eté

        S'il n'y a pas de température renseignée, alors le calcul se base sur les dates :
        Hivers : jour compris entre le 21 septembre et le 21 mars (période plus ou moins fraiche)
        Eté : jour entre le 21 juin et le 21 septembre (période plus ou moins chaude)
        Sinon on prend les règle de la saison intermédiaire

## Ouvertures

Listes des fenêtres à suivre.
    
    1 sera consédéré comme fermé,
    0 comme ouvert


## Actions

Actions et scénario à déclencher
Lorsque le plugin detectera qu'il serait bien d'ouvrir ou de fermer une fenêtre, alors les actions seront déclenchées.
Il est possible d'utiliser un scénario ou des commandes.
Il est possible d'utiliser des commandes de type PushBullet. Il est alors demandé un Titre et un Message

Actuellement, il existe quelques variables qui peuvent être utilisées :
    
    #name# = Nom de l'objet
    #message# = Message à afficher  = 'il faut ouvrir' ou 'il faut fermer'
    #temperature_indoor# = température intérieure
    #parent# = nom de l'objet parent (la pièce par exemple) 


## Commandes

Commandes créées pour voir des informations :

    Etat : affiche 1 si une action est souhaitée, 0 sinon
    Rafraichir : relance le calcul
    Compteur : Temps d'aération dans la journée

