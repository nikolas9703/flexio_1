<?php
namespace Flexio\Modulo\Colaboradores\Models;

use Illuminate\Database\Eloquent\Model as Model;

use Flexio\Modulo\CentrosContables\Models\CentrosContables as CentrosContables;
use Flexio\Modulo\DescuentosDirectos\Models\DescuentosDirectos as DescuentosDirectos;
use Flexio\Modulo\Cargos\Models\Cargos as Cargos;
use Flexio\Modulo\Documentos\Models\Documentos;
use Flexio\Modulo\Planilla\Models\Pagadas\PagadasColaborador;
use Flexio\Modulo\Planilla\Models\Planilla;
use Flexio\Modulo\Bancos\Models\Bancos;
use Flexio\Modulo\Comentario\Models\Comentario;
use Flexio\Library\Venturecraft\Revisionable\RevisionableTrait;

class Colaboradores extends Model
{
    protected $table = 'col_colaboradores';
    public $timestamps = false;
    protected $dateFormat = 'U';
    protected $fillable = ['id','uuid_colaborador','empresa_id', 'estado_id','sexo_id', 'estado_civil_id','nombre', 'segundo_nombre', 'apellido', 'apellido_materno', 'cedula', 'provincia_id', 'letra_id', 'tomo', 'asiento', 'no_pasaporte', 'seguro_social', 'fecha_nacimiento','edad','lugar_nacimiento','telefono_residencial','celular','email','direccion', 'centro_contable_id','departamento_id', 'cargo_id', 'tipo_salario', 'salario_mensual', 'ciclo_id', 'rata_hora', 'horas_semanales', 'fecha_inicio_labores', 'creado_por', 'estatura', 'peso', 'talla_camisa', 'talla_pantalon', 'no_botas', 'banco_id', 'forma_pago_id', 'tipo_cuenta_id', 'numero_cuenta', 'tutor_nombre', 'tutor_parentesco_id', 'tutor_cedula', 'designado_nombre', 'designado_parentesco_id', 'designado_cedula', 'consulta_medica', 'consulta_medica_fecha', 'consulta_nombre_medico', 'consulta_causas', 'consulta_examen', 'consulta_resultado', 'enfermedad_sufre', 'enfermedad_nombre', 'enfermedad_sometido_tratamiento', 'enfermedad_explicar', 'seguro_otro', 'seguro_nombre_compania', 'seguro_valor', 'deduccion_tipo_declarante_id', 'deduccion_otros_ingresos_id', 'deduccion_zona_postal', 'deduccion_provincia_id', 'deduccion_distrito', 'deduccion_corregimiento', 'deduccion_barrio', 'deduccion_fecha', 'patrono_clasificacion_empleado', 'patrono_razon_social', 'patrono_nombre_comercial', 'patrono_ruc', 'patrono_telefono', 'patrono_direccion'];

    use RevisionableTrait;

    //Propiedades de Revisiones
    protected $revisionEnabled = true;
    protected $revisionCreationsEnabled = true;
    protected $keepRevisionOf = ['id','uuid_colaborador','empresa_id', 'estado_id','sexo_id', 'estado_civil_id','nombre', 'segundo_nombre', 'apellido', 'apellido_materno', 'cedula', 'provincia_id', 'letra_id', 'tomo', 'asiento', 'no_pasaporte', 'seguro_social', 'fecha_nacimiento','edad','lugar_nacimiento','telefono_residencial','celular','email','direccion', 'centro_contable_id','departamento_id', 'cargo_id', 'tipo_salario', 'salario_mensual', 'ciclo_id', 'rata_hora', 'horas_semanales', 'fecha_inicio_labores', 'creado_por', 'estatura', 'peso', 'talla_camisa', 'talla_pantalon', 'no_botas', 'banco_id', 'forma_pago_id', 'tipo_cuenta_id', 'numero_cuenta', 'tutor_nombre', 'tutor_parentesco_id', 'tutor_cedula', 'designado_nombre', 'designado_parentesco_id', 'designado_cedula', 'consulta_medica', 'consulta_medica_fecha', 'consulta_nombre_medico', 'consulta_causas', 'consulta_examen', 'consulta_resultado', 'enfermedad_sufre', 'enfermedad_nombre', 'enfermedad_sometido_tratamiento', 'enfermedad_explicar', 'seguro_otro', 'seguro_nombre_compania', 'seguro_valor', 'deduccion_tipo_declarante_id', 'deduccion_otros_ingresos_id', 'deduccion_zona_postal', 'deduccion_provincia_id', 'deduccion_distrito', 'deduccion_corregimiento', 'deduccion_barrio', 'deduccion_fecha', 'patrono_clasificacion_empleado', 'patrono_razon_social', 'patrono_nombre_comercial', 'patrono_ruc', 'patrono_telefono', 'patrono_direccion'];

