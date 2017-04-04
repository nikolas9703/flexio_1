var fordenes = function(){

    var data = {
        referrer: document.referrer,
        pathname: window.location.pathname
    };

    var methods = {
        fromForm: function(){
            return (data.referrer.match(/ordenes\/ver/g) || data.referrer.match(/ordenes\/crear/g)) ? true : false;
        },
        inOrdenes:function(){
            return data.pathname.match(/ordenes/g) ? true : false;
        },
        canUseLocalStorage: function(){
            return methods.fromForm() && methods.inOrdenes() && typeof(Storage) !== "undefined";
        }
    };

    return {m:methods};

};
