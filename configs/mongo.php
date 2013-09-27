<?php

namespace Configs;

/**
 * Please make sure that you have the PECL 'mongo' module
 * installed and enabled.
 *
 * Configuration for the 'Mongo' writer.
 * Edit this if you'll be using Mongo for storage.
 */

class Mongo extends \Base\Config
{
    protected $_settings = array(
        'connectionUri'  => 'mongodb://localhost:27017',
        'dbName'         => 'emailLogs',
        'collectionName' => 'logs',
    );
}