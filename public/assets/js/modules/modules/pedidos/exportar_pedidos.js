$("#exportar-pedido").click(function(e){

    if(_.isUndefined(pedido) || _.isEmpty(pedido)){
        return false;
    }
    var id = pedido.id;
    $("#pedido").val(id);
    $("#formImprimirPedido").submit();
});