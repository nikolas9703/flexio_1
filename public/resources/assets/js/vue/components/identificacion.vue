<template>
<div class="row">
	<div class="col-md-3">
		<label>Identificación</label>
		<select class="form-control" name="campo[tipo_identificacion]" v-select2="detalle.tipo_identificacion" :config="config.select2">
            <option value="">Seleccione</option>
            <option value="ruc">Jur&iacute;dica</option>
			<option value="ruc_nt">Jur&iacute;dica NT</option>
            <option value="cedula">Natural</option>
			<option value="cedula_nt">Natural NT</option>
            <option value="pasaporte">Otros</option>
        </select>
		<input type="hidden" name="campo[identificacion]" v-model="getIdenticacion">
	</div>
	<div class="form-group col-xs-12 col-sm-9 col-md-9 col-lg-9" v-if="detalle.tipo_identificacion == 'ruc'">
		<!-- div oculto para enseñar campos juridico RUC -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
				<label>Tomo/Rollo <span required="" aria-required="true">*</span></label>
				<input type="text" class="form-control" name="campo[detalle_identificacion][tomo]" aria-required="true" data-rule-required="true" v-model="detalle.detalle_identificacion.tomo">
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
				<label>Folio/Imagen/Documento <span required="" aria-required="true">*</span></label>
				<input type="text" class="form-control" name="campo[detalle_identificacion][folio]" aria-required="true" data-rule-required="true" v-model="detalle.detalle_identificacion.folio">
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
				<label>Asiento/Ficha <span required="" aria-required="true">*</span></label>
				<input type="text" class="form-control" name="campo[detalle_identificacion][asiento]" aria-required="true" data-rule-required="true" v-model="detalle.detalle_identificacion.asiento">
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-3">
				<label>Digito verificador <span required="" aria-required="true">*</span></label>
				<input type="text" class="form-control" name="campo[detalle_identificacion][dv]" aria-required="true" data-rule-required="true" v-model="detalle.detalle_identificacion.dv">
			</div>
		</div>
	</div>
	<div class="form-group col-xs-12 col-sm-9 col-md-9 col-lg-9" v-if="modeCedula">
		<!-- div oculto para enseñar campos  cedula -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
				<label>Provincia <span required="" aria-required="true">*</span></label>
				<select class="form-control" name="campo[detalle_identificacion][provincia]" aria-required="true" data-rule-required="true" v-select2="detalle.detalle_identificacion.provincia" :config="config.select2"
                    :disabled="detalle.detalle_identificacion.letra == 'E' || detalle.detalle_identificacion.letra == 'N' || detalle.detalle_identificacion.letra == 'PE'">
                    <option value="">Seleccione</option>
                    <option value="1">Bocas del Toro (1)</option>
                    <option value="2">Coclé (2)</option>
                    <option value="3">Colón (3)</option>
                    <option value="4">Chiriquí (4)</option>
                    <option value="5">Darién (5)</option>
                    <option value="6">Herrera (6)</option>
                    <option value="7">Los Santos (7)</option>
                    <option value="8">Panama (8)</option>
                    <option value="9">Veraguas (9)</option>
                    <option value="10">Guna Yala (10)</option>
                    <option value="11">Embera Wounann (11)</option>
                </select>
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
				<label>Letras <span required="" aria-required="true">*</span></label>
				<select class="form-control" name="campo[detalle_identificacion][letra]" aria-required="true" data-rule-required="true"
				v-select2="detalle.detalle_identificacion.letra" :config="config.select2" :disabled="disabledLetra">
                    <option value="">Seleccione</option>
                    <option :value="letra" v-for="letra in getLetras" v-html="letra">
                </select>
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
				<label>Tomo <span required="" aria-required="true">*</span></label>
				<input type="text" name="campo[detalle_identificacion][tomo]" aria-required="true" data-rule-required="true" class="form-control" v-model="detalle.detalle_identificacion.tomo">
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
				<label>Asiento <span required="" aria-required="true">*</span></label>
				<input type="text" name="campo[detalle_identificacion][asiento]" aria-required="true" data-rule-required="true" class="form-control" v-model="detalle.detalle_identificacion.asiento">
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-2">
				<label>Digito verificador</label>
				<input type="text" class="form-control" name="campo[detalle_identificacion][dv]" v-model="detalle.detalle_identificacion.dv">
			</div>
		</div>
	</div>
	<div class="form-group col-xs-12 col-sm-9 col-md-9 col-lg-9" v-if="detalle.tipo_identificacion == 'pasaporte'">
		<!-- div oculto para enseñar campos pasaporte -->
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<div class="form-group col-xs-12 col-sm-12 col-md-6 col-lg-4">
				<label>Otros <span required="" aria-required="true">*</span></label>
				<input type="text" name="campo[detalle_identificacion][pasaporte]" aria-required="true" data-rule-required="true" class="form-control" v-model="detalle.detalle_identificacion.pasaporte">
			</div>
		</div>
	</div>
</div>
</template>

<script>
export default {

    props: {

		config: Object,
		detalle: Object

	},

	data: function() {
		return {}
	},

    watch:{

        'detalle.detalle_identificacion.provincia': function(val, oldVal){

            var context = this;
			if(context.config.vista == 'ver')return;
            context.detalle.detalle_identificacion.letra = '';

        }

    },

    computed:{

		disabledLetra: function () {

			var context = this;
			var options = ['ruc_nt', 'cedula_nt'];
			return options.indexOf(context.detalle.tipo_identificacion) != -1 ? true : false;

		},

		modeCedula: function () {

			var context = this;
			var options = ['ruc_nt', 'cedula', 'cedula_nt'];
			return options.indexOf(context.detalle.tipo_identificacion) != -1 ? true : false;

		},

        getLetras: function(){

            var context = this;

            if(context.detalle.tipo_identificacion == 'cedula' && context.detalle.detalle_identificacion.provincia != '')
            {
                return ["0","PI"];
            }

            return ["E","N","PE"];

        },

		getIdenticacion: function(){

			var context = this;
			var aux = context.detalle.detalle_identificacion;
			if(context.modeCedula){
				if (aux.letra == '0' || aux.letra == '') {
					return aux.provincia + "-" + aux.tomo + "-" + aux.asiento + "-" + aux.dv;
		        }else if (aux.letra == 'E' || aux.letra == 'N' || aux.letra == 'PE' || aux.letra == 'PI') {
					if (aux.letra == 'PI')
					{
						return aux.provincia + "-" + aux.letra + "-" + aux.tomo + "-" + aux.asiento + "-" + aux.dv;
					}
					return aux.letra + "-" + aux.tomo + "-" + aux.asiento + "-" + aux.dv;
		        }
			}else if(context.detalle.tipo_identificacion == 'pasaporte'){
				return aux.pasaporte;
			}else if(context.detalle.tipo_identificacion == 'ruc'){
				return aux.tomo + "-" + aux.folio + "-" + aux.asiento + "-" + aux.dv;
			}

			return '';

		}

    }

}
</script>
