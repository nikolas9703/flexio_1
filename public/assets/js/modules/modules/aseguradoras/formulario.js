//Inicializar Chosen plugin
if ($().chosen){
    if($(".chosen-filtro").attr("class") != undefined){
            $(".chosen-filtro").chosen({
                    width: '100%',
                    disable_search: true,
                    inherit_select_classes: true
            });
    }
}