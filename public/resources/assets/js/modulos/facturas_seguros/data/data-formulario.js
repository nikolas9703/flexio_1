var formulario = {
    id:'',
    cliente_id:'',
    termino_pago:'al_contado',
    saldo_pendiente: 0,
    credito_favor: 0,
    fecha_desde: moment().format('DD/MM/YYYY'),
    fecha_hasta: moment().add(30,'days').format('DD/MM/YYYY'), // fecha 30 dias
    created_by:window.usuario_id,
    item_precio_id:'',
    lista_precio_alquiler_id:'',
    centro_contable_id:'',
    centro_facturable:[],
    centro_facturacion_id:'',
    estado:'por_aprobar',
    comentario:''
};

module.exports = {
    formulario : formulario
};
