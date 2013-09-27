<?php

namespace MaillogImporter;

/**
 * Read the 'maillog' file and pass it to the 'parser'
 */

class FileImporter
{
    /**
     * @var resource
     */
    protected $_fileHandle = null;

    public function __construct($fileName = '/var/log/maillog')
    {
        // Load 'maillog' file
        if (!file_exists($fileName)) {
            throw new MaillogException('Maillog file "' . $fileName . '" does not exist');
        }
        if (!is_readable($fileName)) {
            throw new MaillogException('Maillog file "' . $fileName . '" is not readable');
        }
        $this->_fileHandle = fopen($fileName, 'r');
        if (empty($this->_fileHandle)) {
            throw new MaillogException('Could not open maillog file "' . $fileName . '" for reading');
        }
    }

    public function import(\MaillogImporter\Writer\WriterInterface $writer)
    {
        // Load the last previously parsed date from the Db
        $startDate = $writer->getLastDate();
        $startDateTimestamp = (!empty($startDate)) ? $startDate->getTimestamp() : 0;
        $dateTimeRegex = '/' . Config('Main')->dateTimeRegex . '/';

        // Get all the data from the 'maillog' and add it to the database
        $lastParsedDate = null;
        $MaillogImporter = new \MaillogImporter\Parser($writer);
        while (($line = fgets($this->_fileHandle)) != false) {
            if (preg_match($dateTimeRegex, $line, $matches)) {
                $lastParsedDate = $matches[1];
                // Skip the lines that are already logged
                if (!empty($startDateTimestamp) && (\MaillogImporter\DateConvertor::createFromPostfix($lastParsedDate)->getTimestamp() - $startDateTimestamp) <= 0) {
                    continue;
                }
                $MaillogImporter->parseLine($line);
            }
            else {
                throw new MaillogException('Could not match line with the date regex: "' . $line . '"');
            }
        }
    }

    public function __destruct()
    {
        // Close 'maillog' file
        if (!empty($this->_fileHandle)) {
            fclose($this->_fileHandle);
        }
    }
}