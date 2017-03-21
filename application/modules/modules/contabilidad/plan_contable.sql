insert into contab_cuentas (codigo, nombre, detalle, estado, balance, created_at, updated_at, tipo_cuenta_id, empresa_id, padre_id,uuid_cuenta)
values ('1.','Activos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__','0',ORDER_UUID(UUID()));
insert into contab_cuentas (codigo, nombre, detalle, estado, balance, created_at, updated_at, tipo_cuenta_id, empresa_id, padre_id, uuid_cuenta)
select '1.1.','Activo corriente','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.1.','Efectivo y equivalentes de efectivo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.1.01.','Caja general','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`,padre_id, uuid_cuenta)
select'1.1.1.02.','Caja chica','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(), '1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.2.','Bancos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`,padre_id, uuid_cuenta)
select '1.1.2.01.','Depositos en cuentas corrientes','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`,padre_id, uuid_cuenta)
select '1.1.2.02.','Depositos en cuentas de ahorro','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.2.03.','Depositos a plazo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.3.','Cuentas por cobrar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.3.01.','Cuentas por cobrar de clientes','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.3.02.','Abonos y anticipos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.3.03.','Préstamos al personal','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.3.04.','Préstamos a accionistas','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.3.05.','Otras cuentas por cobrar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.4.','Inventarios','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.4.01.','Inventarios en bodega','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.4." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.4.02.','Pedidos en tránsito','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.4." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.5.','Inversiones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.5.01.','Inversiones y acciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.5." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.6.','Gastos anticipados','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.1.6.01.','Garantías de arrendamiento','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.1.6." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.','Activo no corriente','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(), '1', '__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.1.','Propiedad, planta y equipo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.1.01.','Terrenos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.1.02.','Instalaciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.1.03.','Mobiliario y equipo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.1.04.','Vehiculos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.2.','Depreciación acumulada','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.2.01.','Depreciación acumulada de instalaciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.2.02.','Depreciación acumulada de mobiliario y equipo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.2.03.','Depreciación acumulada de vehículos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.3.','Reevaluaciones de propiedad,planta y equipo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.3.01.','Reevaluación de terrenos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.3.02.','Reevaluación de instalaciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.4.','Impuestos anticipados','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '1.2.4.01.','Impuesto sobre la renta anticipado','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'1','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="1.2.4." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
values ('2.','Pasivo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__','0',ORDER_UUID(UUID()));
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.','Pasivo corriente','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.1.','Préstamos y sobregiros','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.1.01.','Préstamos bancarios a corto plazo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.1.02.','Sobregiros bancarios','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.2.','Cuentas por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.2.01.','Cuentas por pagar a proveedores','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.2.02.','Cuentas por pagar a acreedores','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.2.03.','Contratos a corto plazo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.3.','Provisiones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.3.01.','Provisiones locales','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.3.02.','Intereses por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.3.03.','Impuestos municipales por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.3.04.','Impuestos generales por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.3.05.','Impuesto sobre la renta por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.3.06.','ITBMS por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.4.','Retenciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.4.01.','Cuota obrero patronal por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.4." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.4.02.','Prima de antiguedad','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.4." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.5.','Beneficios a empleados por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.5.01.','Salarios por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.5." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.5.02.','Beneficios a corto plazo por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.5." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.5.03.','Comisiones por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.5." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.5.04.','Bonificaciones por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.5." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.6.','Dividendos por pagar','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.1.6.01.','Dividendos por pagar a accionistas','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.1.6." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.2.','Pasivo no corriente','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.2.1.','Préstamos bancarios a largo plazo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.2.1.01.','Préstamos hipotecarios','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.2.1.02.','Otros préstamos a largo plazo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.2.2.','Anticipos y garantías de clientes','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.2.2.01.','Anticipos de clientes','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.2.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '2.2.2.02.','Garantías de clientes','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'2','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="2.2.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
values('3.','Patrimonio','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'3','__EMP01__','0',ORDER_UUID(UUID()));
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '3.1.','Capital','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'3','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '3.1.1.','Capital social','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'3','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="3.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '3.1.1.01.','Capital social suscrito','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'3','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="3.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '3.1.2.','Superavit','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'3','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="3.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '3.1.2.01.','Superavit por reevaluaciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'3','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="3.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '3.1.3.','Utilidades retenidas','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'3','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="3.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '3.1.3.01.','Utilidades retenidas','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'3','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="3.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
values('4.','Ingresos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__','0',ORDER_UUID(UUID()));
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '4.1.','Ingresos por operaciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="4." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '4.1.1.','Ventas generales','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="4.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '4.1.1.01.','Ventas de servicios generales','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="4.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '4.1.2.','Ventas de bienes','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="4.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '4.1.2.01.','Ventas internas','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="4.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '4.1.3.','Otros ingresos no operacionales','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="4.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '4.1.3.01.','Intereses ganados','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="4.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '4.1.3.02.','Otros ingresos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'4','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="4.1.3." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
values('5.','Costos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'5','__EMP01__','0',ORDER_UUID(UUID()));
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '5.1.','Costos de operación','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'5','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="5." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '5.1.1.','Costo de venta','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'5','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="5.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '5.1.1.01.','Costo de venta de productos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'5','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="5.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '5.1.1.02.','Costo de venta de servicios','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'5','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="5.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;

insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
values('6.','Gastos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__','0',ORDER_UUID(UUID()));

insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.','Gastos de operación','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.','Gastos generales','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.01.','Energía eléctrica','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.02.','Internet','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.03.','Telefonía','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.04.','Papelería y utiles de oficina','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.05.','Depreciación','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.06.','Seguros y pólizas','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.07.','Mensajería','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.08.','Alquileres de oficinas','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.09.','Mantenimiento de oficinas','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.10.','Mantenimiento de vehiculos','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.11.','Mantenimiento de mobiliario','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.12.','Aseo y limpieza','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.1.13.','Publicidad y mercadeo','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.2.','Gastos de recurso humano','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.2.01.','Salarios','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.2.02.','Vacaciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.2.03.','Decimo tercer mes','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.2.04.','Indemnizacion','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.2.05.','Prima de antiguedad','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.2.06.','Cuota obrero patronal','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.1.2.07.','Bonificaciones y gratificaciones','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.1.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.2.','Gastos no operacionales','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.2.1.','Gastos financieros','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.2." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.2.1.01.','Intereses bancarios','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.2.1.02.','Comisiones bancarias','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__',id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
insert into `contab_cuentas` (`codigo`, `nombre`, `detalle`, `estado`, `balance`, `created_at`, `updated_at`, `tipo_cuenta_id`, `empresa_id`, padre_id, uuid_cuenta)
select '6.2.1.03.','Otros cargos bancarios','','1','0',CURRENT_TIMESTAMP(),CURRENT_TIMESTAMP(),'6','__EMP01__', id, ORDER_UUID(UUID()) FROM contab_cuentas WHERE codigo ="6.2.1." AND empresa_id = __EMP01__ ORDER  BY id DESC LIMIT 1;
