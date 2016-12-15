$(document).ready(function (e) {
    "use strict";
 
    $("#fecha_desde").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function (selectedDate) {
            $("#fecha_hasta").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#fecha_hasta").datepicker({
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        numberOfMonths: 1,
        onClose: function (selectedDate) {
            $("#fecha_desde").datepicker("option", "maxDate", selectedDate);
        }
    });
    $('.fecha_entrega').daterangepicker({
        autoUpdateInput: false,
        timePicker24Hour: true,
        timePicker: true,
        timePickerIncrement: 30,
        singleDatePicker: true,
        showDropdowns: true
    });
    $('.fecha_entrega').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY H:mm'));
    });

    $('.fecha_entrega').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    $(".select2").select2({
        theme: "bootstrap",
        width: "100%"
    });

});
