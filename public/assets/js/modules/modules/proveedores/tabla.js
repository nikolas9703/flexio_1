$(function(){
    
    var formato_moneda = {
        decimalSeparator:",",
        thousandsSeparator: ",",
        decimalPlaces: 2,
        prefix: "$ "
    };
    
    var crearEstadoProveedor = $("#crearEstadoProveedor");
    var confirmacion = '<a href="#" class="btn btn-block btn-outline btn-success modal_aceptar" data-url="" data-uuid>Aceptar</a>';
    confirmacion += '<a href="#" class="btn btn-block btn-outline btn-success modal_cancelar">Cancelar</a>';
	
    //Init Pedidos Grid
    $("#proveedoresGrid").jqGrid({
        url: phost() + 'proveedores/ajax-listar',
        datatype: "json",
        colNames:[
            'Nombre',
            'Tel&eacute;fono',
            'E-mail',
            'Categor&iacute;a(s)',
           // 'Tipo',
            'O/C abiertas',
            'Saldo por pagar',
            'Estado',
            '',
            ''
        ],
        colModel:[
            {name:'Nombre', index:'pro_proveedores.nombre', width:80},
            {name:'Telefono', index:'telefono', width:70,  sortable:false},
            {name:'E-mail', index:'email', width:70,  sortable:false},
            {name:'Categoria(s)', index:'categoria', width:70,  sortable:false},
           // {name:'Tipo', index:'tipo', width:70,  sortable:false},
            {name:'O/C abiertas', index:'oc_abiertas', width: 50, sortable:false, align:'center'},
            {name:'Total a pagar', index:'total_pagar', width: 50,sortable:false, align:'right'},
            {name: 'Estado', index: 'estado', width: 45, sortable: false},
            {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false, hidedlg:true},
            {name:'options', index:'options', hidedlg:true, hidden: true},
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#pager",
        loadtext: '<p>Cargando...',
        pgtext : "Página {0} de {1}",
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: true,
        sortname: 'pro_proveedores.nombre',
        sortorder: "DESC",
        beforeProcessing: function(data, status, xhr){
            //Check Session
            if( $.isEmptyObject(data.session) == false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {//propiedadesGrid_cb
            $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
            $(this).closest("div.ui-jqgrid-view").find("#proveedoresGrid_cb, #jqgh_proveedoresGrid_link").css("text-align", "center");
        }, 
        beforeRequest: function(data, status, xhr){},
        loadComplete: function(data){
			
            //check if isset data
            if( data['total'] == 0 ){
                $('#gbox_proveedoresGrid').hide();
                $('.NoRecordsProveedores').empty().append('No se encontraron proveedores.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('.NoRecordsProveedores').hide();
                $('#gbox_proveedoresGrid').show();
            }

            //---------
            // Cargar plugin jquery Sticky Objects
            //----------
            //add class to headers
            $("#proveedoresGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view', 
                    className:'jqgridHeader'
                });

                //Arreglar tamaño de TD de los checkboxes
                $("#proveedoresGrid_cb").css("width","50px");
                $("#proveedoresGrid tbody tr").children().first("td").css("width","50px");
                
                
            },
            onSelectRow: function(id){
                $(this).find('tr#'+ id).removeClass('ui-state-highlight');
            },
	});
	$("#proveedoresGrid").jqGrid('columnToggle');
	
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
	$("#proveedoresGrid").on("click", ".viewOptions", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            
            var nombre = '';
            var id_proveedor = $(this).attr("data-proveedor");
            var rowINFO = $("#proveedoresGrid").getRowData(id_proveedor);
	    var options = rowINFO["options"];
            
            
            nombre = rowINFO["Nombre"];
	    //Init boton de opciones
            $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ nombre);
            $('#optionsModal').find('.modal-body').empty().append(options);
            $('#optionsModal').find('.modal-footer').empty();
            $('#optionsModal').modal('show');
            
	});
        
        //Documentos Modal
    $("#optionsModal").on("click", ".subirArchivoBtn", function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            //Cerrar modal de opciones
            $("#optionsModal").modal('hide');
            var proveedores_id = $(this).attr("data-id");

            //Inicializar opciones del Modal
            $('#documentosModal').modal({
                    backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                    show: false
            });

            //$('#pedido_id').val(pedido_id);
            var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();

        scope.safeApply(function(){
            scope.campos.proveedores_id = proveedores_id;
        });
            $('#documentosModal').modal('show');
    });
        
	$('#optionsModal').on('click', '.estadoProveedor', function() { 
            var proveedor_id = $(this).attr("data-id");
            crearEstadoProveedor.find('input[name*="proveedor"]').remove();
            crearEstadoProveedor.append('<input type="hidden" name="proveedor_id" value="'+ proveedor_id +'" />');
            //Enviar formulario
            crearEstadoProveedor.submit();
            
	});
        
        
	
	
	//-------------------------
	// Botones de formulario de Busqueda
	//-------------------------
	$('#searchBtn').bind('click', searchBtnHlr);
 	
	$('#clearBtn').click(function(e){
            e.preventDefault();

            $("#proveedoresGrid").setGridParam({
                url: phost() + 'proveedores/ajax-listar',
                datatype: "json",
                postData: {
                    nombre: '',
                    telefono: '',
                    email: '',
                    categoria: '',
                    tipo: '',
                    erptkn: tkn,
                    estados: ''
                }
            }).trigger('reloadGrid');

            //Reset Fields
            $('#nombre, #telefono, #email, #categoria, #tipo, #estados').val('');
            
            //Reset Chosens
            $("#categoria, #tipo").trigger("chosen:updated");
	});

    
 
});
 

function searchBtnHlr(e) {

	e.preventDefault();
	$('#searchBtn').unbind('click', searchBtnHlr);

	var nombre = $('#nombre').val();
	var telefono = $('#telefono').val();
	var email = $('#email').val();
        var categoria = $('#categoria').val();
	var tipo = $('#tipo').val();
        var estados = $('#estados').val();
        
	if(nombre != "" || telefono != "" || email != ""  || categoria != "" || tipo != "" || estados !="")  
	{
            $("#proveedoresGrid").setGridParam({
                url: phost() + 'proveedores/ajax-listar',
                datatype: "json",
                postData: {
                    nombre: nombre,
                    telefono: telefono,
                    email: email,
                    categoria: categoria,
                    tipo: tipo,
                    erptkn: tkn,
                    estados: estados
                }
            }).trigger('reloadGrid');
		
            $('#searchBtn').bind('click', searchBtnHlr);
	}else{
            $('#searchBtn').bind('click', searchBtnHlr);
	}
}
