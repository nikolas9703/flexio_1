//Directiva para campos Chosen
Vue.directive('chosen', {
    twoWay: true,
    bind: function () {
        var scope = this;
        var formulario = $('#plantillaForm');

        setTimeout(function () {
            $(scope.el).chosen({
                width: '100%',
                inherit_select_classes: true
            }).on('chosen:ready', function (e, params) {

                //Remover clase hide de los campos y contenedores
                $(this).closest('div').find('div.chosen-container').removeClass('hide').closest('form').removeClass('hide').closest('.filtro-plantillas').removeClass('hide');

                //Ejecutar trigger change
                //formulario.find('select').trigger('chosen:updated');

            }).trigger('chosen:ready').change(function (e) {
                scope.set(scope.el.value);

                //
                //---------------------------------------------
                // Al cambiar la seleccion de plantilla
                // cambiar mediante tab, la plantilla que se muestra
                // en la ventana de abajo.
                //---------------------------------------------
                var data_tab_id = $(e.currentTarget).find('option:selected').attr('data-tab-id');

                formulario.find('ul').find('a[href="#' + data_tab_id + 'Tab"]').trigger('click');

            });
        }.bind(this), 500);
    },
    update: function (nv, ov) {
        // note that we have to notify chosen about update

        $(this.el).trigger("chosen:updated");
    }
});

$('#plantilla_id').on('change', function () {
    var plantilla_id_selected2 = $('#plantillaForm').find('option:selected').val();
    //var plantilla_id_selected2 = $(scope.el).val();
    plantillaModel.seleccionarPlantilla2(plantilla_id_selected2);
});

