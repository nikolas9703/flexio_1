var config = {

    checks: {
        color: "#023859",
        size: 'small'

    },
    chosen: {
        width: '100%'
    }
};
var planesCrear,

    planes = {

        settings: {
            siguiente1: $("#siguiente1"),
            siguientedos: $("#siguientedos"),
            siguientetres: $("#siguientetres"),
            anterior1: $("#anterior1"),
            anterior2: $("#anterior2"),
            anterior3: $("#anterior3"),
            segundotab: $("#segundotab"),
            tercertab: $("#tercertab"),
            cuartotab: $("#cuartotab"),
            primertab: $("#primertab"),
            ramo_nombre: $("#nombre_plan"),
            aseguradora: $("#aseguradora"),
            impuesto: $("#impuesto"),
            coberturas: $(".coberturas"),
            iAcordeon: "#accordion",
            iCheckComision: "#ch_comision",
            comision: $("#comision"),
            sobre_comision: $("#sobre_comision"),
            ch_comision: $("#ch_comision"),
            codigo_ramo: $("#codigo_ramo"),
            treeRamos: $("#treeRamos"),
            anio_inicio: $("#anio_inicio"),
            anio_fin: $(".anio_fin")
        },

        init: function () {
            planesCrear = this.settings;
            this.eventos();
            $("#p_comision").inputmask('integer',{min:1, max:100}).css("text-align", "left");
            $("#p_sobre_comision").inputmask('integer',{min:1, max:100}).css("text-align", "left");
        },
        suscribeEvents: function () {
            //checkbox
            planesCrear.switcheryComision = new Switchery(planesCrear.iCheckComision[0], config.checks);

            // var elem = document.querySelector('.js-switch');
            // planesCrear.switcheryComision = new Switchery(elem, config.checks);

        },
        agregarfila: function (evt,tabla) {
            var $tr = $('#'+tabla).find("tbody tr:last").clone();
            $(evt).parent().parent().find("#agregarbtn").attr('style', 'margin-top: 5px;display: none');
            $(evt).parent().parent().find("#eliminarbtn").attr('style', 'margin-top: 5px');
            $tr.attr('style', '');
            $tr.find("input:text").val("");
            $tr.find("input:hidden").val("");
            $tr.find("input,select").attr("name", function () {
                var name = this.name;
                return name;
            }).attr("id", function () {
                var id = this.id;
                return id;
            });
            //.removeAttr()
            if(tabla == 'tabla_comisiones'){
                var fila=1;
                $("#tabla_comisiones tbody tr").each(function (index){
                    fila++;
                });
                $tr.find("#anio_inicio").attr('name', 'anio_inicio['+fila+']');
                $tr.find("#anio_fin").attr('name', 'anio_fin['+fila+']');
                $tr.find("#p_comision").attr('name', 'p_comision['+fila+']');
                $tr.find("#p_sobre_comision").attr('name', 'p_sobre_comision['+fila+']');
                var inicio = $(evt).parent().parent().parent().find("#anio_fin").val();
                $tr.find("#anio_final").val(Number(inicio)+1);
                $tr.find("#anio_inicio option").remove();
                $tr.find("#anio_inicio option").remove();
                for (i = (Number(inicio)+1); i < 21; i++) {
                    $tr.find('#anio_inicio').append($('<option>', {
                        value: i,
                        text: i
                    }));
                }
                //$(evt).parent().parent().parent().find("#anio_fin").removeAttr('onchange');
                $tr.attr('style', 'padding-top: 10px;');
                $tr.attr('data-num', fila);
                $tr.find("#p_comision").inputmask('integer',{min:1, max:100}).css("text-align", "left");
                $tr.find("#p_sobre_comision").inputmask('integer',{min:1, max:100}).css("text-align", "left");
            }
            $('#'+tabla).find("tbody tr:last").after($tr);
            
            
            //$('select option').remove();
        },
        copyTabla: function (tabla1,tabla2) {
            var Clonedtable = $("#"+tabla1).clone();
            Clonedtable.find("input,select").attr("disabled", true);
            Clonedtable.find("a").remove();
            if(tabla1 == 'tabla_comis'){
                Clonedtable.find("select,.th_inicio,.th_fin").remove();
                Clonedtable.find(".th_anio").attr('style', 'width: 20%;');
                /*Clonedtable.find("select").remove();
                Clonedtable.find("select").remove();*/
            }
            $("#"+tabla2).html("");
            $("#"+tabla2).append(Clonedtable);
            $("#mensaje").html("");
            $("#"+tabla2).find("#"+tabla1).removeClass( "col-lg-10" );
            $("#"+tabla2).find("#"+tabla1).addClass( "col-lg-12" );
            $("#"+tabla2).find(".form-group").attr("style", 'margin-bottom: 0px ! important; padding-left: 0px; padding-right: 0px;padding-bottom: 5px;');
            
        },
        eliminarfila: function (evt) {
            $(evt).parent().parent().parent().remove();
            var con=0;
            $("#tabla_comisiones tbody tr").each(function (index){
                con++;
                $(this).find("#anio_inicio").attr('name', 'anio_inicio['+con+']');
                $(this).find("#anio_fin").attr('name', 'anio_fin['+con+']');
                $(this).find("#p_comision").attr('name', 'p_comision['+con+']');
                $(this).find("#p_sobre_comision").attr('name', 'p_sobre_comision['+con+']');
                $(this).attr('data-num', (Number(con)));
            });
        },
        anioFin: function (evt) {
            if($(evt).val() == '+'){
                $(".btn-tabla-agregar").hide();
                $(".btn-tabla-eliminar").hide();
                var inicio = $(evt).parent().parent().parent().find("#anio_inicio").val();
                $(evt).parent().parent().parent().find("#anio_final").val(inicio+$(evt).val());
                var indice = $(evt).parent().parent().parent().data("num");
                $("#tabla_comisiones tbody tr").each(function (index){
                    if(($(this).data("num"))>indice){
                        this.remove();
                    }
                });
            }else{
                $(evt).parent().find("#anio_fin-error").remove();;
                var indice = $(evt).parent().parent().parent().data("num");
                var indece2 = $('#tabla_comisiones').find("tbody tr:last").data("num");
                if(indice == indece2){
                    $(".btn-tabla-eliminar").show();
                    $('#tabla_comisiones').find("tbody tr:last").find(".btn-tabla-agregar").show();
                    $('#tabla_comisiones').find("tbody tr:last").find(".btn-tabla-eliminar").hide();
                }else{
                    var indice = $(evt).parent().parent().parent().data("num");
                    var acti = 1;
                    var inicio = $(evt).parent().parent().parent().find("#anio_fin").val();
                    $("#tabla_comisiones tbody tr").each(function (index){
                        
                        if(($(this).data("num"))>indice && acti==1){
                            $(this).find("#anio_inicio option").remove();
                            acti=0;
                            $(this).find("#anio_final").val(Number(inicio)+1);
                            for (i = (Number(inicio)+1); i < 21; i++) {
                                $(this).find('#anio_inicio').append($('<option>', {
                                    value: i,
                                    text: i
                                }));
                            }
                        }
                        
                    });
                }
                
               
            }
            planes.copyTabla('tabla_comis','tabla_final_comisiones');
        },
        anioInicio: function (evt) {
            $(evt).parent().parent().parent().find("#anio_final").val($(evt).val());
            planes.copyTabla('tabla_comis','tabla_final_comisiones');
        },
        eventos: function () {//treeRamos
            planesCrear.codigo_ramo.on("keyup", function (e) {
                var valor = $(this).val();
                var nuevo = valor.toUpperCase();
                $(this).val(nuevo);
            });
            planesCrear.treeRamos.on("click", function (e) {
                e.preventDefault();
                $(".requerido").html("*");
                $('#tipo_interes_ramo').attr('required', true);
                $('#tipo_poliza_ramo').attr('required', true);
            });
            planesCrear.siguiente1.on("click", function (e) {
                e.preventDefault();
                $(segundotab).trigger('click');
                $( "#tab2-1" ).removeClass( "active" );
                $( "#tab_aseguradora" ).removeClass( "active" );
                $( "#tab2-2" ).addClass( "active" );
                $( "#tab_coberturas" ).addClass( "active" );
            });
            planesCrear.siguientedos.on("click", function (e) {
                e.preventDefault();
                $(tercertab).trigger('click');
                $( "#tab2-2" ).removeClass( "active" );
                $( "#tab_coberturas" ).removeClass( "active" );
                $( "#tab2-3" ).addClass( "active" );
                $( "#tab_comision" ).addClass( "active" );
            });
            planesCrear.siguientetres.on("click", function (e) {
                e.preventDefault();
                var formValidado = $('#crearplanesForm').validate();
                if (formValidado.form() === true) {
                    $(cuartotab).trigger('click');
                    $( "#tab2-3" ).removeClass( "active" );
                    $( "#tab_comision" ).removeClass( "active" );
                    $( "#tab2-4" ).addClass( "active" );
                    $( "#tab_confirmar" ).addClass( "active" );
                }
            });
            planesCrear.anterior1.on("click", function (e) {
                e.preventDefault();
                $(primertab).trigger('click');
                $( "#tab2-2" ).removeClass( "active" );
                $( "#tab_coberturas" ).removeClass( "active" );
                $( "#tab2-1" ).addClass( "active" );
                $( "#tab_aseguradora" ).addClass( "active" );
            });
            planesCrear.anterior2.on("click", function (e) {
                e.preventDefault();
                $(segundotab).trigger('click');
                $( "#tab2-3" ).removeClass( "active" );
                $( "#tab_comision" ).removeClass( "active" );
                $( "#tab2-2" ).addClass( "active" );
                $( "#tab_coberturas" ).addClass( "active" );
            });
            planesCrear.anterior3.on("click", function (e) {
                e.preventDefault();
                $(tercertab).trigger('click');
                $( "#tab2-4" ).removeClass( "active" );
                $( "#tab_confirmar" ).removeClass( "active" );
                $( "#tab2-3" ).addClass( "active" );
                $( "#tab_comision" ).addClass( "active" );
            });
            planesCrear.ramo_nombre.on("keyup", function (e) {
                $("#nombre_plan_final").val($(this).val());
                $("#mensaje").html("");
            });
            planesCrear.comision.on("keyup", function (e) {
                $("#comision_final").val($(this).val());
            });
            planesCrear.sobre_comision.on("keyup", function (e) {
                $("#sobre_comision_final").val($(this).val());
            });
            planesCrear.aseguradora.on("change", function (e) {//aseguradora_final
                $('#aseguradora_final > option[value="' + $(this).val() + '"]').attr('selected', 'selected');
                $("#mensaje").html("");
            });
            planesCrear.impuesto.on("change", function (e) {
                $('#impuesto_final > option[value="' + $(this).val() + '"]').attr('selected', 'selected');
            });
            planesCrear.ch_comision.on("change", function (e) {//
                $('#ch_comision_final').trigger('click');
                var Cloned = $("#ch_comision_copy").clone();
                $("#ch_comision_paste").html("");
                $("#ch_comision_paste").append(Cloned);
                
            });
        }

    };
    
