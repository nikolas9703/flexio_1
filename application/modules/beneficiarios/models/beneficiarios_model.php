<?php
class Beneficiarios_model extends CI_Model
{
    private $modulo;
    private $ruta_modulos;
    private $modulo_controlador;

    public function __construct() {
        parent::__construct ();

        $this->modulo = $this->router->fetch_module();
        $this->ruta_modulos = $this->config->item('modules_locations');
        $this->modulo_controlador = $this->router->fetch_class();
    }

    /**
     * Conteo de los beneficiarios existentes
     *
     * @return [array] [description]
     */
    function contar_beneficiarios($clause)
    {
        $fields = array (
            "cl.id_cliente"
        );
        $result = $this->db->select($fields)
            ->distinct()
            ->from('cl_clientes AS cl')
            ->join('cl_clientes_cat AS ccat', 'ccat.id_cat = cl.id_tipo_cliente', 'LEFT')
            ->join('usuarios AS usr', 'usr.uuid_usuario = cl.id_asignado', 'LEFT')
            ->join('cl_clientes_sociedades AS csoc', 'csoc.uuid_cliente = cl.uuid_cliente', 'LEFT')
            ->join('cl_cliente_sociedades_contactos AS csocon', 'csocon.uuid_cliente = cl.uuid_cliente', 'LEFT')
            ->join('con_contactos AS con', 'con.uuid_contacto = csocon.uuid_contacto', 'LEFT')
            ->join('cl_cliente_correos AS cco', 'cco.uuid_cliente = cl.uuid_cliente', 'LEFT')
            ->join('cl_cliente_telefonos AS ctel', 'ctel.uuid_cliente = cl.uuid_cliente', 'LEFT')
            ->where($clause)
            ->get()
            ->result_array();
        return $result;
    }

    /**
     * [list beneficiarios description]
     *
     * @param integer $sidx [description]
     * @param integer $sord [description]
     * @param integer $limit [description]
     * @param integer $start [description]
     * @return [array] [description]
     */
    function listar_beneficiarios($clause, $sidx = 1, $sord = 1, $limit = 0, $start = 0)
    {
        $i= 0;
        $result = array();
        $fields = array (
            "cl.id_cliente",
            "HEX(cl.uuid_cliente) AS uuid_cliente",
            "cl.nombre",
            "cl.razon_social",
            "cl.apellido",
            "cl.cedula",
            "cl.ruc",
            "cl.imagen_archivo",
            "cl.id_tipo_cliente",
            "CONCAT_WS(' ', IF(usr.nombre != '', usr.nombre, ''), IF(usr.apellido != '', usr.apellido, '')) AS usuario_asignado",
        );
        $query = $this->db->select($fields)
            ->distinct()
            ->from('cl_clientes AS cl')
            ->join('cl_clientes_cat AS ccat', 'ccat.id_cat = cl.id_tipo_cliente', 'LEFT')
            ->join('usuarios AS usr', 'usr.uuid_usuario = cl.id_asignado', 'LEFT')
            ->join('cl_clientes_sociedades AS csoc', 'csoc.uuid_cliente = cl.uuid_cliente', 'LEFT')
            ->join('cl_cliente_sociedades_contactos AS csocon', 'csocon.uuid_cliente = cl.uuid_cliente', 'LEFT')
            ->join('con_contactos AS con', 'con.uuid_contacto = csocon.uuid_contacto', 'LEFT')
            ->join('cl_cliente_correos AS cco', 'cco.uuid_cliente = cl.uuid_cliente', 'LEFT')
            ->join('cl_cliente_telefonos AS ctel', 'ctel.uuid_cliente = cl.uuid_cliente', 'LEFT')
            ->where($clause)
            ->order_by($sidx, $sord)
            ->limit($limit, $start)
            ->get()
            ->result_array();
        if(!empty($query)){
            foreach($query as $row){
                $uuid = $row['uuid_cliente'];
                list($fecha, $hace) = $this->actividades_model->seleccionar_ultimo_contacto(
                    array (
                        "act.uuid_cliente = UNHEX('$uuid')" => NULL,
                        "act.completada = 1" => NULL
                    )
                );
                $result[$i]['ultimo_contacto'] = $hace;
                $result[$i]['id_cliente'] 	= $row['id_cliente'];
                $result[$i]['uuid_cliente'] = $row['uuid_cliente'];
                $result[$i]['nombre'] 		= $row['nombre'];
                $result[$i]['nombre_comercial'] 		= $row['nombre'];
                $result[$i]['razon_social'] 		= $row['razon_social'];
                $result[$i]['apellido'] 	= $row['apellido'];
                $result[$i]['ruc'] 	= $row['ruc'];
                $result[$i]['cedula'] 		= $row['cedula'];
                $result[$i]['imagen_archivo']  = $row['imagen_archivo'];
                $result[$i]['id_tipo_cliente'] = $row['id_tipo_cliente'];
                $result[$i]['usuario_asignado']= $row['usuario_asignado'];
                ++$i;
            }

        }


        return $result;
    }
}
?>