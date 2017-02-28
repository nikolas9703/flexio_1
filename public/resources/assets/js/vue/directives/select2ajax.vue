<script>
export default {
    twoWay: true,
    //priority: 1000,
    params: ['config','options'],

    bind: function () {
        var self = this;
        var config = {'minimumInputLength':0, 'width':'100%'};
        var ajax = {
            dataType:'json',
            processResults: function (data) {
                return {
                    results: data
                };
            }
        };

        this.params.config = $.extend(config, this.params.config);
        this.params.config.ajax = $.extend(ajax, this.params.config.ajax);
        $(this.el).select2(this.params.config).on('change', function(e) {
            self.set($(self.el).val());
        });
    },

    update: function (value) {
        var self = this;
        $(this.el).val(value);
        if($(this.el).val() === null && value !== ''){
            var datos = $.extend({erptkn: tkn},{campo:{id:value}});
            $.ajax({
                url: self.params.config.ajax.url(),
                type:"POST",
                data:datos,
                dataType:"json",
                success: function(data){
                    if(!_.isEmpty(data)){
                        $(self.el).append('<option value="'+ data.id +'" selected>'+ data.nombre +'</option>');
                        $(self.el).val(data.id != '' ? value : '').trigger('change');
                        if (typeof self.params.config.catalogo !== 'undefined'){
                            self.params.config.catalogo([data]);
                        }
                    }
                }
            });
        }else{
            $(this.el).val(value).trigger('change');
        }
    },

    unbind: function () {
         $(this.el).off().select2('destroy');
    }
};

</script>
