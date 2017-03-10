
//Directiva para campos Chosen
Vue.directive('chosen', {
    twoWay: true,
    bind: function () {
    	var scope = this;
        var formulario = $(scope.$el);

        setTimeout(function() {
        	$(scope.el).chosen({
                width: '100%',
                inherit_select_classes: true
            }).on('chosen:ready', function (e, params) {
                //Ejecutar trigger change
            	$(scope.el).trigger('chosen:updated');
            }).trigger('chosen:ready').change(function (e) {
                scope.set(scope.el.value);
            })
        }.bind(this), 500);
    },
    update: function (nv, ov) {
    	var scope = this;
        var formulario = $(scope.$el);
        // note that we have to notify chosen about update
        setTimeout(function(){
        	$(scope.el).trigger("chosen:updated");
        },500);
    }
});

Vue.directive('datepicker', {
  bind: function () {
    var vm = this.vm;
    var key = this.expression;
    var context = this;
    $(this.el).datepicker({
      dateFormat: "YYYY-MM-DD",
      onSelect: function (date) {
        vm.$set(key, date);
      },
      onClose: function( selectedDate ) {}
    });
  },
  update: function (val) {
    $(this.el).datepicker('setDate', val);
  }
});

//Reusable functionalities for Vue components
Vue.mixin({
	methods: {
		ajax: function(url, data) {
			var scope = this;
			return Vue.http({
                url: phost() + url,
                method: 'POST',
                data: $.extend({erptkn: tkn}, data)
            });
		},
		actualizar_chosen: function(){
			var formulario = $(this.$el);
			setTimeout(function(){
	         	formulario.find('.chosen-select').trigger('chosen:updated');
	        }, 800);
	    },
	    toggleSubTabla: function(e) {
    		e.preventDefault();
            e.returnValue = false;
            e.stopPropagation();

    		//Toggle animacion de icono
    		$(e.currentTarget).find('i').toggleClass('fa-rotate-90');

    		//Toggle td subpanel
    		$(e.currentTarget).closest('tbody').find('tr:has(table) > td:first-child').toggleClass('hide');
    	}
	}
});

Vue.http.options.emulateJSON = true;
Vue.config.debug = true;
var Factura = new Vue({
    el: '#facturaForm',
    data: {
        modal: {
    		titulo:'',
    		contenido:'',
    		footer:''
    	},
    	id: typeof id != 'undefined' && _.isNumber(id) ? id : '',
    	guardarBtn: 'Guardar',
    	guardarBtnDisabled: false,
    },
    ready: function () {

    	//console.log('CREAR: ', this);

        var scope = this;
        var formulario = $(scope.$el);

        this.$nextTick(function () {

        	$('div.loader').remove();

        	//Mostrar formulario
        	formulario.removeClass('hide').addClass('fadeIn');

        	//mostrar div subpaneles
        	$('div.subpaneles').removeClass('hide').addClass('fadeIn');

        	//mostrar div comentarios
        	$('div.comentarios').removeClass('hide').addClass('fadeIn');
        });

        //Validacion jQuery Validate
        $.validator.setDefaults({
            errorPlacement: function (error, element) {
                return true;
            }
        });
        $(formulario).validate({
            //debug: true,
        	focusInvalid: true,
            ignore: '',
            wrapper: ''
        });

       if(editar_precio == 1){
           setTimeout(function(){
           $("#guardarBtn").removeAttr('disabled');
       }, 300);
       }
    },
    methods: {
        guardar: function () {

            var scope = this;
            var formulario = $(scope.$el);

            if (formulario.validate().form() == false) {
                //mostrar mensaje
                toastr.error('Debe completar los campos requeridos.');
            }else{
                formulario.find(':disabled').removeAttr('disabled');
                formulario.find('#guardarBtn').attr("disabled", true);
                formulario.submit();
            }

            //Mensaje
            //toastr.info('<h3><i class="fa fa-circle-o-notch fa-spin fa-fw"></i> Guardando...</h3>', '', {toastClass:'navy-bg', iconClass: 'in', progressBar:false, extendedTimeOut:60});

            //Guardar

        }
    }
});
