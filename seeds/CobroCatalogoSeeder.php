<?php

use Phinx\Seed\AbstractSeed;

class CobroCatalogoSeeder extends AbstractSeed
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
                'key'    => '16',
                'valor' => "Depositar en cuenta de Banco:",
                'etiqueta' => "banco",
                'tipo' => "tipo_cobro",
                'orden' => 1,
            ),
            array(
                'key'    => '17',
                'valor' => "Recibir en Caja:",
                'etiqueta' => "caja",
                'tipo' => "tipo_cobro",
                'orden' => 2,
            )
          );

          $catalogo_cobro = $this->table('cob_cobro_catalogo');
          $catalogo_cobro->insert($data)
                ->save();

    }
}
