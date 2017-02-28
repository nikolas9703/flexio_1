// jshint esversion:6
import Vuex from 'vuex';

const state = {
    empezable_type: [],
    current: null,
    catalogo:[],
    empezable_id:null,
    items:[],
    precio:null,
    cat_alquiler_lista_precio_venta:null,
    estado:null
};

const mutations = {

    ['SET_CURRENT'](state, item) {
        state.current = item;
    },

    ['SET_CURRENT_BY_ID'](state, id) {
        state.current = state.items.find((item) => item.id == id);
    },
    ['SET_EMPEZABLETYPE'](state,todos){
      state.empezable_type = todos;
    },
    ['SET_CATALOGO'](state,catalogo){
        state.catalogo = catalogo;
    },
    ['SET_EMPEZABLEID'](state,id){
        state.empezable_id = id;
    },
    ['SET_PRECIO'](state,precio){
        state.precio = precio;
    },
    ['SET_ALQUILER_LISTA_PRECIO_VENTA_CAT'](state,catalogo){
        state.cat_alquiler_lista_precio_venta = catalogo;
    },
    ['SET_ESTADO'](state, estado){
        state.estado = estado;
    }
};

const getters = {
    empezable_type: (state) => state.empezable_type,
    currentEmpezableType: (state) => state.current,
};

export default new Vuex.Store({
  state,
  mutations,
  getters,
  strict: true
});
