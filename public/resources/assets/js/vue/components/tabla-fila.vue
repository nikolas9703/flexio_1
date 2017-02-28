<template>
    <!-- method requiredvalidation:util.js -->
    <tr :style="(row.facturado || config.vista == 'crear') ? 'background:white;' : 'background:orange;'"
        class="animated" transition="listado">

        <td style='background: white;' v-if="precioCompra() || precioInventario() || precioVenta()">
            <i class="fa" :class="fa_caret" style="font-size: 28px;width: 10px;" @click="changeCaret"></i>
        </td>

        <td class="categoria{{parent_index}} ">
            <select name="items[{{parent_index}}][categoria]" class="categoria" id="categoria{{parent_index}}"
                    data-rule-requiredvalidation="true" aria-required="true" v-select2="row.categoria_id"
                    :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="categoria.id" v-for="categoria in catalogos.categorias">{{categoria.nombre}}</option>
            </select>
        </td>

        <td class="item{{parent_index}} ">
            <input type="hidden" name="items[{{parent_index}}][item_hidden]" class="item_hidden"
                   id="item_hidden{{parent_index}}" v-model="row.item_hidden_id">

            <!-- No quitar la clase pop_over_precio_id del campo que contiene el id del item, se usa para obtener el precio del mismo-->
            <input type="hidden" name="items[{{parent_index}}][item_id]" class="item_hidden pop_over_precio_id"
                   id="item{{parent_index}}" v-model="row.item_id">
            <input type="hidden" id="comentario{{parent_index}}" name="items[{{parent_index}}][comentario]"
                   value="{{row.comentario}}">
            <div class="input-group">
                <typeahead :item_url.sync="item_url" :categoria_id.sync="categoria_id" :parent_index="parent_index"
                           :disabled="config.disableArticulos || disabledArticulo"></typeahead>

                <span class="input-group-btn">
                  <a id="boton{{parent_index}}" type="button" class="btn btn-default" rel=popover
                     v-item-comentario="row.comentario" :i="parent_index" :comentado="row.comentario"> <span
                          class="fa fa-comment"></span></a>
			<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false"
                    v-if="config.modulo == 'pedidos'">
                      <span class="caret"></span>
                  </button>
			<ul class="dropdown-menu " v-if="config.modulo == 'pedidos'">
				<li>
					<quick-add :row.sync="row" :config.sync="config" :catalogos="catalogos"
                               :parent_index="parent_index"></quick-add>
				</li>
			</ul>
			</span>

            </div>
        </td>

        <td class="atributo{{parent_index}} ">
            <input type="text" name="items[{{parent_index}}][atributo_text]" class="form-control atributo"
                   id="atributo_text{{parent_index}}" v-if="row.atributos.length == 0" v-model="row.atributo_text"
                   :disabled="config.disableArticulos || disabledArticulo">
            <select name="items[{{parent_index}}][atributo_id]" class="atributo" id="atributo_id{{parent_index}}"
                    v-if="row.atributos.length > 0" v-select2="row.atributo_id" :config="config.select2"
                    :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="atributo.id" v-for="atributo in row.atributos">{{atributo.nombre}}</option>
            </select>
        </td>

        <td class="cantidad{{parent_index}}  input-group">
        <input type="hidden" name="items[{{parent_index}}][cantidad_usada]" id="cantidad_usada{{parent_index}}" v-model="row.cantidad_usada" >

          <input type="text" name="items[{{parent_index}}][cantidad]" class="form-control cantidad"
                   data-rule-requiredvalidation="true" id="cantidad{{parent_index}}" v-model="row.cantidad | currencyDisplay"
                   :config="config.inputmask.currency2"
                   :disabled="editarCantidadEnabled" @change="restriccionCantidad()">
            <span class="input-group-addon cantidad_info"
                  style="background-color:#27AAE1;color:white;border:1px solid #27AAE1" v-pop_over_cantidad=""><i
                    class="fa fa-info"></i></span>
        </td>

        <td class="unidad{{parent_index}} ">
            <input type="hidden" name="items[{{parent_index}}][unidad_hidden]" class="unidad_hidden"
                   id="unidad_hidden{{parent_index}}" v-model="row.unidad_hidden_id">
            <select name="items[{{parent_index}}][unidad]" class="unidad" id="unidad{{parent_index}}"
                    data-rule-requiredvalidation="true" aria-required="true" v-select2="row.unidad_id"
                    :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
                <option :value="unidad.id" v-for="unidad in row.unidades">{{unidad.nombre}}</option>
            </select>

        </td>

        <td class="precio_unidad{{parent_index}} " v-if="precioCompra() || precioInventario() || precioVenta()">
            <div class="input-group" v-if="precioCompra() || precioInventario()">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input type="text" name="items[{{parent_index}}][precio_unidad]"
                       class="form-control precio_unidad valid" data-rule-requiredvalidation="true" aria-required="true"
                       id="precio_unidad{{parent_index}}" agrupador="items" aria-required="true" aria-invalid="false"
                       v-model="row.precio_unidad" v-inputmask="row.precio_unidad" :config="config.inputmask.currency2"
                       :disabled="editarPrecioUnidadAdicional || editarPrecioMayor">
                <span class="input-group-addon precio_unidad_info"
                      style="background-color:#27AAE1;color:white;border:1px solid #27AAE1" v-pop_over_precio=""><i
                        class="fa fa-info"></i></span>
            </div>
            <div class="input-group" v-if="precioVenta()">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input type="text" name="precio_unidad_txt" id="precio_unidad_txt[{{parent_index}}]" class="form-control precio_unidad valid"
                       data-rule-requiredvalidation="true" aria-required="true" v-model="row.precio_unidad"
                       v-show="config.editarPrecio" :disabled="editarPrecioUnidadAdicional" id="precio_unidad_txt" v-moneda5d="row.precio_unidad">
                <input type="text" name="items[{{parent_index}}][precio_unidad]" id="items[{{parent_index}}][precio_unidad]"
                       class="form-control precio_unidad valid" data-rule-requiredvalidation="true" aria-required="true"
                       :disabled="editarPrecioUnidadAdicional" v-model="row.precio_unidad" v-show="!config.editarPrecio" v-moneda5d="row.precio_unidad">
            </div>
        </td>

        <td class="precio_total{{parent_index}} " v-if="precioCompra() || precioInventario() || precioVenta()">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-dollar"></i></span>
                <input type="text" name="items[{{parent_index}}][precio_total]" value="{{getSubtotal | currency ''}}"
                       class="form-control precio_total" disabled="true" id="precio_total{{parent_index}}"
                       agrupador="items">
                <input type="hidden" name="items[{{parent_index}}][impuesto_total]" value="{{getImpuestoTotal}}"
                       class="form-control impuesto_total" id="impuesto_total{{parent_index}}">
                <input type="hidden" name="items[{{parent_index}}][descuento_total]" value="{{getDescuentoTotal}}"
                       class="form-control descuento_total" id="descuento_total{{parent_index}}">
                <input type="hidden" name="items[{{parent_index}}][retenido_total]" value="{{getRetenidoTotal}}"
                       class="form-control retenido_total" id="retenido_total{{parent_index}}">
            </div>
        </td>

		<td class="cuenta{{parent_index}}" v-if="config.modulo != 'traslados' && !( precioCompra() || precioInventario() || precioVenta() )">
        <select name="items[{{parent_index}}][cuenta]" class="cuenta" id="cuenta{{parent_index}}"
                    data-rule-requiredvalidation="true" aria-required="true"   v-select2ajax="row.cuenta_id"
                    :config="select2cuenta" :disabled="config.disableArticulos || disabledArticulo">
                <option value="">Seleccione</option>
            </select>
        </td>

        <td style="background: white;">
            <button type="button" class="btn btn-default btn-block eliminarBtn" agrupador="items"
                    label="<i class=&quot;fa fa-trash&quot;></i>" @click="removeRow(row)"
                    :disabled="config.disableArticulos || disabledArticulo || disabledByUse"><i class="fa fa-trash"></i></button>
            <input type="hidden" name="items[{{parent_index}}][id_pedido_item]" value="{{row.id}}" class="form-control"
                   id="id_pedido_item">
        </td>

    </tr>

    <tr v-show="fa_caret == 'fa-caret-down'" v-if="precioCompra() || precioInventario() || precioVenta()">
        <td></td>
        <td colspan="7">
            <table style="width: 100%;background: #A2C0DA;">

                <td class="impuesto{{parent_index}}" width="33%" style="padding: 10px;">
                    <label>Impuesto</label>
                    <select name="items[{{parent_index}}][impuesto]" class="impuesto" id="impuesto{{parent_index}}"
                            data-rule-requiredvalidation="true" aria-required="true" v-select2="row.impuesto_id"
                            :config="config.select2"
                            :disabled="config.disableArticulos || disabledArticulo || precioInventario()">
                        <option value="">Seleccione</option>
                        <option :value="impuesto.id" v-for="impuesto in catalogos.impuestos">{{impuesto.nombre}}
                        </option>
                    </select>
                </td>

                <td class="descuento{{parent_index}}" width="33%" style="padding: 10px;">
                    <label>Descuento</label>
                    <div class="input-group" style="width: 100%;">
                        <input type="input-right-addon" name="items[{{parent_index}}][descuento]"
                               class="form-control descuento" id="descuento{{parent_index}}" agrupador="items"
                               v-model="row.descuento" v-inputmask="row.descuento" :config="config.inputmask.descuento"
                               :disabled="config.disableArticulos || disabledArticulo || precioInventario()">
                        <span class="input-group-addon">%</span>
                    </div>
                </td>

                <td class="cuenta{{parent_index}}" width="33%" style="padding: 10px;" v-if="cuentaAjax()">
                    <label>Cuenta</label>
                    <select name="items[{{parent_index}}][cuenta]" class="cuenta" id="cuenta{{parent_index}}"
                            data-rule-requiredvalidation="true" aria-required="true" v-select2ajax="row.cuenta_id"
                            :config="select2cuenta" :disabled="config.disableArticulos || disabledArticulo">
                        <option value="">Seleccione</option>
                    </select>
                </td>

                <td class="cuenta{{parent_index}}" width="33%" style="padding: 10px;" v-if="!cuentaAjax()">
                    <label>Cuenta</label>
                    <select name="items[{{parent_index}}][cuenta]" class="cuenta" id="cuenta{{parent_index}}"
                            data-rule-requiredvalidation="true" aria-required="true" v-select2="row.cuenta_id"
                            :config="config.select2" :disabled="config.disableArticulos || disabledArticulo">
                        <option value="">Seleccione</option>
                        <option :value="cuenta.id" v-for="cuenta in getCuentas">{{cuenta.codigo +' '+ cuenta.nombre}}
                        </option>
                    </select>
                </td>

            </table>
        </td>
        <td></td>
    </tr>

    <tr v-show="fa_caret == 'fa-caret-down'"
        v-if="precioInventario() && (row.tipo_id == 5 || row.tipo_id == 8) && row.cantidad > 0">
        <td></td>
        <td colspan="7">
            <div style="width: 100%;background: #A2C0DA;padding:5px;">

                <div style="margin-top:10px !important;margin-bottom:10px !important"
                     class="form-group col-xs-12 col-sm-6 col-md-6 col-lg-3" v-for="serial in row.seriales">
                    <input type="text" class="form-control" name="items[{{parent_index}}][seriales][{{$index}}]"
                           aria-required="true" data-rule-required="true" v-model="serial.nombre"
                           :disabled="config.disableArticulos">
                </div>

                <div style="clear:both"></div>

            </div>
        </td>
        <td></td>
    </tr>
