<?php
class FiveTakeTwoRow extends Pix_Table_Row
{
}

class FiveTakeTwo extends Pix_Table
{
    public function init()
    {
        $this->_name = 'five_take_two';
        $this->_rowClass = 'FiveTakeTwoRow';

        $this->_primary = 'id';

        $this->_columns['id'] = array('type' => 'int', 'size' => 11, 'unsigned' => true);
        $this->_columns['o1'] = array('type' => 'int', 'size' => 2);
        $this->_columns['o2'] = array('type' => 'int', 'size' => 2);
        $this->_columns['number_id'] = array('type' => 'int', 'size' => 11, 'unsigned' => true);

        $this->_relations['number'] = array('rel' => 'has_one', 'type' => 'Number', 'foreign_key' => 'number_id');
    }
}
