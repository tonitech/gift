//@charset "utf-8"
define(['models/Article'], function(Article) {
	var ArticleList = Backbone.Collection.extend({
		model: Article
	});
	return ArticleList;
});
