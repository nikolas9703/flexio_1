

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Model as Model;
use Illuminate\Database\Capsule\Manager as Capsule;
use Carbon\Carbon as Carbon;

class Chequeras_orm extends Model
{
    protected $table = 'che_chequeras';

    protected $fillable = ['numero','monto_pagado','chequera_id','pago_id','fecha_cheque','empresa_id','created_at','updated_at','estado'];

    protected $guarded = ['id','uuid_chequera'];

    public function __construct(array $attributes = array()){
        $this->setRawAttributes(array_merge($this->attributes, array('uuid_chequera' => Capsule::raw("ORDER_UUID(uuid())"))), true);
        parent::__construct($attributes);
    }

    public function getUuidChequeAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }

    public function getCreatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getUpdatedAtAttribute($value)
    {
        return date("d-m-Y", strtotime($value));
    }

    public function getFechaChequeAttribute($date){
        return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('m/d/Y');
    }

    public function cheque()
    {
        return $this->hasMany('Cheques_orm', 'chequera_id', 'id');
    }

    public function empresa()
    {
        return $this->belongsTo('Empresa_orm', 'empresa_id', 'id');
    }

}