<?php

use \Flexio\Migration\Migration;

class AddValueNotificacionesCatalog extends Migration
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
    public function up() {
        $data = [
            ['valor'=>'activo','etiqueta'=>'Activo', 'tipo' => 'estado', 'orden' => '1'],
            ['valor'=>'inactivo','etiqueta'=>'Inactivo', 'tipo' => 'estado', 'orden' => '2'],
            ['valor'=>'correo','etiqueta'=>'Correo electr&oacute;nico', 'tipo' => 'notificacion', 'orden' => '1'],
            ['valor'=>'alarma_sistema','etiqueta'=>'Alarma de sistema', 'tipo' => 'notificacion', 'orden' => '2'],
            ['valor'=>'notificacion_escritorio','etiqueta'=>'Notificaci&oacute;n de escritorio', 'tipo' => 'notificacion', 'orden' => '3'],
            ['valor'=>'mayor_a','etiqueta'=>'>(mayor a)', 'tipo' => 'operador', 'orden' => '1'],
            ['valor'=>'menor_a','etiqueta'=>'<(menor a)', 'tipo' => 'operador', 'orden' => '2'],
            ['valor'=>'igual_a','etiqueta'=>'=(igual a)', 'tipo' => 'operador', 'orden' => '3'],
        ];
        $this->insert('not_notificaciones_cat', $data);
    }
}
