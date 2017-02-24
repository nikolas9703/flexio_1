var tablaRamos;
var ramos = {
  settings: {
    url: phost() + 'configuracion_seguros/ajax_listar_ramos',
    grid_id : "#RamosGrid",
    grid_obj : $("#RamosGrid"),
    opcionesModal : $('#opcionesModal'),
    formId: $('#crearRamosForm'),
    modalCambiarEstado: $('#estadoRamoModal'),
    getRol:$("#rol"),
    selectUsers:$('#usuario')
  },
  botones:{
   opciones: "button.viewOptions",
   buscar: $("#searchBtn"),
   limpiar: $("#clearBtn"),
   editarCuenta: 'a.editarRamoBtn',
   cambiarEstado: 'a.cambiarEstadoRamoBtn',
   filtrar: $('a.filtro'),
   exportar: "#exportarRamosLnk",
   cambioGrupal: "#cambiarEstadoRamoLnk",
   estadoModalTest:"a.test"

 },
 init:function(){
   tablaRamos = this.settings;
   this.tablaGrid();
   this.redimencionar();
   this.eventos();
 },
 redimencionar:function(){
   $(window).resizeEnd(function() {
     $(".ui-jqgrid").each(function(){
       var w = parseInt( $(this).parent().width()) - 6;
       var tmpId = $(this).attr("id");
       var gId = tmpId.replace("gbox_","");
       $("#"+gId).setGridWidth(w);
     });
   });
 },
 eventos:function(){

  tablaRamos.grid_obj.on("click", this.botones.estadoModalTest, function(e){

    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var id = $(this).data("id");

    var rowINFO = $.extend({},tablaRamos.grid_obj.getRowData(id));
    var options = rowINFO.modalstate;
    tablaRamos.opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO.nombre +'');
    tablaRamos.opcionesModal.find('.modal-body').empty().append(options);
    tablaRamos.opcionesModal.find('.modal-footer').empty();
    tablaRamos.opcionesModal.modal('show');


  });


  tablaRamos.grid_obj.on("click", this.botones.opciones, function(e){

    e.preventDefault();
    e.returnValue=false;
    e.stopPropagation();
    var id = $(this).data("id");

    var rowINFO = $.extend({},tablaRamos.grid_obj.getRowData(id));
    var options = rowINFO.link;

    tablaRamos.opcionesModal.find('.modal-title').empty().append('Opciones: '+ rowINFO.nombre +'');
    tablaRamos.opcionesModal.find('.modal-body').empty().append(options);
    tablaRamos.opcionesModal.find('.modal-footer').empty();
    tablaRamos.opcionesModal.modal('show');


  });
  
  tablaRamos.getRol.on("change", function(){ 
   
    if(tablaRamos.getRol.val()){
      parametros={idRol:tablaRamos.getRol.val()}; 

      var ajaxEstado = moduloAseguradora.getRoles(parametros);
      ajaxEstado.done(function(data) {
        tablaRamos.selectUsers.empty();
        var respuesta = $.parseJSON(data); 
        tablaRamos.selectUsers.append('<option value="todos">Todos</option>');
        $.map( respuesta, function( item ) {
         tablaRamos.selectUsers.append('<option value="' + item.id + '">' + item.nombre +' '+item.apellido + '</option>');
       });

        tablaRamos.selectUsers.trigger('chosen:updated');

      });  
    }
  }); 

  tablaRamos.selectUsers.on("change", function(){  
    if($(this).val()=="todos"){
      $("#usuario option[value='todos']").remove();
      $("#usuario > option").each(function() {
        if (this.value!="todos"){
            $(this).prop("selected","selected");
        }
        tablaRamos.selectUsers.trigger('chosen:updated');
      });
    }
  });
  this.botones.limpiar.click(function(e){
   e.preventDefault();
   tablaRamos.grid_obj.setGridParam({
     url: tablaRamos.url,
     datatype: "json",
     postData: {
       nombre: '',
       erptkn: tkn
     }
   }).trigger('reloadGrid');

       //Reset Fields
       $('#nombre').val('');
     });


  tablaRamos.opcionesModal.on("click", this.botones.cambiarEstado, function(e) {

    tablaRamos.opcionesModal.modal('hide');
    var id = $(this).data('id');
    var estado =  $(this).data('estado');
    var parametros = {id:id, estado:estado};
    var politicas_general=moduloAseguradora.ajaxcambiarObtenerPoliticasGeneral();
    var permisos_generales=politicas_general.success(function (data){

     var rowINFO = $.extend({},tablaRamos.grid_obj.getRowData(id));
     var estadoReal = rowINFO.estadoReal;  

     if(data >0)
     {  
       var politicas = moduloAseguradora.ajaxcambiarObtenerPoliticas();

       var permisos1=politicas.success(function (data) {
         var permisos=[];

         $.each(data, function(i,filename) {
          permisos.push(filename);
        });

         if(permisos.indexOf(11,0)!=-1 || permisos.indexOf(12,0)!=-1)
         {
          console.log(data); 
          if(estadoReal==1){
           if(permisos.indexOf(11,0)!=-1){

            updateState(parametros);

          }else{
            tablaRamos.modalCambiarEstado.modal('hide');
            
            $("#mensaje_info").html('<div id="danger-alert" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> Usted no tiene permisos para cambiar a este estado </div>');
          }
        }
        if(permisos.indexOf(12,0)!=-1){
          updateState(parametros);

        }else{
          tablaRamos.modalCambiarEstado.modal('hide');
          $("#mensaje_info").html('<div id="danger-alert" class="alert alert-danger"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> Usted no tiene permisos para cambiar a este estado </div>');
        }
        
      }
      else{
        updateState(parametros);
      }
    });
     }
   });
  });

  $(this.botones.exportar).on("click", function(e){




    $('form#exportarAseguradores').submit();
    $('body').trigger('click');


  });

  tablaRamos.opcionesModal.on("click", this.botones.editarCuenta, function(e) {
    var id = $(this).data('id');
    var confir=0;
    var parametros = {id:id};

    var buscarCuenta = moduloAseguradora.getRamo(parametros);
    buscarCuenta.success(function(data){
        //popular
        var datos_ramo = $.parseJSON(data);
        var rol =$('#rol');
        var roles=[];
        var userArray=[];
        var form =$('#crearRamosForm').validate({});
        var usuarios=$('#usuario');
        $("#idEdicion").remove();
        tablaRamos.formId.append('<input type="hidden" name="id" id="idEdicion" value="' + datos_ramo.id + '">');
        tablaRamos.formId.find('#nombre').val(datos_ramo.nombre);
        $('#descripcion').val(datos_ramo.descripcion);
        $('#codigo').val(datos_ramo.padre_id);
        $('#codigo_ramo').val(datos_ramo.codigo_ramo);
        $("#tipo_interes_ramo").val(datos_ramo.interes_asegurado);
        $("#tipo_poliza_ramo").val(datos_ramo.tipo_poliza);
        if(datos_ramo.hasRequest){
         $('#codigo_ramo').attr('readonly', true);
         $('#nombre').attr('readonly', true);
       }else{
        $('#codigo_ramo').attr('readonly', false);
        $('#nombre').attr('readonly', false);
      }
      if(datos_ramo.agrupador){
        $("#isGrouper").prop("checked",true);
        $('#codigo_ramo').rules(
         "add",{ required: false, 

         });

        $('#descripcion').rules(
         "add",{ required: false, 

         });
        $('#has-error').hide();

        $('select').each(function() {
          $(this).rules('add', {
            required: false,

          });
        });
      }
        //$('.chosen-select').val('').trigger('chosen:updated');
        $.map( datos_ramo.roles, function( val, i ) {

          roles.push(datos_ramo.roles[i].id_rol); 
          
        }); 

        $.map(datos_ramo.usuarios, function( item ) {
          userArray.push(item.id_usuario); 
          

        });
        parametros={idRol:roles};
        var ajaxEstado = moduloAseguradora.getRoles(parametros);
        ajaxEstado.done(function(data) {
          tablaRamos.selectUsers.empty();
          var respuesta = $.parseJSON(data); 
          $.map( respuesta, function( item ) {
           tablaRamos.selectUsers.append('<option value="' + item.id + '">' + item.nombre +' '+item.apellido + '</option>');
           
         });
          rol.val(roles).trigger("chosen:updated");

          usuarios.val(userArray).trigger("chosen:updated");
          tablaRamos.selectUsers.trigger('chosen:updated');
        });  
        
        form.resetForm();
        tablaRamos.formId.find('#cuenta_id').val(datos_ramo.formulario_solic);
        tablaRamos.opcionesModal.modal("hide");
        padre_id=datos_ramo.padre_id;
      });

    buscarCuenta.done(function(data){

      var datos_cuenta = $.parseJSON(data);
      var cuentas = moduloAseguradora.listarRamosTree();
      cuentas.success(function(){
        $("#treeRamos").jstree("destroy");
      });

      cuentas.done(function(data){

        var arbol = jQuery.parseJSON(data);
        $('#treeRamos').jstree(arbol);
        /**/
        $('#treeRamos').jstree(true).redraw(true);

        $("#treeRamos").bind("loaded.jstree",function(e,data){

         data.instance.select_node(datos_cuenta.id);
             //data.instance.open_node(datos_cuenta.id);

           });
        $("#treeRamos").on("select_node.jstree", function(e, data){
          if(!confir==0){
            var nodo = data.node;
            var nodo_id = nodo.id;
            $('#codigo').val(nodo_id);
          }confir++; 

              //console.log(data);
              //console.log(data.instance.get_node(data.node.id).text);

            });
        $("#treeRamos").on("changed.jstree", function(e, data){
          console.log("changed");

        });
        }); // fin del done

    });
  });
},
tablaGrid:function(){
 tablaRamos.grid_obj.jqGrid({
   url: tablaRamos.url,
   datatype: "json",
   colNames: ['','Ramo','Descripcion','código','Tipo de interes','Tipo de póliza','Estado','','','',''],
   colModel: [
   {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
   {name:'nombre', index:'nombre',sorttype:"text", sortable:true, width:150},
   {name:'descripcion',index:'descripcion', sortable:false},
   {name: 'codigo',index:'codigo',sortable:false},
   {name:'tipo_interes',index:'tipo_interes',sortable:false},
   {name:'tipo_poliza',index:'tipo_poliza',sortable:false},
   {name:'estado', index:'estado', formatter: 'text', sortable:false, align:'center'},
   {name:'opciones', index:'opciones', sortable:false, align:'center'},
   {name:'link', index:'link', hidedlg:true, hidden: true},
   {name:'modalstate', index:'modalstate', hidedlg:true, hidden: true},
   {name:'estadoReal', index:'estadoReal', hidedlg:true, hidden: true}
   ],
   mtype: "POST",
   postData: { erptkn:tkn},
   gridview: true,
   ExpandColClick: true,
   treeGrid: true,
   sortorder: "asc",
   hiddengrid: false,
   hoverrows: false,
   treeGridModel: 'adjacency',
   treedatatype:"json",
   ExpandColumn: 'nombre',
   treeIcons: {leaf:'fa fa-calculator',plus:'fa fa-caret-right',minus:'fa fa-caret-down'},
   height: 'auto',
   page: 1,
   pager : tablaRamos.grid_id+"Pager",
   rowNum:10,
   autowidth: true,
   sortname: 'nombre',
   viewrecords: false,
   beforeProcessing: function(data, status, xhr){
       //Check Session
       if( $.isEmptyObject(data.session) === false){
         window.location = phost() + "login?expired";
       }},
       beforeRequest: function(data, status, xhr){},
       loadComplete: function(data, status, xhr){
        tablaRamos.grid_obj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
        tablaRamos.grid_obj.find('div.tree-wrap').children().removeClass('ui-icon');
      //floating headers
      $('#gridHeader').sticky({
        getWidthFrom: '.ui-jqgrid-view',
        className:'jqgridHeader'
      });
    },
    onSelectRow: function(id){
      $(this).find('tr#'+ id).removeClass('ui-state-highlight');
    }
  });
}
};
$(document).ready(function(){
  ramos.init();

  $("#isGrouper").change(function() {

    var form =$('#crearRamosForm').validate({


    });
    if(this.checked) {

     form.resetForm();

     $('#codigo_ramo').rules(
       "add",{ required: false, 

       });

     $('#descripcion').rules(
       "add",{ required: false, 

       });
     $('#has-error').hide();

     $('select').each(function() {
      $(this).rules('add', {
        required: false,

      });
    });

   }else{
     $('#codigo_ramo').rules(
       "add",{ required: true, 

       });

     $('#descripcion').rules(
       "add",{ required: true, 
        regex:'^[a-zA-Z0-9áéíóúñ ]+$',
      });
     $('#has-error').show();

     $('select').each(function() {
      $(this).rules('add', {
        required: true,


      });




    });

   }
 });

  $.validator.setDefaults({ ignore: ":hidden:not(.chosen-select)" });
  $('#crearRamosForm').validate({



    rules: {
      nombre: {
        required: true,
        regex:'^[a-zA-Z0-9áéíóúñ ]+$'
      },
      codigo_ramo: {
        required: true
      },
      tipo_interes_ramo:{
        required:true
      },
      tipo_poliza_ramo:{
        required:true
      },
      'roles[]':{
        required:true
      },
      'usuarios[]':{
        required:true
      },
      descripcion:{
        required:true,
        regex:'^[a-zA-Z0-9áéíóúñ ]+$'
      },

    },

    errorPlacement: function(error, element) {
      var placement = $(element).data('error');
      if (element.hasClass("chosen-select")) {
        element.parent().append(error);

      } else {
        error.insertAfter(element);
      }
    }
  });

});

function updateState(parametros){

  var ajaxEstado = moduloAseguradora.cambiarEstadoRamo(parametros);
  tablaRamos.modalCambiarEstado.find('.modal-title').empty().html('Cambiar Estado');
  var opciones = '<div class="loading-progress"></div>';
  $('#estadoRamoModal').find('.modal-body').empty().html(opciones);
  $('#estadoRamoModal').find('.modal-footer').empty();
  tablaRamos.modalCambiarEstado.modal('show');

  var progress = $(".loading-progress").progressTimer({
    timeLimit: 300,
    completeStyle: 'progress-bar-success',
    onFinish: function() {
      tablaRamos.grid_obj.trigger('reloadGrid');
      tablaRamos.modalCambiarEstado.modal('hide');


    }

  });



  ajaxEstado.fail(function(){
    progress.progressTimer('error', {
      errorText: 'error al cambiar el estado!',
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

    }
  });
}