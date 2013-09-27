<?php

namespace MaillogImporter;

/**
 * DateTime conversion helper
 */

class DateConvertor
{
    const DATETIME_FORMAT_POSTFIX = 'M j H:i:s';

    public static function createFromPostfix($dateStr)
    {
        return \DateTime::createFromFormat(self::DATETIME_FORMAT_POSTFIX, $dateStr);
    }
}