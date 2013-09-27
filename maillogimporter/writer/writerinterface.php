<?php

namespace MaillogImporter\Writer;

interface WriterInterface
{
    /**
     * Returns a DateTime object for the newest database item
     * @return \DateTime
     */
    function getLastDate();

    /**
     * Add new message to be logged
     * @param string $postfixMessageId
     * @param array  $data
     */
    function addData($postfixMessageId, array $data);

    /**
     * Update the data of an existing message
     * @param string $postfixMessageId
     * @param array  $data
     */
    function updateData($postfixMessageId, array $data);
}