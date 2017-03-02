<?php

use \Flexio\Migration\Migration;

class Incapacidades extends Migration
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
     /* id INT(11) NOT NULL AUTO_INCREMENT,*/


     public function creando()
     {
        $this->schema->create('inp_incapacidades', function(Illuminate\Database\Schema\Blueprint $table) {
          $table->increments('id');
          $table->integer('empresa_id');
          $table->integer('colaborador_id');
          $table->integer('tipo_incapacidad_id');
          $table->integer('dias_disponibles_id');
          $table->datetime('fecha_desde');
          $table->datetime('fecha_hasta');
          $table->integer('cuenta_pasivo_id');
          $table->text('observaciones');
          $table->integer('incapacidad_pagada_id');
          $table->integer('estado_id');
          $table->tinyInteger('certificado_medico');
          $table->string('certificado_ruta', 255);
          $table->string('certificado_nombre', 255);
          $table->tinyInteger('carta_descuento');
          $table->string('carta_ruta', 255);
          $table->string('carta_nombre', 255);
          $table->integer('creado_por');
          $table->string('cons_inst_medica_ruta', 255);
          $table->string('cons_inst_medica_nombre', 255);
          $table->tinyInteger('constancia_institucion_medica');
          $table->tinyInteger('orden_medica_hospitalizacion');
          $table->string('ord_med_hospt_nombre', 255);
          $table->string('ord_med_hospt_ruta', 255);
          $table->tinyInteger('orden_css_pension');
          $table->string('ord_css_pens_nombre', 255);
          $table->string('ord_css_pens_ruta', 255);
          $table->tinyInteger('desgloce_salario');
          $table->string('desg_sal_nombre', 255);
          $table->string('desg_sal_ruta', 255);
          $table->tinyInteger('reporte_accion_trabajo');
          $table->string('report_acc_trab_nombre', 255);
          $table->string('report_acc_trab_ruta', 255);
          $table->tinyInteger('certificado_incapacidad_accidente_trabajo');
          $table->string('cert_incp_accid_trab_nombre', 255);
          $table->string('cert_incp_accid_trab_ruta', 255);
          $table->timestamps();
       });
     }

     public function up()
     {
       $this->schema->drop('inp_incapacidades');
       $this->creando();
     }
}