$(document).ready(function () {
    planes.init();
    
    //popular desde asegurados
    if(typeof vista !== 'undefined'){
        
        if(vista == 'planes-editar' || vista == 'planes-ver') {
              var data_plan = JSON.parse(data_planes);
              $('#tab_ramos').removeClass( "active" );  
              $('#tab_planes').addClass( "active" );
              $('#tab-1').removeClass( "active" );  
              $('#tab-3').addClass( "active" ); 
              if(data_plan.desc_comision =='SI'){
                  $('#ch_comision').trigger('click') ;
                  $('#ch_comision_final').trigger('click') ;
              }
              $("#treeRamos2").bind("loaded.jstree",function(e,data){
                data.instance.select_node(data_plan.id_ramo);
              });
              new Vue({
                el: '#crearplanesForm',
                data: {
                    nombre_plan: data_plan.nombre,
                    aseguradora: data_plan.id_aseguradora,
                    impuesto: data_plan.id_impuesto,
                    ch_comision: (data_plan.desc_comision =='SI')? true : false,
                    nombre_plan_final:data_plan.nombre,
                    aseguradora_final: data_plan.id_aseguradora,
                    impuesto_final: data_plan.id_impuesto,
                    comision_final: data_plan.comision,
                    sobre_comision_final: data_plan.sobre_comision,
                    id_planes: id_planes
                }
              });
              planes.copyTabla("tabla_planes","tabla_final");
              planes.copyTabla('tabla_comis','tabla_final_comisiones');
              if(vista == 'planes-ver'){
                    $(cuartotab).trigger('click');
                    $( "#tab2-1" ).removeClass( "active" );
                    $( "#tab_aseguradora" ).removeClass( "active" );
                    $( "#tab2-4" ).addClass( "active" );
                    $( "#tab_confirmar" ).addClass( "active" );
                    planesCrear.anterior3.remove();
                    
              }

        }else if(vista == 'planes-crear'){
            $('#tab_ramos').removeClass( "active" );  
            $('#tab_planes').addClass( "active" );
            $('#tab-1').removeClass( "active" );  
            $('#tab-3').addClass( "active" );
            new Vue({
                el: '#crearplanesForm',
                data: {
                    aseguradora: id_aseguradora,
                    aseguradora_final: id_aseguradora,
                    vista:vista
                }
              });
        }
    }
    
});

    

