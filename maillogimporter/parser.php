<?php

namespace MaillogImporter;

/**
 * A class to parse 'maillog' lines and add them to the 'Db helper' queue
 */

class Parser
{
    /**
     * @var \MaillogImporter\Writer\WriterInterface
     */
    protected $_writer = null;

    public function __construct(\MaillogImporter\Writer\WriterInterface $writer)
    {
        $this->_writer = $writer;
    }

    public function parseLine($line)
    {
        if (!$this->_parseMessageIds($line)) {
            if (!$this->_parseSendStatuses($line)) {
                $this->_parseExpiredStatuses($line);
            }
        }
    }

    /**
     * Get message IDs: Postfix message ID and NC message ID, including date updated.
     * @param  string $line
     * @return boolean
     */
    protected function _parseMessageIds($line)
    {
        if (preg_match('/' . Config('Main')->dateTimeRegex . '(?:.*postfix\/cleanup\[\d+\]\: )([A-Z0-9]{11})(?:\: message-id=<)([a-zA-Z0-9]+)(?:@'
          . Config('Main')->messageIdHost . '>)/', $line, $matches)) {
            $postfixMessageId = $matches[2];
            $this->_writer->addData($postfixMessageId, array(
                'email_message_id' => $matches[3],
                'date_updated' => \MaillogImporter\DateConvertor::createFromPostfix($matches[1]),
                'status' => 'unknown',
                'info' => '',
            ));
            return true;
        }
        return false;
    }

    /**
     * Get statuses: success/deferred/bounced
     * @param  string $line
     * @return boolean
     */
    protected function _parseSendStatuses($line)
    {
        if (preg_match('/' . Config('Main')->dateTimeRegex . '(?:.*postfix\/smtp\[\d+\]\: )([A-Z0-9]{11})(?:\: to=<.*>.*dsn=[\d\.]+, status=)([a-zA-Z]+)(?: \()(.*)(?:\))/', $line, $matches)) {
            $this->_updateData($matches);
            return true;
        }
        return false;
    }

    /**
     * Get statuses: expired (and probably something else from 'qmgr')
     * @param  string $line
     * @return boolean
     */
    protected function _parseExpiredStatuses($line)
    {
        if (preg_match('/' . Config('Main')->dateTimeRegex . '(?:.*postfix\/qmgr\[\d+\]\: )([A-Z0-9]{11})(?:\: from=<.*>, status=)([a-zA-Z]+)/', $line, $matches)) {
            $this->_updateData($matches);
            return true;
        }
        return false;
    }

    protected function _updateData(array $matches)
    {
        if (empty($matches[4])) {
            $matches[4] = null;
        }

        $postfixMessageId = $matches[2];
        $this->_writer->updateData($postfixMessageId, array(
            'date_updated' => \MaillogImporter\DateConvertor::createFromPostfix($matches[1]),
            'status' => $matches[3],
            'info' => $matches[4]
        ));
    }
}