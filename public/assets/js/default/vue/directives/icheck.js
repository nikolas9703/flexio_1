Vue.directive('icheck', {
   twoWay: true,

    params: ['config'],

    bind: function () {
       // $(this.el).iCheck(this.params.config);
       $(this.el).iCheck({ checkboxClass: 'icheckbox_square-green',});
    },

    update: function (value) {
      var self = this;
      $(this.el).on('ifChecked', function(e) {
        self.set($(self.el).val());
       });
      $(this.el).val(value);


   },

    unbind: function () {

        $(this.el).off().select2('destroy');

    }
});
