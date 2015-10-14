<?php

namespace pybilling;

/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 26.08.15
 * Time: 16:06
 *
 * Project: pybilling-php-bind
 */

require_once 'resource.php';

class DedicOffer extends Resource {

    public static function getResourceName() {
        return 'dedic_offer';
    }

}
