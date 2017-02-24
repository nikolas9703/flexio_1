<?php
namespace Flexio\Modulo\Anticipos\Repository;
use Flexio\Modulo\Anticipos\Models\Anticipo;
use Flexio\Library\Util\FlexioSession;


class AnticipoRepository{

    public $builder;

    function findByUuid($uuid) {
        $session = FlexioSession::now();
        return Anticipo::where('uuid_anticipo', hex2bin($uuid))->where('empresa_id',$session->empresaId())->first();
    }

    function __construct(){
      $this->builder = (new Anticipo)->newQuery();
    }

    function getAnticipos($empresa_id){
      $this->builder->where('empresa_id', $empresa_id);
      return $this;
    }

    function con_pago_anulado(){
        $this->builder->has('no_anulados','<',1);
        $this->builder->has('pagos_anulados');
        return $this;
    }

    function fetch(){
      return $this->builder->get();
    }
}
