<?php
namespace Flexio\Modulo\Anticipos\Repository;
use Flexio\Modulo\Anticipos\Models\Anticipo;
use Flexio\Library\Util\FlexioSession;


class AnticipoRepository{

    function findByUuid($uuid) {
        $session = FlexioSession::now();
        return Anticipo::where('uuid_anticipo', hex2bin($uuid))->where('empresa_id',$session->empresaId())->first();
    }
}
