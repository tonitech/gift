<?php
/**
 * 商品评论类
 * @author acer
 *
 */
class Business_Goods_Comment extends Business_Goods_Abstract
{
    private static $_instance;
    
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new Business_Goods_Comment();
        }
        return self::$_instance;
    }
    
    public function setComment($bind)
    {
        try {
            $result = Utility_Db::getInstance()->conn()->insert('goods_comment', $bind);
            if ($result) {
                $updateBind = array(
                    'mtime' => date('c'),
                    'comment' => new Zend_Db_Expr('`comment`+1')
                );
                $where = array('id=?' => $bind['goods_id']);
                Business_Goods_Operation::getInstance()->updateGoods($updateBind, $where);
                $rtn['errorcode'] = 0;
                $rtn['errormsg'] = 'succes';
            } else {
                $rtn['errorcode'] = -2;
                $rtn['errormsg'] = 'failed';
            }
        } catch (Exception $e) {
            $rtn['errorcode'] = -3;
            $rtn['errormsg'] = $e->getMessage();
        }
        return $rtn;
    }
    
    public function getComment($id)
    {
        return Utility_Db::getInstance()
            ->conn()
            ->select()
            ->from('goods_comment', '*')
            ->where('goods_id=?', $id)
            ->order('ctime DESC')
            ->query()
            ->fetchAll();
    }
}