<?php

namespace Flexio\Modulo\Inventarios\HttpRequest;

use Illuminate\Http\Request;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

//utils
use Flexio\Library\Util\FormRequest;
use Flexio\Library\Util\FlexioSession;


//models
use Flexio\Modulo\Inventarios\Models\Categoria;


class FormGuardarCategoria{

 protected $request;
 protected $session;

 function __construct(){
    $this->request = Request::capture();
    $this->session = new FlexioSession;
        
 }

 function guardar(){
    $categoria = FormRequest::data_formulario($this->request->input('campo'));
    

    if(isset($categoria['id'])){
        return $this->actualizar($categoria);
    }

    $categoria["empresa_id"] = $this->session->empresaId();
    $categoria['created_by'] = $this->session->usuarioId();
    
    return $this->crear($categoria);
 }

 function crear($campos){
    return Capsule::transaction(function() use($campos){
            $categoria = Categoria::create($campos);
            return $categoria;
    });
 }

 function actualizar($campos){
      return Capsule::transaction(function() use($campos){
            $categoria = Categoria::find($campos['id']);       
            $categoria->update($campos);
            return $categoria;
     });
 }

}