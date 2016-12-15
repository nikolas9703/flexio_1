<?php
    namespace Flexio\Modulo\Inventarios\Transform;
 
    use League\Fractal\Manager;
    use League\Fractal\Resource\Collection;
    
    //repositories
    use Flexio\Modulo\DepreciacionActivosFijos\Repository\DepreciacionItemsRepository;
    
    class ItemTransform{
        
        public static function transform($items){

            $resource = new Collection($items,function($item){
                //dd($item->toArray());
                $DepreciacionItemsRepository    = new DepreciacionItemsRepository();
                $depreciones_items              = $DepreciacionItemsRepository->get(['empresa_id'=>$item->items->empresa_id,'codigo_serial'=>$item->nombre,'item_id'=>$item->item_id],'dep_depreciaciones_activos_fijos_items.id','desc');
                return [
                    'item_id'           => $item->items->id,
                    'categoria_id'      => $item->categoria_id,
                    'serial_id'         => $item->id,
                    'nombre'            => $item->items->nombre,
                    'descripcion'       => $item->items->descripcion,
                    'codigo'            => $item->items->codigo,
                    'codigo_serial'     => $item->nombre,
                    'valor_inicial'     => $item->items->lines_items->precio_unidad,
                    'valor_inicial2'    => (count($depreciones_items)) ? $depreciones_items->first()->valor_actual : 0,
                    'valor_actual'      => '',
                    'depreciacion'      => '',
                    'monto_depreciado'  => ''
                ];
            });
            $fractal = new Manager();
            return $fractal->createData($resource)->toArray();
        }

        
        
    }
