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
                    //return ele;
                    var self = ele.$element;
                    var fila = self.closest("tr");
                    var item_id = fila.find('.item').val();
                    var uuid_bodega = $('form').find('#lugar').val();
                    var disponible = 0;
                    var noDisponible = 0;

                    self.prop('disabled',true);

                    $.ajax({
                        async: false,//para la primera version, luego usar replace....
                        url: phost() + "inventarios/ajax-get-cantidad",
                        type:"POST",
                        data:{
                            erptkn:tkn,
                            item_id: item_id,
                            uuid_bodega: uuid_bodega
                        },
                        dataType:"json",
                        success: function(data){

                            if(!_.isEmpty(data)){

                                disponible = parseInt(data.cantidadDisponibleBase) || 0;
                                noDisponible = parseInt(data.cantidadNoDisponibleBase) || 0;

                            }

                            self.prop('disabled',false);
                        }
                    });

                    return [
                        '<table class="table table-bordered no-margins">',
                            '<thead>',
                                '<tr>',
                                        '<th>Disp.</th>',
                                        '<th>No Disponible</th>',
                                        '<th>Total</th>',
                                '</tr>',
                            '</thead>',
                            '<tbody>',
                                '<tr>',
                                    '<td>'+ disponible +'</td>',
                                    '<td>'+ noDisponible +'</td>',
                                    '<td>'+ (parseInt(disponible) + parseInt(noDisponible)) +'</td>',
                                '</tr>',
                            '</tbody>',
                        '</table>'].join("\n");


                }

            });
        }

    };

</script>
