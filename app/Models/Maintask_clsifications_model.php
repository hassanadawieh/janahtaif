<?php

namespace App\Models;

class Maintask_clsifications_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'maintask_clsifications';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $maintask_cls = $this->db->prefixTable('maintask_clsifications');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $maintask_cls.id=$id";
        }

       

        $sql = "SELECT $maintask_cls.*
        FROM $maintask_cls
        WHERE $maintask_cls.deleted=0 $where
        ORDER BY $maintask_cls.sort ASC";
        return $this->db->query($sql);
    }

    function get_max_sort_value() {
        $$maintask_cls = $this->db->prefixTable('task_status');

        $sql = "SELECT MAX($$maintask_cls.sort) as sort
        FROM $$maintask_cls
        WHERE $$maintask_cls.deleted=0";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            return $result->getRow()->sort;
        } else {
            return 0;
        }
    }

    

}
