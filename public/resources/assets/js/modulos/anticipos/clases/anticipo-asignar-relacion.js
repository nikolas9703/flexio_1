// jshint esversion:6
const anticipoRelacion = class AnticipoRelacion {

	constructor(padreVue) {
		this.padrevue = padreVue;
	}

	orden_compra(response) {
		if(response.orden_compra.length > 0 && _.has(response,'orden_compra')){
			var self = this.padrevue;
			var orden = _.head(response.orden_compra);
			self.header_empezable.empezable_type = 'orden_compra';
			self.$store.dispatch('SET_CURRENT', 'orden_compra');
			self.header_empezable.aux_empezable_id = orden.id;
	   }
	}

	subcontrato(response) {
		if(response.subcontrato.length > 0  && _.has(response,'subcontrato')){
			var self = this.padrevue;
			var subcontrato = _.head(response.subcontrato);
			self.header_empezable.empezable_type = 'subcontrato';
			self.$store.dispatch('SET_CURRENT','subcontrato');
			self.header_empezable.aux_empezable_id = subcontrato.id;
		}
	}

	orden_venta(response) {
		if(response.orden_venta.length > 0 && _.has(response,'orden_venta')){
			var self = this.padrevue;
			var ordenv = _.head(response.orden_venta);
			self.header_empezable.empezable_type = 'orden_venta';
			self.$store.dispatch('SET_CURRENT','orden_venta');
			self.header_empezable.aux_empezable_id = ordenv.id;
		}
	}
	contrato(response) {
		if(response.contrato.length > 0 && _.has(response,'contrato')){
			var self = this.padrevue;
			var contrato = _.head(response.contrato);
			self.header_empezable.empezable_type = 'contrato';
			self.$store.dispatch('SET_CURRENT','contrato');
			self.header_empezable.aux_empezable_id = contrato.id;
		}
	}

};

module.exports = {
	AnticipoRelacion: anticipoRelacion
};
