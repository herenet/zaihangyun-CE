<?php

namespace app\service;

use app\model\User;
use app\model\IAPProduct;

class OrderBZLogicService
{

    protected $order;

    protected $product;

    public function __construct(array $order, array $product)
    {
        $this->order = $order;
        $this->product = $product;
    }

    /**
     * 订单业务逻辑处理
     * @param array $order
     * @param array $product
     * @return bool
     */
    public function orderBZLogic(): bool
    {
        switch($this->product['type']){
            case IAPProduct::TYPE_MEMBER_DURATION:
                return $this->memberDurationOrderBZLogic();
            case IAPProduct::TYPE_MEMBER_FOREVER:
                return $this->memberForeverOrderBZLogic();
            case IAPProduct::TYPE_MEMBER_CUSTOM:
                return $this->memberCustomOrderBZLogic();
            default:
                return true; // 其他类型暂时返回成功
        }
    }

    /**
     * 会员时长订单业务逻辑
     * 采用简化方案：先使用本地配置设置VIP时长，后续Apple回调会用准确的过期时间覆盖
     * @param array $order
     * @param array $product
     * @return bool
     */
    private function memberDurationOrderBZLogic(): bool
    {
        // 如果订单中已有Apple的过期时间，直接使用Apple的时间
        if (!empty($this->order['expires_date'])) {
            return $this->updateUserVipByAppleExpireTime();
        }
        
        // 否则使用本地配置的时长（购买时的临时方案）
        $vip_duration = $this->product['function_value'] * 24 * 60 * 60;
        $user_model = new User();
        $user = $user_model->getUserInfoByUid($this->order['uid']);
        if(empty($user)){
            return false;
        }
        
        if(strtotime($user['vip_expired_at']) > time()){
            $user_model->updateUserInfoByUid($this->order['uid'], [
                'vip_expired_at' => date('Y-m-d H:i:s', strtotime($user['vip_expired_at']) + $vip_duration)
            ]);
        }else{
            $user_model->updateUserInfoByUid($this->order['uid'], [
                'vip_expired_at' => date('Y-m-d H:i:s', time() + $vip_duration)
            ]);
        }
        return true;
    }

    /**
     * 使用Apple的过期时间更新用户VIP状态
     * 这是最准确的方案，直接使用Apple返回的过期时间
     * @param array $order 订单信息，包含Apple的expires_date
     * @return bool
     */
    private function updateUserVipByAppleExpireTime(): bool
    {
        if (empty($this->order['uid']) || $this->order['uid'] == 0) {
            // 匿名订单暂时跳过用户VIP更新
            return true;
        }
        
        $user_model = new User();
        $user = $user_model->getUserInfoByUid($this->order['uid']);
        if(empty($user)){
            return false;
        }
        
        // 直接使用Apple的过期时间，这是最准确的
        $apple_expire_time = $this->order['expires_date'];
        $current_vip_expire = $user['vip_expired_at'];
        
        // 如果Apple的过期时间比当前VIP过期时间更晚，则更新
        if (strtotime($apple_expire_time) > strtotime($current_vip_expire)) {
            $user_model->updateUserInfoByUid($this->order['uid'], [
                'vip_expired_at' => $apple_expire_time
            ]);
        }
        
        return true;
    }

     /**
     * 永久会员订单业务逻辑
     * @param array $order
     * @param array $product
     * @return bool
     */
    private function memberForeverOrderBZLogic(): bool
    {
        $user_model = new User();
        $user = $user_model->getUserInfoByUid($this->order['uid']);
        if(empty($user)){
            return false;
        }
        $user_model->updateUserInfoByUid($this->order['uid'], ['is_forever_vip' => 1]);
        return true;
    }

    /**
     * 自定义会员订单业务逻辑
     * @param array $order
     * @param array $product
     * @return bool
     */
    private function memberCustomOrderBZLogic(): bool
    {
        // TODO: 实现自定义业务逻辑
        return true;
    }
}
