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
require_once dirname(dirname(__FILE__)) . '/resources/contacts.php';

class AccountContactsApiTest extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
    }

    public function test_accounts_manage() {
        // Create user
        $account = new pybilling\Account(array(
            'name' => 'Dmitry'
        ));
        $account->save();

        # Create
        $aContact = new pybilling\AccountContact(array(
            'name' => 'home',
            'address' => 'dmitry@shyliaev.com',
            'type' => 'email',
            'account' => $account->id
        ));
        $aContact->save();

        $this->assertEquals('home', $aContact->name);
        $this->assertEquals('email', $aContact->type);
        $this->assertEquals('dmitry@shyliaev.com', $aContact->address);
        $this->assertEquals($account->id, $aContact->account);

        # Update
        $aContact->address = 'bxtgroup@gmail.com';
        $aContact->name = 'work';

        $aContact->save();

        $aContact_upd = pybilling\AccountContact::get($aContact->id);
        $this->assertEquals($aContact->id, $aContact_upd->id);
        $this->assertEquals('work', $aContact_upd->name);
        $this->assertEquals('email', $aContact_upd->type);
        $this->assertEquals('bxtgroup@gmail.com', $aContact_upd->address);
        $this->assertEquals($account->id, $aContact_upd->account);

        // Delete contacts
        $aContact_upd->delete();

        try {
            pybilling\AccountContact::get($aContact_upd->id);
            $this->fail("Waiting for the exception.");
        }
        catch(Exception $ex) {
            $this->assertEquals(404, $ex->getCode());
        }
    }
}
