<script>
export default {
    twoWay: true,
    //priority: 1000,
    params: ['config','options'],

    bind: function () {
        var self = this;
        $(this.el).select2(this.params.config);
        $(this.el).select2(this.params.config).on('change', function(e) {
            self.set($(self.el).val());
        });
    },

    update: function (value) {
        var self = this;
        $(this.el).val(value).trigger('change');
    },

    unbind: function () {
         $(this.el).off().select2('destroy');
    }
};

</script>
