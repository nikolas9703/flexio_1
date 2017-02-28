<?php
namespace Flexio\Provider;
use Illuminate\Database\Eloquent\Builder;


abstract class QueryFilters
{
    /**
     * The request object.
     *
     * @var Request
     */
    protected $request;
    /**
     * The builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Apply the filters to the builder.
     *
     * @param  Builder $builder
     * @return Builder
     */
     function apply(Builder $builder, $clause){
       $this->builder = $builder;

       foreach(array_filter($clause) as $key => $value){
         if(method_exists($this,$key)){

           call_user_func_array([$this, $key], array_filter([$value]));
         }
       }

       return $this->builder;
     }

}
