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
 * Highcharts helper methods
 *
 * @class		Highcharts
 * @namespace	Todoyu
 */
Todoyu.Highcharts = {

	/**
	 * Default graph colors
	 *
	 * @property	colors
	 * @type		Array
	 */
	colors: [
		'#56A356',	// green
		'#DA7373'	// red
	],



	/**
	 * Get rendering options for given type of highcharts chart
	 *
	 * @method	getChartOptions
	 * @param	{String}	chartType
	 * @param	{Object}	renderData
	 * @return	{Object}
	 */
	getChartOptions:	function(chartType, renderData) {
		var options;

			// Set graph specific config
		switch(chartType) {
			case 'area':
				options	= this.getAreaChartOptions(renderData);
				break;
			case 'column':
			default:
				options	= this.getColumnChartOptions(renderData);
				break;
		}

			// Set render data
		options.chart.renderTo	= renderData.renderTo;
		options.title			= {
			text: 	renderData.title,
			style:	{
				fontSize:	'12px',
				color:		'#5D7E9F',
				fontWeight:	'normal'
			}
		};
		options.xAxis.categories	= renderData.xAxisCategories;
		options.yAxis.title			= {
			align:	'low',
			style:	{
				color:		'#5D7E9F',
				fontWeight:	'normal'
			},
			x:		40,
			y:		100,
			text:	renderData.yAxisTitle
		};
		options.series		= renderData.series;

			// Set default render options
		options.chart.zoomType	= 'xy';
		options.chart.style		= { margin:	'-12px 0 0 0' };
		options.credits			= {	enabled:	false };
		options.colors			= this.colors;

		return options;
	},



	/**
	 * Get rendering options for highcharts chart type: 'stackedArea'
	 *
	 * @method	getAreaChartOptions
	 * @param	{Object}		renderData
	 * @return	{Object}
	 */
	getAreaChartOptions: function(renderData) {
		return {
			chart: { defaultSeriesType:	'area' },
			xAxis: {	// 31.8., 1.9., 2.9 ....
				alternateGridColor:	'#EEEEEE',		// light grey
				lineColor:			'#6D869F',		// blue (bottom line)
				tickColor:			'#6D869F',		// blue (ticks at bottom)
				tickPosition:		'outside',
				labels:	{
					x:			-6,
					y:			16,
					rotation:	90
				}
			},
			yAxis: {	// 8, 10, 8, 8, 0, ...
//				max:				12,		// disable to activate maximum auto-detection
				min:				0,
				labels:	{
					formatter: function() { return '' + this.value + 'h'; }
				}
			},
			tooltip: {
				 formatter: function() {
					return '' + this.x + ': ' + Highcharts.numberFormat(this.y, 0, ',') + ' h';
				 }
			},
			plotOptions: {
				area: {
					fillOpacity:	0.5,
					stacking:		'normal',
					lineColor:		'#666666',
					lineWidth:		1,
					marker: {
						lineWidth: 1,
						lineColor: '#666666'
					},
					dataLabels: {
						enabled:	true,
						color:		'#444444',
						rotation:	90,
						align:		'center',
						y:			20,
						formatter: function() {
							val	= this.y + '';
							val	= val.replace('.', ',');
							return val;
						}
					}
				}
			}
		};
	},



	/**
	 * Get rendering options for highcharts chart type: 'column'  
	 *
	 * @method	getColumnChartOptions
	 * @param	{Object}		renderData
	 * @return	{Object}
	 */
	getColumnChartOptions:	function(renderData) {
		return {
			chart:	{ defaultSeriesType:	'column' },
			legend: {
				layout:				'horizontal',
				x:					-100,
				y:					10,
				borderWidth:		1,
				backgroundColor:	'#FFFFFF'
			},
			xAxis: {
				alternateGridColor:	'#EEEEEE',		// light grey
				lineColor:			'#6D869F',		// blue (bottom line)
				tickColor:			'#6D869F',		// blue (ticks at bottom)
				tickPosition:		'outside',
				labels:	{
					x:			-6,
					y:			16,
					rotation:	90
				}
			},
			yAxis: {
				min:				0,
				max:				10,			// disable to activate maximum auto-detection
				alternateGridColor:	'#EBEEE2',	// light green
				tickmarkPlacement:	'on'
			},
			tooltip: { formatter: function() { return '' + this.x + ': ' + this.y + 'h'; } },
			plotOptions: {
				column: {
					pointPadding:	0,
					borderWidth:	0,
					dataLabels: {
							enabled:	true,
							rotation:	0,
							y:			30,
							style:	{
								color:		'#5D7E9F',
								fontWeight:	'normal'
							}
					}
				}
			}
		};
	}

};