var tabla;
var centro = {
  settings: {
    url: phost() + 'contabilidad/ajax-listar-cuentas-contables',
    url_exportar : phost() + 'contabilidad/ajax-exportar-cuentas-contables',
    grid_id: "tablaCentroGrid",
    grid_obj: $("#tablaCentroGrid"),
   },
  botones: {
  //  opciones: "button.viewOptions",
    buscar: $("#searchBtn"),
    limpiar: $("#clearBtn"),
    habilDestarCuentaBtn: 'a.habilDestarCuentaBtn',
    exportar: $("#exportarBtn"),
    deshabilitar: $('#moduloOpciones').find('#deshabilitarBtn'),
    habilitar: $('#moduloOpciones').find('#habilitarBtn')

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
      colNames: ['', 'Cuenta contable', 'Estado'],
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
        name: 'estado',
        index: 'estado',
        formatter: 'text',
        sortable: false,
      }],
      mtype: "POST",
      postData: {
        erptkn: tkn,
        centro_id : centro_id
      },
      gridview: true,
      multiselect: false,
      sortorder: "asc",
      hiddengrid: false,
      hoverrows: false,
      treedatatype: "json",
      ExpandColumn: 'nombre',
      height: 'auto',
      page: 1,
      pager: "#" + tabla.grid_id + "Pager",
      rowNum: 10,
      autowidth: true,
      rowList: [10, 20,50, 100],
      sortname: 'id',
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


     tabla.grid_obj.on("click", this.botones.habilDestarCuentaBtn, function(e) {

    		var centro_id = $(this).attr('data-centro-id');
    		var cuenta_id = $(this).attr('data-cuenta-id');
    		var tipo = $(this).attr('data-tipo');
        $("#cuenta_"+cuenta_id).text('Un momento').css('background',"#cccccc");

      $.ajax({
  			url: phost() + 'contabilidad/ajax-habilitar-cuenta',
  			data: {
  				erptkn: tkn,
          tipo:tipo,
          cuenta_id:cuenta_id,
          centro_id:centro_id
   			},
  			type: "POST",
  			dataType: "json",
  			cache: false,
  		}).done(function(json) {
   				 //Check Session
  				if( $.isEmptyObject(json.session) == false){
  					window.location = phost() + "login?expired";
  				}
  				//If json object is empty.
  				if($.isEmptyObject(json) == true){
  					return false;
  				}

  				//Mostrar Mensaje
  				if(json.response == false){
  					toastr.error(json.mensaje);
  				}else{
  				      centro.recargar();
  				}
   		});
    });

    this.botones.limpiar.click(function(e) {
      $('#buscarCentroContableForm').find('input[type="text"]').prop("value", "");
      $('#buscarCentroContableForm').find('select').prop("value", "");
      centro.recargar();
    });

    this.botones.buscar.click(function(e) {

      var nombre = $('#nombre').val();
      var codigo = $('#codigo').val();
      var estado = $('#estado').val();

      if (nombre !== "" || codigo !== "" || estado !== "") {
        //Reload Grid
        tabla.grid_obj.setGridParam({
          url: tabla.url,
          datatype: "json",
          postData: {
            centro_id:centro_id,
            nombre: nombre,
            codigo: codigo,
            estado: estado,
            erptkn: tkn
          }
        }).trigger('reloadGrid');
      }
    });

    this.botones.exportar.click(function (e) {

      var nombre = $('#nombre').val();
      var codigo = $('#codigo').val();
      var estado = $('#estado').val();

      $("#nombre_cuenta").val(nombre);
      $("#codigo_cuenta").val(codigo);
      $("#estado_cuenta").val(estado);
      $("#centro_id_cuenta").val(centro_id);

      $("#formExportarCuentasContables").submit();

    });

    this.botones.deshabilitar.click(function(e) {

      toastr.error('Se estan deshabilitando las cuentas...');
      $.ajax({
        url: phost() + 'contabilidad/ajax-deshabilitar-cuentas-total',
        data: {
          erptkn: tkn,
        },
        type: "POST",
        dataType: "json",
        cache: false,
      }).done(function(json) {
        //Check Session
        if( $.isEmptyObject(json.session) == false){
          window.location = phost() + "login?expired";
        }
        //If json object is empty.
        if($.isEmptyObject(json) == true){
          return false;
        }
        //Mostrar Mensaje
        if(json.response == false){
          toastr.error(json.mensaje);
        }else {
          //$(this).remove();
          window.setTimeout(
              $('#moduloOpciones').find('#deshabilitarBtn').empty().append('Habilitar').attr('id', '#habilitarBtn'),
              1000);
          window.location.reload();
         // $("#moduloOpciones").hide().fadeIn('fast');
         //$( "#moduloOpciones" ).load(window.location.href + " #moduloOpciones" );
         // centro.recargar();

        }
      });
    });

    this.botones.habilitar.click(function(e) {

      toastr.success('Se estan habilitando las cuentas...');
      $.ajax({
        url: phost() + 'contabilidad/ajax-habilitar-cuentas-total',
        data: {
          erptkn: tkn,
          uuid_cuenta: window.uuid_cuenta
        },
        type: "POST",
        dataType: "json",
        cache: false,
      }).done(function(json) {
        //Check Session
        if( $.isEmptyObject(json.session) == false){
          window.location = phost() + "login?expired";
        }
        //If json object is empty.
        if($.isEmptyObject(json) == true){
          return false;
        }
        //Mostrar Mensaje
        if(json.response == false){
          toastr.error(json.mensaje);
        }else {
          //$(this).remove();
          window.setTimeout(
              $('#moduloOpciones').find('#habilitarBtn').empty().append('Deshabilitar').attr('id','#deshabilitarBtn'),
              1000);
          window.location.reload();
          //$( "#moduloOpciones" ).load(window.location.href + " #moduloOpciones" );
          //centro.recargar();

        }
      });
    });
  },
  recargar: function() {

    //Reload Grid
    tabla.grid_obj.setGridParam({
      url: tabla.url,
      datatype: "json",
      postData: {
        nombre: '',
        codigo:'',
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
