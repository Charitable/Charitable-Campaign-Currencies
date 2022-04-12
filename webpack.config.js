/**
 * External dependencies.
 */
 const path = require('path');
 const defaultConfig = require('@wordpress/scripts/config/webpack.config');
 const webpackConfig = {
	 ...defaultConfig,
	 entry: {
		'charitable-campaign-currencies': './assets/js/index.js'
	 },
	 output: {
		 path: path.resolve( __dirname, './dist/' ),
		 filename: `[name].js`
	 },
 };

 module.exports = webpackConfig;
