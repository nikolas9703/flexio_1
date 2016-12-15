Vue.directive('datepicker2', {
    
    twoWay: true,
    
    params: ['config'],
    
    bind: function () {
        
        var self = this;
        $(this.el)
                .datepicker(self.params.config)
                .on('change', function () {
                    self.set(this.value);
                });
                
    },
    
    update: function (value) {
        
        $(this.el).val(value).trigger('change');
        
    },
    unbind: function () {
        
        $(this.el).off().datepicker('destroy');
        
    }
});


