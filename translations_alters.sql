drop table if exists translations_tmp;

CREATE TABLE `translations_tmp` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `row_id` int(10) NOT NULL DEFAULT '0',
  `field_name` varchar(255) CHARACTER SET utf8 NOT NULL,
  `az` longtext CHARACTER SET utf8 NOT NULL,
  `en` longtext CHARACTER SET utf8 NOT NULL,
  `ru` longtext CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


INSERT INTO `translations_tmp` (select 0, table_name, row_id, field_name, `translation`, 
ifnull((select `translation` from translations b where b.language_id=2 
and b.table_name = a.table_name 
and b.row_id = a.row_id 
and b.field_name = a.field_name limit 1), ''), 
ifnull((select `translation` from translations b where b.language_id=3 
and b.table_name = a.table_name 
and b.row_id = a.row_id 
and b.field_name = a.field_name limit 1),'') from translations a where language_id = 1); 

drop table translations;

alter table translations_tmp rename translations;

insert into translations select 0, '', 0, w_key, w_value_az, w_value_en, w_value_ru from translations_words;

drop table translations_words;
