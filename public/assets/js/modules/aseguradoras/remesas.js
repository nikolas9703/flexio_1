$(function(){
"use strict";
	//Init Bootstrap Calendar Plugin
    $('#fecha1, #fecha2').daterangepicker({
        format: 'DD-MM-YYYY',
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }).val('');

     $(".chosen-select").chosen({width: "100%"});

    $('.estado').on("change", function(){
        $('#estadoRemesa').find('h3').empty().append("Estado: "+$(this).val());
    });


});
