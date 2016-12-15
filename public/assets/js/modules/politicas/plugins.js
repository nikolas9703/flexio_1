$(document).ready(function (e) {
    "use strict";
    //Init Bootstrap Calendar Plugin
    $(':input[data-inputmask]').inputmask();

    $(".select2").select2({
        theme: "bootstrap",
        width: "100%"
    });

});
