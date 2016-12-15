<?php

use Phinx\Seed\AbstractSeed;

class CatalogoReporteFinancieroSeed extends AbstractSeed
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
              'valor' => "Hoja de balance de situación",
              'etiqueta' => "balance_situacion",
              'tipo' => "reporte",
              'orden' => 1,
          ),
          array(
              'valor' => "Estado de ganancias y pérdidas",
              'etiqueta' => "ganancias_perdidas",
              'tipo' => "reporte",
              'orden' => 2,
          ),
          array(
              'valor' => "Flujo de efectivo",
              'etiqueta' => "flujo_efectivo",
              'tipo' => "reporte",
              'orden' => 3,
          )
        );
        $this->execute("TRUNCATE TABLE cat_reporte_financiero");
        $posts = $this->table('cat_reporte_financiero');
        $posts->insert($data)
              ->save();
    }
}
