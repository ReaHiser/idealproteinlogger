<?php

class CreateTypeTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $user = $this->create_table('type');
        $user->column('name', 'string');

        $user->finish();
    }//up()

    public function down()
    {
        $this->drop_table('type');
    }//down()
}
