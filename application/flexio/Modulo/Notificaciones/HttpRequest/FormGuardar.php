<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 1/11/16
 * Time: 2:15 PM
 */

namespace Flexio\Modulo\Notificaciones\HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

//utils
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;
use Flexio\Modulo\Notificaciones\Models\Notificaciones;

class FormGuardar
{
    protected $request;
    protected $session;

    public function __construct()
    {
        $this->request = Request::capture();
        $this->session = new FlexioSession;
    }

    public function guardar()
    {
        $campos = FormRequest::data_formulario($this->request->input('campo'));
        if(isset($campos['id'])){
            return $this->_update($campos);
        }
        //crear notificaciones
        return $this->_create($campos);
    }
    private function _create($campos)
    {
        return  Capsule::transaction(function() use($campos){
            $notificacion = Notificaciones::create($campos);
            return $notificacion;
        });
    }
}