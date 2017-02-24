//Modulo
var tablaPlanes = (function(){
  var uuid_asegura=$('input[name="campo[uuid]').val();
  var url = 'planes/ajax-listar-planes';
  var grid_id = "PlanesGrid";
  var grid_obj = $("#PlanesGrid");
  var opcionesModal = $('#opcionesModal');
  //var opcionesModalEstado = $('#opcionesModalEstado');
  //var crearContactoForm = $("#crearContactoForm");  
  
  modulo = localStorage.getItem("ml-selected");
  
  var botones = {
    opciones: ".viewOptions",
    //editar: "", 
    //crearContacto: ".agregarContacto",    
    //buscar: "#searchBtn",
    //limpiar: "#clearBtn",
    exportar: "#exportarBtn",
    //cambioGrupal: "#cambiarEstadoPlanesLnk",
  };
  
  var tabla = function(){   
    
    //inicializar jqgrid
    grid_obj.jqGrid({
        url: phost() + url,
        datatype: "json",
        colNames:[
            '',
            'Nombre',
            'Producto',
            'Ramo',
            'Comisi&oacute;n (1er año)',
            'Sobre comisi&oacute;n',
            'Desc. Comisi&oacute;n',
            '',
            ''
        ],
        colModel:[
            {name:'id', index:'id', width:30,  hidedlg:true, hidden: true},
            {name:'plan', index:'plan', width:70 },
            {name:'producto', index:'producto', width:70 },
            {name:'ramo', index:'ramo', width: 50 },
            {name:'comision', index:'comision', width: 30 },
            {name:'sobre_comision', index:'sobre_comision', width: 30 },
            {name:'desc_comision', index:'desc_comision', width: 30 },
            {name:'link', index:'link', width:50, sortable:false, hidedlg:true, align:"center", resizable:false, search:false},
            {name:'options', index:'options',hidedlg:true, hidden: true, search:false}
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            uuid: uuid_asegura,
			      modulo:modulo
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50, 100],
        rowNum: 10,
        page: 1,
        pager: "#"+ grid_id +"Pager",
        loadtext: '<p>Cargando Planes...</p>',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        search: true,
        multiselect: true,
        sortname: 'plan',
        sortorder: "ASC",
        beforeProcessing: function(data, status, xhr){
          //Check Session
        if( $.isEmptyObject(data.session) == false){
          window.location = phost() + "login?expired";
        }
        },
        loadBeforeSend: function () {
        $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
        grid_obj.find('input[type="text"]').css("width", "95% !important");
                $(this).closest("div.ui-jqgrid-view").find("#"+grid_id+"_cb, #jqgh_tablaPlanesGrid_link").css("text-align", "center");
        
      },
        beforeRequest: function(data, status, xhr){},
        loadComplete: function(data){
        $('#'+ grid_id +'NoRecords').hide();
          $('#gbox_'+ grid_id).show();
      },
      onSelectRow: function(id){
        $(this).find('tr#'+ id).removeClass('ui-state-highlight');
      },
      /*loadComplete: function(data){
        //check if isset data
        if( data['total'] == 0 ){
          $('#gbox_'+ grid_id).hide();
          $('#'+ grid_id +'NoRecords').empty().append('No se encontraron Planes.').css({"color":"#868686","padding":"30px 0 0"}).show();
        }
        else{
          $('#'+ grid_id +'NoRecords').hide();
          $('#gbox_'+ grid_id).show();
        }
      },
      onSelectRow: function(id){
        $(this).find('tr#'+ id).removeClass('ui-state-highlight');
      },*/
    });
    
    //Al redimensionar ventana
    $(window).resizeEnd(function() {
      tablaPlanes.redimensionar();
    });
    //Input Buscar en Tabla
    grid_obj.jqGrid('navGrid',grid_id,{del:false,add:false,edit:false,search:true});
    grid_obj.jqGrid('filterToolbar',{searchOnEnter : false});

  };
  
  //Inicializar Eventos de Botones
  var eventos = function(){   
    
    //Boton Opciones
    grid_obj.on("click", botones.opciones, function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();

      var id = $(this).attr("data-id");
      var rowINFO = grid_obj.getRowData(id);  
      var option = rowINFO["options"];
      //evento para boton collapse sub-menu Accion Personal
      opcionesModal.on('click', 'a[href="#collapse'+ id +'"]', function(){
        opcionesModal.find('#collapse'+ id ).collapse();
      });

      //Init Modal
      opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO["plan"] +'');
      opcionesModal.find('.modal-body').empty().append(option);
      opcionesModal.find('.modal-footer').empty();
      opcionesModal.modal('show');
    });
    
    /*$(opcionesModal).on("click", botones.crearContacto, function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();      
      //Cerrar modal de opciones
      opcionesModal.modal('hide');      
      var planes_id = $(this).attr("data-id");
      var planes_uuid = $(this).attr("data-uuid");
      //Limpiar formulario
            crearContactoForm.attr('action', phost() + 'aseguradoras/editar/' + aseguradoras_uuid);
      crearContactoForm.find('input[name*="aseguradoras_"]').remove();
      crearContactoForm.append('<input type="hidden" name="aseguradoras_id" value="'+ aseguradoras_id +'" />'); 
      crearContactoForm.append('<input type="hidden" name="agregar_contacto" value="1" />');      
      //Enviar formulario
      crearContactoForm.submit();
          $('body').trigger('click');
    });*/
    
    //Boton de Buscar Colaborador
    /*$(botones.buscar).on("click", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();
      
      buscarplanes();
    });
    
    //Boton de Reiniciar jQgrid
    $(botones.limpiar).on("click", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();
      
      recargar();
      limpiarCampos();
    });*/
    
    //Boton de Exportar aseguradores
    $(botones.exportar).on("click", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();                        
      if($('#id_tab_planes').is(':visible') == true){     
        //Exportar Seleccionados del jQgrid
        var ids = [];
        ids = grid_obj.jqGrid('getGridParam','selarrrow');
        
        //Verificar si hay seleccionados
        if(ids.length > 0){
          console.log(ids); 
          $('#ids2').val(ids);
          $('form#exportarPlanesLnk').submit();
          $('body').trigger('click');
		  if($("#cb_"+grid_id).is(':checked')) {
				$("#cb_"+grid_id).trigger('click');
			}
			else
			{
				$("#cb_"+grid_id).trigger('click');
				$("#cb_"+grid_id).trigger('click');
			}
        }
      }
    });
    
    //Boton de activar aseguradores
    /*$(botones.activar).on("click", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();                        
      if($('#tabla').is(':visible') == true){     
        //Exportar Seleccionados del jQgrid
        var ids = [];
        ids = grid_obj.jqGrid('getGridParam','selarrrow');
        
        //Verificar si hay seleccionados
        if(ids.length > 0){
        console.log(ids); 
        $('#ids').val(ids);
              $('form#exportarAseguradores').submit();
              $('body').trigger('click');
        }
          }
    });*/
  };
  
  /*$(botones.cambioGrupal).on("click", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();

        //Seleccionados del jQgrid
        var ids = [];
        ids = grid_obj.jqGrid('getGridParam','selarrrow');
        console.log(ids);
        if(ids.length > 0){
        var ids_aprobados = _.filter(ids,function(fila){
          var infoFila = $.extend({}, grid_obj.getRowData(fila));
          if($(infoFila.estado).text() == 'Por aprobar'){
            return infoFila.id;
          }
        });
        var ids_activos = _.filter(ids,function(fila){
          var infoFila = $.extend({}, grid_obj.getRowData(fila));
          if($(infoFila.estado).text() == 'Activo'){
            return infoFila.id;
          }
        });
        var ids_inactivos = _.filter(ids,function(fila){
          var infoFila = $.extend({}, grid_obj.getRowData(fila));
          if($(infoFila.estado).text() == 'Inactivo'){
            return infoFila.id;
          }
        });
        };
        
        $("#opcionesModal").on("click", ".activo", function(){
          var datos = {campo:{estado:'Activo',ids:ids_aprobados}};
          if(ids_aprobados.length > 0){
          var cambio = moduloPlanes.ajaxcambiarEstados(datos);
          cambio.done(function(response){
            var planes =response;
            _.map(planes,function(ant){
              $("#tablaPlanesGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
            });
              opcionesModal.modal('hide');
          });
        }
        });
        $("#opcionesModal").on("click", ".inactivo", function(){
            if(ids_activos.length > 0){
          var datos = {campo:{estado:'Inactivo',ids:ids_activos}};
          var cambio = moduloPlanes.ajaxcambiarEstados(datos);
          cambio.done(function(response){
            var planes =response;
            _.map(planes,function(ant){
              $("#tablaPlanesGrid").jqGrid('setCell', ant.id, 'estado', ant.estado);
            });
              opcionesModal.modal('hide');
          });
        }
        });
        
        if((ids_aprobados.length >0 && ids_activos.length ==0 && ids_inactivos.length ==0) || (ids_aprobados.length ==0 && ids_activos.length >0 && ids_inactivos.length ==0) || (ids_aprobados.length ==0 && ids_activos.length ==0 && ids_inactivos.length >=0))
        {
          
          if(ids_aprobados.length >0 && ids_activos.length ==0 && ids_inactivos.length ==0)
          {
            var options = '<a href="#" id="activargrupal" class="btn btn-block btn-outline btn-success activo">Activo</a><a href="#" class="btn btn-block btn-outline btn-success inactivo">Inactivo</a>';
          }
          
          if(ids_aprobados.length ==0 && ids_activos.length >0 && ids_inactivos.length ==0)
          {
            var options = '<a href="#" class="btn btn-block btn-outline btn-success inactivo">Inactivo</a>';
          }
          
          if(ids_aprobados.length ==0 && ids_activos.length ==0 && ids_inactivos.length >0)
          {
            var options = '<a href="#" class="btn btn-block btn-outline btn-success activo">Activo</a>';
          }
          
          opcionesModal.find('.modal-title').empty().append('Cambiar estado');
          opcionesModal.find('.modal-body').empty().append(options);
          opcionesModal.find('.modal-footer').empty();
          opcionesModal.modal('show');
        }
      });*/
      
      /*function aprobado(e){
        var self = $(this);
        var id = self.data("id");
        var datos = {campo:{estado:'aprobado',id:id}};
        var cambio = moduloAnticipo.ajaxcambiarEstado(datos);
          cambio.done(function(response){
              $("#tablaAnticiposGrid").jqGrid('setCell', id, 'estado', response.estado);
              $("#tablaAnticiposGrid").jqGrid('setCell', id, 'total', response.monto);
              opcionesModal.modal('hide');
          });
    }
    function anulado(e){
        var self = $(this);
        var id = self.data("id");
        var estado = "anulado";
        var datos = {campo:{estado:'anulado',id:id}};
        var cambio = moduloAnticipo.ajaxcambiarEstado(datos);
          cambio.done(function(response){
              $("#tablaAnticiposGrid").jqGrid('setCell', id, 'estado', response.estado);
              $("#tablaAnticiposGrid").jqGrid('setCell', id, 'total', response.monto);
              opcionesModal.modal('hide');
          });
    }*/
  
  //Reload al jQgrid
  var recargar = function(){
    
    //Reload Grid
    grid_obj.setGridParam({
      url: phost() + url,
      datatype: "json",
      postData: {
        plan: '',
        producto: '',
        ramo: '',
        comision: '',
        sobre_comision: '',
        desc_comision: ''
      }
    }).trigger('reloadGrid');
  };
  
  /*var buscarplanes = function(){
            
    var nombre = $('#nombre').val();
    var ruc = $('#ruc').val();
    var telefono = $('#telefono').val();              
    var direccion = $('#direccion').val();
    var email = $('#email').val();  
    var estado = $('#estado').val();

    if(nombre != "" || ruc != "" || telefono != "" || direccion != "" || email != "" || estado != "")
    {
      //Reload Grid
      grid_obj.setGridParam({
        url: phost() + url,
        datatype: "json",
        postData: {
          nombre: nombre,
          ruc: ruc,
          telefono: telefono,
          direccion: direccion,
          email: email,   
          estado: estado
        }
      }).trigger('reloadGrid');
    }
  };*/
  
  //Limpiar campos de busqueda
  /*var limpiarCampos = function(){
    $('#buscarPlanesForm').find('input[type="text"]').prop("value", "");
    $('#buscarPlanesForm').find('input[type="select"]').prop("value", "");
    $('#buscarPlanesForm').find('.chosen-select').val('').trigger('chosen:updated');
  };*/
  
  return{     
    init: function() {
      tabla();
      eventos();
    },
    recargar: function(){
      //reload jqgrid
      recargar();
    },
    redimensionar: function(){
      //Al redimensionar ventana      
      $(".ui-jqgrid").each(function(){
        var w = parseInt( $(this).parent().width()) - 6;
        var tmpId = $(this).attr("id");
        var gId = tmpId.replace("gbox_","");
        $("#"+gId).setGridWidth(w);
      });
    }
  };
})();
tablaPlanes.init();