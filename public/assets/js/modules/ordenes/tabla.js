// Definimos la variable tabs la cual contendrá todo nuestro modulo.
var tablaOrdenes = (function(){

    // Objeto al cual establecemos valores que vamos a usar mas adelante en este ambito.
    var st = {
        jqGrid: "#ordenesGrid",
        optionsModal: "#optionsModal, #opcionesModal",
        searchBtn: "#searchBtn",
        clearBtn: "#clearBtn",
        noRecords: ".NoRecordsOrdenes",
        //campos del formulario de busqueda
        fecha_desde: "#fecha_desde",
        fecha_hasta: "#fecha_hasta",
        centro: "#centro",
        estado: "#estado",
        //referencia: "#referencia",
        numero: "#numero",
        proveedor: "#proveedor",
        montos_de: "#montos_de",
        montos_a: "#montos_a",
        creado_por: "#creado_por",
        categoria_id: "#categoria_id",
        inputsSearch: "#fecha_desde, #fecha_hasta, #centro, #estado, #numero, #proveedor, #montos_de, #montos_a, #creado_por,#categoria_id"
    };

    // Objeto vacío que guardará elementos que se manejan por HTML.
    var dom = {}

    // Función que llenará al objeto dom con los objetos HTML a través de jQuery ($).
    var catchDom = function(){
        dom.jqGrid = $(st.jqGrid);
        dom.optionsModal = $(st.optionsModal);
        dom.searchBtn = $(st.searchBtn);
        dom.clearBtn = $(st.clearBtn);
        dom.noRecords = $(st.noRecords);
        dom.fecha_desde = $(st.fecha_desde);
        dom.fecha_hasta = $(st.fecha_hasta);
        dom.centro = $(st.centro);
        dom.estado = $(st.estado);
        //dom.referencia = $(st.referencia);
        dom.numero = $(st.numero);
        dom.proveedor = $(st.proveedor);
        dom.montos_de = $(st.montos_de);
        dom.montos_a = $(st.montos_a);
        dom.creado_por = $(st.creado_por);
        dom.categoria_id = $(st.categoria_id);
        dom.inputsSearch = $(st.inputsSearch);
    };

    // Función donde establecemos los eventos que tendrán cada elemento.
    var suscribeEvents = function(){
        dom.jqGrid.on("click", ".viewOptions", events.eMostrarModal);
        dom.searchBtn.bind('click', events.eSearchBtnHlr);
        dom.clearBtn.on('click', events.eClearBtn);
    };

    /* Objeto que guarda métodos que se van a usar en cada evento definido
      en la función suscribeEvents. */
    var events = {
        eMostrarModal: function(e)
        {
            self = $(this);

            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var nombre = '';
            var id_orden = self.attr("data-orden");
            var rowINFO = dom.jqGrid.getRowData(id_orden);
	    var options = rowINFO["options"];


            nombre = rowINFO["Numero"];
	    //Init boton de opciones
            dom.optionsModal.find('.modal-title').empty().append('Opciones: '+ nombre);
            dom.optionsModal.find('.modal-body').empty().append(options);
            dom.optionsModal.find('.modal-footer').empty();
            dom.optionsModal.modal('show');
        },
        eSearchBtnHlr: function (e) {

            e.preventDefault();
            //actualizar chosens...
            //console.log(listarOrdenes.dom.chosens);
            dom.searchBtn.unbind('click', events.eSearchBtnHlr);
console.log(dom);
            var fecha_desde = dom.fecha_desde.val();
            var fecha_hasta = dom.fecha_hasta.val();
            var centro = dom.centro.val();
            var estado = dom.estado.val();
            //var referencia = dom.referencia.val();
            var numero = dom.numero.val();
            var proveedor = dom.proveedor.val();
            var montos_de = dom.montos_de.val();
            var montos_a = dom.montos_a.val();
            var creado_por = dom.creado_por.val();
            var categoria_id = dom.categoria_id.val();

            if(fecha_desde != "" || fecha_hasta != "" || centro != "" || estado != ""  || creado_por != "" || numero != "" || proveedor != "" || montos_de != "" || montos_a != "" || categoria_id != "")
            {
                dom.jqGrid.setGridParam({
                    url: phost() + 'ordenes/ajax-listar',
                    datatype: "json",
                    postData: {
                        fecha_desde: fecha_desde,
                        fecha_hasta: fecha_hasta,
                        centro: centro,
                        estado: estado,
                        //referencia: referencia,
                        numero: numero,
                        proveedor: proveedor,
                        montos_de: montos_de,
                        montos_a: montos_a,
                        creado_por: creado_por,
                        categoria_id: categoria_id,
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');

                dom.searchBtn.bind('click', events.eSearchBtnHlr);
            }else{
                dom.searchBtn.bind('click', events.eSearchBtnHlr);
            }
        },
        eClearBtn: function(e){
            e.preventDefault();
            dom.jqGrid.setGridParam({
                url: phost() + 'ordenes/ajax-listar',
                datatype: "json",
                postData: {
                    fecha_desde: '',
                    fecha_hasta: '',
                    centro: '',
                    estado: '',
                    creado_por: '',
                    categoria_id: '',
                    numero: '',
                    proveedor: '',
                    montos_de: '',
                    montos_a: '',
                    erptkn: tkn
                }
            }).trigger('reloadGrid');

            //Reset Fields
            dom.inputsSearch.val('');

            //Reset Chosens
              $('.ibox').find('select[id="categoria_id"]').find('option').removeAttr("selected");
              //dom.inputsSearch.categoria_id.find('option').removeAttr("selected");

            dom.inputsSearch.trigger("chosen:updated");
	}
    };

    var muestra_tabla = function(){

        dom.jqGrid.jqGrid({
            url: phost() + 'ordenes/ajax-listar',
            datatype: "json",
            colNames:[
                'No. O/C',
                'Fecha',
                'Proveedor',
                'Monto',
                'Centro contable',
                'Creado por',
                'Estado',
                '',
                ''
            ],
            colModel:[
                {name:'Numero', index:'numero', width:60,  sortable:true},
                {name:'Fecha', index:'fecha_creacion', width:60},
                {name:'Proveedor', index:'proveedor', width:80,  sortable:false},
                {name:'Monto', index:'monto', width: 50,sortable:false, align:'right'},
                {name:'Centro Contable', index:'cen_centros.nombre', width: 80, sortable:false, align:'left'},
                {name:'Creado por', index:'creado_por', width:70,  sortable:false},
                {name:'Estado', index:'etiqueta', width: 50,sortable:false, align:'center'},
                {name:'link', index:'link', width:60, align:"center", sortable:false, resizable:false, hidedlg:true},
                {name:'options', index:'options', hidedlg:true, hidden: true},
            ],
            mtype: "POST",
            postData: {
                erptkn: tkn,
                proveedor: uuid_proveedor,
                pedido_id: (typeof window.sp_pedido_id !== 'undefined') ? window.sp_pedido_id : '',//from subpanels ver pedido
                factura_compra_id: (typeof factura_compra_id !== 'undefined') ? factura_compra_id : ''
            },
            height: "auto",
            autowidth: true,
            rowList: [10,20,50,100],
            rowNum: 10,
            page: 1,
            pager: "#pagerOrdenesCompras",
            loadtext: '<p>Cargando...',
            pgtext : "Página {0} de {1}",
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: multiselect,
            sortname: 'numero',
            sortorder: "DESC",
            beforeProcessing: function(data, status, xhr){
                //Check Session
                if( $.isEmptyObject(data.session) == false){
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find(st.jqGrid + "_cb, #jqgh_" + st.jqGrid + "_link").css("text-align", "center");
            },
            beforeRequest: function(data, status, xhr){},
            loadComplete: function(data){

                //check if isset data
                if( data['total'] == 0 ){
                    $('#gbox_' + st.jqGrid).hide();
                    dom.noRecords.empty().append('No se encontraron ordenes de compra.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    dom.noRecords.hide();
                    $('#gbox_' + st.jqGrid).show();
                }

                if(multiselect == true){
                    //---------
                    // Cargar plugin jquery Sticky Objects
                    //----------
                    //add class to headers
                    dom.jqGrid.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                    //floating headers
                    $('#gridHeader').sticky({
                        getWidthFrom: '.ui-jqgrid-view',
                        className:'jqgridHeader'
                    });

                    //Arreglar tamaño de TD de los checkboxes
                    $(st.jqGrid + "_cb").css("width","50px");
                    $(st.jqGrid + " tbody tr").children().first("td").css("width","50px");
                }




            },
            onSelectRow: function(id){
                $(this).find('tr#'+ id).removeClass('ui-state-highlight');
            },
        });

        dom.jqGrid.jqGrid('columnToggle');

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
    }

    $("#optionsModal").on("click", ".enviar_correo_proveedor", function(e){
      e.preventDefault();
      e.returnValue=false;
      e.stopPropagation();

      //Cerrar modal de opciones
      $("#optionsModal").modal('hide');
      var orden_id = $(this).attr("data-id");
      var correo = $(this).attr("data-correo");
      var nombre = $(this).attr("data-nombre");
      var codigo = $(this).attr("data-codigo");

      swal({
          title: "<i class='fa fa-envelope-o'></i> "+'Enviar: '+codigo,
          text: "Proveedor: "+nombre,
          html: true,
          type: "input",
          inputValue: correo,
          value: '',
          showCancelButton: true,
          confirmButtonText: "Enviar",
          cancelButtonText: "Cancelar",
          closeOnConfirm: false,
          animation: "slide-from-top",
          confirmButtonColor: "#0070b9",
          cancelButtonColor:"#999898",
          inputPlaceholder: "Correo Electrónico",
          showLoaderOnConfirm: true,
          //closeOnConfirm: false
     }, function(inputValue){


       if (inputValue === false)
         return false;

       if (inputValue === "") {
         swal.showInputError("Introduzca el correo, por favor!");
         return false
       }

       		$.ajax({
       			url: phost() + 'ordenes/ajax-enviar-correo',
       			data: {
       				erptkn: tkn,
       				correo: inputValue,
              orden_id: orden_id
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
       				if(json.response == "" || json.response == undefined || json.response == 0){
       					toastr.error(json.mensaje);
       				}else{
                setTimeout(function(){     swal("Éxito: Operacion Finalizada!");   }, 2000);
       					//toastr.success(json.mensaje);
       				}


       		});



        }).trigger('reloadGrid');


    });
    //Documentos Modal
		$("#optionsModal").on("click", ".subirDocumento", function(e){
			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			//Cerrar modal de opciones
			$("#optionsModal").modal('hide');
			var orden_id = $(this).attr("data-id");

			//Inicializar opciones del Modal
			$('#documentosModal').modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});

                        //$('#pedido_id').val(pedido_id);
                        var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
		    scope.safeApply(function(){
		    	scope.campos.orden_id = orden_id;
		    });



			$('#documentosModal').modal('show');
		});

    //Boton de Exportar Descuento
		$('#exportarBtn').on("click", function(e){

			e.preventDefault();
			e.returnValue=false;
			e.stopPropagation();

			if($('#tabla').is(':visible') == true){

				//Exportar Seleccionados del jQgrid
				var ids = [];

				ids = dom.jqGrid.jqGrid('getGridParam','selarrrow');

				//Verificar si hay seleccionados
				if(ids.length > 0){

					$('#ids').val(ids);

			        $('form#exportarOrdenes').submit();
			        $('body').trigger('click');
				}
	        }
		});

    // Función que inicializará los funciones decritas anteriormente.
    var initialize = function(){
        catchDom();
        suscribeEvents();
        muestra_tabla();
    };

    /* Retorna un objeto literal con el método init haciendo referencia a la
       función initialize. */
    return{
        init:initialize
    }
})();

//verificar si la url actual es contactos
//de lo contrario no mostrar multiselect del jqgrid
var multiselect = window.location.pathname.match(/ordenes/g) ? true : false;
if(typeof uuid_proveedor === 'undefined'){
    uuid_proveedor="";
};
if(typeof uuid_pedido === 'undefined'){
    uuid_pedido="";
};

// Ejecutando el método "init" del módulo tabs.
tablaOrdenes.init();
