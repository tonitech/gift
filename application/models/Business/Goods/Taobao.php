<?php
/**
 * 淘宝接口类
 * 读取淘宝商品的信息
 * @author acer
 *
 */
class Business_Goods_Taobao
{
    public function __construct()
    {
        include 'taobao/top/RequestCheckUtil.php';
    }
    
    /**
     * 获取返利的url
     * @param string $url
     */
    public function getFanliProductId($url)
    {
        $idRtn = $this->_getProductionIdByUrl($url);
        if ($idRtn['errorcode'] == 0) {
            $id = $idRtn['result'];
//             if ($this->_isFanliProduct($id)) {
//                 $result = $idRtn;
//             } else {
                $productInfo = $this->_getProductInfo($id);
                $result = array(
                	'errorcode' => -3,
                	'errormsg' => 'no fl',
                	'result' => $productInfo
                );
//             }
        } else {
            $result = $idRtn;
        }
        return $result;
    }
    
    /**
     * 对url进行解析，获取id参数值
     * @param string $url
     */
    private function _getProductionIdByUrl($url)
    {
        $urlElementsOne = explode('?', $url);
        if (isset($urlElementsOne[1])) {
            $urlElementsTwo = explode('&', $urlElementsOne[1]);
            foreach ($urlElementsTwo as $value) {
                if (substr($value, 0, 3) == 'id=') {
                    $id = substr($value, 3);
                    break;
                }
            }
            if (isset($id)) {
                $rtn = array(
                    'errorcode' => 0,
                    'errormsg' => 'success',
                    'result' => $id
                );
            } else {
                $rtn = array(
                    'errorcode' => -1,
                    'errormsg' => 'url without product id'
                );
            }
        } else {
            $rtn = array(
                'errorcode' => -2,
                'errormsg' => 'url without parameters'
            );
        }
        return $rtn;
    }
    
    /**
     * 获取商品的图片url和标题信息
     * @param int $id
     */
    private function _getProductInfo($id)
    {
        $c = $this->_getTaobaoTopClient();
        include 'taobao/top/request/ItemsListGetRequest.php';
        $req = new ItemsListGetRequest();
        $req->setFields("detail_url,num_iid,title,pic_url,price");
        $req->setNumIids($id);
        $resp = $c->execute($req);
        $rtn = $resp->items->item[0];
        return $rtn;
    }
    
    /**
     * 获得淘宝接口的对象
     */
    private function _getTaobaoTopClient()
    {
        include 'taobao/top/TopClient.php';
        $c = new TopClient();
        $c->format = 'json';
        $c->appkey = Zend_Registry::get('config')->taobao->appkey;
        $c->secretKey = Zend_Registry::get('config')->taobao->secretkey;
        return $c;
    }
    
    /**
     * 检测商品是否有返利
     */
    private function _isFanliProduct($id)
    {
        $c = $this->_getTaobaoTopClient();
        include 'taobao/top/request/TaobaokeItemsDetailGetRequest.php';
        $req = new TaobaokeItemsDetailGetRequest();
        $req->setFields("click_url, title");
        $req->setNumIids($id);
        $resp = $c->execute($req);
        if ($resp->total_results != 0) {
            return true;
        } else {
            return false;
        }
    }
}