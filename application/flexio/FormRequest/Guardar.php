<?php

namespace Flexio\FormRequest;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

//utils
use Illuminate\Http\Request;
use Flexio\Library\Util\FlexioSession;

abstract class Guardar
{
    protected $request;
    protected $session;
    protected $transform;
    protected $model;
    protected $modelName;
    protected $prefijo;

    public function __construct($transform, $model)
    {
        $this->setModel(new $model);
        $this->setUtils(new FlexioSession, $transform);
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
        $this->modelName = get_class($model);
    }

    public function setUtils(FlexioSession $session, $transform)
    {
        $this->session = $session;
        $this->transform = $transform;
        $this->request = Request::capture();
    }

    protected function setCodigo($len_numero=6, $inicia='0')
    {
        $year = Carbon::now()->format('y');
        $codigo = str_pad($this->getLastCodigo($year), $len_numero, $inicia, STR_PAD_LEFT);
        return $this->prefijo.$year.$codigo;
	}

    protected function getLastCodigo($year)
    {
        $modelName = $this->modelName;
        $clause = ['empresa_id' => $this->session->empresaId()];
        $result = $modelName::where($clause)->get()->last();
        $codigo = empty($result) ? 0 : (int) str_replace($this->prefijo.$year, "", $result->codigo);
        return $codigo + 1;
    }

}
