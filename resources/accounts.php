<?php

/**
 * Created by PhpStorm.
 * User: dmitry
 * Date: 26.08.15
 * Time: 16:06
 *
 * Project: pybilling-php-bind
 */

require_once 'resource.php';
require_once 'accounts.php';
require_once 'contacts.php';
require_once 'pdata.php';

class Account extends Resource {
    public static function getResourceName() {
        return 'accounts';
    }
}
