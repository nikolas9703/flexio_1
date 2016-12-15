// jshint esversion:6
import Vuex from 'vuex';
//Vue.use(Vuex);
const state = {
    empezable_type: [],
    current: null,
    catalogo:[],
    empezable_id:null
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
