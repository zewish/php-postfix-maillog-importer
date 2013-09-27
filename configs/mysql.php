<?php

namespace Configs;

/**
 * Please make sure that you have the 'mysql' or 'mysqli' and the 'pdo'
 * modules installed and enabled.
 *
 * Configuration for the 'MySql' writer.
 * Edit this if you'll be using MySQL/MariaDB for storage.
 */

class MySql extends \Base\Config
{
    protected $_settings = array(
        'hostname'          => 'localhost',
        'username'          => 'root',
        'password'          => 'Str0ngP4$sw0rd!',
        'dbName'            => 'emailLogs',
        'tableName'         => 'email_log',

        /**
         * This is a very important option that depends on the configuration
         * of your MySQL/MariaDB server. This is the batch insert count when
         * parsing. '1000' must be pretty standard count, but if you
         * experience any problems you can tweak this eiter up or down.
         */
        'batchInsertsCount' => 1000,
    );
}