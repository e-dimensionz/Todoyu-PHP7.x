// Calendar FR language
// Author: Mihai Bazon, <mihai_bazon@yahoo.com>
// Encoding: UTF-8
// Translator: André Liechti, <developer@sysco.ch> (2006-01-04) from scratch for version 1.x

// full day names
Calendar._DN = new Array
("Dimanche",
 "Lundi",
 "Mardi",
 "Mercredi",
 "Jeudi",
 "Vendredi",
 "Samedi",
 "Dimanche");

// short day names
Calendar._SDN = new Array
("Dim",
 "Lun",
 "Mar",
 "Mer",
 "Jeu",
 "Ven",
 "Sam",
 "Dim");
 
// full month names
Calendar._MN = new Array
("Janvier",
 "Février",
 "Mars",
 "Avril",
 "Mai",
 "Juin",
 "Juillet",
 "Août",
 "Septembre",
 "Octobre",
 "Novembre",
 "Décembre");

// short month names
Calendar._SMN = new Array
("Jan",
 "Fév",
 "Mar",
 "Avr",
 "Mai",
 "Juin",
 "Juil",
 "Aoû",
 "Sep",
 "Oct",
 "Nov",
 "Déc");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "À propos du calendrier";

Calendar._TT["ABOUT"] =
"Sélecteur DHTML de date/heure\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this ;-)
"http://www.dynarch.com/projects/calendar\n\n" +
"Sélection de la date:\n" +
"- Utiliser les boutons \xab, \xbb pour sélectionner l'année\n" +
"- Utiliser les boutons " + String.fromCharCode(0x2039) + ", " + String.fromCharCode(0x203a) + " pour sélectionner le mois\n" +
"- En conservant pressé le bouton de la souris sur l'un de ces boutons, la sélection devient plus rapide.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Sélection de l\'heure:\n" +
"- Cliquer sur l'une des parties du temps pour l'augmenter\n" +
"- ou Majuscule + Clic pour le diminuer\n" +
"- ou faire un Cliquer-Déplacer horizontal pour une modification plus rapide.";

Calendar._TT["PREV_YEAR"] = "Année préc. (maintenir pour afficher menu)";
Calendar._TT["PREV_MONTH"] = "Mois préc. (maintenir pour afficher menu)";
Calendar._TT["GO_TODAY"] = "Atteindre la date du jour";
Calendar._TT["NEXT_MONTH"] = "Mois suiv. (maintenir pour afficher menu)";
Calendar._TT["NEXT_YEAR"] = "Année suiv. (maintenir pour afficher menu)";
Calendar._TT["SEL_DATE"] = "Sélectionner une date";
Calendar._TT["DRAG_TO_MOVE"] = "Glisser pour déplacer";
Calendar._TT["PART_TODAY"] = " (aujourd'hui)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Afficher %s en premier";

// Locale-dependent. It specifies the week-end days, as an array of comma-separated numbers.
// The numbers are from 0 to 6 : 0 = Sunday, 1 = Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Fermer";
Calendar._TT["TODAY"] = "Aujourd'hui";
Calendar._TT["TIME_PART"] = "(MAJ+)Clic, ou glisser pour changer la valeur";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%d.%m.%Y";
Calendar._TT["TT_DATE_FORMAT"] = "%A, %e %B";

Calendar._TT["WK"] = "Sem.";
Calendar._TT["TIME"] = "Heure:";
