<?php
$path="http://img01.taobaocdn.com/bao/uploaded/i1/15559022577717371/T1SMauXCpXXXXXXXXX_!!0-item_pic.jpg";
list($width, $height) =getimagesize($path);//获得图像的宽高
echo $width . ' ' . $height;