bluapp.controller("configRamosController", function ($scope, $http) {

    $scope.ramolimpiar = {
        nombre: '',
        descripcion: '',
        codigo: '',
        cuenta_id: ''
    };

    var vista = {
        formRamo: $('#crearRamosForm')
    };
    var botonModal = {
        editar: 'a.editarImpuestoBtn',
        cambiarEstado: 'a.cambiarEstadoImpuestoBtn'
    };


    $scope.guardarRamo = function (ramo) {

        $scope.ramo = angular.copy(ramo);
        $('input[name="nombre').rules(
           "add",{ required: true, 
            regex:'^[a-zA-Z0-9áéíóúñ ]+$',
        });
        $('input[name="descripcion').rules(
           "add",{ required: false, 
            regex:'^[a-zA-Z0-9áéíóúñ ]+$',
        });
        var formValidado = vista.formRamo.validate();
        if (formValidado.form() === true) {
            //$(selfButton).unbind("click");
            var guardar = moduloAseguradora.guardarRamos(vista.formRamo);
            guardar.done(function (data) {
                var respuesta = $.parseJSON(data);
                if (respuesta.estado == 200) {
                    $("#mensaje_info").empty().html('<div id="success-alert" class="alert alert-' + respuesta.clase + '"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>' + respuesta.mensaje + '</div>');
                    $('html, body').animate({
                        scrollTop: $("#mensaje_info").offset().top
                    }, 500);
                    if(respuesta.clase != "danger"){
                        $scope.cargar_tree();
                        $scope.cargar_tree_planes();
                        
                        tablaRamos.grid_obj.setGridParam({
                            url: tablaRamos.url,
                            datatype: "json",
                            postData: {
                                nombre: '',
                                erptkn: tkn
                            }
                        }).trigger('reloadGrid');
                        $(".requerido").html("");
                        $('#tipo_interes_ramo').attr('required', false);
                        $('#tipo_poliza_ramo').attr('required', false);
                        $('#idEdicion').remove();
                        $('#cuenta_id').val('').trigger('chosen:updated');
                        vista.formRamo.trigger('reset');
                        $scope.inicializar_plugin(ramo);
                    }
                    
                }
                //$(selfButton).bind("click");
                //$("#addCuentaModal").modal('hide');
            });

        }
    };

    $scope.limpiarFormRamo = function (e) {
        vista.formRamo.trigger('reset');
        $scope.cargar_tree();
        $('#idEdicion').remove();
    };
    


    $scope.cargar_tree = function () {
        var cuentas = moduloAseguradora.listarRamosTree();
        cuentas.success(function () {
            $("#cuentas_tabs li:first-child").addClass('active');
            $("#nombre").val('');
            $("#codigo").val('');
            $("#codigo").val('');
            $("#padre_id").val('');
            $('#codigo').prop('readonly', true);
            $("#treeRamos").jstree("destroy");
        });
        cuentas.done(function (data) {

            var arbol = jQuery.parseJSON(data);
            $('#treeRamos').jstree(arbol)
                .bind("select_node.jstree", function (e, data) {

                    var nodo = data.node;
                    var nodo_id = nodo.id;
                    $('#codigo').val(nodo_id);
                });
            $('#treeRamos').jstree(true).redraw(true);
            

        }); // fin del done
    };

    $scope.cargar_tree_planes = function () {
        var cuentas = moduloAseguradora.listarRamosTree();
        cuentas.success(function () {
            $("#treeRamos2").jstree("destroy");
        });
        cuentas.done(function (data) {

            var arbol = jQuery.parseJSON(data);
            $('#treeRamos2').jstree(arbol)
                .bind("select_node.jstree", function (e, data) {
                    var nodo = data.node;
                    var nodo_id = nodo.id;
                    $('#idRamo').val(nodo_id);
                    var i = 0;
                    var nombre = []
                    for (i in data.node.parents) {
                        if (data.node.parents[i] != "#") {
                            nombre.push($('#treeRamos2').find("#" + data.node.parents[i] + "_anchor").find("#labelramo")[0].lastChild.nodeValue);
                        }
                    }
                    var labelNombre = "";
                    for (i = nombre.length - 1; i >= 0; i = i - 1) {
                        labelNombre += nombre[i] + "/";
                        //console.log(nombre[i]);
                    }
                    
                    labelNombre += " "+$(data.node.text).find("#labelramo").prevObject[0].innerText;
                    console.log(labelNombre);
                    $("#ramo_plan_final").val(labelNombre);
                });
            $('#treeRamos2').jstree(true).redraw(true);
            
        }); // fin del done
    };

    $scope.inicializar_plugin = function () {
        vista.formRamo.validate({
            focusInvalid: true,
            ignore: ".ignore",
            wrapper: '',
        });
    };


    $scope.inicializar_plugin();
    $scope.cargar_tree();
    $scope.cargar_tree_planes();

});

