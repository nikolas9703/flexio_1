$(document).ready(function (e) {
    "use strict";
    //Init Bootstrap Calendar Plugin
    $(':input[data-inputmask]').inputmask();

    $('#fecha_devolucion').daterangepicker({
        autoUpdateInput: true,
        timePicker24Hour: true,
        timePicker: true,
        timePickerIncrement: 30,
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY H:mm:ss'
          },
    });
    $('#fecha_devolucion').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY H:mm:ss'));
    });

    $('#fecha_devolucion').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });


    $(".select2").select2({
        theme: "bootstrap",
        width: "100%"
    });

});
