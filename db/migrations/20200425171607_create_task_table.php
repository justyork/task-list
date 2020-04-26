<?php

use Phinx\Migration\AbstractMigration;

class CreateTaskTable extends AbstractMigration
{

    public function change()
    {
        $this->table('task')
            ->addColumn('email', 'string')
            ->addColumn('name', 'string')
            ->addColumn('text', 'text')
            ->addColumn('is_updated', 'boolean', ['default' => false])
            ->addColumn('status', 'boolean', ['default' => false])
            ->addTimestamps()
            ->create();
    }
}
