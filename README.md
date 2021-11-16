# JeedomWindows

Plugin pour Jeedom permettant la gestion des ouvrants.


# Informations
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

# Règles de calcul
Les actions seront réalisées selone la saison.
HIVER :

    Fermer sur durée et temp. int < consigne
    Fermer si temp. int < temp. mini (consigne - seuil) quelque soit la durée


ETE :

    Ouvrir température ext < temp. int.
    Fermer sur durée

