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
                    if (respuesta.clase != "danger") {
                        $scope.cargar_tree();


                        tablaRamos.grid_obj.setGridParam({
                            url: tablaRamos.url,
                            datatype: "json",
                            postData: {
                                nombre: '',
                                erptkn: tkn
                            }
                        }).trigger('reloadGrid');


                        //$scope.inicializar_plugin(ramo);
                        $scope.limpiarFormRamo(vista);
                        $('#codigo_ramo').rules(
                                "add", {required: true,

                                });

                        $('#descripcion').rules(
                                "add", {required: true,
                                    regex: '^[a-zA-Z0-9áéíóúñ ]+$',
                                });
                        $('#has-error').hide();

                        $('select').each(function () {
                            $('#usuario').rules('add', {
                                required: true,
                                messages: {
                                    required: "Seleccione una opción"

                                }


                            });
                            $('#rol').rules('add', {
                                required: true,
                                messages: {
                                    required: "Seleccione una opción"

                                }


                            });
                            $('#tipo_interes_ramo').rules('add', {
                                required: true,
                                messages: {
                                    required: "Seleccione una opción"

                                }


                            });
                            $('#tipo_poliza_ramo').rules('add', {
                                required: true,
                                messages: {
                                    required: "Seleccione una opción"

                                }


                            });

                        });
                    }

                }
                //$(selfButton).bind("click");
                //$("#addCuentaModal").modal('hide');
            });

        }
    };

    $scope.limpiarFormRamo = function (e) {
        $('.chosen-select').val('').trigger('chosen:updated');
        vista.formRamo.trigger('reset');
        $scope.cargar_tree();

        $('#idEdicion').remove();
        var form = $('#crearRamosForm').validate({
        });
        form.resetForm();
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
                        console.log(nodo);
                        var nodo_id = nodo.id;
                        $('#codigo').val(nodo_id);

                    });
            $('#treeRamos').jstree(true).redraw(true);

            $scope.validaciones();

        }); // fin del done
    };

    $scope.validaciones = function () {
        $("#treeRamos").on("select_node.jstree", function (e, data) {
            /*console.log(e);
             console.log("data");*/
            id_ramo = data.selected[0];

            $.ajax({
                url: phost() + "configuracion_seguros/ajax_verifica_padres_ramo",
                type: "post",
                async: false,
                data: {id_ramo: id_ramo, erptkn: tkn},
                success: function (res) {
                    var inf = $.parseJSON(res);
                    if (inf.permitido == 0) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        //No permitido
                        $("#" + id_ramo + "_anchor").removeClass("jstree-clicked");
                        $("#" + id_ramo + "_anchor").parent("li").attr("aria-selected", "false");
                        $("#" + id_ramo + "_anchor").parent("li").parent("ul").parent("li").attr("aria-selected", "true");

                        var id_p = $("#" + id_ramo + "_anchor").parent("li").parent("ul").parent("li").children("a").attr("id");
                        var id_n = $("[aria-labelledby='" + id_p + "']").attr("id");

                        $("#treeRamos").jstree("select_node", "#" + id_p);
                        $("#codigo").val(id_n);
                        $("#isGrouper").removeAttr("checked").attr("disabled", "disabled");
                    } else {
                        if (inf.count < 2) {
                            $("#isGrouper").removeAttr("disabled");
                        } else {
                            $("#isGrouper").removeAttr("checked").attr("disabled", "disabled");
                        }
                    }
                }
            });
        });
    };

    /*$scope.cargar_tree_planes = function () {
     
     var cuentas = moduloAseguradora.listarRamosTree();
     cuentas.success(function () {
     $("#treeRamosP").jstree("destroy");
     });
     cuentas.done(function (data) {
     console.log("Aqui1");
     var arbol = jQuery.parseJSON(data);
     $('#treeRamosP').jstree(arbol)
     .bind("select_node.jstree", function (e, data) {
     var nodo = data.node;
     var nodo_id = nodo.id;
     $('#idRamo').val(nodo_id);
     var i = 0;
     var nombre = [];
     for (i in data.node.parents) {
     if (data.node.parents[i] != "#") {
     nombre.push($('#treeRamosP').find("#" + data.node.parents[i] + "_anchor").find("#labelramo")[0].lastChild.nodeValue);
     hijos.push(("#labelramo")[0].lastChild.nodeValue);
     }
     }
     console.log("hijos="+hijos);
     var labelNombre = "";
     for (i = nombre.length - 1; i >= 0; i = i - 1) {
     labelNombre += nombre[i] + "/";
     //console.log(nombre[i]);
     }
     
     labelNombre += " "+$(data.node.text).find("#labelramo").prevObject[0].innerText;
     console.log(labelNombre);
     $("#ramo_plan_final").val(labelNombre);
     });
     $('#treeRamosP').jstree(true).redraw(true);
     
     }); // fin del done
     };*/

    $scope.inicializar_plugin = function () {


    };


    $scope.inicializar_plugin();
    $scope.cargar_tree();
    //$scope.cargar_tree_planes();

});


/*$('#treeRamos').on("select_node.jstree", function (e, data) {
 var form =$('#crearRamosForm').validate({
 errorElement : 'div',
 errorLabelContainer: '.customError'
 
 });
 if (data.node.parents.length !== 1) {
 
 }else{
 $('#codigo_ramo').rules(
 "add",{ required: true, 
 
 });
 
 $('#descripcion').rules(
 "add",{ required: true, 
 regex:'^[a-zA-Z0-9áéíóúñ ]+$',
 });
 $('select').each(function() {
 $(this).rules('add', {
 required: true,
 messages: {
 required:  "Seleccione una opción"
 
 }
 
 });
 
 
 });
 }
 });*/

/*$('.chosen-select').on('change',function(){
 var roles = [];
 var usuarios=[];
 if($(this).val()=='todos'){
 
 $("#rol > option").each(function() {
 if($(this).val()!=="todos"){
 roles.push(this.value);
 console.log(roles);
 $('#rol').val(roles);
 }
 });
 
 
 
 $("#usuario > option").each(function() {
 if($(this).val()!=="todos"){
 usuarios.push(this.value);
 console.log(usuarios);
 var value= $('#usuario').val();
 console.log(value);
 }
 });
 
 }
 });
 */