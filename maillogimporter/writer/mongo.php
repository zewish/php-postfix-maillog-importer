<?php

namespace MaillogImporter\Writer;

/**
 * Email log writer for MongoDB
 */

class Mongo implements WriterInterface
{
    /**
     * @var \MongoCollection
     */
    protected $_db = null;

    public function __construct()
    {
        // Connect to MongoDB
        $mongoConfig = Config('Mongo');
        $dbConnection = new \MongoClient($mongoConfig->connectionUri);
        // Load the requested connection
        $collectionName = $mongoConfig->collectionName;
        $this->_db = $dbConnection->selectDB($mongoConfig->dbName)->$collectionName;
        // Ensure we have descending index on 'date_updated' column
        $this->_db->ensureIndex(array('date_updated' => -1));
    }

    public function getLastDate()
    {
        // Get the last item
        $lastItem = $this->_db->find()->sort(array('date_updated' => -1))->limit(1)->getNext();
        if (empty($lastItem) || empty($lastItem['date_updated'])) {
            return null;
        }
        // Extract 'date_updated' column only
        $dbDateTime = $lastItem['date_updated'];
        if (empty($dbDateTime['date']) || empty($dbDateTime['timezone'])) {
            return null;
        }
        // Convert 'date_updated' to DateTime
        return new \DateTime($dbDateTime['date'], new \DateTimeZone($dbDateTime['timezone']));
    }

    public function addData($postfixMessageId, array $data)
    {
        return $this->updateData($postfixMessageId, $data);
    }

    public function updateData($postfixMessageId, array $data)
    {
        return $this->_db->update(
            array('_id' => $postfixMessageId),
            array('$set' => $data),
            array('upsert' => true)
        );
    }
}