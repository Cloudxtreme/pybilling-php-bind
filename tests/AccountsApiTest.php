<?php
/*
 * PHP Unit tests for API PHP binding.
 * (c) Dmitry Shilyaev
 *
 * Using tests
 *      Start the development API server: ./manage.py runserver
 */

require_once dirname(dirname(__FILE__)) . '/resources/accounts.php';

class AccountsApiTest extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
    }

    public function test_accounts_create_defined_id() {
        $account = new pybilling\Account(array(
            'name' => 'Dmitry',
            'pk' => 150
        ));

        $account->save();
        $account = pybilling\Account::get(150);

        $this->assertEquals($account->id, 150);
    }

    public function test_accounts_with_contacts_and_personal_data() {
        $account = new pybilling\Account(array(
            'name' => 'Dmitry',
        ));
        $account->save();

        # Create
        $personal_data = new pybilling\PersonalData(array(
            'fio' => "Zbignev Bj Жезинский",
            'birth' => '1983-09-05',
            'postal_index' => 610001,
            'postal_address' => 'Address Postal',
            'phone' => '+7 495 6680903',
            'passport' => '8734 238764234 239874',
            'email' => 'lkdfds@ldkjfs.com',
            'account' => $account->id,
            'type' => 'PersonalDataPerson'
        ));
        $personal_data->save();


        $aContact = new pybilling\AccountContact(array(
            'name' => 'home',
            'address' => 'dmitry@shyliaev.com',
            'type' => 'email',
            'account' => $account->id
        ));
        $aContact->save();


        //  Checks
        $updated_account = pybilling\Account::get($account->id);
        $this->assertEquals(1, count($updated_account->contacts));
        $this->assertEquals(1, count($updated_account->personal_data));

        // check contacts
        $contact = $updated_account->contacts[0];
        $this->assertEquals('home', $contact->name);
        $this->assertEquals('email', $contact->type);
        $this->assertEquals('dmitry@shyliaev.com', $contact->address);
        $this->assertEquals($account->id, $contact->account);

        // check personal data
        $pdata = $updated_account->personal_data[0];
        $this->assertEquals('610001', $pdata->postal_index);
        $this->assertEquals('Zbignev Bj Zhezinskij', $pdata->fio_lat);
        $this->assertEquals('Zbignev Bj Жезинский', $pdata->fio);
        $this->assertEquals('PersonalDataPerson', $pdata->type);
        $this->assertEquals('+7 495 6680903', $pdata->phone);
        $this->assertEquals($account->id, $pdata->account);
    }

    public function test_accounts_manage() {
        // Create user
        $account = new pybilling\Account(array(
            'name' => 'Dmitry'
        ));

        $account->save();

        $this->assertTrue($account->id > 0);
        $this->assertEquals('Dmitry', $account->name);
        $this->assertEquals(0, $account->balance);
        $this->assertEquals(0, $account->bonus_balance);
        $this->assertEquals('RU', $account->language);

        // Update user
        $account->name = 'Dmitry Shilyaev';
        $account->language = 'EN';
        $account->balance = 100;
        $account->bonus_balance = 200;
        $account->save();

        // User details
        $account_upd = pybilling\Account::get($account->id);
        $this->assertEquals($account->id, $account_upd->id);
        $this->assertEquals('Dmitry Shilyaev', $account_upd->name);
        $this->assertEquals(0, $account_upd->balance);
        $this->assertEquals(0, $account_upd->bonus_balance);
        $this->assertEquals('EN', $account_upd->language);

        // Delete user
        $account_upd->delete();

        try {
            $account_upd = pybilling\Account::get($account_upd->id);
            $this->fail("Waiting for the exception.");
        } catch (Exception $ex) {
            $this->assertEquals(404, $ex->getCode());
        }
    }
}
