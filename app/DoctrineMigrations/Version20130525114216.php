<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20130525114216 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("CREATE TABLE `data` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `source_id` int(11) NOT NULL,
          `date` date NOT NULL,
          `value` int(11) NOT NULL,
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

        CREATE TABLE `source` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `title` varchar(255) NOT NULL DEFAULT '',
          `link` varchar(255) NOT NULL DEFAULT '',
          `unit` varchar(50) NOT NULL DEFAULT '',
          `created_at` timestamp NULL DEFAULT NULL,
          `updated_at` timestamp NULL DEFAULT NULL,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
