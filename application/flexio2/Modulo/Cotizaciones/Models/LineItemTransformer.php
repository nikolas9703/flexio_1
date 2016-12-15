<?php namespace Flexio\Modulo\Cotizaciones\Models;
use Flexio\Transformers\Transformer;
class LineItemTransformer extends Transformer{
  public function transform($lineitem)
  {
    return [
        'categoria_id'    =>  $lineitem['categoria_id'],
        'item_id'    =>  $lineitem['item_id'],
        'empresa_id'    =>  $lineitem['empresa_id'],
        'cantidad'    =>  $lineitem['cantidad'],
        'unidad_id'    =>  !empty($item['unidad_id']) ? $item['unidad_id'] : 0,
        'item_adicional' => !empty($item['item_adicional']) ? $item['item_adicional'] : "",
        'periodo_tarifario_id' => !empty($lineitem['periodo_tarifario_id']) ? $lineitem['periodo_tarifario_id'] : "",
        'precio_unidad'    =>  $lineitem['precio_unidad'],
        'impuesto_id'    =>  $lineitem['impuesto_id'],
        'descuento'    =>  $lineitem['descuento'],
        'cuenta_id'    =>  $lineitem['cuenta_id'],
        'precio_total'    =>  $lineitem['precio_total'],
        'impuesto_total'    =>  $lineitem['impuesto_total'],
        'descuento_total'    =>  $lineitem['descuento_total'],
        'cantidad_devolucion' => $lineitem['cantidad_devolucion'],
        'comentario' =>  $lineitem['comentario']
    ];
  }

  public function crearInstancia($linesItems){
      
    $model=[];
    foreach($linesItems as $item){
      if(isset($item['lineitem_id']) && !empty($item['lineitem_id'])){
      array_push($model,$this->setData($item));
    }else{
      array_push($model,new LineItem($item));
    }
    }
    return $model;
  }

   function setData($item){
     $line = LineItem::find($item['lineitem_id']);
     if(count($line) > 0){
     	foreach($item as $key => $value){
     		if(empty($value) && $value==""){
     			continue;
     		}
     		if($key !='id' )$line->{$key} = $value;
     	}
     }
     /// se elimina este key del objecto porque no existe en la base de datos.
     unset($line->lineitem_id);
     return $line;
   }
}
