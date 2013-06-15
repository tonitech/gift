function isLogin()
{
	var login = false;
	var arrStr = document.cookie.split("; ");
	for (var i = 0; i < arrStr.length; i++) {
		var temp = arrStr[i].split("=");
		if (temp[0] == 'user') {
			login = true;
			break;
		}
	}
	return login;
}

$(function() {
	$('#loginBtn').click(function() {
		$('#loginBox').popup();
	});
	
	$('#loginClose').click(function() {
		$('#loginBox').pophide();
	});
	
	$('#loginButton').click(function(){
		var username = $('#username').val();
		var password = $('#passwd').val();
		$.ajax({
			url : '/user/ajax-login',
			dataType : 'json',
			data : {'username':username, 'password':password},
			success : function (data) {
				if (data.errorcode == 0) {
					$('#loginBox').pophide();
					$('#login-guide').html('<input type="hidden" value="' + data.result.id + '"/><a href="/user" class="username">' + data.result.username + '</a> <a href="/user/logout" class="username">退出</a>'﻿﻿);
				} else {
					alert(data.errormsg);
				}
			}
		});
	});
});