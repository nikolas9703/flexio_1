<?php 
namespace Flexio\Transformers;

abstract class TransformerObject{
  public function transformCollection($collection)
  {
      return $collection->map(function($item){
        return $this->transform($item);
      });
  }

  public abstract function transform($item);  
}
