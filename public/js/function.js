/** 
 * pagination function
 */
function initPagination(id)
{
	if (id == 1) {
		//goods pagination
		var num_entries = $('#goodsAmount').val();
		$("#goodsPage").pagination(num_entries, {
			num_edge_entries: 2,
			num_display_entries: 3,
			callback: listPage,
			items_per_page:18,
			orderfield:false,
			order:false,
            prev_text : "<<",
            next_text : ">>",
            first_text : "FirstPage",
            last_text : "LastPage"
		});
	}
}

function isLogin()
{
	var rtn = '';
	$.ajax({
		url : urlpath + "user/islogin/",
		type : "post",
		dataType : "json",
		success : function (result) {
			if (result.islogin == 1) {
				hideLogin(result);
				rtn = true;
			} else {
				showLogin();
				rtn = false;
			}
			return rtn;
		},
		error : function () {
			alert("error", "failed");
		}
	});
}

function hideLogin(result)
{
	$("#loginText").hide();
	$("#login_done").show();
	$("#login_done a").eq(0).html("Hi," + result.result);
	$('#teapot').unbind('click');
}

function showLogin()
{
	$("#loginText").show();
	$("#login_done").hide();
	$('#teapot').bind('click', function(){
		$("#loginWrapper").slideToggle(1000);
	});
}

function renderList(list)
{
	var tmparr = [];
	for (var key in list) {
		tmparr.push('<div class="storetea" style="background-image:url(');
		tmparr.push(path + list[key].p_picture + ');">');
		tmparr.push('<a href="');
		tmparr.push(urlpath + 'goods/detail/teaid/' + list[key].id + '">');
		tmparr.push('<div class="storeteadetail"><div class="storeteaprice"><p>Price:£');
		tmparr.push(list[key].p_price);
	    tmparr.push('</p></div><div class="storeteaname">');
	    tmparr.push(list[key].p_name);
	    tmparr.push('</div><p>&nbsp;</p><p>&nbsp;</p></div></a></div>');
	}
	var content = tmparr.join('');
	$('#listContent').html(content);
}

function listPage(page_index)
{
	var type = $('.selected').attr('type');
	//ajax请求获得数据
	$.ajax ({
		type 		: 'get',
		dataType	: 'json',
		url			: urlpath + '/goods/gettealist/',
		cache	 	: false,
		data 		: {"type":type, "pagenum":page_index},
		success		: function(data) {
			$('#goodsAmount').val(data.amount);
			initPagination(1);
			renderList(data.result);
		},
		error		: function(a, b, c) {
			alert(a + b + c);
			return;
		}
	});
	
	return false;
}

function renderCart(data)
{
	var content = [];
	for (var key in data) {
		content.push('<p>');
		content.push(data[key][config.producttable_name]);
		content.push('&nbsp; Qyt:');
		content.push(data[key].quantity);
		content.push('</p>');
	}
	var newStr = content.join("");
	$('#shoppingcartlist').html(newStr);
}

function setLike(id)
{
	$.ajax({
		url : urlpath + "goods/setlike",
		type : "POST",
		data : {"teaid" : id},
		dataType : "json",
		success : function (data) {
			if (data.errorcode == 0) {
				var curr = $('#detail_whiteheart').children('a').html();
				alert('success');
				curr++;
				$('#detail_whiteheart').children('a').html(curr);
			}
		},
		error : function () {
		}
	});
}