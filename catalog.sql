-- Adminer 4.8.1 MySQL 8.0.24 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `catalog` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `catalog`;

DROP TABLE IF EXISTS `elements`;
CREATE TABLE `elements` (
  `uuid` varchar(36) NOT NULL,
  `section_uuid` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `created` int NOT NULL,
  `updated` int DEFAULT NULL,
  `type` varchar(20) NOT NULL,
  `data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `uuid_index` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `elements` (`uuid`, `section_uuid`, `name`, `created`, `updated`, `type`, `data`) VALUES
('493fdb4c-34e2-4484-8255-25f71c9b73f7',	'545f2617-ed58-4e9b-b4d2-e6dfa6c80f76',	'Очень важная новость, наверное',	1645954507,	1645959513,	'ARTICLE',	'Да, очень важная'),
('54e1a848-26b1-4e4d-8c01-c97d7a4bdd5b',	'92653c4d-b3a5-437b-81b4-d988a748e685',	'Отзыв в разделе ',	1645964146,	1645964166,	'REVIEW',	'Ну и содержание c изменением'),
('14a1a9d0-39d9-494f-baee-0d0fff9eb3ac',	'3d9dad9e-ff05-4b2e-a088-aed59ab1f2c7',	'Новость',	1645964279,	NULL,	'PAPER',	'123456'),
('b85540d4-70aa-46ab-b417-cc74e9244fb5',	'3d9dad9e-ff05-4b2e-a088-aed59ab1f2c7',	'Статья',	1645964296,	NULL,	'PAPER',	'123456'),
('995b9046-72df-4618-b5a4-1f818ce11bc0',	'3d9dad9e-ff05-4b2e-a088-aed59ab1f2c7',	'Отзыв',	1645964315,	NULL,	'REVIEW',	'Угу'),
('c51f165b-5385-4cc3-9475-7722c7bb1184',	'92653c4d-b3a5-437b-81b4-d988a748e685',	'Еще один элемент в корне раздела',	1645964383,	NULL,	'COMMENT',	'Пусть будет комментарий'),
('3294c1b2-b017-4949-8266-ff18aed4b06f',	NULL,	'Элемент в корне каталога',	1645964426,	1645964451,	'ARTICLE',	'Да да, пустой');

DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `uuid` varchar(36) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created` int NOT NULL,
  `updated` int DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `parent_uuid` varchar(36) DEFAULT NULL,
  PRIMARY KEY (`uuid`),
  UNIQUE KEY `uuid_unique` (`uuid`),
  KEY `uuid_index` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `sections` (`uuid`, `name`, `created`, `updated`, `description`, `parent_uuid`) VALUES
('4c92b9fd-c660-4c49-9ae7-c2ca2c1b6682',	'Первый раздел',	1645945757,	1645946192,	'После второго изменения. Просто первоначальный раздел для проверки работы добавления разделов',	NULL),
('a87545c5-cfaf-4173-95fc-6744de4d07cc',	'В первом 1',	1645946302,	1645962844,	'ДА',	'4c92b9fd-c660-4c49-9ae7-c2ca2c1b6682'),
('f3aa49ec-e0a0-4d26-a62d-c5a486ff1ee6',	'В первом 2',	1645946312,	NULL,	'ДА 2',	'4c92b9fd-c660-4c49-9ae7-c2ca2c1b6682'),
('e8b1c8fa-c8f0-4229-8b6e-dcce43b5647b',	'Где то там',	1645946370,	NULL,	'Ага',	'f3aa49ec-e0a0-4d26-a62d-c5a486ff1ee6'),
('92653c4d-b3a5-437b-81b4-d988a748e685',	'Еще один раздел в корне',	1645955030,	1645964022,	'Ну тут должно быть описание, maybe',	NULL),
('290934c8-bd32-4d43-b4ec-c6551924b6b5',	'В первом 3',	1645964230,	NULL,	'Ага',	'4c92b9fd-c660-4c49-9ae7-c2ca2c1b6682'),
('545f2617-ed58-4e9b-b4d2-e6dfa6c80f76',	'Folder',	1645957988,	1645960296,	'Test folder',	'92653c4d-b3a5-437b-81b4-d988a748e685'),
('3d9dad9e-ff05-4b2e-a088-aed59ab1f2c7',	'In first 4',	1645964259,	NULL,	'YEAH',	'4c92b9fd-c660-4c49-9ae7-c2ca2c1b6682'),
('4021d189-e663-4025-b25b-cb7450dea49c',	'Пустой раздел в корне',	1645964349,	NULL,	'Здесь пусто',	NULL),
('9db96587-e359-4dd7-aeae-6bbf338977fe',	'Пустой раздел в корне каталога НОМЕР 2',	1645964485,	NULL,	'Пустой, совсем пустой',	NULL);

-- 2022-02-27 12:37:42
