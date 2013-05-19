var global;
var maxWidth = $(window).width();
var columns = [];


if (maxWidth > 960) {
	generateColumns(4);
} else {
	generateColumns(parseInt(maxWidth / 240));
}

function setLocation(str)
{
	var parent = $(str);
	$('#store').append(parent);
	global = parent;
	var height = parent.height();
	var column = getLowestColumn();
	parent.css({'top':columns[column], 'left':column * 240});
	columns[column] = columns[column] + height + 20;
	parent.css('visibility', 'visible');
}

function getImgParentDiv(str)
{
	return $(str).parent('a').parent('li').parent('ul').parent('div').parent('div');
}

function getLowestColumn()
{
	var lowest = columns[0];
	var column = 0;
	for (var i = 1; i < columns.length; i++) {
		if (lowest > columns[i]) {
			lowest = columns[i];
			column = i;
		}
	}
	return column;
}

function generateColumns(str)
{
	for (var i = 0; i < str; i++) {
		columns[i] = 0;
	}
}

function getProductList()
{
	var cate = $('#cateValue').val();
	var page = $('#pageValue').val();
	var userid = $('#useridValue').val();
	var order = $('#orderValue').val();
	
	$.ajax({
		url : '/goods/get-product-list',
		dataType : 'json',
		data : {'cate' : cate, 'page' : page, 'userid' : userid, 'order' : order},
		success : function (data) {
			var len = data.length;
			console.log(len);
			for (var i = 0; i < len; i++) {
				setLocation(data[i]);
			}
		}
	});
}

$(function() {
	getProductList();
	
	$(document).scroll(function(){
		var windowHeight = $(window).height();
		var scrollTop = $(document).scrollTop();
		var domHeight = $(document).height();

		if ((windowHeight + scrollTop) == domHeight) {
			var page = $('#pageValue').val();
			page++;
			$('#pageValue').val(page);
			getProductList();
		}
	});
	
});
