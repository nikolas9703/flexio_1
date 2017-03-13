$(function(){
    var items = {
    aprobado:{
        name:"Aprobado",
        callback:cambiarEstado
    },
    anulado:{
        name:"Anulado",
        callback:cambiarEstado
    }
};

    $.contextMenu({
    className: 'data-title',
    selector: 'label.menu-estado',
    items: items
});

function cambiarEstado(itemKey, opt){
    var estado =  itemKey;
    var id = opt.$trigger[0].id;
    var datos = {campo:{id:id,estado:estado}};
    var cambio = moduloAnticipo.ajaxcambiarEstado(datos);
    cambio.done(function(response){
        $("#tablaAnticiposGrid").jqGrid('setCell', id, 'estado', response.estado);
        $("#tablaAnticiposGrid").jqGrid('setCell', id, 'total', response.monto);

        return false;
    });
}

 $('.data-title').attr('data-menutitle', "Some JS Title");

});
