<?php

class CreateCategoryTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $user = $this->create_table('category');
        $user->column('name', 'string');

        $user->finish();
    }//up()

    public function down()
    {
        $this->drop_table('category');
    }//down()
}
