var tablaReclamosPersonas = (function () {

        var unico = $("#detalleunico").val();
        var id_poliza = $("#poliza_seleccionado").val();
        if (id_poliza == "") { id_poliza = 0; }
        var tablaUrl = phost() + 'reclamos/ajax_listar_personas';
        var gridId = "tablaReclamosPersonas";
        var gridObj = $('#verModalIntereses').find("#tablaReclamosPersonas");
        var opcionesModal = $('#verModalIntereses');
        var formularioBuscar = '';
        var documentosModal = $('#documentosModal');
        var grid_obj = $("#tablaReclamosPersonas");



        var botones = {
            opciones: ".seleccionarpoliza",
            buscar: "#modal_filtrar",
            limpiar: "#modal_limpiar",
            modal: "#modalInteres"
        };

        var tabla = function () {
            gridObj.jqGrid({
                url: tablaUrl,
                mtype: "POST",
                datatype: "json",
                colNames: ['N° interés','N° Certificado', 'Nombre', 'Cédula', 'Fecha nacimiento','Edad','Nacionalidad','Sexo','Estatura','Peso','Telefono','Relación','Tipo relación','Participación','Fecha inclusión','Fecha exclusión', 'Prima','Estado',''],
                colModel: 
                [
                {name:'numero', index:'numero',width:45,sortable:true,sorttype:"text"},
                {name:'certificado', index:'detalle_int_asociado',width:45,sortable:true,sorttype:"text"},
                {name:'nombrePersona', index:'nombrePersona', width:60,sortable:true,sorttype:"text"},
                {name:'identificacion', index:'identificacion', width:45,sortable:true,sorttype:"text"},
                {name:'fecha_nacimiento', index:'fecha_nacimiento', width: 40,sortable:true,sorttype:"text"},
                {name:'edad', index:'edad', width:20,sortable:true,sorttype:"text"},
                {name:'nacionalidad', index:'fecha_nacimiento', width: 40,sortable:true,sorttype:"text",hidden:setting.nacionalidad},
                {name:'sexo', index:'sexo', width:20,sortable:true,sorttype:"text"},
                {name:'estatura', index:'estatura', width: 20,sortable:true,sorttype:"text",hidden:setting.estatura},
                {name:'peso', index:'peso', width:20,sortable:true,sorttype:"text",hidden:setting.peso},
                {name:'telefono', index:'telefono_residencial', width: 40,sortable:true,sorttype:"text"},
                {name:'relacion', index:'peso', width:45,sortable:true,sorttype:"text",hidden:setting.relacion},
                {name:'tipo_relacion', index:'int_intereses_asegurados_detalles.detalle_relacion', width:45,sortable:true,sorttype:"text",hidden:setting.tipo_relacion},
                {name:'participacion', index:'peso', width:20,sortable:true,sorttype:"text",hidden:setting.participacion},
                {name:'fecha_inclusion', index:'fecha_inclusion', width: 40,sortable:true,sorttype:"text"},
                {name:'fecha_exclusion', index:'fecha_exclusion', width: 40,sortable:true,sorttype:"text"},
                {name:'prima', index:'detalle_prima', width: 30,sortable:true,sorttype:"text"},
                {name:'estado', index:'estado', width: 40,sortable:true,sorttype:"text"},
                {name:'options', index:'options', width:65, sortable:false, resizable:false, hidedlg:true, align:"center"}
                ],
                postData: {
                    detalle_unico: unico,
                    desde: vista,
                    erptkn: tkn,
                    id_poliza: id_poliza,
                    relacion: tablaTipo == "salud" ? 'Principal' : ''
                },
                //  caption: "Stack Overflow Adjacency Example",
                height: "auto",
                treeGridModel: 'adjacency',
                treedatatype:"json",
                ExpandColClick: true,
                treeIcons: {leaf: 'ui-icon-blank'},
                autowidth: true,
                treeGrid: setting.treeview,
                rowList: [10, 20, 50, 100],
                rowNum: 10,
                page: 1,
                pager: "#" + gridId + "Pager",
                loadtext: '<p>Cargando...</p>',
                hoverrows: false,
                viewrecords: true,
                refresh: true,
                gridview: true,
                sortname: "id",
                sortorder: "ASC",

                beforeProcessing: function (data, status, xhr) {
                //Check Session
                if ($.isEmptyObject(data.session) == false) {
                    window.location = phost() + "login?expired";
                }
            },
            loadBeforeSend: function () {//propiedadesGrid_cb
                $(this).closest("div.ui-jqgrid-view").find("table.ui-jqgrid-htable>thead>tr>th").css("text-align", "left");
                $(this).closest("div.ui-jqgrid-view").find("#tablaReclamosPersonasGrid_cb, #jqgh_tablaReclamosPersonasGrid_link").css("text-align", "center");
            },
            beforeRequest: function (data, status, xhr) {

            },
            loadComplete: function (data, status, xhr) {

                if (gridObj.getGridParam('records') === 0) {
                    sendIdividualForm =false;
                    $("#relaciondetalle_persona option").each(function () {
                        ContVidaInd = 0;
                        if ($(this).val() == "Dependiente" || $(this).val() == "Beneficiario") {
                            $(this).prop("disabled", true);

                        }
                        else {
                            $(this).prop("disabled", false);

                        }
                    });
                    $('#gbox_' + gridId).hide();
                    $('#' + gridId + 'NoRecords').empty().append('No se han agregado intereses asegurados.').css({"color": "#868686", "padding": "30px 0 0"}).show();
                } else {
                    ContVidaInd = 1;
                    sendIdividualForm=true;
                    $('#gbox_' + gridId).show();
                    $('#' + gridId + 'NoRecords').empty();
                    $("#relaciondetalle_persona option").each(function () {

                        if ($(this).val() == "Principal" && id_tipo_poliza==1) {
                            $(this).prop("disabled", true);

                        }
                    });
                }

                //---------
                // Cargar plugin jquery Sticky Objects
                //----------
                //add class to headers
                //gridObj.jqGrid('setGridWidth', $('.tabladetalle_personas').width());
                /*gridObj.closest("div.ui-jqgrid-view").find("div.ui-jqgrid-hdiv").attr("id", "gridHeader");*/
                //floating headers
                
                //Arreglar tamaño de TD de los checkboxes
                //FALTA ADAPTAR EL CODIGO PARA QUE LOS CHECKBOX SE VEAN BIEN
                $('#jqgh_' + gridId + "_cb").css("text-align", "center");
                $('.s-ico').removeAttr('style');
            },
            onSelectRow: function (id) {
                $(this).find('tr#' + id).removeClass('ui-state-highlight');
            }
        });
        //Al redimensionar ventana
        $(window).resizeEnd(function () {
            tablaReclamosPersonas.redimencionar_tabla();
        });
    };



    var eventos = function () {
        //Bnoton de Opciones
        
        gridObj.on("click", botones.opciones, function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();
            var id = $(this).attr("data-id");
            var idpoliza = $(this).attr("data-poliza");
            var certificado = $(this).attr("data-certificado");
            var rowINFO = $.extend({}, gridObj.getRowData(id));
            var options = rowINFO.link;

            formularioCrear.interesesPoliza( idpoliza,"modal", id);
            opcionesModal.modal('hide');
        }); 

        $(botones.modal).on("click", function (e) {
            
            if (id_tipo_poliza == 2) {
                if (vista == "crear" || ( vista == "editar" && permiso_editar == 1 &&  typeof formularioCrear.reclamoInfo.estado != "undefined" && formularioCrear.reclamoInfo.estado != "Cerrado" && formularioCrear.reclamoInfo.estado != "Anulado") ) {
                    recargar();
                    $('#verModalIntereses').modal('show');                
                    
                    //Boton de Buscar Colaborador
                    $(botones.buscar).on("click", function (e) {
                        e.preventDefault();
                        e.returnValue = false;
                        e.stopPropagation();
                        buscarReclamos();
                    });
                    //Boton de Reiniciar jQgrid
                    $(botones.limpiar).on("click", function (e) {
                        e.preventDefault();
                        e.returnValue = false;
                        e.stopPropagation();
                        recargar();
                        limpiarCampos();
                    });

                    gridObj.on("click", botones.opciones, function (e) {
                        e.preventDefault();
                        e.returnValue = false;
                        e.stopPropagation();
                        var id = $(this).attr("data-id");
                        var idpoliza = $(this).attr("data-poliza");
                        var certificado = $(this).attr("data-certificado");
                        var rowINFO = $.extend({}, gridObj.getRowData(id));
                        var options = rowINFO.link;

                        formularioCrear.interesesPoliza( idpoliza,"modal", id);
                        //Init Modal
                        console.log(rowINFO.numero);
                        opcionesModal.modal('hide');
                    });
                }
            }           
                
        });
        

    };

    

    var recargar = function () {
        var id_poliza = $("#poliza_seleccionado").val();
        if (id_poliza == "") { id_poliza = 0; }
        //Reload Grid
        gridObj.setGridParam({
            url: tablaUrl,
            datatype: "json",
            postData: {
                    edad: "",
                    sexo :"",
                    estatura:"" ,
                    telefono_residencial: "",
                    created_at: "" ,
                    nombre: '' ,
                    estado:"",
                    prima:"",
                    numero:"",
                    relacion:tablaTipo,
                    nombrePersona:"",
                    identificacion: '',
                    no_certificado: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
        }).trigger('reloadGrid');

    };
    var redimencionar_tabla = function () {
        $(window).resizeEnd(function () {
            $(".ui-jqgrid").each(function () {
                var w = parseInt($(this).parent().width()) - 6;
                var tmpId = $(this).attr("id");
                var gId = tmpId.replace("gbox_", "");
                $("#" + gId).setGridWidth(w);
            });
        });
    };


    //Buscar cargo en jQgrid
    var buscarReclamos = function () {
        var id_poliza = $('#poliza_seleccionado').val();
        if (id_poliza == "") { id_poliza = 0; }
        var nombre = $('#modal_nombre_persona').val();
        var cedula = $('#modal_cedula_persona').val();
        var certificado = $('#modal_certificado_persona').val(); 

        if (id_poliza != "" || nombre != "" || cedula != "" || certificado != "" )
        {
            //Reload Grid
            grid_obj.setGridParam({
                url: phost() + 'reclamos/ajax_listar_personas',
                datatype: "json",
                postData: {
                    edad: "",
                    sexo :"",
                    estatura:"" ,
                    telefono_residencial: "",
                    created_at: "" ,
                    nombre: nombre ,
                    estado:"",
                    prima:"",
                    numero:"",
                    relacion:tablaTipo,
                    nombrePersona: nombre,
                    identificacion: cedula,
                    no_certificado: certificado,
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };
    //Limpiar campos de busqueda
    var limpiarCampos = function () {
        console.log("limpiar");
        $('#modal_nombre_persona').val('');
        $('#modal_cedula_persona').val('');
        $('#modal_certificado_persona').val('');
        recargar();
    };


    return{
        init: function () {
            tabla();
            eventos();
            gridObj.jqGrid('setGridWidth', $(".modal-lg").width()-70);
            redimencionar_tabla();
        },
        recargar: function () {
            var id_poliza = $("#poliza_seleccionado").val();
            if (id_poliza == "") { id_poliza = 0; }
            gridObj.setGridParam({
                url: tablaUrl,
                datatype: "json",
                postData: {
                    edad: "",
                    sexo :"",
                    estatura:"" ,
                    telefono_residencial: "",
                    created_at: "" ,
                    nombre: '' ,
                    estado:"",
                    prima:"",
                    numero:"",
                    relacion:tablaTipo,
                    nombrePersona:"",
                    identificacion: '',
                    no_certificado: '',
                    id_poliza: id_poliza,
                    erptkn: tkn
                }
            }).trigger('reloadGrid');
        }
    };

})();

$(function () {
    tablaReclamosPersonas.init();
    $("#jqgh_tablaReclamosPersonasGrid_cb span").removeClass("s-ico");
    $('#jqgh_tablaReclamosPersonasGrid_options span').removeClass("s-ico");
});
