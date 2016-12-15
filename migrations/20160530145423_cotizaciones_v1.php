<?php

use \Flexio\Migration\Migration;

class CotizacionesV1 extends Migration
{
    public function up()
    {
        $rows = [
            [
                'id'        => '12',
                'key'       => '12',
                'valor'     => 'Por aprobar',
                'etiqueta'  => 'por_aprobar',
                'tipo'      => 'etapa',
                'orden'     => '6'
            ]
        ];

        $this->insert('cotz_cotizaciones_catalogo', $rows);
        
        $this->execute("UPDATE cotz_cotizaciones_catalogo SET valor='Aprobado', etiqueta='aprobado' WHERE id = '7'");
        $this->execute("UPDATE cotz_cotizaciones_catalogo SET valor='Ganado', etiqueta='ganado' WHERE id = '9'");
        $this->execute("UPDATE cotz_cotizaciones_catalogo SET valor='Perdido', etiqueta='perdido' WHERE id = '10'");
        $this->execute("UPDATE cotz_cotizaciones_catalogo SET valor='Anulado', etiqueta='anulado' WHERE id = '11'");
        
        $this->execute("UPDATE cotz_cotizaciones SET estado='aprobado' WHERE estado = 'abierta'");
        $this->execute("UPDATE cotz_cotizaciones SET estado='ganado' WHERE estado = 'ganada'");
        $this->execute("UPDATE cotz_cotizaciones SET estado='perdido' WHERE estado = 'perdida'");
        $this->execute("UPDATE cotz_cotizaciones SET estado='anulado' WHERE estado = 'anulada'");
    }

    /**
     * Migrate Down.
     */
    public function down()
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