    protected $guarded      = ['id'];
    protected $appends      = ['icono','enlace','total_devengado'];


    public static function findByUuid($uuid){
        return self::where('uuid_colaborador',hex2bin($uuid))->first();
    }

    public static function boot() {
        parent::boot();
    }

    function documentos(){
    	return $this->morphMany(Documentos::class, 'documentable');
    }
    public function banco(){
      return $this->hasOne(Bancos::class, 'id', 'banco_id');
    }

    //transformaciones para GET
    public function getUuidColaboradorAttribute($value)
    {
        return strtoupper(bin2hex($value));
    }
    public function getUuidAttribute()
    {
        return $this->uuid_colaborador;
    }
    public function getNombreCompletoAttribute()
    {
        return $this->nombre.' '.$this->apellido;
    }
    public function getNombreCompletoEnlaceAttribute()
    {
        $attrs = [
            "href"  => base_url("colaborador/ver/".$this->uuid_colaborador),
            "class" => "link",
            "style" => "color:blue;"
        ];
        $html   = new \Flexio\Modulo\Base\Services\Html(new \Flexio\Modulo\Base\Services\HtmlTypeFactory());
        return  $html->setType("htmlA")->setAttrs($attrs)->setHtml($this->nombre_completo)->getSalida();
    }

    //transformaciones para SET

    //scopes
    public function scopeDeEmpresa($query, $empresa_id)
    {
        return $query->where("empresa_id", $empresa_id);
    }

    public function scopeDeAcreedor($query, $acreedor_id)
    {
        return $query->whereHas("descuentos_directos", function($q) use ($acreedor_id){
            $q->where("acreedor_id", $acreedor_id);
        });
    }
    public function toArray()
  	{
  		$array = parent::toArray();

  		$array['nombre_completo'] = (!empty($this->attributes['nombre']) ? $this->attributes['nombre'] : ""). ' ' . (!empty($this->attributes['apellido']) ? $this->attributes['apellido'] : "");

  		return $array;
  	}

    public function centro_contable(){
        return $this->belongsTo(CentrosContables::class, 'centro_contable_id', 'id');
    }
    public function forma_pago(){
    	return $this->hasOne('Catalogo_orm', 'id_cat', 'forma_pago_id');
    }
    public function cargo(){
        return $this->belongsTo(Cargos::class, 'cargo_id', 'id');
    }

    public function descuentos_directos(){
        return $this->hasMany(DescuentosDirectos::class, 'colaborador_id', 'id')
            ->where("desc_descuentos.estado_id", "6");//descuentos activos
    }
 

    public function colaboradores_contratos(){
      return $this->hasMany(ColaboradoresContratos::Class, 'colaborador_id')->where("estado",1);
    }

    //Esta funcion se usa en Planilla cuando se liquida un colaborador, tomando como Salario devengado no pagado
    public function planilla_activa(){
         return $this->belongsToMany( Planilla::Class, 'pln_planilla_colaborador','colaborador_id','planilla_id')->where("estado_id",29)->where("activo",1);
    }

    public function salarios_devengados(){
      	return $this->hasMany(PagadasColaborador::Class, 'colaborador_id', 'id');
    }

  public function getTotalDevengadoAttribute(){

       return $this->salarios_devengados()->where("contrato_id", $this->colaboradores_contratos[0]->id)->sum('salario_bruto');

  }

    public function salarios_devengados_ultimos_cinco_anos(){
        $haceCinco = strtotime ( '-5 year' , strtotime ( date("Y-m-d") ) ) ;
        $haceCinco = date ( 'Y-m-d' , $haceCinco ); //Fecha de hace 5 a�os

        return $this->hasMany(PagadasColaborador::Class, 'colaborador_id', 'id')->where("fecha_cierre_planilla",">=", $haceCinco);
    }
    //falta modelos para obtener los colaboradores con acreedor_id

    public function comentario_timeline() {
        return $this->morphMany(Comentario::class,'comentable');
    }
    public function landing_comments() {
        return $this->morphMany(Comentario::class,'comentable');
    }

    //functiones para el landing_page

    public function getEnlaceAttribute()
    {
        return base_url("colaboradores/ver/".$this->uuid_colaborador);
    }
    public function getIconoAttribute(){
        return 'fa fa-users';
    }
    public function beneficiarios(){
        return $this->hasMany('Beneficiarios_orm', 'colaborador_id');
    }
    public function deducciones(){
        return $this->hasMany('Deducciones_orm', 'colaborador_id');
    }
    public function dependientes(){
        return $this->hasMany('Dependientes_orm', 'colaborador_id');
    }
}
