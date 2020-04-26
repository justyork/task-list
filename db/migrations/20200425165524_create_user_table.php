<?php

use Phinx\Migration\AbstractMigration;

class CreateUserTable extends AbstractMigration
{
    use \Core\Secure;

    public function change()
    {
        $table = $this->table('user');
        $table
            ->addColumn('name', 'string')
            ->addColumn('salt', 'string')
            ->addColumn('password', 'string')
            ->addColumn('auth_token', 'string', ['null' => true])
            ->create();

        // Add admin
        $salt = $this->createSalt();
        $table->insert([
            'name' => 'admin',
            'salt' => $salt,
            'password' => $this->passwordHash('123', $salt)
        ])->saveData();
    }
}
