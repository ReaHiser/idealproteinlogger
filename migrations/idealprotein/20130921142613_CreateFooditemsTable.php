<?php

class CreateFooditemsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $user = $this->create_table('fooditems');
        $user->column('category_id', 'integer');
        $user->column('name', 'string');
        $user->column('restricted', 'boolean');
        $user->column('foodtype_id', 'integer');

        $user->finish();
    }//up()

    public function down()
    {
        $this->drop_table('fooditems');
    }//down()
}
