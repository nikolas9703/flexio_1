<template>
  <div>
    <!-- optional indicators -->
    <i class="fa fa-spinner fa-spin" v-if="loading" _v-09b76360></i>
    <template v-else>
      <!-- <i class="fa fa-search" v-show="isEmpty" _v-09b76360></i>
      <i class="fa fa-times" v-show="isDirty" @click="reset" _v-09b76360></i> -->
    </template>

    <!-- the input field -->
    <input type="text"
           class="form-control"
           placeholder="Buscar Item"
           name="typeahead"
           autocomplete="off"
           v-model="query"
           @keydown.down="down"
           @keydown.up="up | debounce 500"
           @keydown.enter="hit"
           @keydown.esc="reset"
           @input="update"
          />

    <!-- the list -->
    <ul v-show="hasItems" class="typehead-result">
      <li v-for="item in items" :class="activeClass($index)" @mousedown="hit" @mousemove="setActive($index)">
        <span class="item_nombre" v-text="item.nombre +' - '+ item.codigo"></span>
        <span class="item_categoria" v-text="item.categoria[0].nombre"></span>
        <!-- <span class="item_info" v-text="enExistencia(item)"></span>
        <span class="item_info" v-text="enPedido(item)"></span> -->
      </li>
    </ul>
  </div>
</template>

<script>
import VueTypeahead from 'vue-typeahead'
import Articulos from '../../../js/items';
export default {
  extends: VueTypeahead, // vue@1.0.22+
  // mixins: [VueTypeahead], // vue@1.0.21-
  props:['item_url', 'categoria_id', 'parent_index'],
  data () {
    return {
      // The source url
      // (required)
      src: window.phost() + this.item_url,

      // The data that would be sent by request
      // (optional)
      data: {
        categoria_id:''
      },

      // Limit the number of items which is shown at the list
      // (optional)
      limit: 25,

      // The minimum character length needed before triggering
      // (optional)
      minChars: 3,

      // Override the default value (`q`) of query parameter name
      // Use a falsy value for RESTful query
      // (optional)
      queryParamName: 'search',

    }
  },

  watch:{

    'query': function() {

      var context = this;
      context.data.categoria_id = context.categoria_id;

    }

  },

  methods: {
    // The callback function which is triggered when the user hits on an item
    // (required)
    onHit (item) {

      this.reset();
      this.query = item.nombre;
      this.$dispatch('update-item', $.extend(item,{parent_index:this.parent_index}));

    },

    // The callback function which is triggered when the response data are received
    // (optional)
    prepareResponseData (data) {
      // data = ...
      //
      //this.data = data;
      return data
    },
    isInCategory(items){
      if(_.isEmpty(this.query)){
        return true;
      }
      var item = _.find(items, ['nombre',this.query]) || false;

      return _.isObject(item)?true:this.query="";
    },

    unidad_actual(item){
      var actual = item;
      var unidades = actual.unidades;
      var item_unidad = _.find(unidades,['id',actual.unidad_id]);
      return item_unidad.nombre || '';
    },
    enExistencia(item){
      var actual = item;
      return  'En existencia: '+ actual.existencia.cantidadDisponibleBase + ' ' + this.unidad_actual(item);
    },
    enPedido(item){
      var actual = item;
      return 'En pedido: '+actual.existencia.cantidadDisponibleBase + ' ' + this.unidad_actual(item);
    }
  },
  events:{
    'fill-typeahead'(items){
      this.isInCategory(items);

      if(_.isEmpty(this.query)){
        this.items = items;
      }
    },
    'set-typeahead-nombre'(nombre){
      if(!_.isEmpty(nombre)){
       this.reset();
       this.query=nombre;
      }
    }
  }
}
</script>

<style lang="sass">


  ul.typehead-result{
    background-color:#FFFFFF;
    border: 1px solid #999999;
    border-radius: 4px;
    box-sizing: border-box;
    display: block;
    position: absolute;
    z-index:1051;
    max-height: 300px;
    overflow-y: auto;
    width:100%;
    -webkit-padding-start: 0px;
    top: 35px;
    li{
      /* ... */
      cursor: pointer;
      padding:10px 0px 10px 10px;
      width:100%;
      &.active{
        color:#FFFFFF;
        background-color:#5897FB;
        .item_categoria{
          font-size:10px;
          display:block;
          color:#FFFFFF;
        }
        .item_info{
          font-size:10px;
          display:block;
          color:#000000;
          text-align:right;
          margin-right:10px;
        }
      }
    }
    .item_nombre{
        font-size:12px;
        display:block;
    }
    .item_categoria{
      font-size:10px;
      display:block;
      color:#27AAE1;
      font-weight:bold;
    }
    .item_info{
      font-size:10px;
      display:block;
      color:#A9A9A9;
      text-align:right;
      margin-right:10px;
    }
  }
  i[_v-09b76360] {
    color: #000000;
    float: right;
    top: 10px;
    right: 43px;
    position: absolute;
    opacity: .4;
    z-index: 10;
}
</style>
