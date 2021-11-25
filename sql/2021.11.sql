ALTER TABLE node ADD COLUMN `custom_config` text NOT NULL;
ALTER TABLE node DROP COLUMN `method`;
ALTER TABLE node DROP COLUMN `custom_method`;
ALTER TABLE node DROP COLUMN `custom_rss`;

INSERT INTO `config` (`id`, `item`, `value`, `class`, `is_public`, `type`, `default`, `mark`) VALUES
(115, 'invitation_mode', 'after_recharge', 'invite', 0, 'string', 'after_purchase', '邀请模式'),
(116, 'invite_rebate_mode', 'limit_frequency', 'invite', 0, 'string', 'limit_amount', '返利模式'),
(117, 'rebate_frequency_limit', '6', 'invite', 0, 'string', '3', '返利总次数限制'),
(118, 'rebate_amount_limit', '9', 'invite', 0, 'int', '100', '返利总金额限制'),
(119, 'rebate_ratio', '0.2', 'invite', 1, 'string', '0.2', '返利比例'),
(120, 'rebate_time_range_limit', '180', 'invite', '0', 'int', '180', '返利时间范围限制（单位：天）');
