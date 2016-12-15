<?php

use \Flexio\Migration\Migration;

class ContratosCatalogoMigration extends Migration
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
         
            $this->execute('DELETE FROM cotz_cotizaciones_catalogo WHERE id = 12');
        
            $this->execute("UPDATE cotz_cotizaciones_catalogo SET valor='Abierta', etiqueta='abierta' WHERE id = '7'");
            $this->execute("UPDATE cotz_cotizaciones_catalogo SET valor='Ganada', etiqueta='ganada' WHERE id = '9'");
            $this->execute("UPDATE cotz_cotizaciones_catalogo SET valor='Perdida', etiqueta='perdida' WHERE id = '10'");
            $this->execute("UPDATE cotz_cotizaciones_catalogo SET valor='Anulada', etiqueta='anulada' WHERE id = '11'");
        
            $this->execute("UPDATE cotz_cotizaciones SET estado='abierta' WHERE estado = 'aprobado'");
            $this->execute("UPDATE cotz_cotizaciones SET estado='ganada' WHERE estado = 'ganado'");
            $this->execute("UPDATE cotz_cotizaciones SET estado='perdida' WHERE estado = 'perdido'");
            $this->execute("UPDATE cotz_cotizaciones SET estado='anulada' WHERE estado = 'anulado'");
     }
}
