<?php

use Phinx\Seed\AbstractSeed;

class CatalogoNotaCreditoSeeder extends AbstractSeed
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
              'key'    => '1',
              'valor' => "Por Aprobar",
              'etiqueta' => "por_aprobar",
              'tipo' => "estado",
              'orden' => 1,
          ),
          array(
              'key'    => '2',
              'valor' => "Aprobado",
              'etiqueta' => "aprobado",
              'tipo' => "estado",
              'orden' => 2,
          ),
          array(
              'key'    => '3',
              'valor' => "Anulado",
              'etiqueta' => "anulado",
              'tipo' => "estado",
              'orden' => 3,
          )
        );

        $posts = $this->table('venta_nota_credito_catalogo');
        $posts->insert($data)
              ->save();
    }
    
}
