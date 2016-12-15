<?php
$I = new FunctionalTester($scenario);
//$I = am('soy usuario de Flexio');
$I->wantTo('entrar a mi cuneta de Flexio');
$I->signIn();
$I->seeInCurrentUrl('/usuarios/organizacion');
$I->see("Organizaci&oacute;n");
