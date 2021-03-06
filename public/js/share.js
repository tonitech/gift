function noBack(url, result)
{
	$('#url').attr('href', url);
	$('#url').html(result.title);
	$('#fanli').html('<span style="color:red;">该商品暂时没有返利!</span>');
	$('#image').html('<a href="' + url + '" target="_blank"><img src="' + result.picurl + '" height="230px" width="230px"/></a>');
}

var shareLinkGoods;

$(function() {
	$('#shareLink').click(function(){
		if (!isLogin()) {
			$('#loginBox').popup();
			return false;
		}
		$('#preShareLink').popup();
	});
	
	$('.shareLinkClose1').click(function(){
		$('#preShareLink').pophide();
	});
	
	$('.shareLinkClose2').click(function(){
		$('#afterShareLink').pophide();
	});
	
	$('#shareLinkBtn').click(function(){
		var url = $('#shareLinkUrl').val();
		var contact = $('#contact').val();
		$.ajax({
			url : '/goods/get-product-id',
			data : {url:url}, 
			success : function(data){
				if (data.errorcode == -3) {
					var respItem = data.result;
					shareLinkGoods = respItem;
					$('#shareLinkPrice').html('<span class="p">¥' + respItem.price + '</span>' + respItem.title + '');
					$('#shareLinkImage').html('<img src="' + respItem.pic_url + '" alt="' + respItem.title + '" style="width: 142px; height: 142px;">');
					$('#preShareLink').pophide();
					$('#afterShareLink').popup();
				}
			}
		});
	});
	
	$('#shareLinkSubmit').click(function(){
		var desc = $('#shareLinkDesc').val();
		$.ajax({
			url : '/goods/share',
			dataType : 'json',
			data : {'goods' : shareLinkGoods, 'desc' : desc},
			success : function (data) {
				if (data.errorcode == 0) {
					alert('分享成功！');
					$('#afterShareLink').pophide();
				} else {
					alert(data.errormsg);
				}
			}
		});
	});
});