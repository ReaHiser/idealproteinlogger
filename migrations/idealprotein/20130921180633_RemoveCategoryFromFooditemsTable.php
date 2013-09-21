<?php

class RemoveCategoryFromFooditemsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->remove_column('fooditems', 'category_id');
    }//up()

    public function down()
    {
        $this->add_column('fooditems', 'category_id', 'integer');
    }//down()
}
