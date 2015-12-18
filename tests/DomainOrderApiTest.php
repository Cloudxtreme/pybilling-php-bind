<?php
/*
 * PHP Unit tests for API PHP binding.
 * (c) Dmitry Shilyaev
 *
 * Using tests
 *      Start the development API server: ./manage.py runserver
 */

require_once dirname(dirname(__FILE__)) . '/lib/ParametersWrapper.php';
require_once dirname(dirname(__FILE__)) . '/lib/Httpful/Bootstrap.php';

require_once dirname(dirname(__FILE__)) . '/resources/accounts.php';
require_once dirname(dirname(__FILE__)) . '/resources/domain_orders.php';
require_once dirname(dirname(__FILE__)) . '/resources/pdata.php';

class DomainOrderApiTest extends PHPUnit_Framework_TestCase {
    public function test_offers_manage() {
        // Create user
        $account = new pybilling\Account(array(
            'name' => 'Dmitry'
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
            'email' => 'lkdfds@gmail.com',
            'account' => $account->id,
            'type' => 'PersonalDataPerson'
        ));
        $personal_data->save();


        // Register domain
        $rand = rand(1, 1000);
        $domain_name = "dfjslkfjsdlkfj-$rand.ru";
        $payload = array(
            'domain' => $domain_name,
            'registrar' => 'rucenter',
            'account_id' => $account->id
        );

        $domain_order = new pybilling\DomainOrder($payload);
        $domain_order->save();

        $this->assertEquals($domain_order->domain, $domain_name);
        $this->assertTrue(isset($domain_order->contract));
        $this->assertTrue(isset($domain_order->balance));
        $this->assertTrue($domain_order->id > 0);
    }
}
