CREATE TABLE `email_log` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_message_id` CHAR(100) UNIQUE NOT NULL,
  `postfix_message_id` CHAR(11) UNIQUE NOT NULL,
  `date_updated` DATETIME NOT NULL,
  `status` ENUM ('unknown', 'sent', 'deferred', 'bounced', 'expired') NOT NULL DEFAULT 'unknown',
  `info` TEXT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
ALTER TABLE `email_log` ADD INDEX `date_updated` (`date_updated`);
