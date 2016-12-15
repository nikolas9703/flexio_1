<?php namespace Flexio\Transformers;
abstract class Transformer{
  public function transformCollection(array $collection)
  {
      return array_map([$this,'transform'],$collection->toArray());
  }

  public abstract function transform($item);  
}
