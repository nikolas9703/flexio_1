function EntradaTransaccion(subgrid_id, row_id){
    this.subgrid_id = subgrid_id;
    this.row_id = row_id;
}

Object.defineProperties(
    EntradaTransaccion.prototype,{
        subgrid:{
            get:function subgrid(){
                var subgrid_table_id = this.subgrid_id+"_t";
                var pager_id = "p_"+subgrid_table_id;
                var url =  phost() + 'entrada_manual/ajax-listar-transacciones';
                $("#"+this.subgrid_id).html("<table id='"+subgrid_table_id+"' class='scroll'></table><div id='"+pager_id+"' class='scroll'></div>");

                 $("#"+subgrid_table_id).jqGrid({
                     url: url,
                 datatype: "json",
                 colNames: ['','Cuenta contable','Descripci&oacute;n','Centro contable','Débito','Crédito'],
                 colModel: [
                           {name:'id', index:'id', hidedlg:true,key: true, hidden: true},
                            {name:'cuenta', index:'cuenta',sorttype:"text", sortable:true, width:150},
                            {name:'descripcion',index:'descripcion', formatter: 'text', sortable:true},
                            {name:'centro_contable',index:'centro_contable', sortable:true},
                            {name:'debito', index:'debito',sortable:true},
                            {name:'credito', index:'credito',sortable:true},
                          ],
                             mtype: "POST",
                             postData: { erptkn:tkn,
                                 campo:{
                                     entrada_manual:this.row_id
                                 }
                             },
                             sortorder: "asc",
                             hiddengrid: false,
                             loadtext: '<p>Cargando...</p>',
                             hoverrows: false,
                             viewrecords: true,
                             refresh: true,
                             gridview: true,
                             height: 'auto',
                             page: 1,
                             pager : pager_id,
                             rowNum:10,
                             autowidth: true,
                             rowList:[10,30,50],
                             sortname: 'id',
                             beforeProcessing: function(data, status, xhr){

                                 if( $.isEmptyObject(data.session) === false){
                                   window.location = phost() + "login?expired";
                                 }
                             },
                            loadBeforeSend: function () {
                               $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                           },
                           loadComplete: function(data, status, xhr){

                           }
                 });
            }
        }
    }
);
