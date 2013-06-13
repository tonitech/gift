$(function() {
	$('#postArticle').click(function(){
		var title = $('#title').val();
		var content = $('#article-content').val();
		$.ajax({
			url : '/bbs/post-article',
			dataType : 'json',
			data : {'title':title, 'content':content},
			success : function (data) {
				if (data.errorcode == 0) {
					alert('发帖成功！');
					setTimeout(function(){
						window.location.reload();
					}, 1000); //指定1秒刷新一次 
				} else if (data.errorcode == -1) {
					$('#loginBox').popup();
				}
			}
		});
	});
});