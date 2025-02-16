const path = require('path')
const { VueLoaderPlugin } = require('vue-loader')
const BundleAnalyzerPlugin = require('@bundle-analyzer/webpack-plugin')

const config = {
	entry: path.join(__dirname, 'src', 'main.js'),
	output: {
		path: path.resolve(__dirname, './js'),
		publicPath: '/js/',
		filename: `twigs.js`,
		chunkFilename: 'chunks/[name]-[hash].js',
	},
	module: {
		rules: [
			{
				test: /\.css$/,
				use: ['vue-style-loader', 'css-loader'],
			},
			{
				test: /\.(js|vue)$/,
				use: 'eslint-loader',
				exclude: /node_modules/,
				enforce: 'pre',
			},
			{
				test: /\.vue$/,
				loader: 'vue-loader',
				exclude: /node_modules/,
			},
			{
				test: /\.js$/,
				loader: 'babel-loader',
				exclude: /node_modules/,
			},
		],
	},
	plugins: [
		new VueLoaderPlugin(),
	],
	resolve: {
		extensions: ['*', '.js', '.vue'],
		symlinks: false,
	},
}

if (process.env.BUNDLE_ANALYZER_TOKEN) {
	config.plugins.push(new BundleAnalyzerPlugin({ token: process.env.BUNDLE_ANALYZER_TOKEN }))
}

module.exports = config
