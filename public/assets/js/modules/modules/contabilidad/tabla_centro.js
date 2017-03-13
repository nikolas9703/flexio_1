var tabla;
var centro = {
  settings: {
    url: phost() + 'contabilidad/ajax-listar-centros-contable',
    grid_id: "tablaCentroGrid",
    grid_obj: $("#tablaCentroGrid"),
    opcionesModal: $('#opcionesModal'),
    botonCrear: $('a.open-modal-centro_contable'),
    formId: $('#crearCentroForm')
  },
  botones: {
    opciones: "button.viewOptions",
    buscar: $("#searchBtn"),
    limpiar: $("#clearBtn"),
    editarCentro: 'a.editarCentroBtn',
    cambiarEstado: 'a.estadoCentroBtn'
  },
  ventana:{
    formId:$('#crearCentroForm')
  },
  modal: {
    crear: $('#modalCrearCentro'),
    cambiarEstado: $('#modalCambiarEstadoCentro')
  },
  init: function() {
    tabla = this.settings;
    this.tablaGrid();
    this.redimencionar();
    this.eventos();
  },
  tablaGrid: function() {
    tabla.grid_obj.jqGrid({
      url: tabla.url,
      datatype: "json",
      colNames: ['', 'Centro Contable', 'Dirección Completa', 'Estado', '', ''],
      colModel: [{
        name: 'id',
        index: 'id',
        hidedlg: true,
        key: true,
        hidden: true
      }, {
        name: 'nombre',
        index: 'nombre',
        sorttype: "text",
        sortable: true,
        width: 150
      }, {
        name: 'descripcion',
        index: 'descripcion',
        formatter: 'text',
        sortable: false
      }, {
        name: 'estado',
        index: 'estado',
        formatter: 'text',
        sortable: false,
        align: 'center'
      }, {
        name: 'opciones',
        index: 'opciones',
        sortable: false,
        align: 'center'
      }, {
        name: 'link',
        index: 'link',
        hidedlg: true,
        hidden: true
      }],
      mtype: "POST",
      postData: {
        erptkn: tkn
      },
      gridview: true,
      ExpandColClick: true,
      treeGrid: true,
      sortorder: "asc",
      hiddengrid: false,
      hoverrows: false,
      treeGridModel: 'adjacency',
      treedatatype: "json",
      ExpandColumn: 'nombre',
      treeIcons: {
        leaf: 'fa fa-university',
        plus: 'fa fa-caret-right',
        minus: 'fa fa-caret-down'
      },
      height: 'auto',
      page: 1,
      pager: "#" + tabla.grid_id + "Pager",
      rowNum: 10,
      autowidth: true,
      rowList: [10, 20, 30],
      sortname: 'nombre',
      viewrecords: true,
      beforeProcessing: function(data, status, xhr) {
        //Check Session
        var obj = data;
        if ($.isEmptyObject(obj.session) === false) {
          window.location = phost() + "login?expired";
        }
      },beforeRequest: function(data, status, xhr) {},
      loadBeforeSend: function () {//propiedadesGrid_cb
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	      $(this).closest("div.ui-jqgrid-view").find("#tabla_cb, #jqgh_tabla_link").css("text-align", "center");
	    },
      loadComplete: function(data, status, xhr) {
        var obj = data;
        //check if isset data
        if (!_.isUndefined(obj)) {
          if (obj.total === 0) {
            tabla.grid_obj.hide();
            $('#tablaCentroGridNoRecords').empty().append('No se encontraron centros.').css({
              "color": "#868686",
              "padding": "30px 0 0"
            }).show();
          } else {
            $('#tablaCentroGridNoRecords').hide();
            tabla.grid_obj.show();
          }
        }
        //---------
        // Cargar plugin jquery Sticky Objects
        //----------
        //add class to headers
        tabla.grid_obj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
        tabla.grid_obj.find('div.tree-wrap').children().removeClass('ui-icon');
        //floating headers
        $('#gridHeader').sticky({
          getWidthFrom: '.ui-jqgrid-view',
          className: 'jqgridHeader'
        });
      },
      onSelectRow: function(id) {
        $(this).find('tr#' + id).removeClass('ui-state-highlight');
      }
    });
  },
  redimencionar: function() {
    $(window).resizeEnd(function() {
      $(".ui-jqgrid").each(function() {
        var w = parseInt($(this).parent().width()) - 6;
        var tmpId = $(this).attr("id");
        var gId = tmpId.replace("gbox_", "");
        $("#" + gId).setGridWidth(w);
      });
    });
  },
  eventos: function() {
    this.botones.limpiar.click(function(e) {
      $('#buscarCentroContableForm').find('input[type="text"]').prop("value", "");
      $('#buscarCentroContableForm').find('select').prop("value", "");
      centro.recargar();
    });

    this.botones.buscar.click(function(e) {

      var nombre = $('#nombre').val();
      var descripcion = $('#descripcion').val();
      var estado = $('#estado').val();

      if (nombre !== "" || descripcion !== "" || estado !== "") {
        //Reload Grid
        tabla.grid_obj.setGridParam({
          url: tabla.url,
          datatype: "json",
          postData: {
            nombre: nombre,
            descripcion: descripcion,
            estado: estado,
            erptkn: tkn
          }
        }).trigger('reloadGrid');
      }
    });
    tabla.grid_obj.on("click", this.botones.opciones, function(e) {

      e.preventDefault();
      e.returnValue = false;
      e.stopPropagation();
      var id = $(this).data("id");

      var rowINFO = $.extend({}, tabla.grid_obj.getRowData(id));
      var options = rowINFO.link;

      tabla.opcionesModal.find('.modal-title').empty().append('Opciones: ' + rowINFO.nombre + '');
      tabla.opcionesModal.find('.modal-body').empty().append(options);
      tabla.opcionesModal.find('.modal-footer').empty();
      tabla.opcionesModal.modal('show');

    });
    tabla.opcionesModal.on("click", this.botones.editarCentro, function(e) {
      tabla.opcionesModal.modal("hide");
      var uuid = $(this).data("uuid");
      console.log(uuid);
      var parametros = {
        uuid_centro: uuid
      };


      var datos = moduloContabilidad.getCentro(parametros);
      var selectDatos = moduloContabilidad.getListaCentro();
      selectDatos.success(function(data){
        tabla.formId.find('.chosen-select').empty();
      });

      selectDatos.done(function(data){
        console.log(data);
        var items = $.parseJSON(data);
        tabla.formId.find('.chosen-select').append($('<option>', {
            value: '',
            text : 'Seleccione'
        }));
        $.each(items, function (i, item) {
          tabla.formId.find('.chosen-select').append($('<option>', {
              value: item.id,
              text : item.nombre
          }));
        });
        datos.success(function(data) {
          //popular
          console.log(data);
          tabla.formId.find('#idEdicion').remove();
          var datos_centro = $.parseJSON(data);
          tabla.formId.append('<input type="hidden" name="campo[id]" id="idEdicion" value="' + datos_centro.id + '">');
          $('input[name="campo[nombre]"]').val(datos_centro.nombre);
          $('input[name="campo[descripcion]"]').val(datos_centro.descripcion);

        });
        datos.done(function(data) {

          var datos_centro = $.parseJSON(data);
          if (datos_centro.padre_id === 0) {
            tabla.formId.find('.chosen-select').prop('value', '');
          } else {
            tabla.formId.find('.chosen-select').prop('value', datos_centro.padre_id);

            tabla.formId.find('.chosen-select option[value="'+ datos_centro.padre_id+'"]').prop('selected',true);
          }

          tabla.formId.find('#padre_idCheck').prop('checked',false);
          if(!tabla.formId.find('#padre_idCheck').is(':checked')) {
            tabla.formId.find('.chosen-select').prop('disabled',true);
          }
          centro.modal.crear.find('.modal-title').empty().html('Editar: Centro Contable');
          centro.modal.crear.modal('show');
        });
      });


    });
    tabla.opcionesModal.on("click", this.botones.cambiarEstado, function(e) {

      tabla.opcionesModal.modal("hide");
      centro.modal.cambiarEstado.find('.modal-title').empty().html('Cambiar Estado');

      var uuid = $(this).data("uuid");
      var estado = $(this).data("estado");
      var parametros = {
        uuid_centro: uuid,
        estado: estado
      };
      var ajaxEstado = moduloContabilidad.cambiarEstadoCentroContable(parametros);

      centro.modal.cambiarEstado.find('.modal-footer').empty();
      centro.modal.cambiarEstado.modal('show');
      var progress = $("#loading-progress").progressTimer({
        timeLimit: 300,
        completeStyle: 'progress-bar-success',
        onFinish: function() {
          centro.modal.cambiarEstado.modal('hide');
          tabla.grid_obj.trigger('reloadGrid');
          console.log('completed!');
        }
      });
      ajaxEstado.fail(function() {
        progress.progressTimer('error', {
          errorText: 'ERROR!',
          onFinish: function() {
            console.log('hubo un error en cambiar el estado');
          }

        });
      });
      ajaxEstado.done(function(data) {
        var respuesta = $.parseJSON(data);
        if (respuesta.estado == 200) {
          $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-success"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
          progress.progressTimer('complete');
        }else if(respuesta.estado == 500){
          $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'+respuesta.mensaje+'</div>');
          progress.progressTimer('error',{
              warningStyle: 'progress-bar-warning',
              errorText: respuesta.mensaje
          });
        }

      });

    });

    tabla.botonCrear.click(function(e) {
      tabla.formId.find('#idEdicion').remove();
      centro.modal.crear.find('.modal-title').empty().html('Crear: Centro Contable');
      centro.modal.crear.modal('show');
      var selectDatos = moduloContabilidad.getListaCentro();
      selectDatos.success(function(data){
        tabla.formId.find('.chosen-select').empty();
      });

      selectDatos.done(function(data){
        var items = $.parseJSON(data);
        tabla.formId.find('.chosen-select').append($('<option>', {
            value: '',
            text : 'Seleccione'
        }));
        $.each(items, function (i, item) {
          tabla.formId.find('.chosen-select').append($('<option>', {
              value: item.id,
              text : item.nombre
          }));
        });
      });
        tabla.formId.find('.chosen-select').prop('value','');
        tabla.formId.find('#padre_idCheck').prop('checked',false);
        if(!tabla.formId.find('#padre_idCheck').is(':checked')) {
          tabla.formId.find('.chosen-select').prop('disabled',true);
        }

    });

    tabla.formId.on('click','#padre_idCheck',function(e){
      if(!tabla.formId.find('#padre_idCheck').is(':checked')) {
        tabla.formId.find('.chosen-select').prop('disabled',true);
      }else{
        tabla.formId.find('.chosen-select').prop('disabled',false);
      }
    });

  },
  recargar: function() {

    //Reload Grid
    tabla.grid_obj.setGridParam({
      url: tabla.url,
      datatype: "json",
      postData: {
        nombre: '',
        descripcion: '',
        estado:'',
        erptkn: tkn
      }
    }).trigger('reloadGrid');

  }

};

(function() {
  centro.init();
})();
