<?php
/**
 * 从数据库读取回复，并整理成
 * 楼/回复 的格式
 * @author Pengchi<pengchi@myhexin.com>
 */
class Reply
{
	private $_replys;

	public function __construct($aid)
	{
		$table = new DbTable_Reply();
		$this->_replys = $table->findByArticleId($aid);
	}


	/**
	 * format the reply from plain arrays to floor/subfloor
	 * @return Array
	 */
	public function format()
	{
		$result = array();
		if ($this->_replys) {
			$floors = $this->_getFloors(); 
			foreach ($floors as $floor) {
				$subFloors = $this->_getSubFloors($floor->id);		
				$result[] = array(
					'floor' => $floor->toArray(),
					'subFloors' => $subFloors
				);
			}
		}
		return $result;
	}

	private function _getFloors()
	{
		$floors = array();
		foreach ($this->_replys as $reply) {
			if ($reply->reply_to === 0) {
				$floors[] = $reply;
			}
		}
		return $floors;
	}

	private function _getSubFloors($floorId)
	{
		$subFloorIds = array();
		$subFloors = array();
		foreach ($this->_replys as $reply) {
			if ($reply->reply_to == $floorId || in_array($reply->reply_to, $subFloorIds)) {
				$subFloors[] = $reply->toArray();
				$subFloorIds[] = $reply->id;
			}
		}
		return $subFloors;
	}
}
