-- 套餐订单表
CREATE TABLE `subscription_orders` (
  `order_id` varchar(64) NOT NULL COMMENT '订单号',
  `tenant_id` bigint unsigned NOT NULL COMMENT '租户ID',
  `order_type` varchar(20) NOT NULL COMMENT '订单类型:new_purchase,upgrade,renew',
  `from_product` varchar(32) DEFAULT NULL COMMENT '升级前套餐',
  `to_product` varchar(32) NOT NULL COMMENT '目标套餐',
  `product_name` varchar(64) NOT NULL COMMENT '套餐名称',
  `original_price` int unsigned NOT NULL COMMENT '套餐原价(分)',
  `final_price` int unsigned NOT NULL COMMENT '实际支付金额(分)',
  `status` tinyint DEFAULT 1 COMMENT '订单状态:1待支付,2已支付,3已取消,4支付失败',
  `pay_channel` tinyint DEFAULT 1 COMMENT '支付渠道:1微信,2支付宝',
  `wechat_prepay_id` varchar(128) DEFAULT NULL COMMENT '微信预支付ID',
  `wechat_code_url` varchar(500) DEFAULT NULL COMMENT '微信支付二维码链接',
  `third_party_order_id` varchar(128) DEFAULT NULL COMMENT '第三方订单号',
  `third_party_transaction_id` varchar(128) DEFAULT NULL COMMENT '第三方交易号',
  `paid_at` timestamp NULL DEFAULT NULL COMMENT '支付时间',
  `upgrade_info` json DEFAULT NULL COMMENT '升级信息详情',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `idx_tenant_id` (`tenant_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='套餐订单表';

-- 为租户表添加套餐到期时间字段（如果不存在）
ALTER TABLE `tenant` ADD COLUMN `subscription_expires_at` timestamp NULL DEFAULT NULL COMMENT '套餐到期时间' AFTER `product`; 