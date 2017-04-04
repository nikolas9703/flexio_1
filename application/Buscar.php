<?php

use Illuminate\Database\Eloquent\Model as Model;

interface BuscarInterface {

	public function findByUuid($uuid);

}

class Buscar implements BuscarInterface{

  protected $clase ='';
  protected $atributo ='';


  function __construct(Model $clase,$atributo){
    $this->clase = $clase;
    $this->atributo = $atributo;
  }

  public function findByUuid($uuid){
    return $this->clase->where($this->atributo,hex2bin($uuid))->first();
  }

    public function findById($id){
        return $this->clase->where($this->atributo,$id)->first();
    }

}
