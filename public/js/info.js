$(function(){
    $('#province-select').change(function() {
        var province = $('#province-select').val();
        if(province != 0) {
            $.get('/user/city', {
                province : province
            }, function(data) {
                var city = '<option value="0"></option>';
                var district = '<option value="0"></option>';
                for(var i = 0; i < data.city.length; i++) {
                    city += '<option value="' + data.city[i].code + '">' + data.city[i].name + '</option>';
                }
                $('#city-select').html(city);

                for(var i = 0; i < data.district.length; i++) {
                    district += '<option value="' + data.district[i].code + '">' + data.district[i].name + '</option>';
                }
                $('#district-select').html(district);
            }, "json");
        } else {
            $('#city-select').html('');
            $('#district-select').html('');
        }
    });

    $('#city-select').change(function() {
        var city = $('#city-select').val();
        if(city != 0) {
            $.get('/user/district', {
                city : city
            }, function(data) {
                var district = '<option value="0"></option>';
                for(var i = 0; i < data.district.length; i++) {
                    district += '<option value="' + data.district[i].code + '">' + data.district[i].name + '</option>';
                }
                $('#district-select').html(district);
            }, "json");
        } else {
            $('#district-select').html('<option value="0"></option>');
        }
    });

    $('#year-select').change(function() {
        year = $('#year-select').val();
        if(year != 0) {
            month = '<option value="0"></option>';
            for(var i = 1; i <= 12; i++) {
                if(i < 10) {
                    month += '<option value="' + i + '">0' + i + '</option>';
                } else {
                    month += '<option value="' + i + '">' + i + '</option>';
                }
            }
            $('#month-select').html(month);
            day = '<option value="0"></option>';
            $('#day-select').html(day);
        } else {
            month = '<option value="0"></option>';
            $('#month-select').html(month);
            day = '<option value="0"></option>';
            $('#day-select').html(day);
        }
    });

    $('#month-select').change(function() {
        var smooth = false;
        if(year % 100 == 0) {
            if(year % 400 == 0) {
                smooth = true;
            } else {
                smooth = false;
            }
        } else if(year % 4 == 0) {
            smooth = true;
        } else {
            smooth = false;
        }
        month = $('#month-select').val();
        day = '<option value="0"></option>';
        if(month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12) {
            for(var i = 1; i <= 31; i++) {
                if(i < 10) {
                    day += '<option value="' + i + '">0' + i + '</option>';
                } else {
                    day += '<option value="' + i + '">' + i + '</option>';
                }
            }
        } else if(month == 4 || month == 6 || month == 9 || month == 11) {
            for(var i = 1; i <= 30; i++) {
                if(i < 10) {
                    day += '<option value="' + i + '">0' + i + '</option>';
                } else {
                    day += '<option value="' + i + '">' + i + '</option>';
                }
            }
        } else if(month == 2) {
            if(smooth) {
                for(var i = 1; i <= 29; i++) {
                    if(i < 10) {
                        day += '<option value="' + i + '">0' + i + '</option>';
                    } else {
                        day += '<option value="' + i + '">' + i + '</option>';
                    }
                }
            } else {
                for(var i = 1; i <= 28; i++) {
                    if(i < 10) {
                        day += '<option value="' + i + '">0' + i + '</option>';
                    } else {
                        day += '<option value="' + i + '">' + i + '</option>';
                    }
                }
            }
        } else {
            day = '<option value="0"></option>';
        }
        $('#day-select').html(day);
    });
    
    $('#button').click(function() {
        var username = $('#username').val();
        var email = $('#email').val();
        var blog = $('#blog').val();
        var province = $('#province-select').val();
        var city = $('#city-select').val();
        var district = $('#district-select').val();
        var gender;
        if($('#female:checked').val() == 1) {
            gender = $('#female:checked').val();
        } else {
            gender = $('#male:checked').val();
        }
        var year = $('#year-select').val();
        var month = $('#month-select').val();
        var day = $('#day-select').val();
        var occupation = $('#occupation').val();
        var introduction = $('#textarea').val();

        $.get('submit', {
            username : username,
            gender : gender,
            email : email,
            blog : blog,
            province : province,
            city : city,
            district : district,
            year : year,
            month : month,
            day : day,
            occupation : occupation,
            introduction : introduction
        }, function(data) {
            alert(data.errormsg);
        });
    });
});