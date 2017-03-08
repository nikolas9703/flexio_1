bluapp.controller("identificacionController", function($scope, $timeout, identificacionService, $document){
    var model = this;

    if (typeof acreedor_id === 'undefined') {
        acreedor_id = "";
    }

    $scope.ruc = {
        tipo:'natural',
        provincia_id:'',
        letra_id:'',
        tomo:'',
        asiento:'',
        no_pasaporte:'',
        disabledNatural:false,
        hideNatural:false,
        disabledNatural2:true,
        hideNatural2:true
    };

    $scope.ruc2 = {
        tomo:'',
        folio:'',
        asiento:'',
        digito_verificador:'',
        disabledJuridico:true,
        hideJuridico:true
    };

    $scope.changedIdentificacion = function(){
        limpiarIdentificacion();
        if($scope.ruc.tipo == "natural")
        {
            $scope.ruc.disabledNatural=false;
            $scope.ruc.hideNatural=false;
            $scope.ruc.disabledNatural2=true;
            $scope.ruc.hideNatural2=true;
        }
        else if($scope.ruc.tipo == "juridico")
        {
            $scope.ruc.disabledNatural=true;
            $scope.ruc.hideNatural=true;
            $scope.ruc.disabledNatural2=true;
            $scope.ruc.hideNatural2=true;
            $scope.ruc2.disabledJuridico=false;
            $scope.ruc2.hideJuridico=false;
        }
        else
        {
            $scope.ruc.disabledNatural=true;
            $scope.ruc.hideNatural=true;
            $scope.ruc.disabledNatural2=true;
            $scope.ruc.hideNatural2=true;
            $scope.ruc2.disabledJuridico=true;
            $scope.ruc2.hideJuridico=false;
        }
    };

    $scope.changedLetra = function(){
        $scope.ruc.disabledNatural2=true;
        $scope.ruc.hideNatural2=true;
        if(!($scope.ruc.letra_id == '50' || $scope.ruc.letra_id == '53'))
        {
            $scope.ruc.provincia_id = '';
            if($scope.ruc.letra_id == '54')
            {
                $scope.ruc.disabledNatural2=false;
                $scope.ruc.hideNatural2=false;
            }
        }
        generarRUC();
    };

    $scope.chagedRUC = function(){
        generarRUC();
    };

    var limpiarIdentificacion = function(){
        $scope.ruc = {
            tipo:$scope.ruc.tipo,
            provincia_id:'',
            letra_id:'',
            tomo:'',
            asiento:'',
            no_pasaporte:'',
            disabledNatural:false,
            hideNatural:false,
            disabledNatural2:true,
            hideNatural2:true
        };

        $scope.ruc2 = {
            tomo:'',
            folio:'',
            asiento:'',
            digito_verificador:'',
            disabledJuridico:true,
            hideJuridico:true
        };
    };

    var getProvinciasNumber = function(){
        var provincia = [];

            provincia["35"]=1,
            provincia["38"]=2;
            provincia["37"]=3;
            provincia["36"]=4;
            provincia["39"]=5;
            provincia["40"]=6;
            provincia["41"]=7;
            provincia["42"]=8;
            provincia["43"]=9;
            provincia["47"]=10;
            provincia["48"]=11;
            provincia["49"]=12;
            provincia["44"]=13;
        return provincia;
    };

    var generarRUC = function(){
        var ruc = "";
        var provincias = getProvinciasNumber();

        if($scope.ruc.tipo == "natural")
        {
            ruc += ($("#letra_id").find("option:selected").text() != "Seleccione") ? $("#letra_id").find("option:selected").text() : "";

            if($scope.ruc.letra_id == "54")
            {
                ruc += $scope.ruc.no_pasaporte;
            }
            else
            {
                ruc += (typeof provincias[$scope.ruc.provincia_id] !== "undefined") ? provincias[$scope.ruc.provincia_id] : "";
                var tomo = $scope.ruc.tomo;
                var asiento = $scope.ruc.asiento
                ruc += tomo.length > 0 ? '-'+ tomo : "";
                ruc += asiento.length > 0 ? '-'+ asiento : "";
            }

        }
        else if($scope.ruc.tipo == "juridico")
        {
            var tomo = $scope.ruc2.tomo;
            var folio = $scope.ruc2.folio;
            var asiento = $scope.ruc2.asiento;
            var digito_verificador = $scope.ruc2.digito_verificador;

            ruc += tomo.length > 0 ? tomo : "";
            ruc += folio.length > 0 ? '-'+ folio : "";
            ruc += asiento.length > 0 ? '-'+ asiento : "";
            ruc += digito_verificador.length > 0 ? '-'+ digito_verificador : "";
        }
        else
        {
            ruc = "";
        }

        $scope.identificacion = ruc;
    };

    if(acreedor_id > 0)
    {
        var identificacion = identificacionService.getAcreedor({acreedor_id:acreedor_id});
        identificacion.then(function(data){
            var ref = (data.referencia) ? data.referencia : {"identificacion":"natural"};



            $scope.ruc = {
                tipo:ref.identificacion,
                provincia_id:(typeof ref.provincia_id !== 'undefined') ? ref.provincia_id : '',
                letra_id:(typeof ref.letra_id !== 'undefined') ? ref.letra_id : '',
                tomo:(typeof ref.tomo !== 'undefined') ? ref.tomo : '',
                asiento:(typeof ref.asiento !== 'undefined') ? ref.asiento : '',
                no_pasaporte:(typeof ref.no_pasaporte !== 'undefined') ? ref.no_pasaporte : '',
                disabledNatural:(ref.identificacion == "natural") ? false : true,
                hideNatural:(ref.identificacion == "natural") ? false : true,
                disabledNatural2:(ref.identificacion == "natural" && ((typeof ref.letra_id !== 'undefined') && ref.letra_id != "54")) ? true : false,
                hideNatural2:(ref.identificacion == "natural" && ((typeof ref.letra_id !== 'undefined') && ref.letra_id != "54")) ? true : false
            };
 
            $scope.ruc2 = {
                tomo:(typeof ref.tomo2 !== 'undefined') ? ref.tomo2 : '',
                folio:(typeof ref.folio !== 'undefined') ? ref.folio : '',
                asiento:(typeof ref.asiento2 !== 'undefined') ? ref.asiento2 : '',
                digito_verificador:(typeof ref.digito_verificador !== 'undefined') ? ref.digito_verificador : '',
                disabledJuridico:(ref.identificacion == "natural") ? true : false,
                hideJuridico:(ref.identificacion == "natural") ? true : false
            };

            setTimeout(function(){
                generarRUC();
            });
        });
    }

});
