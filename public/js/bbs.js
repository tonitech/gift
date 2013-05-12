require.config({
	baseUrl: '/public/js',
	paths: {
		'lib': 'lib/'
	}
});

require(['backbone-min.js', 'collections/ArticleList'], function(Backbone) {
});