</template>

<script>
export default {

	props: {

		config: Object,
		detalle: Object,
		catalogos: Object,
		parent_index: Number,
		row: Object,
		empezable: Object

	},

	data: function () {
        var context = this;

		return {
            select2cuenta:{
                ajax:{
                    url: function(params){
                        var centro_contable_id = context.getCentroContable();
                        var aplica_centro = (context.row.id === '' || context.config.vista == 'crear') ? 1 : 0;
                        console.log(context.row.id);

                        if(centro_contable_id == -1){
                            toastr['error']('Por favor, indique el centro contable antes de seleccionar la cuenta');
                        }else if(context.row.item_id == ''){
                            toastr['error']('Por favor, indique el nombre del item antes de seleccionar la cuenta');
                            centro_contable_id = -1;
                        }

                        return phost() + 'contabilidad/ajax_get_cuentas?centro_contable_id='+ centro_contable_id +'&cuentas='+ context.row.cuentas + '&aplica_centro=' + aplica_centro;
                    },
                    data: function (params) {
                        return {
                            q: params.term
                        }
                    }
                }
            },
			disabledArticulo: false, //se usa para inhabilitar miestras se espera respuesta del ajax
			fa_caret: 'fa-caret-right',
			enableWatch: true,
			item_url: 'inventarios/ajax_get_typehead_items',
			categoria_id: '',
			seriales: [],
            editarPrecioUnidadIAd: window.editar_precio,
			cloneRow: Object.assign({},this.row)

		};

	},

	ready: function () {

		var context = this;
		var ejm = context.precioVenta();
		Vue.nextTick(function () {
			context.item_url = 'inventarios/ajax_get_typehead_items/?ventas='.ejm;
		});
	},
	components: {
		'typeahead': require('./typeahead.vue'),
		'quick-add': require('./utils/quick-add.vue')
	},
	transitions: {
		'listado': require('./../transitions/fade.vue')
	},
	watch: {
        'row.precio_unidad': function(val, oldVal) {
            var context = this;
            Vue.nextTick(function () {
                context.getPrecioUnidad;
                context.getSubtotal;
                context.row.precio_total = context.getSubtotal;
            });

        },

		'row.categoria_id': function (val, oldVal) {

			this.getItems();
			this.categoria_id = val; //se usa para el filtro de typeahead

		},

		'row.item_id': function (val, oldVal) {

			var context = this;
			var item = _.find(context.row.items, function (item) {
				return item.id == context.row.item_id;
			});

			this.getUnidades();
			if (this.config.enableWatch && this.enableWatch) {

				context.row.cantidad = 1;
				context.row.descripcion = '';
				context.row.unidad_id = '';
				context.row.facturado = false;

				if (!_.isEmpty(item)) {

					context.setItem(item);

				}

			}

		},

		'row.cantidad': function (val, oldVal) {

			var context = this;
            /** Validacion de cantidades cuando se selecciona un tipo de operacion en empezar desde para orden de compras o pedidos */
			this.restriccionCantidadEmpezable();
            /** Validacion en editar pedidos, la cantidad no permita que sea menor a la cantidad ya usada */
           // this.restriccionCantidad();

			//falta verificar si es serializado
			if (context.precioInventario()) {

				var i = 0;
				context.seriales = (context.seriales.length < context.row.seriales.length) ? JSON.parse(JSON.stringify(context.row.seriales)) : context.seriales;
				context.row.seriales = [];

				for (i = 0; i < val; i++) {
					context.row.seriales.push({
						nombre: typeof context.seriales[i] !== 'undefined' ? context.seriales[i].nombre : ''
					});
				}

			}

		},
		'detalle.item_precio_id': function (val, oldVal) {

			var context = this;

			_.forEach(this.detalle.articulos, function (row) {

				var precio = _.find(precios, function (precio) {
					return precio.id == context.detalle.item_precio_id;
				});

				if (!_.isEmpty(precio)) {

					var unidad = _.find(context.row.unidades, function (unidad) {
						return unidad.id == context.row.unidad_id;
					});
					//look for index
					var i = 0;
					var index = 0;
					for (var i = 0; i < row.precios.length; i++) {
						if (row.precios[i].id == precio.id) {
							index = i;
						}
					}

					var pr_item = 0;
					if (typeof (row.items[0]) != 'undefined') {
						pr_item = _.find(row.items[0].precios, function (precio) {
							return precio.id == context.detalle.item_precio_id;
						});
					}
					if (!_.isEmpty(unidad) && ((parseFloat(pr_item.pivot.precio) || 0) * (parseFloat(unidad.pivot.factor_conversion) || 0)) != 0) {
						row.precio_unidad = (parseFloat(pr_item.pivot.precio) || 0) * (parseFloat(unidad.pivot.factor_conversion) || 0);
					}

					Vue.nextTick(function () {
						if (typeof (precio.pivot) != 'undefined') {
							row.precio_unidad = parseFloat(pr_item.pivot.precio);
						}
					});

				}

			});

		}
	},

	computed: {


  disabledByUse: function() {

         if(this.config.modulo=='pedidos'&& window.vista=='editar'){
                 if(this.row.cantidad_usada > 0 ){
                    return true;
                }
                else{
                    return false;
                }
          }

 },


         editarCantidadEnabled: function() {
                 //case #1010 solo para pedidos, editar
                var disablebycant = false;
                if(this.config.modulo=='pedidos'&& window.vista=='editar'){

                    var ordenes = window.ordenesPedido;
                    var context = this;
                    var _item_id = typeof(this.row.item_id) != 'undefined' ? this.row.item_id : '';
                    var _cantidad = typeof(this.row.cantidad) != 'undefined' ? this.row.cantidad : '';
                    var _cantidad_count= 0;


                    _.forEach(ordenes, function(orden) {

                             //ir por las ordenes y contar las cantidades de items

                            var items = _.filter(orden.lines_items, function(item) {
                                return item.item_id == _item_id;
                            });
                            if (typeof(items[0])!='undefined'){
                                _cantidad_count = _cantidad_count + parseFloat(items[0].cantidad);
                            }

                    });

                    if(_cantidad_count == _cantidad){
                        disablebycant = true;
                    } else {
                        disablebycant = false;
                    }

                }
                if(this.config.disabledArticulo || this.disabledArticulo || this.editarPrecioMayor || disablebycant) {
                    return true;
                } else {
                    return false;
                }
        },



        editarPrecioUnidadAdicional: function() {
            if (this.precioCompra() || this.precioInventario()) {
                if (this.editarPrecioUnidadIAd == 0 || this.config.disableArticulos || this.disabledArticulo || this.precioInventario()){
                    return true;
                } else {
                    return false;
                }
            }
            if (this.precioVenta()){
                if (this.editarPrecio==0 || this.editarPrecioUnidadIAd == 0){
                    return true;
                } else {
                    return false;
                }
            }
        },

		editarPrecioMayor: function() {
            //Si la factura ya esta en estado por pagar y el usuario no es super admin
            //no se puede editar el precio. Si esta funcion retorna true inhabilita
            //de lo contrario deshabilita
			var estado = _.find(this.catalogos.estados, 'id', this.detalle.estado);
			if(this.precioCompra() && this.config.superUsuario == 0 && estado.etiqueta == 'por_pagar'){
				return false;
			}else{
				return false;
			}
		},

        getCuentas:function(){

              var context = this;
              var aux = [];

              if(context.row.cuentas.length > 0 && context.row.cuentas != 'null'){
                  aux = _.filter(context.catalogos.cuentas, function(cuenta){
                      if((context.precioCompra() || context.config.modulo == 'pedidos') && context.config.vista == 'crear')
                      {
                        return context.row.cuentas.indexOf("costo:"+ cuenta.id +"\"") > -1
                      }
                      else if (context.precioVenta() && context.row.cuentas.indexOf("ingreso:") > -1)
                      {
                        return context.row.cuentas.indexOf("ingreso:"+ cuenta.id +"\"") > -1
                      }

                      return context.catalogos.cuentas;
                  });
              }


              return aux.length > 0 ? aux : context.catalogos.cuentas;

        },

		getPrecioUnidad: function () {

			var context = this;
			if (context.precioCompra() || context.precioInventario() || context.row.id != '' || (typeof context.config.editarPrecio !== "undefined" && context.config.editarPrecio)) {

				return context.row.precio_unidad;

			}

                        if (context.precioVenta() || context.row.id != '' || (typeof context.config.editarPrecio !== "undefined" && context.config.editarPrecio)) {
                                return context.row.precio_unidad;
                        }

			var precio = _.find(context.row.precios, function (precio) {
				return precio.id == context.detalle.item_precio_id;
			});

			if (!_.isEmpty(precio)) {

				var unidad = _.find(context.row.unidades, function (unidad) {
					return unidad.id == context.row.unidad_id;
				});
				if (!_.isEmpty(unidad)) {

					return (parseFloat(precio.pivot.precio) || 0) * (parseFloat(unidad.pivot.factor_conversion) || 0);

				}
				return parseFloat(precio.pivot.precio) || 0;

			}

			return 0;

		},

		getSubtotal: function () {

			var context = this;
			return context.row.cantidad * context.getPrecioUnidad;

		},

		getImpuestoTotal: function () {

			var context = this;
			var impuesto = _.find(context.catalogos.impuestos, function (impuesto) {
				return impuesto.id == context.row.impuesto_id;
			});
			var aux = (!_.isEmpty(impuesto)) ? context.round(impuesto.impuesto) : 0;

			let total_impuesto =  (context.round(context.getSubtotal)  -  context.round(context.getDescuentoTotal)) * (aux / 100);

			if(this.config.modulo =="facturas_compras"){
				Vue.set(this.row,'total_impuesto',total_impuesto);
			}

			return total_impuesto;
		},

		getRetenidoTotal: function () {

			var context = this;
			var impuesto = _.find(context.catalogos.impuestos, function (impuesto) {
				return impuesto.id == context.row.impuesto_id;
			});

			var aux = (!_.isEmpty(impuesto)) ? context.round(impuesto.porcentaje_retenido) : 0;

            let total_impuesto = context.detalle.articulos.length > 1 ? context.round(context.getImpuestoTotal) : context.round2(context.getImpuestoTotal);
            let retenido = total_impuesto * (aux / 100);
			if(this.config.modulo =="facturas_compras"){
				Vue.set(this.row, 'total_retenido', retenido);
			}


			return retenido;
		},

		getDescuentoTotal: function () {

			var context = this;
			return (context.getSubtotal * context.row.descuento) / 100;

		}

	},
	events: {

		//se ejecuta cuando se selecciona un item de la lista desplegable
		'update-item': function (item, exonerado_impuesto) {

			var context = this;
			context.row.items = [item];
			context.enableWatch = false;

			Vue.nextTick(function () {
				var selected_categoria = _.head(item.categoria);
				context.row.categoria_id = selected_categoria.id;
				context.row.item_id = item.id;
				context.row.item_hidden_id = item.id;
				context.row.cuenta_id = item.cuenta_id;
				context.row.cuentas = item.cuentas; //string con json para el filtro de cuentas
				//#case 1499 verificar si tiene solo una cuenta para seleccionarla por defualt
				context.row.tipo_id = item.tipo_id;
				context.row.atributos = item.atributos;
				context.getUnidades();
				context.row.unidades = item.unidades;
				Vue.nextTick(function () {

					if(typeof context.getCuentas !== 'undefined' && context.getCuentas.length == 1)
					{
						context.row.cuenta_id = context.getCuentas[0].id;
					}

					context.row.unidad_id = item.unidad_id;
					context.row.unidad_hidden_id = item.unidad_id;
					context.row.precio_unidad = item.precio_unidad;
					//considerate do a method for this logic
					var impuesto_exonerado = context.getImpuestoExonerado();
					if(context.isExonerado() && !_.isEmpty(impuesto_exonerado))
					{
						//Tax exeption.
						context.row.impuesto_id = impuesto_exonerado.id;
					}
					else
					{
						//'No tax except.
						context.row.impuesto_id = item.impuesto_id;
					}


					if (context.precioVenta()) {
						context.row.precios = item.precios;
						Vue.nextTick(function () {
							context.row.precio_unidad = context.getPrecioUnidadMethod();
						});
					}

					setTimeout(function () {
						context.enableWatch = true;
					}, 400);

				});

			});

		}
	},
	methods: {

        getCentroContable:function(){
            var context = this;
            var centro_contable_id = typeof context.detalle.centro_contable_id !== 'undefined' ? context.detalle.centro_contable_id : '';
            centro_contable_id = typeof context.detalle.uuid_centro !== 'undefined' ? context.detalle.uuid_centro : centro_contable_id;

            if(centro_contable_id && centro_contable_id !== ''){
                return centro_contable_id;
            }

            return -1;
        },

        round:function(v){
            return parseFloat(roundNumber(v,4));
        },

        round2:function(v){
            return parseFloat(roundNumber(v,2));
        },

		isExonerado: function(){

			var context = this;
			if(context.precioVenta && typeof context.catalogos.clientes !== 'undefined')
			{
				var cliente = _.find(context.catalogos.clientes, function(cliente){
					//no apply to potencials_clients
					return cliente.id == context.detalle.cliente_id && cliente.tipo == 'cliente';
				});
				if(!_.isEmpty(cliente) && cliente.exonerado_impuesto.length)
				{
					return true;
				}
			}
			return false;
		},

		getImpuestoExonerado: function(){

			var context = this;
			return _.find(context.catalogos.impuestos, function (impuesto) {
				return impuesto.impuesto == 0.00;
			});

		},

		getPrecioUnidadMethod: function () {

			var context = this;
			var precio = _.find(context.row.precios, function (precio) {
				return precio.id == context.detalle.item_precio_id;
			});

			if (!_.isEmpty(precio)) {

				var unidad = _.find(context.row.unidades, function (unidad) {
					return unidad.id == context.row.unidad_id;
				});
				if (!_.isEmpty(unidad)) {

					return (parseFloat(precio.pivot.precio) || 0) * (parseFloat(unidad.pivot.factor_conversion) || 0);

				}
				return parseFloat(precio.pivot.precio) || 0;

			}

			return 0;

		},

		precioInventario: function () {

			var context = this;
			var modulos_compras = ['ajustes'];
			return modulos_compras.indexOf(context.config.modulo) != -1 ? true : false;

		},

        cuentaAjax: function(){
            var context = this;
			var modulos_compras = ['ordenes', 'facturas_compras'];
			return modulos_compras.indexOf(context.config.modulo) != -1 ? true : false;
        },

		precioCompra: function () {

			var context = this;
			var modulos_compras = ['ordenes', 'facturas_compras'];
			return modulos_compras.indexOf(context.config.modulo) != -1 ? true : false;

		},

		precioVenta: function () {

			var context = this;
			var modulos_ventas = ['cotizaciones', 'ordenes_ventas', 'ordenes_alquiler', 'ordenes_trabajo'];
			return modulos_ventas.indexOf(context.config.modulo) != -1 ? true : false;

		},

		changeCaret: function () {

			if (this.fa_caret === 'fa-caret-right') {
				this.fa_caret = 'fa-caret-down';
			} else {
				this.fa_caret = 'fa-caret-right';
			}

		},

		getUnidades: function () {

			var context = this;
			var item = _.find(context.row.items, function (item) {

				return item.id == context.row.item_id;

			});

			if (_.isEmpty(item)) {

				context.row.unidades = [];

			} else {

				context.row.unidades = JSON.parse(JSON.stringify(item.unidades)); //inmutable

			}

		},

		getItems: function () {

			var context = this;
			if (!context.enableWatch) return;
			var categoria = _.find(context.catalogos.categorias, function (categoria) {

				return categoria.id == context.row.categoria_id;

			});

			if (!_.isEmpty(categoria)) {

				if (context.disabledArticulo == false) {
					context.getItemsAjax(categoria);
				}
				return;
			}
			context.row.items = [];

		},
		siEsAlquiler: function () {
			//-------------------------
			// Modificado por: @josecoder
			// No aplicar setUnidadId en alquiler
			//-------------------------
			let context = this;
			if(typeof context.$root.config != 'undefined' && typeof context.$root.config.modulo != 'undefined' && context.$root.config.modulo.match(/alquiler/gi)){
			return true;
			}
			return false;
		},
		getItemsAjax: function (categoria) {
			var context = this;
			var datos = $.extend({
				erptkn: tkn
			}, {
				id: categoria.id
			}, {
				'ventas': context.precioVenta() ? 1 : 0,
				item_id: context.row.item_id
			});
			context.disabledArticulo = true;

			this.$http.post({
					url: window.phost() + "inventarios/ajax-get-items-categoria",
					method: 'POST',
					data: datos
				}).then(function (response) {

					if (_.has(response.data, 'session')) {
						window.location.assign(window.phost());
						return;
					}
					if (!_.isEmpty(response.data)) {

						categoria.items = JSON.parse(JSON.stringify(response.data.items)); //inmutable
						context.row.items = JSON.parse(JSON.stringify(response.data.items)); //inmutable
						context.disabledArticulo = false;
						if (context.config.modulo != 'ordenes') {
							$('#proveedor_id').attr('disabled', 'disabled');
						}
						context.config.disableEmpezarDesde = false;
						context.$broadcast('fill-typeahead', response.data.items);

						// context.$broadcast('fill-typeahead',response.data.items);
						context.enableWatch = false;
						context.setItemId();

						Vue.nextTick(function () {
							context.enableWatch = true;
							if(context.siEsAlquiler()){
								return false;
							}
							context.setUnidadId();
						});
					}

				})
				/*.catch(function(err){
				                  window.toastr['error'](err.statusText + ' ('+err.status+') ');
				              })*/
			;

		},

		setItemId: function () {

			var context = this;
			context.row.item_id = context.row.item_hidden_id;
			if (_.isInteger(context.row.item_hidden_id)) {
				var item = _.find(context.row.items, ['id', context.row.item_id]);
				if (!_.isUndefined(item)) {
					context.row.unidades = item.unidades;
					context.$broadcast('set-typeahead-nombre', item.nombre);
				}
			}
		},

		setUnidadId: function () {
			var context = this;
			context.row.unidad_id = context.row.unidad_hidden_id;
		},

		setItem: function (item) {

			var context = this;
			context.row = $.extend(context.row, JSON.parse(JSON.stringify(item)));
			context.row.id = '';

		},

		removeRow: function (row) {

			var context = this;
			context.detalle.articulos.$remove(row);
			Vue.nextTick(function () {
				if (context.detalle.articulos.length === 0) {
					context.$dispatch('eAddRow');
				}
			});

		},
		restriccionCantidadEmpezable(){
		if (_.isUndefined(this.empezable)) {
			 return false
		}
			if(this.empezable.type == "orden_compra" || this.empezable.type == "pedido"){

       if(this.row.item_id == this.cloneRow.item_id && parseFloat(this.row.cantidad) > parseFloat(this.cloneRow.cantidad) + parseFloat(this.cloneRow.cantidad_disponible)){
				   let type = this.empezable.types.find(tipo => (tipo.id ==  this.empezable.type));

				   toastr.error("La cantidad del item no puede ser mayor la operacion de '"+ type.nombre +"' ", "Mensaje");
				   this.$nextTick(function(){
					   this.row.cantidad = this.cloneRow.cantidad;
				   });
			   }
			}

		},
        restriccionCantidad:function(){
            var context = this;
            if(( context.config.modulo == 'pedidos') && context.config.vista == 'editar'){
                var ordenes = window.ordenesPedido;
                var _item_id = typeof(this.row.item_id) != 'undefined' ? this.row.item_id : '';
                var _cantidad_count= 0;

                _.forEach(ordenes, function(orden) {
                    var items = _.filter(orden.lines_items, function(item) {
                        return item.item_id == _item_id;
                    });
                    if (typeof(items[0])!='undefined'){
                        _cantidad_count = _cantidad_count + parseFloat(items[0].cantidad);
                    }
                });

                if(parseFloat(this.row.cantidad) < parseFloat(_cantidad_count)){
                    toastr.error("La cantidad del item no puede ser menor a la ya usada en las ordenes, que es "+ _cantidad_count +". ", "Mensaje");
                }
            }

        }

	},
	directives: {
		'item-comentario': require('./../directives/item-comentario.vue'),
                'moneda':require('./../directives/inputmask-currency.vue'),
                'moneda5d' :require ('./../directives/inputmask-currency5d.vue'),
	}

}

</script>
