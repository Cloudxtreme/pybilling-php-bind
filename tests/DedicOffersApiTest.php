<?php
/*
 * PHP Unit tests for API PHP binding.
 * (c) Dmitry Shilyaev
 *
 * Using tests
 *      Start the development API server: ./manage.py runserver
 */

require_once dirname(dirname(__FILE__)) . '/resources/dedic_offer.php';

class DedicOffersApiTest extends PHPUnit_Framework_TestCase {
    public function test_offers_manage() {
        $payload = array(
            'platform' => '1U Asus RS120-X5-PI2',
            'cpu_name' => 'Intel Pentium D 820 2.8GHz',
            'cpu_count' => 1,
            'ram_gb' => 3,
            'hdd_gb' => 250,
            'hdd_count' => 2,
            'price' => 5000,
            'visible' => True,
            'comment' => 'some comment'
        );

        $offer = new pybilling\DedicOffer($payload);
        $offer->save();

        $loaded_offer = pybilling\DedicOffer::get($offer->id);
        $this->assertEquals('1U Asus RS120-X5-PI2', $loaded_offer->platform);
        $this->assertEquals(5000, $loaded_offer->price);

        // Load all offers
        $loaded_offers_list = pybilling\DedicOffer::filter();
        $this->assertTrue(1, count($loaded_offers_list));
    }
}
