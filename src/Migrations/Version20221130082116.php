<?php

declare(strict_types=1);

namespace OutputDataConfigToolkitBundle\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221130082116 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migrate columns with o_ prefix';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('bundle_outputdataconfigtoolkit_outputdefinition');

        $query = 'ALTER TABLE `%s` RENAME COLUMN `%s` TO `%s`';

        $this->addSql(sprintf($query, $table, 'o_classId', 'classId'));

    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('bundle_outputdataconfigtoolkit_outputdefinition');

        $query = 'ALTER TABLE `%s` RENAME COLUMN `%s` TO `%s`';

        $this->addSql(sprintf($query, $table, 'classId', 'o_classId'));

    }
}
