
<script>
  export default {
    read: function(date, format) {
      if (!date) {
        return ''
      }
      return moment(date, 'YYYY-MM-DD').format(format);
    },
    write: function(val, oldVal) {
      return val;
    }
  };
</script>
