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

class DomainOrder extends Resource {
    public static function getResourceName() {
        return 'domain_orders';
    }
}
