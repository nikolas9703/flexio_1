var ventana;
var crear = {
    modal: {
        crear: $('#modalCrearGrupoCliente')
                //cambiarEstado: $('#modalCambiarEstadoCentro')
    },
    settings: {
        url: phost() + 'grupo_clientes/ajax-listar',
        botonCancelar: $("#cancelarBtn"),
        botonGuardar: $("#guardarBtn"),
        botonCrear: $('a.open-modal-crear'),
        formId: $('#crearGrupoClienteForm')
    },
    init: function () {
        ventana = this.settings;
        this.inicializar_plugin();
        this.eventos();
    },
    eventos: function () {

        ventana.botonCancelar.click(function (e) {
            console.log("boton cancelar");
            crear.limpiarFormulario();
            crear.modal.crear.modal('hide');
        });
        
        //Boton crear
        ventana.botonCrear.click(function (e) {
            ventana.formId.find('#idEdicion').remove();
             $('#crearGrupoClienteForm').find('#ids').val('');
            crear.modal.crear.modal('show');
           /* var selectDatos = moduloGrupoClientes.getListaGrupo();
            selectDatos.success(function (data) {
                ventana.formId.find('.chosen-select').empty();
            });

            selectDatos.done(function (data) {
                var items = $.parseJSON(data);
                ventana.formId.find('.chosen-select').append($('<option>', {
                    value: '',
                    text: 'Seleccione'
                }));
                $.each(items, function (i, item) {
                    ventana.formId.find('#select-id').append($('<option>', {
                        value: item.id,
                        text: item.nombre
                    }));
                });
            });*/
            ventana.formId.find('#select-id').prop('value', '');
            ventana.formId.find('#padre_idCheck').prop('checked', false);
            if (!ventana.formId.find('#padre_idCheck').is(':checked')) {
                ventana.formId.find('#select-id').prop('disabled', true);
            }
        });
        
        ventana.formId.on('click','#padre_idCheck',function(e){
      if(!ventana.formId.find('#padre_idCheck').is(':checked')) {
        ventana.formId.find('#select-id').prop('disabled',true);
      }else{
        ventana.formId.find('#select-id').prop('disabled',false);
      }
    });
       
        ventana.botonGuardar.click(function (e) {
            console.log("Boton guardar");
            e.preventDefault();
            var selfButton = this;
            if (ventana.formId.validate().form() === true)
            {
                //$(selfButton).unbind("click");
                ventana.formId.find('#select-id').prop('disabled', false);
                var guardar = moduloGrupoClientes.crearGrupoCliente(ventana.formId);
                // console.log(guardar);
                guardar.done(function (data) {
                     //console.log(data);
                    var respuesta = $.parseJSON(data);
                     console.log(respuesta);
                    if (respuesta.estado == 200)
                    {
                        $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                        //crear.recargar();
                       // moduloGrupoClientes.getListaGrupo();
                       //crear.recargar();
                      $('#tablaGrupoClientesGrid').jqGrid().trigger('reloadGrid');
                    }
                   // $(selfButton).bind("click");
                    crear.limpiarFormulario();
                    crear.modal.crear.modal('hide');
                });

            }
        });
        
    },
    limpiarFormulario:function(){
     var validator = ventana.formId.validate();
     validator.resetForm();
     ventana.formId.trigger("reset");
   },
   inicializar_plugin:function(){
     ventana.formId.validate({
       focusInvalid: true,
       ignore: ".ignore",
       wrapper: ''
     });
   }, 
     recargar: function() {  
         tablaGrupoClientes().reloadGrid();
  
  }

};

(function () {
    crear.init();
})();
