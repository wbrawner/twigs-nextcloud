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

        if (!$schema->hasTable(BudgetMapper::$TABLE_NAME)) {
            $table = $schema->createTable(BudgetMapper::$TABLE_NAME);
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
                'length' => 1000,
            ]);

            $table->setPrimaryKey(['id']);
        }

        if (!$schema->hasTable(CategoryMapper::$TABLE_NAME)) {
            $table = $schema->createTable(CategoryMapper::$TABLE_NAME);
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
                'length' => 1000
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
            $table->addForeignKeyConstraint(BudgetMapper::$TABLE_NAME, ['budget_id'], ['id']);
        }

        if (!$schema->hasTable(TransactionMapper::$TABLE_NAME)) {
            $table = $schema->createTable(TransactionMapper::$TABLE_NAME);
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
            $table->addColumn('date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('created_date', 'datetime', [
                'notnull' => true,
            ]);
            $table->addColumn('updated_date', 'datetime', [
                'notnull' => false,
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
            $table->addForeignKeyConstraint(BudgetMapper::$TABLE_NAME, ['budget_id'], ['id']);
            $table->addForeignKeyConstraint(CategoryMapper::$TABLE_NAME, ['category_id'], ['id']);
        }

        if (!$schema->hasTable(UserPermissionMapper::$TABLE_NAME)) {
            $table = $schema->createTable(UserPermissionMapper::$TABLE_NAME);
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('budget_id', 'integer', [
                'notnull' => true,
            ]);
            $table->addColumn('user_id', 'string', [
                'notnull' => true,
                'length' => 64,
            ]);
            $table->addColumn('permission', 'integer', [
                'notnull' => true,
            ]);

            $table->setPrimaryKey(['id']);
            $table->addForeignKeyConstraint(BudgetMapper::$TABLE_NAME, ['budget_id'], ['id']);
            $table->addForeignKeyConstraint('oc_users', ['user_id'], ['uid']);
        }

        return $schema;
    }
}
