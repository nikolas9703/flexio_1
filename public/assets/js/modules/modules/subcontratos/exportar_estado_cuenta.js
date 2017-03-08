$("#exportarEstadoCuenta").click(function(e){

    if(_.isUndefined(subcontrato) || _.isEmpty(subcontrato)){
        console.log(subcontrato.id);
        return false;
    }
    var id = subcontrato.id;
    $("#subcontrato_id").val(id);
    $("#formExportarEstadoCuenta").submit();
});
