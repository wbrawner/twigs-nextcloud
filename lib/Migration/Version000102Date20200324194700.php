<?php

namespace OCA\Twigs\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use OCA\Twigs\Db\CategoryMapper;
use OCA\Twigs\Db\TransactionMapper;

class Version000102Date20200324194700 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if ($schema->hasTable(CategoryMapper::TABLE_NAME)) {
            $table = $schema->getTable(CategoryMapper::TABLE_NAME);
            $table->changeColumn('description', [
                'notnull' => false,
            ]);
        }

        return $schema;
    }
}
