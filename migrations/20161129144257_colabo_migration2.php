<?php

use \Flexio\Migration\Migration;

class ColaboMigration2 extends Migration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Apellido paterno'  WHERE id_campo = '6'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='&Aacute;rea de negocio'  WHERE id_campo = '38'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='&Aacute;rea de negocio'  WHERE id_campo = '164'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='&Aacute;rea de negocio'  WHERE id_campo = '166'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Centro contable'  WHERE id_campo = '100'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Centro contable'  WHERE id_campo = '155'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Devengado total'  WHERE id_campo = '99'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Direcci&oacute;n completa'  WHERE id_campo = '18'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Estado civil'  WHERE id_campo = '11'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Fecha de entrega'  WHERE id_campo = '172'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Fecha de nacimiento'  WHERE id_campo = '12'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Fecha de nacimiento'  WHERE id_campo = '24'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Fecha de nacimiento'  WHERE id_campo = '206'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Fecha salida'  WHERE id_campo = '36'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Forma de pago'  WHERE id_campo = '107'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Gastos de mortuoria'  WHERE id_campo = '73'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Grado acad&eacute;mico'  WHERE id_campo = '28'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Lugar de nacimiento'  WHERE id_campo = '14'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='No. de colaborador'  WHERE id_campo = '2'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='No. de cuenta'  WHERE id_campo = '109'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='No. pasaporte'  WHERE id_campo = '148'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='No. pasaporte'  WHERE id_campo = '186'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='No. pasaporte'  WHERE id_campo = '191'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='No. pasaporte'  WHERE id_campo = '196'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='No. pasaporte'  WHERE id_campo = '201'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='No. pasaporte'  WHERE id_campo = '214'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Nombre de empresa'  WHERE id_campo = '34'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Pr&oacute;xima entrega'  WHERE id_campo = '173'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Rata por hora'  WHERE id_campo = '102'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Salario mensual'  WHERE id_campo = '40'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Segundo nombre'  WHERE id_campo = '5'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Seguro social'  WHERE id_campo = '9'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Tel&eacute;fono residencial'  WHERE id_campo = '15'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Tipo de salario'  WHERE id_campo = '101'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Tipo de cuenta'  WHERE id_campo = '108'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Trabajo anterior'  WHERE id_campo = '33'");
      $this->execute("UPDATE col_colaboradores_campos SET etiqueta='Zona postal'  WHERE id_campo = '113'");

      //Tabla Mod Pestañas
      $this->execute("UPDATE mod_pestanas SET pestana='Centro contable'  WHERE id_pestana = '48'");
      $this->execute("UPDATE mod_pestanas SET pestana='Crear aseguradora'  WHERE id_pestana = '50'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos espec&iacute;ficos'  WHERE id_pestana = '47'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos de la oportunidad'  WHERE id_pestana = '27'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos de la oportunidad'  WHERE id_pestana = '32'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos del cliente'  WHERE id_pestana = '15'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos del cliente'  WHERE id_pestana = '11'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos del cliente'  WHERE id_pestana = '5'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos del cliente'  WHERE id_pestana = '13'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos generales'  WHERE id_pestana = '40'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos generales'  WHERE id_pestana = '71'");
      $this->execute("UPDATE mod_pestanas SET pestana='Datos generales comisi&oacute;n'  WHERE id_pestana = '61'");
      $this->execute("UPDATE mod_pestanas SET pestana='Editar aseguradora'  WHERE id_pestana = '55'");
      $this->execute("UPDATE mod_pestanas SET pestana='Informaci&oacute;n Personal'  WHERE id_pestana = '17'");
      $this->execute("UPDATE mod_pestanas SET pestana='Informaci&oacute;n Personal'  WHERE id_pestana = '16'");
      $this->execute("UPDATE mod_pestanas SET pestana='Informaci&oacute;n Personal'  WHERE id_pestana = '6'");
      $this->execute("UPDATE mod_pestanas SET pestana='Informaci&oacute;n Personal'  WHERE id_pestana = '7'");




    }
}
