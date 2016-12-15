<?php
namespace Flexio\Modulo\ConfiguracionCompras\Models;
use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;

class ChequesCatalogo extends Model
{
    protected $table = 'che_cheques_catalogo';
    protected $guarded = ['id'];
}