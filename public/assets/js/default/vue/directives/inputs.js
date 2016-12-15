Vue.directive('datepicker', {
  bind: function () {
    var vm = this.vm;
    var key = this.expression;
    var context = this;
    $(this.el).datepicker({
      dateFormat: "dd/mm/yy",
      onSelect: function (date) {
        vm.$set(key, date);
      },
      onClose: function( selectedDate ) {
        if(context.el.id ==='fecha_desde'){
           $("#fecha_hasta").datepicker( "option", "minDate", selectedDate );
        }
        if(context.el.id ==='fecha_hasta'){
          $("#fecha_desde").datepicker( "option", "maxDate", selectedDate );
        }
      }
    });

  },
  update: function (val) {
    $(this.el).datepicker('setDate', val);
  }
});

//Vue.directive('icheck', {
//    bind: function () {
//        var vm = this.vm;
//        var key = this.expression;
//        var context = this;
//        
//        
//        console.log(vm.transacciones.length);
//        console.log(key);
//        console.log(this.el.class);
//        console.log($(this.el).prop("class"));
//        $(this.el).iCheck({
//            checkboxClass: 'icheckbox_square-green',
//            radioClass: 'iradio_square-green',
//        });
//    },
//    update: function (val) {
//        //$(this.el).datepicker('setDate', val);
//    }
//});