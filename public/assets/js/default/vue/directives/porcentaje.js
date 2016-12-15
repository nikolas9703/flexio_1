Vue.directive('porcentaje', {
    twoWay: true,
    bind: function () {

        var self = this;
        $(this.el).inputmask('percentage',{suffix: "",clearMaskOnLostFocus: false});
    }
});
