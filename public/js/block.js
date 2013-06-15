var maxWidth;
var columns;

function setLocation(str) {
	var parent = $(str);
	$('#store').append(parent);
	var height = parent.height();
	var column = getLowestColumn();
	parent.css({
		'top' : columns[column],
		'left' : column * 240
	});
	columns[column] = columns[column] + height + 20;
	parent.css('visibility', 'visible');
}

function getImgParentDiv(str) {
	return $(str).parent('a').parent('li').parent('ul').parent('div').parent(
			'div');
}

function getLowestColumn() {
	var lowest = columns[0];
	var column = 0;
	for ( var i = 1; i < columns.length; i++) {
		if (lowest > columns[i]) {
			lowest = columns[i];
			column = i;
		}
	}
	return column;
}

function generateColumns(str) {
	for ( var i = 0; i < str; i++) {
		columns[i] = 0;
	}
}

function getProductList() {
	var cate = $('#cateValue').val();
	var page = $('#pageValue').val();
	var userid = $('#useridValue').val();
	var usertype = $('#usertypeValue').val();
	var order = $('#orderValue').val();
	var keyword = $('#keywordValue').val();

	$.ajax({
		url : '/goods/get-product-list',
		dataType : 'json',
		data : {
			'cate' : cate,
			'page' : page,
			'userid' : userid,
			'order' : order,
			'usertype' : usertype,
			'keyword' : keyword
		},
		success : function(data) {
			var len = data.length;
			console.log(len);
			for ( var i = 0; i < len; i++) {
				setLocation(data[i]);
			}
		}
	});
}

function initColumns() {
	$('#pageValue').val(1);
	$('#store').html('');
	maxWidth = $(window).width();
	columns = [];
	if (maxWidth > 960) {
		generateColumns(4);
	} else {
		generateColumns(parseInt(maxWidth / 240));
	}
	getProductList();
}

$(function() {
	initColumns();

	$(document).scroll(function() {
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

	$('.mulu').click(function() {
		$('.mulu').find('a').css({
			'color' : '',
			'font-weight' : ''
		});
		$(this).find('a').css({
			'color' : 'red',
			'font-weight' : 'bolder'
		});
		var cate = $(this).find('input').val();
		$('#cateValue').val(cate);
		$('#keywordValue').val('');
		initColumns();
	});

	$('.filter_module a').click(function() {
		$('.filter_module a').removeClass().addClass('selector');
		$(this).removeClass().addClass('current');
		var order = $(this).attr('order');
		$('#orderValue').val(order);
		$('#keywordValue').val('');
		initColumns();
	});

	$('.usertype').click(function() {
		$(this).parent('li').siblings().removeClass();
		$(this).parent('li').addClass('c');
		var usertype = $(this).attr('type');
		$('#usertypeValue').val(usertype);
		$('#keywordValue').val('');
		initColumns();
	});
	
	$('#searchButton').click(function() {
		var keyword = $('#input-search-guide').val();
		$('#keywordValue').val(keyword);
		window.location.href = '/goods/index/keyword/' + keyword;
	});
});
