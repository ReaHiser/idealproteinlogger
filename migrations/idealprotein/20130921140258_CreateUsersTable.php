<?php

class CreateUsersTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $user = $this->create_table('user');
        $user->column('username', 'string');
        $user->column('password', 'string');
        $user->column('role', 'string');
        $user->column('full_name', 'string');
        $user->column('email', 'string');

        $user->finish();
    }//up()

    public function down()
    {
        $this->drop_table('user');
    }//down()
}
