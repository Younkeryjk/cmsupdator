CREATE TABLE `add_table` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `col1` varchar(64) DEFAULT '' COMMENT '字段一',
  `col2` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
INSERT INTO `add_table` VALUES ('1', '字段1的内容', '字段2的内容');
INSERT INTO `add_table` VALUES ('2', '字段1的内容', '字段2的内容');
DROP TABLE delete_table;

ALTER TABLE add_column_table ADD col3 tinyint(3) unsigned NOT NULL COMMENT '新增字段';
ALTER  TABLE update_column_table CHANGE col1 col11 varchar(64) DEFAULT '' COMMENT '修改字段名称';
ALTER TABLE delete_column_table DROP col2;

INSERT INTO `add_table_data` VALUES ('3', '新增语句的内容', '新增语句的内容2');
UPDATE update_table_data SET col1 = '修改语句的内容', col2 = '修改语句的内容2' WHERE id = 2;
DELETE FROM delete_table_data WHERE id = 4;