<?php

use \Flexio\Migration\Migration;

class TblaMigrationCol extends Migration
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















     public function up()
     {
       $rows = [

         [
           'nombre_campo'    => 'prcentaje_distribucion',
           'etiqueta'  => 'Porcentaje',
           'id_tipo_campo'  => 23,
           'estado'  => 'activo',
           'atributos'  => '{"disabled":"disabled","data-addon-text":"%","class":"form-control"}',
           'agrupador_campo'  =>'distribucion',
           'contenedor'  => 'tabla-dinamica',
           'tabla_relacional'  => '',
           'posicion'  => 66
         ],
         [
           'nombre_campo'    => 'id',
           'etiqueta'  => '',
           'id_tipo_campo'  => 7,
           'estado'  => 'activo',
           'atributos'  => '{"disabled":"disabled"}',
           'agrupador_campo'  =>'distribucion',
           'contenedor'  => 'tabla-dinamica',
           'tabla_relacional'  => '',
           'posicion'  => 69
         ],
         [
           'nombre_campo'    => 'agregarBtn',
           'etiqueta'  => '&lt;i class=&quot;fa fa-plus&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md hidden-lg&quot;&gt;&amp;nbsp;Agregar&lt;/span&gt;',
           'id_tipo_campo'  => 1,
           'estado'  => 'activo',
           'atributos'  => '{"disabled":"disabled","class":"btn btn-default btn-block agregarEstudiosBtn"}',
           'agrupador_campo'  =>'distribucion',
           'contenedor'  => 'tabla-dinamica',
           'tabla_relacional'  => '',
           'posicion'  => 68
         ],


           [
             'nombre_campo'    => 'eliminarBtn',
             'etiqueta'  => '&lt;i class=&quot;fa fa-trash&quot;&gt;&lt;/i&gt;&lt;span class=&quot;hidden-xs hidden-sm hidden-md hidden-lg&quot;&gt;&amp;nbsp;Eliminar&lt;/span&gt;',
             'id_tipo_campo'  => 1,
             'estado'  => 'activo',
             'atributos'  => '{"disabled":"disabled","class":"btn btn-default btn-block eliminarEstudiosBtn"}',
             'agrupador_campo'  =>'distribucion',
             'contenedor'  => 'tabla-dinamica',
             'tabla_relacional'  => '',
             'posicion'  => 67
           ],
           [
             'nombre_campo'    => 'monto_asignado',
             'etiqueta'  => 'Monto asignado',
              'id_tipo_campo'  => 22,
             'estado'  => 'activo',
             'atributos'  => '{"disabled":"disabled","data-addon-text":"$","class":"form-control"}',
             'agrupador_campo'  =>'distribucion',
             'contenedor'  => 'tabla-dinamica',
             'tabla_relacional'  => '',
             'posicion'  => 66
           ],
             [
               'nombre_campo'    => 'centro_contable_id',
               'etiqueta'  => 'Centro contable',
                'id_tipo_campo'  => 18,
               'estado'  => 'activo',
               'atributos'  => '{"disabled":"disabled","class":"chosen-select form-control"}',
               'agrupador_campo'  =>'distribucion',
               'contenedor'  => 'tabla-dinamica',
               'tabla_relacional'  => 'entrada_manual_centro',
               'posicion'  => 65
             ],
             [
               'nombre_campo'    => 'cuenta_costo_id',
               'etiqueta'  => 'Cuenta de costo/gasto',
                'id_tipo_campo'  => 18,
               'estado'  => 'activo',
               'atributos'  => '{"disabled":"disabled","class":"chosen-select form-control"}',
               'agrupador_campo'  =>'distribucion',
               'contenedor'  => 'tabla-dinamica',
               'tabla_relacional'  => 'gastos_colaborador',
               'posicion'  => 64
             ],
             [
               'nombre_campo'    => 'separador',
               'etiqueta'  => 'Distribuci&oacute;n de salario',
                'id_tipo_campo'  => 27,
               'estado'  => 'activo',
                 'contenedor'  => 'div',
               'tabla_relacional'  => 'gastos_colaborador',
               'posicion'  => 63
             ]
         ];

         $this->insert('col_colaboradores_campos', $rows);




     }
}
