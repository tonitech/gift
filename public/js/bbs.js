$(function() {
	$('#postArticle').click(function(){
		var title = $('#title').val();
		var content = $('#article-content').val();
		if (title == '') {
			alert('请输入标题！');
			return false;
		}
		if (content == '') {
			alert('请输入内容！');
			return false;
		}
		$.ajax({
			type : 'post',
			url : '/bbs/post-article',
			dataType : 'json',
			data : {'title':title, 'content':content},
			success : function (data) {
				if (data.errorcode == 0) {
					alert('发帖成功！');
					setTimeout(function(){
						window.location.reload();
					}, 1000); // 指定1秒刷新一次
				} else if (data.errorcode == -1) {
					$('#loginBox').popup();
				}
			}
		});
	});
	
	$('#postReply').click(function(){
		var articleid = $('#articleid').val();
		var articleauthor = $('#articleauthor').val();
		var replyto = $('#replyto').val();
		var content = $('#article-content').val();
		$.ajax({
			url : '/bbs/reply-article',
			type : 'post',
			dataType : 'json',
			data : {'articleid':articleid, 'articleauthor':articleauthor, 'replyto':replyto, 'content':content},
			success : function (data) {
				if (data.errorcode == 0) {
					alert('发帖成功！');
					setTimeout(function(){
						window.location.reload();
					}, 1000); // 指定1秒刷新一次
				} else if (data.errorcode == -1) {
					$('#loginBox').popup();
				}
			}
		});
	});
	
	$('.p_reply').click(function(){
		if ($(this).parent().siblings('.core_reply_wrapper').css('display') == 'block') {
			$(this).find('a').html('回复');
		} else {
			$(this).find('a').html('收起回复');
		}
		$(this).parent().siblings('.core_reply_wrapper').slideToggle();
	});
	
	$('.lzl_s_r').click(function(){
		if ($(this).parents('ul').siblings('.lzl_editor_container').css('display') == 'block') {
			
		} else {
			$(this).parents('ul').siblings('.lzl_editor_container').slideToggle();
		}
	});
	
	$('.j_lzl_p').click(function(){
		$(this).parents('ul').siblings('.lzl_editor_container').slideToggle();
	});
});
