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
					document.cookie = data.cookieName﻿ + "=" + data.cookieValue﻿﻿;
					$('#loginBox').pophide();
					$('#login-guide').html('<input type="hidden" value="' + data.result.id + '"/><a href="">' + data.result.username + '</a>'﻿﻿);
				} else {
					alert(data.errormsg);
				}
			}
		});
	});
});