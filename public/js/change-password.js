$(function() {
	$('#changePassword').click(function() {
		var oldPassword = $('#oldPwd').val();
		var password1 = $('#newPwd1').val();
		var password2 = $('#newPwd2').val();
		$.ajax({
			url : '/user/change-password',
			type : 'post',
			dataType : 'json',
			data : {
				oldpwd : oldPassword,
				pwd1 : password1,
				pwd2 : password2
			},
			success : function(data) {
				if (data.errorcode == 0) {
					alert('密码修改成功！');
					$('#oldPwd').val('');
					$('#newPwd1').val('');
					$('#newPwd2').val('');
				} else if (data.errorcode == -1) {
					$('#oldPwdTip').html(data.errormsg);
					$('#newPwd1Tip').html('');
					$('#newPwd2Tip').html('');
				} else if (data.errorcode == -2) {
					$('#oldPwdTip').html('');
					$('#newPwd1Tip').html(data.errormsg);
					$('#newPwd2Tip').html('');
				} else if (data.errorcode == -3) {
					$('#oldPwdTip').html('');
					$('#newPwd1Tip').html('');
					$('#newPwd2Tip').html(data.errormsg);
				}
			},
		});
	});
});