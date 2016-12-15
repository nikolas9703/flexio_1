<?php

use Phinx\Seed\AbstractSeed;

class CatalogoContratoAlquiler extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = array(
            array(
                'valor' => "periodo_de_dias",
                'Nombre' => "Periodo de d&iacute;as",
                'tipo' => "corte_facturacion",
            )
        );
        $catalogo = $this->table('conalq_contratos_alquiler_catalogos');
        $catalogo->insert($data)
              ->save();
    }
}
