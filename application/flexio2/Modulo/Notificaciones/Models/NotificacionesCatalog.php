<?php
/**
 * Created by PhpStorm.
 * User: Ivan Cubilla
 * Date: 31/10/16
 * Time: 7:57 PM
 */

namespace Flexio\Modulo\Notificaciones\Models;

use Illuminate\Database\Eloquent\Model as Model;

class NotificacionesCatalog extends Model
{
    protected $table = 'not_notificaciones_cat';
    protected $guarded = ['id'];
}