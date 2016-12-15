<?php

namespace Flexio\Modulo\Plantillas\Models;

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class PlantillaCatalogo extends Model {

    protected $table = 'plnt_plantillas_cat';
    protected $fillable = ['id_campo', 'identificador', 'etiqueta'];
    protected $guarded = ['id'];

    public function getEtiquetaPlantillaAttribute() {
        return $this->etiqueta;
    }

}
