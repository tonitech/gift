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
				if (data.errorcode == -1) {
					noBack(url, data.result);
				} else if (data.errorcode == -2) {
					alert('请输入合法的淘宝商品链接!');
				} else {
					TOP.api('rest', 'get', {
						method : 'taobao.taobaoke.widget.items.convert',
						num_iids : data.result,
						fields : 'num_iid,title,click_url,pic_url,price,commission_rate'
					}, function(resp) {
						if(resp.error_response) {
							alert('taobao.taobaoke.widget.items.convert接口获取商品信息品失败!' + resp.error_response.msg);
							return false;
						}
						if (resp.total_results > 0) {
							var respItem = resp.taobaoke_items.taobaoke_item;
							shareLinkGoods = respItem[0];
							$('#shareLinkPrice').html('<span class="p">¥' + respItem[0].price + '</span>' + respItem[0].title + '');
							$('#shareLinkImage').html('<img src="' + respItem[0].pic_url + '" alt="' + respItem[0].title + '" style="width: 142px; height: 142px;">');
							$('#preShareLink').pophide();
							$('#afterShareLink').popup();
						} else {
							noBack(url, data.result);
						}
					});
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