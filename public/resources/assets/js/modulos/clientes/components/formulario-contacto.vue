<template>

    <div id="vistaFormularioContacto" class="animated fadeInRight">

        <div class="tab-content">

            <div id="contacto-9" class="tab-pane active">

                <div class="ibox">

                    <div class="ibox-title border-bottom">
                        <h5>Datos del Contacto</h5>
                        <div class="ibox-tools">
	                           <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content m-b-sm" style="display: block; border:0px">
                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Nombre<spanrequired>*</span> ">
                                <label>Nombre <span required="">*</span></label>
                                <input type="text" class="form-control" v-model="detalle.nombre" data-rule-required="true">
	                        </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Correo <span required="">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">@</span>
                                    <input type="text" class="form-control" v-model="detalle.correo" data-rule-email="true" data-rule-required="true">
                                </div>
	                        </div>
	                        <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Celular </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-mobile"></i></span>
                                    <input type="text" class="form-control" v-model="detalle.celular">
                                </div>
	                        </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 ">
                                <label>Teléfono </label>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                                    <input type="text" class="form-control" v-model="detalle.telefono">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3 Cargo ">
                                <label>Cargo </label>
                                <input type="text" class="form-control" v-model="detalle.cargo">
                            </div>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 Direccion ">
                                <label>Direccion </label>
                                <input type="text" class="form-control" v-model="detalle.direccion">
	                        </div>
                        </div>

                        <div class="row">
                            <!-- ID Component Start -->
                            <identificacion :config.sync="config" :detalle.sync="detalle"></identificacion>
                            <!-- ID Component End -->
                        </div>

                        <div class="row">
                            <br>
                            <div class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-6 Comentarios ">
                                <label>Comentarios </label>
                                <input type="text" class="form-control" v-model="detalle.comentario">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xs-0 col-sm-6 col-md-8 col-lg-8">&nbsp;</div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                <button type="button" class="btn btn-default btn-block" @click.stop="hideAgregarContacto()">Cancelar</button>
                            </div>
                            <div class="form-group col-xs-12 col-sm-3 col-md-2 col-lg-2">
                                <button type="button" class="btn btn-primary btn-block" @click.stop="guardarContacto()">Guardar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
export default {

	props: {

		config: Object,
		detalle: Object,
		catalogos: Object

	},

	data: function () {

		return {};

	},

    methods:{

        hideAgregarContacto: function(){
            this.config.showFormContacto = false;
        },

        guardarContacto: function(){

            var context = this;
            $.ajax({
                url: phost() + "contactos/ajax-guardar-contacto",
                type: "POST",
                data: {
                    erptkn: tkn,
                    campos: context.detalle
                },
                dataType: "json",
                success: function (response) {
                    if (!_.isEmpty(response)) {
                        if(response.clase == 'alert-success'){
                          toastr.success(response.contenido);
                      }else if(response.clase == 'alert-error'){
                          toastr.error(response.contenido);
                      }
                    }
                    context.hideAgregarContacto();
                    $('body').find("#contactosGrid").trigger('reloadGrid');
                }
            });

        }

    },

    components:{

        'identificacion': require('./../../../vue/components/identificacion.vue')

    }

}
</script>
