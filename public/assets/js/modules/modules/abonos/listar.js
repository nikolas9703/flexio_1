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

});
