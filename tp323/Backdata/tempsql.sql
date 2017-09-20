//数据更新记录20170905
ALTER TABLE `yy_auth_rule`
ADD COLUMN `funid`  int NOT NULL AFTER `type`;

UPDATE `yy_auth_rule` SET `funid`='1' WHERE (`id`='2');
UPDATE `yy_auth_rule` SET `funid`='2' WHERE (`id`='3');
UPDATE `yy_auth_rule` SET `funid`='1' WHERE (`id`='15');