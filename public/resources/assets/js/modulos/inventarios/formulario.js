
//al final del archivo hay un poco de jquery
var crear_items_form = new Vue({

    el: '#crear_items_form_div',

    data: {

        comentario: {

            comentarios: [],
            //se usa un modelo temporal para evitar data que viene en binario
            comentable_type: 'Flexio\\Modulo\\Inventarios\\Models\\Items2',
            comentable_id: '',

          },

        config: {

            vista: window.vista,
            enableWatch: false,
            select2: { width: '100%' },
            datepicker2: { dateFormat: 'dd/mm/yy' },
            inputmask: {

                cantidad: { mask: '9{1,4}', greedy: false},
                descuento: {'mask':'9{1,2}[.9{0,2}]','greedy':false},
                currency: {'mask':'9{1,8}[.9{0,2}]','greedy':false},
                currency2: {'mask':'9{1,8}[.9{0,4}]','greedy':false}

            },
            disableDetalle:false,
            disableGuardar:false,
            modulo:'inventarios'

        },

        catalogos:{

            tipos:window.tipos,
            categorias:window.categorias,
            estados:window.estados,
            unidades:window.unidades,
            precios_venta:window.precios_venta,
            impuestos:window.impuestos,
            precios_alquiler:window.precios_alquiler,
            aux:{}

        },

        detalle:{
            id:'',
            codigo:'',
            nombre:'',
            descripcion:'',
            tipo_id:'',
            categorias:[],
            precios_alquiler:[],
            codigo_barra:'',
            estado:'9',//por aprobar
            item_unidades:[
                {id_unidad:'', base:0, factor_conversion:1}
            ],
            atributos:[
                {id:'', nombre:'', descripcion:''}
            ],
            precios:window.precios_venta,
            item_alquiler:false,
            tarifa_hora:'',
            tarifa_diario:'',
            tarifa_mensual:'',
            tarifa_4_horas:'',
            tarifa_6_dias:'',
            tarifa_15_dias:'',
            tarifa_28_dias:'',
            tarifa_30_dias:'',
            uuid_compra:'',
            uuid_venta:''
        },

        item:{

            item_alquiler:false

        },

        atributosColumns: ['Nombre', 'Descripcion'],

        atributosData: [
            {Nombre: '', Descripcion: ''}
        ]

    },

    components:{
        'detalle': require('./components/detalle.vue'),
        'vista_comments':require('./../../vue/components/comentario.vue')
    },

    methods:{

        setCategoryAll:function(){

            var context = this;
            var category_all = _.find(context.catalogos.categorias, function(category){
                return category.nombre.indexOf("Todos") > -1;
            });

            if(!_.isEmpty(category_all))
            {
                Vue.nextTick(function(){
                    context.detalle.categorias = [];
                    context.detalle.categorias.push(category_all.id);
                });
            }

        },

        addAttribute:function(){
            this.detalle.atributos.push({id:'', nombre:'', descripcion:''});
        },

        removeAttribute:function(atributo){
            this.detalle.atributos.$remove(atributo);
        },

        guardar: function () {
            var context = this;
            var $form = $("#crear_items_form");

            $form.validate({
                //debug:true,
                ignore: '',
                wrapper: '',
                errorPlacement: function (error, element) {
                    var self = $(element);
                    if (self.closest('div').hasClass('input-group') && !self.closest('table').hasClass('itemsTable')) {
                        element.parent().parent().append(error);
                    }else if(self.closest('div').hasClass('form-group') && !self.closest('table').hasClass('itemsTable')){
                        self.closest('div').append(error);
                    }else if(self.closest('table').hasClass('unidadesTable')){
                        $form.find('.tabla_dinamica_error').empty().append('<label class="error">Estos campos son obligatorios (*).</label>');
                    }else{
                        error.insertAfter(error);
                    }
                },
                submitHandler: function (form) {
                    //context.disabledHeader = false;
                    //context.disabledEstado = false;
                    $('input, select').prop('disabled', false);
                    $('form').find(':submit').prop('disabled',true);
                    form.submit();
                }
            });
        },
        setTarifasAlquiler:function(precios_catalogo)
            {
                var context = this;
                var precio_catalogo_conprecios = _.map(precios_catalogo, function(precio) {

                      var detalle_precio_alquiler = _.find(window.item.precios_alquiler, function(alquiler){
                         return alquiler.id_precio == precio.id;
                      });
                        if(typeof detalle_precio_alquiler != "undefined"){
                        return  _.extend({}, precio, {
                          hora:detalle_precio_alquiler.hora,
                          diario:detalle_precio_alquiler.diario,
                          semanal:detalle_precio_alquiler.semanal,
                          mensual:detalle_precio_alquiler.mensual,
                          tarifa_4_horas:detalle_precio_alquiler.tarifa_4_horas,
                          tarifa_6_dias:detalle_precio_alquiler.tarifa_6_dias,
                          tarifa_15_dias:detalle_precio_alquiler.tarifa_15_dias,
                          tarifa_28_dias:detalle_precio_alquiler.tarifa_28_dias,
                          tarifa_30_dias:detalle_precio_alquiler.tarifa_30_dias
                        });
                     }else{
                       return  _.extend({}, precio, {
                         hora:'',
                         diario:'',
                         semanal:'',
                         mensual:'',
                         tarifa_4_horas:'',
                         tarifa_15_dias:'',
                         tarifa_6_dias:'',
                         tarifa_28_dias:'',
                         tarifa_30_dias:'',
                       });
                     }

            });
              context.catalogos.precios_alquiler =  precio_catalogo_conprecios;
            },

    },

    ready: function(){

        var context = this;

        //setear categoria cuyo nombre haga like con 'Todos'
        context.setCategoryAll();


        if(context.config.vista == 'editar'){
            Vue.nextTick(function(){
                context.detalle = JSON.parse(JSON.stringify(window.item));
                context.comentario.comentarios = JSON.parse(JSON.stringify(window.item.comentario_timeline));
                context.comentario.comentable_id = JSON.parse(JSON.stringify(window.item.id));
            });
            console.log("Edicion");
            context.setTarifasAlquiler(window.precios_alquiler);
        }

    }

});

Vue.nextTick(function () {
    crear_items_form.guardar();
});

$('document').ready(function(){

    //Se usa para mantener el menu en el tope
    $('#navbar-example2').affix({
      offset: {
        top: function (){
            return 197;
        }
      }
    });

    // Add smooth scrolling to all links
  $("#navbar-example2 a").on('click', function(event) {


    // Make sure this.hash has a value before overriding default behavior
    if (this.hash !== "") {
      // Prevent default anchor click behavior
      event.preventDefault();

      //marcar el tab clickeado
      $("#navbar-example2 a").closest('li').removeClass("active");
      $(this).closest('li').addClass("active");

      // Store hash
      var hash = this.hash;

      // Using jQuery's animate() method to add smooth page scroll
      // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
      $('html, body').animate({
        scrollTop: $(hash).offset().top - 48
      }, 800, function(){

        // Add hash (#) to URL when done scrolling (default click behavior)
        window.location.hash = hash;
      });
    } // End if
  });

});
