$(function() {
	$('#pub_submit').click(
			function() {
				var goodsId = $('#goodsId').val();
				var goodsAuthor = $('#goodsAuthor').val();
				var content = $(this).parent().siblings('.pub_area_bd').find(
						'textarea').val();
				$.ajax({
					url : '/goods/comment-goods',
					type : 'post',
					dataType : 'json',
					data : {
						'goodsid' : goodsId,
						'goodsauthor' : goodsAuthor,
						'content' : content
					},
					success : function(data) {
						if (data.errorcode == 0) {
							alert('评论成功！');
							setTimeout(function() {
								window.location.reload();
							}, 100); // 指定1秒刷新一次
						} else if (data.errorcode == -1) {
							$('#loginBox').popup();
						} else {
							alert(data.errormsg);
						}
					}
				});
			});

	$('#likeButton').click(function() {
		var goodsId = $('#goodsId').val();
		$.ajax({
			url : '/goods/like',
			type : 'post',
			dataType : 'json',
			data : {
				'goodsid' : goodsId
			},
			success : function(data) {
				if (data.errorcode == 0) {
					alert('喜欢成功！');
					setTimeout(function() {
						window.location.reload();
					}, 100); // 指定1秒刷新一次
				} else if (data.errorcode == -1) {
					$('#loginBox').popup();
				} else {
					alert(data.errormsg);
				}
			}
		});
	});
});