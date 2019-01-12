/*
SQLyog Enterprise - MySQL GUI v8.12 
MySQL - 5.5.5-10.1.34-MariaDB : Database - csapi
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`csapi` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `csapi`;

/*Table structure for table `country` */

DROP TABLE IF EXISTS `country`;

CREATE TABLE `country` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `iso2` char(2) DEFAULT NULL,
  `iso3` char(3) DEFAULT NULL,
  `mcc` text,
  `phonePrefix` text,
  `currency` int(20) DEFAULT NULL,
  `continent` enum('Asia','Africa','North America','South America','Antarctica','Europe','Australia') DEFAULT NULL COMMENT 'Required.',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

/*Table structure for table `currency` */

DROP TABLE IF EXISTS `currency`;

CREATE TABLE `currency` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `symbol` varchar(30) DEFAULT NULL,
  `euroRelation` float NOT NULL,
  `usdRelation` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Table structure for table `gatewayoffer` */

DROP TABLE IF EXISTS `gatewayoffer`;

CREATE TABLE `gatewayoffer` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `gateway` int(20) NOT NULL,
  `mobileNetwork` int(20) DEFAULT NULL,
  `country` int(20) DEFAULT NULL,
  `TTCharacteristics` text,
  `SIDCharacteristics` text,
  `includesVat` enum('true','false') NOT NULL DEFAULT 'true',
  `perDelivered` enum('true','false') NOT NULL DEFAULT 'false',
  `negotiated` enum('true','false') DEFAULT NULL,
  `offerDate` datetime DEFAULT NULL,
  `domestic` enum('true','false') NOT NULL DEFAULT 'true',
  `international` enum('true','false') NOT NULL DEFAULT 'true',
  `currency` int(20) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `fromDate` datetime DEFAULT NULL,
  `toDate` datetime DEFAULT NULL,
  `perSenderId` enum('true','false') NOT NULL DEFAULT 'false',
  `perTaxId` enum('true','false') NOT NULL DEFAULT 'false',
  `priceWithRebate` enum('true','false') DEFAULT NULL,
  `period` int(10) DEFAULT NULL,
  `matrixEntries` text,
  `isCommitement` enum('true','false') DEFAULT NULL,
  `commitmentEntry` int(20) DEFAULT NULL,
  `fee` double DEFAULT NULL,
  `feeUnits` int(2) DEFAULT NULL,
  `prepaid` enum('true','false') NOT NULL DEFAULT 'false',
  `penality` enum('true','false') DEFAULT NULL,
  `penaltyDescription` text,
  `comments` text,
  `status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `headoperator` */

DROP TABLE IF EXISTS `headoperator`;

CREATE TABLE `headoperator` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Table structure for table `matrixentry` */

DROP TABLE IF EXISTS `matrixentry`;

CREATE TABLE `matrixentry` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `from` int(10) DEFAULT NULL,
  `to` int(20) DEFAULT NULL,
  `value` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mobilegateway` */

DROP TABLE IF EXISTS `mobilegateway`;

CREATE TABLE `mobilegateway` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `nationalCountry` text,
  `offers` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `mobilenetwork` */

DROP TABLE IF EXISTS `mobilenetwork`;

CREATE TABLE `mobilenetwork` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `mccmnc` char(6) DEFAULT NULL,
  `operator` int(20) NOT NULL,
  `mvno` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Table structure for table `mvno` */

DROP TABLE IF EXISTS `mvno`;

CREATE TABLE `mvno` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Table structure for table `operator` */

DROP TABLE IF EXISTS `operator`;

CREATE TABLE `operator` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) DEFAULT NULL,
  `country` int(20) DEFAULT NULL,
  `headOperator` int(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Table structure for table `sidcharacteristic` */

DROP TABLE IF EXISTS `sidcharacteristic`;

CREATE TABLE `sidcharacteristic` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sidregisterationrequirements` */

DROP TABLE IF EXISTS `sidregisterationrequirements`;

CREATE TABLE `sidregisterationrequirements` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `sidregistrationrequirements` */

DROP TABLE IF EXISTS `sidregistrationrequirements`;

CREATE TABLE `sidregistrationrequirements` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `ttcharacteristic` */

DROP TABLE IF EXISTS `ttcharacteristic`;

CREATE TABLE `ttcharacteristic` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
