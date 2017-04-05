var tablaContratosAlquiler = (function(){

    var tablaUrl = phost() + 'contratos_alquiler/ajax-listar';
    var gridId = "tablaContratosAlquilerGrid";
    var gridObj = $("#" + gridId);
    var opcionesModal = $('#optionsModal');
    var formularioBuscar = $('#buscarContratosAlquilerForm');
    var documentosModal = $('#documentosModal');
    var crearFacturaForm = $("#crearFacturaForm");

    var botones = {
        opciones: ".viewOptions",
        buscar: "#searchBtn",
        limpiar: "#clearBtn",
        exportar: "#exportarContratosAlquiler",
        subirArchivo: ".subirArchivoBtn",
        crearFactura: ".facturar"
    };

    var tabla = function(){
        gridObj.jqGrid({
            url: tablaUrl,
            mtype: "POST",
            datatype: "json",
            colNames:['','No. contrato','Cliente','Centro de facturaci&oacute;n','Fecha de inicio','Fecha de vencimiento','Saldo por facturar','Total facturado', 'Centro contable', 'Creado por', 'Estado','', ''],
            colModel:[
            {name:'uuid', index:'uuid', width:30,  hidedlg:true, hidden: true},
            {name:'codigo', index:'codigo', width:55, sortable:true},
            {name:'cliente_id', index:'cliente_id', width:50, sortable:true},
            {name:'centro_facturacion_id', index:'centro_facturacion_id', width: 50,  sortable:false},
            {name:'fecha_inicio', index:'fecha_inicio', width:40,  sortable:false, },
            {name:'fecha_final', index:'fecha_final', width:40,  sortable:false, },
            {name:'saldo_facturado', index:'saldo_facturado', width:50,  sortable:false, },
            {name:'total_facturado', index:'total_facturado', width: 50,  sortable:false},
            {name:'centro_contable_id', index:'centro_contable_id', width: 50,  sortable:false},
            {name:'created_by', index:'created_by', width: 50,  sortable:false},
            {name:'estado_id', index:'estado_id', width:35,  sortable:false },
            {name:'options', index:'options',width: 40},
            {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false,hidden: true, hidedlg:true},
            ],
            postData: {
                erptkn: tkn
            },
            height: "auto",
            autowidth: true,
            rowList: [10,20,50,100],
            rowNum: 10,
            page: 1,
            pager: gridId+"Pager",
            loadtext: '<p>Cargando...',
            hoverrows: false,
            viewrecords: true,
            refresh: true,
            gridview: true,
            multiselect: true,
            sortname: 'codigo',
            sortorder: "DESC",
            subGrid : true,
            subGridUrl: 'subgrid.php?q=2', 
            subGridModel: [{ name : ['No','Item','Qty','Unit','Line Total'], width : [55,200,80,80,80] } ],
            caption: "Subgrid Example",
            beforeProcessing: function(data, status, xhr){
                if( $.isEmptyObject(data.session) === false){
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaProveedoresGrid_cb, #jqgh_tablaProveedoresGrid_link").css("text-align", "center");
            },
            loadComplete: function(data, status, xhr){

                if(gridObj.getGridParam('records') === 0 ){
                    $('#gbox_'+gridId).hide();
                    $('#'+gridId+'NoRecords').empty().append('No se encontraron Contratos de alquiler.').css({"color":"#868686","padding":"30px 0 0"}).show();
                }
                else{
                    $('#gbox_'+gridId).show();
                    $('#'+gridId+'NoRecords').empty();
                }

            //---------
            // Cargar plugin jquery Sticky Objects
            //----------
            //add class to headers
            gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");
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
    };

    var eventos = function(){
      $("#cliente_id").select2({
         theme: "bootstrap",
         width:"100%"
     });
      $("#categoria").select2({
         theme: "bootstrap",
         width:"100%"
     });

      $(".select2-search__field").css("height","27px");

      $('#fecha_desde, #fecha_hasta').daterangepicker({
         locale: {
            format: 'YYYY-MM-DD'
        },
        showDropdowns: true,
        defaultDate: '',
        singleDatePicker: true
    }).val("");

        //Bnoton de Opciones
        gridObj.on("click", botones.opciones, function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var id = $(this).attr("data-id");

            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;
            //Init Modal
            opcionesModal.find('.modal-title').empty().append('Opciones: '+ $(rowINFO.codigo).text() +'');
            opcionesModal.find('.modal-body').empty().append(options);
            opcionesModal.find('.modal-footer').empty();
            opcionesModal.modal('show');
        });
        $(botones.exportar).click(function(){

            //contiene un arreglo con los identificadores de los proveedores (uuid_proveedor)
            var registros_jqgrid = gridObj.jqGrid('getGridParam','selarrrow');

            if(registros_jqgrid.length)
            {
                var url = phost() + "contratos_alquiler/ajax-exportar";
                var vars = "";
                $.each(registros_jqgrid, function(i, val){
                    vars += '<input type="hidden" name="ids[]" value="'+ val +'">';
                });
                var form = $(
                    '<form action="' + url + '" method="post" style="display:none;">' +
                    vars +
                    '<input type="hidden" name="erptkn" value="' + tkn + '">' +
                    '<input type="submit">' +
                    '</form>'
                    );
                $('body').append(form);
                form.submit();
            }
        });

        //boton limpiaar
        $(botones.limpiar).click(function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();
            formularioBuscar.find('input[type="text"]').prop("value", "");
            formularioBuscar.find('select').prop("value", "");
            $("#cliente_id").select2("val", "");
            $("#categoria").select2("val", "");

            recargar();
        });
        //boton Buscar
        $(botones.buscar).click(function(e){
            e.preventDefault();
            e.returnValue=false;
            e.stopPropagation();

            var codigo = $('#codigo').val();
            var cliente_id = $('#cliente_id').val();
            var categoria = $('#categoria').val();
            var fecha_desde= $('#fecha_desde').val();
            var fecha_hasta= $('#fecha_hasta').val();
            var estado_id = $('#estado_id').val();
            var creado_por = $('#creado_por').val();
            var centro_contable_id = $('#centro_contable_id').val();

            if (codigo !== "" || cliente_id !== "" || fecha_desde !== "" || fecha_hasta !== "" || estado_id) {
                //Reload Grid
                gridObj.setGridParam({
                    url: tablaUrl,
                    datatype: "json",
                    postData: {
                        codigo: codigo,
                        cliente_id: cliente_id,
                        categoria: categoria,
                        fecha_desde: fecha_desde,
                        fecha_hasta: fecha_hasta,
                        estado_id: estado_id,
                        creado_por: creado_por,
                        centro_contable_id: centro_contable_id,
                        erptkn: tkn
                    }
                }).trigger('reloadGrid');
            }
        });

        $(opcionesModal).on("click", botones.crearFactura, function(e){
          console.log("holaass");
          e.preventDefault();
          e.returnValue=false;
          e.stopPropagation();

          //Cerrar modal de opciones
          opcionesModal.modal('hide');

          var contrato_alquiler_uuid = $(this).attr("data-id");

          //Limpiar formulario
          crearFacturaForm.find('input[name*="contrato_alquiler_"]').remove();
          crearFacturaForm.append('<input type="hidden" name="contrato_alquiler_uuid" value="'+ contrato_alquiler_uuid +'" />');
          //Enviar formulario
          crearFacturaForm.submit();
          $('body').trigger('click');
      });

        //Documentos Modal
        $(opcionesModal).on("click", botones.subirArchivo, function(e){
         e.preventDefault();
         e.returnValue=false;
         e.stopPropagation();

			//Cerrar modal de opciones
			opcionesModal.modal('hide');

			var contrato_id = $(this).attr("data-id");
			var codigo = $(this).attr("data-codigo");

			//Inicializar opciones del Modal
			documentosModal.modal({
				backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
				show: false
			});
			//documentosModal.modal.find('.modal-title').text('New message to');
			var scope = angular.element('[ng-controller="subirDocumentosController"]').scope();
          scope.safeApply(function(){
             scope.campos.contrato_id = contrato_id;
         });
          documentosModal.find('.modal-title').empty().append('Contrato Número: '+codigo);

          documentosModal.modal('show');
      });
    };
    var recargar = function(){
        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                codigo: '',
                cliente_id: '',
                categoria: '',
                fecha_desde: '',
                fecha_hasta: '',
                estado_id: '',
                creado_por: '',
                centro_contable_id: '',
                erptkn: tkn
            }
        }).trigger('reloadGrid');
    };
    var redimencionar_tabla = function(){
        $(window).resizeEnd(function() {
            $(".ui-jqgrid").each(function(){
                var w = parseInt( $(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_","");
                $("#"+gId).setGridWidth(w);
            });
        });
    };

    return{
        init:function(){
            tabla();
            eventos();
            redimencionar_tabla();
        }
    };

})();

$(function(){
    tablaContratosAlquiler.init();
});