var plantillaModel = new Vue({
    el: '#formulario-plantilla',
    data: {
        plantilla_id_selected: typeof plantilla_id_selected != 'undefined' ? plantilla_id_selected : '',
        colaborador_id_selected: typeof colaborador_id_selected != 'undefined' ? colaborador_id_selected : '',
        firmado_por_id_selected: typeof firmado_por_id_selected != 'undefined' ? firmado_por_id_selected : '',
        id_plant_detalle:  typeof id_plant_detalle != 'undefined' ? id_plant_detalle : '',
        destinatario_id_selected: '',
        prefijo_id_selected: '',
        firmado_por_selected: '',
        estado_id_selected: 5,
        plantillaOptions: [],
        colaboradoresOptions: $.parseJSON(colaboradores),
        acreedoresOptions: $.parseJSON(acreedores),
        prefijosOptions: $.parseJSON(prefijos),
        firmado_porOptions: $.parseJSON(firmado_por),
        estadosOptions: $.parseJSON(estados),
        plantilla_verOptions: $.parseJSON(plantilla_ver),
        displayBtn: 'hide',
        vistaPreviaBtn: 'Vista Previa',
    },
    ready: function () {

        var scope = this;
        var formulario = $('#plantillaForm');

        //---------------------------------------------
        // Obtener el arreglo de opciones de plantillas
        // y establcer array plantillaOptions
        //---------------------------------------------
        var options = this.getPlantillasObjectArray();
        this.plantillaOptions = _.concat(this.plantilla_options, options);

        //Ejecutar en (x) tiempo, despues
        //de cargar pagina completa
        //setTimeout(function(){
        this.$nextTick(function () {
            //Verificar si existe variable "plantilla_id_selected"
            if (typeof plantilla_id_selected != 'undefined') {

                //ejecutar evento change

                //$('#plantilla_id').trigger('change').trigger('chosen:change');

                //$('#plantilla_id').trigger('change').trigger('chosen:change');

                //ejecutar funcion
                if (scope.plantilla_verOptions != null) {

                    $('#plantilla_id').on('change', function () {
                        var plantilla_id_selected3 = scope.plantilla_id_selected;
                        plantillaModel.seleccionarPlantilla2(plantilla_id_selected3);
                    });
                } else {
                   // console.log("crear");
                    scope.seleccionarPlantilla(scope.plantilla_id_selected);
                    moduloUpdateChosen.actualizar();
                }


            }



            //Validacion jQuery Validate
            $.validator.setDefaults({
                errorPlacement: function (error, element) {
                    return true;
                }
            });
            $(formulario).validate({
                focusInvalid: true,
                ignore: '',
                wrapper: ''
            });

            //verificar si existe plugin
            //adaptador de ckeditor
            if ($().ckeditor != undefined) {

                //inicializar ckeditor
                $('.inline-ckeditor').ckeditor(config);
            }

            //Evento Boton de Exportar
            $('#moduloOpciones').find('a#exportarBtn').on('click', function (e) {
                e.preventDefault();
                e.returnValue = false;
                e.stopPropagation();

                scope.exportarPlantilla();
            });

            //Mostrar botones (cancelar, guardar)
            scope.displayBtn = 'show';

        });
    },
    methods: {
        exportarPlantilla: function () {

            var scope = this;
            var formulario = $('#plantillaForm');

            if ($(formulario).validate().form() == false) {
                //mostrar mensaje
                toastr.error('Debe seleccionar los campos requeridos para poder generar la vista previa.');
                return false;
            }

            //Obtener informacion del Colaborador
            Vue.http.options.emulateJSON = true;
            Vue.http({
                url: phost() + 'plantillas/ajax-seleccionar-datos',
                method: 'POST',
                headers: {
                    erptkn: tkn,
                },
                data: {
                    erptkn: tkn,
                    colaborador_id: this.colaborador_id_selected,
                    firmado_por_id: this.firmado_por_id_selected,
                }
            }).then(function (response) {
                // success callback

                //Check Session
                if ($.isEmptyObject(response.data.session) == false) {
                    window.location = phost() + "login?expired";
                }

                //Reemplazar las etiquetas por los valores.

                // Generando PDF
                var plantilla = scope.reemplazarEtiquetas(response);
                $('#ocultohtml').empty().append(plantilla);
                $('#ocultohtml').css('display', 'block');
                var data_html = $('#ocultohtml'),
                        cache_width = data_html.width(),
                        a4 = [595.28, 750.89];

                //Generamos una imagen usando Canvas

                getCanvas().then(function (canvas) {
                    var img = canvas.toDataURL("image/png");
                    var imgWidth = 195;
                    var pageHeight = 295;
                    var imgHeight = canvas.height * imgWidth / canvas.width;
                    var heightLeft = imgHeight;

                    var doc = new jsPDF('p', 'mm');
                    var position = 10;

                    doc.addImage(img, 'PNG', 8, position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;

                    while (heightLeft >= 0) {
                        position = heightLeft - imgHeight;
                        doc.addPage();
                        doc.addImage(img, 'PNG', 8, position, imgWidth, imgHeight);
                        heightLeft -= pageHeight;
                    }
                    var currentDate = new Date();
                    var day = currentDate.getDate();
                    var month = currentDate.getMonth() + 1;
                    var year = currentDate.getFullYear();
                    var n = currentDate.getTime();
                    doc.save('plantilla-' + day + month + year + n + '.pdf');
                    data_html.width(cache_width);
                });

                function getCanvas() {
                    data_html.width((a4[0] * 1.33333) - 80).css('max-width', '720px');
                    return html2canvas(data_html, {
                        imageTimeout: 2000,
                        removeContainer: true
                    });
                }
                $('#ocultohtml').css('display', 'none');



            }, function (response) {
                // error callback
                //console.log(response, 'DOS');
            });


        },
        getPlantillasObjectArray: function () {
            var optgroups = [];
            var grupos = $.parseJSON(grupo_plantillas);
            var i = 0;
            $.each(grupos, function (index, plantillas) {

                var grupo_name = index;
                var grupo_collapse_id = index.replace(/(cartas)/gi, 'carta').replace(/(adendas)/gi, 'adenda').replace(/\s/gi, '-');
                var grupo_collapse_name = ucFirst(index.replace(/(cartas)/gi, 'carta').replace(/(adendas)/gi, 'adenda'));

                //Botones Acordion - Agrupados
                if (plantillas.length > 1) {

                    //Agrupador
                    optgroups.push({
                        label: grupo_name
                    });

                    //Opciones del agrupador
                    var j = 0;
                    $.each(plantillas, function (indx, plantilla) {

                        var nav_tab_id = ucWords(grupo_collapse_name + '  ' + plantilla['nombre']).replace(/\s/gi, '');

                        if (optgroups[i] != undefined) {
                            //si no existe options
                            if (optgroups[i].options == undefined) {

                                //inicializar array options
                                optgroups[i].options = [];
                                optgroups[i].options.push({
                                    id: plantilla['id'],
                                    nombre: grupo_collapse_name + ' - ' + ucFirst(plantilla['nombre']),
                                    nav_tab_id: nav_tab_id
                                });
                            } else {

                                optgroups[i].options.push({
                                    id: plantilla['id'],
                                    nombre: grupo_collapse_name + ' - ' + ucFirst(plantilla['nombre']),
                                    nav_tab_id: nav_tab_id
                                });
                            }
                        }
                        j++;
                    });

                } else {

                    var nav_tab_id = ucWords(grupo_name).replace(/\s/gi, '').replace(/(&oacute;)/gi, 'o');

                    optgroups.push({
                        label: grupo_name,
                        value: plantillas[0]['id'],
                        nav_tab_id: nav_tab_id
                    });
                }
                i++;
            });

            return optgroups;
        },
        seleccionarPlantilla: function (plantilla_id) {
            if (plantilla_id == '') {
                return false;
            }
            plantilla_id_selected == "5" || plantilla_id_selected == "6" || plantilla_id_selected == "7" || plantilla_id_selected == "8" || plantilla_id_selected == "9" || plantilla_id_selected == "10" || plantilla_id_selected == "11" ? $('#prefijo_id').attr("disabled", true).trigger('chosen:updated') : $('#prefijo_id').removeAttr("disabled").trigger('chosen:updated');
            Vue.http.options.emulateJSON = true;
            Vue.http({
                url: phost() + 'plantillas/ajax-seleccionar-plantilla',
                method: 'POST',
                headers: {
                    erptkn: tkn,
                },
                data: {
                    erptkn: tkn,
                    plantilla_id: plantilla_id_selected
                }
            }).then(function (response) {
                // success callback

                //Verificar tab activo
                $('.tab-content').find('div.tab-pane.active').find('textarea.inline-ckeditor').val(response.data.contenido);

            }, function (response) {
                // error callback
                //console.log(response, 'DOS');
            });
        },
        seleccionarPlantilla2: function (plantilla_id_selected2) {

            //setTimeout(function(){
            $(".inline-ckeditor").val("");

            if (plantilla_id == '') {
                return false;
            }
            plantilla_id_selected2 == "5" || plantilla_id_selected2 == "6" || plantilla_id_selected2 == "7" || plantilla_id_selected2 == "8" || plantilla_id_selected2 == "9" || plantilla_id_selected2 == "10" || plantilla_id_selected2 == "11" ? $('#prefijo_id').attr("disabled", true).trigger('chosen:updated') : $('#prefijo_id').removeAttr("disabled").trigger('chosen:updated');
            Vue.http.options.emulateJSON = true;
            Vue.http({
                url: phost() + 'plantillas/ajax-seleccionar-plantilla',
                method: 'POST',
                headers: {
                    erptkn: tkn,
                },
                data: {
                    erptkn: tkn,
                    plantilla_id: plantilla_id_selected2
                }
            }).then(function (response) {
                // success callback
//$('#plantilla_id').trigger('change').trigger('chosen:change');
                //Verificar tab activo

                $('.tab-content').find('div.tab-pane.active').find('textarea.inline-ckeditor').val(response.data.contenido);

            }, function (response) {
                // error callback
                //console.log(response, 'DOS');
            });
            // }, 500)

        },
        vistaPrevia: function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var scope = this;
            var formulario = $('#plantillaForm');
            var vistaPreviaModal = $('#vistaPreviaModal');

            if ($(formulario).validate().form() == false) {
                //mostrar mensaje
                toastr.error('Debe seleccionar los campos requeridos para poder generar la vista previa.');
                return false;
            }

            //Obtener informacion del Colaborador
            Vue.http.options.emulateJSON = true;
            Vue.http({
                url: phost() + 'plantillas/ajax-seleccionar-datos',
                method: 'POST',
                headers: {
                    erptkn: tkn,
                },
                data: {
                    erptkn: tkn,
                    colaborador_id: this.colaborador_id_selected,
                    firmado_por_id: this.firmado_por_id_selected
                }
            }).then(function (response) {
                // success callback

                //Check Session
                if ($.isEmptyObject(response.data.session) == false) {
                    window.location = phost() + "login?expired";
                }

                //Reemplazar las etiquetas por los valores.
                var plantilla = scope.reemplazarEtiquetas(response);

                //Opciones Modal
                vistaPreviaModal.find('.modal-title').empty().append('Vista Previa');
                vistaPreviaModal.find('.modal-body').empty().append(plantilla);
                vistaPreviaModal.modal('show');

            }, function (response) {
                // error callback
                //console.log(response, 'DOS');
            });
        },
        reemplazarEtiquetas: function (response) {
            //console.log(response);
            //Obtener texto de plantilla
            var editor = CKEDITOR.instances['inline-ckeditor-' + this.plantilla_id_selected];
            // console.log(editor);
            var destinatario = $('#plantillaForm').find('#destinatario_id').find('option:selected').text() != 'Seleccione' ? $('#plantillaForm').find('#destinatario_id').find('option:selected').text() : "";
            var prefijo = $('#plantillaForm').find('#prefijo_id').find('option:selected').text() != 'Seleccione' ? $('#plantillaForm').find('#prefijo_id').find('option:selected').text() : "";

            var nombre = response.data.colaborador.nombre != null ? response.data.colaborador.nombre : "";
            var apellido = response.data.colaborador.apellido != null ? response.data.colaborador.apellido : "";
            var colaborador_sexo = response.data.colaborador.colaborador_sexo != null ? response.data.colaborador.colaborador_sexo : "";
            var colaborador_edad = response.data.colaborador.colaborador_edad != null ? response.data.colaborador.colaborador_edad : "";
            var colaborador_estado_civil = response.data.colaborador.colaborador_estado_civil != null ? response.data.colaborador.colaborador_estado_civil : "";
            var colaborador_nacionalidad = response.data.colaborador.colaborador_nacionalidad != null ? response.data.colaborador.colaborador_nacionalidad : "";
            var colaborador_direccion = response.data.colaborador.colaborador_direccion != null ? response.data.colaborador.colaborador_direccion : "";
            var colaborador_ciclo = response.data.colaborador.colaborador_ciclo != null ? response.data.colaborador.colaborador_ciclo : "";
            var colaborador_botas = response.data.colaborador.colaborador_botas != null ? response.data.colaborador.colaborador_botas : "";
            var numero_cuenta = response.data.colaborador.numero_cuenta != null ? response.data.colaborador.numero_cuenta : "";
            var tipo_salario = response.data.colaborador.tipo_salario != null ? response.data.colaborador.tipo_salario : "";
            var cedula = response.data.colaborador.cedula != null ? response.data.colaborador.cedula : "";
            var seguro_social_colaborador = response.data.colaborador.seguro_social_colaborador != null ? response.data.colaborador.seguro_social_colaborador : "";
            var fecha_inicio_labores = response.data.colaborador.fecha_inicio_labores != null ? response.data.colaborador.fecha_inicio_labores : "";
            var fecha_nacimiento = response.data.colaborador.fecha_nacimiento != null ? response.data.colaborador.fecha_nacimiento : "";
            var cargo = response.data.colaborador.cargo != null ? response.data.colaborador.cargo : "";
            var salario = response.data.colaborador.salario != null ? response.data.colaborador.salario : "";
            var salario_hora = response.data.colaborador.salario_hora != null ? response.data.colaborador.salario_hora : "";
            var horas_semanales = response.data.colaborador.horas_semanales != null ? response.data.colaborador.horas_semanales : "";
            var nombre_usuario = response.data.usuario.nombre_completo != null ? response.data.usuario.nombre_completo : response.data.usuario.nombre + ' ' + response.data.usuario.apellido;
            var telefono = response.data.usuario.telefono != null ? response.data.usuario.telefono : '';
            var cargo_usuario = response.data.usuario.cargo != null ? response.data.usuario.cargo : '';
            var cedula_usuario = response.data.usuario.cedula != null ? response.data.usuario.cedula : '';
            var empresa_direccion = response.data.colaborador.empresa_direccion != null ? response.data.colaborador.empresa_direccion : '';
            var empresa_tomo = response.data.colaborador.empresa_tomo != null ? response.data.colaborador.empresa_tomo : '';
            var empresa_folio = response.data.colaborador.empresa_folio != null ? response.data.colaborador.empresa_folio : '';
            var empresa_asiento = response.data.colaborador.empresa_asiento != null ? response.data.colaborador.empresa_asiento : '';

            //beneficiarios
            var centro_contable = response.data.colaborador.centro_contable != null ? response.data.colaborador.centro_contable : "";
            var area_negocio = response.data.colaborador.area_negocio != null ? response.data.colaborador.area_negocio : "";
            var beneficiario_principal_no = response.data.colaborador.beneficiario_principal_no != null ? response.data.colaborador.beneficiario_principal_no : "";
            var beneficiario_principal = response.data.colaborador.beneficiario_principal != null ? response.data.colaborador.beneficiario_principal : "";

            var beneficiario_principal_parentesco = response.data.colaborador.beneficiario_principal_parentesco != null ? response.data.colaborador.beneficiario_principal_parentesco : "";
            var beneficiario_principal_cedula = response.data.colaborador.beneficiario_principal_cedula != null ? response.data.colaborador.beneficiario_principal_cedula : "";
            var beneficiario_principal_porcentaje = response.data.colaborador.beneficiario_principal_porcentaje != null ? response.data.colaborador.beneficiario_principal_porcentaje : "";
            var beneficiario_contingente_no = response.data.colaborador.beneficiario_contingente_no != null ? response.data.colaborador.beneficiario_contingente_no : "";
            var beneficiario_contingente = response.data.colaborador.beneficiario_contingente != null ? response.data.colaborador.beneficiario_contingente : "";
            var beneficiario_contingente_parentesco = response.data.colaborador.beneficiario_contingente_parentesco != null ? response.data.colaborador.beneficiario_contingente_parentesco : "";
            var beneficiario_contingente_cedula = response.data.colaborador.beneficiario_contingente_cedula != null ? response.data.colaborador.beneficiario_contingente_cedula : "";
            var beneficiario_contingente_porcentaje = response.data.colaborador.beneficiario_contingente_porcentaje != null ? response.data.colaborador.beneficiario_contingente_porcentaje : "";
            var tutor_nombre_menores = response.data.colaborador.tutor_nombre_menores != null ? response.data.colaborador.tutor_nombre_menores : "";
            var gastos_mortuoria_nombre = response.data.colaborador.gastos_mortuoria_nombre != null ? response.data.colaborador.gastos_mortuoria_nombre : "";
            var gastos_mortuoria_cedula = response.data.colaborador.gastos_mortuoria_cedula != null ? response.data.colaborador.gastos_mortuoria_cedula : "";
            var liquidaciones_fecha = response.data.colaborador.liquidaciones_fecha != null ? response.data.colaborador.liquidaciones_fecha : "";
            var liquidaciones_fecha_salida = response.data.colaborador.liquidaciones_fecha_salida != null ? response.data.colaborador.liquidaciones_fecha_salida : "";
            var liquidaciones_fecha_salida_ultima = response.data.colaborador.liquidaciones_fecha_salida_ultima != null ? response.data.colaborador.liquidaciones_fecha_salida_ultima : "";
            var seguro_social = response.data.colaborador.seguro_social != null ? response.data.colaborador.seguro_social : "";

            var seguro_educativo = response.data.colaborador.seguro_educativo != null ? response.data.colaborador.seguro_educativo : "";
            var impuesto_renta = response.data.colaborador.impuesto_renta != null ? response.data.colaborador.impuesto_renta : "";
            var cuota_sindical = response.data.colaborador.cuota_sindical != null ? response.data.colaborador.cuota_sindical : "";
            var descuento_directo = response.data.colaborador.descuento_directo != null ? response.data.colaborador.descuento_directo : "";
            var salario_neto = response.data.colaborador.salario_neto != null ? response.data.colaborador.salario_neto : "";

            //Etiquetas para firma de plantillas + Agregado a ultimo momento para futuras plantillas +
            var firmado_por_nombre = response.data.firmado_por.nombre_completo != null ? response.data.firmado_por.nombre_completo : "";
            var firmado_por_cedula = response.data.firmado_por.firmado_por_cedula != null ? response.data.firmado_por.firmado_por_cedula : "";
            var firmado_por_cargo = response.data.firmado_por.firmado_por_cargo != null ? response.data.firmado_por.firmado_por_cargo : "";

            var plantilla = editor.getData();
            var plantilla = plantilla.replace("[[LOGO]]", response.data.sistema.logo);
            var plantilla = plantilla.replace("[[FECHA_CREACION]]", response.data.sistema.fecha_creacion);
            var plantilla = plantilla.replace("[[FECHA_CREACION]]", response.data.sistema.fecha_creacion);
            var plantilla = plantilla.replace("[[PREFIJO]]", prefijo);
            var plantilla = plantilla.replace("[[PREFIJO]]", prefijo);
            var plantilla = plantilla.replace("[[DESTINATARIO]]", destinatario);
            var plantilla = plantilla.replace("[[NOMBRE_COLABORADOR]]", nombre);
            var plantilla = plantilla.replace("[[NOMBRE_COLABORADOR]]", nombre);
            var plantilla = plantilla.replace("[[NOMBRE_COLABORADOR_FIRMA]]", nombre);
            var plantilla = plantilla.replace("[[COLABORADOR_SEXO]]", colaborador_sexo);
            var plantilla = plantilla.replace("[[COLABORADOR_NACIONALIDAD]]", colaborador_nacionalidad);
            var plantilla = plantilla.replace("[[COLABORADOR_DIRECCION]]", colaborador_direccion);
            var plantilla = plantilla.replace("[[COLABORADOR_EDAD]]", colaborador_edad);
            var plantilla = plantilla.replace("[[COLABORADOR_CICLO]]", colaborador_ciclo);
            var plantilla = plantilla.replace("[[NUMERO_CUENTA]]", numero_cuenta);
            var plantilla = plantilla.replace("[[APELLIDO_COLABORADOR]]", apellido);
            var plantilla = plantilla.replace("[[APELLIDO_COLABORADOR]]", apellido);
            var plantilla = plantilla.replace("[[APELLIDO_COLABORADOR_FIRMA]]", apellido);
            var plantilla = plantilla.replace("[[CEDULA_COLABORADOR]]", cedula);
            var plantilla = plantilla.replace("[[CEDULA_COLABORADOR]]", cedula);
            var plantilla = plantilla.replace("[[COLABORADOR_ESTADO_CIVIL]]", colaborador_estado_civil);
            var plantilla = plantilla.replace("[[SEGURO_SOCIAL_COLABORADOR]]", seguro_social_colaborador);
            var plantilla = plantilla.replace("[[FECHA_INICIO_LABORES]]", fecha_inicio_labores);
            var plantilla = plantilla.replace("[[FECHA_NACIMIENTO]]", fecha_nacimiento);
            var plantilla = plantilla.replace("[[CARGO_COLABORADOR]]", cargo);
            var plantilla = plantilla.replace("[[SALARIO_COLABORADOR]]", salario);
            var plantilla = plantilla.replace("[[COLABORADOR_BOTAS]]", colaborador_botas);
            var plantilla = plantilla.replace("[[SALARIO_COLABORADOR_FIRMA]]", salario);
            var plantilla = plantilla.replace("[[HORAS_SEMANALES]]", horas_semanales);
            var plantilla = plantilla.replace("[[SALARIO_COLABORADOR_HORA]]", salario_hora);
            var plantilla = plantilla.replace("[[TIPO_SALARIO]]", tipo_salario);
            var plantilla = plantilla.replace("[[NOMBRE_EMPRESA]]", response.data.colaborador.empresa);
            var plantilla = plantilla.replace("[[EMPRESA_TOMO]]", response.data.colaborador.empresa_tomo);
            var plantilla = plantilla.replace("[[EMPRESA_FOLIO]]", response.data.colaborador.empresa_folio);
            var plantilla = plantilla.replace("[[EMPRESA_ASIENTO]]", response.data.colaborador.empresa_asiento);
            var plantilla = plantilla.replace("[[NOMBRE_EMPRESA]]", response.data.colaborador.empresa);
            var plantilla = plantilla.replace("[[EMPRESA_DIRECCION]]", response.data.colaborador.empresa_direccion);
            var plantilla = plantilla.replace("[[EMPRESA_DIRECCION]]", response.data.colaborador.empresa_direccion);
            var plantilla = plantilla.replace("[[NOMBRE_COMPLETO_USUARIO]]", nombre_usuario);
            var plantilla = plantilla.replace("[[NOMBRE_COMPLETO_USUARIO_FIRMA]]", nombre_usuario);
            var plantilla = plantilla.replace("[[CEDULA_USUARIO]]", cedula_usuario);
            var plantilla = plantilla.replace("[[TELEFONO_USUARIO]]", telefono);
            var plantilla = plantilla.replace("[[CARGO_USUARIO]]", cargo_usuario);
            var plantilla = plantilla.replace("[[CENTRO_CONTABLE]]", centro_contable);
            var plantilla = plantilla.replace("[[CENTRO_CONTABLE]]", centro_contable);
            var plantilla = plantilla.replace("[[CENTRO_CONTABLE]]", centro_contable);
            var plantilla = plantilla.replace("[[AREA_NEGOCIO]]", area_negocio);
            var plantilla = plantilla.replace("[[AREA_NEGOCIO]]", area_negocio);
            var plantilla = plantilla.replace("[[BENEFICIARIO_PRINCIPAL_NO]]", $.isArray(beneficiario_principal_no) ? beneficiario_principal_no.join("<br>") : beneficiario_principal_no);
            var plantilla = plantilla.replace("[[BENEFICIARIO_PRINCIPAL]]", $.isArray(beneficiario_principal) ? beneficiario_principal.join("<br>") : beneficiario_principal);
            var plantilla = plantilla.replace("[[BENEFICIARIO_PRINCIPAL_PARENTESCO]]", $.isArray(beneficiario_principal_parentesco) ? beneficiario_principal_parentesco.join("<br>") : beneficiario_principal_parentesco);
            var plantilla = plantilla.replace("[[BENEFICIARIO_PRINCIPAL_CEDULA]]", $.isArray(beneficiario_principal_cedula) ? beneficiario_principal_cedula.join("<br>") : beneficiario_principal_cedula);
            var plantilla = plantilla.replace("[[BENEFICIARIO_PRINCIPAL_PORCENTAJE]]", $.isArray(beneficiario_principal_porcentaje) ? beneficiario_principal_porcentaje.join("<br>") : beneficiario_principal_porcentaje);
            var plantilla = plantilla.replace("[[BENEFICIARIO_CONTINGENTE_NO]]", $.isArray(beneficiario_contingente_no) ? beneficiario_contingente_no.join("<br>") : beneficiario_contingente_no);
            var plantilla = plantilla.replace("[[BENEFICIARIO_CONTINGENTE]]", $.isArray(beneficiario_contingente) ? beneficiario_contingente.join("<br>") : beneficiario_contingente);
            var plantilla = plantilla.replace("[[BENEFICIARIO_CONTINGENTE_PARENTESCO]]", $.isArray(beneficiario_contingente_parentesco) ? beneficiario_contingente_parentesco.join("<br>") : beneficiario_contingente_parentesco);
            var plantilla = plantilla.replace("[[BENEFICIARIO_CONTINGENTE_CEDULA]]", $.isArray(beneficiario_contingente_cedula) ? beneficiario_contingente_cedula.join("<br>") : beneficiario_contingente_cedula);
            var plantilla = plantilla.replace("[[BENEFICIARIO_CONTINGENTE_PORCENTAJE]]", $.isArray(beneficiario_contingente_porcentaje) ? beneficiario_contingente_porcentaje.join("<br>") : beneficiario_contingente_porcentaje);
            var plantilla = plantilla.replace("[[TUTOR_NOMBRE_MENORES]]", tutor_nombre_menores);
            var plantilla = plantilla.replace("[[TUTOR_MORTUORIA_NOMBRE]]", gastos_mortuoria_nombre);
            var plantilla = plantilla.replace("[[TUTOR_MORTUORIA_CEDULA]]", gastos_mortuoria_cedula);
            var plantilla = plantilla.replace("[[FECHA_LIQUIDACION]]", $.isArray(liquidaciones_fecha) ? liquidaciones_fecha.join("<br>") : liquidaciones_fecha);
            var plantilla = plantilla.replace("[[FECHA_LIQUIDACION_SALIDA]]", $.isArray(liquidaciones_fecha_salida) ? liquidaciones_fecha_salida.join("<br>") : liquidaciones_fecha_salida);
            var plantilla = plantilla.replace("[[FECHA_LIQUIDACION_SALIDA_ULTIMA]]", $.isArray(liquidaciones_fecha_salida_ultima) ? liquidaciones_fecha_salida_ultima.join("<br>") : liquidaciones_fecha_salida_ultima);
            var plantilla = plantilla.replace("[[SEGURO_SOCIAL]]", $.isArray(seguro_social) ? seguro_social.join("<br>") : seguro_social);
            var plantilla = plantilla.replace("[[SEGURO_EDUCATIVO]]", $.isArray(seguro_educativo) ? seguro_educativo.join("<br>") : seguro_educativo);
            var plantilla = plantilla.replace("[[IMPUESTO_RENTA]]", $.isArray(impuesto_renta) ? impuesto_renta.join("<br>") : impuesto_renta);
            var plantilla = plantilla.replace("[[CUOTA_SINDICAL]]", $.isArray(cuota_sindical) ? cuota_sindical.join("<br>") : cuota_sindical);
            var plantilla = plantilla.replace("[[DESCUENTO_DIRECTO]]", $.isArray(descuento_directo) ? descuento_directo.join("<br>") : descuento_directo);
            var plantilla = plantilla.replace("[[SALARIO_NETO]]", $.isArray(salario_neto) ? salario_neto.join("<br>") : salario_neto);
            var plantilla = plantilla.replace("[[FIRMA_NOMBRE]]", $.isArray(firmado_por_nombre) ? firmado_por_nombre.join("<br>") : firmado_por_nombre);
            var plantilla = plantilla.replace("[[FIRMA_NOMBRE]]", $.isArray(firmado_por_nombre) ? firmado_por_nombre.join("<br>") : firmado_por_nombre);
            var plantilla = plantilla.replace("[[FIRMA_CEDULA]]", $.isArray(firmado_por_cedula) ? firmado_por_cedula.join("<br>") : firmado_por_cedula);
            var plantilla = plantilla.replace("[[FIRMA_CARGO]]", $.isArray(firmado_por_cargo) ? firmado_por_cargo.join("<br>") : firmado_por_cargo);



            return plantilla;
        },
        guardarPlantilla: function (e) {
            e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

            var formulario = $('#plantillaForm');

            if ($(formulario).validate().form() == false) {
                //mostrar mensaje
                toastr.error('Debe seleccionar los campos requeridos para guardar la plantilla.');
            }

            var editor = CKEDITOR.instances['inline-ckeditor-' + this.plantilla_id_selected];

            Vue.http.options.emulateJSON = true;
            Vue.http({
                url: phost() + 'plantillas/ajax-guardar-plantilla',
                method: 'POST',
                headers: {
                    erptkn: tkn,
                },
                data: {
                    erptkn: tkn,
                    id: this.id_plant_detalle,
                    plantilla_id: this.plantilla_id_selected,
                    colaborador_id: this.colaborador_id_selected,
                    destinatario_id: this.destinatario_id_selected,
                    prefijo_id: this.prefijo_id_selected,
                    firmado_por: this.firmado_por_id_selected,
                    estado_id: this.estado_id_selected,
                    plantilla: editor.getData()
                }
            }).then(function (response) {
                // success callback

                //Check Session
                if ($.isEmptyObject(response.data.session) == false) {
                    window.location = phost() + "login?expired";
                }

                //Verificar si el formulario esta siendo usado desde
                //Ver Detalle de Colaborador
                if (window.location.href.match(/(colaboradores)/g)) {

                } else {
                    if (response.data.guardado == true) {
                        window.location = phost() + 'plantillas/listar';
                    }
                }


            }, function (response) {
                // error callback
                //console.log(response, 'DOS');
            });
        }
    }
});
