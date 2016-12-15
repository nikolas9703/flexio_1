var presupuestoTimeLine = Vue.extend({
  template:'#presupuesto-timeline',
  props:['historial','tipo_presupuesto'],
  data:function(){
    return{
      show:true
    };
  },
  methods:{
      icono:function(tipo){
          return tipo ==='creado'?'fa-floppy-o':'fa-pencil-square-o';
      },
      bgcolor:function(tipo){
        return tipo ==='creado'?'blue-bg':'flexio-bg';
      },
      contenido:function(bitacora){
        if(bitacora.tipo === 'creado'){
          return '';
        }
        var contenido = '';
        var antes = $.parseJSON(bitacora.antes);
        var despues = $.parseJSON(bitacora.despues);
        _.forEach(antes,function(value, key){
          if(key === 'montos'){
            contenido += 'Presupuesto de '+accounting.formatMoney(value) + ' a '+accounting.formatMoney(despues.montos) + ' para la cuenta '+bitacora.codigo_cuenta +"<br>";
          }
          if(key === 'porcentaje'){
              contenido += '% de avance de la cuenta '+bitacora.codigo_cuenta +' de '+value+'% a ' +despues.porcentaje+"% <br>";
          }
        });

        return contenido;
      }
  }
 });
