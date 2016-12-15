Vue.directive('select3', {
   twoWay: true,

    params: ['config'],

    bind: function () {
        $(this.el).select2(this.params.config);
    },

    update: function (value) {
      var self = this;
      $(this.el).on('change', function(e) {
        self.set($(self.el).val());
       });
      $(this.el).val(value).trigger('change');


   },

    unbind: function () {

        $(this.el).off().select2('destroy');

    }
});
