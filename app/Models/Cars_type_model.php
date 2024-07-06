<?php

namespace App\Models;

class Cars_type_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'cars_type';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $cars_type_table = $this->db->prefixTable('cars_type');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $cars_type_table.id=$id";
        }

        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND $cars_type_table.status=$status";
        }

         $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }


        $sql = "SELECT SQL_CALC_FOUND_ROWS $cars_type_table.*
        FROM $cars_type_table
        WHERE $cars_type_table.deleted=0 $where $limit_offset";
        //return $this->db->query($sql);

        $raw_query = $this->db->query($sql);
        $total_rows = $this->db->query("SELECT FOUND_ROWS() as found_rows")->getRow();
        if ($limit) {
            return array(
                "data" => $raw_query->getResult(),
                "recordsTotal" => $total_rows->found_rows,
                "recordsFiltered" => $total_rows->found_rows,
            );
        } else {
            return $raw_query;
        }
    }


    function my_delete($id) {
        $cars_type_table = $this->db->prefixTable('cars_type');
        
        $delete_cars_type_sql = "UPDATE $cars_type_table SET $cars_type_table.deleted=1 WHERE $cars_type_table.id=$id; ";
        $this->db->query($delete_cars_type_sql);


        return true;
    }

}
