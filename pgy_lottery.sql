-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2020 �?12 �?14 �?07:37
-- 服务器版本: 5.5.53
-- PHP 版本: 5.6.27

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `pgy_lottery`
--

-- --------------------------------------------------------

--
-- 表的结构 `pgy_probability`
--

CREATE TABLE IF NOT EXISTS `pgy_probability` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `probability` varchar(20) NOT NULL COMMENT '概率数',
  `def_num` int(10) DEFAULT NULL COMMENT '内定索引次数',
  `id_card` char(18) NOT NULL COMMENT '身份证',
  `name` varchar(255) NOT NULL COMMENT '姓名',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='抽取概率' AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `pgy_probability`
--

INSERT INTO `pgy_probability` (`id`, `probability`, `def_num`, `id_card`, `name`) VALUES
(10, '20', NULL, '42930472398723947', '郑专家');

-- --------------------------------------------------------

--
-- 表的结构 `pgy_user`
--

CREATE TABLE IF NOT EXISTS `pgy_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(255) NOT NULL COMMENT '姓名',
  `phone` varchar(15) NOT NULL COMMENT '电话',
  `id_card` char(18) NOT NULL COMMENT '身份证',
  `profession` varchar(255) DEFAULT NULL COMMENT '职称',
  `unit` varchar(255) DEFAULT NULL COMMENT '单位',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户' AUTO_INCREMENT=21 ;

--
-- 转存表中的数据 `pgy_user`
--

INSERT INTO `pgy_user` (`id`, `username`, `phone`, `id_card`, `profession`, `unit`) VALUES
(2, '李专家', '15677880099', '431224188909132256', '工人', '湖南地铁建设国有公司'),
(3, '钟专家', '15677880100', '431224188909139900', '公司职员', '湖南城建公司'),
(4, '吴专家', '15697878101', '431224188909188088', '家政职员', '湖南家家康公司'),
(5, '曹专家', '18755663215', '26487262876191132', '业务员', '曹家班武行有限公司'),
(6, '黄专家', '15688933365', '32984236892364893', '总经理', '湖南恒大'),
(7, '郑专家', '15233647755', '42930472398723947', '部门组长', '长沙矩阵物业有限公司'),
(9, '武专家', '13845962689', '32984236892123457', '职工', '长沙市红满天婚纱摄影'),
(10, '关专家', '18755663215', '26487262876456654', '经理', '湖南正业物业有限公司'),
(11, '刘专家', '15688933365', '32984236892456754', '组长', '长沙是浏阳旅游集团'),
(12, '张专家', '15233647755', '42930472398457845', '部门经理', '衡阳装饰有限公司'),
(13, '赵', '17452733872', '42930472398132546', '经理', '湖南阳光100'),
(14, '钱', '15337896345', '42930472398487544', '部长', '湖南大好时光'),
(15, '孙', '18755673546', '42930472398452115', '员工', '长沙冷链有限公司'),
(16, '李', '19067554371', '42930472398448756', '志愿者', '常德辣条'),
(17, '周', '18966487332', '42930472398457412', '志愿军', '宁乡旅游景区'),
(18, '吴', '15233647757', '42930472398456123', '环卫工', '长沙环境保卫处'),
(19, '郑', '18078665342', '45211145751542124', '协会会长', '长沙乒协'),
(20, '王', '17865336746', '42930564858723947', '所长', '天心区分局');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
