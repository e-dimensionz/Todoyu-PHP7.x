// ** I18N

// Calendar KO (Korean) language
// Author: Mihai Bazon, <mihai_bazon@yahoo.com>
// Translation: Yourim Yi <yyi@yourim.net>
// Encoding: ASCII with \uXXXX unicode escapes
// lang : ko
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names

Calendar._DN = new Array
("\uc77c\uc694\uc77c",
 "\uc6d4\uc694\uc77c",
 "\ud654\uc694\uc77c",
 "\uc218\uc694\uc77c",
 "\ubaa9\uc694\uc77c",
 "\uae08\uc694\uc77c",
 "\ud1a0\uc694\uc77c",
 "\uc77c\uc694\uc77c");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("\uc77c",
 "\uc6d4",
 "\ud654",
 "\uc218",
 "\ubaa9",
 "\uae08",
 "\ud1a0",
 "\uc77c");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 0;

// full month names
Calendar._MN = new Array
("1\uc6d4",
 "2\uc6d4",
 "3\uc6d4",
 "4\uc6d4",
 "5\uc6d4",
 "6\uc6d4",
 "7\uc6d4",
 "8\uc6d4",
 "9\uc6d4",
 "10\uc6d4",
 "11\uc6d4",
 "12\uc6d4");

// short month names
Calendar._SMN = new Array
("1",
 "2",
 "3",
 "4",
 "5",
 "6",
 "7",
 "8",
 "9",
 "10",
 "11",
 "12");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "calendar \uc5d0 \ub300\ud574\uc11c";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Author: Mihai Bazon\n" + // don't translate this this ;-)
"\n"+
"\ucd5c\uc2e0 \ubc84\uc804\uc744 \ubc1b\uc73c\uc2dc\ub824\uba74 http://www.dynarch.com/projects/calendar/ \uc5d0 \ubc29\ubb38\ud558\uc138\uc694\n" +
"\n"+
"GNU LGPL \ub77c\uc774\uc13c\uc2a4\ub85c \ubc30\ud3ec\ub429\ub2c8\ub2e4. \n"+
"\ub77c\uc774\uc13c\uc2a4\uc5d0 \ub300\ud55c \uc790\uc138\ud55c \ub0b4\uc6a9\uc740 http://gnu.org/licenses/lgpl.html \uc744 \uc77d\uc73c\uc138\uc694." +
"\n\n" +
"\ub0a0\uc9dc \uc120\ud0dd:\n" +
"- \uc5f0\ub3c4\ub97c \uc120\ud0dd\ud558\ub824\uba74 \u00ab, \u00bb \ubc84\ud2bc\uc744 \uc0ac\uc6a9\ud569\ub2c8\ub2e4\n" +
"- \ub2ec\uc744 \uc120\ud0dd\ud558\ub824\uba74 \u2039, \u203a \ubc84\ud2bc\uc744 \ub204\ub974\uc138\uc694\n" +
"- \uacc4\uc18d \ub204\ub974\uace0 \uc788\uc73c\uba74 \uc704 \uac12\ub4e4\uc744 \ube60\ub974\uac8c \uc120\ud0dd\ud558\uc2e4 \uc218 \uc788\uc2b5\ub2c8\ub2e4.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"\uc2dc\uac04 \uc120\ud0dd:\n" +
"- \ub9c8\uc6b0\uc2a4\ub85c \ub204\ub974\uba74 \uc2dc\uac04\uc774 \uc99d\uac00\ud569\ub2c8\ub2e4\n" +
"- Shift \ud0a4\uc640 \ud568\uaed8 \ub204\ub974\uba74 \uac10\uc18c\ud569\ub2c8\ub2e4\n" +
"- \ub204\ub978 \uc0c1\ud0dc\uc5d0\uc11c \ub9c8\uc6b0\uc2a4\ub97c \uc6c0\uc9c1\uc774\uba74 \uc880 \ub354 \ube60\ub974\uac8c \uac12\uc774 \ubcc0\ud569\ub2c8\ub2e4.\n";

Calendar._TT["PREV_YEAR"] = "\uc9c0\ub09c \ud574 (\uae38\uac8c \ub204\ub974\uba74 \ubaa9\ub85d)";
Calendar._TT["PREV_MONTH"] = "\uc9c0\ub09c \ub2ec (\uae38\uac8c \ub204\ub974\uba74 \ubaa9\ub85d)";
Calendar._TT["GO_TODAY"] = "\uc624\ub298 \ub0a0\uc9dc\ub85c";
Calendar._TT["NEXT_MONTH"] = "\ub2e4\uc74c \ub2ec (\uae38\uac8c \ub204\ub974\uba74 \ubaa9\ub85d)";
Calendar._TT["NEXT_YEAR"] = "\ub2e4\uc74c \ud574 (\uae38\uac8c \ub204\ub974\uba74 \ubaa9\ub85d)";
Calendar._TT["SEL_DATE"] = "\ub0a0\uc9dc\ub97c \uc120\ud0dd\ud558\uc138\uc694";
Calendar._TT["DRAG_TO_MOVE"] = "\ub9c8\uc6b0\uc2a4 \ub4dc\ub798\uadf8\ub85c \uc774\ub3d9 \ud558\uc138\uc694";
Calendar._TT["PART_TODAY"] = " (\uc624\ub298)";
Calendar._TT["MON_FIRST"] = "\uc6d4\uc694\uc77c\uc744 \ud55c \uc8fc\uc758 \uc2dc\uc791 \uc694\uc77c\ub85c";
Calendar._TT["SUN_FIRST"] = "\uc77c\uc694\uc77c\uc744 \ud55c \uc8fc\uc758 \uc2dc\uc791 \uc694\uc77c\ub85c";
Calendar._TT["CLOSE"] = "\ub2eb\uae30";
Calendar._TT["TODAY"] = "\uc624\ub298";
Calendar._TT["TIME_PART"] = "(Shift-)\ud074\ub9ad \ub610\ub294 \ub4dc\ub798\uadf8 \ud558\uc138\uc694";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%Y-%m-%d";
Calendar._TT["TT_DATE_FORMAT"] = "%b/%e [%a]";

Calendar._TT["WK"] = "\uc8fc";

// Added by Dan Lipofsky just to get it working, but it still needs translation
Calendar._TT["DAY_FIRST"] = "Display %s first";
Calendar._TT["TIME"] = "Time:";
Calendar._TT["WEEKEND"] = "0,6";
