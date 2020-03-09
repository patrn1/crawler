<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$cfg = include('config/.env.php');
$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => $cfg[ 'DB_DRIVER' ],
    'host'      => $cfg[ 'DB_HOST' ],
    'port'      => $cfg[ 'DB_PORT' ],
    'database'  => $cfg[ 'DB_DATABASE' ],
    'username'  => $cfg[ 'DB_USERNAME' ],
    'password'  => $cfg[ 'DB_PASSWORD' ],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);
// $capsule->setEventDispatcher(new Dispatcher(new Container));

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();
// composer require "illuminate/events" //required when you need to use observers with Eloquent.
