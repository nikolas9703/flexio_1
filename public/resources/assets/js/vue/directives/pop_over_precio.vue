<script>

    export default {

        bind: function () {

            var self = this;

            $(this.el).webuiPopover({

                placement:'top',
                trigger:'click',
                title:'',
                multi:true,
                closeable:false,
                cache:false,//destroy and recreate
                style:'',
                delay:300,
                padding:true,
                backdrop:false,
                content: function(ele){

                    var self = ele.$element;
                    var fila = self.closest("tr");
                    var item_id = fila.find('.pop_over_precio_id').val();

                    var tabla = "";
                    var div_id = "div" + Math.floor((Math.random() * 100) + 1);

                    $.ajax({
                        //async: false,//para la primera version, luego usar replace....
                        url: phost() + "inventarios/ajax-get-precios",
                        type:"POST",
                        data:{
                            erptkn:tkn,
                            item_id: item_id
                        },
                        dataType:"json",
                        success: function(data){

                            if(!_.isEmpty(data)){

                                tabla = '';
                                tabla+= '<table class="table table-bordered no-margins"><thead><tr>';
                                _.forEach(data, function(ele){
                                    tabla += '<th style="background:#0076BE;color: white;">'+ ele.proveedor +'<br>'+ ele.fecha +'</th>';
                                });
                                tabla+= '</tr></thead><tbody><tr>';
                                _.forEach(data, function(ele){
                                    tabla += '<td>$'+ ele.precio +' ('+ ele.unidad +')</td>';
                                });
                                tabla+= '</tr></tbody></table>';

                                $('body').find('#'+ div_id).html(tabla);
                                var aux = $('body').find('#'+ div_id).closest('.webui-popover');
                                var top = aux.css('top');
                                aux.css('top', (top.replace('px', '') - 70) + 'px');

                            }else{
                                $('body').find('#'+ div_id).html('<p>Articulo sin facturas.<p>');
                            }

                            self.prop('disabled',false);
                        }
                    });

                    return '<div id="'+ div_id +'">...</div>';

                }

            });
        }

    };

</script>
