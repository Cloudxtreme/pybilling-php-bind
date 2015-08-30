<?php
/*
 * PHP Unit tests for API PHP binding.
 * (c) Dmitry Shilyaev
 *
 * Using tests
 *      Start the development API server: ./manage.py runserver
 */

require_once dirname(dirname(__FILE__)) . '/resources/resource.php';
require_once dirname(dirname(__FILE__)) . '/resources/accounts.php';

class AccountsApiTest extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
    }

    public function test_accounts_manage() {
        // Create user
        $account = new Account(array(
            'name' => 'Dmitry'
        ));

        $account->save();

        $this->assertTrue($account->id > 0);
        $this->assertEquals('Dmitry', $account->name);
        $this->assertEquals(0, $account->balance);
        $this->assertEquals(0, $account->bonus_balance);
        $this->assertEquals('ru', $account->language);

        // Update user
        $account->name = 'Dmitry Shilyaev';
        $account->language = 'en';
        $account->balance = 100;
        $account->bonus_balance = 200;
        $account->save();

        // User details
        $account_upd = Account::get($account->id);
        $this->assertEquals($account->id, $account_upd->id);
        $this->assertEquals('Dmitry Shilyaev', $account_upd->name);
        $this->assertEquals(0, $account_upd->balance);
        $this->assertEquals(0, $account_upd->bonus_balance);
        $this->assertEquals('en', $account_upd->language);

        // Delete user
        $account_upd->delete();

        try {
            $account_upd = Account::get($account_upd->id);
            $this->fail("Waiting for the exception.");
        }
        catch(Exception $ex) {
            $this->assertEquals(404, $ex->getCode());
        }
    }
}
