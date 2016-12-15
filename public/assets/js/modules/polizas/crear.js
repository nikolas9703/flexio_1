$(function(){
    "use strict";
    //Init Bootstrap Calendar Plugin
    $('#inicio_vigencia, #fin_vigencia').daterangepicker({
        format: 'DD-MM-YYYY',
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }).val('');

    $(".chosen").chosen({width: "100%"});

});
