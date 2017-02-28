// jshint esversion:6
import {AnticiposLocalStorage} from './clases/anticipo-local-storage';


var formulario={
  saldo_pendiente:'0.00',
  credito:'0.00',
  fecha_anticipo:moment().format('DD/MM/YYYY'),
  monto:'0.00',
  tipo_deposito:'banco',
  estado:'por_aprobar',
  metodo_anticipo:'',
  depositable_id:'',
  tipo_anticipable: AnticiposLocalStorage.tipoAnticipable,
  anticipable_id:'',
  opciones_metodo_acticipo:{ach:{nombre_banco_ach:'', cuenta:''},cheque:{numero_cheque:'',nombre_banco_cheque:''}},
  id:'',
  creado_por: window.usuario_id,
  centro_contable_id:''
};
module.exports = {
  formulario:formulario
};
