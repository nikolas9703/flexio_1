<?php
namespace Flexio\Modulo\Traslados\Repository;

//utilities
use Illuminate\Database\Capsule\Manager as Capsule;

//repositorios
use Flexio\Modulo\Bodegas\Repository\BodegasRepository as bodegasRep;
use Flexio\Modulo\Inventarios\Repository\ItemsRepository as itemsRep;
use Flexio\Modulo\Entradas\Repository\EntradasRepository as entradasRep;
use Flexio\Modulo\Salidas\Repository\SalidasRepository as salidasRep;
use Flexio\Modulo\Inventarios\Repository\UnidadesRepository as unidadesRep;

//modelos
use Flexio\Modulo\Traslados\Models\Traslados as Traslados;

class TrasladosRepository
{

    private $bodegasRep;
    private $itemsRep;
    private $entradasRep;
    private $salidasRep;
    private $unidadesRep;

    //variables del entorno
    private $prefijo = "TRAS";

    public function __construct() {
        $this->bodegasRep   = new bodegasRep();
        $this->itemsRep     = new itemsRep();
        $this->entradasRep  = new entradasRep();
        $this->salidasRep   = new salidasRep();
        $this->unidadesRep  = new unidadesRep();
    }

    public function findByUuid($uuid)
    {
        return Traslados::where('uuid_traslado',hex2bin($uuid))->first();
    }

    public function getColletionTraslado($registro)
    {
        $articulo = new \Flexio\Library\Articulos\ArticuloVenta;
        return Collect(array_merge(
            $registro->toArray(),
            [
                'uuid_lugar' => strtoupper(bin2hex($registro->uuid_lugar)),
                'uuid_lugar_anterior' => strtoupper(bin2hex($registro->uuid_lugar_anterior)),
                'uuid_pedido' => strtoupper(bin2hex($registro->uuid_pedido)),
                'uuid_proveedor' => '',
                'articulos' => $articulo->get($registro->lines_items, $registro)
            ]
        ));
    }


}
