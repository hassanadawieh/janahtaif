<?php

namespace App\Models;

class Clients_contact_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'clients_contact';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $clients_contact_table = $this->db->prefixTable('clients_contact');
        $clients_table = $this->db->prefixTable('clients');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $clients_contact_table.id=$id";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $clients_contact_table.client_id=$client_id";
        }

        $deleted_client = $this->_get_clean_value($options, "deleted_client");
        if (!$deleted_client) {
            $where .= " AND $clients_table.deleted=0";
        }

        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("tasks", $custom_fields, $clients_contact_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

         $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }


        $sql = "SELECT $clients_contact_table.*,$clients_table.company_name as company_name
        FROM $clients_contact_table
        LEFT JOIN $clients_table ON $clients_table.id=$clients_contact_table.client_id 
        WHERE $clients_contact_table.deleted=0 $where $custom_fields_where $limit_offset";
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

}
