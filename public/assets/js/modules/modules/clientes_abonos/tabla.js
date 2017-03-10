$(function() {

    if(typeof id_cliente === 'undefined'){
        id_cliente="";
    }

    //verificar si la url actual es contactos
    //de lo contrario no mostrar multiselect del jqgrid
    var multiselect = window.location.pathname.match(/contactos/g) ? true : false;

    //Init Contactos Grid
    $("#clientes_abonosGrid").jqGrid({
        url: phost() + 'clientes_abonos/ajax-listar',
        datatype: "json",
        colNames:[
            'No. Abono',
            'Fecha de Abono',
            'Monto',
            '',
            ''
        ],
        colModel:[
            {name:'abono', index:'abono', width:70 },
            {name:'fecha', index:'fecha', width:70 },
            {name:'monto_abonado', index:'monto_abonado', width:70, align:"center" },
            {name:'link', index:'link', width:50, align:"center", sortable:false, resizable:false},
            {name:'options', index:'options', hidden: true}
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            id_cliente:id_cliente,
            campo: typeof window.campo !== 'undefined' ? window.campo : {}
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        loadtext: '<p>Cargando Abonos...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        //multiselect: multiselect,
        sortname: 'nombre',
        sortorder: "ASC",
        beforeProcessing: function(data, status, xhr){
            //Check Session
            if( $.isEmptyObject(data.session) === false){
                window.location = phost() + "login?expired";
            }
        },
        loadBeforeSend: function () {
	    	$(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
	        $(this).closest("div.ui-jqgrid-view").find("#jqgh_contactosGrid_cb, #jqgh_contactosGrid_link").css("text-align", "center");
	    },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_clientes_abonosGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#clientes_abonosGrid").getGridParam('records') === 0 ){
              $('#gbox_clientes_abonosGrid').hide();
              $('#clientes_abonosGridNoRecords').empty().append('No se encontraron Abonos.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
              $('#gbox_contactosGrid').show();
              $('#contactosGridNoRecords').empty();
            }

            if(multiselect == true){
	            //add class to headers
	            $("#contactosGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

	            //floating headers
	            $('#gridHeader').sticky({
	                getWidthFrom: '.ui-jqgrid-view',
	                className:'jqgridHeader'
	            });
	            //Arreglar tamaño de TD de los checkboxes
                $("#contactosGrid_cb").css("width","50px");
                $("#contactosGrid tbody tr").children().first("td").css("width","50px");
            }
        },
        onSelectRow: function(id){
            $(this).find('tr#'+ id).removeClass('ui-state-highlight');
        },
    });

    //Boton de opciones
    $("#clientes_abonosGrid").on("click", ".viewOptions", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var id = $(this).attr("data-id");

        var rowINFO = $("#clientes_abonosGrid").getRowData(id);
        var codigo = $(this).attr("data-codigo");
        var options = rowINFO["options"];
         //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ codigo);
        $('#optionsModal').find('.modal-body').empty().append(options);
        $('#optionsModal').find('.modal-footer').empty();
        $('#optionsModal').modal('show');
    });



    //Resize grid, on window resize end
    $(window).resizeEnd(function() {
        $(".ui-jqgrid").each(function(){
            var w = parseInt( $(this).parent().width()) - 6;
            var tmpId = $(this).attr("id");
            var gId = tmpId.replace("gbox_","");
            $("#"+gId).setGridWidth(w);
        });
    });

    $("#iconGrid").on("click", ".viewOptionsGrid", function(e){

        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ $(this).closest(".chat-element").find("input[type='checkbox']").data("nombre"));
    });

});
