var fpagos = function(){

    var data = {
        referrer: document.referrer,
        pathname: window.location.pathname
    };

    var methods = {
        fromForm: function(){
            return (data.referrer.match(/pagos\/ver/g) || data.referrer.match(/pagos\/crear/g)) ? true : false;
        },
        inPagos:function(){
            return data.pathname.match(/pagos/g) ? true : false;
        },
        canUseLocalStorage: function(){
            return methods.fromForm() && methods.inPagos() && typeof(Storage) !== "undefined";
        }
    };

    return {m:methods};

};
