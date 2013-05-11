var path = '/teaing/', urlpath = '/teaing/index.php/';
//var path = '/~s1203982/teaing/', urlpath = '/~s1203982/teaing/index.php/';
var height = 358; //canvas的高度
var width = 312; //canvas的宽度
var edgeLength = 173.205; //六边形的边长
var pointRadius = 8; //小圆的半径
var config = new Object();
config.usertable_tablename = 'tb_user';
config.usertable_id = 'id';
config.usertable_ctime = 'ctime';
config.usertable_mtime = 'mtime';
config.usertable_username = 'u_name';
config.usertable_password = 'u_pwd';
config.usertable_email = 'u_email';

config.producttable_tablename = 'tb_product';
config.producttable_id = 'id';
config.producttable_name = 'p_name';
config.producttable_picture = 'p_picture';
config.producttable_quantity = 'p_stock';
config.producttable_function = 'p_function';
config.producttable_liked = 'p_rec_time';
config.producttable_price = 'p_price';
config.producttable_timeid = 'p_timeid';
config.producttable_type = 'p_type';
config.producttable_description = 'p_description';
config.producttable_symptom = 'p_symptom';

config.timetable_tablename = 'tb_time';
config.timetable_id = 'id';
config.timetable_name = 't_name';
config.timetable_description = 't_description';
config.timetable_starttime = 't_starttime';
config.timetable_endtime = 't_endtime';

config.ordertable_tablename = 'tb_order';
config.ordertable_id = 'id';
config.ordertable_productid = 'd_productid';
config.ordertable_status = 'd_status';
config.ordertable_userid = 'd_userid';
config.ordertable_deliveryadd = 'd_deliveryadd';
config.ordertable_email = 'd_email';
config.ordertable_postcode = 'd_postcode';
config.ordertable_ctime = 'd_ctime';
config_ordertable_mtime = 'd_mtime';
config_ordertable_phone = 'd_phone';
config.ordertable_ordernumber = 'd_ordernumber';
config.ordertable_quantity = 'd_quantity';

config.display_recommend = '7';
config.display_tealist = '18';