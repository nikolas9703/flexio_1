<script>
export default {
    previousValue:null,
    twoWay: true,
    data:[],
    select2:{

    },
    //priority: 1000,
    params: ['config','options'],

    bind: function () {
        console.log("databinding", this.vm);
        var self = this;
        console.log("bind::",this)
        if(this.params.config==null){
            console.log("Parameter config is null select catalog");
            return;
        }
        this.select2={
            width:'100%',
             ajax: {
                url: self.params.config.url,
                method:'POST',
                dataType: 'json',
                delay: 200,
                cache: true,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page,
                        limit: 10,
                        erptkn: window.tkn
                    };
                },
                processResults: function (data, params) {
                    self.data=data;
                    if(self.params.config.using==null){
                        console.log("ERROR: using is missing in select2-catalog config");
                    }
                    let resultsReturn = data.map(resp=> [{'id': resp[self.params.config.using[0]],'text': resp[self.params.config.using[1]]}]).reduce((a, b) => a.concat(b),[]);
                    self.vm.$emit("select_result", data, self.el);
                    return {results:resultsReturn};
                },
                escapeMarkup: function (markup) { return markup; },
             }
        }
        $(this.el).select2(this.select2);

        console.log("Initial value:", $(this.el).val());
        this.previousValue=$(this.el).val();
    },

    update: function (value) {
        var self = this;

        console.log("Changed value:", value,":",$(this.el).val() , this);
        if(value!=null && value != ""){
            var obj = this.data.find((q)=> q[self.params.config.using[0]] == value);
            console.log("cliente encontrado:", obj);

            if(typeof obj != "undefined"){
               this.select2['data']=[{'id': obj[self.params.config.using[0]],'text': obj[self.params.config.using[1]]}];
               //this.select2['ajax']=null;
               self.vm.$emit("selected", obj, self.el);
            }else{

                 self.vm.$http.post({
                    url: typeof self.params.config.url_find != "undefined"? self.params.config.url_find:self.params.config.url ,
                    method: 'POST',
                    data: {
                            id: value, // search term
                            erptkn: window.tkn
                        }}).then((response) => {
                           if(response!=null && response.data.length > 0){
                               self.vm.$emit("select_result", response.data, self.el);
                               obj=response.data[0];
                                self.select2['data']=[{'id': obj[self.params.config.using[0]],'text': obj[self.params.config.using[1]]}];
                               // self.select2['ajax']=null;
                                $(self.el).select2(self.select2).on('change', function(e) {
                                    self.set($(self.el).val());
                                })
                                $(self.el).val(value).trigger('change');
                                self.vm.$emit("selected", obj, self.el);
                           }
                        });
            }
        }

        $(self.el).select2(self.select2).on('change', function(e) {
          self.set($(self.el).val());
         })
       $(self.el).val(value).trigger('change');
    },

    unbind: function () {
         $(this.el).off().select2('destroy');
    }
};

</script>
