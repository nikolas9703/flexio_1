/**
 * Created by angel on 01/03/16.
 */
 $(function() {

    if(typeof id_cliente === 'undefined'){
        id_cliente="";
    }

    var multiselect = window.location.pathname.match(/remesas/g) ? true : false;


    //Init Contactos Grid
    $("#remesasGrid").jqGrid({
        url: phost() + 'remesas/ajax-listar-remesas',
        datatype: "json",
        colNames:[
        '',
        'Nº de Remesa',
        'Recibos remesados',
        'Aseguradora',
        'Monto',
        'Fecha',
        'Usuario',
        'Estado',
        '',
        ''
        ],
        colModel:[
        {name:'id',index:'id', width: 0, align: "center", sortable: false, hidden: true},
        {name:'remesa', index:'remesa', width:10},
        {name:'recibos_remesados', index:'recibos_remesados', width:10 },
        {name:'aseguradora_id', index:'aseguradora_id', width:10 },
        {name:'monto', index:'monto', width: 10  },
        {name:'fecha', index:'fecha', width: 10  },
        {name:'usuario', index:'usuario', width: 10 },
        {name:'estado', index:'estado', width: 10 },
        {name:'link', index:'link', width:10, align:"center", sortable:false, resizable:false},
        {name:'options', index:'options', width: 10, align: "center", sortable: false, hidden: true}
        ],
        mtype: "POST",
        postData: {
            erptkn: tkn,
            id_cliente: id_cliente
        },
        height: "auto",
        autowidth: true,
        rowList: [10, 20,50,100],
        rowNum: 10,
        page: 1,
        pager: "#remesasGridPager",
        loadtext: '<p>Cargando Remesas...',
        hoverrows: false,
        viewrecords: true,
        refresh: true,
        gridview: true,
        multiselect: true,
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
            $(this).closest("div.ui-jqgrid-view").find("#jqgh_remesasGrid_cb, #jqgh_remesasGrid_link").css("text-align", "center");
        },
        beforeRequest: function(data, status, xhr){
            $('.jqgrid-overlay').show();
            $('#load_remesasGrid').addClass('ui-corner-all');
        },
        loadComplete: function(data){

            $('.jqgrid-overlay').hide();

            //check if isset data
            if($("#remesasGrid").getGridParam('records') === 0 ){
                $('#gbox_remesasGrid').hide();
                $('#remesasGridNoRecords').empty().append('No se encontraron Planes.').css({"color":"#868686","padding":"30px 0 0"}).show();
            }
            else{
                $('#gbox_remesasGrid').show();
                $('#remesasGridNoRecords').empty();
            }

            /*if(multiselect == true){
                //add class to headers
                $("#remesasGrid").closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id","gridHeader");

                //floating headers
                $('#gridHeader').sticky({
                    getWidthFrom: '.ui-jqgrid-view',
                    className:'jqgridHeader'
                });
                //Arreglar tamaño de TD de los checkboxes
                $("#remesasGrid_cb").css("width","50px");
                $("#remesasGrid tbody tr").children().first("td").css("width","50px");
            }*/
        },
        onSelectRow: function(id){
            $(this).find('tr#'+ id).removeClass('ui-state-highlight');
        },
    });

    //Boton de opciones
    $("#remesasGrid").on("click", ".viewOptions", function(e){
        e.preventDefault();
        e.returnValue=false;
        e.stopPropagation();
        var id_plan = $(this).attr("data-plan");

        var rowINFO = $("#remesasGrid").getRowData(id_plan);
        var nombre = $(this).attr("data-nombre");
        var options = rowINFO["options"];
        //Init boton de opciones
        $('#optionsModal').find('.modal-title').empty().append('Opciones: '+ nombre);
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
