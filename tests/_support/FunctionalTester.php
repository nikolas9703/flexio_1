<?php

use \Laracasts\TestDummy\Factory as TestDummy;
/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    use _generated\FunctionalTesterActions;

   /**
    * Define custom actions here
    */
    public function signIn(){
      $this->tengoUnaCuenta(['username'=>'admin@pensanomica.com','password'=>'ejemplo']);
      //load $I
      $I->amOnPage('/login');
      $I->fillField('email','admin@pensanomica.com');
      $I->fillField('password','ejemplo');
      $I->click('Login');
    }

    public function tengoUnaCuenta($overwrites=[])
    {
      TestDummy::create('\Flexio\Modulo\Usuario\Models\Usuario', $overwrites);
    }
}
