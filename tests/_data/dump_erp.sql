/*
SQLyog Community v8.71 
MySQL - 5.6.24 : Database - erps
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`erps` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `erps`;

/*Table structure for table `aju_ajustes` */

DROP TABLE IF EXISTS `aju_ajustes`;

CREATE TABLE `aju_ajustes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_ajuste` binary(16) NOT NULL,
  `uuid_bodega` binary(16) NOT NULL,
  `numero` int(8) unsigned zerofill NOT NULL,
  `descripcion` text NOT NULL,
  `tipo_ajuste_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `comentarios` text NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `aju_ajustes` */

insert  into `aju_ajustes`(`id`,`uuid_ajuste`,`uuid_bodega`,`numero`,`descripcion`,`tipo_ajuste_id`,`created_at`,`updated_at`,`created_by`,`comentarios`,`total`,`empresa_id`) values (1,'ÂßÁ€$Óè»ºvNT†','...\0\0\0\0\0\0\0\0\0\0\0\0\0',00000001,'Ajuste negativo',1,'2015-12-21 08:30:58','2015-12-21 08:30:58',1,'<p>Creando un ajuste de prueba en clientes...</p>\r\n','4069.32',1),(2,'Â®\"\r·Rè»ºvNT†','Âù%,Gïï„ºvNT†',00000002,'Alicate',2,'2015-12-21 15:33:13','2015-12-21 15:33:13',1,'','0.00',1),(3,'Â®\"E≥^*è»ºvNT†','Âù$î `Qï„ºvNT†',00000003,'Extractor',1,'2015-12-21 15:34:45','2015-12-21 15:34:45',1,'','0.00',1);

/*Table structure for table `aju_ajustes_campos` */

DROP TABLE IF EXISTS `aju_ajustes_campos`;

CREATE TABLE `aju_ajustes_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `aju_ajustes_campos` */

insert  into `aju_ajustes_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'bodega','Locaci&oacute;n',0,18,'activo','{\"class\":\"chosen bodega\"}','','div','bod_bodegas',1,'','0000-00-00 00:00:00',2),(2,'numero','N&uacute;mero de ajuste',0,14,'activo','{\"class\":\"form-control\",\"disabled\":\"true\"}','','div','',1,'','0000-00-00 00:00:00',4),(3,'descripcion','Descripci&oacute;n',0,15,'activo','{\"class\":\"form-control\",\"style\":\"height:115px;\"}','','div','',1,'','0000-00-00 00:00:00',6),(4,'tipo_ajuste','Tipo de ajuste',0,12,'activo','{\"class\":\"chosen tipo_ajuste\"}','','div','',1,'','0000-00-00 00:00:00',8),(5,'fecha','Fecha',0,22,'activo','{\"disabled\":\"true\",\"data-addon-icon\":\"fa-calendar\",\"class\":\"form-control\"}','','div','',1,'','0000-00-00 00:00:00',10),(6,'item','Item',0,18,'activo','{\"class\":\"chosen item\"}','items','tabla-dinamica','inv_items',1,'','0000-00-00 00:00:00',12),(7,'cantidad_disponible','Cantidad disponible',0,14,'activo','{\"class\":\"form-control cantidad_disponible\",\"disabled\":\"true\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',14),(8,'cantidad','cantidad',0,14,'activo','{\"class\":\"form-control cantidad\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',16),(9,'precio_unitario','Precio unitario',0,22,'activo','{\"disabled\":\"true\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control precio_unitario\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,2}\',\'greedy\':false\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',18),(10,'total','Total',0,22,'activo','{\"disabled\":\"true\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control total\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,2}\',\'greedy\':false\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',20),(11,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;',0,1,'activo','{\"class\":\"btn btn-default btn-block agregarBtn\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',22),(12,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;',0,1,'activo','{\"class\":\"btn btn-default btn-block eliminarBtn\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',24),(13,'id_ajuste_item','',0,7,'activo','{\"class\":\"id_ajuste_item\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',26),(14,'total_general','Total',0,22,'activo','{\"disabled\":\"true\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control total_general\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,2}\',\'greedy\':false\"}','','div','',1,'','0000-00-00 00:00:00',28),(15,'comentarios','Comentarios',0,15,'activo','{\"class\":\"form-control\",\"id\":\"comentarios\",\"style\":\"height:115px;\"}','','div','',0,'','0000-00-00 00:00:00',30),(16,'cancelarAjuste','Cancelar',0,8,'activo','','','div','',0,'ajustes/listar','0000-00-00 00:00:00',32),(17,'guardarAjuste','Guardar',0,13,'activo','','','div','',0,'','0000-00-00 00:00:00',34);

/*Table structure for table `aju_ajustes_cat` */

DROP TABLE IF EXISTS `aju_ajustes_cat`;

CREATE TABLE `aju_ajustes_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `aju_ajustes_cat` */

insert  into `aju_ajustes_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,4,'','Negativo'),(2,4,'','Positivo');

/*Table structure for table `aju_ajustes_items` */

DROP TABLE IF EXISTS `aju_ajustes_items`;

CREATE TABLE `aju_ajustes_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ajuste_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `cantidad_disponible` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `aju_ajustes_items` */

insert  into `aju_ajustes_items`(`id`,`ajuste_id`,`item_id`,`cantidad`,`cantidad_disponible`,`precio_unitario`) values (1,1,1,2,11,'2034.66'),(2,2,4,2,0,'0.00'),(3,3,5,0,0,'0.00');

/*Table structure for table `aju_ajustes_items_entradas_items` */

DROP TABLE IF EXISTS `aju_ajustes_items_entradas_items`;

CREATE TABLE `aju_ajustes_items_entradas_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ajuste_item_id` int(11) NOT NULL,
  `entrada_item_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `aju_ajustes_items_entradas_items` */

/*Table structure for table `bod_bodegas` */

DROP TABLE IF EXISTS `bod_bodegas`;

CREATE TABLE `bod_bodegas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_bodega` binary(16) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `contacto_principal` varchar(100) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `entrada_id` int(11) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `empresa_id` int(11) NOT NULL,
  `creado_por` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `bod_bodegas` */

insert  into `bod_bodegas`(`id`,`uuid_bodega`,`codigo`,`nombre`,`contacto_principal`,`direccion`,`telefono`,`entrada_id`,`estado`,`empresa_id`,`creado_por`,`fecha_creacion`) values (1,'uuid_lugar\0\0\0\0\0\0','QWE','Lugar 1','Jos√© P√©rez','Paitilla','12345',1,1,1,0,'0000-00-00 00:00:00'),(2,'...\0\0\0\0\0\0\0\0\0\0\0\0\0','RTY','Lugar 2','Lu√≠s Sanchez','Obarrios','54321',1,1,1,0,'0000-00-00 00:00:00'),(3,'Âù+˝˜ìï„ºvNT†','qwerty2','Bodega 32','Contacto Principal2','Direccion2','123452',1,1,1,1,'2015-12-07 00:00:00'),(4,'Âù$î `Qï„ºvNT†','432134','Juan','Juan Gonzalez','Calle 50','234-7656',2,1,1,1,'2015-12-07 00:00:00'),(5,'Âù$Ã–ùBï„ºvNT†','tgbf','Bodega Col√≥n','Carmen','Via Espa√±a ','345-6785',1,1,1,1,'2015-12-07 00:00:00'),(6,'Âù%,Gïï„ºvNT†','507mas','Bodega Chitr√©','Paola','Calle Julio Botello','789-5434',2,1,1,1,'2015-12-07 00:00:00');

/*Table structure for table `bod_bodegas_campos` */

DROP TABLE IF EXISTS `bod_bodegas_campos`;

CREATE TABLE `bod_bodegas_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `bod_bodegas_campos` */

insert  into `bod_bodegas_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'nombre','Nombre',0,14,'activo','','','div','',1,'','0000-00-00 00:00:00',2),(2,'codigo','C&oacute;digo',0,14,'activo','','','div','',1,'','0000-00-00 00:00:00',4),(3,'contacto_principal','Contacto principal',0,14,'activo','','','div','',1,'','0000-00-00 00:00:00',6),(4,'direccion','Direcci&oacute;n',0,14,'activo','','','div','',1,'','0000-00-00 00:00:00',8),(5,'telefono','Tel&eacute;fono',0,14,'activo','','','div','',1,'','0000-00-00 00:00:00',10),(6,'entrada','Recepci&oacute;n de items',0,12,'activo','{\"class\":\"chosen\"}','','div','',1,'','0000-00-00 00:00:00',12),(7,'cancelarBodega','Cancelar',0,8,'activo','','','div','',0,'bodegas/listar','0000-00-00 00:00:00',14),(8,'guardarBodega','Guardar',0,13,'activo','','','div','',0,'','0000-00-00 00:00:00',16);

/*Table structure for table `bod_bodegas_cat` */

DROP TABLE IF EXISTS `bod_bodegas_cat`;

CREATE TABLE `bod_bodegas_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `bod_bodegas_cat` */

insert  into `bod_bodegas_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,6,'','Manual'),(2,6,'','Autom&aacute;tica');

/*Table structure for table `cargos` */

DROP TABLE IF EXISTS `cargos`;

CREATE TABLE `cargos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) DEFAULT NULL,
  `departamento_id` int(11) NOT NULL,
  `codigo` varchar(150) DEFAULT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo_rata` enum('Hora','Mensual') DEFAULT NULL,
  `rata` decimal(10,2) DEFAULT NULL,
  `estado_id` tinyint(2) DEFAULT '1',
  `creado_por` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cargos_departamentos1_idx` (`departamento_id`),
  KEY `fk_cargos_empresas2_idx` (`empresa_id`),
  CONSTRAINT `fk_cargos_departamentos1` FOREIGN KEY (`departamento_id`) REFERENCES `dep_departamentos` (`id`),
  CONSTRAINT `fk_cargos_empresas1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `cargos` */

insert  into `cargos`(`id`,`empresa_id`,`departamento_id`,`codigo`,`nombre`,`descripcion`,`tipo_rata`,`rata`,`estado_id`,`creado_por`,`created_at`,`updated_at`) values (1,1,1,NULL,'Gerente','encargado principal','Mensual','3000.00',1,1,'2015-11-02 13:59:30','2015-11-16 08:39:29'),(2,1,1,NULL,'Gerente','encargado principal','Mensual','3000.00',0,1,'2015-11-02 13:59:37','2015-11-02 15:00:38'),(3,1,8,NULL,'Gerente','Principal','Hora','56.00',1,1,'2015-11-02 15:22:30','2015-11-02 15:22:30'),(4,1,8,NULL,'Gerente','Principal','Hora','56.00',0,1,'2015-11-02 15:22:38','2015-11-02 15:22:57'),(5,2,3,NULL,'Asistente','Asistente Adminitrativa','Mensual','950.00',1,1,'2015-11-06 16:13:58','2015-11-09 18:51:29'),(6,2,5,NULL,'Gerente','Gerente','Hora','5.75',1,1,'2015-11-06 16:17:21','2015-11-06 16:17:21');

/*Table structure for table `cen_centros` */

DROP TABLE IF EXISTS `cen_centros`;

CREATE TABLE `cen_centros` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_centro` binary(16) DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  `empresa_id` int(11) NOT NULL,
  `padre_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

/*Data for the table `cen_centros` */

insert  into `cen_centros`(`id`,`uuid_centro`,`nombre`,`descripcion`,`estado`,`empresa_id`,`padre_id`,`created_at`,`updated_at`) values (1,'ÂtKÜ\\·<ÆKƒ⁄&K≥','Centros 1',NULL,'Activo',1,0,NULL,'2015-11-23 15:58:01'),(2,'ÔøΩvÔøΩÔøΩtÔøΩÔ','Nuevo PAnama',NULL,'Activo',1,0,NULL,'2016-01-29 13:45:59'),(3,'ÂåõÔøΩV	ÔøΩÔøΩÔ','Centro 3','','Activo',1,0,NULL,NULL),(4,'ÂåõÔøΩgÔøΩÔøΩ','Centro 3.1','','Activo',1,3,NULL,NULL),(8,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','sucursals 1',NULL,'Activo',1,0,'2015-11-17 16:22:19','2015-11-19 11:11:24'),(9,'ÔøΩrD>jﬂµÔøΩbf','primer centro comercial','comercial','Activo',1,1,'2015-11-17 16:27:08','2015-11-23 15:58:01'),(10,'ÂçrhÙóæµ˛bfäa•','sucursal s','nuevo centro','Activo',1,8,'2015-11-17 16:30:23','2015-11-23 15:57:41'),(11,'ÂçtÔÃ¯µ˛bfäa•','salon',NULL,'Activo',1,1,'2015-11-17 16:48:28','2015-11-23 15:58:01'),(12,'ÂçÏúKkµ˛bfäa•','Panama SA','centro panama Padre1','Activo',1,0,'2015-11-18 07:01:25','2016-01-29 13:53:18'),(13,'Âéÿ=ÄÛdµ˛bfäa•','comercials 123',NULL,'Activo',1,22,'2015-11-19 11:11:49','2016-01-29 15:23:14'),(14,'Âíl8µ˛bfäa•','Otra empresa',NULL,'Activo',2,0,'2015-11-23 15:19:09','2015-11-23 15:19:09'),(15,'Âí€WúÍµ˛bfäa•','centro Universal',NULL,'Activo',3,0,'2015-11-24 13:44:20','2015-11-24 13:44:20'),(22,'Âò=∑ÀÅMµ˛bfäa•','adta',NULL,'Activo',1,0,'2015-12-01 10:11:20','2016-01-29 13:42:03'),(23,'Â∆≤©?[Çlbfäa•','Panama Hijo','Hijo de Panama','Activo',1,12,'2016-01-29 13:04:07','2016-01-29 13:04:07'),(24,'Â∆πõÜÿ`Çlbfäa•','nuevo centro viernes 29',NULL,'Activo',1,0,'2016-01-29 13:53:50','2016-01-29 13:54:29'),(25,'Â∆≈€N“ÛÇlbfäa•','comercial',NULL,'Activo',1,22,'2016-01-29 15:21:31','2016-01-29 15:21:31'),(26,'Â∆∆&e˝Çlbfäa•','Rafa padre','padre','Activo',1,0,'2016-01-29 15:23:37','2016-01-29 15:23:48'),(27,'Â∆∆6Ë:≤Çlbfäa•','hipo123',NULL,'Activo',1,26,'2016-01-29 15:24:05','2016-01-29 15:24:52'),(28,'Â∆∆iNxÇlbfäa•','hipo12es',NULL,'Activo',1,26,'2016-01-29 15:25:29','2016-01-29 15:25:44');

/*Table structure for table `ci_sessions` */

DROP TABLE IF EXISTS `ci_sessions`;

CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` blob NOT NULL,
  `user_agent` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`session_id`),
  KEY `ci_sessions_timestamp` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `ci_sessions` */

insert  into `ci_sessions`(`session_id`,`ip_address`,`last_activity`,`user_data`,`user_agent`) values ('6f2a5c8310794112ab7d47b549b15060','::1',1454102731,'a:10:{s:9:\"user_data\";s:0:\"\";s:13:\"huuid_usuario\";s:32:\"11E5741821C1213FA4610862668A61A5\";s:10:\"id_usuario\";i:10;s:6:\"nombre\";s:6:\"Rafael\";s:8:\"apellido\";s:8:\"Williams\";s:6:\"estado\";s:6:\"Activo\";s:10:\"por_vencer\";s:0:\"\";s:14:\"imagen_archivo\";N;s:5:\"roles\";a:2:{i:0;i:1;i:1;i:1;}s:12:\"uuid_empresa\";s:32:\"11EFBFBD767A010519EFBFBDEFBFBD61\";}','Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.111 Safari/537.36');

/*Table structure for table `cli_clientes` */

DROP TABLE IF EXISTS `cli_clientes`;

CREATE TABLE `cli_clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_cliente` binary(16) NOT NULL,
  `codigo` varchar(200) DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `correo` varchar(200) DEFAULT NULL,
  `web` varchar(200) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `comentario` varchar(200) DEFAULT NULL,
  `credito` decimal(10,2) DEFAULT NULL,
  `tipo_identificacion` varchar(200) DEFAULT NULL,
  `identificacion` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `toma_contacto_id` int(11) DEFAULT NULL,
  `letra` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_empresa_id` (`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `cli_clientes` */

insert  into `cli_clientes`(`id`,`uuid_cliente`,`codigo`,`nombre`,`telefono`,`correo`,`web`,`direccion`,`comentario`,`credito`,`tipo_identificacion`,`identificacion`,`created_at`,`updated_at`,`empresa_id`,`toma_contacto_id`,`letra`) values (1,'Â≠e˘›*ëûbfäa•','CUS000001','Pablo Marmol','233-4578','pablo@gmail.com',NULL,NULL,NULL,'300.00','natural','4-123-3652','2015-12-28 08:22:23','2015-12-28 08:22:23',1,1,'0'),(2,'Â≠fT ëûbfäa•','CUS000002','Juan Ramilrez','369-1328','ramirez@gmail.com',NULL,NULL,NULL,'360.00','natural','PE-3652-2145','2015-12-28 08:24:56','2015-12-28 08:24:56',1,4,'PE'),(3,'Â≠ôÅªﬁëûbfäa•','CUS000003','Marta JImenez','123-9652','Marta@gmail.com',NULL,NULL,NULL,'400.00','natural','1PI-265-6325','2015-12-28 14:28:31','2015-12-28 14:28:31',1,7,'PI'),(4,'Â≠ôøkº˜ëûbfäa•','CUS000004','bOOKS','255-5363','info@books.com','www.books.com',NULL,NULL,'5000.00','juridico','12365-569854-12365-8547','2015-12-28 14:33:13','2015-12-28 14:33:13',1,3,NULL),(5,'Â≠ö\\˜\0ãëûbfäa•','CUS000005','Juan Magan','507-6325','jam@gmail.com',NULL,'Panama, Panama','cliente viejo','5000.00','natural','N-236-974','2015-12-28 14:37:37','2015-12-28 15:46:28',1,2,'N'),(6,'Â≠§Ê/Wlëûbfäa•','CUS000006','James Doe','978-6314','jdoe@gmail.com',NULL,NULL,NULL,'6000.00','natural','N-287-1023','2015-12-28 15:53:02','2015-12-28 15:53:02',1,5,'N'),(7,'Â≠≠FÎ‚6ëûbfäa•','CUS000007','Juan Perez',NULL,'jperz@gmail.com',NULL,NULL,NULL,'50000.00','natural','123654','2015-12-28 16:53:00','2015-12-28 16:53:00',1,4,'PAS'),(8,'Â¿LÙÇ-LÇlbfäa•','CUS000008','Molina SA','623-5458','molina@gmail.com',NULL,NULL,NULL,'600000.00','juridico','12366-45425-8455-123645','2016-01-21 09:40:45','2016-01-21 09:40:45',1,4,NULL),(9,'Â∆°ósÌÀÇlbfäa•','CUS000009','Demetrio Sanchez','523-6985','dsanchez@gmail.com',NULL,NULL,NULL,'600000.00','natural','1-12365-25147','2016-01-29 11:01:56','2016-01-29 11:42:40',1,3,'0'),(10,'Â∆ÆåDÜ™Çlbfäa•','CUS000010','Julio Pruebas','362-5487','jpruebas@pensanomica.com',NULL,NULL,NULL,'50000.00','natural','9-236-32541','2016-01-29 12:34:40','2016-01-29 12:34:40',1,2,'0'),(11,'Â∆ØMxaÇlbfäa•','CUS000011','Fabricia Dominguez','632-5842','fdominguez@pensa.com',NULL,NULL,NULL,'100.00','natural','E-36255-63542','2016-01-29 12:38:40','2016-01-29 12:38:40',1,3,'E');

/*Table structure for table `cli_clientes_campos` */

DROP TABLE IF EXISTS `cli_clientes_campos`;

CREATE TABLE `cli_clientes_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` text,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Data for the table `cli_clientes_campos` */

insert  into `cli_clientes_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'codigo','Codigo',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,1),(2,'nombre','Nombre del Cliente',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,2),(3,'telefono','Tel√©fono',NULL,22,'activo','{\"data-addon-icon\":\"fa-phone\",\"class\":\"form-control\", \"data-inputmask\":\"\'mask\': \'9{1,15}.99\', \'greedy\':true\"}',NULL,'div',NULL,NULL,NULL,NULL,3),(4,'correo','Correo Electr√≥nico',NULL,22,'activo','{\"data-addon-text\":\"@\",\"class\":\"form-control debito\", \"data-inputmask\":\"\'mask\': \'9{1,15}.99\', \'greedy\':true\"}',NULL,'div',NULL,NULL,NULL,NULL,4),(5,'identificacion','Identificac√≠on',NULL,12,'activo','',NULL,'div',NULL,NULL,NULL,NULL,5),(6,'show','',NULL,7,'activo','{\"data-columns\":3,\"class\":\"example\"}',NULL,'div',NULL,NULL,NULL,NULL,6),(7,'web','Sitio Web',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,7),(8,'contacto_tipo','Toma Contacto',NULL,12,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,8),(9,'credito','L√≠mite de cr√©dito',NULL,22,'activo','{\"data-addon-text\":\"$\",\"class\":\"form-control\", \"data-inputmask\":\"\'mask\': \'9{1,15}.99\', \'greedy\':true\"}',NULL,'div',NULL,NULL,NULL,NULL,9),(10,'actual','Balace actual:',NULL,28,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,10),(11,'saldo','',NULL,22,'activo','{\"data-addon-text\":\"$\",\"class\":\"form-control debito\", \"data-inputmask\":\"\'mask\': \'9{1,15}.99\', \'greedy\':true\"}',NULL,'div',NULL,NULL,NULL,NULL,11),(12,'lcredito','',NULL,22,'activo','{\"data-addon-text\":\"$\",\"class\":\"form-control debito\", \"data-inputmask\":\"\'mask\': \'9{1,15}.99\', \'greedy\':true\"}',NULL,'div',NULL,NULL,NULL,NULL,12),(13,'direccion','Direcci√≥n',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,13),(14,'comentario','Comentarios',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,14),(15,'cancelar','Cancelar',NULL,8,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,15),(16,'guardar','Guardar',NULL,13,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,16);

/*Table structure for table `cli_clientes_catalogo` */

DROP TABLE IF EXISTS `cli_clientes_catalogo`;

CREATE TABLE `cli_clientes_catalogo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  `tipo` varchar(200) NOT NULL,
  `orden` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

/*Data for the table `cli_clientes_catalogo` */

insert  into `cli_clientes_catalogo`(`id`,`key`,`valor`,`etiqueta`,`tipo`,`orden`) values (1,'1','Bocas del Toro (1)','bocas_del_toro','provincias',1),(2,'2','Cocl√© (2)','cocle','provincias',2),(3,'3','Col√≥n (3)','colon','provincias',3),(4,'4','Chiriqu√≠ (4)','chiriqui','provincias',4),(5,'5','Dari√©n (5)','darien','provincias',5),(6,'6','Herrera (6)','herrera','provincias',6),(7,'7','Los Santos (7)','los_santos','provincias',7),(8,'8','Panam√° (8)','panama','provincias',8),(9,'9','Veraguas (9)','veraguas','provincias',9),(10,'10','Guna Yala (10)','guna_yala','provincias',10),(11,'11','Embera Wounann (11)','embera_wounann','provincias',11),(12,'12','Ng√§be-Bugl√© (12)','ngabe_bugle','provincias',12),(13,'13','Panam√° Oeste (13)','panama_oeste','provincias',13),(14,'0','0','0','letras',1),(15,'N','N','n','letras',2),(16,'PE','PE','pe','letras',3),(17,'PI','PI','pi','letras',4),(18,'PAS','PAS','pas','letras',5),(19,'E','E','e','letras',6);

/*Table structure for table `cli_clientes_catalogo_toma_contacto` */

DROP TABLE IF EXISTS `cli_clientes_catalogo_toma_contacto`;

CREATE TABLE `cli_clientes_catalogo_toma_contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  `orden` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `cli_clientes_catalogo_toma_contacto` */

insert  into `cli_clientes_catalogo_toma_contacto`(`id`,`nombre`,`etiqueta`,`orden`) values (1,'Llamada en fr√≠o','llamada_en_frio',1),(2,'Cliente existente','cliente_existente',2),(3,'Correo directo','correo_directo',3),(4,'Conferencia','conferencia',4),(5,'Sitio web','sitio_web',5),(6,'Referido','referido',6),(7,'Campa√±a','campana',7),(8,'Walk-In','walk_In',8);

/*Table structure for table `col_colaboradores` */

DROP TABLE IF EXISTS `col_colaboradores`;

CREATE TABLE `col_colaboradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) DEFAULT NULL,
  `estado_id` int(11) NOT NULL,
  `uuid_colaborador` binary(16) NOT NULL,
  `no_colaborador` varchar(200) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `segundo_nombre` varchar(200) DEFAULT NULL,
  `apellido` varchar(200) DEFAULT NULL,
  `apellido_materno` varchar(200) DEFAULT NULL,
  `cedula` varchar(200) NOT NULL,
  `seguro_social` varchar(150) DEFAULT NULL,
  `sexo_id` int(11) DEFAULT NULL,
  `estado_civil_id` int(11) DEFAULT NULL,
  `fecha_nacimiento` datetime DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `lugar_nacimiento` varchar(255) DEFAULT NULL,
  `telefono_residencial` varchar(50) DEFAULT NULL,
  `celular` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `centro_contable_id` int(11) DEFAULT NULL,
  `departamento_id` int(11) DEFAULT NULL,
  `cargo_id` int(11) DEFAULT NULL,
  `salario` decimal(11,2) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_col_colaboradores_cat` (`estado_id`),
  CONSTRAINT `fk_col_colaboradores_cat` FOREIGN KEY (`estado_id`) REFERENCES `col_colaboradores_cat` (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `col_colaboradores` */

insert  into `col_colaboradores`(`id`,`empresa_id`,`estado_id`,`uuid_colaborador`,`no_colaborador`,`nombre`,`segundo_nombre`,`apellido`,`apellido_materno`,`cedula`,`seguro_social`,`sexo_id`,`estado_civil_id`,`fecha_nacimiento`,`edad`,`lugar_nacimiento`,`telefono_residencial`,`celular`,`email`,`direccion`,`centro_contable_id`,`departamento_id`,`cargo_id`,`salario`,`creado_por`,`created_at`,`updated_at`) values (1,NULL,1,'ÂäE”wi@ï„ºvNT†',NULL,'Fredd',NULL,'Lammie',NULL,'M4-549-87',NULL,2,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,5,'950.00',NULL,'2015-11-13 15:33:40','2015-11-13 15:33:40'),(2,NULL,2,'ÂäF¯Øòï„ºvNT†',NULL,'Laura',NULL,'Carrizo',NULL,'7-899-220',NULL,1,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,'3000.00',NULL,'2015-11-13 15:41:52','2015-11-13 15:41:52');

/*Table structure for table `col_colaboradores_campos` */

DROP TABLE IF EXISTS `col_colaboradores_campos`;

CREATE TABLE `col_colaboradores_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8;

/*Data for the table `col_colaboradores_campos` */

insert  into `col_colaboradores_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'estado_id','Estado',NULL,12,'activo','{\"class\":\"chosen-select form-control\"}',NULL,'div',NULL,1,NULL,'2015-10-13 14:45:39',1),(2,'no_colaborador','No. de Colaborador',NULL,14,'activo','{\"disabled\":\"disabled\"}',NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:26',2),(3,'space','',NULL,7,'activo','{\"data-columns\":\"2\"}',NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:28',3),(4,'nombre','Nombre',NULL,14,'activo',NULL,NULL,'div',NULL,1,NULL,'2015-10-13 15:18:30',4),(5,'segundo_nombre','Segundo Nombre',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:31',5),(6,'apellido','Apellido Paterno',NULL,14,'activo',NULL,NULL,'div',NULL,1,NULL,'2015-10-13 15:18:33',6),(7,'apellido_materno','Apellido materno / Casada',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:34',7),(8,'cedula','C√©dula',NULL,14,'activo',NULL,NULL,'div',NULL,1,NULL,'2015-10-13 15:18:36',8),(9,'seguro_social','Seguro Social',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:38',9),(10,'sexo_id','Sexo',NULL,12,'activo','{\"class\":\"chosen-select form-control\"}',NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:39',10),(11,'estado_civil_id','Estado Civil',NULL,12,'activo','{\"class\":\"chosen-select form-control\"}',NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:40',11),(12,'fecha_nacimiento','Fecha de Nacimiento',NULL,20,'activo','{\"class\":\"form-control fecha\",\"data-format\":\"YYYY-MM-DD\",\"data-template\":\"D MMM YYYY\"}',NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:41',12),(13,'edad','Edad',NULL,14,'activo','{\"disabled\":\"disabled\"}',NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:43',13),(14,'lugar_nacimiento','Lugar de Nacimiento',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:44',14),(15,'telefono_residencial','Tel√©fono Residencial',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:46',15),(16,'celular','Celular',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:48',16),(17,'email','E-mail',NULL,14,'activo','{\"data-rule-email\":\"true\"}',NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:49',17),(18,'direccion','Direcci√≥n Completa',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:51',18),(19,'dependientes','Dependientes',NULL,12,'inactivo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-14 10:41:33',19),(20,'separador','Dependientes',NULL,27,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-14 10:41:34',20),(21,'nombre','Nombre',NULL,14,'activo',NULL,'dependientes','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 10:41:36',21),(22,'id_parentesco','Parentesco',NULL,14,'activo',NULL,'dependientes','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 10:41:37',22),(23,'cedula','C&eacute;dula',NULL,14,'activo',NULL,'dependientes','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 10:41:39',23),(24,'fecha_nacimiento','Fecha de Nacimiento',NULL,14,'activo','{\"class\":\"form-control fecha\",\"data-format\":\"YYYY-MM-DD\",\"data-template\":\"D MMM YYYY\"}','dependientes','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 10:41:40',24),(25,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Eliminar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block eliminarDependientesBtn disabled\"}','dependientes','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 10:41:43',25),(26,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Agregar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block agregarDependientesBtn\"}','dependientes','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 11:05:42',26),(27,'separador','Estudios',NULL,27,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-14 11:05:43',27),(28,'id_grado_academico','Grado Academico',NULL,12,'activo','{\"class\":\"chosen-select form-control\"}','estudios','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 12:57:59',28),(29,'titulo','Titulo',NULL,14,'activo',NULL,'estudios','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 12:58:00',29),(30,'institucion','Instituci&oacute;n',NULL,14,'activo',NULL,'estudios','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 12:58:01',30),(31,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Eliminar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block eliminarEstudiosBtn\"}','estudios','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 12:58:03',31),(32,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Agregar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block agregarEstudiosBtn\"}','estudios','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 12:58:04',32),(33,'separador','Trabajo Anterior',NULL,27,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-14 13:23:13',33),(34,'empresa','Nombre de Empresa',NULL,14,'activo',NULL,'experiencia','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 13:23:15',34),(35,'ocupacion','Ocupaci&oacute;n',NULL,14,'activo',NULL,'experiencia','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 13:23:16',35),(36,'fecha_salida','Fecha Salida',NULL,22,'activo','{\"readonly\":\"readonly\", \"data-addon-icon\":\"fa-calendar\",\"class\":\"form-control fecha-salida\"}','experiencia','tabla-dinamica',NULL,NULL,NULL,'2015-10-14 13:23:17',36),(37,'separador','Vacante',NULL,27,'activo',NULL,NULL,NULL,NULL,NULL,NULL,'2015-10-14 13:47:36',37),(38,'departamento_id','Departamento',NULL,18,'activo','{\"class\":\"chosen-select form-control\"}',NULL,'div','departamentos',NULL,NULL,'2015-10-14 13:47:38',38),(39,'cargo_id','Cargo',NULL,12,'activo','{\"disabled\":\"disabled\", \"class\":\"chosen-select form-control\"}',NULL,'div',NULL,NULL,NULL,'2015-10-14 13:47:39',39),(40,'salario','Salario Hora/Mensual',NULL,22,'activo','{\"data-addon-text\":\"$\",\"class\":\"form-control\", \"data-inputmask\":\"\'mask\': \'9[9][9][.*{1,20}]\', \'greedy\':true\"}',NULL,'div',NULL,NULL,NULL,'2015-10-14 14:54:11',40),(41,'cancelarFormBtn','Cancelar',NULL,8,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-14 14:54:13',41),(42,'guardarFormBtn','Guardar',NULL,13,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-15 10:32:25',42),(43,'estatura','Estatura',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 11:05:51',43),(44,'peso','Peso (libras)',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 11:05:50',44),(45,'talla','Talla de uniforme',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 11:05:49',45),(46,'no_botas','No de botas',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 11:05:47',46),(47,'separador','Datos para p&oacute;liza colectiva',NULL,27,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 13:12:24',47),(48,'texto','Beneficiarios principales de la p&oacute;liza colectiva',NULL,28,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 13:12:25',48),(49,'nombre','Nombre',NULL,14,'activo',NULL,'beneficiario_principal','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:12:28',49),(50,'parentezco_id','Parentezco',NULL,14,'activo',NULL,'beneficiario_principal','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:13:13',50),(51,'cedula','C&eacute;dula',NULL,14,'activo',NULL,'beneficiario_principal','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:13:38',51),(52,'porcentaje','Porcentaje (&#37;)',NULL,14,'activo',NULL,'beneficiario_principal','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:15:02',52),(53,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Eliminar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block eliminarBenPrinBtn\"}','beneficiario_principal','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:16:15',53),(54,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Agregar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block agregarBenPrinBtn\"}','beneficiario_principal','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:16:16',54),(55,'texto','Beneficiarios contingentes de la p&oacute;liza colectiva',NULL,28,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 13:42:43',55),(56,'nombre','Nombre',NULL,14,'activo',NULL,'beneficiario_contingente','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:45:16',56),(57,'parentezco_id','Parentezco',NULL,14,'activo',NULL,'beneficiario_contingente','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:45:17',57),(58,'cedula','C&eacute;dula',NULL,14,'activo',NULL,'beneficiario_contingente','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:45:18',58),(59,'porcentaje','Porcentaje (&#37;)',NULL,14,'activo',NULL,'beneficiario_contingente','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:45:19',59),(60,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Eliminar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block eliminarBenConBtn\"}','beneficiario_contingente','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:45:21',60),(61,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Agregar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block agregarBenConBtn\"}','beneficiario_contingente','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:45:22',61),(62,'texto','Datos generales de parientes que no dependen',NULL,28,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 13:50:30',62),(63,'nombre','Nombre',NULL,14,'activo',NULL,'beneficiario_parientes','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:52:38',63),(64,'parentezco_id','Parentezco',NULL,14,'activo',NULL,'beneficiario_parientes','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:52:40',64),(65,'cedula','C&eacute;dula',NULL,14,'activo',NULL,'beneficiario_parientes','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:52:41',65),(66,'porcentaje','Porcentaje (&#37;)',NULL,14,'activo',NULL,'beneficiario_parientes','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:52:42',66),(67,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Eliminar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block eliminarBenParBtn\"}','beneficiario_parientes','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:52:44',67),(68,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Agregar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block agregarBenParBtn\"}','beneficiario_parientes','tabla-dinamica',NULL,NULL,NULL,'2015-11-16 13:52:45',68),(69,'tutor_nombre','Tutor de los menores',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:49',69),(70,'tutor_parentezco_id','Parentezco',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',70),(71,'tutor_cedula','C&eacute;dula',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',71),(72,'space','',NULL,7,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',72),(73,'designado_mortuorio','Gastos de Mortuoria',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',73),(74,'designado_parentezco_id','Parentezco',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',74),(75,'designado_cedula','C&eacute;dula',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',75),(76,'space','',NULL,7,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',76),(77,'consulta_medica','&#191;Ha consultado alg&uacute;n m&eacute;dico\r\nen los √∫ltimos 5 a√±os&#63;',NULL,12,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',77),(78,'fecha','Fecha',NULL,22,'activo','{\"readonly\":\"readonly\", \"data-addon-icon\":\"fa-calendar\",\"class\":\"form-control fecha-consulta-medica\"}',NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',78),(79,'nombre_medico','M&eacute;dico',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',79),(80,'causas','Causas',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',80),(81,'examen','Examen',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',81),(82,'resultado','Resultado',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',82),(83,'space','',NULL,7,'activo','{\"data-columns\":2}',NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',83),(84,'sufre_enfermedad','Sufre de alguna enfermedad o lesi&oacute;n&#63;',NULL,12,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 14:33:50',84),(85,'cual_enfermedad','C&uacute;al',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 15:07:05',85),(86,'sometido_tratamiento','&#191;Ha sido sometido a tratamiento&#63;',NULL,12,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 15:07:06',86),(87,'explicar_enfermedad','Explicar',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 15:07:08',87),(88,'tiene_otro_seguro_vida','&#191;Tiene otro seguro de vida&#63;',NULL,12,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 15:07:09',88),(89,'compania','Compa&ntilde;ia',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 15:07:10',89),(90,'valor','Valor',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 15:07:12',90),(91,'cancelarFormBtn','Cancelar',NULL,8,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 15:16:00',91),(92,'guardarFormBtn','Guardar',NULL,13,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-16 15:16:02',92);

/*Table structure for table `col_colaboradores_cat` */

DROP TABLE IF EXISTS `col_colaboradores_cat`;

CREATE TABLE `col_colaboradores_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `col_colaboradores_cat` */

insert  into `col_colaboradores_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,1,'activo','Activo'),(2,1,'pasivo','Pasivo'),(3,1,'vacaciones','Vacaciones'),(4,1,'licencia','Licencia'),(5,1,'cesado','Cesado'),(6,28,'bachiller','Bachiller'),(7,28,'diplomado','Diplomado'),(8,28,'tecnico','Tecnico'),(9,28,'licenciatura','Licenciatura'),(10,28,'post_grado','Post Grado'),(11,28,'maestria','Maestria'),(12,28,'doctorado','Doctorado');

/*Table structure for table `col_dependientes` */

DROP TABLE IF EXISTS `col_dependientes`;

CREATE TABLE `col_dependientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `colaborador_id` int(11) NOT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `id_parentezco` int(11) DEFAULT NULL,
  `cedula` varchar(11) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_col_colaboradores1_idx` (`colaborador_id`),
  CONSTRAINT `fk_col_colaboradores1` FOREIGN KEY (`colaborador_id`) REFERENCES `col_colaboradores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `col_dependientes` */

insert  into `col_dependientes`(`id`,`colaborador_id`,`nombre`,`id_parentezco`,`cedula`,`fecha_nacimiento`,`created_at`,`updated_at`) values (1,1,'Esther',NULL,'8-432-567','2008-04-02','2015-10-26 11:15:56',NULL),(2,2,'Alvarez S√°nchez',NULL,'8-322-567','2008-03-02','2015-10-26 11:23:54',NULL);

/*Table structure for table `col_estudios` */

DROP TABLE IF EXISTS `col_estudios`;

CREATE TABLE `col_estudios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `colaborador_id` int(11) NOT NULL,
  `id_grado_academico` int(11) NOT NULL,
  `titulo` varchar(150) DEFAULT NULL,
  `institucion` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_col_colaboradores2_idx` (`colaborador_id`),
  CONSTRAINT `fk_col_colaboradores2` FOREIGN KEY (`colaborador_id`) REFERENCES `col_colaboradores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `col_estudios` */

insert  into `col_estudios`(`id`,`colaborador_id`,`id_grado_academico`,`titulo`,`institucion`,`created_at`,`updated_at`) values (1,1,6,'Letra','Escuela Isauro Jose Carrizo','2015-10-26 11:15:56',NULL),(2,2,6,'Comercio','Escuela Primaria de Atalaya','2015-10-26 11:23:54',NULL);

/*Table structure for table `col_experiencia_laboral` */

DROP TABLE IF EXISTS `col_experiencia_laboral`;

CREATE TABLE `col_experiencia_laboral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `colaborador_id` int(11) NOT NULL,
  `empresa` varchar(150) DEFAULT NULL COMMENT 'Nombre de la Empresa',
  `ocupacion` varchar(200) DEFAULT NULL,
  `fecha_salida` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`colaborador_id`),
  CONSTRAINT `fk_col_colaboradores3` FOREIGN KEY (`colaborador_id`) REFERENCES `col_colaboradores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `col_experiencia_laboral` */

insert  into `col_experiencia_laboral`(`id`,`colaborador_id`,`empresa`,`ocupacion`,`fecha_salida`,`created_at`,`updated_at`) values (1,1,NULL,'Ayudante General',NULL,'2015-10-26 11:15:56',NULL),(2,2,NULL,'Ayudante General','2015-10-08','2015-10-26 11:23:54',NULL);

/*Table structure for table `com_acumuladas` */

DROP TABLE IF EXISTS `com_acumuladas`;

CREATE TABLE `com_acumuladas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `acumulada_id` int(11) DEFAULT NULL,
  `comision_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `com_acumuladas_ibfk_1` (`comision_id`),
  CONSTRAINT `com_acumuladas_ibfk_1` FOREIGN KEY (`comision_id`) REFERENCES `com_comisiones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

/*Data for the table `com_acumuladas` */

insert  into `com_acumuladas`(`id`,`acumulada_id`,`comision_id`,`fecha_creacion`) values (1,7,66,'2015-11-18 16:47:05'),(2,6,66,'2015-11-18 16:47:05'),(3,7,67,'2015-11-18 16:49:22'),(4,6,67,'2015-11-18 16:49:22'),(5,7,68,'2015-11-18 16:51:18'),(6,6,68,'2015-11-18 16:51:18'),(7,7,69,'2015-11-18 16:53:24'),(8,6,69,'2015-11-18 16:53:24'),(9,7,70,'2015-11-18 16:53:58'),(10,6,70,'2015-11-18 16:53:58'),(11,7,71,'2015-11-18 16:54:21'),(12,6,71,'2015-11-18 16:54:21'),(13,7,72,'2015-11-18 16:55:18'),(14,6,72,'2015-11-18 16:55:18'),(15,7,73,'2015-11-18 16:56:02'),(16,6,73,'2015-11-18 16:56:02'),(17,7,74,'2015-11-18 16:56:14'),(18,6,74,'2015-11-18 16:56:14'),(19,7,75,'2015-11-18 16:56:21'),(20,6,75,'2015-11-18 16:56:21'),(21,7,76,'2015-11-18 16:56:44'),(22,6,76,'2015-11-18 16:56:44'),(23,7,77,'2015-11-18 16:57:27'),(24,6,77,'2015-11-18 16:57:27'),(25,7,78,'2015-11-18 17:02:56'),(26,6,78,'2015-11-18 17:02:56'),(27,7,79,'2015-11-18 17:09:20'),(28,6,79,'2015-11-18 17:09:20'),(29,7,80,'2015-11-18 17:24:41'),(30,6,80,'2015-11-18 17:24:41'),(31,7,81,'2015-11-18 17:31:14'),(32,6,81,'2015-11-18 17:31:14'),(33,7,82,'2015-11-18 17:40:40'),(34,7,83,'2015-11-19 08:07:47'),(35,6,83,'2015-11-19 08:07:47');

/*Table structure for table `com_colaboradores` */

DROP TABLE IF EXISTS `com_colaboradores`;

CREATE TABLE `com_colaboradores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comision_id` int(11) DEFAULT NULL,
  `colaborador_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comision_id` (`comision_id`),
  KEY `colaborador_id` (`colaborador_id`),
  CONSTRAINT `com_colaboradores_ibfk_2` FOREIGN KEY (`comision_id`) REFERENCES `com_comisiones` (`id`),
  CONSTRAINT `com_colaboradores_ibfk_3` FOREIGN KEY (`colaborador_id`) REFERENCES `col_colaboradores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;

/*Data for the table `com_colaboradores` */

insert  into `com_colaboradores`(`id`,`comision_id`,`colaborador_id`,`created_at`,`updated_at`) values (1,66,1,'2015-11-18 16:47:05','2015-11-18 16:47:05'),(2,66,2,'2015-11-18 16:47:05','2015-11-18 16:47:05'),(3,66,3,'2015-11-18 16:47:05','2015-11-18 16:47:05'),(4,67,2,'2015-11-18 16:49:22','2015-11-18 16:49:22'),(5,67,2,'2015-11-18 16:49:22','2015-11-18 16:49:22'),(6,67,3,'2015-11-18 16:49:22','2015-11-18 16:49:22'),(7,68,2,'2015-11-18 16:51:18','2015-11-18 16:51:18'),(8,68,2,'2015-11-18 16:51:18','2015-11-18 16:51:18'),(9,68,3,'2015-11-18 16:51:18','2015-11-18 16:51:18'),(10,69,2,'2015-11-18 16:53:24','2015-11-18 16:53:24'),(11,69,2,'2015-11-18 16:53:24','2015-11-18 16:53:24'),(12,69,3,'2015-11-18 16:53:24','2015-11-18 16:53:24'),(13,70,2,'2015-11-18 16:53:58','2015-11-18 16:53:58'),(14,70,2,'2015-11-18 16:53:58','2015-11-18 16:53:58'),(15,70,3,'2015-11-18 16:53:58','2015-11-18 16:53:58'),(16,71,2,'2015-11-18 16:54:21','2015-11-18 16:54:21'),(17,71,2,'2015-11-18 16:54:21','2015-11-18 16:54:21'),(18,71,3,'2015-11-18 16:54:21','2015-11-18 16:54:21'),(19,72,2,'2015-11-18 16:55:18','2015-11-18 16:55:18'),(20,72,2,'2015-11-18 16:55:18','2015-11-18 16:55:18'),(21,72,3,'2015-11-18 16:55:18','2015-11-18 16:55:18'),(22,73,2,'2015-11-18 16:56:02','2015-11-18 16:56:02'),(23,73,2,'2015-11-18 16:56:02','2015-11-18 16:56:02'),(24,73,3,'2015-11-18 16:56:02','2015-11-18 16:56:02'),(25,74,2,'2015-11-18 16:56:14','2015-11-18 16:56:14'),(26,74,2,'2015-11-18 16:56:14','2015-11-18 16:56:14'),(27,74,3,'2015-11-18 16:56:14','2015-11-18 16:56:14'),(28,75,2,'2015-11-18 16:56:21','2015-11-18 16:56:21'),(29,75,2,'2015-11-18 16:56:21','2015-11-18 16:56:21'),(30,75,3,'2015-11-18 16:56:21','2015-11-18 16:56:21'),(31,76,2,'2015-11-18 16:56:44','2015-11-18 16:56:44'),(32,76,2,'2015-11-18 16:56:44','2015-11-18 16:56:44'),(33,76,3,'2015-11-18 16:56:44','2015-11-18 16:56:44'),(34,77,2,'2015-11-18 16:57:27','2015-11-18 16:57:27'),(35,77,2,'2015-11-18 16:57:27','2015-11-18 16:57:27'),(36,77,3,'2015-11-18 16:57:27','2015-11-18 16:57:27'),(37,78,2,'2015-11-18 17:02:56','2015-11-18 17:02:56'),(38,78,2,'2015-11-18 17:02:56','2015-11-18 17:02:56'),(39,78,3,'2015-11-18 17:02:56','2015-11-18 17:02:56'),(40,79,2,'2015-11-18 17:09:20','2015-11-18 17:09:20'),(41,80,2,'2015-11-18 17:24:41','2015-11-18 17:24:41'),(42,80,2,'2015-11-18 17:24:41','2015-11-18 17:24:41'),(43,81,2,'2015-11-18 17:31:14','2015-11-18 17:31:14'),(44,81,2,'2015-11-18 17:31:14','2015-11-18 17:31:14'),(45,82,2,'2015-11-18 17:40:40','2015-11-18 17:40:40'),(46,82,2,'2015-11-18 17:40:40','2015-11-18 17:40:40'),(47,83,2,'2015-11-19 08:07:47','2015-11-19 08:07:47'),(48,83,2,'2015-11-19 08:07:47','2015-11-19 08:07:47'),(49,83,3,'2015-11-19 08:07:47','2015-11-19 08:07:47'),(50,83,4,'2015-11-19 08:07:47','2015-11-19 08:07:47');

/*Table structure for table `com_comisiones` */

DROP TABLE IF EXISTS `com_comisiones`;

CREATE TABLE `com_comisiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_comision` binary(16) DEFAULT NULL,
  `nombre` varchar(240) DEFAULT NULL,
  `centro_contable` int(11) DEFAULT NULL,
  `cuenta_contable` int(11) DEFAULT NULL,
  `metodo_pago` int(11) DEFAULT NULL,
  `fecha_aplicar` timestamp NULL DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `descripcion` varchar(240) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `fecha_creacion` date DEFAULT '0000-00-00',
  `estado_id` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `centro_contable` (`centro_contable`),
  KEY `cuenta_contable` (`cuenta_contable`),
  KEY `metodo_pago` (`metodo_pago`),
  KEY `empresa_id` (`empresa_id`),
  KEY `estado_id` (`estado_id`),
  CONSTRAINT `com_comisiones_ibfk_1` FOREIGN KEY (`centro_contable`) REFERENCES `cen_centros` (`id`),
  CONSTRAINT `com_comisiones_ibfk_2` FOREIGN KEY (`cuenta_contable`) REFERENCES `contab_cuentas` (`id`),
  CONSTRAINT `com_comisiones_ibfk_3` FOREIGN KEY (`metodo_pago`) REFERENCES `com_comisiones_cat` (`id_cat`),
  CONSTRAINT `com_comisiones_ibfk_4` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `com_comisiones_ibfk_5` FOREIGN KEY (`estado_id`) REFERENCES `com_comisiones_cat` (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;

/*Data for the table `com_comisiones` */

insert  into `com_comisiones`(`id`,`uuid_comision`,`nombre`,`centro_contable`,`cuenta_contable`,`metodo_pago`,`fecha_aplicar`,`monto`,`descripcion`,`empresa_id`,`fecha_creacion`,`estado_id`) values (66,'Âé=ÊÅ<ﬂç≠º_Ù°ãí','CM1',3,12,NULL,'2015-11-18 00:00:00','788.00','2',1,'2015-11-18',16),(67,'Âé>7‘≥+ç≠º_Ù°ãí','CM9',9,12,NULL,'2015-11-18 00:00:00','788.00','16',1,'2015-11-18',16),(68,'Âé>},[‘ç≠º_Ù°ãí','CM10',3,12,NULL,'2015-11-18 00:00:00','788.00','15',1,'2015-11-18',16),(69,'Âé>»òÈç≠º_Ù°ãí','CM11',9,12,NULL,'2015-11-18 00:00:00','788.00','14',1,'2015-11-18',16),(70,'Âé>‹Ã˛zç≠º_Ù°ãí','CM12',3,12,NULL,'2015-11-18 00:00:00','788.00','13',1,'2015-11-18',16),(71,'Âé>Í∂ç≠º_Ù°ãí','CM13',3,12,NULL,'2015-11-18 00:00:00','788.00','12',1,'2015-11-18',16),(72,'Âé?é¿Ÿç≠º_Ù°ãí','CM8',3,12,NULL,'2015-11-18 00:00:00','788.00','11',1,'2015-11-18',16),(73,'Âé?&∂oç≠º_Ù°ãí','CM7',3,12,NULL,'2015-11-18 00:00:00','788.00','10',1,'2015-11-18',16),(74,'Âé?-∂,ﬂç≠º_Ù°ãí','CM6',3,12,NULL,'2015-11-18 00:00:00','788.00','9',1,'2015-11-18',16),(75,'Âé?2§Óç≠º_Ù°ãí','CM5',3,12,NULL,'2015-11-18 00:00:00','788.00','8',1,'2015-11-18',16),(76,'Âé??Æjç≠º_Ù°ãí','CM4',9,12,NULL,'2015-11-18 00:00:00','788.00','7',1,'2015-11-18',16),(77,'Âé?YÑ{ç≠º_Ù°ãí','CM3',9,12,NULL,'2015-11-18 00:00:00','788.00','6',1,'2015-11-18',16),(78,'Âé@Ä*	ç≠º_Ù°ãí','CM2',9,12,NULL,'2015-11-18 00:00:00','788.00','5',1,'2015-11-18',16),(79,'ÂéAŒ ç≠º_Ù°ãí','CM14',3,12,NULL,'2015-11-18 00:00:00','788.00','4',1,'2015-11-18',16),(80,'ÂéC&¯d,ç≠º_Ù°ãí','CM15',3,12,14,'2015-11-18 00:00:00','89.00','3',1,'2015-11-18',16),(81,'ÂéDWGΩç≠º_Ù°ãí','CM16',3,12,13,'2015-12-24 00:00:00','455.00','17',1,'2015-11-18',16),(82,'ÂéEbæÚaç≠º_Ù°ãí','CM17',3,12,13,'2015-11-25 00:00:00','788.00','1',1,'2015-11-18',16),(83,'ÂéæÉLíç≠º_Ù°ãí','CM18',3,12,13,'2015-11-20 00:00:00','0.00','18',1,'2015-11-19',16),(84,NULL,'001',1,6,NULL,NULL,NULL,NULL,NULL,'0000-00-00',NULL);

/*Table structure for table `com_comisiones_campos` */

DROP TABLE IF EXISTS `com_comisiones_campos`;

CREATE TABLE `com_comisiones_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `com_comisiones_campos` */

insert  into `com_comisiones_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'nombre','C√≥digo',NULL,14,'activo','',NULL,'div',NULL,1,NULL,'2015-10-13 14:45:39',1),(2,'centro_contable','Centro Contable',NULL,18,'activo','{\"class\":\"chosen-select form-control\"}',NULL,'div','cen_centros',NULL,NULL,'2015-10-13 15:18:26',2),(3,'colaboradores][','Nombre de colaborador',NULL,18,'activo','{\"class\":\"chosen-select form-control\", \"multiple\":\"multitple\"}','nombres','div','col_colaboradores',1,NULL,'2015-10-13 15:18:28',3),(4,'cuenta_contable','Cuenta Contable',NULL,18,'activo','{\"class\":\"chosen-select form-control\"}',NULL,'div','contab_cuentas',1,NULL,'2015-10-13 15:18:30',4),(5,'metodo_pago','M√©todo de Pago',NULL,12,'activo',NULL,NULL,'div',NULL,1,NULL,'2015-10-13 15:18:31',5),(6,'fecha_aplicar','Fecha para aplicar',NULL,14,'activo','',NULL,'div',NULL,1,NULL,'2015-10-13 15:18:33',6),(7,'monto','Monto',NULL,22,'activo','{\"data-addon-text\":\"$\",  \"data-inputmask\":\"\'mask\': \'9{1,15}.99\', \'greedy\' : true\"}  ',NULL,'div',NULL,1,NULL,'2015-10-13 15:18:34',7),(8,'deducciones][','Deducciones aplicables',NULL,12,'activo','{\"class\":\"chosen-select form-control\", \"multiple\":\"multitple\"}','deducciones','div','pln_planilla_dias_feriados_deducciones',1,NULL,'2015-10-13 15:18:36',8),(9,'acumuladas][','Acumulados aplicables',NULL,12,'activo','{\"class\":\"chosen-select form-control\", \"multiple\":\"multitple\"}','acumuladas','div','pln_planilla_dias_feriados_acumulados',1,NULL,'2015-10-13 15:18:36',8),(10,'descripcion','Descripci√≥n',NULL,14,'activo','{\"data-columns\":\"3\"}',NULL,'div',NULL,NULL,NULL,'2015-10-13 15:18:39',10),(11,'cancelarFormBtn','Cancelar',NULL,8,'activo',NULL,NULL,'div',NULL,NULL,'comisiones/listar','2015-10-14 14:54:13',41),(12,'guardarFormBtn','Guardar',NULL,13,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-10-15 10:32:25',42);

/*Table structure for table `com_comisiones_cat` */

DROP TABLE IF EXISTS `com_comisiones_cat`;

CREATE TABLE `com_comisiones_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Data for the table `com_comisiones_cat` */

insert  into `com_comisiones_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,8,'vacaciones','Vacaciones'),(2,8,'XIII mes','XIII mes'),(3,8,'prima de antiguedad','Prima de Antiguedad'),(4,8,'indemnizacion','Indemnizaci√≥n'),(5,9,'bachiller','Seguro Social'),(6,9,'diplomado','Seguro Educativo'),(7,9,'tecnico','Impuesto Sobre la Renta'),(13,5,'ach','ACH'),(14,5,'cheque','Cheque'),(15,5,'efectivo','Efectivo'),(16,0,'abierta','Abierta'),(17,0,'cerrada','Cerrada'),(18,0,'en proceso','En Proceso');

/*Table structure for table `com_deducciones` */

DROP TABLE IF EXISTS `com_deducciones`;

CREATE TABLE `com_deducciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deduccion_id` int(11) DEFAULT NULL,
  `comision_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `colaborador_id` (`comision_id`),
  CONSTRAINT `com_deducciones_ibfk_2` FOREIGN KEY (`comision_id`) REFERENCES `com_comisiones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1;

/*Data for the table `com_deducciones` */

insert  into `com_deducciones`(`id`,`deduccion_id`,`comision_id`,`fecha_creacion`) values (1,4,66,'2015-11-18 16:47:05'),(2,3,66,'2015-11-18 16:47:05'),(3,1,66,'2015-11-18 16:47:05'),(4,4,67,'2015-11-18 16:49:21'),(5,3,67,'2015-11-18 16:49:21'),(6,1,67,'2015-11-18 16:49:21'),(7,4,68,'2015-11-18 16:51:18'),(8,3,68,'2015-11-18 16:51:18'),(9,1,68,'2015-11-18 16:51:18'),(10,4,69,'2015-11-18 16:53:23'),(11,3,69,'2015-11-18 16:53:23'),(12,1,69,'2015-11-18 16:53:23'),(13,4,70,'2015-11-18 16:53:58'),(14,3,70,'2015-11-18 16:53:58'),(15,1,70,'2015-11-18 16:53:58'),(16,4,71,'2015-11-18 16:54:20'),(17,3,71,'2015-11-18 16:54:20'),(18,1,71,'2015-11-18 16:54:20'),(19,4,72,'2015-11-18 16:55:18'),(20,3,72,'2015-11-18 16:55:18'),(21,1,72,'2015-11-18 16:55:18'),(22,4,73,'2015-11-18 16:56:02'),(23,3,73,'2015-11-18 16:56:02'),(24,1,73,'2015-11-18 16:56:02'),(25,4,74,'2015-11-18 16:56:14'),(26,3,74,'2015-11-18 16:56:14'),(27,1,74,'2015-11-18 16:56:14'),(28,4,75,'2015-11-18 16:56:21'),(29,3,75,'2015-11-18 16:56:21'),(30,1,75,'2015-11-18 16:56:21'),(31,4,76,'2015-11-18 16:56:44'),(32,3,76,'2015-11-18 16:56:44'),(33,1,76,'2015-11-18 16:56:44'),(34,4,77,'2015-11-18 16:57:27'),(35,3,77,'2015-11-18 16:57:27'),(36,4,78,'2015-11-18 17:02:56'),(37,3,78,'2015-11-18 17:02:56'),(38,4,79,'2015-11-18 17:09:20'),(39,3,79,'2015-11-18 17:09:20'),(40,4,80,'2015-11-18 17:24:41'),(41,3,80,'2015-11-18 17:24:41'),(42,4,81,'2015-11-18 17:31:14'),(43,3,81,'2015-11-18 17:31:14'),(44,4,82,'2015-11-18 17:40:40'),(45,4,83,'2015-11-19 08:07:47'),(46,3,83,'2015-11-19 08:07:47');

/*Table structure for table `con_contactos` */

DROP TABLE IF EXISTS `con_contactos`;

CREATE TABLE `con_contactos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_contacto` binary(16) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `cargo` varchar(150) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `correo` varchar(200) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `comentario` varchar(200) DEFAULT NULL,
  `principal` tinyint(1) DEFAULT '0',
  `cliente_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `empresa_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

/*Data for the table `con_contactos` */

insert  into `con_contactos`(`id`,`uuid_contacto`,`nombre`,`cargo`,`telefono`,`celular`,`correo`,`direccion`,`comentario`,`principal`,`cliente_id`,`created_at`,`updated_at`,`empresa_id`) values (1,'Â!¢\"∂Û≠õvºvNT†','Carlos Palma','CEO','2517043','65952315','carlos@gmail.com','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum>\n','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum',0,1,'2015-12-28 16:53:00','2016-01-06 08:34:51',1),(2,'Â$≤¬=HõvºvNT†','Linda','Pasacable √±√≥√±o','','','','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(3,'Â%ÇæoÜõvºvNT†','Frank','Vendedor','230-3030','6060606','corre@correo.com','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(4,'Â%éM<AÒõvºvNT†','Andrea','Gerente','2325252','633633','azarate@pensanomica.com','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(5,'Â&i>ßí&õvºvNT†','Don','','25879598','36587984','','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(6,'Â&k≠ü4…õvºvNT†','Kael','Gerente General','230899','678768','correo@correo.com','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(7,'Â&mlã&%õvºvNT†','Joaquin','','2587987','698711','','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum','Lorem Ipsum, y m√°s recientemente con software de autoedici√≥n, como por ejemplo Aldus PageMaker, el cual incluye versiones de Lorem Ipsum',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(8,'Â&pÆZ°1õvºvNT†','Jorge','','','','','','',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(9,'Â\'=ãÕôBõvºvNT†','Diana','vendedor','','','','','',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(10,'Â\'=œI\nÿõvºvNT†','Said','','','','','','',0,NULL,'2015-12-28 16:53:00','2015-12-28 16:53:00',1),(11,'ÂØ2fﬁÇëûbfäa•','Dorindo Cardenas','Panadero','789-6325','6325-1263','dcardenas@gmail.com',NULL,NULL,0,1,'2015-12-30 15:18:28','2016-01-06 08:34:54',1),(12,'ÂØ3üOìëûbfäa•','Luis Suarez','Salonero','123-6545','3256-9845','lsuarez@gmail.com',NULL,NULL,1,1,'2015-12-30 15:27:12','2016-01-06 08:34:54',1),(13,'ÂØ4&ë∂˘ëûbfäa•','Raul Miranda','Panadero','123-6548','6985-2365','rmirando@gmail.com',NULL,NULL,0,1,'2015-12-30 15:30:59','2015-12-30 15:30:59',1),(14,'ÂØ4‹îˇçëûbfäa•','Foo Bar','Gerente','954-3214','6289-6325','foo@gmail.com',NULL,NULL,0,1,'2015-12-30 15:36:05','2015-12-30 15:36:05',1),(15,'Â≤˙Ω˝\0/ëûbfäa•','Juan Molina','Soporte','789-6521','6325-4958','jmolina@gmail.com',NULL,NULL,1,4,'2016-01-04 10:50:07','2016-01-29 15:53:23',1),(16,'Â≤ˇsJ ëûbfäa•','Juana Doe',NULL,'759-8645','6325-9854','Jdoe@gmail.com','Panama','Test',1,6,'2016-01-04 11:23:49','2016-01-04 11:39:38',1),(17,'Â¿M<Çlbfäa•','Luis Calle','empresario','632-5458','6525-4852','lcalle@gmail.com',NULL,NULL,0,8,'2016-01-21 09:41:55','2016-01-21 09:41:55',1),(18,'Â∆°◊6ÂÇlbfäa•','Familiar miranda','logistica','236-5587','6958-4213','fmiranda@gmail.com','','nuevo comentario',1,9,'2016-01-29 11:03:43','2016-01-29 12:27:52',1),(19,'Â∆ßùk«¢Çlbfäa•','Familiar Jimenez','logistica','233-6544','6325-4878','fji@gmail.com',NULL,NULL,0,9,'2016-01-29 11:45:03','2016-01-29 11:45:03',1),(20,'Â∆≠d∑5∂Çlbfäa•','Justino Miranda','Salonero','236-5445','3265-9554','jmiranda@pensanomica.com',NULL,NULL,0,9,'2016-01-29 12:26:24','2016-01-29 12:26:24',1),(21,'Â∆Æ™A≈Çlbfäa•','Mirna Pruebas','Mesera','123-6548','6569-8512','mpruebas@pensanomica.com',NULL,NULL,0,10,'2016-01-29 12:35:31','2016-01-29 12:35:31',1),(22,'Â∆Ø„>\Z.Çlbfäa•','Jhon Dominguez','Empresario','365-2145','3625-1458','jdominguez@pensa.com',NULL,NULL,0,11,'2016-01-29 12:44:16','2016-01-29 12:44:16',1);

/*Table structure for table `con_contactos_campos` */

DROP TABLE IF EXISTS `con_contactos_campos`;

CREATE TABLE `con_contactos_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT NULL,
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla','tabla-dinamica') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

/*Data for the table `con_contactos_campos` */

insert  into `con_contactos_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (18,'imagen_archivo','',NULL,6,'inactivo',NULL,NULL,'div',NULL,0,NULL,'2015-04-21 12:17:54',1),(19,'nombre','Nombre',NULL,14,'activo','{\"class\":\"form-control\", \"ng-model\":\"contacto.nombre\"}',NULL,'div',NULL,1,NULL,'2015-04-21 12:21:10',2),(20,'apellido','Apellido',NULL,14,'inactivo',NULL,NULL,'div',NULL,0,NULL,'2015-04-21 12:21:34',3),(21,'mas_informacion','M√°s Informaci√≥n',NULL,2,'inactivo','{\"class\":\"form-control js-switch\"}',NULL,'div',NULL,0,NULL,'2015-04-21 12:33:48',4),(22,'apellido_materno','Apellido Materno',NULL,14,'inactivo','{\"data-hide-field\":\"true\"}',NULL,'div',NULL,0,NULL,'2015-04-21 12:33:50',5),(23,'apellido_casada','Apellido Casada',NULL,14,'inactivo','{\"data-hide-field\":\"true\"}',NULL,'div',NULL,0,NULL,'2015-04-21 12:33:51',6),(24,'cargo','Cargo',NULL,14,'activo','{\"class\":\"form-control\", \"ng-model\":\"contacto.cargo\"}',NULL,'div',NULL,0,NULL,'2015-04-21 12:33:53',10),(26,'celular','Celular',NULL,22,'activo','{\"data-addon-icon\":\"fa-mobile\", \"class\":\"form-control\", \"ng-model\":\"contacto.celular\", \"data-inputmask\":\"\'mask\': \'9999-9999\', \'greedy\':true\"}',NULL,'div',NULL,0,NULL,'2015-04-21 13:06:13',8),(27,'correo','Correo',NULL,22,'activo','{\"data-addon-text\":\"@\",\"class\":\"form-control\", \"ng-model\":\"contacto.correo\", \"data-rule-email\":\"true\"}',NULL,'div',NULL,1,NULL,'2015-04-21 13:06:15',7),(28,'uuid_cliente','',NULL,7,'activo','{\"data-columns\":\"3\", \"ng-model\":\"contacto.uuidcliente\"}',NULL,'div','',0,NULL,'2015-04-22 09:10:50',11),(33,'cancelar','Cancelar',NULL,32,'activo','{\"class\":\"btn btn-default btn-block\",\"ng-click\":\"cancelarBtn($event)\"}',NULL,'div',NULL,0,NULL,'2015-04-22 09:10:57',17),(46,'telefono','Tel√©fono',NULL,22,'activo','{\"data-addon-icon\":\"fa-phone\", \"class\":\"form-control\", \"ng-model\":\"contacto.telefono\",\"data-inputmask\":\"\'mask\': \'999-9999\', \'greedy\':true\"}',NULL,'div',NULL,0,NULL,'2015-04-17 15:44:19',9),(48,'id_toma_contacto','Toma de Contacto',NULL,12,'inactivo',NULL,NULL,'div',NULL,0,NULL,'2015-04-17 15:50:11',13),(49,'direccion','Direccion',NULL,14,'activo','{\"data-columns\":\"2\", \"ng-model\":\"contacto.direccion\"}',NULL,'div',NULL,0,NULL,'2015-04-17 15:53:57',15),(50,'id_asignado','Asignado a',NULL,18,'inactivo',NULL,NULL,'div','usuarios',0,NULL,'2015-04-17 15:55:39',14),(51,'comentarios','Comentarios',NULL,14,'activo','{\"data-columns\":\"2\", \"ng-model\":\"contacto.comentario\"}',NULL,'div',NULL,0,NULL,'2015-04-17 15:59:56',16),(70,'guardar','Guardar',NULL,33,'activo','{\"class\":\"btn btn-primary btn-block\", \"ng-click\":\"guardarBtn(contacto)\"}',NULL,'div',NULL,0,NULL,'2015-04-22 09:10:58',18),(71,'nombre_comercial][','Nombre Comercial',NULL,16,'inactivo','{\"multiple\":\"multiple\"}',NULL,'div',NULL,0,NULL,'2015-05-26 09:10:58',12);

/*Table structure for table `con_contactos_cat` */

DROP TABLE IF EXISTS `con_contactos_cat`;

CREATE TABLE `con_contactos_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `con_contactos_cat` */

insert  into `con_contactos_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,48,'llamada_frio','Llamada en Frio'),(2,48,'cliente_existente','Cliente Existente'),(3,48,'correo_direccto','Correcto Directo'),(4,48,'conferencia','Conferencia'),(5,48,'sitio_web','Sitio web'),(6,48,'referido','Referido'),(7,48,'email','Email'),(8,48,'walk_in','Walk-In');

/*Table structure for table `configuracion_sistema` */

DROP TABLE IF EXISTS `configuracion_sistema`;

CREATE TABLE `configuracion_sistema` (
  `id_configuracion` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_configuracion` binary(16) NOT NULL,
  `contr_long_minima_contrasena` int(11) DEFAULT NULL,
  `contr_expira_despues_dias` int(11) DEFAULT NULL,
  `contr_configuracion_avanzada` tinyint(1) DEFAULT '0',
  `contr_notificacion_usuarios_expiracion` tinyint(1) DEFAULT '0',
  `contr_notificar_antes_dias` int(11) DEFAULT NULL,
  `contr_minima_cantidad_letras` int(11) DEFAULT NULL,
  `contr_minima_cantidad_numeros` int(11) DEFAULT NULL,
  `contr_minima_cantidad_caracteres` int(11) DEFAULT NULL,
  `contr_restringir_contrasena_vieja` tinyint(1) DEFAULT '0',
  `contr_cambiar_contrasena_login` tinyint(1) DEFAULT '0',
  `contr_cambiar_contrasena` tinyint(1) DEFAULT '0',
  `usu_long_minima_usuario` int(11) DEFAULT NULL,
  `usu_long_maxima_usuario` int(11) DEFAULT NULL,
  `usu_uso_correo` tinyint(1) DEFAULT '0',
  `usu_editar_perfil` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_configuracion`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `configuracion_sistema` */

insert  into `configuracion_sistema`(`id_configuracion`,`uuid_configuracion`,`contr_long_minima_contrasena`,`contr_expira_despues_dias`,`contr_configuracion_avanzada`,`contr_notificacion_usuarios_expiracion`,`contr_notificar_antes_dias`,`contr_minima_cantidad_letras`,`contr_minima_cantidad_numeros`,`contr_minima_cantidad_caracteres`,`contr_restringir_contrasena_vieja`,`contr_cambiar_contrasena_login`,`contr_cambiar_contrasena`,`usu_long_minima_usuario`,`usu_long_maxima_usuario`,`usu_uso_correo`,`usu_editar_perfil`) values (1,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',6,0,0,0,3,2,2,2,0,1,1,5,18,1,1);

/*Table structure for table `contab_contabilidad_campos` */

DROP TABLE IF EXISTS `contab_contabilidad_campos`;

CREATE TABLE `contab_contabilidad_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) DEFAULT NULL,
  `etiqueta` varchar(200) DEFAULT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT NULL,
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(200) DEFAULT NULL,
  `contenedor` varchar(200) DEFAULT '''div''',
  `tabla_relacional` varchar(100) DEFAULT NULL,
  `requerido` tinyint(1) DEFAULT NULL,
  `link_url` varchar(200) DEFAULT NULL,
  `fecha_cracion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `posicion` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `contab_contabilidad_campos` */

insert  into `contab_contabilidad_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'nombre','Centro Contable',NULL,14,'activo','{\"data-columns\":4}',NULL,'\'div\'',NULL,1,NULL,'2015-11-17 07:38:54',1),(2,'descripcion','Descripcion',NULL,14,'activo','{\"data-columns\":4}',NULL,'\'div\'',NULL,NULL,NULL,'2015-11-17 07:39:19',3),(3,'padre_id','Agrupador',NULL,16,'activo','{\"class\":\"chosen-select form-control\", \"data-columns\":4}',NULL,'\'div\'','cen_centros_formulario',NULL,NULL,'2015-11-17 07:39:41',2);

/*Table structure for table `contab_cuentas` */

DROP TABLE IF EXISTS `contab_cuentas`;

CREATE TABLE `contab_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) DEFAULT NULL,
  `nombre` varchar(250) DEFAULT NULL,
  `detalle` text,
  `estado` tinyint(1) DEFAULT '1',
  `balance` decimal(10,0) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `padre_id` int(11) DEFAULT NULL,
  `tipo_cuenta_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `impuesto_id` int(11) DEFAULT NULL,
  `uuid_cuenta` binary(16) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `indice_unico` (`codigo`,`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=latin1;

/*Data for the table `contab_cuentas` */

insert  into `contab_cuentas`(`id`,`codigo`,`nombre`,`detalle`,`estado`,`balance`,`created_at`,`updated_at`,`padre_id`,`tipo_cuenta_id`,`empresa_id`,`impuesto_id`,`uuid_cuenta`) values (1,'1.','Activos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,1,1,NULL,'Âìp⁄Í9µ˛bfäa•'),(2,'1.1.','Activo corriente','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',1,1,1,NULL,'Âìp⁄Ó˛øµ˛bfäa•'),(3,'1.1.1.','Efectivo y equivalentes de efectivo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,1,NULL,'Âìp⁄ÒZ|µ˛bfäa•'),(4,'1.1.1.01.','Caja general','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',3,1,1,NULL,'Âìp⁄ı]2µ˛bfäa•'),(5,'1.1.1.02.','Caja chica','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',3,1,1,NULL,'Âìp⁄ıf2µ˛bfäa•'),(6,'1.1.2.','Bancos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,1,NULL,'Âìp⁄ıkÇµ˛bfäa•'),(7,'1.1.2.01.','Depositos en cuentas corrientes','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',6,1,1,NULL,'Âìp⁄ıpôµ˛bfäa•'),(8,'1.1.2.02.','Depositos en cuentas de ahorro','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',6,1,1,NULL,'Âìp⁄ıu≤µ˛bfäa•'),(9,'1.1.2.03.','Depositos a plazo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',6,1,1,NULL,'Âìp⁄ız•µ˛bfäa•'),(10,'1.1.3.','Cuentas por cobrar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,1,NULL,'Âìp⁄ıŸµ˛bfäa•'),(11,'1.1.3.01.','Cuentas por cobrar de clientes','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',10,1,1,NULL,'Âìp⁄ıÑoµ˛bfäa•'),(12,'1.1.3.02.','Abonos y anticipos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',10,1,1,NULL,'Âìp⁄ıà˘µ˛bfäa•'),(13,'1.1.3.03.','Pr√©stamos al personal','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',10,1,1,NULL,'Âìp⁄ıç≈µ˛bfäa•'),(14,'1.1.3.04.','Pr√©stamos a accionistas','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',10,1,1,NULL,'Âìp⁄ıíeµ˛bfäa•'),(15,'1.1.3.05.','Otras cuentas por cobrar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',10,1,1,NULL,'Âìp⁄ıñıµ˛bfäa•'),(16,'1.1.4.','Inventarios','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,1,NULL,'Âìp⁄ıõâµ˛bfäa•'),(17,'1.1.4.01.','Inventarios en bodega','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',16,1,1,NULL,'Âìp⁄ı†	µ˛bfäa•'),(18,'1.1.4.02.','Pedidos en tr√°nsito','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',16,1,1,NULL,'Âìp⁄ı§üµ˛bfäa•'),(19,'1.1.5.','Inversiones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,1,NULL,'Âìp⁄ı©Yµ˛bfäa•'),(20,'1.1.5.01.','Inversiones y acciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',19,1,1,NULL,'Âìp⁄ı≠ıµ˛bfäa•'),(21,'1.1.6.','Gastos anticipados','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,1,NULL,'Âìp⁄ı≤Çµ˛bfäa•'),(22,'1.1.6.01.','Garant√≠as de arrendamiento','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',21,1,1,NULL,'Âìp⁄ıπIµ˛bfäa•'),(23,'1.2.','Activo no corriente','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',1,1,1,NULL,'Âìp⁄ıΩœµ˛bfäa•'),(24,'1.2.1.','Propiedad, planta y equipo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',23,1,1,NULL,'Âìp⁄ı¬Oµ˛bfäa•'),(25,'1.2.1.01.','Terrenos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',24,1,1,NULL,'Âìp⁄ı∆Ãµ˛bfäa•'),(26,'1.2.1.02.','Instalaciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',24,1,1,NULL,'Âìp⁄ıÀOµ˛bfäa•'),(27,'1.2.1.03.','Mobiliario y equipo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',24,1,1,NULL,'Âìp⁄ıœ“µ˛bfäa•'),(28,'1.2.1.04.','Vehiculos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',24,1,1,NULL,'Âìp⁄ı‘Lµ˛bfäa•'),(29,'1.2.2.','Depreciaci√≥n acumulada','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',23,1,1,NULL,'Âìp⁄ıÿœµ˛bfäa•'),(30,'1.2.2.01.','Depreciaci√≥n acumulada de instalaciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',29,1,1,NULL,'Âìp⁄ıﬁÚµ˛bfäa•'),(31,'1.2.2.02.','Depreciaci√≥n acumulada de mobiliario y equipo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',29,1,1,NULL,'Âìp⁄ı„âµ˛bfäa•'),(32,'1.2.2.03.','Depreciaci√≥n acumulada de veh√≠culos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',29,1,1,NULL,'Âìp⁄ıËµ˛bfäa•'),(33,'1.2.3.','Reevaluaciones de propiedad,planta y equipo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',23,1,1,NULL,'Âìp⁄ıÏ•µ˛bfäa•'),(34,'1.2.3.01.','Reevaluaci√≥n de terrenos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',33,1,1,NULL,'Âìp⁄ıÒIµ˛bfäa•'),(35,'1.2.3.02.','Reevaluaci√≥n de instalaciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',33,1,1,NULL,'Âìp⁄ıı’µ˛bfäa•'),(36,'1.2.4.','Impuestos anticipados','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',23,1,1,NULL,'Âìp⁄˝∑µ˛bfäa•'),(37,'1.2.4.01.','Impuesto sobre la renta anticipado','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',36,1,1,NULL,'Âìp⁄˝√©µ˛bfäa•'),(38,'2.','Pasivo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,2,1,NULL,'Âìp⁄˝«¨µ˛bfäa•'),(39,'2.1.','Pasivo corriente','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',38,2,1,NULL,'Âìp⁄˝Ã¬µ˛bfäa•'),(40,'2.1.1.','Pr√©stamos y sobregiros','',1,'0','2015-11-06 13:55:49','2015-12-04 10:21:55',39,2,1,15,'Âìp⁄˝“µ˛bfäa•'),(42,'2.1.1.02.','Sobregiros bancarios','',1,'0','2015-11-06 13:55:49','2015-12-04 14:13:41',40,2,1,6,'Âìp⁄˝‹ïµ˛bfäa•'),(43,'2.1.2.','Cuentas por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',39,2,1,NULL,'Âìp⁄˝·ïµ˛bfäa•'),(44,'2.1.2.01.','Cuentas por pagar a proveedores','',1,'0','2015-11-06 13:55:49','2015-12-04 14:06:46',43,2,1,13,'Âìp⁄˝Êèµ˛bfäa•'),(45,'2.1.2.02.','Cuentas por pagar a acreedores','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',43,2,1,NULL,'Âìp⁄˝Îåµ˛bfäa•'),(46,'2.1.2.03.','Contratos a corto plazo','',1,'0','2015-11-06 13:55:49','2015-12-04 14:13:41',43,2,1,6,'Âìp⁄˝lµ˛bfäa•'),(47,'2.1.3.','Provisiones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',39,2,1,NULL,'Âìp⁄˝ıEµ˛bfäa•'),(48,'2.1.3.01.','Provisiones locales','',1,'0','2015-11-06 13:55:49','2015-12-04 11:39:57',47,2,1,22,'Âìp⁄˝˙/µ˛bfäa•'),(49,'2.1.3.02.','Intereses por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',47,2,1,NULL,'Âìp⁄˝ˇÇµ˛bfäa•'),(50,'2.1.3.03.','Impuestos municipales por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',47,2,1,NULL,'Âìp⁄˛bµ˛bfäa•'),(51,'2.1.3.04.','Impuestos generales por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',47,2,1,NULL,'Âìp⁄˛	,µ˛bfäa•'),(52,'2.1.3.05.','Impuesto sobre la renta por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',47,2,1,NULL,'Âìp⁄˛/µ˛bfäa•'),(53,'2.1.3.06.','ITBMS por pagar','',1,'0','2015-11-06 13:55:49','2015-12-04 11:27:31',47,2,1,11,'Âìp⁄˛µ˛bfäa•'),(54,'2.1.4.','Retenciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',39,2,1,NULL,'Âìp⁄˛Ôµ˛bfäa•'),(55,'2.1.4.01.','Cuota obrero patronal por pagar','',1,'0','2015-11-06 13:55:49','2015-12-04 11:52:25',54,2,1,22,'Âìp⁄˛¬µ˛bfäa•'),(56,'2.1.4.02.','Prima de antiguedad','',1,'0','2015-11-06 13:55:49','2015-12-04 10:32:52',54,2,1,56,'Âìp⁄˛!¬µ˛bfäa•'),(57,'2.1.5.','Beneficios a empleados por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',39,2,1,NULL,'Âìp⁄˛&¨µ˛bfäa•'),(58,'2.1.5.01.','Salarios por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',57,2,1,NULL,'Âìp⁄˛+Çµ˛bfäa•'),(59,'2.1.5.02.','Beneficios a corto plazo por pagar','',1,'0','2015-11-06 13:55:49','2015-12-04 11:23:46',57,2,1,59,'Âìp⁄˛0yµ˛bfäa•'),(60,'2.1.5.03.','Comisiones por pagar','',1,'0','2015-11-06 13:55:49','2015-12-04 10:38:21',57,2,1,60,'Âìp⁄˛5Uµ˛bfäa•'),(61,'2.1.5.04.','Bonificaciones por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',57,2,1,NULL,'Âìp⁄˛:,µ˛bfäa•'),(62,'2.1.6.','Dividendos por pagar','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',39,2,1,NULL,'Âìp⁄˛?µ˛bfäa•'),(63,'2.1.6.01.','Dividendos por pagar a accionistas','',1,'0','2015-11-06 13:55:49','2015-12-04 11:49:08',62,2,1,18,'Âìp⁄˛D	µ˛bfäa•'),(64,'2.2.','Pasivo no corriente','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',38,2,1,NULL,'Âìp⁄˛H‚µ˛bfäa•'),(65,'2.2.1.','Pr√©stamos bancarios a largo plazo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',64,2,1,NULL,'Âìp⁄˛NUµ˛bfäa•'),(66,'2.2.1.01.','Pr√©stamos hipotecarios','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',65,2,1,NULL,'Âìp⁄˛Sbµ˛bfäa•'),(67,'2.2.1.02.','Otros pr√©stamos a largo plazo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',65,2,1,NULL,'Âìp⁄˛X5µ˛bfäa•'),(68,'2.2.2.','Anticipos y garant√≠as de clientes','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',64,2,1,NULL,'Âìp⁄˛\\ˇµ˛bfäa•'),(69,'2.2.2.01.','Anticipos de clientes','',1,'0','2015-11-06 13:55:49','2015-12-04 15:50:51',68,2,1,12,'Âìp⁄˛a¨µ˛bfäa•'),(70,'2.2.2.02.','Garant√≠as de clientes','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',68,2,1,NULL,'Âìp⁄˛f’µ˛bfäa•'),(71,'3.','Patrimonio','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,3,1,NULL,'Âìp⁄˛j%µ˛bfäa•'),(72,'3.1.','Capital','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',71,3,1,NULL,'Âìp⁄˛nrµ˛bfäa•'),(73,'3.1.1.','Capital social','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',72,3,1,NULL,'Âìp⁄˛sµ˛bfäa•'),(74,'3.1.1.01.','Capital social suscrito','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',73,3,1,NULL,'Âìp⁄˛wüµ˛bfäa•'),(75,'3.1.2.','Superavit','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',72,3,1,NULL,'Âìp⁄˛|?µ˛bfäa•'),(76,'3.1.2.01.','Superavit por reevaluaciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',75,3,1,NULL,'Âìp⁄˛Ä¬µ˛bfäa•'),(77,'3.1.3.','Utilidades retenidas','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',72,3,1,NULL,'Âìp⁄˛ÖOµ˛bfäa•'),(78,'3.1.3.01.','Utilidades retenidas','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',77,3,1,NULL,'Âìp⁄˛â∆µ˛bfäa•'),(79,'4.','Ingresos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,4,1,NULL,'Âìp⁄˛ç9µ˛bfäa•'),(80,'4.1.','Ingresos por operaciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',79,4,1,NULL,'Âìp⁄˛ëµ˛bfäa•'),(81,'4.1.1.','Ventas generales','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',80,4,1,NULL,'Âìp⁄˛ïˇµ˛bfäa•'),(82,'4.1.1.01.','Ventas de servicios generales','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',81,4,1,NULL,'Âìp⁄˛öåµ˛bfäa•'),(83,'4.1.2.','Ventas de bienes','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',80,4,1,NULL,'Âìp⁄˛ü,µ˛bfäa•'),(84,'4.1.2.01.','Ventas internas','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',83,4,1,NULL,'Âìp⁄˛£¨µ˛bfäa•'),(85,'4.1.3.','Otros ingresos no operacionales','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',80,4,1,NULL,'Âìp⁄˛®/µ˛bfäa•'),(86,'4.1.3.01.','Intereses ganados','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',85,4,1,NULL,'Âìp⁄˛¨≤µ˛bfäa•'),(87,'4.1.3.02.','Otros ingresos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',85,4,1,NULL,'Âìp⁄˛±&µ˛bfäa•'),(88,'5.','Gastos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,5,1,NULL,'Âìp⁄˛¥ñµ˛bfäa•'),(89,'5.1.','Costos de operaci√≥n','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',88,5,1,NULL,'Âìp⁄˛∏Èµ˛bfäa•'),(90,'5.1.1.','Costo de venta','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',89,5,1,NULL,'Âìp⁄˛Ωrµ˛bfäa•'),(91,'5.1.1.01.','Costo de venta de productos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',90,5,1,NULL,'Âìp⁄˛√Ïµ˛bfäa•'),(92,'5.1.1.02.','Costo de venta de servicios','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',90,5,1,NULL,'Âìp⁄˛»lµ˛bfäa•'),(93,'5.2.','Gastos de operaci√≥n','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',88,5,1,NULL,'Âìp⁄˛Õµ˛bfäa•'),(94,'5.2.1.','Gastos generales','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,1,NULL,'Âìp⁄˛“Çµ˛bfäa•'),(95,'5.2.1.01.','Energ√≠a el√©ctrica','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄˛÷¸µ˛bfäa•'),(96,'5.2.1.02.','Internet','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄˛€|µ˛bfäa•'),(97,'5.2.1.03.','Telefon√≠a','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄˛‡<µ˛bfäa•'),(98,'5.2.1.04.','Papeler√≠a y utiles de oficina','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄˛‰øµ˛bfäa•'),(99,'5.2.1.05.','Depreciaci√≥n','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄˛Íbµ˛bfäa•'),(100,'5.2.1.06.','Seguros y p√≥lizas','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄˛Ô¶µ˛bfäa•'),(101,'5.2.1.07.','Mensajer√≠a','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄˛ıŸµ˛bfäa•'),(102,'5.2.1.08.','Alquileres de oficinas','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄˛¸2µ˛bfäa•'),(103,'5.2.1.09.','Mantenimiento de oficinas','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄ˇ¨µ˛bfäa•'),(104,'5.2.1.10.','Mantenimiento de vehiculos','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄ˇ\rˇµ˛bfäa•'),(105,'5.2.1.11.','Mantenimiento de mobiliario','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄ˇRµ˛bfäa•'),(106,'5.2.1.12.','Aseo y limpieza','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄ˇ\Z‹µ˛bfäa•'),(107,'5.2.1.13.','Publicidad y mercadeo','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',94,5,1,NULL,'Âìp⁄ˇ%ˇµ˛bfäa•'),(108,'5.2.2.','Gastos de recurso humano','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,1,NULL,'Âìp⁄ˇ.Üµ˛bfäa•'),(109,'5.2.2.01.','Salarios','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',108,5,1,NULL,'Âìp⁄ˇ>Ïµ˛bfäa•'),(110,'5.2.2.02.','Vacaciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',108,5,1,NULL,'Âìp⁄ˇHYµ˛bfäa•'),(111,'5.2.2.03.','Decimo tercer mes','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',108,5,1,NULL,'Âìp⁄ˇT¬µ˛bfäa•'),(112,'5.2.2.04.','Indemnizacion','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',108,5,1,NULL,'Âìp⁄ˇgyµ˛bfäa•'),(113,'5.2.2.05.','Prima de antiguedad','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',108,5,1,NULL,'Âìp⁄ˇyˆµ˛bfäa•'),(114,'5.2.2.06.','Cuota obrero patronal','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',108,5,1,NULL,'Âìp⁄ˇÇôµ˛bfäa•'),(115,'5.2.2.07.','Bonificaciones y gratificaciones','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',108,5,1,NULL,'Âìp⁄ˇç?µ˛bfäa•'),(116,'5.3.','Gastos no operacionales','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',88,5,1,NULL,'Âìp€Mâµ˛bfäa•'),(117,'5.3.1.','Gastos financieros','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',116,5,1,NULL,'Âìp€Srµ˛bfäa•'),(118,'5.3.1.01.','Intereses bancarios','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',117,5,1,NULL,'Âìp€Xπµ˛bfäa•'),(119,'5.3.1.02.','Comisiones bancarias','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',117,5,1,NULL,'Âìp€]“µ˛bfäa•'),(120,'5.3.1.03.','Otros cargos bancarios','',1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',117,5,1,NULL,'Âìp€b…µ˛bfäa•');

/*Table structure for table `contab_cuentas1_copy` */

DROP TABLE IF EXISTS `contab_cuentas1_copy`;

CREATE TABLE `contab_cuentas1_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) DEFAULT NULL,
  `nombre` varchar(250) DEFAULT NULL,
  `detalle` text,
  `estado` tinyint(1) DEFAULT '1',
  `balance` decimal(10,0) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `padre_id` int(11) DEFAULT NULL,
  `tipo_cuenta_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=latin1;

/*Data for the table `contab_cuentas1_copy` */

insert  into `contab_cuentas1_copy`(`id`,`codigo`,`nombre`,`detalle`,`estado`,`balance`,`created_at`,`updated_at`,`padre_id`,`tipo_cuenta_id`,`empresa_id`) values (1,'1','Activos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,1,NULL),(2,'11','Activo corriente\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',1,1,NULL),(3,'111','Efectivo y equivalentes de efectivo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,NULL),(4,'11101','Caja general\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',3,1,NULL),(5,'11102','Caja chica\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',3,1,NULL),(6,'11103','Depositos en cuentas corrientes\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',3,1,NULL),(7,'11104','Depositos en cuentas de ahorro\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',3,1,NULL),(8,'11105','Dep?sitos a plazo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',3,1,NULL),(9,'112','Cuentas por cobrar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,NULL),(10,'11201','Cuentas por cobrar de clientes\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',9,1,NULL),(11,'11202','Abonos y anticipos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',9,1,NULL),(12,'11203','Pr√©stamos al personal\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',9,1,NULL),(13,'11204','Pr√©stamos a accionistas\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',9,1,NULL),(14,'11205','Otras cuentas por cobrar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',9,1,NULL),(15,'113','Inventarios\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,NULL),(16,'11301','Inventarios en bodega\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',15,1,NULL),(17,'11302','Pedidos en tr?nsito\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',15,1,NULL),(18,'114','Inversiones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,NULL),(19,'11401','Inversiones y acciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',18,1,NULL),(20,'115','Gastos anticipados\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',2,1,NULL),(21,'11501','Garant√≠as de arrendamiento\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',21,1,NULL),(22,'12','Activo no corriente\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',1,1,NULL),(23,'121','Propiedad, planta y equipo',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',22,1,NULL),(24,'12101','Terrenos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',23,1,NULL),(25,'12102','Instalaciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',23,1,NULL),(26,'12103','Mobiliario y equipo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',23,1,NULL),(27,'12104','Vehiculos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',23,1,NULL),(28,'122','Depreciaci√≥n acumulada\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',22,1,NULL),(29,'12201','Depreciaci√≥n acumulada de instalaciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',28,1,NULL),(30,'12202','Depreciaci√≥n acumulada de mobiliario y equipo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',28,1,NULL),(31,'12203','Depreciaci√≥n acumulada de veh√≠culos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',28,1,NULL),(32,'123','Reevaluaciones de propiedad,planta y equipo',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',22,1,NULL),(33,'12301','Reevaluaci√≥n de terrenos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',32,1,NULL),(34,'12302','Reevaluaci√≥n de instalaciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',32,1,NULL),(35,'124','Impuestos anticipados\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',22,1,NULL),(36,'12401','Impuesto sobre la renta anticipado\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',35,1,NULL),(37,'2','Pasivo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,2,NULL),(38,'21','Pasivo corriente\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',37,2,NULL),(39,'211','Pr√©stamos y sobregiros\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',38,2,NULL),(40,'21101','Pr√©stamos bancarios a corto plazo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',39,2,NULL),(41,'21102','Sobregiros bancarios\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',39,2,NULL),(42,'212','Cuentas por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',38,2,NULL),(43,'21201','Cuentas por pagar a proveedores\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',42,2,NULL),(44,'21202','Cuentas por pagar a acreedores\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',42,2,NULL),(45,'21203','Contratos a corto plazo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',42,2,NULL),(46,'213','Provisiones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',38,2,NULL),(47,'21301','Provisiones locales\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',46,2,NULL),(48,'21302','Intereses por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',46,2,NULL),(49,'21303','Impuestos municipales por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',46,2,NULL),(50,'21304','Impuestos generales por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',46,2,NULL),(51,'21305','Impuesto sobre la renta por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',46,2,NULL),(52,'21306','ITBMS por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',46,2,NULL),(53,'214','Retenciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',38,2,NULL),(54,'21401','Cuota obrero patronal por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',53,2,NULL),(55,'21402','Prima de antiguedad\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',53,2,NULL),(56,'215','Beneficios a empleados por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',38,2,NULL),(57,'21501','Salarios por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',56,2,NULL),(58,'21502','Beneficios a corto plazo por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',56,2,NULL),(59,'21503','Comisiones por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',56,2,NULL),(60,'21504','Bonificaciones por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',56,2,NULL),(61,'216','Dividendos por pagar\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',38,2,NULL),(62,'21601','Dividendos por pagar a accionistas\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',61,2,NULL),(63,'22','Pasivo no corriente\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',37,2,NULL),(64,'221','Pr√©stamos bancarios a largo plazo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',63,2,NULL),(65,'22101','Pr√©stamos hipotecarios\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',64,2,NULL),(66,'22102','Otros pr√©stamos a largo plazo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',64,2,NULL),(67,'222','Anticipos y garant√≠as de clientes\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',63,2,NULL),(68,'22201','Anticipos de clientes\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',67,2,NULL),(69,'22202','Garant√≠as de clientes\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',67,2,NULL),(70,'3','Patrimonio\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,3,NULL),(71,'31','Capital\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',70,3,NULL),(72,'311','Capital social\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',71,3,NULL),(73,'31101','Capital social suscrito\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',72,3,NULL),(74,'312','Superavit\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',71,3,NULL),(75,'31201','Superavit por reevaluaciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',74,3,NULL),(76,'313','Utilidades retenidas\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',71,3,NULL),(77,'31301','Utilidades retenidas\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',77,3,NULL),(78,'4','Ingresos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,4,NULL),(79,'41','Ingresos por operaciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',78,4,NULL),(80,'411','Ventas generales\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',79,4,NULL),(81,'41101','Ventas de servicios generales\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',80,4,NULL),(82,'412','Ventas de bienes\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',79,4,NULL),(83,'41201','Ventas internas\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',82,4,NULL),(84,'413','Otros ingresos no operacionales\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',79,4,NULL),(85,'41301','Intereses ganados\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',84,4,NULL),(86,'41302','Otros ingresos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',84,4,NULL),(87,'5','Gastos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',0,5,NULL),(88,'51','Costos de operaci√≥n\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',87,5,NULL),(89,'511','Costo de venta\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',88,5,NULL),(90,'51101','Costo de venta de productos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',89,5,NULL),(91,'51102','Costo de venta de servicios\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',89,5,NULL),(92,'52','Gastos de operaci√≥n\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',87,5,NULL),(93,'521','Gastos generales\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',92,5,NULL),(94,'52101','Energ√≠a el√©ctrica\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(95,'52102','Internet\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(96,'52103','Telefon√≠a\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(97,'52104','Papeler√≠a y ?tiles de oficina\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(98,'52105','Depreciaci√≥n\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(99,'52106','Seguros y p√≥lizas\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(100,'52107','Mensajer√≠a\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(101,'52108','Alquileres de oficinas\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(102,'52109','Mantenimiento de oficinas\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(103,'52110','Mantenimiento de veh?culos\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(104,'52111','Mantenimiento de mobiliario\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(105,'52112','Aseo y limpieza\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(106,'52113','Publicidad y mercadeo\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',93,5,NULL),(107,'522','Gastos de recurso humano\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',92,5,NULL),(108,'52201','Salarios\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',107,5,NULL),(109,'52202','Vacaciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',107,5,NULL),(110,'52203','D?cimo tercer mes\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',107,5,NULL),(111,'52204','Indemnizaci?n\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',107,5,NULL),(112,'52205','Prima de antig?edad\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',107,5,NULL),(113,'52206','Cuota obrero patronal\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',107,5,NULL),(114,'52207','Bonificaciones y gratificaciones\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',107,5,NULL),(115,'53','Gastos no operacionales\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',87,5,NULL),(116,'531','Gastos financieros\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',115,5,NULL),(117,'53101','Intereses bancarios\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',116,5,NULL),(118,'53102','Comisiones bancarias\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',116,5,NULL),(119,'53103','Otros cargos bancarios\r',NULL,1,'0','2015-11-06 13:55:49','2015-11-06 13:55:49',116,5,NULL);

/*Table structure for table `contab_cuentas_centros` */

DROP TABLE IF EXISTS `contab_cuentas_centros`;

CREATE TABLE `contab_cuentas_centros` (
  `cuenta_id` int(11) NOT NULL DEFAULT '0',
  `centro_id` int(11) NOT NULL DEFAULT '0',
  `empresa_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cuenta_id`,`centro_id`,`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `contab_cuentas_centros` */

insert  into `contab_cuentas_centros`(`cuenta_id`,`centro_id`,`empresa_id`) values (1,22,1),(1,23,1),(1,24,1),(1,25,1),(1,26,1),(1,27,1),(1,28,1),(2,22,1),(2,23,1),(2,24,1),(2,25,1),(2,26,1),(2,27,1),(2,28,1),(3,22,1),(3,23,1),(3,24,1),(3,25,1),(3,26,1),(3,27,1),(3,28,1),(4,22,1),(4,23,1),(4,24,1),(4,25,1),(4,26,1),(4,27,1),(4,28,1),(5,22,1),(5,23,1),(5,24,1),(5,25,1),(5,26,1),(5,27,1),(5,28,1),(6,22,1),(6,23,1),(6,24,1),(6,25,1),(6,26,1),(6,27,1),(6,28,1),(7,22,1),(7,23,1),(7,24,1),(7,25,1),(7,26,1),(7,27,1),(7,28,1),(8,22,1),(8,23,1),(8,24,1),(8,25,1),(8,26,1),(8,27,1),(8,28,1),(9,22,1),(9,23,1),(9,24,1),(9,25,1),(9,26,1),(9,27,1),(9,28,1),(10,22,1),(10,23,1),(10,24,1),(10,25,1),(10,26,1),(10,27,1),(10,28,1),(11,22,1),(11,23,1),(11,24,1),(11,25,1),(11,26,1),(11,27,1),(11,28,1),(12,22,1),(12,23,1),(12,24,1),(12,25,1),(12,26,1),(12,27,1),(12,28,1),(13,22,1),(13,23,1),(13,24,1),(13,25,1),(13,26,1),(13,27,1),(13,28,1),(14,22,1),(14,23,1),(14,24,1),(14,25,1),(14,26,1),(14,27,1),(14,28,1),(15,22,1),(15,23,1),(15,24,1),(15,25,1),(15,26,1),(15,27,1),(15,28,1),(16,22,1),(16,23,1),(16,24,1),(16,25,1),(16,26,1),(16,27,1),(16,28,1),(17,22,1),(17,23,1),(17,24,1),(17,25,1),(17,26,1),(17,27,1),(17,28,1),(18,22,1),(18,23,1),(18,24,1),(18,25,1),(18,26,1),(18,27,1),(18,28,1),(19,22,1),(19,23,1),(19,24,1),(19,25,1),(19,26,1),(19,27,1),(19,28,1),(20,22,1),(20,23,1),(20,24,1),(20,25,1),(20,26,1),(20,27,1),(20,28,1),(21,22,1),(21,23,1),(21,24,1),(21,25,1),(21,26,1),(21,27,1),(21,28,1),(22,22,1),(22,23,1),(22,24,1),(22,25,1),(22,26,1),(22,27,1),(22,28,1),(23,22,1),(23,23,1),(23,24,1),(23,25,1),(23,26,1),(23,27,1),(23,28,1),(24,22,1),(24,23,1),(24,24,1),(24,25,1),(24,26,1),(24,27,1),(24,28,1),(25,22,1),(25,23,1),(25,24,1),(25,25,1),(25,26,1),(25,27,1),(25,28,1),(26,22,1),(26,23,1),(26,24,1),(26,25,1),(26,26,1),(26,27,1),(26,28,1),(27,22,1),(27,23,1),(27,24,1),(27,25,1),(27,26,1),(27,27,1),(27,28,1),(28,22,1),(28,23,1),(28,24,1),(28,25,1),(28,26,1),(28,27,1),(28,28,1),(29,22,1),(29,23,1),(29,24,1),(29,25,1),(29,26,1),(29,27,1),(29,28,1),(30,22,1),(30,23,1),(30,24,1),(30,25,1),(30,26,1),(30,27,1),(30,28,1),(31,22,1),(31,23,1),(31,24,1),(31,25,1),(31,26,1),(31,27,1),(31,28,1),(32,22,1),(32,23,1),(32,24,1),(32,25,1),(32,26,1),(32,27,1),(32,28,1),(33,22,1),(33,23,1),(33,24,1),(33,25,1),(33,26,1),(33,27,1),(33,28,1),(34,22,1),(34,23,1),(34,24,1),(34,25,1),(34,26,1),(34,27,1),(34,28,1),(35,22,1),(35,23,1),(35,24,1),(35,25,1),(35,26,1),(35,27,1),(35,28,1),(36,22,1),(36,23,1),(36,24,1),(36,25,1),(36,26,1),(36,27,1),(36,28,1),(37,22,1),(37,23,1),(37,24,1),(37,25,1),(37,26,1),(37,27,1),(37,28,1),(38,22,1),(38,23,1),(38,24,1),(38,25,1),(38,26,1),(38,27,1),(38,28,1),(39,22,1),(39,23,1),(39,24,1),(39,25,1),(39,26,1),(39,27,1),(39,28,1),(40,22,1),(40,23,1),(40,24,1),(40,25,1),(40,26,1),(40,27,1),(40,28,1),(41,22,1),(42,22,1),(42,23,1),(42,24,1),(42,25,1),(42,26,1),(42,27,1),(42,28,1),(43,22,1),(43,23,1),(43,24,1),(43,25,1),(43,26,1),(43,27,1),(43,28,1),(44,22,1),(44,23,1),(44,24,1),(44,25,1),(44,26,1),(44,27,1),(44,28,1),(45,22,1),(45,23,1),(45,24,1),(45,25,1),(45,26,1),(45,27,1),(45,28,1),(46,22,1),(46,23,1),(46,24,1),(46,25,1),(46,26,1),(46,27,1),(46,28,1),(47,22,1),(47,23,1),(47,24,1),(47,25,1),(47,26,1),(47,27,1),(47,28,1),(48,22,1),(48,23,1),(48,24,1),(48,25,1),(48,26,1),(48,27,1),(48,28,1),(49,22,1),(49,23,1),(49,24,1),(49,25,1),(49,26,1),(49,27,1),(49,28,1),(50,22,1),(50,23,1),(50,24,1),(50,25,1),(50,26,1),(50,27,1),(50,28,1),(51,22,1),(51,23,1),(51,24,1),(51,25,1),(51,26,1),(51,27,1),(51,28,1),(52,22,1),(52,23,1),(52,24,1),(52,25,1),(52,26,1),(52,27,1),(52,28,1),(53,22,1),(53,23,1),(53,24,1),(53,25,1),(53,26,1),(53,27,1),(53,28,1),(54,22,1),(54,23,1),(54,24,1),(54,25,1),(54,26,1),(54,27,1),(54,28,1),(55,22,1),(55,23,1),(55,24,1),(55,25,1),(55,26,1),(55,27,1),(55,28,1),(56,22,1),(56,23,1),(56,24,1),(56,25,1),(56,26,1),(56,27,1),(56,28,1),(57,22,1),(57,23,1),(57,24,1),(57,25,1),(57,26,1),(57,27,1),(57,28,1),(58,22,1),(58,23,1),(58,24,1),(58,25,1),(58,26,1),(58,27,1),(58,28,1),(59,22,1),(59,23,1),(59,24,1),(59,25,1),(59,26,1),(59,27,1),(59,28,1),(60,22,1),(60,23,1),(60,24,1),(60,25,1),(60,26,1),(60,27,1),(60,28,1),(61,22,1),(61,23,1),(61,24,1),(61,25,1),(61,26,1),(61,27,1),(61,28,1),(62,22,1),(62,23,1),(62,24,1),(62,25,1),(62,26,1),(62,27,1),(62,28,1),(63,22,1),(63,23,1),(63,24,1),(63,25,1),(63,26,1),(63,27,1),(63,28,1),(64,22,1),(64,23,1),(64,24,1),(64,25,1),(64,26,1),(64,27,1),(64,28,1),(65,22,1),(65,23,1),(65,24,1),(65,25,1),(65,26,1),(65,27,1),(65,28,1),(66,22,1),(66,23,1),(66,24,1),(66,25,1),(66,26,1),(66,27,1),(66,28,1),(67,22,1),(67,23,1),(67,24,1),(67,25,1),(67,26,1),(67,27,1),(67,28,1),(68,22,1),(68,23,1),(68,24,1),(68,25,1),(68,26,1),(68,27,1),(68,28,1),(69,22,1),(69,23,1),(69,24,1),(69,25,1),(69,26,1),(69,27,1),(69,28,1),(70,22,1),(70,23,1),(70,24,1),(70,25,1),(70,26,1),(70,27,1),(70,28,1),(71,22,1),(71,23,1),(71,24,1),(71,25,1),(71,26,1),(71,27,1),(71,28,1),(72,22,1),(72,23,1),(72,24,1),(72,25,1),(72,26,1),(72,27,1),(72,28,1),(73,22,1),(73,23,1),(73,24,1),(73,25,1),(73,26,1),(73,27,1),(73,28,1),(74,22,1),(74,23,1),(74,24,1),(74,25,1),(74,26,1),(74,27,1),(74,28,1),(75,22,1),(75,23,1),(75,24,1),(75,25,1),(75,26,1),(75,27,1),(75,28,1),(76,22,1),(76,23,1),(76,24,1),(76,25,1),(76,26,1),(76,27,1),(76,28,1),(77,22,1),(77,23,1),(77,24,1),(77,25,1),(77,26,1),(77,27,1),(77,28,1),(78,22,1),(78,23,1),(78,24,1),(78,25,1),(78,26,1),(78,27,1),(78,28,1),(79,22,1),(79,23,1),(79,24,1),(79,25,1),(79,26,1),(79,27,1),(79,28,1),(80,22,1),(80,23,1),(80,24,1),(80,25,1),(80,26,1),(80,27,1),(80,28,1),(81,22,1),(81,23,1),(81,24,1),(81,25,1),(81,26,1),(81,27,1),(81,28,1),(82,22,1),(82,23,1),(82,24,1),(82,25,1),(82,26,1),(82,27,1),(82,28,1),(83,22,1),(83,23,1),(83,24,1),(83,25,1),(83,26,1),(83,27,1),(83,28,1),(84,22,1),(84,23,1),(84,24,1),(84,25,1),(84,26,1),(84,27,1),(84,28,1),(85,22,1),(85,23,1),(85,24,1),(85,25,1),(85,26,1),(85,27,1),(85,28,1),(86,22,1),(86,23,1),(86,24,1),(86,25,1),(86,26,1),(86,27,1),(86,28,1),(87,22,1),(87,23,1),(87,24,1),(87,25,1),(87,26,1),(87,27,1),(87,28,1),(88,22,1),(88,23,1),(88,24,1),(88,25,1),(88,26,1),(88,27,1),(88,28,1),(89,22,1),(89,23,1),(89,24,1),(89,25,1),(89,26,1),(89,27,1),(89,28,1),(90,22,1),(90,23,1),(90,24,1),(90,25,1),(90,26,1),(90,27,1),(90,28,1),(91,22,1),(91,23,1),(91,24,1),(91,25,1),(91,26,1),(91,27,1),(91,28,1),(92,22,1),(92,23,1),(92,24,1),(92,25,1),(92,26,1),(92,27,1),(92,28,1),(93,22,1),(93,23,1),(93,24,1),(93,25,1),(93,26,1),(93,27,1),(93,28,1),(94,22,1),(94,23,1),(94,24,1),(94,25,1),(94,26,1),(94,27,1),(94,28,1),(95,22,1),(95,23,1),(95,24,1),(95,25,1),(95,26,1),(95,27,1),(95,28,1),(96,22,1),(96,23,1),(96,24,1),(96,25,1),(96,26,1),(96,27,1),(96,28,1),(97,22,1),(97,23,1),(97,24,1),(97,25,1),(97,26,1),(97,27,1),(97,28,1),(98,22,1),(98,23,1),(98,24,1),(98,25,1),(98,26,1),(98,27,1),(98,28,1),(99,22,1),(99,23,1),(99,24,1),(99,25,1),(99,26,1),(99,27,1),(99,28,1),(100,22,1),(100,23,1),(100,24,1),(100,25,1),(100,26,1),(100,27,1),(100,28,1),(101,22,1),(101,23,1),(101,24,1),(101,25,1),(101,26,1),(101,27,1),(101,28,1),(102,22,1),(102,23,1),(102,24,1),(102,25,1),(102,26,1),(102,27,1),(102,28,1),(103,22,1),(103,23,1),(103,24,1),(103,25,1),(103,26,1),(103,27,1),(103,28,1),(104,22,1),(104,23,1),(104,24,1),(104,25,1),(104,26,1),(104,27,1),(104,28,1),(105,22,1),(105,23,1),(105,24,1),(105,25,1),(105,26,1),(105,27,1),(105,28,1),(106,22,1),(106,23,1),(106,24,1),(106,25,1),(106,26,1),(106,27,1),(106,28,1),(107,22,1),(107,23,1),(107,24,1),(107,25,1),(107,26,1),(107,27,1),(107,28,1),(108,22,1),(108,23,1),(108,24,1),(108,25,1),(108,26,1),(108,27,1),(108,28,1),(109,22,1),(109,23,1),(109,24,1),(109,25,1),(109,26,1),(109,27,1),(109,28,1),(110,22,1),(110,23,1),(110,24,1),(110,25,1),(110,26,1),(110,27,1),(110,28,1),(111,22,1),(111,23,1),(111,24,1),(111,25,1),(111,26,1),(111,27,1),(111,28,1),(112,22,1),(112,23,1),(112,24,1),(112,25,1),(112,26,1),(112,27,1),(112,28,1),(113,22,1),(113,23,1),(113,24,1),(113,25,1),(113,26,1),(113,27,1),(113,28,1),(114,22,1),(114,23,1),(114,24,1),(114,25,1),(114,26,1),(114,27,1),(114,28,1),(115,22,1),(115,23,1),(115,24,1),(115,25,1),(115,26,1),(115,27,1),(115,28,1),(116,22,1),(116,23,1),(116,24,1),(116,25,1),(116,26,1),(116,27,1),(116,28,1),(117,22,1),(117,23,1),(117,24,1),(117,25,1),(117,26,1),(117,27,1),(117,28,1),(118,22,1),(118,23,1),(118,24,1),(118,25,1),(118,26,1),(118,27,1),(118,28,1),(119,22,1),(119,23,1),(119,24,1),(119,25,1),(119,26,1),(119,27,1),(119,28,1),(120,22,1),(120,23,1),(120,24,1),(120,25,1),(120,26,1),(120,27,1),(120,28,1);

/*Table structure for table `contab_cuentas_old` */

DROP TABLE IF EXISTS `contab_cuentas_old`;

CREATE TABLE `contab_cuentas_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(50) DEFAULT NULL,
  `nombre` varchar(250) DEFAULT NULL,
  `detalle` text,
  `estado` tinyint(1) DEFAULT '1',
  `balance` decimal(10,0) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `padre_id` int(11) DEFAULT NULL,
  `tipo_cuenta_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Data for the table `contab_cuentas_old` */

insert  into `contab_cuentas_old`(`id`,`codigo`,`nombre`,`detalle`,`estado`,`balance`,`created_at`,`updated_at`,`padre_id`,`tipo_cuenta_id`,`empresa_id`) values (1,'1.1','Activo Corriente','0',1,'1','2015-11-02 08:43:49','2015-11-02 08:43:49',0,1,1),(2,'1.1.1','Efectivo Y equivalente de efectivo','0',1,'1','2015-11-02 08:43:49','2015-11-02 08:43:49',1,1,1),(3,'1.1.1.01','Caja general','0',1,'1','2015-11-02 08:43:49','2015-11-02 08:43:49',2,1,1),(4,'1.1.1.02','Caja chica','0',1,'1','2015-11-02 08:43:49','2015-11-02 08:43:49',2,1,1),(5,'1.1.1.03','Depositos en cuentas corrientes','0',1,'1','2015-11-02 08:43:49','2015-11-02 08:43:49',2,1,1),(6,'1.1.1.04','Depositos en cuentas de ahorro','0',1,'1','2015-11-02 08:43:49','2015-11-02 08:43:49',2,1,1),(7,'1.1.1.05','Dep√≥sitos a plazo','0',1,'1','2015-11-02 08:43:49','2015-11-02 08:43:49',2,1,1),(8,'1.1.2','Cuentas por cobrar','0',1,'1','2015-11-02 08:43:49','2015-11-02 08:43:49',1,1,1),(9,'1.1.2.01','Cuentas por cobrar de clientes','0',1,'0','2015-11-02 08:43:49','2015-11-02 08:43:49',8,1,1);

/*Table structure for table `contab_entrada_manual` */

DROP TABLE IF EXISTS `contab_entrada_manual`;

CREATE TABLE `contab_entrada_manual` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_entrada` binary(16) DEFAULT NULL,
  `codigo` varchar(200) DEFAULT NULL,
  `nombre` text,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `fecha_inicial_tarea` datetime DEFAULT NULL,
  `fecha_final_tarea` datetime DEFAULT NULL,
  `tarea` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

/*Data for the table `contab_entrada_manual` */

insert  into `contab_entrada_manual`(`id`,`uuid_entrada`,`codigo`,`nombre`,`created_at`,`updated_at`,`empresa_id`,`fecha_inicial_tarea`,`fecha_final_tarea`,`tarea`) values (1,'Âì±ÔøΩÔøΩ(ÔøΩÔø','EM000001','Pago Membresia','2015-11-25 15:20:00','2025-11-25 15:20:00',1,NULL,NULL,NULL),(4,'Âó•X;ïµ˛bfäa•','EM000002','ventas articulos','2015-11-30 15:58:57','2015-11-30 15:58:57',1,NULL,NULL,NULL),(5,'Âó¶≈wL]µ˛bfäa•','EM000003','utilidades relacionales','2015-11-30 16:10:49','2015-11-30 16:10:49',1,NULL,NULL,NULL),(6,'Âóß´	µ˛bfäa•','EM000004','decimo','2015-11-30 16:12:35','2015-11-30 16:12:35',1,NULL,NULL,NULL),(7,'Âò**Kÿ;µ˛bfäa•','EM000005','utiles de oficina','2015-12-01 07:51:22','2015-12-01 07:51:22',1,NULL,NULL,NULL),(8,'Âò*öI\"ßµ˛bfäa•','EM000006','pago luz electrica','2015-12-01 07:54:30','2015-12-01 07:54:30',1,NULL,NULL,NULL),(9,'Âò+%WãÎµ˛bfäa•','EM000007','ventas de articulos','2015-12-01 07:58:23','2015-12-01 07:58:23',1,NULL,NULL,NULL),(13,'ÂòHÙ«£∆µ˛bfäa•','EM000009','fdsfsdfsd','2015-12-01 11:31:47','2015-12-01 11:31:47',1,NULL,NULL,NULL),(14,'Âò]’\0]µ˛bfäa•','EM000009','pagos varios','2015-12-01 14:01:13','2015-12-01 14:01:13',1,NULL,NULL,NULL),(15,'Âò^235xµ˛bfäa•','EM000010','pagos varios 2','2015-12-01 14:03:49','2015-12-01 14:03:49',1,NULL,NULL,NULL),(16,'Âò^ÿ◊wµ˛bfäa•','EM000011','pago 3','2015-12-01 14:08:29','2015-12-01 14:08:29',1,NULL,NULL,NULL),(17,'Âòi∆†ª†µ˛bfäa•','EM000012','pagos varios datos','2015-12-01 15:26:43','2015-12-01 15:26:43',1,NULL,NULL,NULL),(18,'Âß‡∫å«~ëûbfäa•','EM000013','pruebas para presupuesto','2015-12-21 07:45:49','2015-12-21 07:45:49',1,NULL,NULL,NULL),(19,'ÂßÈÇıhhëûbfäa•','EM000014','nuevo test','2015-12-21 08:48:41','2015-12-21 08:48:41',1,NULL,NULL,NULL);

/*Table structure for table `contab_entrada_manual_campos` */

DROP TABLE IF EXISTS `contab_entrada_manual_campos`;

CREATE TABLE `contab_entrada_manual_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) DEFAULT NULL,
  `etiqueta` varchar(200) DEFAULT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` text,
  `agrupador_campo` varchar(200) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(100) DEFAULT NULL,
  `requerido` tinyint(1) DEFAULT NULL,
  `link_url` varchar(200) DEFAULT NULL,
  `fecha_cracion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `posicion` tinyint(2) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Data for the table `contab_entrada_manual_campos` */

insert  into `contab_entrada_manual_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'nombre','Narraci√≥n',NULL,14,'activo','{\"data-columns\":\"2\"}',NULL,'div',NULL,1,NULL,'2015-11-26 10:05:31',4),(2,'fecha_inicial_tarea','Fecha inicial de entrada',NULL,22,'activo','{\"readonly\":\"readonly\", \"data-addon-icon\":\"fa-calendar\",\"class\":\"form-control fecha-tarea\"}',NULL,'div',NULL,NULL,NULL,'2015-11-26 10:06:51',1),(3,'fecha_final_tarea','Fecha final de entrada (opcional)',NULL,22,'activo','{\"readonly\":\"readonly\", \"data-addon-icon\":\"fa-calendar\",\"class\":\"form-control fecha-tarea\"}',NULL,'div',NULL,NULL,NULL,'2015-11-26 10:11:41',3),(4,'separador','Transacciones',NULL,27,'activo',NULL,'','div',NULL,NULL,NULL,'2015-11-26 10:16:21',6),(5,'nombre','Descripci√≥n',NULL,14,'activo','{\"data-columns\":\"2\"}','transacciones','tabla-dinamica-sumativa',NULL,1,NULL,'2015-11-26 10:16:50',7),(6,'cuenta_id','Cuenta',NULL,18,'activo','{\"class\":\"chosen-select form-control\", \"data-rule-required\":\"true\"}','transacciones','tabla-dinamica-sumativa','entrada_manual_cuenta',1,NULL,'2015-11-26 10:17:08',8),(7,'centro_id','Centro Contable',NULL,18,'activo','{\"class\":\"chosen-select form-control\", \"data-table-footer-sum-column\":false, \"data-table-footer-text\":\"Total\", \"data-rule-required\":\"true\"}','transacciones','tabla-dinamica-sumativa','entrada_manual_centro',1,NULL,'2015-11-26 10:17:36',9),(8,'debito','D√©bito',NULL,22,'activo','{\"data-addon-text\":\"$\",\"class\":\"form-control debito\", \"data-table-footer-sum-column\":true,\"data-inputmask\":\"\'mask\': \'9{1,15}.99\', \'greedy\':true\"}','transacciones','tabla-dinamica-sumativa',NULL,NULL,NULL,'2015-11-26 10:17:51',10),(9,'credito','Cr√©dito',NULL,22,'activo','{\"data-addon-text\":\"$\",\"class\":\"form-control credito\", \"data-table-footer-sum-column\":true,\"data-inputmask\":\"\'mask\': \'9{1,15}.99\', \'greedy\':true\"}','transacciones','tabla-dinamica-sumativa',NULL,NULL,NULL,'2015-11-26 10:18:12',11),(10,'incluir','Incluir narraci√≥n a la descripci√≥n de la entrada manual',NULL,2,'activo','{\"data-columns\":\"4\",\"class\":\"chekbox-incluir\"}',NULL,'div',NULL,NULL,NULL,'2015-11-26 10:18:40',5),(11,'tarea','Repetir esta entrada cada',NULL,29,'activo',NULL,NULL,'div',NULL,NULL,NULL,'2015-11-26 10:26:44',2),(13,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Eliminar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block eliminarTransaccionesBtn disabled eliminar\"}','transacciones','tabla-dinamica-sumativa',NULL,NULL,NULL,'2015-11-26 11:48:43',12),(14,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;Agregar&lt;/span&gt;',NULL,1,'activo','{\"class\":\"btn btn-default btn-block agregarTransaccionesBtn\"}','transacciones','tabla-dinamica-sumativa',NULL,NULL,NULL,'2015-11-26 12:25:31',13),(15,'cancelarFormBoton','Cancelar',NULL,1,'activo','{\"class\":\"btn btn-block btn-default cancelarEntradaManual pull-right\"}',NULL,'div',NULL,NULL,'','2015-11-27 10:40:37',15),(16,'guardarFormBoton','Guardar',NULL,1,'activo','{\"class\":\"btn btn-block btn-primary guardarEntradaManual\"}',NULL,'div',NULL,NULL,NULL,'2015-11-27 10:41:08',16),(17,'id',NULL,NULL,7,'activo','{\"class\":\"entrada_id from-control\",\"data-columns\":\"4\"}',NULL,'div',NULL,NULL,NULL,'2015-12-01 15:16:50',6),(18,'id',NULL,NULL,7,'activo',NULL,'transacciones','tabla-dinamica-sumativa',NULL,NULL,NULL,'2015-12-01 15:17:58',14);

/*Table structure for table `contab_entrada_manual_cat` */

DROP TABLE IF EXISTS `contab_entrada_manual_cat`;

CREATE TABLE `contab_entrada_manual_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `contab_entrada_manual_cat` */

insert  into `contab_entrada_manual_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,2,'0','nula'),(2,11,'semana','Semana(s)'),(3,11,'mes','Mes(es)');

/*Table structure for table `contab_impuestos` */

DROP TABLE IF EXISTS `contab_impuestos`;

CREATE TABLE `contab_impuestos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_impuesto` binary(16) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` varchar(200) DEFAULT NULL,
  `impuesto` decimal(4,2) NOT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `empresa_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `contab_impuestos` */

insert  into `contab_impuestos`(`id`,`uuid_impuesto`,`nombre`,`descripcion`,`impuesto`,`estado`,`empresa_id`,`created_at`,`updated_at`) values (1,'ÂíÙS\"xˇï„ºvNT†','Seguros','Colectivo','5.00','Activo',1,'2015-11-24 16:42:56','2015-12-07 08:55:07'),(2,'Âì{1ãnÄï„ºvNT†','ITBMS','test 25 nov','7.00','Activo',1,'2015-11-25 08:48:22','2015-11-25 08:48:22'),(3,'Âì{Cò¸‡ï„ºvNT†','Pruebas RBP','25 nov','5.50','Inactivo',1,'2015-11-25 08:48:52','2015-11-25 08:49:17'),(4,'ÂúÈÑã¬÷ï„ºvNT†','Exento','Exento de Impuesto (No Aplica)','0.00','Activo',1,'2015-12-07 08:50:46','2015-12-07 08:50:46');

/*Table structure for table `contab_tipo_cuentas` */

DROP TABLE IF EXISTS `contab_tipo_cuentas`;

CREATE TABLE `contab_tipo_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) DEFAULT NULL,
  `uuid_contabilidad` binary(16) DEFAULT NULL,
  `nombre` varchar(250) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `contab_tipo_cuentas` */

insert  into `contab_tipo_cuentas`(`id`,`codigo`,`uuid_contabilidad`,`nombre`,`created_at`,`updated_at`) values (1,'1','ÔøΩxÔøΩÔøΩ+ÔøΩÔ','Activo','2015-10-22 14:42:47','2015-10-22 14:42:47'),(2,'2','ÔøΩxÔøΩÔøΩÔø','Pasivo','2015-10-22 14:42:47','2015-10-22 14:42:47'),(3,'3','ÔøΩxÔøΩÔøΩYÔøΩ.','Patrimonio','2015-10-22 14:42:47','2015-10-22 14:42:47'),(4,'4','ÔøΩxÔøΩÊÆÆÔøΩÔø','Ingreso','2015-10-22 14:42:47','2015-10-22 14:42:47'),(5,'5','ÔøΩxÔøΩkÔøΩÔøΩ','Gastos','2015-10-22 14:42:47','2015-10-22 14:42:47');

/*Table structure for table `contab_transacciones` */

DROP TABLE IF EXISTS `contab_transacciones`;

CREATE TABLE `contab_transacciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(200) DEFAULT NULL,
  `uuid_transaccion` binary(16) NOT NULL,
  `nombre` text,
  `debito` decimal(10,2) DEFAULT '0.00',
  `credito` decimal(10,2) DEFAULT '0.00',
  `empresa_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `cuenta_id` int(11) DEFAULT NULL,
  `centro_id` int(11) DEFAULT NULL,
  `entrada_manual_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8;

/*Data for the table `contab_transacciones` */

insert  into `contab_transacciones`(`id`,`codigo`,`uuid_transaccion`,`nombre`,`debito`,`credito`,`empresa_id`,`created_at`,`updated_at`,`cuenta_id`,`centro_id`,`entrada_manual_id`) values (1,'PG000001','Âìµ}ÔøΩÔøΩÔøΩÔø','Pago Membresia','500.00','0.00',1,'2015-11-25 15:40:00','2015-11-25 15:40:00',106,13,1),(2,'PG000002','Âì∂ÔøΩÔøΩ|ÔøΩÔø','pago vehiculo','0.00','250.00',1,'2015-11-25 15:40:00','2015-11-25 15:40:00',28,13,1),(3,'PG000003','Âì∂ÔøΩ\"ÔøΩÔøΩÔ','algo pagado','0.00','250.00',1,'2015-11-25 15:40:00','2015-11-25 15:40:00',15,13,1),(8,NULL,'Âó•[[Ëµ˛bfäa•','ventas articulos','100.00','0.00',1,'2015-11-30 15:58:57','2015-11-30 15:58:57',4,3,4),(9,NULL,'Âó•[Ö(µ˛bfäa•','ventas articulos','0.00','100.00',1,'2015-11-30 15:58:57','2015-11-30 15:58:57',103,3,4),(10,NULL,'Âó¶≈y‡˜µ˛bfäa•','utilidades relacionales','300.00','0.00',1,'2015-11-30 16:10:49','2015-11-30 16:10:49',5,13,5),(11,NULL,'Âó¶≈{À0µ˛bfäa•','utilidades relacionales','0.00','300.00',1,'2015-11-30 16:10:49','2015-11-30 16:10:49',114,13,5),(12,NULL,'Âóßm6µ˛bfäa•','decimo','20.00','0.00',1,'2015-11-30 16:12:35','2015-11-30 16:12:35',7,13,6),(13,NULL,'ÂóßÉSµ˛bfäa•','decimo','0.00','20.00',1,'2015-11-30 16:12:35','2015-11-30 16:12:35',111,13,6),(14,NULL,'Âò**U±±µ˛bfäa•','utiles de oficina','100.00','0.00',1,'2015-12-01 07:51:22','2015-12-01 07:51:22',4,13,7),(15,NULL,'Âò**WÌHµ˛bfäa•','utiles de oficina','0.00','100.00',1,'2015-12-01 07:51:22','2015-12-01 07:51:22',106,13,7),(16,NULL,'Âò*öJÚ4µ˛bfäa•','pago luz electrica','100.00','0.00',1,'2015-12-01 07:54:30','2015-12-01 07:54:30',5,13,8),(17,NULL,'Âò*öK˝µ˛bfäa•','pago luz electrica','0.00','100.00',1,'2015-12-01 07:54:30','2015-12-01 07:54:30',95,13,8),(18,'TR000006','Âò+%]Åﬂµ˛bfäa•','ventas de articulos','100.00','0.00',1,'2015-12-01 07:58:23','2015-12-01 07:58:23',5,13,9),(19,'TR000007','Âò+%]ò%µ˛bfäa•','ventas de articulos','0.00','100.00',1,'2015-12-01 07:58:23','2015-12-01 07:58:23',103,13,9),(23,'TR000017','ÂòHÙŒ^ôµ˛bfäa•','fdsfsdfsd','10.00','0.00',1,'2015-12-01 11:31:47','2015-12-01 11:31:47',7,10,13),(24,'TR000018','ÂòHÙŒtÌµ˛bfäa•','fdsfsdfsd','0.00','10.00',1,'2015-12-01 11:31:47','2015-12-01 11:31:47',110,11,13),(25,'TR000018','Âò]’Æáµ˛bfäa•','pagos varios','100.00','0.00',1,'2015-12-01 14:01:13','2015-12-01 14:01:13',4,2,14),(26,'TR000019','Âò]’√˝µ˛bfäa•','pagos varios','0.00','100.00',1,'2015-12-01 14:01:13','2015-12-01 14:01:13',97,2,14),(27,'TR000020','Âò^25ÊNµ˛bfäa•','pagos varios 2','50.00','0.00',1,'2015-12-01 14:03:49','2015-12-01 14:03:49',4,9,15),(28,'TR000021','Âò^25¸Òµ˛bfäa•','pagos varios 2','0.00','50.00',1,'2015-12-01 14:03:49','2015-12-01 14:03:49',98,9,15),(29,'TR000022','Âò^ÿÛPµ˛bfäa•','pago 3','60.00','0.00',1,'2015-12-01 14:08:29','2015-12-01 14:08:29',5,2,16),(30,'TR000023','Âò^ÿÛbäµ˛bfäa•','pago 3','0.00','60.00',1,'2015-12-01 14:08:29','2015-12-01 14:08:29',104,2,16),(31,'TR000024','Âòi∆§ÒŸµ˛bfäa•','pagos varios datos','20.00','0.00',1,'2015-12-01 15:26:43','2015-12-01 15:26:43',4,12,17),(32,'TR000025','Âòi∆•pµ˛bfäa•','pagos varios datos','0.00','20.00',1,'2015-12-01 15:26:43','2015-12-01 15:26:43',115,12,17),(33,'TR000026','Âß‡∫ïÛ˜ëûbfäa•','pruebas para presupuesto','15.00','0.00',1,'2015-12-21 07:45:49','2015-12-21 07:45:49',97,13,18),(34,'TR000027','Âß‡∫ñ&Wëûbfäa•','pruebas para presupuesto','0.00','15.00',1,'2015-12-21 07:45:49','2015-12-21 07:45:49',5,13,18),(35,'TR000028','ÂßÈÉ∂ëûbfäa•','nuevo test','0.00','40.00',1,'2015-12-21 08:48:41','2015-12-21 08:48:41',4,13,19),(36,'TR000029','ÂßÈÉóïëûbfäa•','nuevo test','40.00','0.00',1,'2015-12-21 08:48:41','2015-12-21 08:48:41',95,13,19);

/*Table structure for table `cotz_cotizaciones` */

DROP TABLE IF EXISTS `cotz_cotizaciones`;

CREATE TABLE `cotz_cotizaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_cotizacion` binary(16) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha_desde` datetime NOT NULL,
  `fecha_hasta` datetime NOT NULL,
  `etapa` varchar(100) NOT NULL,
  `creado_por` int(11) NOT NULL,
  `comentario` text,
  `empresa_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `termino_pago` varchar(100) NOT NULL,
  `fecha_termino_pago` datetime NOT NULL,
  `item_precio_id` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `impuestos` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `index_empresa_id` (`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `cotz_cotizaciones` */

insert  into `cotz_cotizaciones`(`id`,`uuid_cotizacion`,`codigo`,`cliente_id`,`fecha_desde`,`fecha_hasta`,`etapa`,`creado_por`,`comentario`,`empresa_id`,`created_at`,`updated_at`,`termino_pago`,`fecha_termino_pago`,`item_precio_id`,`subtotal`,`impuestos`,`total`) values (1,'Â√êœ AÇlbfäa•','QT16000001',1,'2016-01-18 13:24:15','2016-01-29 13:24:15','ganada',28,NULL,1,'2016-01-25 13:24:15','2016-01-26 08:55:57','14_dias','2016-02-01 13:24:15',4,'136.00','6.80','142.80'),(2,'Â√êÛÂá\nÇlbfäa•','QT16000002',4,'2016-02-01 13:25:16','2016-02-24 13:25:16','ganada',28,NULL,1,'2016-01-25 13:25:16','2016-01-25 13:48:42','14_dias','2016-02-15 13:25:16',4,'931.01','46.55','977.56'),(3,'Â√ëy+Çlbfäa•','QT16000003',6,'2016-02-02 13:26:18','2016-02-17 13:26:18','ganada',28,NULL,1,'2016-01-25 13:26:18','2016-01-25 13:26:54','14_dias','2016-02-16 13:26:18',4,'253.99','12.70','266.69'),(4,'Âƒ.–ùL\"Çlbfäa•','QT16000004',5,'2016-01-26 08:15:17','2016-02-05 08:15:17','ganada',28,NULL,1,'2016-01-26 08:15:17','2016-01-26 08:16:11','14_dias','2016-02-09 08:15:17',3,'87.00','4.35','91.35'),(5,'Â∆üã“⁄;Çlbfäa•','QT16000005',3,'2016-01-29 10:47:17','2016-02-12 10:47:17','ganada',28,NULL,1,'2016-01-29 10:47:17','2016-01-29 10:59:52','14_dias','2016-02-12 10:47:17',3,'118.05','5.90','123.95');

/*Table structure for table `cotz_cotizaciones_catalogo` */

DROP TABLE IF EXISTS `cotz_cotizaciones_catalogo`;

CREATE TABLE `cotz_cotizaciones_catalogo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  `tipo` varchar(200) NOT NULL,
  `orden` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `cotz_cotizaciones_catalogo` */

insert  into `cotz_cotizaciones_catalogo`(`id`,`key`,`valor`,`etiqueta`,`tipo`,`orden`) values (1,'1','Al contado','al_contado','termino_pago',1),(2,'2','7 d√≠as','7_dias','termino_pago',2),(3,'3','14 d√≠as','14_dias','termino_pago',3),(4,'4','30 d√≠as','30_dias','termino_pago',4),(5,'5','60 d√≠as','60_dias','termino_pago',5),(6,'6','90 d√≠as','90_dias','termino_pago',6),(7,'7','Abierta','abierta','etapa',7),(9,'9','Ganada','ganada','etapa',9),(10,'10','Perdida','perdida','etapa',10),(11,'11','Anulada','anulada','etapa',11);

/*Table structure for table `cotz_cotizaciones_items` */

DROP TABLE IF EXISTS `cotz_cotizaciones_items`;

CREATE TABLE `cotz_cotizaciones_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_cotizacion_item` binary(16) NOT NULL,
  `cotizacion_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `unidad_id` int(11) NOT NULL,
  `precio_unidad` decimal(10,2) NOT NULL,
  `impuesto_id` int(11) NOT NULL,
  `descuento` decimal(10,2) NOT NULL,
  `cuenta_id` int(11) NOT NULL,
  `precio_total` decimal(10,2) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_unique` (`id`,`cotizacion_id`,`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `cotz_cotizaciones_items` */

insert  into `cotz_cotizaciones_items`(`id`,`uuid_cotizacion_item`,`cotizacion_id`,`item_id`,`empresa_id`,`cantidad`,`unidad_id`,`precio_unidad`,`impuesto_id`,`descuento`,`cuenta_id`,`precio_total`,`updated_at`,`created_at`) values (1,'Â√êœÕ[gÇlbfäa•',1,3,1,1,2,'34.00',1,'0.00',4,'35.70','2016-01-25 13:24:15','2016-01-25 13:24:15'),(2,'Â√êœœ€Çlbfäa•',1,3,1,3,2,'34.00',1,'0.00',4,'107.10','2016-01-25 13:24:15','2016-01-25 13:24:15'),(3,'Â√êÛËLîÇlbfäa•',2,4,1,1,1,'31.04',1,'0.00',4,'32.59','2016-01-25 13:25:16','2016-01-25 13:25:16'),(4,'Â√êÛË¿GÇlbfäa•',2,5,1,3,1,'299.99',1,'0.00',4,'944.97','2016-01-25 13:25:16','2016-01-25 13:25:16'),(5,'Â√ëRÒÇlbfäa•',3,3,1,6,2,'34.00',1,'0.00',4,'214.20','2016-01-25 13:26:18','2016-01-25 13:26:18'),(6,'Â√ë»*Çlbfäa•',3,6,1,1,2,'49.99',1,'0.00',4,'52.49','2016-01-25 13:26:18','2016-01-25 13:26:18'),(7,'Âƒ.–°DÂÇlbfäa•',4,3,1,1,2,'87.00',1,'0.00',4,'91.35','2016-01-26 08:15:17','2016-01-26 08:15:17'),(8,'Â∆üã⁄ûÇlbfäa•',5,3,1,1,2,'87.00',1,'0.00',4,'91.35','2016-01-29 10:47:17','2016-01-29 10:47:17'),(9,'Â∆üã‹\\{Çlbfäa•',5,4,1,1,1,'31.05',1,'0.00',4,'32.60','2016-01-29 10:47:17','2016-01-29 10:47:17');

/*Table structure for table `dep_departamentos` */

DROP TABLE IF EXISTS `dep_departamentos`;

CREATE TABLE `dep_departamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `empresa_id` int(11) DEFAULT NULL,
  `nombre` varchar(150) DEFAULT NULL,
  `estado` tinyint(2) DEFAULT '1' COMMENT '1=activo, 0=inactivo',
  `creado_por` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_departamentos_empresas3` (`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `dep_departamentos` */

insert  into `dep_departamentos`(`id`,`empresa_id`,`nombre`,`estado`,`creado_por`,`created_at`,`updated_at`) values (1,1,'Ventas',1,1,'2015-11-06 16:01:10','2015-11-06 16:01:10'),(2,1,'Bodega',0,1,'2015-11-06 16:01:46','2015-11-16 10:02:08'),(3,1,'Hogar',1,1,'2015-11-06 16:02:00','2015-11-06 16:17:34'),(4,2,'RRHH',1,1,'2015-11-06 16:10:57','2015-11-06 16:10:57'),(5,2,'Compras',1,1,'2015-11-06 16:11:07','2015-11-06 16:11:07'),(6,2,'Fichas',1,1,'2015-11-06 16:11:17','2015-11-06 16:11:17');

/*Table structure for table `dep_departamentos_centros` */

DROP TABLE IF EXISTS `dep_departamentos_centros`;

CREATE TABLE `dep_departamentos_centros` (
  `departamento_id` int(11) NOT NULL DEFAULT '0',
  `centro_id` int(11) NOT NULL DEFAULT '0',
  `empresa_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`departamento_id`,`centro_id`,`empresa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `dep_departamentos_centros` */

insert  into `dep_departamentos_centros`(`departamento_id`,`centro_id`,`empresa_id`) values (1,4,1),(1,8,1),(1,13,1),(1,16,1),(1,17,1),(2,4,1),(2,8,1),(2,12,18),(2,13,1),(2,16,1),(2,17,1),(3,4,1),(3,8,1),(3,13,1),(3,16,1),(3,17,1),(4,4,1),(4,8,1),(4,10,1),(4,12,1),(4,16,1),(4,17,1),(5,4,1),(5,8,1),(5,10,1),(5,12,1),(5,16,1),(5,17,1),(6,4,1),(6,8,1),(6,10,1),(6,12,1),(6,16,1),(6,17,1),(7,8,1),(7,10,1),(7,12,1),(7,16,1),(7,21,1),(8,8,18),(8,12,1);

/*Table structure for table `empresas` */

DROP TABLE IF EXISTS `empresas`;

CREATE TABLE `empresas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_empresa` binary(16) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `ruc` varchar(100) DEFAULT NULL,
  `descripcion` varchar(250) DEFAULT NULL,
  `telefono` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `organizacion_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `ruc_UNIQUE` (`ruc`),
  KEY `fk_empresas_empresas1_idx` (`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

/*Data for the table `empresas` */

insert  into `empresas`(`id`,`uuid_empresa`,`nombre`,`fecha_creacion`,`empresa_id`,`ruc`,`descripcion`,`telefono`,`created_at`,`updated_at`,`logo`,`organizacion_id`) values (1,'ÔøΩvzÔøΩÔøΩa','Empresa A','2015-10-19 10:00:00',0,'63257841555','calle 50','500-0223','2015-10-19 10:00:00','2015-10-27 14:48:30','logos_01.jpg',1),(2,'ÔøΩvÔøΩÔøΩÔøΩÔ','Empresa A.1','2015-10-19 11:00:00',1,'658421478','Peru','236-5555','2015-10-19 11:00:00','2015-10-27 14:40:28','',1),(3,'ÔøΩvÔøΩHKŒ§ab','Empresa B','2015-10-19 12:00:00',0,'96582147',NULL,NULL,'2015-10-19 12:00:00','2015-10-19 11:00:00',NULL,0),(7,'Âw[Ó*wX§abfäa•','empresa 4',NULL,0,'99999999744',NULL,'254784','2015-10-20 13:54:17','2015-10-20 13:54:17','',0),(8,'Âw^6Òó$§abfäa•','empresa G','2015-10-20 14:10:39',0,'2154878',NULL,'2568547','2015-10-20 14:10:39','2015-10-20 14:10:39','',0),(12,'Âw^¿[‘“§abfäa•','presa','2015-10-20 14:14:29',0,'33666999','santiago','124587','2015-10-20 14:14:29','2015-10-20 14:14:29','',0),(13,'Âw_¨Â†§abfäa•','compass','2015-10-20 14:21:06',0,'69858944112222','santiago','5874562','2015-10-20 14:21:06','2015-10-20 14:21:06','',0),(14,'Âw_˘Pòh§abfäa•','farmacia','2015-10-20 14:23:14',0,'1114445558888','peru','965874','2015-10-20 14:23:14','2015-10-20 14:23:14','',0),(15,'Âw`æ§¥§abfäa•','sal marina','2015-10-20 14:28:45',0,'7896547896547','peru','9998547','2015-10-20 14:28:45','2015-10-20 14:28:45','',0),(16,'Âwa3Ñê§abfäa•','otra empresa','2015-10-20 14:32:01',0,'55566688877','panama','7785455','2015-10-20 14:32:01','2015-10-20 14:32:01','',0),(17,'Âwaë›ˇ∫§abfäa•','otra A2','2015-10-20 14:34:40',1,'12547896524','panama','36985214','2015-10-20 14:34:40','2015-10-21 13:24:27','logos_01.jpg',0),(18,'ÂwzÖÓWS§abfäa•','tests','2015-10-20 17:33:17',0,'25871444111','panama','14254856','2015-10-20 17:33:17','2015-10-20 17:33:17','',0),(19,'Âwı`ÑÄ)§abfäa•','agua viva','2015-10-21 08:12:42',0,'111000111','panama','2589657','2015-10-21 08:12:42','2015-10-21 08:12:42','',0),(20,'Âwıèyk¢§abfäa•','agua viva 2','2015-10-21 08:14:01',0,'777111777','panama','2369854','2015-10-21 08:14:01','2015-10-21 08:14:01','',0),(21,'Âw˘îaÍ§abfäa•','logo tipo','2015-10-21 08:39:13',0,'96665555881111','panama','2569874','2015-10-21 08:39:13','2015-10-21 08:39:13','131001445434752.jpg',0),(22,'ÂwˇÀNWå•bfäa•','sal via','2015-10-21 09:21:50',0,'123654789','salvador','8563214','2015-10-21 09:21:50','2015-10-21 09:21:50','empresa-de-comunicacion-vector-logo-plantilla_63-2568.jpg',0),(23,'Âx\0[…=å•bfäa•','logo it','2015-10-21 09:29:00',21,'556655544444','panama','2568745214','2015-10-21 09:29:00','2015-10-21 13:32:00','',0),(25,'Â| .‰î±bfäa•','Gas Natural','2015-10-27 11:45:51',0,'1234569854','panama','500-3254','2015-10-27 11:45:51','2015-10-27 11:45:51','',1),(26,'Â|ÿäy±bfäa•','ENA','2015-10-27 13:28:37',0,'3652147895','Panama','600-0000','2015-10-27 13:28:37','2015-10-27 13:28:37','',1),(27,'Â|Ÿ3bÅ±bfäa•','Corredores','2015-10-27 13:32:04',0,'1250003000','Panama','652-521478','2015-10-27 13:32:04','2015-10-27 13:32:04','',1),(28,'Â|⁄≈k’±bfäa•','Tabaco Panama','2015-10-27 13:44:35',0,'30011100044','panama','800-2012','2015-10-27 13:44:35','2015-10-27 13:44:35','',1),(29,'Â|‹ÉV}Ω±bfäa•','Pozuelo','2015-10-27 13:57:04',0,'7744001177','Costa del Este','800-0202','2015-10-27 13:57:04','2015-10-27 13:57:04','',1),(30,'Â~t¥?…ï±bfäa•','solo una','2015-10-29 14:39:00',0,'89655114400','Panama','236-6985','2015-10-29 14:39:00','2015-10-29 14:39:00','',2),(31,'Â~tÛF\0t±bfäa•','blue','2015-10-29 14:40:46',0,'12456999999','panama','236-2365','2015-10-29 14:40:46','2015-10-29 14:56:20','logo-sidebar.png',2),(32,'Â~wSÌ\nô±bfäa•','3 que 1','2015-10-29 14:57:47',0,'3336665521444','panama','256-1245','2015-10-29 14:57:47','2015-10-29 14:57:47','',2),(33,'Â~~*f≠T±bfäa•','soya','2015-10-29 15:46:44',0,'336652145999','panama','1258-9658','2015-10-29 15:46:44','2015-10-29 15:46:44','',3);

/*Table structure for table `empresas_has_modulos` */

DROP TABLE IF EXISTS `empresas_has_modulos`;

CREATE TABLE `empresas_has_modulos` (
  `empresas_id` int(11) NOT NULL,
  `modulos_id` int(11) NOT NULL,
  PRIMARY KEY (`empresas_id`,`modulos_id`),
  UNIQUE KEY `fk_empresas_has_modulos_empresas1_idx` (`empresas_id`,`modulos_id`),
  KEY `fk_empresas_has_modulos_modulos1_idx` (`modulos_id`),
  CONSTRAINT `fk_empresas_has_modulos_modulos1` FOREIGN KEY (`modulos_id`) REFERENCES `modulos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `empresas_has_modulos` */

/*Table structure for table `empresas_has_roles` */

DROP TABLE IF EXISTS `empresas_has_roles`;

CREATE TABLE `empresas_has_roles` (
  `empresa_id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`empresa_id`,`rol_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_empresas_has_roles_roles1` (`rol_id`),
  KEY `fk_empresas_has_roles_empresas1` (`empresa_id`),
  CONSTRAINT `fk_empresas_has_roles_empresas1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `fk_empresas_has_roles_roles1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `empresas_has_roles` */

insert  into `empresas_has_roles`(`empresa_id`,`rol_id`,`id`) values (0,3,1),(1,5,2);

/*Table structure for table `entrada_manual_comentario` */

DROP TABLE IF EXISTS `entrada_manual_comentario`;

CREATE TABLE `entrada_manual_comentario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_comentario` binary(16) DEFAULT NULL,
  `comentario` text,
  `entrada_id` int(11) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `entrada_manual_comentario` */

insert  into `entrada_manual_comentario`(`id`,`uuid_comentario`,`comentario`,`entrada_id`,`usuario_id`,`empresa_id`,`created_at`,`updated_at`) values (1,'Âô8§Å\nÎµ˛bfäa•','<p>actualizacion de datos</p>\n',1,10,1,'2015-12-02 16:07:31','2015-12-02 16:07:31'),(2,'Âô–ä~È{µ˛bfäa•','<p>otro comentario</p>\n',1,10,1,'2015-12-03 10:14:51','2015-12-03 10:14:51'),(3,'Âô‘˚¥Gµ˛bfäa•','<p>otro comentario</p>\n',1,10,1,'2015-12-03 10:46:39','2015-12-03 10:46:39'),(4,'Âôÿ	•∫Áµ˛bfäa•','<p>primer comentario</p>\n',4,10,1,'2015-12-03 11:08:31','2015-12-03 11:08:31');

/*Table structure for table `fac_factura_catalogo` */

DROP TABLE IF EXISTS `fac_factura_catalogo`;

CREATE TABLE `fac_factura_catalogo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  `tipo` varchar(200) NOT NULL,
  `orden` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `fac_factura_catalogo` */

insert  into `fac_factura_catalogo`(`id`,`key`,`valor`,`etiqueta`,`tipo`,`orden`) values (1,'1','Al contado','al_contado','termino_pago',1),(2,'2','7 d√≠as','7_dias','termino_pago',2),(3,'3','14 d√≠as','14_dias','termino_pago',3),(4,'4','30 d√≠as','30_dias','termino_pago',4),(5,'5','60 d√≠as','60_dias','termino_pago',5),(6,'6','90 d√≠as','90_dias','termino_pago',6),(7,'7','Por Pagar','por_pagar','etapa',7),(8,'8','Pago parcial','pago_parcial','etapa',8),(9,'9','Pagada','pagada','etapa',9),(11,'11','Anulada','anulada','etapa',11);

/*Table structure for table `fac_factura_items` */

DROP TABLE IF EXISTS `fac_factura_items`;

CREATE TABLE `fac_factura_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_factura_item` binary(16) DEFAULT NULL,
  `factura_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `unidad_id` int(11) DEFAULT NULL,
  `precio_unidad` decimal(10,2) DEFAULT NULL,
  `impuesto_id` int(11) DEFAULT NULL,
  `descuento` decimal(10,2) DEFAULT NULL,
  `cuenta_id` int(11) DEFAULT NULL,
  `precio_total` decimal(10,2) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_unique` (`id`,`factura_id`,`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `fac_factura_items` */

insert  into `fac_factura_items`(`id`,`uuid_factura_item`,`factura_id`,`item_id`,`empresa_id`,`cantidad`,`unidad_id`,`precio_unidad`,`impuesto_id`,`descuento`,`cuenta_id`,`precio_total`,`updated_at`,`created_at`) values (1,'Âƒ[ÑßõøÇlbfäa•',1,3,1,1,2,'34.00',1,'0.00',4,'35.70','2016-01-26 13:35:17','2016-01-26 13:35:17'),(2,'Âƒ[Ñ©—©Çlbfäa•',1,3,1,3,2,'34.00',1,'0.00',4,'107.10','2016-01-26 13:35:17','2016-01-26 13:35:17'),(5,'Â≈-óMKÇlbfäa•',6,3,1,1,2,'87.00',1,'0.00',4,'91.35','2016-01-27 14:39:03','2016-01-27 14:39:03'),(6,'Â≈-óRnÀÇlbfäa•',6,3,1,1,1,'17.40',1,'0.00',4,'18.27','2016-01-27 14:39:03','2016-01-27 14:39:03'),(7,'Â≈8È∑kÑÇlbfäa•',7,3,1,2,2,'34.00',1,'0.00',4,'71.40','2016-01-27 16:00:05','2016-01-27 16:00:05'),(8,'Â≈8Èæ|pÇlbfäa•',7,4,1,2,1,'31.04',1,'0.00',4,'65.18','2016-01-27 16:00:05','2016-01-27 16:00:05'),(11,'Â≈ﬂÑÁ7àÇlbfäa•',6,4,1,1,1,'31.05',1,'0.00',4,'32.60','2016-01-28 11:52:42','2016-01-28 11:52:42');

/*Table structure for table `fac_facturas` */

DROP TABLE IF EXISTS `fac_facturas`;

CREATE TABLE `fac_facturas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid_factura` binary(16) DEFAULT NULL,
  `referencia` varchar(140) DEFAULT NULL,
  `codigo` varchar(100) DEFAULT NULL,
  `centro_contable_id` int(11) DEFAULT NULL,
  `bodega_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `orden_venta_id` int(11) DEFAULT NULL,
  `cotizacion_id` int(11) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `fecha_desde` datetime DEFAULT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `comentario` text,
  `termino_pago` varchar(100) DEFAULT NULL,
  `fecha_termino_pago` datetime DEFAULT NULL,
  `item_precio_id` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `impuestos` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `fac_facturas` */

insert  into `fac_facturas`(`id`,`uuid_factura`,`referencia`,`codigo`,`centro_contable_id`,`bodega_id`,`cliente_id`,`created_by`,`created_at`,`updated_at`,`empresa_id`,`orden_venta_id`,`cotizacion_id`,`estado`,`fecha_desde`,`fecha_hasta`,`comentario`,`termino_pago`,`fecha_termino_pago`,`item_precio_id`,`subtotal`,`impuestos`,`total`) values (1,'Âƒ[Ñ£mÈÇlbfäa•',NULL,'INV16000001',11,2,1,28,'2016-01-26 13:35:17','2016-01-28 11:49:42',1,6,1,'por_pagar','2016-01-18 11:49:42','2016-01-29 11:49:42','guardado al editar','14_dias','2016-02-12 11:49:42',4,'136.00','6.80','142.80'),(6,'Â≈-óIWUÇlbfäa•',NULL,'INV16000002',9,3,4,28,'2016-01-27 14:39:03','2016-01-28 11:52:42',1,NULL,NULL,'por_pagar','2016-01-27 11:52:42','2016-02-28 11:52:42',NULL,'14_dias','2016-03-13 11:52:42',3,'135.45','6.77','142.22'),(7,'Â≈8ÈØâßÇlbfäa•',NULL,'INV16000003',9,3,3,28,'2016-01-27 16:00:05','2016-01-27 16:00:05',1,2,NULL,'por_pagar','2016-01-25 16:00:05','2016-01-31 16:00:05',NULL,'30_dias','2016-03-01 16:00:05',4,'130.08','6.50','136.58');

/*Table structure for table `inv_categorias` */

DROP TABLE IF EXISTS `inv_categorias`;

CREATE TABLE `inv_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_categoria` binary(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `empresa_id` int(11) NOT NULL,
  `uuid_activo` binary(16) NOT NULL,
  `uuid_ingreso` binary(16) NOT NULL,
  `uuid_gasto` binary(16) NOT NULL,
  `uuid_variante` binary(16) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `inv_categorias` */

insert  into `inv_categorias`(`id`,`uuid_categoria`,`nombre`,`descripcion`,`estado`,`empresa_id`,`uuid_activo`,`uuid_ingreso`,`uuid_gasto`,`uuid_variante`,`created_at`,`updated_at`,`created_by`) values (1,'Âí\Z[∑ï„ºvNT†','Categoria 1','Descripcion 1',1,1,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','0000-00-00 00:00:00','0000-00-00 00:00:00',0),(2,'Âí\Z[æTóï„ºvNT†','Categoria 2','Descripcion 2',1,1,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','0000-00-00 00:00:00','0000-00-00 00:00:00',0),(3,'Âí\Z[æWbï„ºvNT†','Categoria 3','Descripcion 3',1,1,'\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','0000-00-00 00:00:00','0000-00-00 00:00:00',0),(4,'ÂÆ`†”‹¨è»ºvNT†','Categor√≠a Nueva','Descripci√≥n de la categor√≠a nueva (editada)',1,1,'ÂòFä$ìËï„ºvNT†','ÂòFä&Æï„ºvNT†','ÂòFä&AŸï„ºvNT†','ÂòFä&F‰ï„ºvNT†','2015-12-29 14:16:14','2015-12-29 14:16:28',1),(5,'ÂÆf<°è»ºvNT†','Categoria c','Ventas',1,1,'ÂòFä$Ú°ï„ºvNT†','ÂòFä&Æï„ºvNT†','ÂòFä&<Ÿï„ºvNT†','ÂòFä&AŸï„ºvNT†','2015-12-29 14:56:22','2015-12-29 14:56:22',1);

/*Table structure for table `inv_inventarios_campos` */

DROP TABLE IF EXISTS `inv_inventarios_campos`;

CREATE TABLE `inv_inventarios_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

/*Data for the table `inv_inventarios_campos` */

insert  into `inv_inventarios_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'codigo','C&oacute;digo',0,14,'activo','','','div','',1,'','0000-00-00 00:00:00',2),(2,'nombre','Nombre',0,14,'activo','','','div','',1,'','0000-00-00 00:00:00',4),(3,'descripcion','Descripci&oacute;n',0,15,'activo','{\"class\":\"form-control\",\"style\":\"height:115px;\"}','','div','',0,'','0000-00-00 00:00:00',6),(4,'categorias][','Categor&iacute;a(s)',0,18,'activo','{\"class\":\"chosen categorias\",\"multiple\":\"true\"}','','div','inv_categorias',1,'','0000-00-00 00:00:00',8),(5,'tipo','Tipo',0,12,'activo','{\"class\":\"chosen\"}','','div','',1,'','0000-00-00 00:00:00',10),(6,'unidad_medida','Unidad de medida',0,14,'activo','','unidades','tabla-dinamica','',0,'','0000-00-00 00:00:00',12),(7,'unidad','U/M',0,18,'activo','{\"class\":\"chosen unidad\",\"style\":\"min-width:300px;\"}','unidades','tabla-dinamica','inv_unidades',1,'','0000-00-00 00:00:00',14),(8,'base','Base',0,11,'activo','{\"class\":\"base\"}','unidades','tabla-dinamica','',0,'','0000-00-00 00:00:00',16),(9,'factor_conversion','Factor de conversi&oacute;n',0,14,'activo','{\"class\":\"form-control factor_conversion\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,2}\',\'greedy\':false\"}','unidades','tabla-dinamica','',1,'','0000-00-00 00:00:00',18),(10,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;',0,1,'activo','{\"class\":\"btn btn-default btn-block agregarBtn\"}','unidades','tabla-dinamica','',0,'','0000-00-00 00:00:00',20),(11,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;',0,1,'activo','{\"class\":\"btn btn-default btn-block eliminarBtn\",\"style\":\"max-width:40px;\"}','unidades','tabla-dinamica','',0,'','0000-00-00 00:00:00',22),(12,'id_item_unidad','',0,7,'activo','{\"class\":\"id_item_unidad\"}','unidades','tabla-dinamica','',0,'','0000-00-00 00:00:00',24),(13,'unidad_medida2','Unidad de medida2',0,14,'activo','','unidades','tabla-dinamica','',0,'','0000-00-00 00:00:00',23),(14,'precio_venta','Precio de Venta',0,14,'activo','{\"class\":\"form-control precio_venta\"}','precios','tabla-dinamica','',0,'','0000-00-00 00:00:00',26),(15,'precio','Precio',0,22,'activo','{\"style\":\"width:100%;\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control precio\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,2}\',\'greedy\':false\"}','precios','tabla-dinamica','',0,'','0000-00-00 00:00:00',28),(16,'precio_panel','',0,7,'activo','{\"class\":\"precio_panel\"}','','div','',0,'','0000-00-00 00:00:00',25),(17,'id_precio','id_precio',0,14,'activo','{\"class\":\"form-control id_precio\"}','precios','tabla-dinamica','',0,'','0000-00-00 00:00:00',30),(18,'activos','Activos',0,18,'activo','{\"class\":\"chosen activos\"}','','div','activos',1,'','0000-00-00 00:00:00',32),(19,'ingresos','Ingresos',0,18,'activo','{\"class\":\"chosen ingresos\"}','','div','ingresos',1,'','0000-00-00 00:00:00',34),(20,'gastos','Gastos',0,18,'activo','{\"class\":\"chosen gastos\"}','','div','gastos',1,'','0000-00-00 00:00:00',36),(21,'variantes','Variantes',0,18,'activo','{\"class\":\"chosen variantes\"}','','div','variantes',1,'','0000-00-00 00:00:00',38),(22,'compra','Compra',0,18,'activo','{\"class\":\"chosen compra\"}','','div','contab_impuestos',1,'','0000-00-00 00:00:00',40),(23,'venta','Venta',0,18,'activo','{\"class\":\"chosen venta\"}','','div','contab_impuestos',1,'','0000-00-00 00:00:00',42),(24,'cancelarItem','Cancelar',0,8,'activo','{\"class\":\"btn btn-default pull-right\"}','','div','',0,'inventarios/listar','0000-00-00 00:00:00',44),(25,'guardarItem','Guardar',0,13,'activo','{\"class\":\"btn btn-primary btn-block\",\"style\":\"width:90px;\"}','','div','',0,'','0000-00-00 00:00:00',46);

/*Table structure for table `inv_inventarios_cat` */

DROP TABLE IF EXISTS `inv_inventarios_cat`;

CREATE TABLE `inv_inventarios_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `inv_inventarios_cat` */

insert  into `inv_inventarios_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,0,'','Activo'),(2,0,'','Inactivo'),(3,8,'1','<span style=\'width:72px;display:block\'></span>'),(4,5,'','Inventariado'),(5,5,'','No inventariado'),(6,5,'','Activo fijo');

/*Table structure for table `inv_item_inv_unidad` */

DROP TABLE IF EXISTS `inv_item_inv_unidad`;

CREATE TABLE `inv_item_inv_unidad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `id_unidad` int(11) NOT NULL,
  `base` tinyint(1) NOT NULL DEFAULT '0',
  `factor_conversion` decimal(10,2) NOT NULL DEFAULT '1.00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Data for the table `inv_item_inv_unidad` */

insert  into `inv_item_inv_unidad`(`id`,`id_item`,`id_unidad`,`base`,`factor_conversion`) values (5,1,1,1,'1.00'),(6,1,2,0,'12.00'),(7,2,1,0,'1.00'),(8,2,2,1,'1.00'),(9,3,1,0,'0.20'),(10,3,2,1,'1.00'),(11,4,1,1,'1.00'),(13,5,1,1,'1.00'),(14,6,2,1,'1.00');

/*Table structure for table `inv_items` */

DROP TABLE IF EXISTS `inv_items`;

CREATE TABLE `inv_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_item` binary(16) NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `empresa_id` int(11) NOT NULL,
  `creado_por` int(11) NOT NULL,
  `tipo_id` int(11) NOT NULL,
  `uuid_activo` binary(16) NOT NULL,
  `uuid_ingreso` binary(16) NOT NULL,
  `uuid_gasto` binary(16) NOT NULL,
  `uuid_variante` binary(16) NOT NULL,
  `uuid_compra` binary(16) NOT NULL,
  `uuid_venta` binary(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `inv_items` */

insert  into `inv_items`(`id`,`uuid_item`,`codigo`,`nombre`,`descripcion`,`fecha_creacion`,`estado`,`empresa_id`,`creado_por`,`tipo_id`,`uuid_activo`,`uuid_ingreso`,`uuid_gasto`,`uuid_variante`,`uuid_compra`,`uuid_venta`) values (1,'uuid_item\0\0\0\0\0\0\0','COD001','Item 1','desc 1','0000-00-00 00:00:00',1,1,1,4,'Âìp⁄ı]2µ˛bfäa•','ÂòFä&∏ï„ºvNT†','ÂòFä&AŸï„ºvNT†','ÂòFä&AŸï„ºvNT†','Âì{1ãnÄï„ºvNT†','ÂíÙS\"xˇï„ºvNT†'),(2,'...\0\0\0\0\0\0\0\0\0\0\0\0\0','ORI112W','Item 2','desc 2','0000-00-00 00:00:00',1,1,1,4,'Âìp⁄ı]2µ˛bfäa•','ÂòFä&¿ï„ºvNT†','ÂòFä&ºvï„ºvNT†','ÂòFä&∑Üï„ºvNT†','Âì{1ãnÄï„ºvNT†','ÂíÙS\"xˇï„ºvNT†'),(3,'ÂòGö8f∏ï„ºvNT†','TOR00010','TORNILLO 10mm','12345','2015-12-01 00:00:00',1,1,1,4,'Âìp⁄ı]2µ˛bfäa•','ÂòFä%¸§ï„ºvNT†','ÂòFä&-Ωï„ºvNT†','ÂòFä&(πï„ºvNT†','Âì{1ãnÄï„ºvNT†','ÂíÙS\"xˇï„ºvNT†'),(4,'Âòaw’ï∏ï„ºvNT†','QWEW','Alicate','Alicate','2015-12-01 00:00:00',1,1,1,6,'Âìp⁄ı]2µ˛bfäa•','ÂòFä&Æï„ºvNT†','ÂòFä&<Ÿï„ºvNT†','ÂòFä&(πï„ºvNT†','ÂíÙS\"xˇï„ºvNT†','ÂíÙS\"xˇï„ºvNT†'),(5,'Âòb°ö\\ï„ºvNT†','13234','Extractor ','extractor ','2015-12-01 00:00:00',1,1,1,4,'Âìp⁄ı]2µ˛bfäa•','ÂòFä&Æï„ºvNT†','ÂòFä&(πï„ºvNT†','ÂòFä&<Ÿï„ºvNT†','Âì{1ãnÄï„ºvNT†','ÂíÙS\"xˇï„ºvNT†'),(6,'Âòc!&¸èï„ºvNT†','13245t','Llave Allen','Llave Allen','2015-12-01 00:00:00',1,1,1,5,'Âìp⁄ı]2µ˛bfäa•','ÂòFä&∏ï„ºvNT†','ÂòFä&âµï„ºvNT†','ÂòFä&-Ωï„ºvNT†','Âì{1ãnÄï„ºvNT†','ÂíÙS\"xˇï„ºvNT†');

/*Table structure for table `inv_items_cat` */

DROP TABLE IF EXISTS `inv_items_cat`;

CREATE TABLE `inv_items_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `inv_items_cat` */

insert  into `inv_items_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,0,'','Activo'),(2,0,'Inactivo','');

/*Table structure for table `inv_items_categorias` */

DROP TABLE IF EXISTS `inv_items_categorias`;

CREATE TABLE `inv_items_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `inv_items_categorias` */

insert  into `inv_items_categorias`(`id`,`id_item`,`id_categoria`) values (1,1,1),(2,2,1),(3,2,2),(4,3,1),(6,4,1),(7,5,2),(8,6,3),(9,3,2),(10,3,3),(11,3,4);

/*Table structure for table `inv_items_precios` */

DROP TABLE IF EXISTS `inv_items_precios`;

CREATE TABLE `inv_items_precios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `id_precio` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

/*Data for the table `inv_items_precios` */

insert  into `inv_items_precios`(`id`,`id_item`,`id_precio`,`precio`) values (1,3,1,'100.00'),(2,3,2,'99.99'),(3,3,3,'87.00'),(4,3,4,'34.00'),(5,3,5,'120.00'),(6,4,1,'32.06'),(7,4,2,'30.45'),(8,4,3,'31.05'),(9,4,4,'31.04'),(10,4,5,'29.99'),(11,5,1,'234.89'),(12,5,2,'233.99'),(13,5,3,'230.09'),(14,5,4,'299.99'),(15,5,5,'280.00'),(16,6,1,'56.89'),(17,6,2,'50.00'),(18,6,3,'76.00'),(19,6,4,'49.99'),(20,6,5,'78.00'),(21,1,1,'0.00'),(22,1,2,'0.00'),(23,1,3,'0.00'),(24,1,4,'0.00'),(25,1,5,'0.00'),(26,2,1,'0.00'),(27,2,2,'0.00'),(28,2,3,'0.00'),(29,2,4,'0.00'),(30,2,5,'0.00'),(31,3,6,'100.00'),(32,3,7,'100.00'),(33,2,6,'0.00'),(34,2,7,'0.00');

/*Table structure for table `inv_precios` */

DROP TABLE IF EXISTS `inv_precios`;

CREATE TABLE `inv_precios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_precio` binary(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `empresa_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `inv_precios` */

insert  into `inv_precios`(`id`,`uuid_precio`,`nombre`,`descripcion`,`estado`,`empresa_id`,`created_by`,`created_at`,`updated_at`) values (1,'Âï≥O•!ï„ºvNT†','Regular','Precio regular',1,1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(2,'Âï≥O®≠ï„ºvNT†','Preferido','Precio preferido',1,1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(3,'Âï≥O©ôï„ºvNT†','Precio web','Precio web',1,1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(4,'Âï≥O™\\ï„ºvNT†','Colaborador','Precio colaborador',1,1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(5,'Âï≥O´ï„ºvNT†','Platinium','Precio platiniun',1,1,0,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,'ÂÆ`¬˘mCè»ºvNT†','Precio Nuevo','Precio Nuevo Descripci√≥n (editado)',1,1,1,'2015-12-29 14:17:11','2015-12-29 14:17:25'),(7,'ÂÆ˜Ø\nP‡è»ºvNT†','Precio Nuevo 2','Precio Nuevo 2',1,1,1,'2015-12-30 08:17:32','2015-12-30 08:17:32');

/*Table structure for table `inv_unidades` */

DROP TABLE IF EXISTS `inv_unidades`;

CREATE TABLE `inv_unidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_unidad` binary(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `empresa_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `inv_unidades` */

insert  into `inv_unidades`(`id`,`uuid_unidad`,`nombre`,`descripcion`,`estado`,`empresa_id`,`created_by`,`created_at`,`updated_at`) values (1,'uuid_unidad\0\0\0\0\0','Gramos',' Gramos - Editado',1,1,0,'0000-00-00 00:00:00','2015-12-30 14:50:39'),(2,'...\0\0\0\0\0\0\0\0\0\0\0\0\0','Kilos','Kilos - Editado',1,1,0,'0000-00-00 00:00:00','2015-12-30 14:50:22');

/*Table structure for table `lug_lugares` */

DROP TABLE IF EXISTS `lug_lugares`;

CREATE TABLE `lug_lugares` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_lugar` binary(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `id_empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `lug_lugares` */

insert  into `lug_lugares`(`id`,`uuid_lugar`,`nombre`,`estado`,`id_empresa`) values (1,'uuid_lugar\0\0\0\0\0\0','Lugar 1',1,1),(2,'...\0\0\0\0\0\0\0\0\0\0\0\0\0','Lugar 2',1,1);

/*Table structure for table `mod_catalogo_modulos` */

DROP TABLE IF EXISTS `mod_catalogo_modulos`;

CREATE TABLE `mod_catalogo_modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cat` int(11) NOT NULL,
  `id_modulo` int(11) NOT NULL,
  `id_campo` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;

/*Data for the table `mod_catalogo_modulos` */

insert  into `mod_catalogo_modulos`(`id`,`id_cat`,`id_modulo`,`id_campo`) values (1,1,18,10),(2,2,18,10),(3,3,18,11),(4,4,18,11),(5,5,18,11),(6,6,18,11),(7,7,18,11),(8,8,22,7),(9,9,22,7),(10,10,22,7),(11,11,22,7),(12,12,22,8),(13,13,22,8),(14,14,22,8),(15,15,22,9),(16,16,22,9),(17,17,18,77),(18,18,18,77),(19,17,18,84),(20,18,18,84),(21,17,18,86),(22,18,18,86),(23,17,18,88),(24,18,18,88),(25,19,18,22),(26,20,18,22),(27,21,18,22),(28,22,18,22),(29,23,18,22),(30,24,18,22),(31,25,18,22),(32,19,18,50),(33,20,18,50),(34,21,18,50),(35,22,18,50),(36,23,18,50),(37,24,18,50),(38,25,18,50),(39,19,18,57),(40,20,18,57),(41,21,18,57),(42,22,18,57),(43,23,18,57),(44,24,18,57),(45,25,18,57),(46,19,18,57),(47,20,18,57),(48,21,18,57),(49,22,18,57),(50,23,18,57),(51,24,18,57),(52,25,18,57),(53,19,18,64),(54,20,18,64),(55,21,18,64),(56,22,18,64),(57,23,18,64),(58,24,18,64),(59,25,18,64),(60,19,18,70),(61,20,18,70),(62,21,18,70),(63,22,18,70),(64,23,18,70),(65,24,18,70),(66,25,18,70),(67,19,18,74),(68,20,18,74),(69,21,18,74),(70,22,18,74),(71,23,18,74),(72,24,18,74),(73,25,18,74),(74,8,18,107),(75,9,18,107),(76,10,18,107),(77,11,18,107),(78,15,18,108),(79,16,18,108),(80,32,20,3),(81,31,20,3),(82,30,20,3),(83,26,20,2),(84,27,20,2),(85,28,20,2),(86,29,20,2),(87,33,18,111),(88,34,18,111),(89,17,18,112),(90,18,18,112),(91,35,18,114),(92,36,18,114),(93,37,18,114),(94,38,18,114),(95,39,18,114),(96,40,18,114),(97,41,18,114),(98,42,18,114),(99,43,18,114),(100,44,18,114),(101,19,18,127),(102,20,18,127),(103,21,18,127),(104,22,18,127),(105,23,18,127),(106,24,18,127),(107,25,18,127),(108,45,2,5),(109,46,2,5),(110,35,18,144),(111,36,18,144),(112,37,18,144),(113,38,18,144),(114,39,18,144),(115,40,18,144),(116,41,18,144),(117,42,18,144),(118,43,18,144),(119,44,18,144),(120,47,18,144),(121,48,18,144),(122,49,18,144),(123,50,18,145),(124,51,18,145),(125,52,18,145),(126,53,18,145),(127,54,18,145),(128,55,18,145),(129,56,18,158),(130,57,18,158),(131,58,18,158),(132,59,18,158),(133,60,18,158);

/*Table structure for table `mod_catalogos` */

DROP TABLE IF EXISTS `mod_catalogos`;

CREATE TABLE `mod_catalogos` (
  `id_cat` bigint(20) NOT NULL AUTO_INCREMENT,
  `identificador` varchar(150) NOT NULL COMMENT 'Nombre del catalogo',
  `valor` varchar(200) DEFAULT NULL,
  `etiqueta` varchar(200) NOT NULL,
  `orden` int(11) DEFAULT NULL COMMENT 'Orden en que se mostrara catalogo',
  `activo` enum('1','0') DEFAULT '1' COMMENT '1 activo, 0 inactivo',
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8;

/*Data for the table `mod_catalogos` */

insert  into `mod_catalogos`(`id_cat`,`identificador`,`valor`,`etiqueta`,`orden`,`activo`) values (1,'Sexo','femenino','Femenino',1,'1'),(2,'Sexo','masculino','Masculino',2,'1'),(3,'Estado Civil','soltero','Soltero',1,'1'),(4,'Estado Civil','casado','Casado',2,'1'),(5,'Estado Civil','unido','Unido',3,'1'),(6,'Estado Civil','divorciado','Divorciado',4,'1'),(7,'Estado Civil','viudo','Viudo',5,'1'),(8,'Forma de Pago','tarjeta_credito','Tarjeta de Credito',1,'1'),(9,'Forma de Pago','tarjeta_debito','Tarjeta de D&eacute;bito',2,'1'),(10,'Forma de Pago','deposito','Deposito',3,'1'),(11,'Forma de Pago','transferencia','Transferencia',4,'1'),(12,'Banco','banco_1','Banco 1',1,'1'),(13,'Banco ','banco_2','Banco 2',2,'1'),(14,'Banco','banco_3','Banco 3',3,'1'),(15,'Tipo de Cuenta','cuenta_1','Cuenta Corriente',1,'1'),(16,'Tipo de Cuenta','cuenta_2','Cuenta de Ahorros',2,'1'),(17,'Pregunta Cerrada 1','no','No',1,'1'),(18,'Pregunta Cerrada 1','si','Si',2,'1'),(19,'Parentesco','hijo','Hijo',1,'1'),(20,'Parentesco','esposa','Esposa',2,'1'),(21,'Parentesco','hermano','Hermano',3,'1'),(22,'Parentesco','madre','Madre',4,'1'),(23,'Parentesco','padre','Padre',5,'1'),(24,'Parentesco','tio','Tio',6,'1'),(25,'Parentesco','abuelo','Abuelo',7,'1'),(26,'deducciones','indemnizacion','Indemnizaci&oacute;n',1,'1'),(27,'deducciones','antiguedad','Prima de Antiguedad',2,'1'),(28,'deducciones','decimo','XIII mes**',3,'1'),(29,'deducciones','vacaciones','Vacaciones',4,'1'),(30,'acumulados','seguro_social','Seguro Social',1,'1'),(31,'acumulados','seguro_educativo','Seguro Educativo**',2,'1'),(32,'acumulados','renta','Impuesto Sobre la Renta',3,'1'),(33,'Tipo de declarante','individual','Individual',1,'1'),(34,'Tipo de declarante','conjunta','Conjunta',2,'1'),(35,'Provincias','bocas_del_toro','Bocas del Toro (1)',1,'1'),(36,'Provincias','chiriqui','Chiriqu&iacute; (4)',4,'1'),(37,'Provincias','colon','Col&oacute;n (3)',3,'1'),(38,'Provincias','cocle','Cocl&eacute; (2)',2,'1'),(39,'Provincias','darien','Dari&eacute;n (5)',5,'1'),(40,'Provincias','herrera','Herrera (6)',6,'1'),(41,'Provincias','los_santos','Los Santos (7)',7,'1'),(42,'Provincias','panama','Panam&aacute; (8)',8,'1'),(43,'Provincias','veraguas','Veraguas (9)',9,'1'),(44,'Provincias','panama_oeste','Panam&aacute; Oeste (13)',13,'1'),(45,'Identificacion','juridico','Jur&iacute;dico',1,'1'),(46,'Identificacion','natural','Natural',2,'1'),(47,'Provincias','guna_yala','Guna Yala (10)',10,'1'),(48,'Provincias','embera_wounan','Embera Wounann (11)',11,'1'),(49,'Provincias','ngabe_bugle','Ng&#228;be-Bugl&#233; (12)',12,'1'),(50,'Letra','0','0',1,'1'),(51,'Letra','n','N',2,'1'),(52,'Letra','pe','PE',3,'1'),(53,'Letra','pi','PI',4,'1'),(54,'Letra','pas','PAS',5,'1'),(55,'Letra','e','E',6,'1'),(56,'Calificacion 1','muy_malo','Muy Malo',1,'1'),(57,'Calificacion 1','malo','Malo',2,'1'),(58,'Calificacion 1','regular','Regular',3,'1'),(59,'Calificacion 1','bueno','Bueno',4,'1'),(60,'Calificacion 1','muy_bueno','Muy Bueno',5,'1');

/*Table structure for table `mod_formularios` */

DROP TABLE IF EXISTS `mod_formularios`;

CREATE TABLE `mod_formularios` (
  `id_formulario` int(11) NOT NULL AUTO_INCREMENT,
  `id_pestana` int(11) NOT NULL,
  `nombre_formulario` varchar(200) DEFAULT NULL,
  `atributos` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_formulario`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

/*Data for the table `mod_formularios` */

insert  into `mod_formularios`(`id_formulario`,`id_pestana`,`nombre_formulario`,`atributos`) values (1,1,'editarUsuario',NULL),(2,2,'editarUsuarioAdmin',NULL),(3,3,'crearClientePotencial',NULL),(4,4,'editarClientePotencial',NULL),(5,5,'formClienteN',NULL),(6,6,'formClienteNInformacionAdional',NULL),(7,7,'formClienteNInformacionPersonal',NULL),(8,8,'formClienteNAgentes',NULL),(9,9,'crearContacto',NULL),(10,10,'editarContacto',NULL),(11,11,'datosClienteJuridico',NULL),(12,12,'formClienteJuridicoPropiedades',NULL),(13,13,'datosClienteJuridico',NULL),(14,14,'formClienteJuridicoPropiedades',NULL),(15,15,'formClienteN',NULL),(16,16,'formClienteNInformacionAdional',NULL),(17,17,'formClienteNInformacionPersonal',NULL),(18,18,'formClienteNAgentes',NULL),(19,19,'formClientesNPropiedades',NULL),(20,20,'formClientesNPolizas',NULL),(21,21,'formClientesNPropiedades',NULL),(22,22,'formClientesNPolizas',NULL),(23,23,'formClienteJuridicoPolizas',NULL),(24,24,'formAgentes',NULL),(25,25,'formClienteJuridicoPolizas',NULL),(26,26,'formAgentes',NULL),(27,27,'formNuevaOportunidad',NULL),(28,28,'crearProyecto',NULL),(29,29,'crearPropiedad',NULL),(30,30,'editarProyecto',NULL),(31,31,'editarPropiedad',NULL),(32,32,'formEditarOportunidad',NULL),(33,33,'formNuevoAgente',NULL),(34,34,'crearActividad',NULL),(35,35,'crearActividad',NULL),(36,36,'formEditarAgente',NULL),(37,37,'editarActividad',NULL),(38,38,'crearCaso',NULL),(39,39,'editarCaso',NULL),(40,40,'crearColaboradorForm',NULL),(41,41,'crearPedidosForm',NULL),(42,42,'editarPedidosForm',NULL),(43,43,'crearProveedoresForm',NULL),(44,44,'editarProveedoresForm',NULL),(45,45,'crearOrdenesForm',NULL),(46,46,'editarOrdenesForm',NULL),(47,47,'datosEspecificosForm',NULL),(48,48,'crearCentroForm',NULL),(50,50,'crearAseguradora',NULL),(51,51,'crearComisionForm',NULL),(52,52,'crearItemsForm',NULL),(53,53,'editarItemsForm',NULL),(54,54,'crearEntradaManualForm',NULL),(55,55,'editarAseguradora',NULL),(56,56,'crearAjustesForm',NULL),(57,57,'editarAjustesForm',NULL),(58,58,'crearBodegasForm',NULL),(59,59,'editarBodegasForm',NULL),(60,60,'editarEntradasForm',NULL),(61,61,'editarComision',NULL),(62,62,'crearPlanilla',NULL),(63,63,'formulario82',NULL),(64,64,'crearTrasladosForm',NULL),(65,65,'editarTrasladosForm',NULL),(66,66,'evaluacionForm','{\"ng-controller\":\"evaluacionController\"}');

/*Table structure for table `mod_panel_campos` */

DROP TABLE IF EXISTS `mod_panel_campos`;

CREATE TABLE `mod_panel_campos` (
  `id_panel_campo` int(11) NOT NULL AUTO_INCREMENT,
  `id_panel` int(11) NOT NULL,
  `id_campo` int(11) NOT NULL,
  PRIMARY KEY (`id_panel_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=1131 DEFAULT CHARSET=utf8;

/*Data for the table `mod_panel_campos` */

insert  into `mod_panel_campos`(`id_panel_campo`,`id_panel`,`id_campo`) values (1,1,1),(2,1,2),(3,1,3),(4,1,4),(5,1,5),(6,1,8),(7,1,9),(9,1,11),(10,1,12),(11,1,13),(15,2,18),(16,2,19),(17,2,20),(18,2,21),(19,2,22),(20,2,23),(21,2,24),(22,2,25),(23,2,26),(24,2,27),(25,2,28),(26,2,29),(27,3,1),(28,3,2),(29,3,3),(30,3,4),(31,3,5),(32,3,6),(33,3,7),(34,3,8),(35,4,1),(36,4,2),(37,4,3),(38,4,4),(39,4,5),(40,4,6),(41,4,7),(42,4,8),(43,5,44),(44,5,45),(45,5,46),(46,5,47),(47,5,48),(48,5,49),(49,5,50),(50,5,51),(51,5,52),(52,5,53),(53,5,54),(54,5,55),(55,5,56),(56,5,57),(57,5,58),(58,5,59),(59,5,60),(60,5,61),(61,5,62),(62,5,63),(63,5,64),(64,5,65),(65,5,66),(66,5,67),(67,6,68),(68,6,69),(69,6,70),(70,6,71),(71,6,72),(72,6,73),(73,6,74),(74,6,75),(75,6,76),(76,7,77),(77,8,78),(78,9,79),(79,9,80),(80,9,81),(81,10,82),(82,10,83),(83,10,84),(84,10,85),(85,10,86),(86,10,87),(87,10,88),(88,11,48),(89,11,49),(90,11,50),(91,11,51),(92,6,0),(93,11,21),(94,11,46),(95,11,33),(96,11,22),(97,11,20),(98,11,19),(99,11,18),(100,11,23),(101,11,24),(102,11,26),(103,11,27),(104,11,28),(105,12,48),(106,12,49),(107,12,50),(108,12,51),(110,12,46),(111,12,33),(112,12,28),(113,12,18),(114,12,19),(115,12,20),(116,12,22),(117,12,23),(118,12,24),(119,12,26),(120,12,27),(121,12,21),(122,13,2),(123,13,3),(124,13,1),(125,13,4),(126,13,5),(127,13,6),(128,13,7),(129,13,8),(130,13,9),(131,13,10),(132,13,11),(133,13,12),(134,13,13),(135,13,14),(136,13,15),(137,13,16),(138,13,17),(139,14,18),(140,14,19),(141,14,20),(142,14,21),(143,14,22),(144,14,23),(145,14,24),(146,14,25),(147,14,26),(148,14,27),(149,14,28),(150,14,29),(151,14,30),(152,14,31),(153,14,32),(154,14,33),(155,14,34),(156,30,35),(157,30,36),(158,30,37),(159,30,38),(160,30,39),(161,30,40),(162,30,41),(163,16,2),(164,16,3),(165,16,1),(166,16,4),(167,16,5),(168,16,6),(169,16,7),(170,16,8),(171,16,9),(172,16,10),(173,16,11),(174,16,12),(175,16,13),(176,16,14),(177,16,15),(178,16,16),(179,16,34),(180,32,35),(181,32,36),(182,32,37),(183,32,38),(184,32,39),(185,32,40),(186,32,41),(187,13,42),(188,13,43),(189,16,42),(190,16,43),(192,6,89),(193,6,90),(194,6,91),(195,6,92),(196,6,93),(198,25,95),(199,25,96),(200,25,97),(201,25,99),(202,6,100),(226,20,68),(227,20,69),(228,20,70),(229,20,71),(230,20,72),(231,20,73),(232,20,74),(233,20,75),(234,20,76),(235,20,89),(236,20,90),(237,20,91),(238,20,92),(239,20,93),(241,27,95),(242,27,96),(243,27,97),(244,27,99),(245,20,100),(246,19,44),(247,19,45),(248,19,46),(249,19,47),(250,19,48),(251,19,49),(252,19,50),(253,19,51),(254,19,52),(255,19,53),(256,19,54),(257,19,55),(258,19,56),(259,19,57),(260,19,58),(261,19,59),(262,19,60),(263,19,61),(264,19,62),(265,19,63),(266,19,64),(267,19,65),(268,19,66),(269,19,67),(270,24,82),(271,24,83),(272,24,84),(273,24,85),(274,24,86),(275,24,87),(276,24,88),(277,21,77),(278,22,78),(279,23,79),(280,23,81),(281,23,80),(282,0,101),(283,25,102),(284,27,102),(285,26,103),(286,26,104),(288,26,106),(289,26,107),(290,26,108),(291,26,109),(292,28,103),(293,28,104),(294,28,105),(295,28,106),(296,28,107),(297,28,108),(298,28,109),(299,25,112),(300,25,113),(301,27,112),(302,27,113),(303,25,115),(304,27,115),(305,25,116),(306,27,116),(307,25,117),(308,27,117),(310,15,119),(311,15,120),(312,15,121),(313,15,122),(314,15,123),(315,15,124),(316,15,125),(317,15,126),(318,15,127),(319,15,128),(320,29,129),(321,29,130),(322,29,131),(323,29,132),(324,29,133),(325,29,134),(326,18,119),(327,18,120),(328,18,121),(329,18,122),(330,18,123),(331,18,124),(332,18,125),(333,18,126),(334,18,127),(335,18,128),(336,31,129),(337,31,130),(338,31,131),(339,31,132),(340,31,133),(341,31,134),(342,16,135),(343,18,136),(344,26,137),(345,25,138),(346,20,139),(347,11,70),(348,33,1),(349,33,2),(350,33,3),(351,33,4),(352,33,5),(353,33,6),(354,33,7),(355,33,8),(356,33,9),(357,33,10),(358,33,11),(359,33,12),(360,33,13),(361,33,14),(362,33,15),(363,33,0),(364,34,1),(365,34,2),(366,34,3),(367,34,4),(368,34,5),(369,34,6),(370,34,7),(371,34,8),(372,34,9),(373,33,16),(374,33,17),(375,33,18),(376,33,19),(377,33,20),(378,33,21),(379,35,1),(380,35,2),(381,35,3),(382,35,4),(383,35,5),(384,35,6),(385,35,7),(386,35,8),(387,35,9),(388,35,10),(389,35,11),(390,35,12),(391,35,13),(392,35,14),(393,35,15),(394,35,16),(395,35,17),(396,35,18),(397,35,19),(398,35,20),(399,35,21),(400,35,22),(401,11,71),(402,36,1),(403,36,2),(404,36,3),(405,36,4),(406,36,5),(407,36,6),(408,36,7),(409,36,8),(410,36,9),(411,37,1),(412,37,2),(413,37,3),(414,37,4),(415,37,5),(416,37,6),(417,37,7),(418,37,8),(419,37,9),(420,37,10),(421,37,11),(422,37,12),(423,37,13),(424,37,14),(425,37,15),(426,37,16),(427,37,17),(428,37,18),(429,37,19),(430,37,20),(431,37,21),(432,37,22),(433,37,23),(434,35,24),(435,37,24),(436,38,1),(437,38,2),(438,38,3),(439,38,4),(440,38,5),(441,38,6),(442,38,7),(443,38,8),(444,38,9),(445,38,10),(446,38,11),(447,38,12),(448,38,13),(449,38,14),(450,38,15),(451,38,0),(452,38,16),(453,38,17),(454,38,18),(455,38,19),(456,38,20),(457,38,21),(458,38,22),(459,39,1),(460,39,2),(461,39,3),(462,39,4),(463,39,5),(464,39,6),(465,39,7),(466,39,8),(467,41,1),(468,41,13),(469,41,2),(470,41,3),(471,41,4),(472,41,5),(473,41,6),(474,41,7),(475,41,12),(476,41,11),(477,41,10),(478,41,9),(479,41,8),(480,40,1),(481,40,3),(482,40,4),(483,40,5),(484,40,6),(485,40,7),(486,40,8),(487,40,2),(488,40,13),(489,40,12),(490,40,11),(491,40,10),(492,40,9),(493,42,1),(494,42,2),(495,42,3),(496,42,4),(497,42,5),(498,42,6),(499,42,7),(500,42,8),(501,43,1),(502,43,13),(503,43,2),(504,43,3),(505,43,4),(506,43,5),(507,43,6),(508,43,7),(509,43,12),(510,43,11),(511,43,10),(512,43,9),(513,43,8),(514,40,14),(515,40,15),(516,41,14),(517,41,15),(518,43,14),(519,43,15),(520,12,70),(521,32,141),(523,30,141),(524,24,141),(525,5,143),(526,19,143),(527,35,26),(528,37,26),(529,11,72),(530,11,73),(531,11,74),(532,34,10),(533,36,10),(534,35,27),(535,37,27),(536,34,11),(537,36,11),(538,12,71),(539,12,72),(540,12,73),(541,12,74),(542,3,9),(543,4,9),(544,35,25),(545,35,28),(546,35,29),(547,37,25),(548,37,28),(549,37,29),(550,33,23),(551,33,24),(552,33,25),(553,38,23),(554,38,24),(555,38,25),(556,33,26),(557,33,27),(558,38,26),(559,38,27),(562,44,1),(563,44,2),(564,44,3),(565,44,4),(566,44,5),(567,44,6),(568,44,7),(569,44,8),(570,44,9),(571,44,10),(572,44,11),(573,44,12),(574,45,1),(575,45,2),(576,45,3),(577,45,4),(578,45,5),(579,45,6),(580,45,7),(581,45,8),(582,45,9),(583,45,10),(584,45,11),(585,45,12),(586,5,144),(587,6,145),(588,6,146),(589,6,147),(590,6,148),(591,27,149),(592,27,150),(595,19,144),(596,20,145),(597,20,146),(598,20,147),(599,20,148),(600,25,149),(601,25,150),(602,15,153),(603,15,154),(604,18,153),(605,18,154),(606,35,30),(607,41,17),(608,43,17),(609,46,1),(610,46,2),(611,46,3),(612,46,4),(613,46,5),(614,46,6),(615,46,7),(616,46,8),(617,46,9),(618,46,10),(619,46,11),(620,46,12),(621,46,13),(622,46,14),(623,46,15),(624,46,16),(625,46,17),(626,46,18),(627,46,19),(628,46,20),(629,46,21),(630,46,22),(631,46,23),(632,46,24),(633,46,25),(634,46,26),(635,47,27),(636,47,28),(637,47,29),(638,47,30),(639,47,31),(640,47,32),(641,47,33),(642,47,34),(643,47,35),(644,47,36),(645,47,37),(646,47,38),(647,47,39),(648,47,40),(649,47,41),(650,47,42),(651,48,1),(652,48,2),(653,48,3),(654,48,4),(655,48,5),(657,48,7),(658,48,8),(659,48,9),(660,48,10),(661,48,11),(662,48,12),(663,48,13),(664,48,14),(665,48,15),(666,48,16),(667,48,17),(668,48,18),(669,49,1),(670,49,2),(671,49,3),(672,49,4),(673,49,5),(674,49,10),(675,49,7),(676,49,8),(677,49,9),(678,49,11),(679,49,12),(680,49,13),(681,49,14),(682,49,15),(683,49,16),(684,49,17),(685,49,18),(686,49,19),(687,50,1),(688,50,2),(689,50,3),(690,50,4),(691,50,5),(692,50,6),(693,50,7),(694,50,8),(695,50,9),(696,50,10),(697,50,11),(698,50,12),(699,50,13),(700,50,14),(701,50,15),(702,51,1),(703,51,2),(704,51,3),(705,51,4),(706,51,5),(707,51,6),(708,51,7),(709,51,8),(710,51,9),(711,51,10),(712,51,11),(713,51,12),(714,51,13),(715,51,14),(716,51,15),(717,51,16),(718,51,17),(719,51,18),(720,51,19),(721,51,20),(722,52,1),(723,52,2),(724,52,3),(725,52,4),(726,52,5),(727,52,7),(728,52,8),(729,52,9),(731,52,11),(732,52,12),(733,52,13),(734,52,14),(735,52,15),(736,52,16),(737,52,17),(738,52,18),(739,52,19),(740,52,20),(741,52,21),(742,52,22),(743,52,23),(744,52,24),(745,52,25),(747,52,26),(748,52,27),(749,54,43),(750,54,44),(751,54,45),(752,54,46),(753,54,47),(754,54,48),(755,54,49),(756,54,50),(757,54,51),(758,54,52),(759,54,53),(760,54,54),(761,54,55),(762,54,56),(763,54,57),(764,54,58),(765,54,59),(766,54,60),(767,54,61),(768,54,62),(769,54,63),(770,54,64),(771,54,65),(772,54,66),(773,54,67),(774,54,68),(775,54,69),(776,54,70),(777,54,71),(778,54,72),(779,54,73),(780,54,74),(781,54,75),(782,54,76),(783,54,77),(784,54,78),(785,54,79),(786,54,80),(787,54,81),(788,54,82),(789,54,83),(790,54,84),(791,54,85),(792,54,86),(793,54,87),(794,54,88),(795,54,89),(796,54,90),(797,54,91),(798,54,92),(799,55,1),(800,55,2),(801,55,3),(805,46,93),(806,47,94),(807,47,95),(808,54,96),(809,54,97),(810,54,98),(811,53,1),(812,53,2),(813,53,3),(814,53,4),(815,53,5),(816,53,7),(817,53,8),(818,53,9),(819,53,11),(820,53,12),(821,53,13),(822,53,14),(823,53,15),(824,53,16),(825,53,17),(826,53,18),(827,53,19),(828,53,20),(829,53,21),(830,53,22),(831,53,23),(832,53,24),(833,53,25),(834,53,26),(835,53,27),(836,58,1),(837,58,2),(838,58,3),(839,58,4),(840,58,5),(841,58,6),(842,58,7),(843,58,8),(844,58,9),(845,58,10),(846,58,11),(847,58,12),(848,57,1),(849,57,2),(850,57,3),(851,57,4),(852,57,5),(853,57,6),(854,57,7),(855,57,8),(856,57,9),(857,69,4),(858,69,8),(859,69,7),(860,69,6),(861,69,5),(862,69,9),(863,69,3),(864,69,2),(865,69,1),(866,67,11),(867,67,3),(868,67,2),(869,68,1),(870,68,4),(871,68,5),(872,68,6),(873,68,7),(874,68,8),(875,68,9),(876,68,10),(877,68,13),(878,68,14),(879,68,15),(880,68,16),(881,59,1),(882,59,2),(883,59,3),(884,59,4),(885,59,5),(886,59,6),(887,59,7),(888,59,8),(889,59,9),(890,59,10),(891,59,11),(892,59,12),(893,59,13),(894,60,14),(895,60,15),(896,60,16),(897,60,17),(898,61,18),(899,61,19),(900,61,20),(901,61,21),(902,62,22),(903,62,23),(904,62,24),(905,62,25),(906,63,1),(907,63,2),(908,63,3),(909,63,4),(910,63,5),(911,63,6),(912,63,7),(913,63,8),(914,63,9),(915,63,10),(916,63,11),(917,63,12),(918,63,13),(919,64,14),(920,64,15),(921,64,16),(922,64,17),(923,65,18),(924,65,19),(925,65,20),(926,65,21),(927,66,22),(928,66,23),(929,66,24),(930,66,25),(931,58,13),(932,72,1),(933,72,2),(934,72,3),(935,72,4),(936,72,5),(937,72,6),(938,72,7),(939,72,8),(940,73,1),(941,73,2),(942,73,4),(943,73,5),(944,73,6),(945,73,7),(946,73,8),(947,73,3),(948,47,99),(949,47,100),(950,47,101),(951,47,102),(952,54,103),(953,47,104),(954,74,1),(955,74,2),(956,74,3),(957,74,4),(958,74,5),(959,74,6),(960,74,7),(961,74,8),(962,74,9),(963,74,10),(964,74,11),(965,74,12),(966,74,13),(967,74,14),(968,74,15),(969,74,16),(970,74,17),(971,75,1),(972,75,2),(973,75,4),(974,75,5),(975,75,6),(976,75,7),(977,75,8),(978,75,9),(979,75,10),(980,75,11),(981,75,12),(982,70,1),(983,70,2),(984,70,3),(985,70,4),(986,70,5),(987,70,6),(988,70,7),(989,70,8),(990,70,9),(991,70,10),(992,70,11),(993,70,12),(994,70,13),(995,70,14),(996,70,15),(997,70,16),(998,70,17),(999,71,1),(1000,71,2),(1001,71,3),(1002,71,4),(1003,71,5),(1004,71,6),(1005,71,7),(1006,71,8),(1007,71,9),(1008,71,10),(1009,71,11),(1010,71,12),(1011,71,13),(1012,71,14),(1013,71,15),(1014,71,16),(1015,71,17),(1016,54,105),(1017,54,106),(1018,54,107),(1019,54,108),(1020,54,109),(1021,54,0),(1022,76,1),(1023,76,2),(1024,76,3),(1025,76,4),(1026,76,5),(1027,76,6),(1028,76,7),(1029,76,8),(1030,76,9),(1031,77,110),(1032,77,4),(1033,77,5),(1034,77,6),(1035,77,7),(1036,77,8),(1037,78,1),(1038,78,2),(1039,78,3),(1040,78,4),(1041,78,5),(1042,78,6),(1043,78,7),(1044,78,8),(1045,78,9),(1046,78,10),(1047,78,11),(1048,78,12),(1049,78,13),(1050,78,14),(1051,78,15),(1052,78,16),(1053,78,17),(1054,78,18),(1055,79,1),(1056,79,2),(1057,79,3),(1058,79,4),(1059,79,5),(1060,79,6),(1061,79,7),(1062,79,8),(1063,79,9),(1064,79,10),(1065,79,11),(1066,79,12),(1067,79,13),(1068,79,14),(1069,79,15),(1070,79,16),(1071,79,17),(1072,79,18),(1073,77,111),(1074,77,11),(1075,77,112),(1076,77,15),(1077,77,113),(1078,77,18),(1079,77,114),(1080,77,115),(1081,77,116),(1082,77,117),(1083,77,118),(1084,77,119),(1085,77,120),(1086,77,121),(1087,77,122),(1088,77,123),(1089,77,124),(1090,77,125),(1091,77,126),(1092,77,127),(1093,77,128),(1094,77,129),(1095,77,130),(1096,77,131),(1097,77,132),(1098,77,133),(1099,77,134),(1100,77,135),(1101,77,136),(1102,77,137),(1103,77,138),(1104,77,139),(1105,77,140),(1106,77,141),(1107,77,142),(1108,77,143),(1109,46,144),(1110,46,145),(1111,46,146),(1112,46,147),(1113,46,148),(1114,46,149),(1115,46,150),(1116,47,151),(1117,70,18),(1118,71,18),(1119,80,152),(1120,80,153),(1121,80,154),(1122,80,155),(1123,80,156),(1124,80,157),(1125,80,158),(1126,80,159),(1127,80,160),(1128,80,161),(1129,80,162),(1130,80,163);

/*Table structure for table `mod_paneles` */

DROP TABLE IF EXISTS `mod_paneles`;

CREATE TABLE `mod_paneles` (
  `id_panel` int(11) NOT NULL AUTO_INCREMENT,
  `id_formulario` int(11) NOT NULL,
  `panel` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_panel`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

/*Data for the table `mod_paneles` */

insert  into `mod_paneles`(`id_panel`,`id_formulario`,`panel`) values (1,1,'Acutalice sus Datos Personales'),(2,2,'Acutalice sus Datos Personales'),(3,3,'Crear Cliente Potencial'),(4,4,'Editar Cliente Potencial'),(5,5,''),(6,6,''),(7,7,'Salud'),(8,7,'Intereses'),(9,7,'Pasatiempos'),(10,8,''),(11,9,'Datos del Contacto'),(12,10,'Editar Contacto'),(13,11,'Datos del Cliente'),(14,11,'Datos del Contacto'),(15,12,''),(16,13,'Datos del Cliente'),(17,13,'Datos del Contacto'),(18,14,''),(19,15,''),(20,16,''),(21,17,'Salud'),(22,17,'Intereses'),(23,17,'Pasatiempos'),(24,18,''),(25,19,''),(26,20,''),(27,21,''),(28,22,''),(29,23,NULL),(30,24,NULL),(31,25,NULL),(32,26,NULL),(33,27,NULL),(34,28,NULL),(35,29,NULL),(36,30,NULL),(37,31,NULL),(38,32,NULL),(39,33,NULL),(40,34,'Actividad'),(41,35,'Actividad'),(42,36,NULL),(43,37,'Actividad'),(44,38,'Caso'),(45,39,'Caso'),(46,40,''),(47,40,'Datos Profesionales'),(48,41,'Datos generales del pedido'),(49,42,'Datos generales del pedido'),(50,43,'Datos generales del proveedor'),(51,44,'Datos generales del proveedor'),(52,45,'Datos de orden de compra'),(53,46,'Datos de orden de compra'),(54,47,''),(55,48,NULL),(56,49,NULL),(57,50,'Datos de la aseguradora'),(58,51,'Datos Generales'),(59,52,'Datos generales del item'),(60,52,'Precios de venta'),(61,52,'Cuentas'),(62,52,'Impuestos'),(63,53,'Datos generales del item'),(64,53,'Precios de venta'),(65,53,'Cuentas'),(66,53,'Impuestos'),(67,54,'Datos de entrada peri√≥dica'),(68,54,'Detalles de la entrada manual'),(69,55,'Datos de la aseguradora'),(70,56,'Datos generales del ajuste'),(71,57,'Datos generales del ajuste'),(72,58,'Datos generales de la bodega'),(73,59,'Datos generales de la bodega'),(74,60,'Datos generales de la entrada'),(75,61,'Datos Generales de Comision'),(76,62,'Datos Genereales de Planilla'),(77,63,'Declaraci√≥n Jurada de Deducciones Personales / 82'),(78,64,'Datos generales del traslado'),(79,65,'Datos generales del traslado'),(80,66,NULL);

/*Table structure for table `mod_pestanas` */

DROP TABLE IF EXISTS `mod_pestanas`;

CREATE TABLE `mod_pestanas` (
  `id_pestana` int(11) NOT NULL AUTO_INCREMENT,
  `id_vista` int(11) NOT NULL,
  `pestana` varchar(200) DEFAULT NULL,
  `estado` enum('activo','desactivado') NOT NULL DEFAULT 'activo',
  `orden` int(10) DEFAULT NULL,
  PRIMARY KEY (`id_pestana`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8;

/*Data for the table `mod_pestanas` */

insert  into `mod_pestanas`(`id_pestana`,`id_vista`,`pestana`,`estado`,`orden`) values (1,1,'General','activo',NULL),(2,2,'General','activo',NULL),(3,3,'General','activo',NULL),(4,4,'General','activo',NULL),(5,5,'Datos del Cliente','activo',NULL),(6,5,'Informaci√≥n Personal','activo',NULL),(7,5,'Informaci√≥n Personal','desactivado',NULL),(8,5,'Agentes','desactivado',NULL),(9,6,'Contacto','activo',NULL),(10,7,'Contacto','activo',NULL),(11,8,'Datos del Cliente','activo',NULL),(12,8,'Propiedades','activo',NULL),(13,9,'Datos del Cliente','activo',NULL),(14,9,'Propiedades','activo',NULL),(15,10,'Datos del Cliente','activo',NULL),(16,10,'Informaci√≥n Personal','activo',NULL),(17,10,'Informaci√≥n Personal','desactivado',NULL),(18,10,'Agentes','desactivado',NULL),(19,10,'Propiedades','activo',NULL),(20,10,'P√≥lizas','desactivado',NULL),(21,5,'Propiedades','activo',NULL),(22,5,'P√≥lizas','desactivado',NULL),(23,8,'P&oacute;lizas','desactivado',NULL),(24,8,'Agentes','desactivado',NULL),(25,9,'P&oacute;lizas','desactivado',NULL),(26,9,'Agentes','desactivado',NULL),(27,11,'Datos de la Oportunidad','activo',NULL),(28,12,'General','activo',NULL),(29,13,'General','activo',NULL),(30,14,'General','activo',NULL),(31,15,'General','activo',NULL),(32,16,'Datos de la Oportunidad','activo',NULL),(33,17,'General','activo',NULL),(34,18,'Actividad','activo',NULL),(35,19,'Actividad','activo',NULL),(36,20,'General','activo',NULL),(37,21,'Actividad','activo',NULL),(38,22,'Caso','activo',NULL),(39,23,'Caso','activo',NULL),(40,24,'Datos Generales','activo',1),(41,25,'Datos generales del pedido','activo',NULL),(42,26,'Datos generales del pedido','activo',NULL),(43,27,'Datos generales del proveedor','activo',NULL),(44,28,'Datos generales del proveedor','activo',NULL),(45,29,'Datos de orden de compra','activo',NULL),(46,30,'Datos de orden de compra','activo',NULL),(47,24,'Datos Espec&iacute;ficos','activo',2),(48,31,'Centro Contable','activo',NULL),(50,33,'Crear Aseguradora','activo',NULL),(51,34,'Datos de entrada peripodica','activo',NULL),(52,35,'Datos generales del item','activo',NULL),(53,36,'Datos generales del item','activo',NULL),(54,37,'Datos entrada peri√≥dica','activo',NULL),(55,38,'Editar Aseguradora','activo',NULL),(56,39,'Datos generales del ajuste','activo',NULL),(57,40,'Datos generales del ajuste','activo',NULL),(58,41,'Datos generales de la bodega','activo',NULL),(59,42,'Datos generales de la bodega','activo',NULL),(60,43,'Datos generales de la entrada','activo',NULL),(61,44,'Datos Generales Comision','activo',NULL),(62,45,'Datos Generales de Planilla','activo',NULL),(63,24,'Formulario 82','activo',3),(64,46,'Datos generales del traslado','activo',NULL),(65,47,'Datos generales del traslado','activo',NULL),(66,48,'General','activo',NULL);

/*Table structure for table `mod_tipo_campos` */

DROP TABLE IF EXISTS `mod_tipo_campos`;

CREATE TABLE `mod_tipo_campos` (
  `id_tipo_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_tipo_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

/*Data for the table `mod_tipo_campos` */

insert  into `mod_tipo_campos`(`id_tipo_campo`,`nombre`) values (1,'button'),(2,'checkbox'),(3,'date'),(4,'email'),(5,'file'),(6,'file_imagen'),(7,'hidden'),(8,'link'),(9,'number'),(10,'password'),(11,'radio'),(12,'select'),(13,'submit'),(14,'text'),(15,'textarea'),(16,'select-checkbox-addon'),(17,'select-checkbox-button-addon'),(18,'relate'),(19,'tagsinput'),(20,'fecha'),(21,'select-right-button-addon'),(22,'input-left-addon'),(23,'input-right-addon'),(24,'google_maps'),(25,'relate-right-button'),(26,'groups-radio-button'),(27,'head_title'),(28,'p-text'),(29,'input-select'),(30,'input-daterange'),(31,'firma'),(32,'button-cancelar'),(33,'button-guardar');

/*Table structure for table `mod_vistas` */

DROP TABLE IF EXISTS `mod_vistas`;

CREATE TABLE `mod_vistas` (
  `id_vista` int(11) NOT NULL AUTO_INCREMENT,
  `id_modulo` int(11) NOT NULL,
  `vista` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id_vista`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

/*Data for the table `mod_vistas` */

insert  into `mod_vistas`(`id_vista`,`id_modulo`,`vista`) values (1,7,'ver_usuario'),(2,7,'ver_usuario_admin'),(3,5,'crear_cliente_potencial'),(4,5,'editar_cliente_potencial'),(5,2,'crear_cliente_natural'),(6,3,'crear_contacto'),(7,3,'editar_contacto'),(8,2,'crear_cliente_juridico'),(9,2,'editar_cliente_juridico'),(10,2,'editar_cliente_natural'),(11,8,'crear_oportunidad'),(12,9,'crear_proyecto'),(13,10,'crear_propiedad'),(14,9,'ver_proyecto'),(15,10,'ver_propiedad'),(16,8,'editar_oportunidad'),(17,11,'crear_agente'),(18,16,'crear_actividad'),(19,16,'crear_actividad_modal'),(20,11,'editar_agente'),(21,16,'editar_actividad'),(22,15,'crear_caso'),(23,15,'editar_caso'),(24,18,'crear'),(25,19,'crear'),(26,19,'editar'),(27,22,'crear'),(28,22,'editar'),(29,23,'crear'),(30,23,'editar'),(31,21,'listar_centros_contables'),(33,24,'crear'),(34,25,'crear'),(35,26,'crear'),(36,26,'editar'),(37,27,'crear'),(38,24,'editar'),(39,28,'crear'),(40,28,'editar'),(41,29,'crear'),(42,29,'editar'),(43,31,'editar'),(44,25,'editar'),(45,20,'crear'),(46,32,'crear'),(47,32,'editar'),(48,18,'evaluacion');

/*Table structure for table `modulos` */

DROP TABLE IF EXISTS `modulos`;

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `icono` varchar(45) DEFAULT NULL,
  `controlador` varchar(45) DEFAULT NULL,
  `version` varchar(45) DEFAULT NULL,
  `tipo` enum('core','addon') DEFAULT NULL,
  `grupo` varchar(45) DEFAULT NULL,
  `agrupador` text,
  `menu` text,
  `estado` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `nombre_modulo_uq` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=196 DEFAULT CHARSET=utf8;

/*Data for the table `modulos` */

insert  into `modulos`(`id`,`nombre`,`descripcion`,`icono`,`controlador`,`version`,`tipo`,`grupo`,`agrupador`,`menu`,`estado`) values (1,'Administracion','Modulo de administracion.','','configuracion','1.0','core','Administracion de Sistema','','',1),(2,'Clientes','Modulo para Administracion de clientes.','fa-line-chart','clientes','1.0','addon','Ventas','{\"nombre\":[\"Ventas\"]}','{\"link\":{\"nombre\":\"Clientes\",\"url\":\"clientes\\/listar\"}}',1),(3,'Contactos','Modulo para Administracion de contactos.','fa-briefcase','contactos','1.0','addon','Ventas','{\"nombre\":[\"Ventas\"]}','',1),(4,'Administrador de Modulos','Permite desinstalar o instalar modulos a la herramienta.','fa-archive','modulos','1.1','core','Administracion de Sistema','','',1),(5,'Clientes Potenciales','Modulo para Administracion de clientes potenciales.','fa-briefcase','clientes_potenciales','1.0','addon','Ventas','{\"nombre\":[\"Ventas\"]}','{\"link\":{\"nombre\":\"Clientes Potenciales\",\"url\":\"clientes_potenciales\\/listar-clientes-potenciales\"}}',1),(6,'Roles','Administar roles.','fa-key','roles','1.3','core','Administracion de Sistema','','',1),(7,'Usuarios','Modulo para administrar los usuarios del sistema.','fa-group','usuarios','1.0','core','Administracion de Sistema','','',1),(8,'Oportunidades','Modulo para crear y administrar oportunidades de ventas.','fa-dollar','oportunidades','1.0','addon','Ventas','{\"nombre\":[\"Ventas\"]}','{\"link\":{\"nombre\":\"Oportunidades\",\"url\":\"oportunidades\\/listar-oportunidades\"}}',1),(9,'Proyectos','Modulo para Administracion los Proyectos.','fa-cubes','proyectos','1.0','addon','Inventario','','',1),(10,'Propiedades','Modulo para Administracion Propiedades.','fa-home','propiedades','1.0','addon','Inventario','','',1),(11,'Agentes','Modulo para Administracion de agentes.','fa-child','agentes','1.0','addon','Proveedores','','',1),(13,'Documentos','Modulo para Administracion de Documentos.','fa-copy','documentos','1.0','addon','Administrador De Documentos','','',1),(14,'Tablero de Indicadores','Modulo para ver los indicadores de los distintos modulos.','fa-tachometer','tablero_indicadores','1.0','addon','Dashboard','','',1),(15,'Casos','Modulo para Administraci√≥n de Casos','fa-flag-o','casos','1.0','addon','Administrador de Casos','','',1),(16,'Actividades','M&oacute;dulo para Administraci&oacute;n de Actividades','fa-tty','actividades','1.0','addon','Administrador de Actividades','{\"nombre\":[\"Ventas\",\"Inventario\"]}','',1),(17,'Notificaciones','Modulo para Habiliar y deshabilitar Notificaciones del Sistema.','fa-bell','notificaciones','1.0','addon','Administraci√≥n de Notificaciones','','',1),(18,'Colaboradores','Modulo para Administracion de colaboradores.','fa-child','colaboradores','1.0','addon','Colaboradores','{\"nombre\":[\"Recursos Humanos\"]}','{\"link\":[{\"nombre\":\"Colaboradores\",\"url\":\"colaboradores\\/listar\"},{\"nombre\":\"Configuracion\",\"url\":\"colaboradores\\/configuracion\"}]}',1),(19,'Pedidos','Modulo para Administracion de Pedidos.','fa-building','pedidos','1.0','addon','Pedidos','{\"nombre\":[\"Compras\"]}','{\"link\":[[{\"nombre\":\"Pedidos\",\"url\":\"pedidos\\/listar\"}]]}',1),(20,'Planilla','Modulo para la Administracion de Planilla.','fa-child','planilla','1.0','addon','Planilla','{\"nombre\":[\"Planilla\"]}','{\"link\":[{\"nombre\":\"Planilla\",\"url\":\"planilla\\/listar\"},{\"nombre\":\"Configuracion\",\"url\":\"planilla\\/configuracion\"}]}',1),(21,'Contabilidad','Modulo para Administracion de contabilidad.','fa-calculator','contabilidad','1.0','addon','Contabilidad','{\"nombre\":[\"Contabilidad\"]}','{\"link\":[{\"nombre\":\"Plan Contable\",\"url\":\"contabilidad\\/listar\"},{\"nombre\":\"Centros Contables\",\"url\":\"contabilidad\\/listar_centros_contables\"},{\"nombre\":\"Configuraci\\u00f3n\",\"url\":\"contabilidad\\/configuracion\"}]}',1),(22,'Proveedores','Modulo para Administracion de Proveedores.','fa-building','proveedores','1.0','addon','Proveedores','{\"nombre\":[\"Compras\"]}','',1),(23,'Ordenes','Modulo para Administracion de Ordenes.','fa-building','ordenes','1.0','addon','Ordenes','{\"nombre\":[\"Compras\"]}','',1),(24,'Aseguradoras','Modulo para administrar las aseguradoras.','fa-book','aseguradoras','1.0','addon','Aseguradoras','{\"nombre\":[\"Seguros\"]}','{\"link\":{\"nombre\":\"Aseguradoras\",\"url\":\"aseguradoras\\/listar\"}}',1),(25,'Comisiones','Modulo para Administracion de comisiones.','fa-child','comisiones','1.0','addon','Comisiones','{\"nombre\":[\"Planilla\"]}','{\"link\":{\"nombre\":\"Comisiones\",\"url\":\"comisiones\\/listar\"}}',1),(26,'Inventarios','Modulo para Administracion de Inventarios.','fa-cubes','inventarios','1.0','addon','Inventarios','{\"nombre\":[\"Inventarios\"]}','{\"link\":{\"nombre\":\"Inventarios\",\"url\":\"inventarios\\/listar\"}}',1),(27,'Entrada Manuales','Modulo para Administracion de entrada manual.','fa-calculator','entrada_manual','1.0','addon','Entrada Manuales','{\"nombre\":[\"Contabilidad\"]}','{\"link\":{\"nombre\":\"Entrada Manuales\",\"url\":\"entrada_manual\\/listar\"}}',1),(28,'Ajustes','Modulo para Administracion de Ajustes de Inventario.','fa-cubes','ajustes','1.0','addon','Ajustes','{\"nombre\":[\"Inventarios\"]}','{\"link\":{\"nombre\":\"Ajustes\",\"url\":\"ajustes\\/listar\"}}',1),(29,'Bodegas','Modulo para Administracion de Bodegas.','fa-cubes','bodegas','1.0','addon','Bodegas','{\"nombre\":[\"Inventarios\"]}','{\"link\":{\"nombre\":\"Bodegas\",\"url\":\"bodegas\\/listar\"}}',1),(30,'Presupuestos','Modulo para Administracion de Presupuestos.','fa-calculator','presupuesto','1.0','addon','Contabilidad','{\"nombre\":[\"Contabilidad\"]}','{\"link\":{\"nombre\":\"Presupuesto\",\"url\":\"presupuesto\\/listar\"}}',1),(31,'Entradas','Modulo para Administracion de Entradas de Inventario.','fa-cubes','entradas','1.0','addon','Entradas','{\"nombre\":[\"Inventarios\"]}','{\"link\":{\"nombre\":\"Entradas\",\"url\":\"entradas\\/listar\"}}',1),(32,'Traslados','Modulo para Administracion de Traslados de Inventario.','fa-cubes','traslados','1.0','addon','Traslados','{\"nombre\":[\"Inventarios\"]}','{\"link\":{\"nombre\":\"Traslados\",\"url\":\"traslados\\/listar\"}}',1),(33,'Catalogos de Inventario','Modulo para Administracion de Catalogos de Inventario.','fa-cubes','catalogos_inventario','1.0','addon','Catalogos','{\"nombre\":[\"Inventarios\"]}','{\"link\":{\"nombre\":\"Catalogos\",\"url\":\"catalogos_inventario\\/listar\"}}',1),(37,'Cotizaciones','Modulo para Administracion de cotizaciones.','fa-line-chart','cotizaciones','1.0','addon','Cotizaciones','{\"nombre\":[\"Ventas\"]}','{\"link\":{\"nombre\":\"Cotizaciones\",\"url\":\"cotizaciones\\/listar\"}}',1),(165,'Ordenes de Venta','Modulo para Administracion de Ordenes de Venta.','fa-line-chart','ordenes_ventas','1.0','addon','Ordenes de Venta','{\"nombre\":[\"Ventas\"]}','{\"link\":{\"nombre\":\"Ordenes de Ventas\",\"url\":\"ordenes_ventas\\/listar\"}}',1),(192,'Salidas','Modulo para Administracion de Salidas de Inventario.','fa-cubes','salidas','1.0','addon','Salidas','{\"nombre\":[\"Inventarios\"]}','{\"link\":{\"nombre\":\"Salidas\",\"url\":\"salidas\\/listar\"}}',1),(194,'Consumos','Modulo para Administracion de Consumos de Inventario.','fa-cubes','consumos','1.0','addon','Consumos','{\"nombre\":[\"Inventarios\"]}','{\"link\":{\"nombre\":\"Consumos\",\"url\":\"consumos\\/listar\"}}',1),(195,'Facturas','Modulo para Administracion de facturas.','fa-line-chart','facturas','1.0','addon','Facturas','{\"nombre\":[\"Ventas\"]}','{\"link\":{\"nombre\":\"Facturas\",\"url\":\"facturas\\/listar\"}}',1);

/*Table structure for table `modulos_has_recursos` */

DROP TABLE IF EXISTS `modulos_has_recursos`;

CREATE TABLE `modulos_has_recursos` (
  `modulo_id` int(11) NOT NULL,
  `recurso_id` int(11) NOT NULL,
  PRIMARY KEY (`modulo_id`,`recurso_id`),
  KEY `fk_modulos_has_recursos_recursos1_idx` (`recurso_id`),
  KEY `fk_modulos_has_recursos_modulos1_idx` (`modulo_id`),
  CONSTRAINT `fk_modulos_has_recursos_modulos1` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_modulos_has_recursos_recursos1` FOREIGN KEY (`recurso_id`) REFERENCES `recursos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `modulos_has_recursos` */

insert  into `modulos_has_recursos`(`modulo_id`,`recurso_id`) values (6,1),(9,2),(14,3),(14,4),(16,5),(16,6),(16,7),(16,8),(16,9),(16,10),(16,11),(7,12),(7,13),(7,14),(7,15),(7,16),(7,17);

/*Table structure for table `ord_orden_items` */

DROP TABLE IF EXISTS `ord_orden_items`;

CREATE TABLE `ord_orden_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_orden` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `unidad` int(11) NOT NULL,
  `precio_unidad` decimal(10,2) NOT NULL,
  `uuid_impuesto` binary(16) NOT NULL,
  `descuento` decimal(10,2) NOT NULL,
  `cuenta` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

/*Data for the table `ord_orden_items` */

insert  into `ord_orden_items`(`id`,`id_orden`,`id_item`,`cantidad`,`unidad`,`precio_unidad`,`uuid_impuesto`,`descuento`,`cuenta`) values (40,13,1,2,2,'10.00','ÂåõÔøΩV	ÔøΩÔøΩÔ','10.00',1),(41,13,2,2,1,'10.00','ÂåõÔøΩV	ÔøΩÔøΩÔ','10.00',1),(42,14,1,10,1,'10.00','ÂåõÔøΩV	ÔøΩÔøΩÔ','10.00',1),(43,14,2,10,2,'10.00','ÂåõÔøΩgÔøΩÔøΩ','10.00',1),(44,15,1,200,2,'375.95','Âé> ñ£ï„ºvNT†','1000.00',1),(45,15,2,25,2,'159.99','Âé> ñ£ï„ºvNT†','0.00',1),(46,16,1,200,2,'375.75','Âé>ﬂÂ ï„ºvNT†','10.00',1),(47,16,2,25,2,'156.65','Âé>ﬂÂ ï„ºvNT†','0.00',1),(48,17,1,33,1,'2.50','ÔøΩrD>jﬂµÔøΩbf','5.00',1);

/*Table structure for table `ord_orden_venta_catalogo` */

DROP TABLE IF EXISTS `ord_orden_venta_catalogo`;

CREATE TABLE `ord_orden_venta_catalogo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  `tipo` varchar(200) NOT NULL,
  `orden` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `ord_orden_venta_catalogo` */

insert  into `ord_orden_venta_catalogo`(`id`,`key`,`valor`,`etiqueta`,`tipo`,`orden`) values (1,'1','Al contado','al_contado','termino_pago',1),(2,'2','7 d√≠as','7_dias','termino_pago',2),(3,'3','14 d√≠as','14_dias','termino_pago',3),(4,'4','30 d√≠as','30_dias','termino_pago',4),(5,'5','60 d√≠as','60_dias','termino_pago',5),(6,'6','90 d√≠as','90_dias','termino_pago',6),(7,'7','Abierta','abierta','etapa',7),(9,'9','Por Facturar','por_facturar','etapa',9),(10,'10','Facturada','facturada','etapa',10),(11,'11','Anulada','anulada','etapa',11);

/*Table structure for table `ord_ordenes` */

DROP TABLE IF EXISTS `ord_ordenes`;

CREATE TABLE `ord_ordenes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid_orden` binary(16) NOT NULL,
  `referencia` varchar(140) NOT NULL,
  `numero` int(8) unsigned zerofill NOT NULL,
  `uuid_centro` binary(16) NOT NULL,
  `uuid_lugar` binary(16) NOT NULL,
  `uuid_pedido` binary(16) NOT NULL,
  `uuid_proveedor` binary(16) NOT NULL,
  `credito` tinyint(1) NOT NULL,
  `dias` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `creado_por` int(11) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ord_ordenes_unique1` (`numero`,`id_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `ord_ordenes` */

insert  into `ord_ordenes`(`id`,`uuid_orden`,`referencia`,`numero`,`uuid_centro`,`uuid_lugar`,`uuid_pedido`,`uuid_proveedor`,`credito`,`dias`,`id_estado`,`creado_por`,`fecha_creacion`,`id_empresa`,`monto`) values (13,'Âéj¶x‘ï„ºvNT†','REF01',00000001,'ÂtKÜ\\·<ÆKƒ⁄&K≥','uuid_lugar\0\0\0\0\0\0','Â~N*ÁñÁï„ºvNT†','ÂÑ∆Æºï„ºvNT†',1,10,2,1,'2015-11-18 00:00:00',1,'38.52'),(14,'Âéö4LVï„ºvNT†','REF02',00000002,'Âçı‚⁄⁄ÿï„ºvNT†','...\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','ÂÑ∆TßæÀï„ºvNT†',1,30,2,1,'2015-11-18 00:00:00',1,'96.30'),(15,'Âè†R@Ô\\ï„ºvNT†','gradas',00000003,'Âé> ñ£ï„ºvNT†','...\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','Â~t˙∞»⁄ï„ºvNT†',1,45,1,1,'2015-11-20 00:00:00',1,'76687.70'),(16,'Âèæh÷9ï„ºvNT†','test 19-nov',00000004,'Âé>ﬂÂ ï„ºvNT†','...\0\0\0\0\0\0\0\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','Â~t˙∞ \Zï„ºvNT†',0,0,1,1,'2015-11-20 00:00:00',1,'76559.84'),(17,'Âè∆Ï!V4ï„ºvNT†','20-nov',00000005,'ÂçtÔÃ¯µ˛bfäa•','uuid_lugar\0\0\0\0\0\0','\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','Â~t˙∞ƒIï„ºvNT†',0,0,1,1,'2015-11-20 00:00:00',1,'83.87');

/*Table structure for table `ord_ordenes_campos` */

DROP TABLE IF EXISTS `ord_ordenes_campos`;

CREATE TABLE `ord_ordenes_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

/*Data for the table `ord_ordenes_campos` */

insert  into `ord_ordenes_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'fecha_creacion','Fecha',0,14,'activo','{\"readonly\":\"\"}','','div','',1,'','0000-00-00 00:00:00',2),(2,'centro','Centro Contable',0,18,'activo','{\"class\":\"chosen\"}','','div','cen_centros',1,'','0000-00-00 00:00:00',6),(3,'lugar','Recibir en',0,18,'activo','{\"class\":\"chosen\"}','','div','lug_lugares',1,'','0000-00-00 00:00:00',4),(4,'referencia','Referencia',0,14,'activo','','','div','',0,'','0000-00-00 00:00:00',12),(5,'numero','N√∫mero',0,14,'activo','{\"readonly\":\"\"}','','div','',1,'','0000-00-00 00:00:00',7),(7,'estado','Estado',0,12,'activo','{\"class\":\"chosen\",\"disabled\":\"true\"}','','div','',1,'','0000-00-00 00:00:00',11),(8,'item','Item',0,18,'activo','{\"class\":\"chosen item\"}','items','tabla-dinamica','inv_items',1,'','0000-00-00 00:00:00',16),(9,'descripcion','Descripci√≥n',0,14,'activo','{\"class\":\"form-control descripcion\",\"readonly\":\"true\"}','items','tabla-dinamica','inv_items',0,'','0000-00-00 00:00:00',18),(11,'cuenta','Cuenta',0,12,'activo','{\"class\":\"chosen cuenta\",\"disabled\":\"true\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',31),(12,'cantidad','Cantidad',0,14,'activo','{\"class\":\"form-control cantidad\",\"disabled\":\"true\",\"style\":\"width:70px\",\"data-inputmask\":\"\'mask\':\'9{1,4}\',\'greedy\':false\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',24),(13,'unidad','Unidad',0,12,'activo','{\"class\":\"chosen unidad\",\"disabled\":\"\"}','items','tabla-dinamica','uni_unidades',1,'','0000-00-00 00:00:00',26),(14,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;',0,1,'activo','{\"class\":\"btn btn-default btn-block eliminarBtn\",\"disabled\":\"true\"}','items','tabla-dinamica','',0,'','0000-00-00 00:00:00',35),(15,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;',0,1,'activo','{\"class\":\"btn btn-default btn-block agregarBtn\",\"disabled\":\"true\"}','items','tabla-dinamica','',0,'','2015-05-14 08:10:53',34),(16,'id_pedido_item','',0,7,'activo','{\"class\":\"id_pedido_item\"}','items','tabla-dinamica','inv_items',0,'','2015-05-15 13:16:04',36),(17,'cancelarOrden','Cancelar',0,8,'activo','{\"class\":\"btn btn-default pull-right\"}','items','div','',0,'ordenes/listar','0000-00-00 00:00:00',38),(18,'guardarOrden','Guardar',0,13,'activo','{\"class\":\"btn btn-primary btn-block\",\"style\":\"width:90px;\"}','','div','',0,'','0000-00-00 00:00:00',40),(19,'proveedor','Proveedor',0,18,'activo','{\"class\":\"chosen\"}','','div','pro_proveedores',1,'','0000-00-00 00:00:00',3),(20,'pedido','Pedido',0,18,'activo','{\"class\":\"chosen pedido\"}','','div','ped_pedidos',0,'','0000-00-00 00:00:00',8),(21,'precio_unidad','Precio unidad',0,22,'activo','{\"style\":\"width:100px;\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control precio_unidad\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,2}\',\'greedy\':false\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',27),(22,'impuesto','Impuesto',0,18,'activo','{\"class\":\"chosen impuesto\"}','items','tabla-dinamica','cen_centros',1,'','0000-00-00 00:00:00',28),(23,'descuento','Descuento',0,14,'activo','{\"class\":\"form-control descuento\",\"data-inputmask\":\"\'mask\':\'9{0,2}.{0,1}9{0,2}\',\'greedy\':false\"}','items','tabla-dinamica','',0,'','0000-00-00 00:00:00',29),(24,'precio_total','Precio total',0,22,'activo','{\"disabled\":\"true\",\"style\":\"width:100px;\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control precio_total\",\"data-inputmask\":\"\'mask\':\'9{0,8}.{0,1}9{0,2}\',\'greedy\':false\"}','items','tabla-dinamica','',0,'','0000-00-00 00:00:00',33),(25,'credito','',0,11,'activo','','','div','',1,'','0000-00-00 00:00:00',13),(26,'dias','D&iacute;as',3,14,'activo','{\"style\":\"width:100px;\",\"class\":\"form-control dias\",\"data-inputmask\":\"\'mask\':\'9{0,3}\',\'greedy\':false\"}','','div','',0,'','0000-00-00 00:00:00',14),(27,'monto','',0,7,'activo','{\"class\":\"monto\"}','','div','',0,'','0000-00-00 00:00:00',15);

/*Table structure for table `ord_ordenes_cat` */

DROP TABLE IF EXISTS `ord_ordenes_cat`;

CREATE TABLE `ord_ordenes_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `ord_ordenes_cat` */

insert  into `ord_ordenes_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,7,'','Por aprobar'),(2,7,'','Abierta'),(3,7,'','Facturada parcial'),(4,7,'','Facturada completo'),(5,7,'','Anulada'),(6,25,'1','Contado'),(7,25,'2','Cr&eacute;dito');

/*Table structure for table `ord_ordenes_ventas_items` */

DROP TABLE IF EXISTS `ord_ordenes_ventas_items`;

CREATE TABLE `ord_ordenes_ventas_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_orden_venta_item` binary(16) DEFAULT NULL,
  `orden_venta_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `unidad_id` int(11) DEFAULT NULL,
  `precio_unidad` decimal(10,2) DEFAULT NULL,
  `impuesto_id` int(11) DEFAULT NULL,
  `descuento` decimal(10,2) DEFAULT NULL,
  `cuenta_id` int(11) DEFAULT NULL,
  `precio_total` decimal(10,2) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `index_unique` (`id`,`orden_venta_id`,`empresa_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

/*Data for the table `ord_ordenes_ventas_items` */

insert  into `ord_ordenes_ventas_items`(`id`,`uuid_orden_venta_item`,`orden_venta_id`,`item_id`,`empresa_id`,`cantidad`,`unidad_id`,`precio_unidad`,`impuesto_id`,`descuento`,`cuenta_id`,`precio_total`,`updated_at`,`created_at`) values (1,'Â√éÃN¥jÇlbfäa•',2,3,1,2,2,'34.00',1,'0.00',4,'71.40','2016-01-25 13:10:26','2016-01-25 13:09:50'),(2,'Â√ë.]ˆ¶Çlbfäa•',3,3,1,6,2,'34.00',1,'0.00',4,'214.20','2016-01-25 13:26:54','2016-01-25 13:26:54'),(3,'Â√ë.`ÜÇlbfäa•',3,6,1,1,2,'49.99',1,'0.00',4,'52.49','2016-01-25 13:26:54','2016-01-25 13:26:54'),(4,'Â√î:4±Çlbfäa•',4,4,1,1,1,'31.04',1,'0.00',4,'32.59','2016-01-25 13:48:42','2016-01-25 13:48:42'),(5,'Â√î:>©ÑÇlbfäa•',4,5,1,3,1,'299.99',1,'0.00',4,'944.97','2016-01-25 13:48:42','2016-01-25 13:48:42'),(6,'Â√ôëÃÇlbfäa•',2,4,1,2,1,'31.04',1,'0.00',4,'65.18','2016-01-25 14:26:56','2016-01-25 14:26:56'),(8,'Âƒ.€!æÇlbfäa•',5,3,1,1,2,'87.00',1,'0.00',4,'91.35','2016-01-26 08:16:11','2016-01-26 08:16:11'),(9,'Âƒ4~Íh¢Çlbfäa•',6,3,1,1,2,'34.00',1,'0.00',4,'35.70','2016-01-26 08:55:57','2016-01-26 08:55:57'),(10,'Âƒ4~Í·÷Çlbfäa•',6,3,1,3,2,'34.00',1,'0.00',4,'107.10','2016-01-26 08:55:57','2016-01-26 08:55:57'),(11,'Â∆°MœÕåÇlbfäa•',7,3,1,1,2,'87.00',1,'0.00',4,'91.35','2016-01-29 10:59:52','2016-01-29 10:59:52'),(12,'Â∆°M‘9\"Çlbfäa•',7,4,1,1,1,'31.05',1,'0.00',4,'32.60','2016-01-29 10:59:52','2016-01-29 10:59:52');

/*Table structure for table `ord_ventas` */

DROP TABLE IF EXISTS `ord_ventas`;

CREATE TABLE `ord_ventas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid_venta` binary(16) DEFAULT NULL,
  `referencia` varchar(140) DEFAULT NULL,
  `codigo` varchar(100) DEFAULT NULL,
  `centro_contable_id` int(11) DEFAULT NULL,
  `bodega_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `cotizacion_id` int(11) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `fecha_desde` datetime DEFAULT NULL,
  `fecha_hasta` datetime DEFAULT NULL,
  `comentario` text,
  `termino_pago` varchar(100) DEFAULT NULL,
  `fecha_termino_pago` datetime DEFAULT NULL,
  `item_precio_id` int(11) DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `impuestos` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `ord_ventas` */

insert  into `ord_ventas`(`id`,`uuid_venta`,`referencia`,`codigo`,`centro_contable_id`,`bodega_id`,`cliente_id`,`created_by`,`created_at`,`updated_at`,`empresa_id`,`cotizacion_id`,`estado`,`fecha_desde`,`fecha_hasta`,`comentario`,`termino_pago`,`fecha_termino_pago`,`item_precio_id`,`subtotal`,`impuestos`,`total`) values (2,'Â√éÃL—Çlbfäa•',NULL,'SO16000001',9,3,3,28,'2016-01-25 13:09:50','2016-01-27 16:01:13',1,NULL,'por_facturar','2016-01-25 09:37:23','2016-01-31 09:37:23',NULL,'30_dias','2016-02-24 09:37:23',4,'130.08','6.50','136.58'),(3,'Â√ë.[m†Çlbfäa•',NULL,'SO16000002',2,1,6,28,'2016-01-25 13:26:54','2016-01-25 13:26:54',1,3,'abierta','2016-02-02 13:26:54','2016-02-17 13:26:54',NULL,'14_dias','2016-02-16 13:26:54',4,'253.99','12.70','266.69'),(4,'Â√î:1¥;Çlbfäa•',NULL,'SO16000003',9,1,4,28,'2016-01-25 13:48:42','2016-01-25 13:51:37',1,2,'abierta','2016-02-01 13:51:37','2016-02-24 13:51:37','cotizacion usada para orden de venta editado','14_dias','2016-02-15 13:51:37',4,'931.01','46.55','977.56'),(5,'Âƒ.ŸÇlbfäa•',NULL,'SO16000004',9,3,5,28,'2016-01-26 08:16:11','2016-01-26 08:16:11',1,4,'abierta','2016-01-26 08:16:11','2016-02-05 08:16:11',NULL,'14_dias','2016-02-09 08:16:11',3,'87.00','4.35','91.35'),(6,'Âƒ4~È√6Çlbfäa•',NULL,'SO16000005',11,2,1,28,'2016-01-26 08:55:57','2016-01-26 13:25:18',1,1,'por_facturar','2016-01-18 08:55:57','2016-01-29 08:55:57',NULL,'14_dias','2016-02-01 08:55:57',4,'136.00','6.80','142.80'),(7,'Â∆°MÕ7ïÇlbfäa•',NULL,'SO16000006',9,5,3,28,'2016-01-29 10:59:52','2016-01-29 10:59:52',1,5,'abierta','2016-01-29 10:59:52','2016-02-12 10:59:52',NULL,'14_dias','2016-02-12 10:59:52',3,'118.05','5.90','123.95');

/*Table structure for table `organizacion` */

DROP TABLE IF EXISTS `organizacion`;

CREATE TABLE `organizacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_organizacion` binary(16) DEFAULT NULL,
  `nombre` varchar(240) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `organizacion` */

insert  into `organizacion`(`id`,`uuid_organizacion`,`nombre`,`created_at`,`updated_at`) values (1,'Â|!|[ö±bfäa•','Organization Name','2015-10-26 15:38:16','2015-10-26 15:38:16'),(2,'Â|!ÕÁë/±bfäa•','organizacion 1','2015-10-26 15:40:33','2015-10-26 15:40:33'),(3,'Â~~dÄ±bfäa•','los arcos','2015-10-29 15:45:52','2015-10-29 15:45:52');

/*Table structure for table `ped_pedidos` */

DROP TABLE IF EXISTS `ped_pedidos`;

CREATE TABLE `ped_pedidos` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid_pedido` binary(16) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `referencia` varchar(140) NOT NULL,
  `numero` int(8) unsigned zerofill NOT NULL,
  `uuid_centro` binary(16) NOT NULL,
  `uuid_lugar` binary(16) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `creado_por` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `ped_pedidos` */

insert  into `ped_pedidos`(`id`,`uuid_pedido`,`fecha_creacion`,`referencia`,`numero`,`uuid_centro`,`uuid_lugar`,`id_tipo`,`id_estado`,`creado_por`,`id_empresa`) values (3,'Âyò5‘Û@ï„ºvNT†','2015-10-23 00:00:00','REF0101011',00000001,'ÂtKÜ\\·<ÆKƒ⁄&K≥','uuid_lugar\0\0\0\0\0\0',0,5,1,1),(5,'Ây…à	{Ìï„ºvNT†','2015-10-23 00:00:00','REFERENCIA 03',00000003,'ÂtKÜ\\·<ÆKƒ⁄&K≥','uuid_lugar\0\0\0\0\0\0',0,1,1,1),(7,'Â~M≥Z”ƒï„ºvNT†','2015-10-29 00:00:00','Referencia00017',00000005,'ÂtKÜ\\·<ÆKƒ⁄&K≥','...\0\0\0\0\0\0\0\0\0\0\0\0\0',0,1,1,1),(8,'Â~N*ÁñÁï„ºvNT†','2015-10-29 00:00:00','Referencia00015',00000006,'ÂtKÜ\\·<ÆKƒ⁄&K≥','uuid_lugar\0\0\0\0\0\0',0,3,1,1),(9,'Â~y8ßí™ï„ºvNT†','2015-10-29 00:00:00','adfadafa',00000007,'ÂtKÜ\\·<ÆKƒ⁄&K≥','...\0\0\0\0\0\0\0\0\0\0\0\0\0',0,1,1,1),(10,'Â$µ3ƒï„ºvNT†','2015-10-30 00:00:00','Auto',00000008,'ÂtKÜ\\·<ÆKƒ⁄&K≥','uuid_lugar\0\0\0\0\0\0',0,1,1,1);

/*Table structure for table `ped_pedidos_campos` */

DROP TABLE IF EXISTS `ped_pedidos_campos`;

CREATE TABLE `ped_pedidos_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

/*Data for the table `ped_pedidos_campos` */

insert  into `ped_pedidos_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'pedido_fecha_creacion','Fecha',0,14,'activo','{\"readonly\":\"\"}',NULL,'div',NULL,1,NULL,'0000-00-00 00:00:00',2),(2,'pedido_centro','Centro Contable',0,18,'activo','{\"class\":\"chosen\"}',NULL,'div','cen_centros',1,NULL,'0000-00-00 00:00:00',4),(3,'pedido_lugar','Recibir en',0,18,'activo','{\"class\":\"chosen\"}',NULL,'div','lug_lugares',1,NULL,'0000-00-00 00:00:00',6),(4,'pedido_referencia','Referencia',0,14,'activo',NULL,NULL,'div',NULL,0,NULL,'0000-00-00 00:00:00',8),(5,'pedido_numero','N√∫mero de pedido',0,14,'activo','{\"readonly\":\"\"}',NULL,'div','',1,NULL,'0000-00-00 00:00:00',10),(7,'pedido_estado','Estado',0,12,'activo','{\"class\":\"chosen\",\"disabled\":\"true\"}',NULL,'div',NULL,1,NULL,'0000-00-00 00:00:00',14),(8,'pedido_item','Item',0,18,'activo','{\"class\":\"chosen item\"}','items','tabla-dinamica','inv_items',1,NULL,'0000-00-00 00:00:00',16),(9,'pedido_descripcion','Descripci√≥n',0,14,'activo','{\"class\":\"form-control descripcion\",\"readonly\":\"true\"}','items','tabla-dinamica','inv_items',0,NULL,'0000-00-00 00:00:00',18),(10,'pedido_observacion','Observaci√≥n',0,14,'activo','{\"class\":\"form-control observacion\",\"disabled\":\"true\"}','items','tabla-dinamica','',0,NULL,'0000-00-00 00:00:00',20),(11,'pedido_cuenta','Cuenta',0,12,'activo','{\"class\":\"chosen cuenta\",\"disabled\":\"true\"}','items','tabla-dinamica','',1,NULL,'0000-00-00 00:00:00',22),(12,'pedido_cantidad','Cantidad',0,14,'activo','{\"class\":\"form-control cantidad\",\"disabled\":\"true\",\"style\":\"width:70px\",\"data-inputmask\":\"\'mask\':\'9{1,4}\',\'greedy\':false\"}','items','tabla-dinamica','',1,NULL,'0000-00-00 00:00:00',24),(13,'pedido_unidad','Unidad',0,12,'activo','{\"class\":\"chosen unidad\",\"disabled\":\"\"}','items','tabla-dinamica','uni_unidades',1,NULL,'0000-00-00 00:00:00',26),(14,'eliminarBtn','&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;&lt;/span&gt;',0,1,'activo','{\"class\":\"btn btn-default btn-block eliminarBtn\",\"disabled\":\"true\"}','items','tabla-dinamica','',0,'','0000-00-00 00:00:00',30),(15,'agregarBtn','&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md&quot;&gt;&amp;nbsp;&lt;/span&gt;',0,1,'activo','{\"class\":\"btn btn-default btn-block agregarBtn\",\"disabled\":\"true\"}','items','tabla-dinamica',NULL,0,NULL,'2015-05-14 08:10:53',28),(16,'id_pedido_item','',0,7,'activo','{\"class\":\"id_pedido_item\"}','items','tabla-dinamica','inv_items',0,NULL,'2015-05-15 13:16:04',32),(17,'cancelarPedido','Cancelar',0,8,'activo','{\"class\":\"btn btn-default pull-right\"}','items','div','',0,'pedidos/listar','0000-00-00 00:00:00',34),(18,'guardarPedido','Guardar',0,13,'activo','{\"class\":\"btn btn-primary btn-block\",\"style\":\"width:90px;\"}','','div','',0,'','0000-00-00 00:00:00',36);

/*Table structure for table `ped_pedidos_cat` */

DROP TABLE IF EXISTS `ped_pedidos_cat`;

CREATE TABLE `ped_pedidos_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `ped_pedidos_cat` */

insert  into `ped_pedidos_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,7,'','Pendiente'),(2,7,'','Abierto'),(3,7,'','Parcial'),(4,7,'','En orden'),(5,7,'','Completado'),(6,7,'','Anulado');

/*Table structure for table `ped_pedidos_inv_items` */

DROP TABLE IF EXISTS `ped_pedidos_inv_items`;

CREATE TABLE `ped_pedidos_inv_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `observacion` varchar(100) NOT NULL,
  `cuenta` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `unidad` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

/*Data for the table `ped_pedidos_inv_items` */

insert  into `ped_pedidos_inv_items`(`id`,`id_pedido`,`id_item`,`observacion`,`cuenta`,`cantidad`,`unidad`) values (15,3,1,'OBSERVACION',1,10,1),(16,3,2,'OBSERVACION 2',1,11,2),(17,4,1,'OBS1',1,10,1),(18,4,2,'OBS2',1,11,1),(19,4,2,'OBS3',1,12,2),(20,5,1,'OBSERVACION',1,10,1),(21,5,2,'',1,1,2),(22,6,2,'observaci√≥n 1',1,12,1),(27,6,2,'Observacion 4.- ',1,12,1),(29,8,1,'Pruebas RB',1,10,2),(30,8,2,'Pruebas RB',1,5,1),(31,7,1,'Pruebas RB',1,1,1),(32,9,1,'para pruebas',1,2,2),(33,10,1,'Flota 2015',1,5,2),(34,11,1,'Muebles para Sala de Ventas',1,15,2);

/*Table structure for table `permisos` */

DROP TABLE IF EXISTS `permisos`;

CREATE TABLE `permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) DEFAULT NULL,
  `recurso_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_permisos_recursos1` (`recurso_id`),
  CONSTRAINT `fk_permisos_recursos1` FOREIGN KEY (`recurso_id`) REFERENCES `recursos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1697 DEFAULT CHARSET=utf8;

/*Data for the table `permisos` */

insert  into `permisos`(`id`,`nombre`,`recurso_id`) values (1,'acceso',1),(2,'listar-agentes__exportar',1),(3,'acceso',2),(4,'listar-agentes__exportar',2),(5,'acceso',3),(6,'listar-agentes__exportar',3),(7,'acceso',4),(8,'listar-agentes__exportar',4),(9,'acceso',5),(10,'listar-casos__eliminar',5),(11,'acceso',6),(12,'listar-casos__eliminar',6),(13,'acceso',7),(14,'listar-casos__eliminar',7),(15,'acceso',8),(16,'listar-casos__eliminar',8),(17,'acceso',9),(18,'listar-casos__eliminar',9),(19,'acceso',10),(20,'listar-oportunidades__exportar_oportunidades',10),(21,'acceso',11),(22,'listar-oportunidades__exportar_oportunidades',11),(23,'acceso',12),(24,'listar-oportunidades__exportar_oportunidades',12),(25,'acceso',13),(26,'listar-oportunidades__exportar_oportunidades',13),(27,'acceso',14),(28,'listar-oportunidades__exportar_oportunidades',14),(29,'acceso',15),(30,'listar-oportunidades__exportar_oportunidades',15),(31,'acceso',16),(32,'acceso',17),(33,'listar-propiedades__eliminar',17),(34,'editar-propiedades_comisionCompartida',17),(35,'acceso',18),(36,'listar-propiedades__eliminar',18),(37,'editar-propiedades_comisionCompartida',18),(38,'acceso',19),(39,'listar-propiedades__eliminar',19),(40,'editar-propiedades_comisionCompartida',19),(41,'acceso',20),(42,'listar-propiedades__eliminar',20),(43,'editar-propiedades_comisionCompartida',20),(44,'acceso',21),(45,'listar-clientes__agregar_campana_mercadeo',21),(46,'acceso',22),(47,'listar-clientes__agregar_campana_mercadeo',22),(48,'acceso',23),(49,'listar-clientes__agregar_campana_mercadeo',23),(50,'acceso',24),(51,'listar-clientes__agregar_campana_mercadeo',24),(52,'acceso',25),(53,'listar-clientes__agregar_campana_mercadeo',25),(54,'acceso',26),(55,'listar-contactos__agregar_campana_mercadeo',26),(56,'acceso',27),(57,'listar-contactos__agregar_campana_mercadeo',27),(58,'acceso',28),(59,'listar-contactos__agregar_campana_mercadeo',28),(60,'acceso',29),(61,'acceso',30),(62,'listar-pedidos__exportar',30),(63,'acceso',31),(64,'listar-pedidos__exportar',31),(65,'acceso',32),(66,'listar-pedidos__exportar',32),(67,'acceso',33),(68,'listar-actividades__exportar_actividad',33),(69,'acceso',34),(70,'listar-actividades__exportar_actividad',34),(71,'acceso',35),(72,'listar-actividades__exportar_actividad',35),(73,'acceso',36),(74,'listar-actividades__exportar_actividad',36),(75,'acceso',37),(76,'listar-actividades__exportar_actividad',37),(77,'acceso',38),(78,'listar-clientes-potenciales__exportar',38),(79,'acceso',39),(80,'listar-clientes-potenciales__exportar',39),(81,'acceso',40),(82,'listar-clientes-potenciales__exportar',40),(83,'acceso',41),(84,'listar-clientes-potenciales__exportar',41),(85,'acceso',42),(86,'listar-documentos__eliminar_documentos',42),(87,'acceso',43),(88,'listar-documentos__eliminar_documentos',43),(89,'acceso',44),(90,'listar-documentos__eliminar_documentos',44),(91,'acceso',45),(92,'listar-proyectos__eliminar',45),(93,'acceso',46),(94,'listar-proyectos__eliminar',46),(95,'acceso',47),(96,'listar-proyectos__eliminar',47),(97,'acceso',48),(98,'listar-proyectos__eliminar',48),(99,'acceso',49),(102,'acceso',52),(103,'acceso',53),(104,'acceso',54),(105,'listar-agentes__exportar',54),(106,'acceso',55),(107,'listar-agentes__exportar',55),(108,'acceso',56),(109,'listar-agentes__exportar',56),(110,'acceso',57),(111,'listar-roles__crear_rol',57),(112,'acceso',58),(113,'listar-roles__crear_rol',58),(114,'acceso',59),(115,'listar-roles__crear_rol',59),(116,'acceso',60),(117,'listar',60),(118,'editar',60),(119,'crear',60),(120,'acceso',61),(121,'listar',61),(122,'editar',61),(123,'crear',61),(124,'acceso',62),(125,'listar',62),(126,'editar',62),(127,'crear',62),(128,'acceso',63),(129,'listar',63),(130,'editar',63),(131,'crear',63),(132,'acceso',64),(133,'listar-usuarios__desactivarActivarUsuario',64),(134,'acceso',65),(135,'listar-usuarios__desactivarActivarUsuario',65),(136,'acceso',66),(137,'listar-usuarios__desactivarActivarUsuario',66),(138,'acceso',67),(139,'listar-usuarios__desactivarActivarUsuario',67),(140,'acceso',68),(141,'listar-usuarios__desactivarActivarUsuario',68),(142,'acceso',69),(143,'listar-usuarios__desactivarActivarUsuario',69),(144,'acceso',70),(145,'listar-usuarios__desactivarActivarUsuario',70),(146,'acceso',71),(147,'listar-usuarios__desactivarActivarUsuario',71),(148,'acceso',72),(149,'listar-usuarios__desactivarActivarUsuario',72),(150,'acceso',73),(151,'listar-usuarios__desactivarActivarUsuario',73),(152,'acceso',74),(153,'listar__exportar',74),(154,'acceso',75),(155,'listar__exportar',75),(156,'acceso',76),(157,'listar__exportar',76),(158,'acceso',77),(159,'listar__exportar',77),(160,'acceso',78),(161,'listar__exportar',78),(162,'acceso',79),(163,'listar__exportar',79),(164,'acceso',80),(165,'listar-usuarios__desactivarActivarUsuario',80),(166,'acceso',81),(167,'listar-usuarios__desactivarActivarUsuario',81),(168,'acceso',82),(169,'listar-usuarios__desactivarActivarUsuario',82),(170,'acceso',83),(171,'acceso',85),(172,'listar-agentes__exportar',85),(173,'acceso',84),(174,'listar-agentes__exportar',83),(175,'listar-agentes__exportar',84),(176,'acceso',86),(177,'acceso',86),(178,'listar__exportar',86),(179,'acceso',87),(180,'listar__exportar',87),(181,'acceso',88),(182,'listar__exportar',88),(183,'acceso',89),(184,'acceso',90),(185,'listar__exportar',89),(186,'listar__exportar',90),(187,'acceso',91),(188,'listar-usuarios__desactivarActivarUsuario',91),(189,'acceso',92),(190,'acceso',93),(191,'acceso',94),(192,'listar',94),(193,'editar',94),(194,'crear',94),(195,'acceso',95),(196,'listar__exportar',95),(197,'acceso',96),(198,'listar__exportar',96),(199,'acceso',97),(200,'acceso',97),(201,'listar__exportar',97),(202,'acceso',98),(203,'acceso',99),(204,'acceso',100),(205,'listar-usuarios__desactivarActivarUsuario',100),(206,'acceso',101),(207,'listar-usuarios__desactivarActivarUsuario',101),(208,'acceso',102),(209,'listar-usuarios__desactivarActivarUsuario',102),(210,'acceso',103),(211,'acceso',103),(212,'listar',103),(213,'listar',103),(214,'editar',103),(215,'editar',103),(216,'crear',103),(217,'crear',103),(218,'acceso',104),(219,'acceso',104),(220,'acceso',105),(221,'acceso',104),(222,'acceso',104),(223,'listar',104),(224,'listar',105),(225,'listar',104),(226,'listar',104),(227,'editar',104),(228,'editar',104),(229,'listar',104),(230,'editar',105),(231,'crear',104),(232,'crear',105),(233,'editar',104),(234,'crear',104),(235,'editar',104),(236,'crear',104),(237,'crear',104),(238,'acceso',106),(239,'acceso',107),(240,'listar__exportar',106),(241,'listar__exportar',107),(242,'acceso',108),(243,'listar__exportar',108),(244,'acceso',109),(245,'acceso',110),(246,'listar__exportar',110),(247,'listar__exportar',109),(248,'acceso',111),(249,'acceso',111),(250,'listar__exportar',111),(251,'acceso',112),(252,'acceso',112),(253,'acceso',113),(254,'listar__exportar',112),(255,'listar__exportar',113),(256,'acceso',114),(257,'listar__exportar',114),(258,'acceso',115),(259,'listar__exportar',115),(260,'acceso',116),(261,'listar__exportar',116),(262,'acceso',117),(263,'listar__exportar',117),(264,'acceso',118),(265,'acceso',118),(266,'acceso',119),(267,'acceso',119),(268,'acceso',120),(269,'acceso',120),(270,'acceso',121),(271,'ver__editar',120),(272,'acceso',122),(273,'acceso',122),(274,'acceso',123),(275,'acceso',123),(276,'acceso',124),(277,'acceso',124),(278,'acceso',126),(279,'acceso',125),(280,'acceso',127),(281,'acceso',127),(282,'acceso',128),(283,'acceso',127),(284,'acceso',129),(285,'acceso',129),(286,'acceso',130),(287,'acceso',131),(288,'acceso',132),(289,'acceso',133),(290,'acceso',134),(291,'acceso',135),(292,'acceso',136),(293,'acceso',137),(294,'acceso',139),(295,'acceso',138),(296,'acceso',140),(297,'acceso',142),(298,'acceso',141),(299,'acceso',143),(300,'acceso',144),(301,'acceso',145),(302,'acceso',146),(303,'acceso',147),(304,'acceso',148),(305,'acceso',149),(306,'acceso',150),(307,'acceso',151),(308,'acceso',152),(309,'acceso',153),(310,'acceso',154),(311,'acceso',155),(312,'acceso',156),(313,'acceso',157),(314,'acceso',158),(315,'acceso',159),(316,'acceso',160),(317,'acceso',161),(318,'acceso',162),(319,'acceso',163),(320,'acceso',164),(321,'acceso',165),(322,'acceso',166),(323,'acceso',167),(324,'acceso',168),(325,'acceso',169),(326,'acceso',170),(327,'acceso',171),(328,'acceso',172),(329,'acceso',173),(330,'acceso',174),(331,'acceso',175),(332,'acceso',176),(333,'acceso',177),(334,'acceso',178),(335,'acceso',179),(336,'acceso',180),(337,'acceso',181),(338,'acceso',182),(339,'acceso',183),(340,'acceso',184),(341,'acceso',185),(342,'acceso',186),(343,'acceso',187),(344,'acceso',188),(345,'acceso',189),(346,'acceso',190),(347,'acceso',191),(348,'acceso',192),(349,'acceso',193),(350,'acceso',194),(351,'acceso',195),(352,'acceso',196),(353,'acceso',197),(354,'acceso',198),(355,'acceso',199),(356,'acceso',200),(357,'acceso',201),(358,'acceso',202),(359,'acceso',203),(360,'acceso',204),(361,'acceso',205),(362,'acceso',206),(363,'acceso',207),(364,'acceso',208),(365,'acceso',210),(366,'acceso',209),(367,'acceso',211),(368,'acceso',212),(369,'acceso',213),(370,'acceso',214),(371,'acceso',215),(372,'acceso',216),(373,'acceso',217),(374,'acceso',218),(375,'acceso',219),(376,'acceso',220),(377,'acceso',221),(378,'acceso',222),(379,'acceso',223),(380,'acceso',224),(381,'acceso',226),(382,'acceso',225),(383,'acceso',228),(384,'acceso',227),(385,'acceso',230),(386,'acceso',231),(387,'acceso',229),(388,'acceso',232),(389,'acceso',233),(390,'acceso',234),(391,'acceso',235),(392,'acceso',236),(393,'acceso',237),(394,'acceso',238),(395,'acceso',239),(396,'acceso',240),(397,'acceso',241),(398,'acceso',242),(399,'acceso',243),(400,'acceso',244),(401,'acceso',245),(402,'acceso',246),(403,'acceso',247),(404,'acceso',248),(405,'acceso',249),(406,'acceso',250),(407,'acceso',251),(408,'acceso',252),(409,'acceso',253),(410,'acceso',254),(411,'acceso',255),(412,'acceso',256),(413,'acceso',258),(414,'acceso',259),(415,'acceso',257),(416,'acceso',260),(417,'acceso',261),(418,'acceso',262),(419,'acceso',263),(420,'acceso',264),(421,'acceso',265),(422,'acceso',267),(423,'acceso',266),(424,'acceso',268),(425,'acceso',268),(426,'acceso',269),(427,'acceso',270),(428,'acceso',270),(429,'acceso',271),(430,'acceso',272),(431,'acceso',273),(432,'acceso',275),(433,'acceso',276),(434,'acceso',274),(435,'acceso',277),(436,'acceso',278),(437,'acceso',280),(438,'acceso',279),(439,'acceso',281),(440,'acceso',283),(441,'acceso',282),(442,'acceso',284),(443,'acceso',285),(444,'acceso',286),(445,'acceso',287),(446,'acceso',288),(447,'acceso',289),(448,'acceso',290),(449,'acceso',291),(450,'acceso',292),(451,'acceso',293),(452,'acceso',294),(453,'acceso',295),(454,'acceso',298),(455,'acceso',297),(456,'acceso',296),(457,'acceso',300),(458,'acceso',299),(459,'acceso',301),(460,'acceso',302),(461,'acceso',303),(462,'acceso',304),(463,'acceso',305),(464,'acceso',306),(465,'acceso',307),(466,'acceso',308),(467,'acceso',309),(468,'acceso',310),(469,'acceso',311),(470,'acceso',312),(471,'acceso',313),(472,'acceso',314),(473,'acceso',315),(474,'acceso',316),(475,'acceso',317),(476,'acceso',318),(477,'acceso',319),(478,'acceso',320),(479,'acceso',321),(480,'acceso',322),(481,'acceso',324),(482,'acceso',323),(483,'acceso',325),(484,'acceso',326),(485,'acceso',327),(486,'acceso',331),(487,'acceso',328),(488,'acceso',330),(489,'acceso',329),(490,'acceso',332),(491,'acceso',333),(492,'acceso',335),(493,'acceso',334),(494,'acceso',336),(495,'acceso',337),(496,'acceso',338),(497,'acceso',339),(498,'acceso',340),(499,'acceso',341),(500,'acceso',342),(501,'acceso',343),(502,'acceso',344),(503,'acceso',345),(504,'acceso',346),(505,'acceso',347),(506,'acceso',348),(507,'acceso',349),(508,'acceso',350),(509,'acceso',351),(510,'acceso',352),(511,'acceso',354),(512,'acceso',353),(513,'acceso',355),(514,'acceso',356),(515,'acceso',357),(516,'acceso',358),(517,'acceso',359),(518,'acceso',360),(519,'acceso',361),(520,'acceso',362),(521,'acceso',363),(522,'acceso',365),(523,'acceso',364),(524,'acceso',366),(525,'acceso',367),(526,'acceso',368),(527,'acceso',370),(528,'acceso',369),(529,'acceso',371),(530,'acceso',372),(531,'acceso',373),(532,'acceso',374),(533,'acceso',375),(534,'acceso',376),(535,'acceso',377),(536,'acceso',378),(537,'acceso',379),(538,'acceso',380),(539,'acceso',381),(540,'acceso',382),(541,'acceso',383),(542,'acceso',385),(543,'acceso',384),(544,'acceso',386),(545,'acceso',387),(546,'acceso',388),(547,'acceso',389),(548,'acceso',390),(549,'acceso',391),(550,'acceso',392),(551,'acceso',393),(552,'acceso',394),(553,'acceso',395),(554,'acceso',396),(555,'acceso',397),(556,'acceso',399),(557,'acceso',398),(558,'acceso',400),(559,'acceso',401),(560,'acceso',403),(561,'acceso',402),(562,'acceso',404),(563,'acceso',405),(564,'acceso',406),(565,'acceso',407),(566,'acceso',408),(567,'acceso',409),(568,'acceso',410),(569,'acceso',411),(570,'acceso',412),(571,'acceso',413),(572,'acceso',414),(573,'acceso',415),(574,'acceso',416),(575,'acceso',417),(576,'acceso',418),(577,'acceso',419),(578,'acceso',420),(579,'acceso',421),(580,'acceso',422),(581,'acceso',423),(582,'listar',21),(583,'crear',21),(584,'crear',21),(585,'acceso',426),(586,'acceso',427),(587,'acceso',425),(588,'acceso',424),(589,'acceso',428),(590,'acceso',429),(591,'acceso',430),(592,'acceso',431),(593,'acceso',433),(594,'acceso',432),(595,'acceso',434),(596,'acceso',435),(597,'acceso',438),(598,'acceso',436),(599,'acceso',439),(600,'acceso',437),(601,'acceso',440),(602,'acceso',441),(603,'acceso',443),(604,'acceso',442),(605,'acceso',444),(606,'acceso',445),(607,'acceso',446),(608,'acceso',447),(609,'acceso',448),(610,'acceso',449),(611,'acceso',450),(612,'acceso',451),(613,'acceso',452),(614,'acceso',453),(615,'acceso',454),(616,'acceso',455),(617,'acceso',456),(618,'acceso',457),(619,'acceso',458),(620,'acceso',460),(621,'acceso',461),(622,'acceso',459),(623,'acceso',462),(624,'acceso',463),(625,'acceso',465),(626,'acceso',464),(627,'acceso',466),(628,'acceso',467),(629,'acceso',468),(630,'acceso',469),(631,'acceso',470),(632,'acceso',471),(633,'acceso',472),(634,'acceso',473),(635,'acceso',474),(636,'acceso',475),(637,'acceso',476),(638,'acceso',477),(639,'acceso',478),(640,'acceso',479),(641,'acceso',480),(642,'acceso',481),(643,'acceso',482),(644,'acceso',483),(645,'acceso',485),(646,'acceso',484),(647,'acceso',486),(648,'acceso',487),(649,'acceso',488),(650,'acceso',489),(651,'acceso',490),(652,'acceso',491),(653,'acceso',493),(654,'acceso',492),(655,'acceso',494),(656,'acceso',495),(657,'acceso',496),(658,'acceso',497),(659,'acceso',498),(660,'acceso',499),(661,'acceso',500),(662,'acceso',501),(663,'acceso',502),(664,'acceso',503),(665,'acceso',504),(666,'acceso',505),(667,'acceso',506),(668,'acceso',507),(669,'acceso',508),(670,'acceso',509),(671,'acceso',509),(672,'acceso',510),(673,'acceso',511),(674,'acceso',511),(675,'acceso',511),(676,'acceso',511),(677,'acceso',512),(678,'acceso',512),(679,'acceso',513),(680,'acceso',513),(681,'acceso',513),(682,'acceso',513),(683,'acceso',514),(684,'acceso',515),(685,'acceso',514),(686,'acceso',518),(687,'acceso',516),(688,'acceso',517),(689,'acceso',519),(690,'acceso',519),(691,'acceso',520),(692,'acceso',520),(693,'acceso',520),(694,'acceso',521),(695,'acceso',522),(696,'listar',522),(697,'crear',522),(698,'acceso',523),(699,'listar',523),(700,'crear',523),(701,'acceso',524),(702,'listar',524),(703,'crear',524),(704,'acceso',525),(705,'acceso',526),(706,'acceso',527),(707,'acceso',528),(708,'acceso',529),(709,'listar',529),(710,'crear',529),(711,'acceso',530),(712,'acceso',531),(713,'listar',531),(714,'editar',531),(715,'crear',531),(716,'acceso',532),(717,'acceso',533),(718,'listar',533),(719,'ver',533),(720,'crear',533),(721,'acceso',534),(722,'listar',534),(723,'ver',534),(724,'crear',534),(725,'acceso',535),(726,'acceso',535),(727,'listar',535),(728,'listar',535),(729,'editar',535),(730,'editar',535),(731,'editar',535),(732,'editar',535),(733,'crear',535),(734,'crear',535),(735,'crear',535),(736,'crear',535),(737,'crear',535),(738,'acceso',536),(739,'acceso',537),(740,'listar',536),(741,'acceso',538),(742,'listar',537),(743,'ver',536),(744,'listar',538),(745,'ver',537),(746,'acceso',539),(747,'crear',536),(748,'ver',538),(749,'crear',537),(750,'guardar',536),(751,'crear',538),(752,'listar',539),(753,'guardar',537),(754,'guardar',538),(755,'ver',539),(756,'crear',539),(757,'acceso',540),(758,'acceso',541),(759,'guardar',539),(760,'listar',540),(761,'acceso',542),(762,'listar',541),(763,'ver',541),(764,'ver',540),(765,'listar',542),(766,'crear',541),(767,'crear',540),(768,'ver',542),(769,'acceso',543),(770,'guardar',541),(771,'guardar',540),(772,'crear',542),(773,'listar',543),(774,'guardar',542),(775,'ver',543),(776,'crear',543),(777,'acceso',544),(778,'acceso',545),(779,'guardar',543),(780,'acceso',546),(781,'listar',544),(782,'listar',545),(783,'listar',546),(784,'ver',544),(785,'ver',545),(786,'ver',546),(787,'crear',544),(788,'crear',545),(789,'guardar',544),(790,'crear',546),(791,'acceso',547),(792,'guardar',545),(793,'listar',547),(794,'guardar',546),(795,'ver',547),(796,'crear',547),(797,'guardar',547),(798,'acceso',548),(799,'listar',548),(800,'ver',548),(801,'crear',548),(802,'acceso',549),(803,'guardar',548),(804,'acceso',550),(805,'listar',549),(806,'acceso',551),(807,'listar',550),(808,'ver',550),(809,'ver',549),(810,'listar',551),(811,'acceso',552),(812,'crear',550),(813,'crear',549),(814,'ver',551),(815,'listar',552),(816,'guardar',549),(817,'guardar',550),(818,'crear',551),(819,'ver',552),(820,'guardar',551),(821,'crear',552),(822,'guardar',552),(823,'acceso',553),(824,'acceso',554),(825,'listar',553),(826,'listar',554),(827,'acceso',555),(828,'ver',553),(829,'listar',555),(830,'ver',554),(831,'crear',553),(832,'acceso',556),(833,'crear',554),(834,'ver',555),(835,'guardar',553),(836,'crear',555),(837,'listar',556),(838,'guardar',554),(839,'guardar',555),(840,'ver',556),(841,'crear',556),(842,'acceso',557),(843,'guardar',556),(844,'listar',557),(845,'acceso',558),(846,'acceso',559),(847,'ver',557),(848,'listar',559),(849,'listar',558),(850,'crear',557),(851,'ver',559),(852,'guardar',557),(853,'ver',558),(854,'crear',559),(855,'crear',558),(856,'guardar',559),(857,'guardar',558),(858,'acceso',560),(859,'acceso',561),(860,'listar',561),(861,'listar',560),(862,'ver',561),(863,'ver',560),(864,'crear',561),(865,'crear',560),(866,'guardar',561),(867,'guardar',560),(868,'acceso',562),(869,'acceso',563),(870,'listar',562),(871,'listar',563),(872,'ver',562),(873,'ver',563),(874,'crear',562),(875,'crear',563),(876,'guardar',562),(877,'guardar',563),(878,'acceso',564),(879,'acceso',565),(880,'listar',564),(881,'listar',565),(882,'ver',564),(883,'ver',565),(884,'crear',564),(885,'crear',565),(886,'guardar',564),(887,'guardar',565),(888,'acceso',566),(889,'listar',566),(890,'ver',566),(891,'crear',566),(892,'guardar',566),(893,'acceso',567),(894,'listar',567),(895,'ver',567),(896,'crear',567),(897,'guardar',567),(898,'acceso',568),(899,'listar',568),(900,'ver',568),(901,'crear',568),(902,'guardar',568),(903,'acceso',569),(904,'acceso',570),(905,'acceso',571),(906,'listar',571),(907,'ver',571),(908,'crear',571),(909,'guardar',571),(910,'acceso',572),(911,'listar',572),(912,'ver',572),(913,'crear',572),(914,'guardar',572),(915,'acceso',573),(916,'listar',573),(917,'ver',573),(918,'crear',573),(919,'guardar',573),(920,'acceso',574),(921,'listar',574),(922,'ver',574),(923,'acceso',575),(924,'acceso',576),(925,'crear',574),(926,'listar',576),(927,'listar',575),(928,'guardar',574),(929,'ver',576),(930,'ver',575),(931,'crear',576),(932,'crear',575),(933,'guardar',576),(934,'acceso',577),(935,'guardar',575),(936,'listar',577),(937,'ver',577),(938,'acceso',578),(939,'crear',577),(940,'listar',578),(941,'guardar',577),(942,'acceso',579),(943,'ver',578),(944,'listar',579),(945,'crear',578),(946,'ver',579),(947,'guardar',578),(948,'crear',579),(949,'guardar',579),(950,'acceso',580),(951,'listar',580),(952,'acceso',581),(953,'ver',580),(954,'listar',581),(955,'acceso',582),(956,'crear',580),(957,'ver',581),(958,'listar',582),(959,'acceso',583),(960,'guardar',580),(961,'listar',583),(962,'crear',581),(963,'ver',582),(964,'ver',583),(965,'guardar',581),(966,'crear',582),(967,'crear',583),(968,'acceso',584),(969,'guardar',582),(970,'guardar',583),(971,'listar',584),(972,'ver',584),(973,'crear',584),(974,'acceso',585),(975,'guardar',584),(976,'listar',585),(977,'ver',585),(978,'crear',585),(979,'acceso',586),(980,'listar',586),(981,'guardar',585),(982,'acceso',587),(983,'ver',586),(984,'crear',586),(985,'guardar',586),(986,'acceso',588),(987,'listar',588),(988,'acceso',589),(989,'ver',588),(990,'listar',589),(991,'crear',588),(992,'ver',589),(993,'guardar',588),(994,'crear',589),(995,'guardar',589),(996,'acceso',590),(997,'acceso',591),(998,'listar',591),(999,'ver',591),(1000,'crear',591),(1001,'guardar',591),(1002,'acceso',592),(1003,'listar',592),(1004,'ver',592),(1005,'crear',592),(1006,'guardar',592),(1007,'acceso',593),(1008,'listar',593),(1009,'ver',593),(1010,'crear',593),(1011,'guardar',593),(1012,'acceso',594),(1013,'acceso',595),(1014,'acceso',596),(1015,'acceso',597),(1016,'listar-actividades__ver_todas',597),(1017,'listar-actividades__menu',597),(1018,'acceso',598),(1019,'acceso',599),(1020,'acceso',600),(1021,'acceso',601),(1022,'acceso',602),(1023,'acceso',603),(1024,'acceso',604),(1025,'ver-actividad__editarActividad',604),(1026,'acceso',605),(1027,'acceso',606),(1028,'ver-actividad__administrador_actividad',604),(1029,'ver-agente__editarAgente',606),(1030,'acceso',607),(1031,'acceso',608),(1032,'acceso',609),(1033,'acceso',610),(1034,'acceso',611),(1035,'acceso',612),(1036,'acceso',613),(1037,'listar-casos__exportar_caso',613),(1038,'acceso',614),(1039,'listar-casos__administrador_caso',613),(1040,'acceso',615),(1041,'listar-casos__menu',613),(1042,'listar',615),(1043,'crear',615),(1044,'acceso',616),(1045,'acceso',617),(1046,'acceso',618),(1047,'acceso',619),(1048,'listar',619),(1049,'acceso',620),(1050,'crear',619),(1051,'acceso',621),(1052,'acceso',622),(1053,'acceso',623),(1054,'acceso',624),(1055,'acceso',625),(1056,'listar',624),(1057,'ver-caso__editarCaso',623),(1058,'acceso',626),(1059,'acceso',627),(1060,'crear',624),(1061,'acceso',628),(1062,'acceso',629),(1063,'acceso',630),(1064,'acceso',631),(1065,'acceso',632),(1066,'listar',630),(1067,'listar',632),(1068,'acceso',634),(1069,'acceso',633),(1070,'crear',630),(1071,'editar',632),(1072,'ver__editarColaborador',634),(1073,'listar-contactos__exportar_contacto',633),(1074,'acceso',635),(1075,'crear',632),(1076,'acceso',636),(1077,'acceso',637),(1078,'acceso',638),(1079,'listar',637),(1080,'editar',637),(1081,'acceso',639),(1082,'crear',637),(1083,'ver-contacto__editarContacto',639),(1084,'acceso',640),(1085,'acceso',641),(1086,'listar',641),(1087,'acceso',642),(1088,'listar',642),(1089,'ver',641),(1090,'acceso',643),(1091,'editar',642),(1092,'crear',641),(1093,'configuracion__crearCargo',643),(1094,'crear',642),(1095,'acceso',644),(1096,'guardar',641),(1097,'configuracion__editarCargo',643),(1098,'configuracion__duplicarCargo',643),(1099,'listar-documentos__actualizar_archivo',644),(1100,'configuracion__desactivarActivarCargo',643),(1101,'listar-documentos__menu',644),(1102,'configuracion__crearAreaNegocio',643),(1103,'acceso',645),(1104,'acceso',646),(1105,'acceso',647),(1106,'listar',645),(1107,'listar',646),(1108,'listar',647),(1109,'acceso',648),(1110,'ver',645),(1111,'editar',646),(1112,'crear',646),(1113,'crear',645),(1114,'editar',647),(1115,'acceso',649),(1116,'crear',647),(1117,'guardar',645),(1118,'acceso',650),(1119,'acceso',651),(1120,'acceso',652),(1121,'acceso',653),(1122,'listar',651),(1123,'listar',653),(1124,'editar',651),(1125,'ver',653),(1126,'crear',651),(1127,'acceso',654),(1128,'crear',653),(1129,'acceso',655),(1130,'guardar',653),(1131,'acceso',656),(1132,'listar',656),(1133,'acceso',657),(1134,'editar',656),(1135,'crear',656),(1136,'acceso',658),(1137,'acceso',660),(1138,'acceso',659),(1139,'listar-oportunidades__cambiar_etapa_oportunidad',658),(1140,'acceso',661),(1141,'acceso',662),(1142,'acceso',663),(1143,'crear-oportunidad__asignar_usuario',663),(1144,'acceso',664),(1145,'acceso',665),(1146,'acceso',666),(1147,'acceso',667),(1148,'acceso',668),(1149,'ver-pedido__editarPedido',668),(1150,'acceso',669),(1151,'acceso',671),(1152,'acceso',670),(1153,'ver-oportunidad__comentar_oportunidad',670),(1154,'acceso',672),(1155,'acceso',673),(1156,'acceso',674),(1157,'listar-propiedades__exportar',672),(1158,'editar-propiedades_comisionCompartida',672),(1159,'acceso',675),(1160,'acceso',676),(1161,'acceso',677),(1162,'acceso',678),(1163,'acceso',679),(1164,'acceso',681),(1165,'acceso',680),(1166,'acceso',682),(1167,'listar-proyectos__exportar',679),(1168,'editar-propiedades_comisionCompartida',680),(1169,'acceso',684),(1170,'acceso',683),(1171,'acceso',685),(1172,'acceso',686),(1173,'acceso',687),(1174,'acceso',688),(1175,'acceso',689),(1176,'ver-propiedad__editarPrpiedad',688),(1177,'acceso',690),(1178,'editar-propiedades_comisionCompartida',688),(1179,'acceso',691),(1180,'acceso',692),(1181,'acceso',693),(1182,'acceso',695),(1183,'acceso',694),(1184,'editar-propiedades_comisionCompartida',695),(1185,'ver-proyecto__editarProyecto',693),(1186,'acceso',696),(1187,'listar-roles__editar_rol',696),(1188,'acceso',697),(1189,'listar-roles__duplicar_rol',696),(1190,'acceso',698),(1191,'listar-roles__activar_desactivar_rol',696),(1192,'acceso',699),(1193,'acceso',700),(1194,'acceso',701),(1195,'acceso',702),(1196,'acceso',703),(1197,'acceso',704),(1198,'acceso',705),(1199,'acceso',706),(1200,'acceso',707),(1201,'acceso',708),(1202,'acceso',709),(1203,'acceso',710),(1204,'acceso',711),(1205,'acceso',712),(1206,'acceso',713),(1207,'acceso',714),(1208,'acceso',715),(1209,'acceso',716),(1210,'acceso',717),(1211,'acceso',717),(1212,'acceso',719),(1213,'acceso',718),(1214,'acceso',720),(1215,'acceso',720),(1216,'acceso',721),(1217,'acceso',722),(1218,'acceso',722),(1219,'acceso',724),(1220,'acceso',723),(1221,'acceso',726),(1222,'acceso',725),(1223,'acceso',727),(1224,'acceso',727),(1225,'acceso',727),(1226,'acceso',728),(1227,'acceso',729),(1228,'acceso',730),(1229,'acceso',731),(1230,'acceso',732),(1231,'acceso',732),(1232,'acceso',732),(1233,'acceso',733),(1234,'acceso',733),(1235,'acceso',734),(1236,'acceso',735),(1237,'acceso',736),(1238,'listar',736),(1239,'acceso',738),(1240,'crear',736),(1241,'acceso',737),(1242,'acceso',738),(1243,'crear',736),(1244,'acceso',739),(1245,'acceso',739),(1246,'acceso',740),(1247,'acceso',741),(1248,'acceso',742),(1249,'listar',740),(1250,'acceso',743),(1251,'acceso',743),(1252,'crear',740),(1253,'listar',741),(1254,'crear',741),(1255,'acceso',744),(1256,'acceso',744),(1257,'listar',744),(1258,'acceso',745),(1259,'listar',744),(1260,'crear',744),(1261,'crear',744),(1262,'acceso',747),(1263,'acceso',747),(1264,'acceso',746),(1265,'acceso',747),(1266,'listar',747),(1267,'listar',747),(1268,'listar',747),(1269,'listar',747),(1270,'crear',747),(1271,'crear',747),(1272,'crear',747),(1273,'crear',747),(1274,'crear',747),(1275,'acceso',748),(1276,'acceso',749),(1277,'acceso',750),(1278,'acceso',751),(1279,'acceso',751),(1280,'acceso',751),(1281,'acceso',752),(1282,'acceso',753),(1283,'acceso',752),(1284,'acceso',754),(1285,'acceso',755),(1286,'acceso',756),(1287,'acceso',757),(1288,'acceso',757),(1289,'acceso',757),(1290,'acceso',758),(1291,'acceso',759),(1292,'acceso',760),(1293,'acceso',760),(1294,'acceso',760),(1295,'acceso',760),(1296,'acceso',761),(1297,'acceso',762),(1298,'acceso',762),(1299,'acceso',762),(1300,'acceso',762),(1301,'acceso',762),(1302,'acceso',765),(1303,'acceso',763),(1304,'acceso',765),(1305,'acceso',763),(1306,'acceso',764),(1307,'acceso',766),(1308,'acceso',767),(1309,'acceso',767),(1310,'acceso',768),(1311,'acceso',768),(1312,'acceso',768),(1313,'acceso',768),(1314,'acceso',769),(1315,'acceso',769),(1316,'acceso',772),(1317,'acceso',769),(1318,'acceso',770),(1319,'acceso',771),(1320,'listar',769),(1321,'listar',769),(1322,'listar',769),(1323,'listar',772),(1324,'listar',770),(1325,'listar',771),(1326,'ver',769),(1327,'ver',769),(1328,'ver',772),(1329,'ver',769),(1330,'ver',770),(1331,'ver',771),(1332,'crear',769),(1333,'crear',772),(1334,'crear',769),(1335,'crear',769),(1336,'crear',770),(1337,'guardar',769),(1338,'crear',771),(1339,'guardar',770),(1340,'guardar',769),(1341,'guardar',772),(1342,'guardar',769),(1343,'guardar',771),(1344,'acceso',773),(1345,'acceso',774),(1346,'listar',773),(1347,'listar',774),(1348,'listar',773),(1349,'listar',773),(1350,'listar',773),(1351,'ver',773),(1352,'ver',773),(1353,'ver',774),(1354,'ver',773),(1355,'ver',773),(1356,'ver',773),(1357,'crear',773),(1358,'crear',773),(1359,'crear',773),(1360,'crear',774),(1361,'crear',773),(1362,'crear',773),(1363,'guardar',773),(1364,'guardar',773),(1365,'guardar',773),(1366,'guardar',774),(1367,'guardar',773),(1368,'guardar',773),(1369,'acceso',775),(1370,'acceso',776),(1371,'acceso',775),(1372,'listar',775),(1373,'listar',775),(1374,'listar',775),(1375,'listar',775),(1376,'ver',775),(1377,'listar',776),(1378,'listar',775),(1379,'ver',775),(1380,'ver',775),(1381,'ver',775),(1382,'crear',775),(1383,'ver',776),(1384,'ver',775),(1385,'crear',775),(1386,'crear',775),(1387,'crear',775),(1388,'crear',775),(1389,'guardar',775),(1390,'crear',776),(1391,'guardar',775),(1392,'guardar',775),(1393,'guardar',775),(1394,'guardar',775),(1395,'guardar',776),(1396,'acceso',777),(1397,'acceso',777),(1398,'acceso',779),(1399,'acceso',780),(1400,'acceso',778),(1401,'acceso',781),(1402,'acceso',782),(1403,'acceso',783),(1404,'acceso',784),(1405,'acceso',785),(1406,'acceso',785),(1407,'acceso',786),(1408,'acceso',787),(1409,'acceso',786),(1410,'acceso',786),(1411,'acceso',788),(1412,'acceso',789),(1413,'acceso',789),(1414,'acceso',789),(1415,'acceso',790),(1416,'acceso',791),(1417,'acceso',792),(1418,'acceso',793),(1419,'acceso',793),(1420,'acceso',793),(1421,'acceso',793),(1422,'acceso',794),(1423,'acceso',795),(1424,'acceso',795),(1425,'acceso',797),(1426,'acceso',796),(1427,'acceso',795),(1428,'acceso',798),(1429,'acceso',799),(1430,'acceso',799),(1431,'acceso',799),(1432,'acceso',799),(1433,'acceso',799),(1434,'acceso',801),(1435,'acceso',801),(1436,'acceso',800),(1437,'acceso',801),(1438,'acceso',802),(1439,'acceso',803),(1440,'acceso',802),(1441,'acceso',802),(1442,'acceso',804),(1443,'acceso',805),(1444,'acceso',806),(1445,'acceso',807),(1446,'acceso',807),(1447,'acceso',808),(1448,'acceso',809),(1449,'acceso',810),(1450,'acceso',811),(1451,'acceso',811),(1452,'acceso',813),(1453,'acceso',813),(1454,'acceso',814),(1455,'acceso',812),(1456,'acceso',815),(1457,'acceso',816),(1458,'acceso',817),(1459,'acceso',817),(1460,'acceso',818),(1461,'acceso',819),(1462,'acceso',824),(1463,'acceso',823),(1464,'acceso',821),(1465,'acceso',825),(1466,'acceso',820),(1467,'acceso',822),(1468,'acceso',826),(1469,'acceso',827),(1470,'acceso',829),(1471,'acceso',829),(1472,'editar-propiedades_comisionCompartida',829),(1473,'acceso',828),(1474,'acceso',828),(1475,'acceso',828),(1476,'editar-propiedades_comisionCompartida',828),(1477,'editar-propiedades_comisionCompartida',829),(1478,'editar-propiedades_comisionCompartida',828),(1479,'editar-propiedades_comisionCompartida',828),(1480,'acceso',830),(1481,'acceso',830),(1482,'editar-propiedades_comisionCompartida',830),(1483,'acceso',830),(1484,'editar-propiedades_comisionCompartida',830),(1485,'editar-propiedades_comisionCompartida',830),(1486,'editar-propiedades_comisionCompartida',830),(1487,'editar-propiedades_comisionCompartida',830),(1488,'editar-propiedades_comisionCompartida',830),(1489,'acceso',831),(1490,'acceso',831),(1491,'acceso',831),(1492,'acceso',831),(1493,'acceso',831),(1494,'editar-propiedades_comisionCompartida',831),(1495,'editar-propiedades_comisionCompartida',831),(1496,'editar-propiedades_comisionCompartida',831),(1497,'editar-propiedades_comisionCompartida',831),(1498,'editar-propiedades_comisionCompartida',831),(1499,'editar-propiedades_comisionCompartida',831),(1500,'acceso',832),(1501,'editar-propiedades_comisionCompartida',832),(1502,'editar-propiedades_comisionCompartida',832),(1503,'acceso',832),(1504,'editar-propiedades_comisionCompartida',832),(1505,'editar-propiedades_comisionCompartida',832),(1506,'acceso',833),(1507,'acceso',833),(1508,'acceso',833),(1509,'acceso',834),(1510,'acceso',835),(1511,'acceso',836),(1512,'acceso',835),(1513,'acceso',835),(1514,'acceso',835),(1515,'acceso',837),(1516,'acceso',837),(1517,'acceso',838),(1518,'acceso',837),(1519,'acceso',839),(1520,'acceso',840),(1521,'acceso',841),(1522,'acceso',841),(1523,'acceso',841),(1524,'acceso',843),(1525,'acceso',842),(1526,'acceso',844),(1527,'acceso',845),(1528,'acceso',846),(1529,'acceso',845),(1530,'acceso',847),(1531,'acceso',848),(1532,'acceso',850),(1533,'acceso',849),(1534,'acceso',851),(1535,'acceso',852),(1536,'acceso',852),(1537,'acceso',853),(1538,'acceso',854),(1539,'acceso',853),(1540,'acceso',853),(1541,'acceso',853),(1542,'acceso',855),(1543,'acceso',856),(1544,'acceso',857),(1545,'acceso',858),(1546,'acceso',859),(1547,'acceso',860),(1548,'acceso',861),(1549,'acceso',862),(1550,'acceso',863),(1551,'acceso',864),(1552,'acceso',865),(1553,'acceso',866),(1554,'acceso',867),(1555,'acceso',868),(1556,'acceso',869),(1557,'acceso',870),(1558,'acceso',871),(1559,'listar',871),(1560,'crear',871),(1561,'guardar',533),(1562,'guardar',534),(1563,'acceso',872),(1564,'listar',872),(1565,'ver',872),(1566,'crear',872),(1567,'guardar',872),(1568,'acceso',873),(1569,'listar',873),(1570,'ver',873),(1571,'crear',873),(1572,'guardar',873),(1573,'acceso',874),(1574,'listar',874),(1575,'ver',874),(1576,'crear',874),(1577,'guardar',874),(1578,'guardar',874),(1579,'acceso',875),(1580,'listar',875),(1581,'ver',875),(1582,'crear',875),(1583,'guardar',875),(1584,'acceso',876),(1585,'listar',876),(1586,'listar',876),(1587,'ver',876),(1588,'ver',876),(1589,'crear',876),(1590,'crear',876),(1591,'guardar',876),(1592,'guardar',876),(1593,'guardar',876),(1594,'acceso',879),(1595,'acceso',877),(1596,'acceso',879),(1597,'listar',879),(1598,'acceso',878),(1599,'listar',877),(1600,'listar',878),(1601,'listar',879),(1602,'ver',877),(1603,'ver',879),(1604,'ver',878),(1605,'ver',879),(1606,'crear',877),(1607,'crear',879),(1608,'guardar',877),(1609,'crear',878),(1610,'crear',879),(1611,'guardar',879),(1612,'guardar',878),(1613,'guardar',879),(1614,'acceso',880),(1615,'listar',880),(1616,'ver',880),(1617,'crear',880),(1618,'guardar',880),(1619,'acceso',881),(1620,'acceso',881),(1621,'acceso',884),(1622,'acceso',883),(1623,'acceso',882),(1624,'listar__exportarSalidas',884),(1625,'listar__exportarSalidas',882),(1626,'listar__exportarSalidas',883),(1627,'acceso',885),(1628,'ver__editarSalida',885),(1629,'convertir_order_venta',533),(1630,'guardarOrdenVenta',533),(1631,'convertir_order_venta',534),(1632,'guardarOrdenVenta',534),(1633,'convertir_order_venta',872),(1634,'guardarOrdenVenta',872),(1635,'convertir_order_venta',873),(1636,'guardarOrdenVenta',873),(1637,'convertir_order_venta',880),(1638,'guardarOrdenVenta',880),(1639,'convertir_order_venta',876),(1640,'guardarOrdenVenta',876),(1641,'acceso',886),(1642,'acceso',886),(1643,'listar__exportarConsumos',886),(1644,'acceso',887),(1645,'acceso',887),(1646,'acceso',887),(1647,'acceso',888),(1648,'acceso',888),(1649,'acceso',888),(1650,'ver__editarConsumo',888),(1651,'acceso',890),(1652,'acceso',889),(1653,'listar',890),(1654,'listar',889),(1655,'ver',890),(1656,'ver',889),(1657,'crear',889),(1658,'crear',890),(1659,'guardar',890),(1660,'crear',889),(1661,'guardar',889),(1662,'guardar',889),(1663,'acceso',891),(1664,'acceso',891),(1665,'listar',891),(1666,'acceso',892),(1667,'listar',891),(1668,'ver',891),(1669,'ver',891),(1670,'ver',891),(1671,'listar',892),(1672,'crear',891),(1673,'crear',891),(1674,'crear',891),(1675,'guardar',891),(1676,'ver',892),(1677,'guardar',891),(1678,'crear',892),(1679,'guardar',891),(1680,'guardar',892),(1681,'acceso',893),(1682,'acceso',893),(1683,'acceso',894),(1684,'listar__ver',894),(1685,'acceso',895),(1686,'acceso',896),(1687,'acceso',897),(1688,'acceso',897),(1689,'acceso',898),(1690,'acceso',899),(1691,'acceso',898),(1692,'acceso',900),(1693,'acceso',900),(1694,'acceso',901),(1695,'acceso',900),(1696,'acceso',902);

/*Table structure for table `planilla_configuracion` */

DROP TABLE IF EXISTS `planilla_configuracion`;

CREATE TABLE `planilla_configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicial` date NOT NULL DEFAULT '0000-00-00',
  `id_empresa` int(11) NOT NULL,
  KEY `id` (`id`),
  KEY `id_empresa` (`id_empresa`),
  CONSTRAINT `planilla_configuracion_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=latin1;

/*Data for the table `planilla_configuracion` */

insert  into `planilla_configuracion`(`id`,`fecha_inicial`,`id_empresa`) values (1,'2015-10-30',1),(12,'2015-10-21',2),(13,'2015-11-06',3),(94,'2015-10-21',4),(97,'2015-10-21',6);

/*Table structure for table `planilla_configuracion_cuotas` */

DROP TABLE IF EXISTS `planilla_configuracion_cuotas`;

CREATE TABLE `planilla_configuracion_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_cuota` binary(16) DEFAULT NULL,
  `nombre` varchar(240) DEFAULT NULL,
  `key` varchar(240) NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

/*Data for the table `planilla_configuracion_cuotas` */

insert  into `planilla_configuracion_cuotas`(`id`,`uuid_cuota`,`nombre`,`key`) values (1,NULL,'Riesgo Profesional','r_p'),(2,NULL,'Cuota Sindical','cu_sind'),(3,NULL,'Cuota Seguro social','cu_ssocial'),(4,NULL,'Cuota Seguro Educativo','cu_seduca'),(5,NULL,'Cuota Seguro de Vida','cu_svida'),(6,NULL,'Gasto Profesional (I/R)','cu_pro_ir'),(7,NULL,'Gasto Professional (CSS)','cu_pro_css'),(8,NULL,'Salario Minimo para C.S.S.','sal_min_css');

/*Table structure for table `planilla_configuracion_cuotas_patrono` */

DROP TABLE IF EXISTS `planilla_configuracion_cuotas_patrono`;

CREATE TABLE `planilla_configuracion_cuotas_patrono` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_cuota` binary(16) DEFAULT NULL,
  `nombre` varchar(240) DEFAULT NULL,
  `key` varchar(240) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `planilla_configuracion_cuotas_patrono` */

insert  into `planilla_configuracion_cuotas_patrono`(`id`,`uuid_cuota`,`nombre`,`key`) values (1,NULL,'Empleado','empl'),(2,NULL,'Patrono','patr'),(3,NULL,'XIII Mes','xiii1'),(4,NULL,'XIII Mes','xiii2');

/*Table structure for table `planilla_configuracion_valores` */

DROP TABLE IF EXISTS `planilla_configuracion_valores`;

CREATE TABLE `planilla_configuracion_valores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuota` int(11) DEFAULT NULL,
  `id_patrono` int(11) DEFAULT NULL,
  `id_configuracion` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `id_cuota` (`id_cuota`),
  KEY `id_patrono` (`id_patrono`),
  KEY `planilla_configuracion_valores_ibfk_3` (`id_configuracion`),
  CONSTRAINT `planilla_configuracion_valores_ibfk_1` FOREIGN KEY (`id_cuota`) REFERENCES `planilla_configuracion_cuotas` (`id`),
  CONSTRAINT `planilla_configuracion_valores_ibfk_2` FOREIGN KEY (`id_patrono`) REFERENCES `planilla_configuracion_cuotas_patrono` (`id`),
  CONSTRAINT `planilla_configuracion_valores_ibfk_3` FOREIGN KEY (`id_configuracion`) REFERENCES `planilla_configuracion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=latin1;

/*Data for the table `planilla_configuracion_valores` */

insert  into `planilla_configuracion_valores`(`id`,`id_cuota`,`id_patrono`,`id_configuracion`,`valor`) values (1,1,2,1,'2.34'),(2,2,1,1,'2.00'),(3,3,1,1,'3.45'),(4,3,2,1,'4.00'),(5,3,3,1,'5.00'),(6,3,4,1,'6.00'),(7,4,1,1,'7.00'),(8,4,2,1,'8.00'),(21,5,1,1,'4.00'),(22,5,2,1,'9.00'),(23,6,1,1,'10.00'),(24,7,1,1,'11.00'),(25,8,1,1,'12.00'),(80,1,2,12,'0.00'),(81,2,1,12,'0.00'),(82,3,1,12,'0.00'),(83,3,2,12,'0.00'),(84,3,3,12,'0.00'),(85,3,4,12,'0.00'),(86,4,1,12,'0.00'),(87,4,2,12,'0.00'),(88,5,1,12,'0.00'),(89,5,2,12,'0.00'),(90,6,1,12,'0.00'),(91,7,1,12,'0.00'),(92,8,1,12,'0.00'),(93,1,2,13,'1.00'),(94,2,1,13,'4.58'),(95,3,1,13,'3.69'),(96,3,2,13,'0.00'),(97,3,3,13,'0.00'),(98,3,4,13,'0.00'),(99,4,1,13,'0.00'),(100,4,2,13,'0.00'),(101,5,1,13,'0.00'),(102,5,2,13,'0.00'),(103,6,1,13,'0.00'),(104,7,1,13,'0.00'),(105,8,1,13,'0.00'),(106,1,2,94,'0.00'),(107,2,1,94,'0.00'),(108,3,1,94,'0.00'),(109,3,2,94,'0.00'),(110,3,3,94,'0.00'),(111,3,4,94,'0.00'),(112,4,1,94,'0.00'),(113,4,2,94,'0.00'),(114,5,1,94,'0.00'),(115,5,2,94,'0.00'),(116,6,1,94,'0.00'),(117,7,1,94,'0.00'),(118,8,1,94,'0.00'),(119,1,2,97,'1.20'),(120,2,1,97,'0.00'),(121,3,1,97,'0.00'),(122,3,2,97,'0.00'),(123,3,3,97,'0.00'),(124,3,4,97,'0.00'),(125,4,1,97,'0.00'),(126,4,2,97,'0.00'),(127,5,1,97,'0.00'),(128,5,2,97,'0.00'),(129,6,1,97,'0.00'),(130,7,1,97,'0.00'),(131,8,1,97,'0.00');

/*Table structure for table `pln_planilla_beneficios` */

DROP TABLE IF EXISTS `pln_planilla_beneficios`;

CREATE TABLE `pln_planilla_beneficios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(240) DEFAULT NULL,
  `descripcion` text,
  `empresa_id` int(11) DEFAULT NULL,
  `valor_rata` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` enum('Activo','Desactivado') DEFAULT 'Activo',
  `creado_por` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empresa_id` (`empresa_id`),
  CONSTRAINT `pln_planilla_beneficios_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_beneficios` */

insert  into `pln_planilla_beneficios`(`id`,`nombre`,`descripcion`,`empresa_id`,`valor_rata`,`estado`,`creado_por`) values (72,'3333','4444',1,'5.55','Activo',1),(73,'11','222222222',1,'33.33','Activo',1),(74,'1','2',1,'3.00','Activo',1),(75,'1717171','test test',1,'5.00','Activo',1),(76,'Trabajos bajo lluvia','Altura',1,'2.50','Activo',1);

/*Table structure for table `pln_planilla_cat_horas_no_laboradas` */

DROP TABLE IF EXISTS `pln_planilla_cat_horas_no_laboradas`;

CREATE TABLE `pln_planilla_cat_horas_no_laboradas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(240) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_cat_horas_no_laboradas` */

insert  into `pln_planilla_cat_horas_no_laboradas`(`id`,`nombre`) values (1,'8 Horas'),(2,'4 Horas');

/*Table structure for table `pln_planilla_conf_feriado_acumulados` */

DROP TABLE IF EXISTS `pln_planilla_conf_feriado_acumulados`;

CREATE TABLE `pln_planilla_conf_feriado_acumulados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dia_feriado` int(11) DEFAULT NULL,
  `id_acumulado` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id_dia_feriado` (`id_dia_feriado`),
  KEY `pln_planilla_conf_feriado_acumulados_ibfk_2` (`id_acumulado`),
  CONSTRAINT `pln_planilla_conf_feriado_acumulados_ibfk_1` FOREIGN KEY (`id_dia_feriado`) REFERENCES `pln_planilla_dias_feriados` (`id`),
  CONSTRAINT `pln_planilla_conf_feriado_acumulados_ibfk_2` FOREIGN KEY (`id_acumulado`) REFERENCES `pln_planilla_dias_feriados_acumulados` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_conf_feriado_acumulados` */

insert  into `pln_planilla_conf_feriado_acumulados`(`id`,`id_dia_feriado`,`id_acumulado`,`fecha_creacion`) values (1,265,2,'2015-11-13 15:59:16'),(2,266,1,'2015-11-13 16:29:31'),(3,266,3,'2015-11-13 16:29:31'),(5,268,1,'2015-11-13 16:31:33');

/*Table structure for table `pln_planilla_conf_feriado_deduccion` */

DROP TABLE IF EXISTS `pln_planilla_conf_feriado_deduccion`;

CREATE TABLE `pln_planilla_conf_feriado_deduccion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_dia_feriado` int(11) DEFAULT NULL,
  `id_deduccion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `id_dia_feriado` (`id_dia_feriado`),
  KEY `pln_planilla_conf_feriado_deduccion_ibfk_2` (`id_deduccion`),
  CONSTRAINT `pln_planilla_conf_feriado_deduccion_ibfk_1` FOREIGN KEY (`id_dia_feriado`) REFERENCES `pln_planilla_dias_feriados` (`id`),
  CONSTRAINT `pln_planilla_conf_feriado_deduccion_ibfk_2` FOREIGN KEY (`id_deduccion`) REFERENCES `pln_planilla_dias_feriados_deducciones` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_conf_feriado_deduccion` */

insert  into `pln_planilla_conf_feriado_deduccion`(`id`,`id_dia_feriado`,`id_deduccion`,`fecha_creacion`) values (1,265,1,'2015-11-13 15:59:16'),(2,266,1,'2015-11-13 16:29:31'),(4,268,2,'2015-11-13 16:31:33');

/*Table structure for table `pln_planilla_configuracion` */

DROP TABLE IF EXISTS `pln_planilla_configuracion`;

CREATE TABLE `pln_planilla_configuracion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicial` date NOT NULL DEFAULT '0000-00-00',
  `id_empresa` int(11) NOT NULL,
  KEY `id` (`id`),
  KEY `id_empresa` (`id_empresa`),
  CONSTRAINT `pln_planilla_configuracion_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=112 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_configuracion` */

insert  into `pln_planilla_configuracion`(`id`,`fecha_inicial`,`id_empresa`) values (99,'2015-11-07',1),(100,'2015-11-13',2),(101,'2015-11-13',7),(102,'2015-11-13',10),(103,'2015-11-13',13),(104,'2015-11-13',16),(105,'2015-11-13',8),(106,'2015-11-13',19),(107,'2015-11-13',15),(108,'2015-11-13',11),(109,'2015-11-13',14),(110,'2015-11-13',9),(111,'2015-11-13',18);

/*Table structure for table `pln_planilla_configuracion_cuotas` */

DROP TABLE IF EXISTS `pln_planilla_configuracion_cuotas`;

CREATE TABLE `pln_planilla_configuracion_cuotas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_cuota` binary(16) DEFAULT NULL,
  `nombre` varchar(240) DEFAULT NULL,
  `key` varchar(240) NOT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_configuracion_cuotas` */

insert  into `pln_planilla_configuracion_cuotas`(`id`,`uuid_cuota`,`nombre`,`key`) values (1,NULL,'Riesgo Profesional','r_p'),(2,NULL,'Cuota Sindical','cu_sind'),(3,NULL,'Cuota Seguro Social','cu_ssocial'),(4,NULL,'Cuota Seguro Educativo','cu_seduca'),(5,NULL,'Cuota Seguro de Vida','cu_svida'),(6,NULL,'Gasto Profesional (I/R)','cu_pro_ir'),(7,NULL,'Gasto Professional (CSS)','cu_pro_css'),(8,NULL,'Salario Minimo para C.S.S.','sal_min_css'),(9,NULL,'Cuota Seguro Social (XII Mes)','cu_ssocial_decimo');

/*Table structure for table `pln_planilla_configuracion_cuotas_patrono` */

DROP TABLE IF EXISTS `pln_planilla_configuracion_cuotas_patrono`;

CREATE TABLE `pln_planilla_configuracion_cuotas_patrono` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_cuota` binary(16) DEFAULT NULL,
  `nombre` varchar(240) DEFAULT NULL,
  `key` varchar(240) DEFAULT NULL,
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_configuracion_cuotas_patrono` */

insert  into `pln_planilla_configuracion_cuotas_patrono`(`id`,`uuid_cuota`,`nombre`,`key`) values (1,NULL,'Empleado','empl'),(2,NULL,'Patrono','patr');

/*Table structure for table `pln_planilla_configuracion_valores` */

DROP TABLE IF EXISTS `pln_planilla_configuracion_valores`;

CREATE TABLE `pln_planilla_configuracion_valores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_cuota` int(11) DEFAULT NULL,
  `id_patrono` int(11) DEFAULT NULL,
  `id_configuracion` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `id_cuota` (`id_cuota`),
  KEY `id_patrono` (`id_patrono`),
  KEY `planilla_configuracion_valores_ibfk_3` (`id_configuracion`),
  CONSTRAINT `pln_planilla_configuracion_valores_ibfk_1` FOREIGN KEY (`id_cuota`) REFERENCES `pln_planilla_configuracion_cuotas` (`id`),
  CONSTRAINT `pln_planilla_configuracion_valores_ibfk_2` FOREIGN KEY (`id_patrono`) REFERENCES `pln_planilla_configuracion_cuotas_patrono` (`id`),
  CONSTRAINT `pln_planilla_configuracion_valores_ibfk_3` FOREIGN KEY (`id_configuracion`) REFERENCES `pln_planilla_configuracion` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=170 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_configuracion_valores` */

insert  into `pln_planilla_configuracion_valores`(`id`,`id_cuota`,`id_patrono`,`id_configuracion`,`valor`) values (1,1,2,99,'11.00'),(2,2,1,99,'12.00'),(3,3,1,99,'13.00'),(4,3,2,99,'14.00'),(5,4,1,99,'17.00'),(6,4,2,99,'18.00'),(7,5,1,99,'19.00'),(8,5,2,99,'20.00'),(9,6,1,99,'21.00'),(10,7,1,99,'22.00'),(11,8,1,99,'23.00'),(12,9,1,99,'15.00'),(13,9,2,99,'16.00'),(14,1,2,100,'0.00'),(15,2,1,100,'0.00'),(16,3,1,100,'0.00'),(17,3,2,100,'0.00'),(18,4,1,100,'0.00'),(19,4,2,100,'0.00'),(20,5,1,100,'0.00'),(21,5,2,100,'0.00'),(22,6,1,100,'0.00'),(23,7,1,100,'0.00'),(24,8,1,100,'0.00'),(25,9,1,100,'0.00'),(26,9,2,100,'0.00'),(27,1,2,101,'0.00'),(28,2,1,101,'0.00'),(29,3,1,101,'0.00'),(30,3,2,101,'0.00'),(31,4,1,101,'0.00'),(32,4,2,101,'0.00'),(33,5,1,101,'0.00'),(34,5,2,101,'0.00'),(35,6,1,101,'0.00'),(36,7,1,101,'0.00'),(37,8,1,101,'0.00'),(38,9,1,101,'0.00'),(39,9,2,101,'0.00'),(40,1,2,102,'0.00'),(41,2,1,102,'0.00'),(42,3,1,102,'0.00'),(43,3,2,102,'0.00'),(44,4,1,102,'0.00'),(45,4,2,102,'0.00'),(46,5,1,102,'0.00'),(47,5,2,102,'0.00'),(48,6,1,102,'0.00'),(49,7,1,102,'0.00'),(50,8,1,102,'0.00'),(51,9,1,102,'0.00'),(52,9,2,102,'0.00'),(53,1,2,103,'0.00'),(54,2,1,103,'0.00'),(55,3,1,103,'0.00'),(56,3,2,103,'0.00'),(57,4,1,103,'0.00'),(58,4,2,103,'0.00'),(59,5,1,103,'0.00'),(60,5,2,103,'0.00'),(61,6,1,103,'0.00'),(62,7,1,103,'0.00'),(63,8,1,103,'0.00'),(64,9,1,103,'0.00'),(65,9,2,103,'0.00'),(66,1,2,104,'0.00'),(67,2,1,104,'0.00'),(68,3,1,104,'0.00'),(69,3,2,104,'0.00'),(70,4,1,104,'0.00'),(71,4,2,104,'0.00'),(72,5,1,104,'0.00'),(73,5,2,104,'0.00'),(74,6,1,104,'0.00'),(75,7,1,104,'0.00'),(76,8,1,104,'0.00'),(77,9,1,104,'0.00'),(78,9,2,104,'0.00'),(79,1,2,105,'0.00'),(80,2,1,105,'0.00'),(81,3,1,105,'0.00'),(82,3,2,105,'0.00'),(83,4,1,105,'0.00'),(84,4,2,105,'0.00'),(85,5,1,105,'0.00'),(86,5,2,105,'0.00'),(87,6,1,105,'0.00'),(88,7,1,105,'0.00'),(89,8,1,105,'0.00'),(90,9,1,105,'0.00'),(91,9,2,105,'0.00'),(92,1,2,106,'0.00'),(93,2,1,106,'0.00'),(94,3,1,106,'0.00'),(95,3,2,106,'0.00'),(96,4,1,106,'0.00'),(97,4,2,106,'0.00'),(98,5,1,106,'0.00'),(99,5,2,106,'0.00'),(100,6,1,106,'0.00'),(101,7,1,106,'0.00'),(102,8,1,106,'0.00'),(103,9,1,106,'0.00'),(104,9,2,106,'0.00'),(105,1,2,107,'0.00'),(106,2,1,107,'0.00'),(107,3,1,107,'0.00'),(108,3,2,107,'0.00'),(109,4,1,107,'0.00'),(110,4,2,107,'0.00'),(111,5,1,107,'0.00'),(112,5,2,107,'0.00'),(113,6,1,107,'0.00'),(114,7,1,107,'0.00'),(115,8,1,107,'0.00'),(116,9,1,107,'0.00'),(117,9,2,107,'0.00'),(118,1,2,108,'0.00'),(119,2,1,108,'0.00'),(120,3,1,108,'0.00'),(121,3,2,108,'0.00'),(122,4,1,108,'0.00'),(123,4,2,108,'0.00'),(124,5,1,108,'0.00'),(125,5,2,108,'0.00'),(126,6,1,108,'0.00'),(127,7,1,108,'0.00'),(128,8,1,108,'0.00'),(129,9,1,108,'0.00'),(130,9,2,108,'0.00'),(131,1,2,109,'0.00'),(132,2,1,109,'0.00'),(133,3,1,109,'0.00'),(134,3,2,109,'0.00'),(135,4,1,109,'0.00'),(136,4,2,109,'0.00'),(137,5,1,109,'0.00'),(138,5,2,109,'0.00'),(139,6,1,109,'0.00'),(140,7,1,109,'0.00'),(141,8,1,109,'0.00'),(142,9,1,109,'0.00'),(143,9,2,109,'0.00'),(144,1,2,110,'0.00'),(145,2,1,110,'0.00'),(146,3,1,110,'0.00'),(147,3,2,110,'0.00'),(148,4,1,110,'0.00'),(149,4,2,110,'0.00'),(150,5,1,110,'0.00'),(151,5,2,110,'0.00'),(152,6,1,110,'0.00'),(153,7,1,110,'0.00'),(154,8,1,110,'0.00'),(155,9,1,110,'0.00'),(156,9,2,110,'0.00'),(157,1,2,111,'0.00'),(158,2,1,111,'0.00'),(159,3,1,111,'0.00'),(160,3,2,111,'0.00'),(161,4,1,111,'0.00'),(162,4,2,111,'0.00'),(163,5,1,111,'0.00'),(164,5,2,111,'0.00'),(165,6,1,111,'0.00'),(166,7,1,111,'0.00'),(167,8,1,111,'0.00'),(168,9,1,111,'0.00'),(169,9,2,111,'0.00');

/*Table structure for table `pln_planilla_dias_feriados` */

DROP TABLE IF EXISTS `pln_planilla_dias_feriados`;

CREATE TABLE `pln_planilla_dias_feriados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(240) DEFAULT NULL,
  `descripcion` text,
  `fecha_oficial` date DEFAULT '0000-00-00',
  `horas_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) NOT NULL,
  `estado` varchar(240) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  KEY `id` (`id`),
  KEY `empresa_id` (`empresa_id`),
  KEY `horas_id` (`horas_id`),
  CONSTRAINT `pln_planilla_dias_feriados_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`),
  CONSTRAINT `pln_planilla_dias_feriados_ibfk_2` FOREIGN KEY (`horas_id`) REFERENCES `pln_planilla_cat_horas_no_laboradas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=277 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_dias_feriados` */

insert  into `pln_planilla_dias_feriados`(`id`,`nombre`,`descripcion`,`fecha_oficial`,`horas_id`,`empresa_id`,`estado`,`creado_por`) values (265,'sasas','sasas','2007-11-23',1,19,'Activado',1),(266,'A√±o Nuevo','A√±o Nuevo','2014-01-01',1,19,'Activado',1),(268,'A','D','2014-05-02',1,7,'Activado',1),(269,'A','D','2015-05-02',1,7,'Activado',1),(270,'A√±o Nuevo','A√±o Nuevo','2015-01-01',1,19,'Activado',1),(273,'Grito','','2014-11-10',1,11,'Activado',1),(274,'Grito','','2015-11-10',1,11,'Activado',1),(275,'Grito','','2014-11-10',1,18,'Activado',1),(276,'Grito','','2015-11-10',1,18,'Activado',1);

/*Table structure for table `pln_planilla_dias_feriados_acumulados` */

DROP TABLE IF EXISTS `pln_planilla_dias_feriados_acumulados`;

CREATE TABLE `pln_planilla_dias_feriados_acumulados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(240) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_dias_feriados_acumulados` */

insert  into `pln_planilla_dias_feriados_acumulados`(`id`,`nombre`) values (1,'Vacaciones'),(2,'XIII mes'),(3,'Prima de Antiguedad'),(4,'Indemnizaci√≥n');

/*Table structure for table `pln_planilla_dias_feriados_deducciones` */

DROP TABLE IF EXISTS `pln_planilla_dias_feriados_deducciones`;

CREATE TABLE `pln_planilla_dias_feriados_deducciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(240) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_dias_feriados_deducciones` */

insert  into `pln_planilla_dias_feriados_deducciones`(`id`,`nombre`) values (1,'Seguro Social'),(2,'Seguro Educativo'),(3,'Impuesto Sobre la Renta');

/*Table structure for table `pln_planilla_ratas` */

DROP TABLE IF EXISTS `pln_planilla_ratas`;

CREATE TABLE `pln_planilla_ratas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(240) DEFAULT NULL,
  `descripcion` text,
  `rata_actual` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `pln_planilla_ratas` */

/*Table structure for table `pln_rata_empresa` */

DROP TABLE IF EXISTS `pln_rata_empresa`;

CREATE TABLE `pln_rata_empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rata_id` int(11) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rata_id` (`rata_id`),
  KEY `empresa_id` (`empresa_id`),
  CONSTRAINT `pln_rata_empresa_ibfk_1` FOREIGN KEY (`rata_id`) REFERENCES `pln_planilla_ratas` (`id`),
  CONSTRAINT `pln_rata_empresa_ibfk_2` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

/*Data for the table `pln_rata_empresa` */

insert  into `pln_rata_empresa`(`id`,`rata_id`,`empresa_id`,`valor`) values (1,24,1,'25.00'),(2,25,1,'3.00'),(3,26,1,'54.00'),(4,27,1,'25.35');

/*Table structure for table `pres_catalogo` */

DROP TABLE IF EXISTS `pres_catalogo`;

CREATE TABLE `pres_catalogo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `orden` tinyint(1) DEFAULT NULL,
  `key_valor` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `pres_catalogo` */

insert  into `pres_catalogo`(`id`,`nombre`,`estado`,`orden`,`key_valor`) values (1,'1 meses','Activo',NULL,'1'),(2,'3 meses','Activo',NULL,'3'),(3,'6 meses','Activo',NULL,'6'),(4,'12 meses','Activo',NULL,'12'),(5,'24 meses','Activo',NULL,'24');

/*Table structure for table `pres_presupuesto` */

DROP TABLE IF EXISTS `pres_presupuesto`;

CREATE TABLE `pres_presupuesto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_presupuesto` binary(16) NOT NULL,
  `codigo` varchar(100) DEFAULT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `centro_contable_id` int(11) NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `empresa_id` int(11) NOT NULL,
  `cantidad_meses` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `pres_presupuesto` */

insert  into `pres_presupuesto`(`id`,`uuid_presupuesto`,`codigo`,`nombre`,`centro_contable_id`,`fecha_inicio`,`created_at`,`updated_at`,`empresa_id`,`cantidad_meses`) values (1,'Â®∆¢∞ëûbfäa•','PPTO000001','32',2,'2015-12-01 14:36:06','2015-12-21 14:34:21','2015-12-21 14:36:06',1,'1'),(2,'Â®\Z<i!•ëûbfäa•','PPTO000002','sucursal S',10,'2016-02-01 14:39:58','2015-12-21 14:37:38','2015-12-21 14:39:58',1,'3');

/*Table structure for table `pres_presupuesto_cuenta_centro` */

DROP TABLE IF EXISTS `pres_presupuesto_cuenta_centro`;

CREATE TABLE `pres_presupuesto_cuenta_centro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int(11) NOT NULL,
  `cuentas_id` int(11) NOT NULL,
  `centro_contable_id` int(11) NOT NULL,
  `montos` decimal(10,2) DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `info_presupuesto` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

/*Data for the table `pres_presupuesto_cuenta_centro` */

insert  into `pres_presupuesto_cuenta_centro`(`id`,`presupuesto_id`,`cuentas_id`,`centro_contable_id`,`montos`,`empresa_id`,`created_at`,`updated_at`,`info_presupuesto`) values (1,1,91,2,'10.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":\"10.00\"}}'),(2,1,92,2,'50.00',1,'2015-12-21 14:34:21','2015-12-21 14:35:04','{\"meses\":{\"dic_15\":\"50.00\"}}'),(3,1,95,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:36:06','{\"meses\":{\"dic_15\":0}}'),(4,1,96,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(5,1,97,2,'60.00',1,'2015-12-21 14:34:21','2015-12-21 14:36:06','{\"meses\":{\"dic_15\":\"60.00\"}}'),(6,1,98,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(7,1,99,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(8,1,100,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(9,1,101,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(10,1,102,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(11,1,103,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(12,1,104,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(13,1,105,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(14,1,106,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(15,1,107,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(16,1,109,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(17,1,110,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(18,1,111,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(19,1,112,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(20,1,113,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(21,1,114,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(22,1,115,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(23,1,118,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(24,1,119,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(25,1,120,2,'0.00',1,'2015-12-21 14:34:21','2015-12-21 14:34:21','{\"meses\":{\"dic_15\":0}}'),(26,2,91,10,'626.00',1,'2015-12-21 14:37:38','2015-12-21 14:39:58','{\"meses\":{\"feb_16\":\"63.00\",\"mar_16\":\"63.00\",\"abr_16\":\"500.00\"}}'),(27,2,92,10,'110.00',1,'2015-12-21 14:37:38','2015-12-21 14:39:58','{\"meses\":{\"feb_16\":\"50.00\",\"mar_16\":\"50.00\",\"abr_16\":\"10.00\"}}'),(28,2,95,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:39:58','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":\"0.00\"}}'),(29,2,96,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":\"0.00\",\"abr_16\":0}}'),(30,2,97,10,'70.00',1,'2015-12-21 14:37:38','2015-12-21 14:39:58','{\"meses\":{\"feb_16\":\"10.00\",\"mar_16\":\"10.00\",\"abr_16\":\"50.00\"}}'),(31,2,98,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:39:58','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":\"0.00\"}}'),(32,2,99,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:39:58','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":\"0.00\"}}'),(33,2,100,10,'50.00',1,'2015-12-21 14:37:38','2015-12-21 14:39:58','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":\"50.00\"}}'),(34,2,101,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(35,2,102,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(36,2,103,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(37,2,104,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(38,2,105,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(39,2,106,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(40,2,107,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(41,2,109,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(42,2,110,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(43,2,111,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(44,2,112,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(45,2,113,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(46,2,114,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(47,2,115,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(48,2,118,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(49,2,119,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}'),(50,2,120,10,'0.00',1,'2015-12-21 14:37:38','2015-12-21 14:38:51','{\"meses\":{\"feb_16\":0,\"mar_16\":0,\"abr_16\":0}}');

/*Table structure for table `pro_categorias` */

DROP TABLE IF EXISTS `pro_categorias`;

CREATE TABLE `pro_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_categoria` binary(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `estado` tinyint(2) NOT NULL DEFAULT '1',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creado_por` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `pro_categorias` */

insert  into `pro_categorias`(`id`,`uuid_categoria`,`nombre`,`descripcion`,`estado`,`fecha_creacion`,`creado_por`,`id_empresa`) values (1,'Â~tRyíÀï„ºvNT†','Categor√≠a 1','Descripci√≥n de categor√≠a 1',1,'2015-10-29 14:36:16',1,1),(2,'Â~tRyïVï„ºvNT†','Categor√≠a 2','Descripci√≥n de categor√≠a 2',1,'2015-10-29 14:36:16',1,1);

/*Table structure for table `pro_proveedor_categoria` */

DROP TABLE IF EXISTS `pro_proveedor_categoria`;

CREATE TABLE `pro_proveedor_categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_proveedor` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `pro_proveedor_categoria` */

insert  into `pro_proveedor_categoria`(`id`,`id_proveedor`,`id_categoria`) values (1,1,1),(4,5,1),(7,2,2),(8,6,1),(9,4,1),(10,4,2);

/*Table structure for table `pro_proveedores` */

DROP TABLE IF EXISTS `pro_proveedores`;

CREATE TABLE `pro_proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_proveedor` binary(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `uuid_tipo` binary(16) NOT NULL,
  `ruc` varchar(100) NOT NULL,
  `estado` tinyint(2) NOT NULL DEFAULT '1',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creado_por` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `id_forma_pago` int(11) NOT NULL,
  `id_banco` int(11) NOT NULL,
  `id_tipo_cuenta` int(11) NOT NULL,
  `numero_cuenta` varchar(20) NOT NULL,
  `limite_credito` decimal(10,2) NOT NULL,
  `cheque` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `pro_proveedores` */

insert  into `pro_proveedores`(`id`,`uuid_proveedor`,`nombre`,`telefono`,`email`,`uuid_tipo`,`ruc`,`estado`,`fecha_creacion`,`creado_por`,`id_empresa`,`id_forma_pago`,`id_banco`,`id_tipo_cuenta`,`numero_cuenta`,`limite_credito`,`cheque`) values (1,'Â~t˙∞ƒIï„ºvNT†','Proveedor 1','6674532','email@email.com','uuid_categoria\0\0','12345',1,'2015-10-30 14:25:47',1,1,0,0,0,'','0.00',0),(2,'Â~t˙∞»⁄ï„ºvNT†','Proveedor 22','6672','email2@email2.com2','...\0\0\0\0\0\0\0\0\0\0\0\0\0','12345',1,'2015-11-09 13:28:38',1,1,9,13,15,'222','2.22',1),(3,'Â~t˙∞ \Zï„ºvNT†','Proveedor 3','4532','email3@email3.com','uuid_categoria\0\0','12345',1,'2015-10-30 14:25:47',1,1,0,0,0,'','0.00',0),(4,'ÂÑ≈ \'∫ï„ºvNT†','Proveedor 4','12345','','...\0\0\0\0\0\0\0\0\0\0\0\0\0','',1,'2015-11-06 00:00:00',1,1,0,0,0,'','0.00',0),(5,'ÂÑ∆TßæÀï„ºvNT†','Metro Goldwyn','205-0000','rbatista@pensanomica.com','uuid_categoria\0\0','12345678 D.V. 2',1,'2015-11-06 00:00:00',6,1,11,14,16,'090909090909','10000000.00',0),(6,'ÂÑ∆Æºï„ºvNT†','Bienes y Muebles B','6222-22222','info@info.com','...\0\0\0\0\0\0\0\0\0\0\0\0\0','',1,'2015-11-09 13:49:11',6,1,11,13,15,'08790879999999999999','10000.00',1);

/*Table structure for table `pro_proveedores_campos` */

DROP TABLE IF EXISTS `pro_proveedores_campos`;

CREATE TABLE `pro_proveedores_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

/*Data for the table `pro_proveedores_campos` */

insert  into `pro_proveedores_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'nombre','Nombre',0,14,'activo','','','div','',1,'NULL','0000-00-00 00:00:00',2),(2,'telefono','Tel&eacute;fono',0,14,'activo','','','div','',1,'','0000-00-00 00:00:00',4),(3,'email','E-mail',0,4,'activo','','','div','',0,'','0000-00-00 00:00:00',6),(4,'categorias][','Categor&iacute;a(s)',0,18,'activo','{\"class\":\"chosen categorias\",\"multiple\":\"true\",\"data-placeholder\":\"Seleccione\"}','','div','pro_categorias',1,'','0000-00-00 00:00:00',8),(5,'ruc','R.U.C.',0,14,'activo','','','div','',0,'','0000-00-00 00:00:00',10),(6,'tipo','Tipo',0,18,'activo','{\"class\":\"chosen\"}','','div','pro_tipos',1,'','0000-00-00 00:00:00',12),(7,'forma_pago','Forma de pago',0,12,'activo','{\"class\":\"chosen\"}','','div','',0,'','0000-00-00 00:00:00',14),(8,'banco','Banco',0,12,'activo','{\"class\":\"chosen\"}','','div','',0,'','0000-00-00 00:00:00',16),(9,'tipo_cuenta','Tipo de cuenta',0,12,'activo','{\"class\":\"chosen\"}','','div','',0,'','0000-00-00 00:00:00',18),(10,'numero_cuenta','N&uacute;mero de cuenta',0,14,'activo','{\"data-inputmask\":\"\'mask\':\'9{0,20}\',\'greedy\':false\"}','','div','',0,'','0000-00-00 00:00:00',20),(11,'limite_credito','L&iacute;mite de cr&eacute;dito',0,22,'activo','{\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control\",\"data-inputmask\":\"\'mask\':\'9{0,8}.9{0,2}\',\'greedy\':false\"}','','div','',0,'','0000-00-00 00:00:00',22),(12,'cheque','Cheque',0,2,'activo','','','div','',0,'','0000-00-00 00:00:00',24),(13,'cancelarProveedor','Cancelar',0,8,'activo','','','div','',0,'proveedores/listar','0000-00-00 00:00:00',30),(14,'guardarProveedor','Guardar',0,13,'activo','','','div','',0,'','0000-00-00 00:00:00',32),(15,'titulo1','Informaci&oacute;n de pago',0,27,'activo','','','div','',0,'','0000-00-00 00:00:00',13),(16,'titulo2','Balance de proveedor',0,27,'activo','',NULL,'div','',0,'',NULL,26),(17,'saldo_pendiente','',0,22,'activo','{\"disabled\":\"true\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control saldo_pendiente\",\"data-inputmask\":\"\'mask\':\'9{0,8}.9{0,2}\',\'greedy\':false\"}',NULL,'div',NULL,NULL,NULL,NULL,28),(18,'credito','',0,22,'activo','{\"disabled\":\"true\",\"data-addon-icon\":\"fa-dollar\",\"class\":\"form-control credito\",\"data-inputmask\":\"\'mask\':\'9{0,8}.9{0,2}\',\'greedy\':false\"}','','div','',0,'','0000-00-00 00:00:00',29),(19,'anterior','Anterior',0,1,'activo','{\"class\":\"btn btn-default btn-block anterior\"}',NULL,'div','',0,'',NULL,36),(20,'siguiente','Siguiente',0,1,'activo','{\"class\":\"btn btn-default btn-block siguiente\"}',NULL,'div','',0,NULL,NULL,38);

/*Table structure for table `pro_proveedores_cat` */

DROP TABLE IF EXISTS `pro_proveedores_cat`;

CREATE TABLE `pro_proveedores_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `pro_proveedores_cat` */

/*Table structure for table `pro_tipos` */

DROP TABLE IF EXISTS `pro_tipos`;

CREATE TABLE `pro_tipos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_tipo` binary(16) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `estado` tinyint(2) NOT NULL DEFAULT '1',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `creado_por` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `pro_tipos` */

insert  into `pro_tipos`(`id`,`uuid_tipo`,`nombre`,`descripcion`,`estado`,`fecha_creacion`,`creado_por`,`id_empresa`) values (1,'uuid_categoria\0\0','Tipo 1','Descripci√≥n de tipo 1',1,'2015-10-29 14:35:15',1,1),(2,'...\0\0\0\0\0\0\0\0\0\0\0\0\0','Tipo 2','Descripci√≥n de tipo 2',1,'2015-10-29 14:35:15',1,1);

/*Table structure for table `recursos` */

DROP TABLE IF EXISTS `recursos`;

CREATE TABLE `recursos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_recursos_modulos1_idx` (`modulo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=903 DEFAULT CHARSET=utf8;

/*Data for the table `recursos` */

insert  into `recursos`(`id`,`nombre`,`modulo_id`) values (1,'colaboradores/listar',18),(2,'colaboradores/crear',18),(3,'colaboradores/ver/(:any)',18),(4,'colaboradores/configuracion',18),(5,'casos/listar-casos',15),(6,'casos/crear-caso/(:any)',15),(7,'casos/ver-caso/(:any)',15),(8,'casos/historial/(:any)',15),(9,'casos/asignarme/(:any)',15),(10,'oportunidades/listar-oportunidades',8),(11,'oportunidades/crear-oportunidad/(:any)',8),(12,'oportunidades/editar-oportunidad/(:any)',8),(13,'oportunidades/ver-oportunidad/(:any)',8),(14,'oportunidades/pipeline-de-oportunidades',8),(15,'oportunidades/puntaje-de-oportunidades',8),(16,'configuracion',1),(17,'propiedades/listar-propiedades',10),(18,'propiedades/crear-propiedad/(:any)',10),(19,'propiedades/ver-propiedad/(:any)',10),(20,'propiedades/detalles-propiedad/(:any)',10),(21,'clientes/listar-clientes',2),(22,'clientes/crear-cliente-juridico',2),(23,'clientes/crear-cliente-natural',2),(24,'clientes/ver-cliente-natural/(:any)',2),(25,'clientes/ver-cliente-juridico/(:any)',2),(26,'contactos/listar-contactos',3),(27,'contactos/crear-contacto/(:any)',3),(28,'contactos/ver-contacto/(:any)',3),(29,'modulos/listar-modulos',4),(30,'pedidos/listar',19),(31,'pedidos/crear',19),(32,'pedidos/ver/(:any)',19),(33,'actividades/listar-actividades',16),(34,'actividades/crear-actividad',16),(35,'actividades/calendario-actividades',16),(36,'actividades/pipeline-actividades',16),(37,'actividades/ver-actividad/(:any)',16),(38,'clientes_potenciales/listar-clientes-potenciales',5),(39,'clientes_potenciales/crear-cliente-potencial',5),(40,'clientes_potenciales/ver-cliente-potencial/(:any)',5),(41,'clientes_potenciales/editar-cliente-potencial/(:any)',5),(42,'documentos/listar-documentos',13),(43,'documentos/historial-documento/(:any)',13),(44,'documentos/crear-documentos',13),(45,'proyectos/listar-proyectos',9),(46,'proyectos/listar-propiedades-proyecto',9),(47,'proyectos/crear-proyecto',9),(48,'proyectos/ver-proyecto/(:any)',9),(49,'tablero_indicadores/tablero',14),(52,'planilla/listar-planilla',20),(53,'planilla/registro-tiempo',20),(54,'agentes/listar-agentes',11),(55,'agentes/crear-agente',11),(56,'agentes/ver-agente/(:any)',11),(57,'roles',6),(58,'roles/listar',6),(59,'roles/editar-permisos/(:num)',6),(60,'contabilidad/listar',21),(61,'contabilidad/crear',21),(62,'contabilidad/ver/(:any)',21),(63,'contabilidad/configuracion',21),(64,'usuarios/listar-usuarios',7),(65,'usuarios/listar-empresa',7),(66,'usuarios/crear-empresa',7),(67,'usuarios/editar-empresa',7),(68,'usuarios/agregar-usuarios',7),(69,'usuarios/ver-perfil/(:num)',7),(70,'usuarios/politicas',7),(71,'usuarios/ver-usuario/(:num)',7),(72,'usuarios/ver-usuario-admin/(:num)',7),(73,'usuarios/editar-usuario/(:num)',7),(74,'proveedores/listar',22),(75,'proveedores/crear',22),(76,'proveedores/ver/(:any)',22),(77,'ordenes_compras/crear/(:any)',22),(78,'facturas/crear/(:any)',22),(79,'pagos/crear/(:any)',22),(80,'usuarios/organizacion',7),(81,'usuarios/crear-organizacion',7),(82,'usuarios/empresas-usuario',7),(83,'aseguradoras/listar-aseguradoras',24),(84,'aseguradoras/listar-aseguradoras',23),(85,'aseguradoras/listar-aseguradoras',23),(86,'ordenes/listar',25),(87,'ordenes/crear',25),(88,'ordenes/crear',25),(89,'ordenes/ver/(:any)',25),(90,'ordenes/ver/(:any)',25),(91,'aseguradoras/crear-aseguradora',24),(92,'comisiones/listar',26),(93,'comisiones/listar',25),(94,'entrada_manual/listar',27),(95,'inventarios/listar',28),(96,'inventarios/crear',28),(97,'inventarios/ver/(:any)',28),(98,'notificaciones/listar-notificaciones',28),(99,'notificaciones/ver-notificacion/(:any)',28),(100,'aseguradoras/listar',24),(101,'aseguradoras/crear',24),(102,'aseguradoras/editar',24),(103,'entrada_manual/ver/(:any)',27),(104,'entrada_manualcrear',27),(105,'entrada_manualcrear',27),(106,'ajustes/listar',28),(107,'ajustes/listar',29),(108,'ajustes/crear',28),(109,'ajustes/ver/(:any)',29),(110,'ajustes/ver/(:any)',28),(111,'bodegas/listar',30),(112,'bodegas/crear',30),(113,'bodegas/crear',30),(114,'bodegas/ver/(:any)',30),(115,'presupuesto/listar',31),(116,'presupuesto/crear',31),(117,'presupuesto/ver/(:any)',31),(118,'inventarios/listar',26),(119,'inventarios/crear',26),(120,'inventarios/ver/(:any)',26),(121,'inventarios/ver/(:any)',26),(122,'notificaciones/listar-notificaciones',17),(123,'notificaciones/ver-notificacion/(:any)',17),(124,'ordenes/listar',23),(125,'ordenes/listar',23),(126,'ordenes/listar',23),(127,'ordenes/crear',23),(128,'ordenes/crear',23),(129,'ordenes/ver/(:any)',23),(130,'presupuesto/guardar',31),(131,'comisiones/ver/(:any)',25),(132,'entradas/listar',32),(133,'entradas/crear',32),(134,'entradas/ver/(:any)',32),(135,'entradas/listar',33),(136,'entradas/listar',34),(137,'entradas/crear',33),(138,'entradas/crear',34),(139,'entradas/listar',35),(140,'entradas/ver/(:any)',33),(141,'entradas/listar',36),(142,'entradas/listar',37),(143,'entradas/crear',35),(144,'entradas/ver/(:any)',34),(145,'entradas/crear',37),(146,'entradas/crear',36),(147,'entradas/ver/(:any)',35),(148,'entradas/ver/(:any)',36),(149,'entradas/ver/(:any)',37),(150,'presupuesto/exportar',31),(151,'entradas/listar',38),(152,'entradas/crear',38),(153,'entradas/ver/(:any)',38),(154,'entradas/listar',39),(155,'entradas/crear',39),(156,'entradas/ver/(:any)',39),(157,'entradas/listar',40),(158,'entradas/listar',41),(159,'entradas/listar',42),(160,'entradas/listar',43),(161,'entradas/crear',41),(162,'entradas/crear',40),(163,'entradas/listar',44),(164,'entradas/crear',42),(165,'entradas/crear',43),(166,'entradas/ver/(:any)',41),(167,'entradas/ver/(:any)',40),(168,'entradas/ver/(:any)',42),(169,'entradas/crear',44),(170,'entradas/ver/(:any)',43),(171,'entradas/ver/(:any)',44),(172,'entradas/listar',45),(173,'entradas/crear',45),(174,'entradas/ver/(:any)',45),(175,'entradas/listar',46),(176,'entradas/crear',46),(177,'entradas/listar',47),(178,'entradas/ver/(:any)',46),(179,'entradas/listar',48),(180,'entradas/crear',47),(181,'entradas/crear',48),(182,'entradas/ver/(:any)',47),(183,'entradas/listar',49),(184,'entradas/ver/(:any)',48),(185,'entradas/listar',50),(186,'entradas/crear',49),(187,'entradas/listar',51),(188,'entradas/crear',50),(189,'entradas/ver/(:any)',49),(190,'entradas/crear',51),(191,'entradas/ver/(:any)',50),(192,'entradas/ver/(:any)',51),(193,'entradas/listar',52),(194,'entradas/crear',52),(195,'entradas/ver/(:any)',52),(196,'entradas/listar',53),(197,'entradas/crear',53),(198,'entradas/ver/(:any)',53),(199,'entradas/listar',54),(200,'entradas/crear',54),(201,'entradas/ver/(:any)',54),(202,'entradas/listar',55),(203,'entradas/listar',56),(204,'entradas/listar',57),(205,'entradas/crear',55),(206,'entradas/crear',56),(207,'entradas/crear',57),(208,'entradas/ver/(:any)',55),(209,'entradas/ver/(:any)',57),(210,'entradas/ver/(:any)',56),(211,'entradas/listar',58),(212,'entradas/crear',58),(213,'entradas/ver/(:any)',58),(214,'entradas/listar',59),(215,'entradas/listar',60),(216,'entradas/listar',61),(217,'entradas/listar',62),(218,'entradas/crear',59),(219,'entradas/listar',63),(220,'entradas/crear',61),(221,'entradas/crear',62),(222,'entradas/crear',60),(223,'entradas/ver/(:any)',59),(224,'entradas/crear',63),(225,'entradas/ver/(:any)',62),(226,'entradas/ver/(:any)',61),(227,'entradas/ver/(:any)',63),(228,'entradas/ver/(:any)',60),(229,'entradas/listar',65),(230,'entradas/listar',64),(231,'entradas/listar',66),(232,'entradas/listar',67),(233,'entradas/crear',66),(234,'entradas/crear',65),(235,'entradas/crear',64),(236,'entradas/crear',67),(237,'entradas/ver/(:any)',66),(238,'entradas/ver/(:any)',65),(239,'entradas/ver/(:any)',67),(240,'entradas/ver/(:any)',64),(241,'entradas/listar',68),(242,'entradas/crear',68),(243,'entradas/ver/(:any)',68),(244,'entradas/listar',69),(245,'entradas/listar',70),(246,'entradas/crear',69),(247,'entradas/listar',71),(248,'entradas/crear',70),(249,'entradas/listar',72),(250,'entradas/ver/(:any)',69),(251,'entradas/ver/(:any)',70),(252,'entradas/crear',71),(253,'entradas/crear',72),(254,'entradas/ver/(:any)',71),(255,'entradas/ver/(:any)',72),(256,'entradas/listar',73),(257,'entradas/listar',74),(258,'entradas/listar',75),(259,'entradas/listar',76),(260,'entradas/crear',73),(261,'entradas/crear',76),(262,'entradas/crear',74),(263,'entradas/crear',75),(264,'entradas/ver/(:any)',73),(265,'entradas/ver/(:any)',74),(266,'entradas/ver/(:any)',76),(267,'entradas/ver/(:any)',75),(268,'planilla/listar',20),(269,'planilla/listar',20),(270,'planilla/crear',20),(271,'entradas/listar',77),(272,'entradas/listar',78),(273,'entradas/listar',79),(274,'entradas/crear',78),(275,'entradas/crear',77),(276,'entradas/listar',80),(277,'entradas/ver/(:any)',77),(278,'entradas/crear',80),(279,'entradas/ver/(:any)',78),(280,'entradas/crear',79),(281,'entradas/listar',81),(282,'entradas/listar',82),(283,'entradas/ver/(:any)',80),(284,'entradas/ver/(:any)',79),(285,'entradas/crear',81),(286,'entradas/crear',82),(287,'entradas/ver/(:any)',81),(288,'entradas/ver/(:any)',82),(289,'entradas/listar',83),(290,'entradas/crear',83),(291,'entradas/ver/(:any)',83),(292,'entradas/listar',84),(293,'entradas/crear',84),(294,'entradas/listar',85),(295,'entradas/ver/(:any)',84),(296,'entradas/listar',86),(297,'entradas/crear',85),(298,'entradas/listar',87),(299,'entradas/crear',87),(300,'entradas/listar',88),(301,'entradas/ver/(:any)',85),(302,'entradas/crear',86),(303,'entradas/ver/(:any)',87),(304,'entradas/crear',88),(305,'entradas/ver/(:any)',86),(306,'entradas/ver/(:any)',88),(307,'entradas/listar',89),(308,'entradas/crear',89),(309,'entradas/ver/(:any)',89),(310,'entradas/listar',90),(311,'entradas/crear',90),(312,'entradas/ver/(:any)',90),(313,'entradas/listar',91),(314,'entradas/crear',91),(315,'entradas/ver/(:any)',91),(316,'entradas/listar',92),(317,'entradas/crear',92),(318,'entradas/listar',93),(319,'entradas/listar',94),(320,'entradas/ver/(:any)',92),(321,'entradas/crear',93),(322,'entradas/crear',94),(323,'entradas/ver/(:any)',93),(324,'entradas/listar',95),(325,'entradas/ver/(:any)',94),(326,'entradas/crear',95),(327,'entradas/ver/(:any)',95),(328,'entradas/listar',96),(329,'entradas/listar',97),(330,'entradas/listar',98),(331,'entradas/listar',99),(332,'entradas/crear',97),(333,'entradas/crear',99),(334,'entradas/crear',98),(335,'entradas/crear',96),(336,'entradas/ver/(:any)',99),(337,'entradas/ver/(:any)',97),(338,'entradas/ver/(:any)',98),(339,'entradas/ver/(:any)',96),(340,'entradas/listar',100),(341,'entradas/crear',100),(342,'entradas/ver/(:any)',100),(343,'entradas/listar',101),(344,'entradas/crear',101),(345,'entradas/listar',102),(346,'entradas/ver/(:any)',101),(347,'entradas/listar',103),(348,'entradas/listar',104),(349,'entradas/crear',102),(350,'entradas/crear',103),(351,'entradas/listar',105),(352,'entradas/crear',104),(353,'entradas/ver/(:any)',103),(354,'entradas/ver/(:any)',102),(355,'entradas/ver/(:any)',104),(356,'entradas/crear',105),(357,'entradas/ver/(:any)',105),(358,'entradas/listar',106),(359,'entradas/crear',106),(360,'entradas/listar',107),(361,'entradas/ver/(:any)',106),(362,'entradas/crear',107),(363,'entradas/ver/(:any)',107),(364,'entradas/listar',108),(365,'entradas/listar',109),(366,'entradas/listar',110),(367,'entradas/listar',111),(368,'entradas/crear',108),(369,'entradas/crear',109),(370,'entradas/crear',110),(371,'entradas/crear',111),(372,'entradas/ver/(:any)',110),(373,'entradas/ver/(:any)',109),(374,'entradas/ver/(:any)',108),(375,'entradas/ver/(:any)',111),(376,'entradas/listar',112),(377,'entradas/crear',112),(378,'entradas/ver/(:any)',112),(379,'entradas/listar',113),(380,'entradas/crear',113),(381,'entradas/listar',114),(382,'entradas/ver/(:any)',113),(383,'entradas/crear',114),(384,'entradas/listar',115),(385,'entradas/listar',116),(386,'entradas/listar',117),(387,'entradas/ver/(:any)',114),(388,'entradas/crear',116),(389,'entradas/crear',115),(390,'entradas/crear',117),(391,'entradas/ver/(:any)',116),(392,'entradas/ver/(:any)',115),(393,'entradas/ver/(:any)',117),(394,'entradas/listar',118),(395,'entradas/listar',119),(396,'entradas/crear',118),(397,'entradas/crear',119),(398,'entradas/listar',120),(399,'entradas/listar',121),(400,'entradas/ver/(:any)',118),(401,'entradas/ver/(:any)',119),(402,'entradas/crear',121),(403,'entradas/crear',120),(404,'entradas/ver/(:any)',120),(405,'entradas/ver/(:any)',121),(406,'entradas/listar',122),(407,'entradas/crear',122),(408,'entradas/ver/(:any)',122),(409,'entradas/listar',123),(410,'entradas/crear',123),(411,'entradas/listar',124),(412,'entradas/ver/(:any)',123),(413,'entradas/listar',125),(414,'entradas/crear',124),(415,'entradas/listar',126),(416,'entradas/listar',127),(417,'entradas/ver/(:any)',124),(418,'entradas/crear',125),(419,'entradas/crear',126),(420,'entradas/crear',127),(421,'entradas/ver/(:any)',125),(422,'entradas/ver/(:any)',127),(423,'entradas/ver/(:any)',126),(424,'entradas/listar',129),(425,'entradas/listar',130),(426,'entradas/listar',128),(427,'entradas/listar',131),(428,'entradas/crear',128),(429,'entradas/crear',130),(430,'entradas/crear',131),(431,'entradas/crear',129),(432,'entradas/ver/(:any)',130),(433,'entradas/ver/(:any)',128),(434,'entradas/ver/(:any)',129),(435,'entradas/ver/(:any)',131),(436,'entradas/listar',132),(437,'entradas/listar',133),(438,'entradas/listar',134),(439,'entradas/listar',135),(440,'entradas/crear',132),(441,'entradas/crear',135),(442,'entradas/crear',133),(443,'entradas/crear',134),(444,'entradas/ver/(:any)',132),(445,'entradas/ver/(:any)',133),(446,'entradas/ver/(:any)',135),(447,'entradas/ver/(:any)',134),(448,'entradas/listar',136),(449,'entradas/listar',137),(450,'entradas/crear',136),(451,'entradas/crear',137),(452,'entradas/listar',138),(453,'entradas/ver/(:any)',137),(454,'entradas/ver/(:any)',136),(455,'entradas/crear',138),(456,'entradas/ver/(:any)',138),(457,'entradas/listar',139),(458,'entradas/listar',140),(459,'entradas/crear',139),(460,'entradas/listar',141),(461,'entradas/crear',140),(462,'entradas/crear',141),(463,'entradas/ver/(:any)',140),(464,'entradas/ver/(:any)',141),(465,'entradas/ver/(:any)',139),(466,'entradas/listar',142),(467,'entradas/crear',142),(468,'entradas/ver/(:any)',142),(469,'entradas/listar',143),(470,'entradas/crear',143),(471,'entradas/ver/(:any)',143),(472,'entradas/listar',144),(473,'entradas/listar',145),(474,'entradas/crear',144),(475,'entradas/listar',146),(476,'entradas/listar',147),(477,'entradas/crear',145),(478,'entradas/ver/(:any)',144),(479,'entradas/listar',148),(480,'entradas/crear',147),(481,'entradas/crear',146),(482,'entradas/ver/(:any)',145),(483,'entradas/ver/(:any)',147),(484,'entradas/crear',148),(485,'entradas/ver/(:any)',146),(486,'entradas/ver/(:any)',148),(487,'entradas/listar',149),(488,'entradas/crear',149),(489,'entradas/ver/(:any)',149),(490,'entradas/listar',150),(491,'entradas/listar',151),(492,'entradas/listar',152),(493,'entradas/listar',153),(494,'entradas/listar',154),(495,'entradas/crear',151),(496,'entradas/crear',150),(497,'entradas/crear',152),(498,'entradas/crear',153),(499,'entradas/crear',154),(500,'entradas/ver/(:any)',151),(501,'entradas/ver/(:any)',152),(502,'entradas/ver/(:any)',150),(503,'entradas/ver/(:any)',153),(504,'entradas/ver/(:any)',154),(505,'bodegas/listar',29),(506,'bodegas/crear',29),(507,'bodegas/ver/(:any)',29),(508,'entradas/listar',31),(509,'entradas/crear',31),(510,'entradas/crear',31),(511,'entradas/ver/(:any)',31),(512,'presupuesto/listar',30),(513,'presupuesto/crear',30),(514,'presupuesto/ver/(:any)',30),(515,'presupuesto/ver/(:any)',30),(516,'presupuesto/ver/(:any)',30),(517,'presupuesto/ver/(:any)',30),(518,'presupuesto/ver/(:any)',30),(519,'presupuesto/guardar',30),(520,'presupuesto/exportar',30),(521,'presupuesto/exportar',30),(522,'clientes/listar',2),(523,'clientes/crear',2),(524,'clientes/guardar',2),(525,'colaboradores/exportar',18),(526,'traslados/listar',32),(527,'traslados/crear',32),(528,'traslados/ver/(:any)',32),(529,'clientes/ver',2),(530,'catalogos_inventario/listar',36),(531,'cotizaciones/listar',55),(532,'catalogos_inventario/listar',33),(533,'cotizaciones/listar',37),(534,'cotizaciones/crear',37),(535,'entrada_manual/crear',27),(536,'cotizaciones/listar',213),(537,'cotizaciones/listar',214),(538,'cotizaciones/listar',215),(539,'cotizaciones/listar',216),(540,'cotizaciones/crear',213),(541,'cotizaciones/crear',214),(542,'cotizaciones/crear',215),(543,'cotizaciones/crear',216),(544,'cotizaciones/guardar',214),(545,'cotizaciones/guardar',213),(546,'cotizaciones/guardar',215),(547,'cotizaciones/guardar',216),(548,'cotizaciones/listar',217),(549,'cotizaciones/listar',218),(550,'cotizaciones/listar',219),(551,'cotizaciones/listar',220),(552,'cotizaciones/crear',217),(553,'cotizaciones/crear',219),(554,'cotizaciones/crear',218),(555,'cotizaciones/crear',220),(556,'cotizaciones/guardar',217),(557,'cotizaciones/guardar',219),(558,'cotizaciones/guardar',218),(559,'cotizaciones/guardar',220),(560,'cotizaciones/listar',221),(561,'cotizaciones/listar',222),(562,'cotizaciones/crear',222),(563,'cotizaciones/crear',221),(564,'cotizaciones/guardar',222),(565,'cotizaciones/guardar',221),(566,'cotizaciones/listar',223),(567,'cotizaciones/crear',223),(568,'cotizaciones/guardar',223),(569,'colaboradores/evaluacion',18),(570,'colaboradores/evaluacion/(:any)',18),(571,'cotizaciones/listar',224),(572,'cotizaciones/crear',224),(573,'cotizaciones/guardar',224),(574,'cotizaciones/listar',225),(575,'cotizaciones/listar',226),(576,'cotizaciones/listar',227),(577,'cotizaciones/crear',225),(578,'cotizaciones/crear',227),(579,'cotizaciones/crear',226),(580,'cotizaciones/guardar',225),(581,'cotizaciones/guardar',227),(582,'cotizaciones/listar',228),(583,'cotizaciones/guardar',226),(584,'cotizaciones/listar',229),(585,'cotizaciones/crear',228),(586,'cotizaciones/crear',229),(587,'planilla/ver/(:any)',20),(588,'cotizaciones/guardar',228),(589,'cotizaciones/guardar',229),(590,'entradas/listar',230),(591,'cotizaciones/listar',231),(592,'cotizaciones/crear',231),(593,'cotizaciones/guardar',231),(594,'entradas/listar',232),(595,'entradas/crear',232),(596,'entradas/ver/(:any)',232),(597,'actividades/listar-actividades',1),(598,'actividades/crear-actividad',1),(599,'actividades/calendario-actividades',1),(600,'agentes/listar-agentes',3),(601,'actividades/pipeline-actividades',1),(602,'agentes/crear-agente',3),(603,'ajustes/listar',6),(604,'actividades/ver-actividad/(:any)',1),(605,'ajustes/crear',6),(606,'agentes/ver-agente/(:any)',3),(607,'aseguradoras/listar',15),(608,'ajustes/ver/(:any)',6),(609,'bodegas/listar',18),(610,'aseguradoras/crear',15),(611,'aseguradoras/editar',15),(612,'bodegas/crear',18),(613,'casos/listar-casos',26),(614,'bodegas/ver/(:any)',18),(615,'clientes/listar',39),(616,'clientes_potenciales/listar-clientes-potenciales',43),(617,'casos/crear-caso/(:any)',26),(618,'colaboradores/listar',49),(619,'clientes/crear',39),(620,'clientes_potenciales/crear-cliente-potencial',43),(621,'comisiones/listar',52),(622,'colaboradores/exportar',49),(623,'casos/ver-caso/(:any)',26),(624,'clientes/ver',39),(625,'comisiones/ver/(:any)',52),(626,'clientes_potenciales/ver-cliente-potencial/(:any)',43),(627,'configuracion',56),(628,'colaboradores/crear',49),(629,'casos/historial/(:any)',26),(630,'clientes/guardar',39),(631,'clientes_potenciales/editar-cliente-potencial/(:any)',43),(632,'contabilidad/listar',58),(633,'contactos/listar-contactos',60),(634,'colaboradores/ver/(:any)',49),(635,'casos/asignarme/(:any)',26),(636,'contactos/crear-contacto/(:any)',60),(637,'contabilidad/crear',58),(638,'colaboradores/evaluacion',49),(639,'contactos/ver-contacto/(:any)',60),(640,'colaboradores/evaluacion/(:any)',49),(641,'cotizaciones/listar',73),(642,'contabilidad/ver/(:any)',58),(643,'colaboradores/configuracion',49),(644,'documentos/listar-documentos',81),(645,'cotizaciones/crear',73),(646,'contabilidad/configuracion',58),(647,'entrada_manual/listar',88),(648,'documentos/historial-documento/(:any)',81),(649,'inventarios/listar',92),(650,'documentos/crear-documentos',81),(651,'entrada_manual/ver/(:any)',88),(652,'inventarios/crear',92),(653,'cotizaciones/guardar',73),(654,'inventarios/ver/(:any)',92),(655,'modulos/listar-modulos',108),(656,'entrada_manual/crear',88),(657,'notificaciones/listar-notificaciones',113),(658,'oportunidades/listar-oportunidades',118),(659,'notificaciones/ver-notificacion/(:any)',113),(660,'ordenes/listar',122),(661,'pedidos/listar',129),(662,'ordenes/crear',122),(663,'oportunidades/crear-oportunidad/(:any)',118),(664,'pedidos/crear',129),(665,'ordenes/ver/(:any)',122),(666,'oportunidades/editar-oportunidad/(:any)',118),(667,'planilla/listar',139),(668,'pedidos/ver/(:any)',129),(669,'planilla/crear',139),(670,'oportunidades/ver-oportunidad/(:any)',118),(671,'presupuesto/listar',147),(672,'propiedades/listar-propiedades',150),(673,'planilla/ver/(:any)',139),(674,'proveedores/listar',154),(675,'presupuesto/crear',147),(676,'proveedores/crear',154),(677,'oportunidades/pipeline-de-oportunidades',118),(678,'planilla/listar-planilla',139),(679,'proyectos/listar-proyectos',157),(680,'propiedades/crear-propiedad/(:any)',150),(681,'proveedores/ver/(:any)',154),(682,'presupuesto/ver/(:any)',147),(683,'planilla/registro-tiempo',139),(684,'oportunidades/puntaje-de-oportunidades',118),(685,'ordenes_compras/crear/(:any)',154),(686,'proyectos/listar-propiedades-proyecto',157),(687,'presupuesto/guardar',147),(688,'propiedades/ver-propiedad/(:any)',150),(689,'facturas/crear/(:any)',154),(690,'presupuesto/exportar',147),(691,'proyectos/crear-proyecto',157),(692,'pagos/crear/(:any)',154),(693,'proyectos/ver-proyecto/(:any)',157),(694,'roles',166),(695,'propiedades/detalles-propiedad/(:any)',150),(696,'roles/listar',166),(697,'tablero_indicadores/tablero',182),(698,'traslados/listar',188),(699,'usuarios/organizacion',193),(700,'traslados/crear',188),(701,'roles/editar-permisos/(:num)',166),(702,'usuarios/crear-organizacion',193),(703,'traslados/ver/(:any)',188),(704,'usuarios/listar-empresa',193),(705,'usuarios/crear-empresa',193),(706,'usuarios/editar-empresa',193),(707,'usuarios/agregar-usuarios',193),(708,'usuarios/empresas-usuario',193),(709,'usuarios/ver-perfil/(:num)',193),(710,'usuarios/politicas',193),(711,'usuarios/ver-usuario/(:num)',193),(712,'usuarios/ver-usuario-admin/(:num)',193),(713,'usuarios/editar-usuario/(:num)',193),(714,'agentes/listar-agentes',2),(715,'agentes/crear-agente',2),(716,'agentes/ver-agente/(:any)',2),(717,'ajustes/listar',3),(718,'ajustes/crear',3),(719,'ajustes/crear',3),(720,'ajustes/ver/(:any)',3),(721,'ajustes/ver/(:any)',3),(722,'aseguradoras/listar',4),(723,'aseguradoras/crear',4),(724,'aseguradoras/crear',4),(725,'bodegas/listar',6),(726,'aseguradoras/crear',4),(727,'aseguradoras/editar',4),(728,'aseguradoras/editar',4),(729,'aseguradoras/editar',4),(730,'bodegas/crear',6),(731,'bodegas/ver/(:any)',6),(732,'casos/listar-casos',8),(733,'casos/crear-caso/(:any)',8),(734,'catalogos_inventario/listar',11),(735,'casos/crear-caso/(:any)',8),(736,'clientes/listar',14),(737,'clientes_potenciales/listar-clientes-potenciales',16),(738,'casos/ver-caso/(:any)',8),(739,'casos/historial/(:any)',8),(740,'clientes/crear',14),(741,'clientes/crear',14),(742,'clientes_potenciales/crear-cliente-potencial',16),(743,'casos/asignarme/(:any)',8),(744,'clientes/ver',14),(745,'clientes_potenciales/ver-cliente-potencial/(:any)',16),(746,'clientes_potenciales/editar-cliente-potencial/(:any)',16),(747,'clientes/guardar',14),(748,'colaboradores/listar',17),(749,'colaboradores/exportar',17),(750,'colaboradores/crear',17),(751,'colaboradores/ver/(:any)',17),(752,'colaboradores/evaluacion',17),(753,'colaboradores/evaluacion',17),(754,'colaboradores/evaluacion',17),(755,'colaboradores/evaluacion/(:any)',17),(756,'colaboradores/evaluacion/(:any)',17),(757,'colaboradores/configuracion',17),(758,'colaboradores/configuracion',17),(759,'colaboradores/configuracion',17),(760,'comisiones/listar',18),(761,'configuracion',20),(762,'comisiones/ver/(:any)',18),(763,'contactos/listar-contactos',22),(764,'contactos/listar-contactos',22),(765,'contactos/listar-contactos',22),(766,'contactos/listar-contactos',22),(767,'contactos/crear-contacto/(:any)',22),(768,'contactos/ver-contacto/(:any)',22),(769,'cotizaciones/listar',23),(770,'cotizaciones/listar',23),(771,'cotizaciones/listar',23),(772,'cotizaciones/listar',23),(773,'cotizaciones/crear',23),(774,'cotizaciones/crear',23),(775,'cotizaciones/guardar',23),(776,'cotizaciones/guardar',23),(777,'documentos/listar-documentos',24),(778,'documentos/listar-documentos',24),(779,'documentos/listar-documentos',24),(780,'documentos/listar-documentos',24),(781,'documentos/historial-documento/(:any)',24),(782,'documentos/crear-documentos',24),(783,'documentos/crear-documentos',24),(784,'documentos/crear-documentos',24),(785,'entradas/listar',25),(786,'entradas/crear',25),(787,'entradas/crear',25),(788,'entradas/crear',25),(789,'entradas/ver/(:any)',25),(790,'modulos/listar-modulos',29),(791,'notificaciones/listar-notificaciones',30),(792,'notificaciones/listar-notificaciones',30),(793,'notificaciones/ver-notificacion/(:any)',30),(794,'notificaciones/ver-notificacion/(:any)',30),(795,'oportunidades/listar-oportunidades',31),(796,'oportunidades/listar-oportunidades',31),(797,'oportunidades/listar-oportunidades',31),(798,'ordenes/listar',33),(799,'oportunidades/crear-oportunidad/(:any)',31),(800,'ordenes/crear',33),(801,'oportunidades/editar-oportunidad/(:any)',31),(802,'oportunidades/ver-oportunidad/(:any)',31),(803,'oportunidades/ver-oportunidad/(:any)',31),(804,'oportunidades/ver-oportunidad/(:any)',31),(805,'ordenes/ver/(:any)',33),(806,'oportunidades/pipeline-de-oportunidades',31),(807,'oportunidades/puntaje-de-oportunidades',31),(808,'pedidos/listar',34),(809,'pedidos/crear',34),(810,'pedidos/ver/(:any)',34),(811,'planilla/listar',35),(812,'presupuesto/listar',37),(813,'planilla/crear',35),(814,'planilla/crear',35),(815,'planilla/crear',35),(816,'planilla/crear',35),(817,'planilla/ver/(:any)',35),(818,'presupuesto/crear',37),(819,'planilla/listar-planilla',35),(820,'planilla/listar-planilla',35),(821,'presupuesto/ver/(:any)',37),(822,'planilla/listar-planilla',35),(823,'planilla/listar-planilla',35),(824,'planilla/listar-planilla',35),(825,'planilla/registro-tiempo',35),(826,'presupuesto/guardar',37),(827,'presupuesto/exportar',37),(828,'propiedades/listar-propiedades',38),(829,'propiedades/listar-propiedades',38),(830,'propiedades/crear-propiedad/(:any)',38),(831,'propiedades/ver-propiedad/(:any)',38),(832,'propiedades/detalles-propiedad/(:any)',38),(833,'proveedores/listar',39),(834,'proyectos/listar-proyectos',41),(835,'proveedores/crear',39),(836,'proveedores/crear',39),(837,'proveedores/ver/(:any)',39),(838,'proveedores/ver/(:any)',39),(839,'proveedores/ver/(:any)',39),(840,'proyectos/listar-propiedades-proyecto',41),(841,'ordenes_compras/crear/(:any)',39),(842,'ordenes_compras/crear/(:any)',39),(843,'ordenes_compras/crear/(:any)',39),(844,'proyectos/crear-proyecto',41),(845,'facturas/crear/(:any)',39),(846,'facturas/crear/(:any)',39),(847,'proyectos/ver-proyecto/(:any)',41),(848,'pagos/crear/(:any)',39),(849,'pagos/crear/(:any)',39),(850,'pagos/crear/(:any)',39),(851,'roles',42),(852,'roles/listar',42),(853,'roles/editar-permisos/(:num)',42),(854,'roles/editar-permisos/(:num)',42),(855,'tablero_indicadores/tablero',47),(856,'traslados/listar',49),(857,'traslados/crear',49),(858,'traslados/ver/(:any)',49),(859,'usuarios/organizacion',50),(860,'usuarios/crear-organizacion',50),(861,'usuarios/listar-empresa',50),(862,'usuarios/crear-empresa',50),(863,'usuarios/editar-empresa',50),(864,'usuarios/agregar-usuarios',50),(865,'usuarios/empresas-usuario',50),(866,'usuarios/ver-perfil/(:num)',50),(867,'usuarios/politicas',50),(868,'usuarios/ver-usuario/(:num)',50),(869,'usuarios/ver-usuario-admin/(:num)',50),(870,'usuarios/editar-usuario/(:num)',50),(871,'clientes/ver/(:any)',2),(872,'cotizaciones/ver/(:any)',37),(873,'cotizaciones/guardar',37),(874,'cotizaciones/convertir-order-venta',37),(875,'cotizaciones/listar',165),(876,'cotizaciones/guardarOrdenVenta',37),(877,'ordenes_ventas/listar',165),(878,'ordenes_ventas/listar',165),(879,'ordenes_ventas/listar',165),(880,'cotizaciones/convertir-order-venta/(:any)',37),(881,'planilla/detalles-colaborador/(:any)',20),(882,'salidas/listar',192),(883,'salidas/listar',192),(884,'salidas/listar',192),(885,'salidas/ver/(:any)',192),(886,'consumos/listar',194),(887,'consumos/crear',194),(888,'consumos/ver/(:any)',194),(889,'ordenes_ventas/crear',165),(890,'ordenes_ventas/crear',165),(891,'ordenes_ventas/ver/(:any)',165),(892,'ordenes_ventas/ver/(:any)',165),(893,'planilla/entrar-horas/(:any)',20),(894,'facturas/listar',195),(895,'facturas/crear',195),(896,'facturas/crear',195),(897,'facturas/ver/(:any)',195),(898,'ordenes_ventas/facturar/(:any)',165),(899,'ordenes_ventas/facturar/(:any)',165),(900,'colaboradores/entrega_inventario',18),(901,'colaboradores/entrega_inventario',18),(902,'facturas/guardar',195);

/*Table structure for table `recursos_has_permisos` */

DROP TABLE IF EXISTS `recursos_has_permisos`;

CREATE TABLE `recursos_has_permisos` (
  `recursos_id` int(11) NOT NULL,
  `permisos_id` int(11) NOT NULL,
  PRIMARY KEY (`recursos_id`,`permisos_id`),
  KEY `fk_recursos_has_permisos_permisos1_idx` (`permisos_id`),
  KEY `fk_recursos_has_permisos_recursos1_idx` (`recursos_id`),
  CONSTRAINT `fk_recursos_has_permisos_permisos1` FOREIGN KEY (`permisos_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_recursos_has_permisos_recursos1` FOREIGN KEY (`recursos_id`) REFERENCES `recursos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `recursos_has_permisos` */

insert  into `recursos_has_permisos`(`recursos_id`,`permisos_id`) values (4,2),(5,3),(5,4),(6,5),(7,6),(8,7),(9,8),(10,9),(11,10),(12,11),(13,12),(14,13),(15,14),(16,15),(17,16);

/*Table structure for table `regla_empresa` */

DROP TABLE IF EXISTS `regla_empresa`;

CREATE TABLE `regla_empresa` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `crear` int(11) DEFAULT NULL,
  `estado` enum('Activo','Desactivo') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `regla_empresa` */

insert  into `regla_empresa`(`id`,`crear`,`estado`) values (1,0,'Desactivo');

/*Table structure for table `relacion` */

DROP TABLE IF EXISTS `relacion`;

CREATE TABLE `relacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) DEFAULT NULL,
  `relacion_type` varchar(200) DEFAULT NULL,
  `relacion_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `relacion` */

/*Table structure for table `relacions` */

DROP TABLE IF EXISTS `relacions`;

CREATE TABLE `relacions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_orm_id` int(11) DEFAULT NULL,
  `relacion_type` varchar(200) DEFAULT NULL,
  `relacion_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

/*Data for the table `relacions` */

insert  into `relacions`(`id`,`usuario_orm_id`,`relacion_type`,`relacion_id`,`created_at`,`updated_at`) values (1,10,'Organizacion_orm',1,NULL,NULL),(2,10,'Organizacion_orm',2,NULL,NULL),(4,10,'Empresa_orm',25,NULL,NULL),(5,10,'Empresa_orm',26,NULL,NULL),(6,10,'Empresa_orm',27,NULL,NULL),(7,10,'Empresa_orm',28,NULL,NULL),(8,10,'Empresa_orm',29,NULL,NULL),(9,10,'Empresa_orm',30,NULL,NULL),(10,10,'Empresa_orm',31,NULL,NULL),(11,10,'Empresa_orm',32,NULL,NULL),(12,24,'Organizacion_orm',3,NULL,NULL),(13,24,'Empresa_orm',33,NULL,NULL),(14,10,'Comentario_orm',1,NULL,NULL),(15,10,'Comentario_orm',2,NULL,NULL),(16,10,'Comentario_orm',3,NULL,NULL),(17,10,'Comentario_orm',4,NULL,NULL);

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion` text,
  `superuser` tinyint(2) DEFAULT '0' COMMENT '1=true,0=false',
  `default` tinyint(2) DEFAULT '0' COMMENT '1=true,0=false',
  `estado` tinyint(2) DEFAULT '0',
  `link_inicial` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `empresa_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Data for the table `roles` */

insert  into `roles`(`id`,`nombre`,`descripcion`,`superuser`,`default`,`estado`,`link_inicial`,`created_at`,`updated_at`,`empresa_id`) values (1,'admin','administrador sistema',1,0,1,NULL,'2015-10-29 14:07:49','2015-10-29 14:07:51',NULL),(2,'crear_empresa',NULL,0,0,1,NULL,NULL,NULL,NULL),(3,'Invitado','invitado al sistema',1,0,0,NULL,'2015-10-29 14:55:11','2015-10-29 14:55:11',NULL),(4,'Gerente','gerente',1,0,1,NULL,'2015-10-30 09:51:10','2015-10-30 09:51:10',1),(5,'Vendedor','vendedor del sistema',0,0,1,NULL,'2016-01-06 13:43:10','2016-01-06 13:43:10',1);

/*Table structure for table `roles_permisos` */

DROP TABLE IF EXISTS `roles_permisos`;

CREATE TABLE `roles_permisos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) NOT NULL,
  `permiso_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `fk_roles_permisos_roles1` (`rol_id`),
  KEY `fk_roles_permisos_permisos1` (`permiso_id`),
  CONSTRAINT `fk_roles_permisos_permisos1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`),
  CONSTRAINT `fk_roles_permisos_roles1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `roles_permisos` */

/*Table structure for table `sal_salidas` */

DROP TABLE IF EXISTS `sal_salidas`;

CREATE TABLE `sal_salidas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_salida` binary(16) NOT NULL,
  `prefijo` varchar(20) NOT NULL,
  `numero` int(8) unsigned zerofill NOT NULL,
  `estado_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `operacion_id` int(11) NOT NULL,
  `operacion_type` varchar(100) NOT NULL,
  `comentarios` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

/*Data for the table `sal_salidas` */

insert  into `sal_salidas`(`id`,`uuid_salida`,`prefijo`,`numero`,`estado_id`,`created_at`,`updated_at`,`created_by`,`empresa_id`,`operacion_id`,`operacion_type`,`comentarios`) values (1,'ÂπiÏ÷Ùpú∆ºvNT†','SAL',00000001,1,'2016-01-01 00:00:00','2016-01-01 00:00:00',1,1,9,'Traslados_orm','Comentarios'),(2,'ÂπiÏ÷˜@ú∆ºvNT†','SAL',00000002,2,'2016-01-01 00:00:00','2016-01-19 10:35:41',1,1,8,'Traslados_orm','Comentarios'),(3,'ÂπiÏ÷¯*ú∆ºvNT†','SAL',00000003,1,'2016-01-01 00:00:00','2016-01-01 00:00:00',1,1,7,'Traslados_orm','Comentarios'),(4,'Â∫wô9ôú∆ºvNT†','SAL',00000004,1,'2016-01-01 00:00:00','2016-01-01 00:00:00',1,1,1,'Consumos_orm','comentarios'),(5,'Â∫wô<Ìú∆ºvNT†','SAL',00000005,1,'2016-01-01 00:00:00','2016-01-01 00:00:00',1,1,2,'Consumos_orm','comentarios'),(6,'Â∫wô=›ú∆ºvNT†','SAL',00000006,1,'2016-01-01 00:00:00','2016-01-01 00:00:00',1,1,1,'Orden_ventas_orm','comentarios'),(7,'Â∫wô>œú∆ºvNT†','SAL',00000007,1,'2016-01-01 00:00:00','2016-01-01 00:00:00',1,1,2,'Orden_ventas_orm','comentarios'),(8,'ÂæÂÕ¡ú∆ºvNT†','SAL',00000008,2,'2016-01-19 14:49:50','2016-01-19 14:51:12',1,1,10,'Traslados_orm','ya se envi√≥'),(9,'Â¡j2kú∆ºvNT†','SAL',00000009,2,'2016-01-22 10:40:07','2016-01-22 10:42:57',1,1,11,'Traslados_orm','SAIA Truck with a black freight'),(10,'Â√sÂkBÇlbfäa•','SAL',00000010,1,'2016-01-25 09:57:16','2016-01-25 09:57:16',10,1,4,'Orden_ventas_orm',''),(11,'Â√tæ≠7’Çlbfäa•','SAL',00000011,1,'2016-01-25 10:03:21','2016-01-25 10:03:21',10,1,4,'Orden_ventas_orm',''),(12,'Â√uí’ñÇlbfäa•','SAL',00000012,1,'2016-01-25 10:05:38','2016-01-25 10:05:38',10,1,4,'Orden_ventas_orm',''),(13,'Â√uì∞‹ÏÇlbfäa•','SAL',00000013,1,'2016-01-25 10:09:18','2016-01-25 10:09:18',10,1,4,'Orden_ventas_orm',''),(14,'Â√vVaO’Çlbfäa•','SAL',00000014,1,'2016-01-25 10:14:45','2016-01-25 10:14:45',10,1,4,'Orden_ventas_orm',''),(15,'Â√wsCÌÇlbfäa•','SAL',00000015,1,'2016-01-25 10:19:53','2016-01-25 10:19:53',10,1,4,'Orden_ventas_orm',''),(16,'Â√w\"¯9*Çlbfäa•','SAL',00000016,1,'2016-01-25 10:20:28','2016-01-25 10:20:28',10,1,4,'Orden_ventas_orm',''),(17,'Â√xÜÄ¸Çlbfäa•','SAL',00000017,1,'2016-01-25 10:30:24','2016-01-25 10:30:24',10,1,4,'Orden_ventas_orm',''),(18,'Â√z(á§Çlbfäa•','SAL',00000018,1,'2016-01-25 10:42:06','2016-01-25 10:42:06',10,1,14,'Orden_ventas_orm',''),(19,'Â√{a8ÛqÇlbfäa•','SAL',00000019,1,'2016-01-25 10:50:50','2016-01-25 10:50:50',10,1,11,'Orden_ventas_orm',''),(20,'Â√|#’¬Çlbfäa•','SAL',00000020,1,'2016-01-25 10:56:17','2016-01-25 10:56:17',10,1,11,'Orden_ventas_orm',''),(21,'Â√|”ûÇlbfäa•','SAL',00000021,1,'2016-01-25 11:01:11','2016-01-25 11:01:11',10,1,11,'Orden_ventas_orm',''),(22,'Â√|·ïlÇlbfäa•','SAL',00000022,1,'2016-01-25 11:01:35','2016-01-25 11:01:35',10,1,11,'Orden_ventas_orm',''),(23,'Â√}9i9SÇlbfäa•','SAL',00000023,1,'2016-01-25 11:04:03','2016-01-25 11:04:03',10,1,11,'Orden_ventas_orm',''),(24,'Â√}◊êT¿Çlbfäa•','SAL',00000024,1,'2016-01-25 11:08:28','2016-01-25 11:08:28',10,1,11,'Orden_ventas_orm',''),(25,'Â√~√œçKÇlbfäa•','SAL',00000025,1,'2016-01-25 11:15:04','2016-01-25 11:15:04',10,1,11,'Orden_ventas_orm',''),(26,'Â√ÍüÇlbfäa•','SAL',00000026,1,'2016-01-25 11:17:17','2016-01-25 11:17:17',10,1,11,'Orden_ventas_orm',''),(27,'Â√Œ∑ÃåÇlbfäa•','SAL',00000027,1,'2016-01-25 11:22:32','2016-01-25 11:22:32',10,1,11,'Orden_ventas_orm',''),(28,'Â√˝Mh5Çlbfäa•','SAL',00000028,1,'2016-01-25 11:23:50','2016-01-25 11:23:50',10,1,11,'Orden_ventas_orm',''),(29,'Â√Ä5qÉ\0Çlbfäa•','SAL',00000029,1,'2016-01-25 11:25:24','2016-01-25 11:25:24',10,1,11,'Orden_ventas_orm',''),(30,'Â√Äw®üÇlbfäa•','SAL',00000030,1,'2016-01-25 11:27:15','2016-01-25 11:27:15',10,1,9,'Orden_ventas_orm',''),(31,'Â√Ä°C˝Çlbfäa•','SAL',00000031,1,'2016-01-25 11:28:25','2016-01-25 11:28:25',10,1,9,'Orden_ventas_orm',''),(32,'Â√Ä‰b?«Çlbfäa•','SAL',00000032,1,'2016-01-25 11:30:18','2016-01-25 11:30:18',10,1,11,'Orden_ventas_orm',''),(33,'Â√ÅÅÌ8Çlbfäa•','SAL',00000033,1,'2016-01-25 11:34:41','2016-01-25 11:34:41',10,1,11,'Orden_ventas_orm',''),(34,'Â√ÅÊ]7∏Çlbfäa•','SAL',00000034,1,'2016-01-25 11:37:31','2016-01-25 11:37:31',10,1,11,'Orden_ventas_orm',''),(35,'Â√Çì\nÇlbfäa•','SAL',00000035,1,'2016-01-25 11:38:43','2016-01-25 11:38:43',10,1,11,'Orden_ventas_orm',''),(36,'Â√Ñ@–ÆzÇlbfäa•','SAL',00000036,1,'2016-01-25 11:54:21','2016-01-25 11:54:21',10,1,15,'Orden_ventas_orm',''),(37,'Â√âõ)ØÇlbfäa•','SAL',00000037,1,'2016-01-25 12:32:40','2016-01-25 12:32:40',10,1,15,'Orden_ventas_orm',''),(38,'Â√ç¬ÒaﬁÇlbfäa•','SAL',00000038,1,'2016-01-25 13:02:25','2016-01-25 13:02:25',10,1,17,'Orden_ventas_orm',''),(39,'Â√éÃZ$ÃÇlbfäa•','SAL',00000039,1,'2016-01-25 13:09:50','2016-01-25 13:09:50',10,1,2,'Orden_ventas_orm',''),(40,'Â√é·ÇbÈÇlbfäa•','SAL',00000040,1,'2016-01-25 13:10:26','2016-01-25 13:10:26',10,1,2,'Orden_ventas_orm',''),(41,'Â√ë.Çlbfäa•','SAL',00000041,1,'2016-01-25 13:26:54','2016-01-25 13:26:54',10,1,3,'Orden_ventas_orm',''),(42,'Â√î:Z	?Çlbfäa•','SAL',00000042,1,'2016-01-25 13:48:43','2016-01-25 13:48:43',10,1,4,'Orden_ventas_orm',''),(43,'Â√î¢ûrÇlbfäa•','SAL',00000043,1,'2016-01-25 13:51:37','2016-01-25 13:51:37',10,1,4,'Orden_ventas_orm',''),(44,'Â√ôë-ñíÇlbfäa•','SAL',00000044,1,'2016-01-25 14:26:56','2016-01-25 14:26:56',10,1,2,'Orden_ventas_orm',''),(45,'Âƒ-OE^®Çlbfäa•','SAL',00000045,1,'2016-01-26 08:04:31','2016-01-26 08:04:31',10,1,2,'Orden_ventas_orm',''),(46,'Âƒ.Î}›Çlbfäa•','SAL',00000046,1,'2016-01-26 08:16:11','2016-01-26 08:16:11',10,1,5,'Orden_ventas_orm',''),(47,'Âƒ4∫Çlbfäa•','SAL',00000047,1,'2016-01-26 08:55:57','2016-01-26 08:55:57',10,1,6,'Orden_ventas_orm',''),(48,'Âƒ:Hó•√Çlbfäa•','SAL',00000048,1,'2016-01-26 09:37:23','2016-01-26 09:37:23',10,1,2,'Orden_ventas_orm',''),(49,'Â≈-óoı‘Çlbfäa•','SAL',00000049,1,'2016-01-27 14:39:03','2016-01-27 14:39:03',10,1,6,'Factura_orm',''),(50,'Â≈90`Çlbfäa•','SAL',00000050,1,'2016-01-27 16:01:13','2016-01-27 16:01:13',10,1,8,'Factura_orm',''),(51,'Â≈ﬂªZ∫Çlbfäa•','SAL',00000051,1,'2016-01-28 11:49:42','2016-01-28 11:49:42',10,1,1,'Factura_orm',''),(52,'Â≈ﬂÑ˙í!Çlbfäa•','SAL',00000052,1,'2016-01-28 11:52:42','2016-01-28 11:52:42',10,1,6,'Factura_orm',''),(53,'Â∆°NÙ}Çlbfäa•','SAL',00000053,1,'2016-01-29 10:59:52','2016-01-29 10:59:52',10,1,7,'Orden_ventas_orm','');

/*Table structure for table `sal_salidas_campos` */

DROP TABLE IF EXISTS `sal_salidas_campos`;

CREATE TABLE `sal_salidas_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

/*Data for the table `sal_salidas_campos` */

insert  into `sal_salidas_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'fecha','Fecha',0,14,'activo','{\"class\":\"form-control\",\"disabled\":\"true\"}','','div','',1,'','0000-00-00 00:00:00',2),(2,'destino','Destino',0,18,'activo','{\"class\":\"chosen destino\",\"disabled\":\"true\"}','','div','',1,'','0000-00-00 00:00:00',4),(3,'bodega_salida','Bodega de salida',0,18,'activo','{\"class\":\"chosen\",\"disabled\":\"true\"}','','div','bod_bodegas',1,'','0000-00-00 00:00:00',6),(4,'estado','Estado ',0,12,'activo','{\"class\":\"chosen estado\"}','','div','',1,'','0000-00-00 00:00:00',8),(5,'numero_salida','N&uacute;mero de salida',0,14,'inactivo','{\"class\":\"form-control\",\"disabled\":\"true\"}','','div','',0,'','0000-00-00 00:00:00',10),(6,'numero_documento','N&uacute;mero de documento',0,14,'activo','{\"class\":\"form-control\",\"disabled\":\"true\"}','','div','',0,'','0000-00-00 00:00:00',12),(7,'comentarios','Comentarios',0,14,'activo','{\"class\":\"form-control comentarios\",\"data-columns\":\"2\"}','','div','',0,'','0000-00-00 00:00:00',16),(8,'item','Item',0,18,'activo','{\"class\":\"chosen item\",\"disabled\":\"true\"}','items','tabla-dinamica','inv_items',1,'','0000-00-00 00:00:00',18),(9,'descripcion','Descripci&oacute;n',0,14,'activo','{\"class\":\"form-control descripcion\",\"disabled\":\"true\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',20),(10,'observacion','Observaci&oacute;n',0,14,'activo','{\"class\":\"form-control observacion\",\"disabled\":\"true\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',22),(11,'cuenta','Cuenta de gasto',0,18,'activo','{\"class\":\"chosen cuenta\",\"disabled\":\"true\"}','items','tabla-dinamica','gastos',1,'','0000-00-00 00:00:00',24),(12,'cantidad_enviada','Cantidad enviada',0,14,'activo','{\"class\":\"form-control cantidad_enviada\",\"disabled\":\"true\"}','items','tabla-dinamica','',1,'','0000-00-00 00:00:00',26),(13,'unidad','Unidad',0,18,'activo','{\"class\":\"chosen unidad\",\"disabled\":\"true\"}','items','tabla-dinamica','inv_unidades',1,'','0000-00-00 00:00:00',28),(14,'guardarEntrada','Guardar',0,13,'activo','{\"class\":\"btn btn-primary btn-block btnGuardar\"}','','div','',0,'','0000-00-00 00:00:00',32),(15,'cancelarEntrada','Cancelar',0,8,'activo','','','div','',0,'salidas/listar','0000-00-00 00:00:00',30);

/*Table structure for table `sal_salidas_cat` */

DROP TABLE IF EXISTS `sal_salidas_cat`;

CREATE TABLE `sal_salidas_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `sal_salidas_cat` */

insert  into `sal_salidas_cat`(`id_cat`,`id_campo`,`valor`,`etiqueta`) values (1,4,'','Por enviar'),(2,4,'','Enviado');

/*Table structure for table `seg_aseguradoras` */

DROP TABLE IF EXISTS `seg_aseguradoras`;

CREATE TABLE `seg_aseguradoras` (
  `id_aseguradora` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_aseguradora` binary(16) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `ruc` varchar(100) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `descuenta_comision` tinyint(1) DEFAULT NULL,
  `imagen_archivo` varchar(200) DEFAULT NULL,
  `creado_por` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_aseguradora`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `seg_aseguradoras` */

insert  into `seg_aseguradoras`(`id_aseguradora`,`uuid_aseguradora`,`nombre`,`ruc`,`telefono`,`email`,`direccion`,`descuenta_comision`,`imagen_archivo`,`creado_por`,`created_at`,`updated_at`) values (1,'ÂäS9.Õï„ºvNT†','Aseguradora de prueba','298914220','12345','aseg@aseg.com','Direccion',NULL,NULL,NULL,NULL,NULL),(2,'Âé\ZßÄ˙‡ï„ºvNT†','Aseg','RUC','1234','g@g.com','s',NULL,NULL,NULL,'2015-11-18 12:34:43',NULL),(3,'Âé,iøƒüï„ºvNT†','Assa','867324868672345','233-5555','ghsassa@gmail.com','Calle 50',NULL,NULL,NULL,'2015-11-18 14:41:50',NULL);

/*Table structure for table `seg_aseguradoras_campos` */

DROP TABLE IF EXISTS `seg_aseguradoras_campos`;

CREATE TABLE `seg_aseguradoras_campos` (
  `id_campo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_campo` varchar(200) NOT NULL,
  `etiqueta` varchar(255) NOT NULL,
  `longitud` int(5) DEFAULT NULL,
  `id_tipo_campo` int(11) NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `atributos` varchar(200) DEFAULT NULL,
  `agrupador_campo` varchar(100) DEFAULT NULL,
  `contenedor` enum('div','tabla-dinamica','tabla-dinamica-sumativa') DEFAULT 'div',
  `tabla_relacional` varchar(150) DEFAULT NULL,
  `requerido` tinyint(2) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `fecha_cracion` datetime DEFAULT NULL,
  `posicion` int(3) DEFAULT NULL,
  PRIMARY KEY (`id_campo`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `seg_aseguradoras_campos` */

insert  into `seg_aseguradoras_campos`(`id_campo`,`nombre_campo`,`etiqueta`,`longitud`,`id_tipo_campo`,`estado`,`atributos`,`agrupador_campo`,`contenedor`,`tabla_relacional`,`requerido`,`link_url`,`fecha_cracion`,`posicion`) values (1,'nombre','Nombre',NULL,14,'activo',NULL,NULL,'div',NULL,1,NULL,NULL,2),(2,'ruc','R.U.C.',NULL,14,'activo',NULL,NULL,'div',NULL,1,NULL,NULL,3),(3,'telefono','Tel√©fono',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,4),(4,'email','E-mail',NULL,14,'activo','{\"data-rule-email\":\"true\"}',NULL,'div',NULL,NULL,NULL,NULL,5),(5,'direccion','Direcci√≥n',NULL,14,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,6),(6,'descuenta_comision','Se Descuenta Comisi√≥n',NULL,2,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,7),(7,'imagen_archivo','',NULL,6,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,1),(8,'cancelarFormBtn','Cancelar',NULL,8,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,8),(9,'guardarFormBtn','Guardar',NULL,13,'activo',NULL,NULL,'div',NULL,NULL,NULL,NULL,9);

/*Table structure for table `seg_aseguradoras_cat` */

DROP TABLE IF EXISTS `seg_aseguradoras_cat`;

CREATE TABLE `seg_aseguradoras_cat` (
  `id_cat` int(11) NOT NULL AUTO_INCREMENT,
  `id_campo` int(11) NOT NULL,
  `valor` varchar(200) NOT NULL,
  `etiqueta` varchar(200) NOT NULL,
  PRIMARY KEY (`id_cat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `seg_aseguradoras_cat` */

/*Table structure for table `usuarios` */

DROP TABLE IF EXISTS `usuarios`;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uuid_usuario` binary(16) DEFAULT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `apellido` varchar(45) DEFAULT NULL,
  `usuario` varchar(45) DEFAULT NULL,
  `telefono` varchar(45) DEFAULT NULL,
  `extension` varchar(45) DEFAULT NULL,
  `estado` enum('Activo','Expirado','Pendiente','Inactivo') DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `imagen_archivo` varchar(45) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_login_ip_address` varchar(45) DEFAULT NULL,
  `login_attemps` tinyint(5) DEFAULT NULL,
  `login_attempts_time` datetime DEFAULT NULL,
  `recovery_token` varchar(255) DEFAULT NULL,
  `recovery_time` datetime DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `last_recovery_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `uuid_UNIQUE` (`uuid_usuario`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

/*Data for the table `usuarios` */

insert  into `usuarios`(`id`,`uuid_usuario`,`nombre`,`apellido`,`usuario`,`telefono`,`extension`,`estado`,`email`,`imagen_archivo`,`fecha_creacion`,`ip_address`,`last_login`,`last_login_ip_address`,`login_attemps`,`login_attempts_time`,`recovery_token`,`recovery_time`,`password`,`last_recovery_time`) values (10,'Ât!¡!?§abfäa•','Rafael','Williams','rwilliams@pensanomica.com',NULL,NULL,'Activo','rwilliams@pensanomica.com',NULL,'2015-10-16 10:11:25','::1','2015-10-22 09:21:25','::1',NULL,NULL,'5011625a1f20f538118c2dec432069d1',NULL,'AqzFxTlLXwclOZjkNU4csBKcGHlzHCmZ8Za6TwXM22Clf/9KOgHFC+fNsVmTYAR2aYcpfHPruPwIfRyRh8XitQ==',NULL),(11,'Âx3ö¡ÅÜå•bfäa•','Juan','magan','jmagan@gmail.com',NULL,NULL,'Pendiente','jmagan@gmail.com',NULL,'2015-10-21 15:37:53',NULL,NULL,NULL,NULL,NULL,'0ccb024cfe8bc8e69441a6259b35705a',NULL,'chi8AAOHs6awTHKuasEj7Vh3dvGy5oWVkVY6lu8MKDwqRgOqYtzp1H3u93Qid+74Se55AJOGOMeycH5hOsfYdg==',NULL),(12,'Âx4ÎãNå•bfäa•','Pancracio','Usuario','pusuario@gmail.com',NULL,NULL,'Pendiente','pusuario@gmail.com',NULL,'2015-10-21 15:41:32',NULL,NULL,NULL,NULL,NULL,'f22fd751355d296a7ee3245d37b254b1',NULL,'tyz5fUydEMr7mDw/1RnJUiFRhC83WqpTUhlEGoh0Bmv1RN9s7wZDIIaxh8acDcCR+Vt85fV8uL0vfwSE2hQaQA==',NULL),(13,'Âx9h~å•bfäa•','Datos','Zeballos','dzeballos@gmail.com',NULL,NULL,'Pendiente','dzeballos@gmail.com',NULL,'2015-10-21 16:19:26',NULL,NULL,NULL,NULL,NULL,'947c1a64f5c8895a7f71a1e52de968ce',NULL,'SXGiAeBsG4UDt/1UhgxWpXBcr2TWgQjNtB8p/uTehpw5f2T5oe/DReVH+9sc1vYT3dnNadtqMn/7h3lxzk4shw==',NULL),(14,'Âx9µÖ∏$å•bfäa•','karima','miranda','kmiranda@gmail.com',NULL,NULL,'Pendiente','kmiranda@gmail.com',NULL,'2015-10-21 16:21:35',NULL,NULL,NULL,NULL,NULL,'16305daccb199a1f35dab2c2ce5553ca',NULL,'SBqLSMUaK3Qo6n8epifyW6SicvmZEloT3//Vp7HttDo8XRyFiL0D0cnFaUmnsJpx+45bu7gUOV3AgHILHkUvhw==',NULL),(15,'Âx:ä».å•bfäa•','karima','miranda','kmiranda1@gmail.com',NULL,NULL,'Pendiente','kmiranda1@gmail.com',NULL,'2015-10-21 16:27:32',NULL,NULL,NULL,NULL,NULL,'350be359cce0abefc75de6712c3cfb93',NULL,'LUrTXP4B7dg40+uN7Lwb7mXrSiv1e7O+2lBgAytTBeNSb3wz6SgA8rgHBEnrcniFzdMfmUbiHFceTFbaQBd5mQ==',NULL),(16,'Âx:È€¬‚å•bfäa•','karima','miranda','kmiranda1e@gmail.com',NULL,NULL,'Pendiente','kmiranda1e@gmail.com',NULL,'2015-10-21 16:30:13',NULL,NULL,NULL,NULL,NULL,'ff6ea5ef2fe7a57c2d509e77074752e3',NULL,'aBQ2OOpLYnlxX/5lbCu2T4TBtUdvGJqmyPjNqwglYRrw+D+ilz1776X3h9ZmaZAU9AQLtmAB2bFiW2y9Em+hOA==',NULL),(17,'Âx;¿øå•bfäa•','karima','miranda','kmiranda1e1@gmail.com',NULL,NULL,'Pendiente','kmiranda1e1@gmail.com',NULL,'2015-10-21 16:31:40',NULL,NULL,NULL,NULL,NULL,'21955535c194afe2205fb42444bfd804',NULL,'eQR/qPb8Pj0N/5F8zgjG1YRIqXyB2XjnA6E6diGEpA7RDh8YRgZ6+V2PlmahwNy0LV0H/W0P9I/+g4K3s08fyA==',NULL),(18,'Âx<%$^´å•bfäa•','yut','pass','ypass@gmail.com',NULL,NULL,'Pendiente','ypass@gmail.com',NULL,'2015-10-21 16:39:02',NULL,NULL,NULL,NULL,NULL,'5e5a75549578a0f73140fdcd234bd1b2',NULL,'StIKVKoa5BsG4hfgMuSx1TURO+okQ7VY/B7oPpRKRUG6zjM7xigJPIrktV5c4xuUOIKa85rVy5R21/TSq3dq9A==',NULL),(19,'Âx<¨Õˆå•bfäa•','test','test','testtest@gmail.com',NULL,NULL,'Pendiente','testtest@gmail.com',NULL,'2015-10-21 16:42:49',NULL,NULL,NULL,NULL,NULL,'c89b8d6436f7a988773775f295349bae',NULL,'Hy5mNjUWGmwXxMb1tpKVpToQMwrYusD/rdMiQrCi7RcJ1qZL96EVE9m327SUXnoWPi2qFPqnRHpr96TyF6nbIA==',NULL),(20,'Âx=%“êå•bfäa•','usernama','password','username@gmail.com',NULL,NULL,'Pendiente','username@gmail.com',NULL,'2015-10-21 16:46:12',NULL,NULL,NULL,NULL,NULL,'','0000-00-00 00:00:00','HR+nnSMsaM6BOs0tZuRCzz77sYN7OXtjeyLHa3Y+ZyK/qk74tGDfUIDDfJB5jPXBD+nmM/K3/rwaG7HmxHbAuw==','2015-10-30 15:52:11'),(21,'Âx=ËñU†å•bfäa•','usernama','password','username1@gmail.com',NULL,NULL,'Pendiente','username1@gmail.com',NULL,'2015-10-21 16:51:39',NULL,NULL,NULL,NULL,NULL,'b48d0f0d8cb87fae6ccdf2915ea362cd',NULL,'FBQ/Pr4iFzSGGRsBHxU4OXJm1yl0Rdhv/rjjhM3ES3L7DtANPTRsvjsq0Wh6+r5kM5ndZWw3xff6NtoRxaSkCg==',NULL),(22,'Â}µïß˛∫±bfäa•','Azort','Test','boot@pensa.com',NULL,NULL,'Pendiente','boot@pensa.com',NULL,'2015-10-28 15:50:55',NULL,NULL,NULL,NULL,NULL,'8BYFpTMsmqmxbWR1eNWvfbl6vvo5CWUqr5ZLqwwnjSVkR7VotAVGWEv7sDsr80N','2015-10-30 03:02:23','SlH4teUmBkaHbej3YcAJ2t7wV79xtyy/dpq22CUdw5LyRZFLvzev1gMEvzEZ3txjIMm7DNV/awfHwBd1BWZ0lQ==',NULL),(23,'Â}ΩÅtÕ±bfäa•','azora','Test','root@azora.com',NULL,NULL,'Activo','root@azora.com',NULL,'2015-10-28 16:47:37',NULL,NULL,NULL,NULL,NULL,'02a7f44356d507c7227f610010133d31',NULL,'hkVTKrg5wo31M2qDMI113pDYkC9KjdhQe/I2/hpOPoAf9FuMBGTiOhIzdKpyk9XO/SDSA8zE18teW17H1PJg9g==',NULL),(24,'Â~x;‘º±bfäa•','Benancio','Pimentel','bpimentel@gmail.com',NULL,NULL,'Activo','bpimentel@gmail.com',NULL,'2015-10-29 15:04:16','::1','2015-10-29 15:04:16','::1',NULL,NULL,'','0000-00-00 00:00:00','jNZDCJFOv24sP24GruLZiJ+nDmGV6YtLC86Y5lt6J1bij6uhyR3er2etQoeYGWOiLG0soqZd3EReEp/agD2tJQ==','2015-10-30 15:58:31'),(25,'Â∏äŸÓ±bfäa•','Don','Omar','don@gmail.com',NULL,NULL,'Pendiente','don@gmail.com',NULL,'2015-10-30 10:48:52',NULL,NULL,NULL,NULL,NULL,'e55ba7e91e63a44f9c65bef1b8a5ab51',NULL,'DeNK+xAGsfBVfRaVbMFG3x6Fin1BYqFokbwWfqX6GCXj1SBFfErWlwLCh6XIfCEWQokIyWBOrQoWPgQLW0nNwQ==',NULL),(26,'Â≥£tZëûbfäa•','Rafa','Williams','rafael.will@gmail.com',NULL,NULL,'Pendiente','rafael.will@gmail.com',NULL,'2016-01-04 11:53:48',NULL,NULL,NULL,NULL,NULL,'b689acd82157f446a609954b6e0d1a92',NULL,'kKNh+BEugwLshvt0MqMUwiu4mxws3M38c/e0vxXuAUv0CW55iFECKye+k/xyHOhuqLMn+3CK3f9GXsa4kr7s6g==',NULL),(27,'Â≥<Nˇëûbfäa•','Rafa','Foo','rafael.williams@gmail.com',NULL,NULL,'Activo','rafael.williams@gmail.com',NULL,'2016-01-04 13:09:38',NULL,NULL,NULL,NULL,NULL,'f9e36f0efa1c334fc78a1dafdbfd5b0c',NULL,'2X253MlHkMOuave+2h59gtXWLH006oNqdYHK5u2+8xeLI1q2/R7hCKYFu2YrKU6gxAPWuC0xZvUXLLvBy0TkJg==',NULL),(28,'Â¥•ﬁ∞iµëûbfäa•','Foo','Bar','foobar@pensanomica.com',NULL,NULL,'Activo','foobar@pensanomica.com',NULL,'2016-01-06 13:47:37',NULL,NULL,NULL,NULL,NULL,'ddf324a5bfbe4654e23c40fd96a4033e',NULL,'k9MSVzItwC/+LMY75yxQTRW+CPS+WQ4L/ERKT8rFUAcgSCHTobRr1HcdtF2bPKmaUa1EYrblTX41JyMwwd2IBQ==',NULL);

/*Table structure for table `usuarios_has_empresas` */

DROP TABLE IF EXISTS `usuarios_has_empresas`;

CREATE TABLE `usuarios_has_empresas` (
  `usuario_id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `default` tinyint(1) DEFAULT '0' COMMENT 'empresa default 0 false 1 true',
  `estado` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`usuario_id`,`empresa_id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `usuario_empresa` (`usuario_id`,`empresa_id`),
  KEY `fk_usuarios_has_Empresas_Empresas1_idx` (`empresa_id`),
  KEY `fk_usuarios_has_Empresas_usuarios_idx` (`usuario_id`),
  KEY `index_default` (`default`),
  KEY `index_empresa_id` (`empresa_id`),
  CONSTRAINT `fk_usuarios_has_Empresas_usuarios` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8;

/*Data for the table `usuarios_has_empresas` */

insert  into `usuarios_has_empresas`(`usuario_id`,`empresa_id`,`id`,`default`,`estado`) values (10,1,1,1,0),(10,2,2,0,0),(10,3,3,0,0),(10,16,5,0,0),(10,17,6,0,0),(10,18,7,0,0),(10,19,8,0,0),(10,20,9,0,0),(10,21,10,0,0),(10,22,11,0,0),(10,23,12,0,0),(10,25,25,0,0),(10,26,26,0,0),(10,27,27,0,0),(10,28,28,0,0),(10,29,29,0,0),(10,30,37,0,0),(10,31,38,0,0),(10,32,39,0,0),(11,1,13,0,0),(12,1,14,0,0),(13,1,15,0,0),(14,1,16,0,0),(15,1,17,0,0),(16,1,18,0,0),(17,1,19,0,0),(18,1,20,0,0),(19,1,21,0,0),(20,1,22,0,0),(21,1,23,0,0),(22,1,30,1,0),(23,1,31,1,0),(23,25,36,0,0),(24,33,40,1,0),(25,30,41,1,0),(26,2,42,1,0),(26,28,46,0,0),(27,2,44,1,0),(27,26,45,0,0),(27,28,47,0,0),(28,1,48,1,0);

/*Table structure for table `usuarios_has_roles` */

DROP TABLE IF EXISTS `usuarios_has_roles`;

CREATE TABLE `usuarios_has_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `empresa_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`usuario_id`,`empresa_id`,`role_id`),
  KEY `fk_usuarios_has_roles_usuarios1_idx` (`usuario_id`),
  KEY `fk_usuarios_has_roles_empresas1_idx` (`empresa_id`),
  KEY `fk_usuarios_has_roles_roles1_idx` (`role_id`),
  CONSTRAINT `fk_usuarios_has_roles_roles1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_usuarios_has_roles_usuarios1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

/*Data for the table `usuarios_has_roles` */

insert  into `usuarios_has_roles`(`id`,`usuario_id`,`empresa_id`,`role_id`) values (1,10,30,1),(11,10,1,1),(2,22,1,3),(3,23,1,3),(6,23,25,3),(7,24,33,2),(8,25,30,3),(9,25,30,4),(12,26,2,3),(13,26,2,4),(18,26,28,3),(19,26,28,4),(14,27,2,3),(15,27,2,4),(16,27,26,3),(17,27,26,4),(20,27,28,2),(21,27,28,4),(22,28,1,3),(23,28,1,5);

/*Table structure for table `usuarios_passwords_logs` */

DROP TABLE IF EXISTS `usuarios_passwords_logs`;

CREATE TABLE `usuarios_passwords_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuarios_passwords` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Data for the table `usuarios_passwords_logs` */

insert  into `usuarios_passwords_logs`(`id`,`id_usuario`,`creacion`,`password`) values (1,23,'2015-04-30 18:28:20','CV07cn99SwxVx9Kxsm8ZADnrLNUF4oX+hsv5fTab8aK3aVA1hhI2KudunGrAyAj8O+lVBC1Kz8X6jHbnx9esxw=='),(2,23,'2015-04-30 18:28:36','Shf2xfGFdu9rwkTyZHX9KpoB2pw50Yd+TedthjD1W5AU92GxAhN0XvGtA3zNS8+ctaQty5zPEJ4NzutWhb4NnA=='),(3,26,'2015-05-04 07:33:15','4lxvKf9oJHMvN9ryTLRq14oCuPLR/xn2ZqJTEP0aiMJHs2oIVS0W1pXyIieQvXL4gIlJPkKZ266eMTAaCKRFUw=='),(4,32,'2015-05-04 12:55:16','SssO4vl1xQdxiARSXEEWY8uWx2p3sZSIXH5Uk7IlSHOiW7A5w6EoKwhnjznq09YlG0yFMGcDQhztXxUNd2YV5A=='),(5,33,'2015-05-04 13:03:10','Gh3EMsGIrYcs6/X+fGhZZG+GmuW97Rr0La5GoX0U/fLVyGvZGs1As8GvU3IObtoZ09hcSN9O1+53p6jC4eKaTQ=='),(6,34,'2015-05-04 13:41:53','5WglVJfYVkNUW4bakOrBiF5qMi7E4t6yIT1u2mJgL5zPj3XWTFaq4SJbqnx4y7tBkaEsV5VdmKD45FXdEOjNnA=='),(7,27,'2015-05-04 15:06:47','PWp8TbxD7nGe5MM9uGIMOaKQoazDE6JRmfbt+vl0q/rh0GxznegaE+sOhR3sVjrgyjLfGT9wNirFGxdN6Qa+zw=='),(8,27,'2015-05-04 16:39:09','qxwwuHIPS0uIBjiMzWU5a7vv0NLQRLNL9w2dSB739fxpI84CSZ80HT7eopsrko7gawWMEehdRRs4/unIzhZE9Q=='),(9,35,'2015-05-04 16:47:14','ed21IYaorSEjbzERh/6XCiNF3LcWFroBQ9bPHjnNymK57SNySxuYWnTdddb0wdECBSrkXphxSr32xOil2vOSOQ=='),(10,35,'2015-05-04 16:58:05','HzbkBWz0daUd6znj/izg2lWSHKkUe/QAj3SXzgZuyCQYl1I3BrThgFDiTpY1maLaNSSImCn6mA77AR2Ggp90AQ=='),(11,35,'2015-05-04 16:58:13','qy12n3SNhnzp5D7r5RycqjGBUUaXwZNlb0ePc4MQHBVzkJM+brDKmXDlPbzuunJVoG76+mqz0l9Q+tM0186jWg=='),(12,19,'2015-05-06 08:32:13','5TjjCanx57rYDEsaITqQctI49ke7bPaprMY3Bxjyw+WawzKj5drVLFO6QZXpPpL5DSUHSt20rF/aRlcpxWp56g=='),(13,24,'2015-05-22 16:37:07','N7pcgPfh+jLD+JAL6DfHHqdIgZttH3e0JQ9F1FavvMIZT7okjs8wNq1n+LSzF8fN/jEuPEcfB/vN2PuRTssiEQ=='),(14,37,'2015-08-19 10:34:34','tHEW5tvjzzGcpZyg2u2kI/KRYKtJ4GSIdmeRGRHyvdjxYwS3btYPoeOq90qEdT871qd9vciu2mTuQlvrWfamHA=='),(15,20,'2015-10-30 15:52:11','HR+nnSMsaM6BOs0tZuRCzz77sYN7OXtjeyLHa3Y+ZyK/qk74tGDfUIDDfJB5jPXBD+nmM/K3/rwaG7HmxHbAuw=='),(16,24,'2015-10-30 15:58:31','jNZDCJFOv24sP24GruLZiJ+nDmGV6YtLC86Y5lt6J1bij6uhyR3er2etQoeYGWOiLG0soqZd3EReEp/agD2tJQ==');

/* Function  structure for function  `ORDER_UUID` */

/*!50003 DROP FUNCTION IF EXISTS `ORDER_UUID` */;
DELIMITER $$

/*!50003 CREATE DEFINER=`root`@`localhost` FUNCTION `ORDER_UUID`(UUID BINARY(36)) RETURNS binary(16)
    DETERMINISTIC
BEGIN
	RETURN UNHEX(CONCAT(SUBSTR(UUID, 15, 4),SUBSTR(UUID, 10, 4),SUBSTR(UUID, 1, 8),SUBSTR(UUID, 20, 4),SUBSTR(UUID, 25)));
    END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
