Vue.directive('select2', {

    twoWay: true,

    params: ['config'],

    bind: function () {

        var self = this;
        $(this.el)
                .select2(self.params.config)
                .on('change', function () {
                    var ele = this;
                    setTimeout(function(){
                        if(self.el === null)return;
                        self.set($(ele).val());
                    });
                });
    },

    update: function (value) {

        $(this.el).val(value).trigger('change');

    },

    unbind: function () {

        $(this.el).off().select2('destroy');

    }
});
