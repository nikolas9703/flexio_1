<?php
namespace Flexio\Modulo\Login\Models;
use Flexio\Modulo\Usuarios\Models\Usuarios;


class Auth extends Usuarios{


  public static function check_username($clause){
    return Auth::where($clause)->first();
  }

  public function actualizar_estado()
    {
    	$clause = array(
        "status" => 'Expirado'
    	);
      $user = Auth::find($this->id);
      $user->update($clause);
    }

    public function update_last_login($id)
    {
      	$user = Auth::find($id);
        $user->update(["last_login" => date('Y-m-d H:i:s')]);
    }

}
