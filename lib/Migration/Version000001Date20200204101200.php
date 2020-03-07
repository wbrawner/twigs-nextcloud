<?php

namespace OCA\Twigs\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\SimpleMigrationStep;
use OCP\Migration\IOutput;
use OCA\Twigs\Db\BudgetMapper;
use OCA\Twigs\Db\CategoryMapper;
use OCA\Twigs\Db\TransactionMapper;
use OCA\Twigs\Db\UserPermissionMapper;

class Version000001Date20200204101200 extends SimpleMigrationStep {

    /**
     * @param IOutput $output
     * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
     * @param array $options
     * @return null|ISchemaWrapper
     */
    public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable(BudgetMapper::$tableName)) {
            $table = $schema->createTable(BudgetMapper::$tableName);
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('description', 'string', [
                'notnull' => true,
                'length' => 1000,
            ]);

            $table->setPrimaryKey(['id']);
        }

        if (!$schema->hasTable(CategoryMapper::$tableName)) {
            $table = $schema->createTable(CategoryMapper::$tableName);
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('amount', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('expense', 'boolean', [
                'notnull' => true,
            ]);
            $table->addColumn('budget_id', 'integer', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addForeignKeyConstraint(BudgetMapper::$tableName, ['budget_id'], ['id']);
        }

        if (!$schema->hasTable(TransactionMapper::$tableName)) {
            $table = $schema->createTable(TransactionMapper::$tableName);
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('name', 'string', [
                'notnull' => true,
                'length' => 200
            ]);
            $table->addColumn('description', 'string', [
                'notnull' => false,
                'length' => 1024,
            ]);
            $table->addColumn('amount', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('created_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('updated_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('expense', 'boolean', [
                'notnull' => true,
            ]);
            $table->addColumn('category_id', 'integer', [
                'notnull' => false,
            ]);
            $table->addColumn('budget_id', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('created_by', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('updated_by', 'integer', [
                'notnull' => false,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addForeignKeyConstraint(BudgetMapper::$tableName, ['budget_id'], ['id']);
            $table->addForeignKeyConstraint(CategoryMapper::$tableName, ['category_id'], ['id']);
        }

        if (!$schema->hasTable(UserPermissionMapper::$tableName)) {
            $table = $schema->createTable(UserPermissionMapper::$tableName);
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('budget_id', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('user_id', 'integer', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addForeignKeyConstraint(BudgetMapper::$tableName, ['budget_id'], ['id']);
            $table->addForeignKeyConstraint('oc_users', ['user_id'], ['uid']);
        }

        return $schema;
    }
}
