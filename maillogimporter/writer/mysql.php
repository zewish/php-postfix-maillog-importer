<?php

namespace MaillogImporter\Writer;

/**
 * Email log writer for MySQL
 */

class MySql implements WriterInterface
{
    const DATETIME_FORMAT_MYSQL = 'Y-m-d H:i:s';

    /**
     * @var \PDO
     */
    protected $_db = null;

    /**
     * @var array
     */
    protected $_inserts = array();

    /**
     * @var integer
     */
    protected $_batchInsertsCount = 1;

    public function __construct()
    {
        $mysqlConfig = Config('MySql');
        $this->_db = new \PDO(
            "mysql:host=" . $mysqlConfig->hostname . ';dbname=' . $mysqlConfig->dbName . ';charset=utf8',
            $mysqlConfig->username, $mysqlConfig->password, array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';")
        );

        $this->_tableName = $mysqlConfig->tableName;
        $this->_batchInsertsCount = $mysqlConfig->batchInsertsCount;
    }

    public function __destruct()
    {
        $this->_flushInserts();
    }

    public function getLastDate()
    {
        $sql = 'SELECT `date_updated` FROM `' . $this->_tableName . '` ORDER BY `date_updated` DESC LIMIT 0,1';
        $dbStatement = $this->_db->prepare($sql);
        if (!$dbStatement->execute()) {
            throw new \MaillogImporter\MaillogException('Could not execute the following SQL statement: ' . $sql);
        }
        // Get the last added row
        $row = $dbStatement->fetch(\PDO::FETCH_ASSOC);
        if (empty($row) || empty($row['date_updated'])) {
            return null;
        }
        return \DateTime::createFromFormat(self::DATETIME_FORMAT_MYSQL, $row['date_updated']);;
    }

    public function addData($postfixMessageId, array $data)
    {
        $this->_inserts[$postfixMessageId] = $data;
        if (count($this->_inserts) >= $this->_batchInsertsCount) {
            $this->_flushInserts();
        }
    }

    public function updateData($postfixMessageId, array $data)
    {
        if (array_key_exists($postfixMessageId, $this->_inserts)) {
            if (array_key_exists('info', $this->_inserts[$postfixMessageId]) && !empty($data['info'])) {
                $oldInfo = $this->_inserts[$postfixMessageId]['info'];
                $data['info'] = $oldInfo . (!empty($oldInfo) ? "\n" : '') . $data['info'];
            }
            $this->_inserts[$postfixMessageId] = array_replace($this->_inserts[$postfixMessageId], $data);
        }
        else {
            $this->_updateEmailData($postfixMessageId, $data['date_updated'], $data['status'], $data['info']);
        }
    }

    protected function _updateEmailData($postfixMessageId, $dateUpdated, $status, $info)
    {
        $sql = 'UPDATE `' . $this->_tableName . '` SET '
               . '`date_updated` = ' . $this->_db->quote($dateUpdated) . ', `status` = ' . $this->_db->quote($status);
        // Update 'info' only when there's something to add
        if (!empty($info)) {
            $info = $this->_db->quote($info);
            $sql .= ', `info` = IF(`info` = "", ' . $info . ' , CONCAT(`info`, "\n", ' . $info . '))';
        }
        $sql .= ' WHERE `postfix_message_id` = ' . $this->_db->quote($postfixMessageId);

        $dbStatement = $this->_db->prepare($sql);
        if (!$dbStatement->execute()) {
            throw new \MaillogImporter\MaillogException('Could not execute UPDATE database statement: "' . $sql . '"');
        }
    }

    protected function _flushInserts()
    {
        // Don't do anything if there's nothing to insert
        if (empty($this->_inserts)) {
            return;
        }
        // Generate SQL statement
        $sql = 'INSERT INTO `' . $this->_tableName . '` '
               . '(`email_message_id`, `postfix_message_id`, `date_updated`, `status`, `info`) VALUES' . "\r\n";
        $insertsCount = count($this->_inserts);
        $i = 0;
        foreach ($this->_inserts as $postfixMessageId => $insertData) {
            // Generate values
            $sql .= '('
                . $this->_db->quote($insertData['email_message_id']) . ', '
                . $this->_db->quote($postfixMessageId) . ', '
                . $this->_db->quote($insertData['date_updated']->format(self::DATETIME_FORMAT_MYSQL)) . ', '
                . $this->_db->quote($insertData['status']) . ', '
                . $this->_db->quote($insertData['info'])
            . ')';
            // Add commas only when needed
            $sql .= (++$i == $insertsCount) ? "\r\n" : ",\r\n";
        }
        // If key already exists do an UPDATE
        $sql .=
            ' ON DUPLICATE KEY UPDATE '
            . '`email_message_id` = VALUES(`email_message_id`), '
            . '`postfix_message_id` = VALUES(`postfix_message_id`), '
            . '`date_updated` = VALUES(`date_updated`), '
            . '`status` = VALUES(`status`), '
            . '`info` = VALUES(`info`)';

        // Execute INSERT statement and cleanup 'inserts' array
        $dbStatement = $this->_db->prepare($sql);
        if (!$dbStatement->execute()) {
            throw new \MaillogImporter\MaillogException('Could not execute INSERT database statement: "' . $sql . '"');
        }
        $this->_inserts = array();
    }
}