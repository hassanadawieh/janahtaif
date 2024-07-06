<?php

namespace App\Models;

class Drivers_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'drivers';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $drivers_table = $this->db->prefixTable('drivers');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $drivers_table.id=$id";
        }
        $status = $this->_get_clean_value($options, "status");
        if ($status) {
            $where .= " AND $drivers_table.status=$status";
        }

         $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }


        $sql = "SELECT SQL_CALC_FOUND_ROWS $drivers_table.*
        FROM $drivers_table
        WHERE $drivers_table.deleted=0 $where $limit_offset";
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
        $drivers_table = $this->db->prefixTable('drivers');
        //$general_files_table = $this->db->prefixTable('general_files');
        //$users_table = $this->db->prefixTable('users');

        //get client files info to delete the files from directory 
        /*$client_files_sql = "SELECT * FROM $general_files_table WHERE $general_files_table.deleted=0 AND $general_files_table.client_id=$client_id; ";
        $client_files = $this->db->query($client_files_sql)->getResult();*/

        //delete the client and sub items
        //delete client
        $delete_driver_sql = "UPDATE $drivers_table SET $drivers_table.deleted=1 WHERE $drivers_table.id=$id; ";
        $this->db->query($delete_driver_sql);

        //delete contacts
        /*$delete_contacts_sql = "UPDATE $users_table SET $users_table.deleted=1 WHERE $users_table.client_id=$client_id; ";
        $this->db->query($delete_contacts_sql);*/

        //delete the project files from directory
        /*$file_path = get_general_file_path("client", $client_id);
        foreach ($client_files as $file) {
            delete_app_files($file_path, array(make_array_of_file($file)));
        }*/

        return true;
    }

}
