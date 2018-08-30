/****************************************************************************
 * todoyu is published under the BSD License:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * Copyright (c) 2012, snowflake productions GmbH, Switzerland
 * All rights reserved.
 *
 * This script is part of the todoyu project.
 * The todoyu project is free software; you can redistribute it and/or modify
 * it under the terms of the BSD License.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
 * for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script.
 *****************************************************************************/

/**
 * @module	Core
 */

/**
 *	Todoyu string functions
 *
 * @class		String
 * @namespace	Todoyu
 * @type		{Object}
 */
Todoyu.String = {

	/**
	 * Replace all occurrences of the search string with the replacement string
	 *
	 * @method	replaceAll
	 * @param	{String}	txt
	 * @param	{String}	search
	 * @param	{String}	replace
	 * @return	{String}
	 */
	replaceAll: function(txt, search, replace) {
	  return txt.replace(new RegExp(search, 'g'), replace);
	},



	/**
	 * Crop text to requested length
	 *
	 * @method	cropText
	 * @param	{String}		text
	 * @param	{Number=100}	[length]
	 * @param	{String}		[append]		Appendix
	 */
	cropText: function(text, length, append) {
		length	= length || 100;
		append	= append === undefined ? '...' : append;

		if( text.length <= length ) {
			return text;
		} else {
			return text.substr(0, length-append.length) + append;
		}
	},



	/**
	 * Get a key from a class with the specific prefix
	 *
	 * @method	getClassKey
	 * @param	{Element}		element
	 * @param	{String}		prefix
	 */
	getClassKey: function(element, prefix) {
		var keyClass = $w($(element).className).detect(function(className){
			return className.startsWith(prefix);
		});

		return keyClass ? keyClass.replace(prefix, '').toLowerCase() : '';
	},



	/**
	 * Wraps buffer to selected number of characters using string break char,
	 * while keeping HTML entities intact
	 *
	 * @method	wordwrapEntities
	 * @param	{String}		str
	 * @param	{Number}		int_width
	 * @param	{String}		str_break
	 * @param	{Boolean}		cut
	 * @return	{String}
	 */
	wordwrapEntities: function(str, int_width, str_break, cut) {
		str		= this.html_entity_decode(str);
		str		= this.wordwrap(str, int_width, str_break, cut);
		str		= this.htmlentities(str);

		return str;
	},



	/**
	 * Wraps buffer to selected number of characters using string break char
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @method	wordwrap
	 * @param	{String}		str
	 * @param	{Number}		int_width
	 * @param	{String}		str_break
	 * @param	{Boolean}		cut
	 * @return	{String}
	 */
	wordwrap: function(str, int_width, str_break, cut) {
		var m = (arguments.length >= 2) ? arguments[1] : 75;
		var b = (arguments.length >= 3) ? arguments[2] : "\n";
		var c = (arguments.length >= 4) ? arguments[3] : false;

		var i, j, l, s, r;

		str += '';

		if( m < 1 ) {
			return str;
		}

		for (i = -1, l = (r = str.split(/\r\n|\n|\r/)).length; ++i < l; r[i] += s) {
			for (s = r[i], r[i] = ""; s.length > m; r[i] += s.slice(0, j) + ((s = s.slice(j)).length ? b : "")){
				j = c == 2 || (j = s.slice(0, m + 1).match(/\S*(\s)?$/))[1] ? m : j.input.length - j[0].length || c == 1 && m || j.input.length + (j = s.slice(m).match(/^\S*/)).input.length;
			}
		}

		return r.join("\n");
	},



	/**
	 * Convert all applicable characters to HTML entities
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/htmlentities
	 * version: 1009.2513
	 *
	 * @method	htmlentities
	 * @param	{String}		string
	 * @param	{String}		quote_style
	 */
	htmlentities:function(string, quote_style) {
		var hash_map = {}, symbol = '', tmp_str = '', entity = '';
		tmp_str = string.toString();

		if( false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style)) ) {
				return false;
		}
		hash_map["'"] = '&#039;';
		for (symbol in hash_map) {
			entity = hash_map[symbol];
			tmp_str = tmp_str.split(symbol).join(entity);
		}

		return tmp_str;
	},



	/**
	 * Convert all HTML entities to their applicable characters
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @method	html_entity_decode
	 * @param	{String}	string
	 * @param	{String}	quote_style
	 */
	html_entity_decode:function(string, quote_style) {
		var hash_map = {}, symbol = '', tmp_str = '', entity = '';
		tmp_str = string.toString();

		if( false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style)) ) {
				return false;
		}

		// fix &amp; problem
		// http://phpjs.org/functions/get_html_translation_table:416#comment_97660
		delete(hash_map['&']);
		hash_map['&'] = '&amp;';

		for (symbol in hash_map) {
			entity = hash_map[symbol];
			tmp_str = tmp_str.split(entity).join(symbol);
		}
		tmp_str = tmp_str.split('&#039;').join("'");

		return tmp_str;
	},



	/**
	 * Returns the internal translation table used by htmlspecialchars and htmlentities
	 *
	 * Borrowed from phpjs  http://phpjs.org/functions/wordwrap
	 * version: 1009.2513
	 *
	 * @method	get_html_translation_table
	 * @param	{String}	table
	 * @param	{String}	quote_style
	 */
	get_html_translation_table:function(table, quote_style) {
		var entities = {}, hash_map = {}, decimal = 0, symbol = '';
		var constMappingTable = {}, constMappingQuoteStyle = {};
		var useTable = {}, useQuoteStyle = {};

			// Translate arguments
		constMappingTable[0]		= 'HTML_SPECIALCHARS';
		constMappingTable[1]		= 'HTML_ENTITIES';
		constMappingQuoteStyle[0]	= 'ENT_NOQUOTES';
		constMappingQuoteStyle[2]	= 'ENT_COMPAT';
		constMappingQuoteStyle[3]	= 'ENT_QUOTES';

		useTable		= ! isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
		useQuoteStyle	= ! isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

		if( useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES' ) {
			throw new Error('Table: ' + useTable + ' not supported');
			// return false;
		}

		entities['38'] = '&amp;';

		if( useTable === 'HTML_ENTITIES' ) {
			entities['160'] = '&nbsp;';
			entities['161'] = '&iexcl;';
			entities['162'] = '&cent;';
			entities['163'] = '&pound;';
			entities['164'] = '&curren;';
			entities['165'] = '&yen;';
			entities['166'] = '&brvbar;';
			entities['167'] = '&sect;';
			entities['168'] = '&uml;';
			entities['169'] = '&copy;';
			entities['170'] = '&ordf;';
			entities['171'] = '&laquo;';
			entities['172'] = '&not;';
			entities['173'] = '&shy;';
			entities['174'] = '&reg;';
			entities['175'] = '&macr;';
			entities['176'] = '&deg;';
			entities['177'] = '&plusmn;';
			entities['178'] = '&sup2;';
			entities['179'] = '&sup3;';
			entities['180'] = '&acute;';
			entities['181'] = '&micro;';
			entities['182'] = '&para;';
			entities['183'] = '&middot;';
			entities['184'] = '&cedil;';
			entities['185'] = '&sup1;';
			entities['186'] = '&ordm;';
			entities['187'] = '&raquo;';
			entities['188'] = '&frac14;';
			entities['189'] = '&frac12;';
			entities['190'] = '&frac34;';
			entities['191'] = '&iquest;';
			entities['192'] = '&Agrave;';
			entities['193'] = '&Aacute;';
			entities['194'] = '&Acirc;';
			entities['195'] = '&Atilde;';
			entities['196'] = '&Auml;';
			entities['197'] = '&Aring;';
			entities['198'] = '&AElig;';
			entities['199'] = '&Ccedil;';
			entities['200'] = '&Egrave;';
			entities['201'] = '&Eacute;';
			entities['202'] = '&Ecirc;';
			entities['203'] = '&Euml;';
			entities['204'] = '&Igrave;';
			entities['205'] = '&Iacute;';
			entities['206'] = '&Icirc;';
			entities['207'] = '&Iuml;';
			entities['208'] = '&ETH;';
			entities['209'] = '&Ntilde;';
			entities['210'] = '&Ograve;';
			entities['211'] = '&Oacute;';
			entities['212'] = '&Ocirc;';
			entities['213'] = '&Otilde;';
			entities['214'] = '&Ouml;';
			entities['215'] = '&times;';
			entities['216'] = '&Oslash;';
			entities['217'] = '&Ugrave;';
			entities['218'] = '&Uacute;';
			entities['219'] = '&Ucirc;';
			entities['220'] = '&Uuml;';
			entities['221'] = '&Yacute;';
			entities['222'] = '&THORN;';
			entities['223'] = '&szlig;';
			entities['224'] = '&agrave;';
			entities['225'] = '&aacute;';
			entities['226'] = '&acirc;';
			entities['227'] = '&atilde;';
			entities['228'] = '&auml;';
			entities['229'] = '&aring;';
			entities['230'] = '&aelig;';
			entities['231'] = '&ccedil;';
			entities['232'] = '&egrave;';
			entities['233'] = '&eacute;';
			entities['234'] = '&ecirc;';
			entities['235'] = '&euml;';
			entities['236'] = '&igrave;';
			entities['237'] = '&iacute;';
			entities['238'] = '&icirc;';
			entities['239'] = '&iuml;';
			entities['240'] = '&eth;';
			entities['241'] = '&ntilde;';
			entities['242'] = '&ograve;';
			entities['243'] = '&oacute;';
			entities['244'] = '&ocirc;';
			entities['245'] = '&otilde;';
			entities['246'] = '&ouml;';
			entities['247'] = '&divide;';
			entities['248'] = '&oslash;';
			entities['249'] = '&ugrave;';
			entities['250'] = '&uacute;';
			entities['251'] = '&ucirc;';
			entities['252'] = '&uuml;';
			entities['253'] = '&yacute;';
			entities['254'] = '&thorn;';
			entities['255'] = '&yuml;';
		}

		if( useQuoteStyle !== 'ENT_NOQUOTES' ) {
			entities['34'] = '&quot;';
		}
		if( useQuoteStyle === 'ENT_QUOTES' ) {
			entities['39'] = '&#39;';
		}
		entities['60'] = '&lt;';
		entities['62'] = '&gt;';

			// ASCII decimals to real symbols
		for(decimal in entities) {
			symbol = String.fromCharCode(decimal);
			hash_map[symbol] = entities[decimal];
		}

		return hash_map;
	},



	/**
	 * Uppercase the first character of every word in a string
	 *
	 * @method	ucwords
	 * @param	{String}	str
	 * @return	{String}
	 */
	ucwords: function(str) {
		return (str + '').replace(/^(.)|\s(.)/g, function ($1) {
			return $1.toUpperCase();
		});
	},



	/**
	 * Get amount of lines in given string
	 *
	 * @method	countLines
	 * @param	{String}	multiLineText
	 * @return	{Number}
	 */
	countLines: function(multiLineText) {
		return multiLineText.split("\n").length;
	},



	/**
	 * Convert to 2-digit value (possibly add leading zero)
	 *
	 * @method	twoDigit
	 * @param	{String|Number}		number
	 * @return	{String}
	 */
	twoDigit: function(number) {
		number = parseInt(number, 10);

		if( number < 10 ) {
			number = '0' + number;
		}

		return number;
	},



	/**
	 * Replace count number in string
	 * The count number has to be wrapper in braces
	 * Example: Label (3)
	 *
	 * @param	{String}	label
	 * @param	{Number}	newCount
	 * @return	{String}
	 */
	replaceCounter: function(label, newCount) {
		var pattern	= /\(\d+\)/;
		var replace	= '(' + newCount + ')';

		return label.replace(pattern, replace);
	},



	/**
	 * Get counter value (integer part of the label which is in braces)
	 *
	 * @param	{String}	label
	 * @return	{Number}
	 */
	getCounter: function(label) {
		var pattern	= /\((\d+)\)/;
		var match	= label.match(pattern);

		return match[1] ? match[1] : 0;
	}

};