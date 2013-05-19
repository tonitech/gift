<?php
/**
 * 商品增删改查操作类
 * @author acer
 *
 */
class Business_Goods_Operation extends Business_Goods_Abstract
{
	public function getGoodsList($page, $rowcount, $order = null, $cates = null, $userid = null)
	{
		$stmt = Utility_Db::getInstance()
			->conn()
			->select()
			->from($this->_table->tablename)
			->order('price DESC')
			->limitPage($page, $rowcount);
		
		if ($userid != null) {
			$stmt->where($this->_table->userid . '=?', $userid);
		}
		if ($cates != null) {
			$stmt->where($this->_table->cates . '=?', $cates);
		}
		if ($order != null) {
			$stmt->order('`' . $order . '` DESC');
		}
		return $stmt->query()->fetchAll();
	}
	
	public function getImageSize($url)
	{
		list($width, $height) = getimagesize($url);
		if (empty($width)) {
			$url = 'http://image.taobao.com/bao/uploaded/i1/16738022270335280/T12GupXr8aXXXXXXXX_!!2-item_pic.png';;
			list($width, $height) = getimagesize($url);
		}
		$tmpWidth = $width;
		$width = 200;
		$ratio = $tmpWidth / $width;
		$displayHeight = round($height / $ratio);
		return array(
			'width' => $width,
			'height' => $displayHeight
		);
	}
}