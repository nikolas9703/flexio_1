// jshint esversion:6
const rendondeoDecimal = {
  methods:{
    checkPrecision(val, base) {
      val = Math.round(Math.abs(val));
      return isNaN(val) ? base : val;
    },
      roundNumber(value, precision){
           precision = this.checkPrecision(precision, 2);
           const power = Math.pow(10, precision);

           // Multiply up by precision, round accurately, then divide and use native toFixed()
           return (Math.round((value + 1e-8) * power) / power).toFixed(precision);
      }
  }
};  

export default rendondeoDecimal;