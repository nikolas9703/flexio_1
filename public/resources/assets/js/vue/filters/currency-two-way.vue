
<script>

  export default {

    read: function(value) {
      return accounting.formatNumber(value, 2, ",");
    },

    write: function(value) {
      return accounting.unformat(value);
    }

  };

</script>