bluapp.controller("configPlanesController", function ($scope, $http) {
    var vista = {
        formPlanes: $('#crearplanesForm')
    };


    $scope.guardarPlanes = function () {

        var formValidado = vista.formPlanes.validate();
        if (formValidado.form() === true) {
            vista.formPlanes.submit();

        } else {
            $("#mensaje").html("");
            $("#mensaje").append('<div class="alert alert-dismissable alert-danger" >\n\
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>\n\
            Debe completar el formulario </div>');
        }

    };


    $scope.inicializar_plugin = function () {
        vista.formPlanes.validate({
            focusInvalid: true,
            ignore: ".ignore",
            wrapper: '',
        });
    };
    $scope.inicializar_plugin();
});
$("#isGrouper").change(function() {
     alert("hola");
    var form =$('#crearRamosForm').validate();
    if(this.checked) {
     $('#codigo_ramo').rules(
           "add",{ required: true, 
            
        });
     $('input[name="descripcion').rules(
           "add",{ required: true, 
            
        });
    $('#tipo_interes_ramo').rules(
           "add",{ required: true, 
            
        });
    $('#tipo_poliza_ramo').rules(
           "add",{ required: true, 
            
        });
     $('#crearRamosForm').validate();
 }else{
    
    form.resetForm();
    
    $('#codigo_ramo').rules(
           "add",{ required: false, 
            
        });
    $('#tipo_interes_ramo').rules(
           "add",{ required: false, 
            
        });
    $('#tipo_poliza_ramo').rules(
           "add",{ required: false, 
            
        });
    $('input[name="descripcion').rules(
           "add",{ required: false, 
            
        });
    $('#crearRamosForm').validate();
}
});
