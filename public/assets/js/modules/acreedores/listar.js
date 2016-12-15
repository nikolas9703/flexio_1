$(function(){
    "use strict";
    //Init Bootstrap Calendar Plugin
    $('#fecha1, #fecha2').daterangepicker({
        locale:{
            format: 'DD-MM-YYYY',
        },
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }).val('');

    $(".chosen-select").chosen({width: "100%"});
    
    if(typeof mensaje_clase !== "undefined" && mensaje_clase != 0)
    {
        if(mensaje_clase == "alert-success")
        {
            toastr.success("¡&Eacute;xito! Se ha guardado correctamente el << Acreedor/RRHH >>.");
        }
        else
        {
            toastr.error("¡Error! Su solicitud no fue procesada en el << Acreedor/RRHH >>.");
        }
    }

});
