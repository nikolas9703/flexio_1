CREATE TABLE IF NOT EXISTS `grp_grupo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid_grupo` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `credito_a_favor` decimal(10,2) NOT NULL,
  `saldo_acumulado` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
