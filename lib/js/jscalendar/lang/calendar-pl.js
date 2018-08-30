// ** I18N
// Calendar PL language
// Author: Artur Filipiak, <imagen@poczta.fm>
// January, 2004
// Encoding: UTF-8
Calendar._DN = new Array
("Niedziela", "Poniedzia\u0142ek", "Wtorek", "\u015aroda", "Czwartek", "Pi\u0105tek", "Sobota", "Niedziela");

Calendar._SDN = new Array
("N", "Pn", "Wt", "\u015ar", "Cz", "Pt", "So", "N");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 0;

Calendar._MN = new Array
("Stycze\u0144", "Luty", "Marzec", "Kwiecie\u0144", "Maj", "Czerwiec", "Lipiec", "Sierpie\u0144", "Wrzesie\u0144", "Pa\u017adziernik", "Listopad", "Grudzie\u0144");

Calendar._SMN = new Array
("Sty", "Lut", "Mar", "Kwi", "Maj", "Cze", "Lip", "Sie", "Wrz", "Pa\u017a", "Lis", "Gru");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "O kalendarzu";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"For latest version visit: http://www.dynarch.com/projects/calendar/\n" +
"Distributed under GNU LGPL.  See http://gnu.org/licenses/lgpl.html for details." +
"\n\n" +
"Wyb\u00f3r daty:\n" +
"- aby wybra\u0107 rok u\u017cyj przycisk\u00f3w \u00ab, \u00bb\n" +
"- aby wybra\u0107 miesi\u0105c u\u017cyj przycisk\u00f3w \u2039, \u203a\n" +
"- aby przyspieszy\u0107 wyb\u00f3r przytrzymaj wci\u015bni\u0119ty przycisk myszy nad ww. przyciskami.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Wyb\u00f3r czasu:\n" +
"- aby zwi\u0119kszy\u0107 warto\u015b\u0107 kliknij na dowolnym elemencie selekcji czasu\n" +
"- aby zmniejszy\u0107 warto\u015b\u0107 u\u017cyj dodatkowo klawisza Shift\n" +
"- mo\u017cesz r\u00f3wnie\u017c porusza\u0107 myszk\u0119 w lewo i prawo wraz z wci\u015bni\u0119tym lewym klawiszem.";

Calendar._TT["PREV_YEAR"] = "Poprz. rok (przytrzymaj dla menu)";
Calendar._TT["PREV_MONTH"] = "Poprz. miesi\u0105c (przytrzymaj dla menu)";
Calendar._TT["GO_TODAY"] = "Poka\u017c dzi\u015b";
Calendar._TT["NEXT_MONTH"] = "Nast. miesi\u0105c (przytrzymaj dla menu)";
Calendar._TT["NEXT_YEAR"] = "Nast. rok (przytrzymaj dla menu)";
Calendar._TT["SEL_DATE"] = "Wybierz dat\u0119";
Calendar._TT["DRAG_TO_MOVE"] = "Przesu\u0144 okienko";
Calendar._TT["PART_TODAY"] = " (dzi\u015b)";
Calendar._TT["MON_FIRST"] = "Poka\u017c Poniedzia\u0142ek jako pierwszy";
Calendar._TT["SUN_FIRST"] = "Poka\u017c Niedziel\u0119 jako pierwsz\u0105";
Calendar._TT["CLOSE"] = "Zamknij";
Calendar._TT["TODAY"] = "Dzi\u015b";
Calendar._TT["TIME_PART"] = "(Shift-)klik | drag, aby zmieni\u0107 warto\u015b\u0107";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y.%m.%d";
Calendar._TT["TT_DATE_FORMAT"] = "%a, %b %e";

Calendar._TT["WK"] = "wk";

// This still need to be translated.
// Copied from English to make eveything work
Calendar._TT["DAY_FIRST"] = "Display %s first";
Calendar._TT["TIME"] = "Time:";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";
