<template>

    <div>

        <!-- Se muestra cuando no hay registros -->
        <div :id="dom.no_records_id" class="text-center lead"></div>

        <!-- tabla con registros -->
        <table class="table table-striped" :id="dom.grid_id"></table>

        <!-- paginacion de la tabla con registros  -->
        <div :id="dom.pager_id"></div>

    </div>

    <div class="modal fade" :id="dom.modal_id" tabindex="-1" role="dialog" :aria-labelledby="dom.modal_id" aria-hidden="true">
     <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body"></div>
                <div class="modal-footer"></div>
            </div>
        </div>
    </div>

</template>

<script>

export default {

  props:{

        config: Object,
        detalle: Object,
        catalogos: Object

    },

    data:function(){

        return {

            dom: {

                no_records_id: 'usuariosGridNoRecords',
                grid_id: 'usuariosGrid',
                pager_id: 'usuariosGridPager',
                modal_id: 'opcionesModal',
                form_id: 'crearUsuarioForm'

            }

        };

    },

    methods: {

        getId: function(id){

            return '#'+ id;

        },

        populateForm: function(fila, id){
            var context = this;
            context.detalle.id = id;
            context.detalle.nombre = fila['nombre'];
            context.detalle.apellido = fila['apellido'];
            context.detalle.email = fila['Correo'];
            context.detalle.rol = fila['rol_sistema_id'];
            context.detalle.roles = fila['rol_id'];
            context.detalle.centros_contables = fila['centros_contables'].split(",");
			      context.detalle.categorias = fila['categorias'].split(",");
            context.detalle.tipos_subcontrato		= fila["tipos_subcontrato_id"].split(",");

        },

        editarUsuario: function(obj){

            var context = this;

    		var id = $(obj).attr("data-id");
    		var rowINFO = $(context.getId(context.dom.grid_id)).getRowData(id);

            //solo se requiere popular el formulario
            context.populateForm(rowINFO, id);

    	    //Abrir panel de formulario
    	    $(context.getId(context.dom.form_id)).find('.ibox-content').removeAttr("style");
    		$(context.getId(context.dom.modal_id)).modal('hide');

        }

    },

    ready: function(){

        var context = this;
        //Init Usuarios Grid
    	$("#usuariosGrid").jqGrid({
    	   	url: phost() + 'usuarios/ajax-listar-usuarios',
    	   	datatype: "json",
    	   	colNames:[
    			'Nombre',
    			'Correo Electr&oacute;nico',
    			'Fecha Creaci&oacute;n',
                'Centro(s) contable(s)',
				'Categoria(s)',
    			'Estado',
    			'Acci&oacute;n',
    			'',
    			'',
    			'',
    			'',
    			'',
                '',//centros contables,
				'',//categorias
				''//tipos subcontrato
    		],
    	   	colModel:[
    			{name:'Nombre Completo', index:'nombre_completo', width:70},
    			{name:'Correo', index:'correo', width:70},
    			{name:'Fecha de Creacion', index:'fecha_creacion', formatter: 'date', formatoptions: { newformat: 'd-m-Y' }, width:70, align:"center"},
                {name:'centros_contables_label', index:'centros_contables_label', width:70},
				{name:'filtro_categoria_label', index:'filtro_categoria_label', width:50},
    			{name:'Estado', index:'estado', width: 50, align:"center" },
    			{name:'link', index:'link', width:50, sortable:false, resizable:false, hidedlg:true, align:"center"},
    			{name:'options', index:'options', hidedlg:true, hidden: true},
    			{name:'nombre', index:'nombre', hidedlg:true, hidden: true},
    			{name:'apellido', index:'apellido', hidedlg:true, hidden: true},
    			{name:'rol_sistema_id', index:'rol_sistema_id', hidedlg:true, hidden: true},
    			{name:'rol_id', index:'rol_id', hidedlg:true, hidden: true},
                {name:'centros_contables', index:'centros_contables', hidedlg:true, hidden: true},
				 {name:'categorias', index:'categorias', hidedlg:true, hidden: true},
         {name:'tipos_subcontrato_id', index:'tipos_subcontrato_id', hidedlg:true, hidden: true},
    	   	],
    		mtype: "POST",
    	   	postData: {
    	   		erptkn: tkn,
    	   		uuid_empresa: uuid_empresa
    	   	},
    		height: "auto",
    		autowidth: true,
    		rowList: [10, 20,50, 100],
    		rowNum: 10,
    		page: 1,
    		pager: "#usuariosGridPager",
    		loadtext: '<p>Cargando...',
    		hoverrows: false,
    	    viewrecords: true,
    	    refresh: true,
    	    gridview: true,
    	    sortname: 'nombre',
    	    sortorder: "ASC",
    	    beforeProcessing: function(data, status, xhr){
    	    	//Check Session
    			if( $.isEmptyObject(data.session) == false){
    				window.location = phost() + "login?expired";
    			}
    	    },
    	    loadBeforeSend: function () {

    	    },
    	    beforeRequest: function(data, status, xhr){},
    		loadComplete: function(data){

    			//check if isset data
    			if( data['total'] == 0 ){
    				$('#gbox_usuariosGrid').hide();
    				$('#usuariosGridNoRecords').empty().append('No se encontraron Usuarios.').css({"color":"#868686","padding":"30px 0 0"}).show();
    			}
    			else{
    				$('#usuariosGridNoRecords').hide();
    				$('#gbox_usuariosGrid').show();
    			}

    			//---------
    			// Cargar plugin jquery Sticky Objects
    			//----------
    			//add class to headers
    			$("#usuariosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

    			//floating headers
    			$('#gridHeader').sticky({
    		    	getWidthFrom: '.ui-jqgrid-view',
    		    	className:'jqgridHeader'
    		    });
    		},
    		onSelectRow: function(id){
    			$(this).find('tr#'+ id).removeClass('ui-state-highlight');
    		},
    	});

    	//-------------------------
    	// Redimensioanr Grid al cambiar tamaño de la ventanas.
    	//-------------------------
    	$(window).resizeEnd(function() {
    		$(".ui-jqgrid").each(function(){
    			var w = parseInt( $(this).parent().width()) - 6;
    			var tmpId = $(this).attr("id");
    			var gId = tmpId.replace("gbox_","");
    			$("#"+gId).setGridWidth(w);
    		});
    	});

    	//-------------------------
    	// Boton de opciones
    	//-------------------------
    	$("#usuariosGrid").on("click", ".viewOptions", function(e){
    		e.preventDefault();
    		e.returnValue=false;
    		e.stopPropagation();

    		var id = $(this).attr("data-id");
    		var rowINFO = $("#usuariosGrid").getRowData(id);
    	    var options = rowINFO["options"];

     	    //Init boton de opciones
    		$('#opcionesModal').find('.modal-title').empty().append('Opciones: '+ rowINFO["Nombre Completo"] +'');
    		$('#opcionesModal').find('.modal-body').empty().append(options);
    		$('#opcionesModal').find('.modal-footer').empty();
    		$('#opcionesModal').modal('show');
    	});

    	$('#opcionesModal').on("click", ".editarUsuarioBtn", function(e){
    		e.preventDefault();
    		e.returnValue=false;
    		e.stopPropagation();

            context.editarUsuario(this);
    	});

    	$('#opcionesModal').on("click", ".estadoUsuarioBtn", function(e){
    		e.preventDefault();
    		e.returnValue=false;
    		e.stopPropagation();

    		var usuario_id = $(this).attr("data-id");

    		$.ajax({
    			url: phost() + 'usuarios/ajax-toggle-estado',
    			data: {
    				erptkn: tkn,
    				usuario_id: usuario_id
    			},
    			type: "POST",
    			dataType: "json",
    			cache: false,
    		}).done(function(json, textStatus, xhr) {

    			//Check Session
    			if( $.isEmptyObject(json.session) == false){
    				window.location = phost() + "login?expired";
    			}

    			if(xhr.status != 200){
    				//mensaje error
    				toastr.error('Hubo un error al tratar de cambiar el estado.');
    			}

    			//mensaje success
    			toastr.success(json.mensaje);

    			//cerrar modal
    			$('#opcionesModal').modal('hide');

    			//Recargar tabla jqgrid
    			$("#usuariosGrid").setGridParam({
    				url: phost() + 'usuarios/ajax-listar-usuarios',
    				datatype: "json",
    				postData: {
    					uuid_empresa: uuid_empresa,
    					erptkn: tkn
    				}
    			}).trigger('reloadGrid');

    		}).fail(function(xhr, textStatus, errorThrown) {
    			//mensaje error
    			toastr.error('Hubo un error al tratar de cambiar el estado.');
    		});
    	});

    }

}

</script>
