$(document).ready(function(){
	initPagination(1);
	isLogin();
	
	$.ajax({
		url : urlpath + "purchase/getcart/",
		type : "get",
		dataType : "json",
		success : function (result) {
			if (result.errorcode == 0) {
				var data = result.result;
				renderCart(data);
			}
		},
		error : function () {
			alert("error", "failed");
		}
	});
	
	$.ajax({
		url : urlpath + "recommend/getlist/",
		type : "POST",
		data : {
			"type" : 'like'
		},
		dataType : "json",
		success : function (result) {
			if (result.errorcode == 0) {
				var content = [];
				var data = result.result;
				for (var key in data) {
					content.push('<div class="floating"><div class="imgbox" style="background-image:');
					content.push('url(' + path + data[key].p_picture + ');"><div class="heart">');
					content.push('</div><a href="' + urlpath + 'goods/detail/teaid/' + data[key].id + '" target="_blank"><div class="teaname">' + data[key].p_name + '</div></div></div>');
                    
				}
				var newStr = content.join("");
				$('#top7').html(newStr);
			}
		},
		error : function () {
			alert("error", "failed");
		}
	});
	
	$.ajax({
		url : urlpath + "recommend/getlist/",
		type : "POST",
		data : {
			"type" : 'time'
		},
		dataType : "json",
		success : function (data) {
			if (data.errorcode == 0) {
				$('#time_text').html(data.result.t_description);
				$('#tealeaf').html('<a href="' + urlpath  + "goods/detail/teaid/" + data.result.id + '"><img width="50px" height="50px" src="' + path + 'images/recommend/' + data.result.id + '.png"></a>');
			} else {
				$('#time_text').html('You should have a big rest now.');
			}
		},
		error : function () {
		}
	});

	$('#toRegister').click(function(){
		$('#register_form').show();
		$('#login_form').hide();
	});
	
	$('#toLogin').click(function(){
		$('#login_form').show();
		$('#register_form').hide();
	});

	$('#loginBtn').click(function(){
		$.ajax({
			url : urlpath + "user/login/",
			type : "POST",
			data : {
				"username" : $('#username').val(),
				"password" : $('#password').val()
			},
			dataType : "json",
			success : function (result) {
				if (result.errorcode == 0) {
					$("#loginText").hide();
					$("#login_done").show();
					$("#login_done a").eq(0).html("Hi," + result.result);
					$('#teapot').unbind('click');
					$("#loginWrapper").slideToggle(1000);
				} else {
					alert(result.errormsg);
				}
			},
			error : function () {
				alert("error", "failed");
			}
		});
	});

	$('#registerBtn').click(function(){
		$.ajax({
			url : urlpath + "user/signup/",
			type : "POST",
			data : {
				"username" : $('#username2').val(),
				"password" : $('#password2').val(),
				"email" : $('#email').val()
			},
			dataType : "json",
			success : function (result) {
				if (result.errorcode == 0) {
    				alert("Register success!Your username:" + result.result);
    				$('#login_form').show();
    				$('#register_form').hide();
				} else {
					alert(result.errormsg);
				}
			},
			error : function () {
				alert("error", "failed");
			}
		});
	});
	
	$('.tag').click(function(){
		var type = $(this).attr('type');
		var self = this;
		$.ajax({
			url : urlpath + "goods/gettealist/",
			type : "POST",
			data : {"type":type, "pagenum":0},
			dataType : "json",
			success : function (data) {
				$(self).siblings().removeClass('selected');
				$(self).addClass('selected');
				$('#goodsAmount').val(data.amount);
				initPagination(1);
				renderList(data.result);
			},
			error : function () {
				alert("error", "failed");
			}
		});
	});
	
	$('#addCart').click(function(){
		var id = $('#productid').val();
		var quantity = $('#quantity').val();
		$.ajax({
			url : urlpath + "purchase/setcart/",
			type : "POST",
			data : {"productid":id, 'quantity':quantity},
			dataType : "json",
			success : function (data) {
				if (data.errorcode == 0) {
					var data = data.result;
					renderCart(data);
				} else if (data.errorcode == -2) {
					$("#loginWrapper").slideDown(1000);
				}
			},
			error : function () {
				alert("error", "failed");
			}
		});
	});
	
	$('#seeRec').click(function(){
		$('#symptom').val(clickPoints);
	});
	
	$('.quantityinput').change(function(){
		var id = $(this).siblings('input').val();
		$.ajax({
			url : urlpath + "purchase/modshoppingcart/",
			type : "POST",
			data : {"productid":id, 'quantity':$(this).val()},
			dataType : "json",
			success : function (data) {
				alert(data.errormsg);
			},
			error : function () {
				alert("error", "failed");
			}
		});
	});
});