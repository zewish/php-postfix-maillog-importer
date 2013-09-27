<?php

namespace Configs;

/**
 * Main configration file for the Maillog Importer. Please start from here.
 */

class Main extends \Base\Config
{
    protected $_settings = array(
        /**
         * The hostname you are sending mail from. Example: mysite.com
         */
        'messageIdHost' => 'mysite.com',

        /**
         * Available writers: MySql/Mongo (Warning: CaSe-sENsitiVe)
         *
         * Corresponding configuration files can be found inside the 'configs' directory.
         * If you are using the 'MySql' writer be sure to create database and import the
         * table schema from the 'sql' directory.
         *
         * If using MariaDB for storage please use the 'MySql' writer.
         */
        'dbWriterClass' => 'Mongo',

        /**
         * The default filename to be parsed when the script is called withot any parameters.
         */
        'defaultMaillogFile' => '/var/log/mail.log',

        /**
         * The regular expression used to detect the date of the line in the Postfix
         * 'mail.log' file. It is unlikely that you need to change this unless you have
         * some really weird package of Postfix.
         */
        'dateTimeRegex' => '^([a-zA-Z]{3} [\d|\s]\d \d{2}\:\d{2}\:\d{2})',
    );
}