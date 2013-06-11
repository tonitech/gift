<?php
/**
 * 商品增删改查操作类
 * @author acer
 *
 */
class Business_Goods_Operation extends Business_Goods_Abstract
{
	public function getGoodsList($page, $rowcount, $order = null, $cates = null, $userid = null, $usertype = null)
	{
		$stmt = Utility_Db::getInstance()
			->conn()
			->select()
			->from($this->_table->tablename)
			->limitPage($page, $rowcount);
		
		if ($userid != null) {
		    if ($usertype == 'like') {
		        $likeList = Utility_Db::getInstance()
        			->conn()
        			->select()
        			->from('userlike')
        			->where('userid=?', $userid)
        			->limitPage($page, $rowcount)
		            ->query()
		            ->fetchAll();
		        $rtn = array();
		        foreach ($likeList as $item) {
		            $rtn[] = Utility_Db::getInstance()
    		            ->conn()
    		            ->select()
    		            ->from($this->_table->tablename)
    		            ->where($this->_table->id . '=?', $item['productid'])
    		            ->query()
    		            ->fetch();
		        }
		        return $rtn;
		    } elseif ($usertype == 'share') {
    			$stmt->where($this->_table->userid . '=?', $userid);
		    }
		}
		if ($order != null) {
			$stmt->order($order . ' DESC');
		} else {
			$stmt->order('price DESC');
		}
		
		if ($cates != null) {
			$goods = $stmt->query()->fetchAll();
			if (empty($goods)) {
				return $goods;
			}
			$rtn = array();
			$count = 0;
			foreach ($goods as $item) {
				$category = unserialize($item[$this->_table->cates]);
				if (isset($category[$cates])) {
					$count++;
					$rtn[] = $item;
				}
			}
			if ($count < $rowcount) {
				$tmp = $this->getGoodsList($page + 1, $rowcount, $order, $cates, $userid);
				$rtn = array_merge($rtn, $tmp);
			}
		} else {
			$rtn = $stmt->query()->fetchAll();
		}
		return $rtn;
	}
	
	public function getGoodsDetailsById($id)
	{
		return $this->_db->select()
			->from($this->_table->tablename)
			->where($this->_table->id . '=?', $id)
			->query()
			->fetch();
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
	
	/**
	 * 添加商品到数据库
	 * @param array $goodsDetails
	 */
	public function addGoods(array $goodsDetails)
	{
		$time = date('Y-m-d H:i:s');
		$userinfo = Business_User_Auth::getInstance()->getUserInfoBySession();
		$size = $this->getImageSize($goodsDetails['pic_url']);
		$bind = array(
			$this->_table->ctime => $time,
			$this->_table->mtime => $time,
			$this->_table->type => 'taobao',
			$this->_table->keyid => 'taobao_' . $goodsDetails['num_iid'],
			$this->_table->cates => serialize(
			    $this->jundgeGoodsCates($goodsDetails['title'] . ' ' . $goodsDetails['description'])
			),
			$this->_table->name => $goodsDetails['title'],
// 			$this->_table->url => $goodsDetails['click_url'],
		    $this->_table->url => $goodsDetails['detail_url'],
			$this->_table->picUrl => $goodsDetails['pic_url'],
			$this->_table->price => $goodsDetails['price'],
// 			$this->_table->commission => $goodsDetails['commission_rate'] / 100,
			$this->_table->description => $goodsDetails['description'],
			$this->_table->like => 0,
			$this->_table->comment => 0,
			$this->_table->userid => $userinfo['result']['id'],
			$this->_table->picWidth => $size['width'],
			$this->_table->picHeight => $size['height']
		);
		
		try {
			$rtn = $this->_db->insert($this->_table->tablename, $bind);
			if ($rtn) {
				return array(
					'errorcode' => 0,
					'errormsg' => 'success'
				);
			} else {
				return array(
					'errorcode' => -1,
					'errormsg' => '提交失败'
				);
			}
		} catch (Exception $e) {
			if ($e->getCode() == 1062) {
				return array(
					'errorcode' => -2,
					'errormsg' => '该商品已经分享过了！'
				);
			}
			return array(
				'errorcode' => $e->getCode(),
				'errormsg' => $e->getMessage()
			);
		}
	}
	
	/**
	 * 获取商品所有的类别
	 * @return array
	 */
	public function getGoodsCates()
	{
		$cateTable = Zend_Registry::get('dbtable')->goodscate;
		return $this->_db->select()
			->from($cateTable->tablename)
			->query()
			->fetchAll();
	}
	
	/**
	 * 根据商品的title判断商品的cate
	 * @param string $string
	 * @return array
	 */
	public function jundgeGoodsCates($string)
	{
		error_reporting(0);
		$pscws = new PSCWS2(APPLICATION_PATH . '/../library/dict/dict.xdb');
		$text = iconv('utf-8', 'gbk', $string);
		$res = $pscws->segment($text);
		$cateTable = Zend_Registry::get('dbtable')->goodscate;
		$cateArr = array();
		foreach ($res as $word) {
			$text = iconv('gbk', 'utf-8', $word);
			$len = strlen($text);
			if ($len >= 3) {
				for ($i = 1; $i <= $len / 3; $i++) {
					$str = mb_substr($text, $i - 1, $i, 'utf-8');
					$goodsOperationObj = new Business_Goods_Operation();
					$cates = $goodsOperationObj->getGoodsCates();
					foreach ($cates as $cate) {
						if (mb_strpos($cate[$cateTable->cateName], $str) !== false) {
							$cateArr[$cate[$cateTable->cateId]] = $cate[$cateTable->cateId];
						}
					}
				}
			}
		}
		return $cateArr;
	}
}