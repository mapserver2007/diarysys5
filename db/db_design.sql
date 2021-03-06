SET SESSION FOREIGN_KEY_CHECKS=0;

/* Drop Tables */

DROP TABLE IF EXISTS TAG;
DROP TABLE IF EXISTS DIARY5;
DROP TABLE IF EXISTS AUTHENTICATION;
DROP TABLE IF EXISTS TAG_MASTER;




/* Create Tables */

CREATE TABLE AUTHENTICATION
(
	ID INT(0) NOT NULL AUTO_INCREMENT,
	USER_ID VARCHAR(32) NOT NULL UNIQUE,
	TOKEN VARCHAR(64) NOT NULL UNIQUE,
	PRIMARY KEY (ID)
);


CREATE TABLE DIARY5
(
	-- 連番ID
	ID INT NOT NULL AUTO_INCREMENT COMMENT '連番ID',
	-- 日記のタイトル
	TITLE VARCHAR(100) NOT NULL COMMENT '日記のタイトル',
	-- 日記の本文
	DESCRIPTION TEXT NOT NULL COMMENT '日記の本文',
	-- 登録日付
	DATE DATETIME NOT NULL COMMENT '登録日付',
	WEATHER INT NOT NULL,
	USER_ID INT(0) NOT NULL,
	PRIMARY KEY (ID),
	CONSTRAINT TITLE_DATE UNIQUE (TITLE, DATE)
);


CREATE TABLE TAG
(
	ID INT NOT NULL AUTO_INCREMENT,
	-- 連番ID
	ENTRY_ID INT NOT NULL COMMENT '連番ID',
	-- タグID
	TAG_ID INT NOT NULL COMMENT 'タグID',
	PRIMARY KEY (ID)
);


CREATE TABLE TAG_MASTER
(
	-- タグID
	ID INT NOT NULL AUTO_INCREMENT COMMENT 'タグID',
	-- タグ名
	NAME VARCHAR(100) NOT NULL UNIQUE COMMENT 'タグ名',
	PRIMARY KEY (ID)
);



/* Create Foreign Keys */

ALTER TABLE DIARY5
	ADD FOREIGN KEY (USER_ID)
	REFERENCES AUTHENTICATION (ID)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE TAG
	ADD FOREIGN KEY (ENTRY_ID)
	REFERENCES DIARY5 (ID)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;


ALTER TABLE TAG
	ADD FOREIGN KEY (TAG_ID)
	REFERENCES TAG_MASTER (ID)
	ON UPDATE RESTRICT
	ON DELETE RESTRICT
;



