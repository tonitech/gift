//传入canvas的宽度和高度还有六边形的边长，就可以确定一个六边形的六个点的坐标了
function getHexagonPoints(width, height, edgeLength)
{
	var paramX = edgeLength * Math.sqrt(3) / 2;
	var marginLeft = (width - 2 * paramX) / 2;
	var x5 = x6 = marginLeft;
	var x2 = x3 = marginLeft + paramX * 2;
	var x1 = x4 = marginLeft + paramX;

	var paramY = edgeLength / 2;
	var marginTop = (height - 4 * paramY) / 2;
	var y1 = marginTop;
	var y2 = y6 = marginTop + paramY;
	var y3 = y5 = marginTop + 3 * paramY;
	var y4 = marginTop + 4 * paramY;
	
	var points = new Array();
	points[0] = [x1, y1];
	points[1] = [x2, y2];
	points[2] = [x3, y3];
	points[3] = [x4, y4];
	points[4] = [x5, y5];
	points[5] = [x6, y6];
	return points;
}

//画六个六边形
function drawHexagon(sixParam) 
{
	for (var i = 0; i < 6; i++) {
		allPoints[i] = getHexagonPoints(width, height, sixParam - i * sixParam / 5);
		ctx.beginPath();
		ctx.strokeStyle = "rgba(255,255,255,0)";
		ctx.fillStyle = "rgba(0,0,0,0)";
		ctx.moveTo(allPoints[i][5][0],allPoints[i][5][1]); //5
		for (var j = 0; j < 6; j++) {
			ctx.lineTo(allPoints[i][j][0],allPoints[i][j][1]); //1
		}
		ctx.stroke();
		ctx.closePath();
		ctx.fill();
	}
}

//画交叉的线
function drawLines() 
{
	ctx.beginPath();
	ctx.strokeStyle = "rgba(255,255,255,0)";
	for (var i = 0; i < 3; i++) {
		ctx.moveTo(allPoints[0][i][0],allPoints[0][i][1]); //1-4
		ctx.lineTo(allPoints[0][i+3][0],allPoints[0][i+3][1]); //1-4
		ctx.stroke();
	}
	ctx.closePath();
}

//画覆盖物
function drawCover()
{
	ctx.beginPath();
	ctx.strokeStyle = "rgba(100,100,100,1)";
	ctx.fillStyle = "rgba(255,255,255,0.5)";
	ctx.moveTo(coverPoints[5][0],coverPoints[5][1]); //5
	for (var j = 0; j < 6; j++) {
		ctx.lineTo(coverPoints[j][0],coverPoints[j][1]);
	}
	ctx.stroke();
	ctx.closePath();
	ctx.fill();
}

//描点
function drawPoints(pointRadius) 
{
	ctx.fillStyle="rgba(255,255,255,0)";
	for (var i = 0; i < 5; i++) {
		for (var k = 0; k < 6; k++) {
			ctx.beginPath();
			ctx.arc(allPoints[i][k][0],allPoints[i][k][1],pointRadius,0,Math.PI*2);
			ctx.closePath();
			ctx.fill();
		}
	}
}

//判断用户点击的位置是否在小圆的范围内
function judgeRange()
{
	for (var i = 0; i < 5; i++) {
		for (var k = 0; k < 6; k++) {
			var distance = Math.sqrt((mx - allPoints[i][k][0]) * (mx - allPoints[i][k][0]) + (my - allPoints[i][k][1]) * (my - allPoints[i][k][1]));
			if (distance <= pointRadius) {
				clickPoints[k] = 5 - i;
				$('#symptom').val(clickPoints);
				//清空
				ctx.clearRect(0, 0, width, height);
				//重绘
				drawHexagon(edgeLength); 
				drawLines();
				//给覆盖物确定变化
				coverPoints[k] = allPoints[i][k];
				drawCover();
				drawPoints(pointRadius);
				return;
			}
		}
	}
}

var c = document.getElementById("myCanvas");
var ctx = c.getContext("2d");
var allPoints = [];
var clickPoints = [2, 2, 2, 2, 2, 2];
var mx,my;
drawHexagon(edgeLength); 
drawLines();
//初始化覆盖物
var coverPoints = allPoints[3];
drawCover();
drawPoints(pointRadius);
$(function(){
	$('#symptom').val(clickPoints);
});

this.mousedown = function(e) {
	//判断浏览器的类型，IE和firefox的offset和layer属性需要减去当前标签到浏览器左上角的距离的。
	if (isFirefox = navigator.userAgent.indexOf("Firefox") > 0 || navigator.userAgent.indexOf("MSIE") > 0) {  
		if (e.layerX || e.layerX == 0) {
			mx = e.layerX - c.offsetLeft;
			my = e.layerY - c.offsetTop;
		} else if (e.offsetX || e.offsetX == 0 ){
			mx = e.offsetX - c.offsetLeft;
			my = e.offsetY - c.offsetTop;
		}
	} else {
		if (e.layerX || e.layerX == 0) {
			mx = e.layerX;
			my = e.layerY;
		} else if (e.offsetX || e.offsetX == 0 ){
			mx = e.offsetX;
			my = e.offsetY;
		}
	}
	judgeRange();
};
c.addEventListener('mousedown', this.mousedown, false); //添加鼠标点击监听事件