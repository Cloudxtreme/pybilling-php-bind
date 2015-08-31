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
require_once dirname(dirname(__FILE__)) . '/resources/pdata.php';

class AccountContactsApiTest extends PHPUnit_Framework_TestCase {
    public static function setUpBeforeClass() {
    }

    public function test_accounts_manage() {
        // Create user
        $account = new Account(array(
            'name' => 'Dmitry'
        ));
        $account->save();

        # Create
        $personal_data = new PersonalData(array(
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

        $this->assertEquals('610001', $personal_data->postal_index);
        $this->assertEquals('Zbignev Bj Zhezinskij', $personal_data->fio_lat);
        $this->assertEquals('Zbignev Bj Жезинский', $personal_data->fio);
        $this->assertEquals('PersonalDataPerson', $personal_data->type);
        $this->assertEquals('+7 495 6680903', $personal_data->phone);
        $this->assertEquals($account->id, $personal_data->account);

        # Update
        $personal_data->fio = 'Дмитрий Шиляев';
        $personal_data->save();

        $personal_data_upd = PersonalData::get($personal_data->id);
        $this->assertEquals($personal_data->id, $personal_data_upd->id);
        $this->assertEquals('Zbignev Bj Zhezinskij', $personal_data_upd->fio_lat);
        $this->assertEquals('Дмитрий Шиляев', $personal_data_upd->fio);
        $this->assertEquals('PersonalDataPerson', $personal_data_upd->type);
        $this->assertEquals('+7 495 6680903', $personal_data_upd->phone);
        $this->assertEquals($account->id, $personal_data_upd->account);

        // Delete
        $personal_data_upd->delete();

        try {
            PersonalData::get($personal_data_upd->id);
            $this->fail("Waiting for the exception.");
        } catch (Exception $ex) {
            $this->assertEquals(404, $ex->getCode());
        }
    }
}
