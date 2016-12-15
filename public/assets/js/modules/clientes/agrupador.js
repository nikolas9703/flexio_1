var ventana;
var crear = {
    modal: {
        crear: $('#modalAgrupadorCliente')
    },
    settings: {
        url: phost() + 'clientes/ajax-listar',
        botonCancelar: $("#cancelarBtn"),
        botonGuardar: $("#guardarBtn"),
        botonAgrupar: $('#agrupadorClientesBtn'),
        formId: $('#crearAgrupadorClienteForm')
    },
    init: function () {
        ventana = this.settings;
        // this.inicializar_plugin();
        this.eventos();
    },
    eventos: function () {

        ventana.botonCancelar.click(function (e) {
            // console.log("boton cancelar");
            //crear.limpiarFormulario();
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            crear.modal.crear.modal('hide');
        });

        //Boton agrupar
        ventana.botonAgrupar.click(function (e) {
            console.log("Paso aqui");
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            ventana.formId.find('#idEdicion').remove();

            crear.modal.crear.modal('show');
            var id ="";
            if(!_.isUndefined(localStorage)){

                 id = localStorage.getItem('id_grupo_cliente');

            }
            if(id !== ""){
                ventana.formId.find('#select-id').prop('value', id);
               // localStorage.removeItem("id_grupo_cliente");
            }

            /*ventana.formId.find('#idEdicion').remove();
             crear.modal.crear.modal('show');

             crear.formId.find('#select-id').prop('value', '');
             ventana.formId.find('#padre_idCheck').prop('checked', false);
             if (!ventana.formId.find('#padre_idCheck').is(':checked')) {
             ventana.formId.find('#select-id').prop('disabled', true);
             }*/
        });

        /*  ventana.formId.on('click','#padre_idCheck',function(e){
         if(!ventana.formId.find('#padre_idCheck').is(':checked')) {
         ventana.formId.find('#select-id').prop('disabled',true);
         }else{
         ventana.formId.find('#select-id').prop('disabled',false);
         }
         });*/

        ventana.botonGuardar.click(function (e) {
            //console.log("Boton guardar");
            var self = $(this);
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var ids = [];
            ids = $('#tablaClientesGrid').jqGrid('getGridParam', 'selarrrow');
            console.log(ids);

            var id = ventana.formId.find('#select-id').val();
            console.log(id);
            var clientesIds = [];
            clientesIds.push(ids);
            var idGrupo = [];
            idGrupo.push(id);
            console.log(idGrupo);
            $.ajax({
                url: phost() + 'clientes/guardar-agrupador',
                data: {
                    erptkn: tkn,
                    id_clientes: clientesIds,
                    id_grupo: idGrupo
                },
                type: "POST",
                dataType: "json",
                cache: false
            }).done(function (json) {

              //  console.log(json.mensaje);
               //// $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + json.mensaje + '</div>');
                        //crear.recargar();
                       // moduloGrupoClientes.getListaGrupo();
                       //crear.recargar();
                    //  $('#tablaClientesGrid').jqGrid().trigger('reloadGrid');
                 crear.modal.crear.modal('hide');
                 window.location = phost() + "grupo_clientes/listar";
                localStorage.removeItem("id_grupo_cliente");
                //Check Session
                /* if ($.isEmptyObject(json.session) == false) {
                 window.location = phost() + "login?expired";
                 }
                 
                 //If json object is empty.
                 if ($.isEmptyObject(json.results[0]) == true) {
                 return false;
                 }*/

               // $class_mensaje = 'alert-success';

                //Mostrar Mensaje
               // mensaje_alerta(json.results['mensaje'], $class_mensaje);

                //Recargar grid si la respuesta es true
                // if (json.results[0]['respuesta'] == true)
                //  {
                //  
                //Recargar Grid
               /* dom.jqGrid.setGridParam({
                    url: phost() + st.segmento2 + '/ajax-listar',
                    datatype: "json",
                    postData: {
                        nombre: '',
                        telefono: '',
                        correo: '',
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');*/
                //  }
            });

        });

    },
    limpiarFormulario: function () {
        var validator = ventana.formId.validate();
        validator.resetForm();
        ventana.formId.trigger("reset");
    },
    inicializar_plugin: function () {
        //ventana.formId.validate({
        //  focusInvalid: true,
        //   ignore: ".ignore",
        //  wrapper: ''
        // });
    },
    recargar: function () {
        // tablaGrupoClientes().reloadGrid();

    }

};

(function () {
    crear.init();
})();
