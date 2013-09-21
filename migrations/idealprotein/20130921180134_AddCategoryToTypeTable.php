<?php

class AddCategoryToTypeTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $this->add_column('type', 'category_id', 'integer');
    }//up()

    public function down()
    {
        $this->remove_column('type', 'category_id');
    }//down()
}
