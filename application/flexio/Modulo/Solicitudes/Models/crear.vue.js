
Vue.http.options.emulateJSON = true;
var opcionesModal = $('#verCoberturas');

var formularioCrear = new Vue({
  el: ".wrapper-content",
  data:{
    acceso: acceso === 1? true : false,
    disabledOpcionPlanes: true,
    ramo:ramo,
    tipoPoliza:id_tipo_poliza,
    codigoRamo:codigo_ramo,
    catalogoClientes: catalogo_clientes,
    //catalogoPagador: pagador,
    catalogoPagador: [],
    catalogoCantidadPagos: cantidad_pagos,
    catalogoFrecuenciaPagos: frecuencia_pagos,
    catalogoMetodoPago: metodo_pago,
    catalogoSitioPago: sitio_pago,
    catalogoCentroFacturacion: [],
    clienteCentro: '',
    provinciasList: provincias,
    letrasList: letras,
    clientes: [],
    clienteInfo:{},
    planesInfo:[],        
    comisionPlanInfo:'',
    exoneradoImpuestos:'',      
    primaAnual:0,
    impuestoPlan:0,
    impuestoMonto:'0',
    otrosPrima:0,
    descuentosPrima:0,
    totalPrima:0,
    participacionTotal:0,
    aseguradorasListar: aseguradoras,
    planList: planes,
    agentesArray:[1],
    agentesList:agentes,
    porcentajeParticipacion:[],
    disabledOpcionClientes:true,
    disabledCoberturas:true,
    disabledAseguradora:true,
    disabledSubmit:true,
    disabledCentro:true,
    nombrepadre:nombre_padre,
    catalogoEstado: estado_solicitud,   
    ramoscadena:ramoscadena
  },
  methods: {
    getClienteSeleccionado:function(){    
          //polula el segundo select del header
          var self = this;
          var cliente_tipo = $('#formulario_tipo').val();
          this.$http.post({
            url: phost() + 'solicitudes/ajax-get-clientes',
            method:'POST',
            data:{tipo_cliente:cliente_tipo,erptkn: tkn}
          }).then(function(response){
            if(_.has(response.data, 'session')){
             window.location.assign(phost());
           }     
           if(!_.isEmpty(response.data)){            
             self.tablaError="";
             self.$set('clientes',response.data);
             self.$set('tablaError','');
             self.$set('disabledOpcionClientes',false); 
             alert(id_tipo_poliza);      
           }           

         });
          this.getPagadorCampo();
        },
        seleccionarCliente:function(e){
          this.getClienteSeleccionado();      
        },
        clienteInfoSelect:function(){
          this.getClienteSeleccionadoInfo();
          this.getClienteCentroFacturable();
        },
        nombrePlan:function(){
          this.getPlanesInfo();
        },
        coberturasPlan:function(){
          this.getCoberturasPlanInfo();
          this.getComisionesInfo();
          this.getPrimaNeta();
        },
        porcentajeAgentes:function(index){          
          this.getPorcentajeParticipacion(index);   
        },
        clienteDireccion:function(){          
          this.getClienteDireccion();         
        },
        opcionPagador:function(){          
          this.getOpcionPagador();         
        },
        getClienteSeleccionadoInfo:function(){   
          //polula el segundo select del header
          var self = this;
          var cliente_id = $('#cliente_seleccionado').val();
          this.$http.post({
            url: phost() + 'solicitudes/ajax-get-cliente',
            method:'POST',
            data:{cliente_id:cliente_id,erptkn: tkn}
          }).then(function(response){
            if(_.has(response.data, 'session')){
             window.location.assign(phost());
           }     
           if(!_.isEmpty(response.data)){              
             self.$set('clienteInfo',response.data);
             self.$set('exoneradoImpuestos', response.data.exonerado_impuesto);
             self.$set('disabledAseguradora', false);         
           }
         });

        },
        getClienteCentroFacturable:function(){   
          //polula el segundo select del header
          var self = this;
          var cliente_id = $('#cliente_seleccionado').val();
          this.$http.post({
            url: phost() + 'solicitudes/ajax-get-centro-facturable',
            method:'POST',
            data:{cliente_id:cliente_id,erptkn: tkn}
          }).then(function(response){
            if(_.has(response.data, 'session')){
             window.location.assign(phost());
           }     
           if(!_.isEmpty(response.data)){              
             self.$set('catalogoCentroFacturacion',response.data);
             self.$set('disabledCentro', false);              
           }
         });

        },
        getClienteDireccion:function(){   
          //polula el segundo select del header
          var self = this;
          var centro_id = $('#centro_facturacion').val();
          this.$http.post({
            url: phost() + 'solicitudes/ajax-get-direccion',
            method:'POST',
            data:{centro_id:centro_id,erptkn: tkn}
          }).then(function(response){
            if(_.has(response.data, 'session')){
             window.location.assign(phost());
           }     
           if(!_.isEmpty(response.data)){              
             self.$set('clienteCentro',response.data[0].direccion);                    
           }
         });

        },
        getPlanesInfo:function(){   
          //polula el segundo select del header
          var self = this;
          self.$set('planesInfo',"");
          var aseguradora_id = $('#aseguradoras').val();
          this.$http.post({
            url: phost() + 'solicitudes/ajax-get-planes',
            method:'POST',
            data:{aseguradora_id:aseguradora_id,erptkn: tkn}
          }).then(function(response){
            if(_.has(response.data, 'session')){
             window.location.assign(phost());
           }     
           if(!_.isEmpty(response.data)){             
            console.log(response.data);
             self.$set('planesInfo',response.data);            
             self.$set('disabledOpcionPlanes',false);         
           }
         });

        },
        getCoberturasPlanInfo:function(){   
          //polula el segundo select del header
          var self = this;
          var plan_id = $('#planes').val();
          this.$http.post({
            url: phost() + 'solicitudes/ajax-get-coberturas',
            method:'POST',
            data:{plan_id:plan_id,erptkn: tkn}
          }).then(function(response){
            if(_.has(response.data, 'session')){
             window.location.assign(phost());
           }     
           if(!_.isEmpty(response.data)){
             coberturasForm.$set('coberturasInfo',response.data);
             self.$set('disabledCoberturas',false);         
             self.$set('disabledSubmit',false);         
           }
         });

        },
        coberturasModal:function(e){
        //Inicializar opciones del Modal
        $('#verCoberturas').modal({
                backdrop: 'static', //specify static for a backdrop which doesnt close the modal on click.
                show: false
              });
        //Cerrar modal de opciones        
        var pantalla = $('.div_coberturas');
        var botones_coberturas = $('.botones_coberturas');
        
        pantalla.css('display', 'block');
        botones_coberturas.css('display', 'block');
        opcionesModal.find('.modal-tile').empty();        
        opcionesModal.find('.modal-body').empty().append(pantalla);
        opcionesModal.find('.modal-footer').empty().append(botones_coberturas);
        opcionesModal.modal('show');
      },
      getComisionesInfo:function(e){

        var self = this;
        var id_planes = $('#planes').val();
        this.$http.post({
          url: phost() + 'solicitudes/ajax-get-comision',
          method:'POST',
          data:{id_planes:id_planes,erptkn: tkn}
        }).then(function(response){
          if(_.has(response.data, 'session')){
           window.location.assign(phost());
         }   
         if(!_.isEmpty(response.data)){       
          self.$set('comisionPlanInfo', response.data.planes.comisiones.comision); 
          console.log("impuesto=");
          console.log(response.data); 
          self.$set('impuestoPlan', response.data.planes.impuesto.impuesto); 
          self.$set('impuestoMonto', response.data.planes.impuesto.impuesto);
        }
      });
      },
      getPrimaNeta:function(e){

        var self = this;
        var id_planes = $('#planes').val();
        this.$http.post({
          url: phost() + 'solicitudes/ajax_get_prima',
          method:'POST',
          data:{id_planes:id_planes,erptkn: tkn}
        }).then(function(response){
          if(_.has(response.data, 'session')){
           window.location.assign(phost());
         }   
         if(!_.isEmpty(response.data)){       
          self.$set('primaAnual', response.data.planes.prima); 
        }
      });
      },
      getPorcentajeParticipacion:function(index, e){
        var self = this;
        var agente_id = $('#agentes_' + index).val();       
        this.$http.post({
          url: phost() + 'solicitudes/ajax-get-porcentaje',
          method:'POST',
          data:{agente_id:agente_id,erptkn: tkn}
        }).then(function(response){
          if(_.has(response.data, 'session')){
           window.location.assign(phost());
         }     
         if(!_.isEmpty(response.data)){
          $('#agentes_participacion_' + index).val(response.data[0].porcentaje_participacion);
          sumatotal(index);  
         }
       });
      },
      getPagadorCampo:function(){
        //polula el segundo select del header
        var self = this;
        var cliente_tipo = $('#formulario_tipo').val();
        if (cliente_tipo=="seleccione") {
          self.$set('catalogoPagador','');
          $("#divpagadornombre").hide();
          $("#campopagador").removeAttr("data-rule-required");
          $("#selpagadornombre").removeAttr("data-rule-required");
        }else{
          $("#divpagadornombre").hide();
          $("#campopagador").removeAttr("data-rule-required");
          $("#selpagadornombre").removeAttr("data-rule-required");
          this.$http.post({
            url: phost() + 'solicitudes/ajax_get_pagador',
            method:'POST',
            data:{tipo_cliente:cliente_tipo,erptkn: tkn}
          }).then(function(response){
            if(_.has(response.data, 'session')){
             window.location.assign(phost());
           }     
           if(!_.isEmpty(response.data)){          
             self.tablaError="";
             self.$set('catalogoPagador',response.data);
             self.$set('tablaError','');      
           }
         });
        }        
      },
      getOpcionPagador:function(){
        var self = this;
        var pagador_tipo = $('#pagador').val();
        if (pagador_tipo=="cliente" || pagador_tipo=="otro") {
          $("#divpagadornombre").show();
          $("#divpgnombre").show();
          $("#campopagador").attr("data-rule-required",true);
          $("#divselpagador").hide();
          $("#selpagadornombre").removeAttr("data-rule-required");
          var paga = $(".ncli").val();
          if (pagador_tipo=="cliente") { $("#campopagador").val(paga); }else{ $("#campopagador").val(""); }
        }else if(pagador_tipo=="asegurado"){
          $("#divpagadornombre").show();
          $("#divpgnombre").hide();
          $("#campopagador").removeAttr("data-rule-required");
          $("#divselpagador").show();
          $("#selpagadornombre").attr("data-rule-required",true);
        }else{
          $("#divpagadornombre").hide();
          $("#campopagador").removeAttr("data-rule-required");
          $("#selpagadornombre").removeAttr("data-rule-required");
        }
      },
      addAgente: function () {
        this.agentesArray.push({ value: '' });
      },
      removeAgente: function(agt){
        this.agentesArray.$remove(agt);
      }
    },
    computed: {
      
      impuestoMonto:function(){
        //var impuesto = this.exoneradoImpuestos == false ? this.impuestoPlan : 0; 
        var cadena = $("#ramoscadena").val();
        var imp =  7;
        if ((cadena.indexOf("Auto Individual") > -1)||(cadena.indexOf("Auto Colectivo/Flota") > -1)) {imp=6;}   
        if(this.exoneradoImpuestos == null){this.exoneradoImpuestos=false;}     
        var impuesto = this.exoneradoImpuestos == false ? imp : 0;  
        var prima_anual = this.primaAnual;
        var impuesto_monto = prima_anual * impuesto / 100;      
        return parseFloat(impuesto_monto);
      },    
      totalPrima:function(){
        var prima_anual = this.primaAnual;
        var impuesto_monto = this.impuestoMonto;
        var otros = this.otrosPrima;
        var descuentos = this.descuentosPrima;
        var total = parseInt(prima_anual) + parseInt(impuesto_monto) + parseInt(otros) - parseInt(descuentos);      
        return parseFloat(total);
      },
      
    }
    
  });

var coberturasForm = new Vue ({    
  el: ".div_coberturas",
  data:{    
    coberturasInfo:[],
  },
  methods:{

    addCampos: function () {
      this.coberturasInfo.coberturas.push({ value: '' });
    },
    removeCampos: function(find){
      this.coberturasInfo.coberturas.$remove(find);
    },
    addCamposDeduc: function () {
      this.coberturasInfo.deducion.push({ value: '' });
    },
    removeCamposDeduc: function(find){
      this.coberturasInfo.deducion.$remove(find);
    }
  }
});
function sumatotal(contar) {
    
        var suma=0;
        for (var i = 0; i <= contar; i++) {
            suma = parseFloat(suma) + parseFloat($('#agentes_participacion_' + i).val());
        }
        $('#participacionTotal').val(parseFloat(suma));
    }