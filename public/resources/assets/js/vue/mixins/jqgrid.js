export default{
    methods:{
        initJqgrid:function(obj){
            var context = this;
            var gridObj = $("#"+ context.table_id);
            var params = $.extend(context.jqgrid, {
                mtype: "POST",
                datatype: "json",
                height: "auto",
                autowidth: true,
                rowList: [10, 20,50, 100],
                rowNum: 10,
                page: 1,
                pager: context.table_id + "pager",
                loadtext: '<p>Cargando...</p>',
                hoverrows: false,
                viewrecords: true,
                refresh: true,
                gridview: true,
                multiselect: true,
                beforeProcessing: function(data, status, xhr){
                    if ($.isEmptyObject(data.session) === false){
                        window.location = phost() + "login?expired";
                    }
                },
                loadBeforeSend: function () {//propiedadesGrid_cb
                    $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                    $(this).closest("div.ui-jqgrid-view").find("#"+ context.table_id +"_cb, #jqgh_"+ context.table_id +"_link").css("text-align", "center");
                },
                loadComplete: function (data, status, xhr) {
                    if (gridObj.getGridParam('records') === 0) {
                        $('#gbox_'+ context.table_id).hide();
                        $('#'+ context.table_id +'no-result').empty().append('No se encontraron registros.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                    } else {
                        $('#gbox_'+ context.table_id).show();
                        $('#'+ context.table_id +'no-result').empty();
                    }

                    gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");
                    $('#gridHeader').sticky({getWidthFrom: '.ui-jqgrid-view',className: 'jqgridHeader'});
                    //$('#jqgh_' + gridId + "_cb").css("text-align", "center");
                },
                onSelectRow: function (id) {
                    $(this).find('tr#' + id).removeClass('ui-state-highlight');
                }
            });

            //run
            gridObj.jqGrid(params);

            //init events
            context.initJqueryEvents(obj);
        },
        initJqueryEvents:function(obj){
            var context = this;
            var gridObj = $("#"+ context.table_id);

            //modal options
            gridObj.on("click", ".viewOptions", function (e) {
    			e.preventDefault();
    			e.returnValue = false;
    			e.stopPropagation();

    			var id = $(this).attr("data-id");
    			var rowINFO = gridObj.getRowData(id);
                var params = {
                    title:'Opciones: ' + rowINFO[obj.title],
                    body:rowINFO.link,
                    footer:''
                };

    			//Init Modal
                context.$root.$broadcast('ePopulateModal', params);
    			context.$root.$broadcast('eShowModal');
    		});

            //to csv
            $('body').on("click", "#toCSV", function (e) {
                var registros_jqgrid = gridObj.jqGrid('getGridParam','selarrrow');
                if(registros_jqgrid.length)
                {
                    var vars = "";
                    $.each(registros_jqgrid, function(i, val){
                        vars += '<input type="hidden" name="ids[]" value="'+ val +'">';
                    });
                    var form = $(
                        '<form action="' + obj.export_url + '" method="post" style="display:none;">' +
                        vars +
                        '<input type="hidden" name="erptkn" value="' + window.tkn + '">' +
                        '<input type="submit">' +
                        '</form>'
                    );
                    $('body').append(form);
                    form.submit();
                }
    		});

            //update status
            $('body').on("click", "#cambiar-estado-btn, .cambiar-estado-btn", function (e) {
                var id = $(this).data('id');
                var aux = gridObj.jqGrid('getGridParam','selarrrow');
                var params = $.extend({erptkn:window.tkn}, {id: !id ? aux : id});

                context.$root.$broadcast('ePopulateModal', {title:'Cambiar estado', body:'Por favor, espere un momento...'});

                if(!aux.length && !id){
                    toastr.error('No se ha seleccionado ning&uacute;n elemento');
                    context.$root.$broadcast('eHideModal');
                    return;
                }

                context.$root.$broadcast('eShowModal');
                $.ajax({
                    url: obj.states_segment_url,
                    type: "POST",
                    data: params,
                    dataType: "json",
                    success: function (response) {
                        if (!_.isEmpty(response)) {
                            context.$root.$broadcast('ePopulateModal', {body:response.data});
                        }
                    }
                });
    		});

            $('body').on('click', '.state-btn', function(){
                var btn = $(this);
                var id = btn.data('id');
                var aux = gridObj.jqGrid('getGridParam','selarrrow');
                var params = $.extend({erptkn:window.tkn}, {id: !id ? aux : id, estado: btn.data('estado')});
                context.$root.$broadcast('eHideModal');
        		$.ajax({
                    url: obj.states_update_url,
        			type: "POST",
        			data: params,
        			dataType: "json",
        			success: function (response) {
        				if (!_.isEmpty(response)) {
        					toastr[response.response ? 'success' : 'error'](response.mensaje);
                            context.$root.$broadcast('eReloadGrid');
        				}
        			}
        		});
            });
        }
    }
};
