<?php
$I = new AcceptanceTester($scenario);
$I->wantTo('ver la pagina de login');
$I->amOnpage('/login');
$I->seeInTitle('Login | Flexio');
$I->fillField('username', 'admin@admin.com');
$I->fillField('password', '12345');
$I->click('Login');
