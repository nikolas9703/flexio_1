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
            anterior1: $("#anterior1"),
            anterior2: $("#anterior2"),
            segundotab: $("#segundotab"),
            tercertab: $("#tercertab"),
            primertab: $("#primertab"),
            ramo_nombre: $("#nombre_plan"),
            aseguradora: $("#aseguradora"),
            coberturas: $(".coberturas"),
            iAcordeon: "#accordion",
            iCheckComision: "#ch_comision",
            comision: $("#comision"),
            sobre_comision: $("#sobre_comision")
        },

        init: function () {
            planesCrear = this.settings;
            this.eventos();
        },
        suscribeEvents: function () {
            //checkbox
            planesCrear.switcheryComision = new Switchery(planesCrear.iCheckComision[0], config.checks);

            // var elem = document.querySelector('.js-switch');
            // planesCrear.switcheryComision = new Switchery(elem, config.checks);

        },
        agregarfila: function (evt) {
            var $tr = $('#tabla_fact').find("tbody tr:last").clone();
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
            $('#tabla_fact').find("tbody tr:last").after($tr);

        },
        teclear: function () {
            var Clonedtable = $("#tabla_planes").clone();
            Clonedtable.find("input,select").attr("disabled", true);
            Clonedtable.find("a").remove();
            $("#tabla_final").html("");
            $("#tabla_final").append(Clonedtable);
            $("#mensaje").html("");
        },
        eliminarfila: function (evt) {
            $(evt).parent().parent().parent().remove();
        },
        eventos: function () {
            planesCrear.siguiente1.on("click", function (e) {
                e.preventDefault();
                $(segundotab).trigger('click');
            });
            planesCrear.siguientedos.on("click", function (e) {
                e.preventDefault();
                $(tercertab).trigger('click');
            });
            planesCrear.anterior1.on("click", function (e) {
                e.preventDefault();
                $(primertab).trigger('click')
            });
            planesCrear.anterior2.on("click", function (e) {
                e.preventDefault();
                $(segundotab).trigger('click')
            });
            planesCrear.ramo_nombre.on("keyup", function (e) {
                $("#nombre_plan_final").val($(this).val());
                $("#mensaje").html("");
            });
            planesCrear.aseguradora.on("change", function (e) {//aseguradora_final
                $('#aseguradora_final > option[value="' + $(this).val() + '"]').attr('selected', 'selected');
                $("#mensaje").html("");
            });
        }

    };
$(document).ready(function () {
    planes.init();
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
                        tablaRamos.grid_obj.setGridParam({
                            url: tablaRamos.url,
                            datatype: "json",
                            postData: {
                                nombre: '',
                                erptkn: tkn
                            }
                        }).trigger('reloadGrid');

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
                            nombre.push($('#treeRamos2').find("#" + data.node.parents[i] + "_anchor")[0].lastChild.nodeValue);
                        }
                    }
                    var labelNombre = "";
                    for (i = nombre.length - 1; i >= 0; i = i - 1) {
                        labelNombre += nombre[i] + "/";
                        //console.log(nombre[i]);
                    }
                    labelNombre += data.node.text;
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

    