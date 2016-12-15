Vue.directive('datepicker-range', {

    twoWay: true,

    params: ['rangeto'],

    bind: function () {

        var scope = this;

        $(scope.el).datepicker({
      		dateFormat: 'dd/mm/yy',
      		changeMonth: true,
      		numberOfMonths: 1,
          minDate: scope.params.rangeto,
      		onClose: function( selectedDate ) {
            //$(scope.el).datepicker( "option", "minDate", scope.params.rangeto);
            //$(scope.el).trigger('change');
      		}
      	})
        .on('change', function () {
            scope.set(this.value);
        });

    },

    update: function (value) {

        $(this.el).val(value).trigger('change');

    },
    unbind: function () {

        $(this.el).off().datepicker('destroy');

    }
});
