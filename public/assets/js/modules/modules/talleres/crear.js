var equipoTrabajoTalleres = {

   // var scope = this;
   // var crear = $('#form_crear_equipo_talleres');
    settings: {
        url: phost() + 'grupo_clientes/ajax-listar',
        formId: $('#form_crear_equipo_talleres')
    },
     botones : {
        guardar : $('#guardar')
    },
    init:function () {
        ventana = this.settings;
        this.inicializar_plugin();
        this.eventos();
    },
    eventos: function () {

        $('#guardar').click(function (e) {
            console.log("Boton guardar");
        });

    }

}();
