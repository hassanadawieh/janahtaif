<?php

namespace App\Models;

class Tasks_model extends Crud_model
{

    protected $table = null;

    function __construct()
    {
        $this->table = 'tasks';
        parent::__construct($this->table);
        parent::init_activity_log("task", "title", "project", "project_id");
    }

    function schema()
    {
        return array(
            "id" => array(
                "label" => app_lang("id"),
                "type" => "int"
            ),
            "title" => array(
                "label" => app_lang("title"),
                "type" => "text"
            ),
            "description" => array(
                "label" => app_lang("description"),
                "type" => "text"
            ),
            "assigned_to" => array(
                "label" => app_lang("assigned_to"),
                "type" => "foreign_key",
                "linked_model" => model("App\Models\Users_model"),
                "label_fields" => array("first_name", "last_name"),
            ),
            "collaborators" => array(
                "label" => app_lang("collaborators"),
                "type" => "foreign_key",
                "link_type" => "user_group_list",
                "linked_model" => model("App\Models\Users_model"),
                "label_fields" => array("user_group_name"),
            ),

            "labels" => array(
                "label" => app_lang("labels"),
                "type" => "foreign_key",
                "link_type" => "label_group_list",
                "linked_model" => model("App\Models\Labels_model"),
                "label_fields" => array("label_group_name"),
            ),
            "status" => array(
                "label" => app_lang("status"),
                "type" => "language_key" //we'are not using this field from 1.9 but don't delete it for existing data.
            ),
            "status_id" => array(
                "label" => app_lang("status"),
                "type" => "foreign_key",
                "linked_model" => model("App\Models\Task_status_model"),
                "label_fields" => array("title"),
            ),
            "start_date" => array(
                "label" => app_lang("start_date"),
                "type" => "date"
            ),
            "deadline" => array(
                "label" => app_lang("deadline"),
                "type" => "date"
            ),
            "project_id" => array(
                "label" => app_lang("project"),
                "type" => "foreign_key"
            ),
            "points" => array(
                "label" => app_lang("points"),
                "type" => "int"
            ),
            "deleted" => array(
                "label" => app_lang("deleted"),
                "type" => "int"
            ),
            "sort" => array(
                "label" => app_lang("priority"),
                "type" => "int"
            ),

            "no_of_cycles" => array(
                "label" => app_lang("cycles"),
                "type" => "int"
            ),
            "recurring" => array(
                "label" => app_lang("recurring"),
                "type" => "int"
            ),
            "repeat_type" => array(
                "label" => app_lang("repeat_type"),
                "type" => "text"
            ),
            "repeat_every" => array(
                "label" => app_lang("repeat_every"),
                "type" => "int"
            ),
            "priority_id" => array(
                "label" => app_lang("priority"),
                "type" => "foreign_key",
                "linked_model" => model("App\Models\Task_priority_model"),
                "label_fields" => array("title"),
            ),
        );
    }

    function get_details($options = array())
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $users_table = $this->db->prefixTable('users');
        $projects = $this->db->prefixTable('projects');
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $cities = $this->db->prefixTable('cities');
        $clients = $this->db->prefixTable('clients');
        $clients_contact = $this->db->prefixTable('clients_contact');
        $notifications_table = $this->db->prefixTable("notifications");
        $maintask_clsifications = $this->db->prefixTable("maintask_clsifications");

        $where = "";

        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $tasks_table.id=$id";
        }

        $project_id = $this->_get_clean_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $tasks_table.project_id=$project_id";
        }

        $filter = $this->_get_clean_value($options, "filter");
        if ($filter) {
            if ($filter == "no_invoice") {
                $where .= " AND ($tasks_table.invoice_number='' OR $tasks_table.invoice_number IS NULL)";
            }
            if ($filter == "no_christening_number") {
                $where .= " AND ($tasks_table.christening_number='' OR $tasks_table.christening_number IS NULL)";
            }
            if ($filter == "no_project") {
                $where .= " AND ($tasks_table.project_id=0 OR $tasks_table.project_id IS NULL)";
            }
            if ($filter == "tasks_deleted") {
                $where .= " AND ($sub_tasks_table.deleted=1)";
            }
            if ($filter == "tasks_unpaid_driver") {
                $where .= " AND ($sub_tasks_table.car_expens_stmnt IS NULL OR $sub_tasks_table.car_expens_stmnt = '')";
            }
        }


        $parent_task_id = $this->_get_clean_value($options, "parent_task_id");
        if ($parent_task_id) {
            $where .= " AND $tasks_table.parent_task_id=$parent_task_id";
        }
        $sub_tsk_selected_date = date("Y");
        $selected_year = $this->_get_clean_value($options, "selected_year");
        $ci = new \App\Controllers\Security_Controller(false);
        $this_year = $ci->session->get('selected_year');
        if ($this_year != 1 && $selected_year) {
            $where .= " AND YEAR($tasks_table.created_date)=$this_year";
        }


        $task_ids = $this->_get_clean_value($options, "task_ids");
        if ($task_ids) {
            $where .= " AND $tasks_table.ID IN($task_ids)";
        }




        $status_ids = $this->_get_clean_value($options, "status_ids");
        if ($status_ids) {
            $where .= " AND FIND_IN_SET($tasks_table.is_closed,'$status_ids')";
        }

        $update_status = $this->_get_clean_value($options, "update_status");
        if ($update_status) {
            $where .= " AND $tasks_table.is_closed=$update_status";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $tasks_table.client_id=$client_id";
        }

        $exclude_status_id = $this->_get_clean_value($options, "exclude_status_id");
        if ($exclude_status_id) {
            $where .= " AND $tasks_table.status_id!=$exclude_status_id ";
        }

        $assigned_to = $this->_get_clean_value($options, "assigned_to");
        if ($assigned_to) {
            $where .= " AND $tasks_table.created_by=$assigned_to";
        }

        $cls_id = $this->_get_clean_value($options, "cls_id");
        if ($cls_id) {
            $where .= " AND $tasks_table.cls_id=$cls_id";
        }

        $deleted_client = $this->_get_clean_value($options, "deleted_client");
        if (!$deleted_client) {
            $where .= " AND $clients.deleted=0";
        }

        $client_status = $this->_get_clean_value($options, "client_status");
        if ($client_status) {
            $where .= " AND $clients.status=$client_status";
        }


        $specific_user_id = $this->_get_clean_value($options, "specific_user_id");
        if ($specific_user_id) {
            $where .= " AND ($tasks_table.created_by=$specific_user_id OR FIND_IN_SET('$specific_user_id', $tasks_table.collaborators))";
        }

        $show_assigned_tasks_only_user_id = $this->_get_clean_value($options, "show_assigned_tasks_only_user_id");
        if ($show_assigned_tasks_only_user_id) {
            $where .= " AND ($tasks_table.created_by=$show_assigned_tasks_only_user_id OR FIND_IN_SET('$show_assigned_tasks_only_user_id', $tasks_table.collaborators))";
        }



        $task_status_id = $this->_get_clean_value($options, "task_status_id");
        if ($task_status_id) {
            $where .= " AND $tasks_table.status_id=$task_status_id";
        }



        $exclude_reminder_date = $this->_get_clean_value($options, "exclude_reminder_date");
        if ($exclude_reminder_date) {
            $where .= " AND ($tasks_table.reminder_date !='$exclude_reminder_date') ";
        }


        $order = "";
        $sort_by_project = $this->_get_clean_value($options, "sort_by_project");
        if ($sort_by_project) {
            $order = " ORDER BY $tasks_table.project_id ASC";
        }

        $extra_left_join = "";
        /* $project_member_id = $this->_get_clean_value($options, "project_member_id");
        if ($project_member_id) {
            $where .= " AND $project_members_table.user_id=$project_member_id";
            $extra_left_join = " LEFT JOIN $project_members_table ON $tasks_table.project_id= $project_members_table.project_id AND $project_members_table.deleted=0 AND $project_members_table.user_id=$project_member_id";
        }*/

        $quick_filter = $this->_get_clean_value($options, "quick_filter");
        if ($quick_filter) {
            $where .= $this->make_quick_filter_query($quick_filter, $tasks_table);
        }

        $unread_status_user_id = $this->_get_clean_value($options, "unread_status_user_id");
        if (!$unread_status_user_id) {
            $unread_status_user_id = 0;
        }


        $select_labels_data_query = $this->get_labels_data_query();

        $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }

        $available_order_by_list = array(
            "id" => $tasks_table . ".id",
            "title" => $tasks_table . ".title",
            "created_date" => $tasks_table . ".created_date",
            "client_name" => $tasks_table . ".client_id",
            "is_closed" => $tasks_table . ".is_closed",

            "created_by" => "assigned_to_user",
            "status" => $tasks_table . ".status_id",
            "project" => $projects . ".title",
        );

        $order_by = get_array_value($available_order_by_list, $this->_get_clean_value($options, "order_by"));

        if ($order_by) {
            $order_dir = $this->_get_clean_value($options, "order_dir");
            $order = " ORDER BY $order_by $order_dir ";
        }

        $search_by = get_array_value($options, "search_by");
        if ($search_by) {
            $search_by = $this->db->escapeLikeString($search_by);

            if (strpos($search_by, '#') !== false) {
                //get sub tasks of this task
                $search_by = substr($search_by, 1);
                $where .= " AND ($tasks_table.id='$search_by' OR $tasks_table.parent_task_id='$search_by')";
            } else {
                $where .= " AND (";
                $where .= " $tasks_table.id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR $tasks_table.description LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR $tasks_table.christening_number LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR $tasks_table.invoice_number LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR $tasks_table.created_date LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR CONCAT($clients_contact.first_name, ' ', $clients_contact.last_name) LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR CONCAT($users_table.first_name, ' ', $users_table.last_name) LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR $projects.title LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR $clients.company_name LIKE '%$search_by%' ESCAPE '!' ";

                $where .= $this->get_custom_field_search_query($tasks_table, "tasks", $search_by);
                $where .= " )";
            }
        }

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("tasks", $custom_fields, $tasks_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        $this->db->query('SET SQL_BIG_SELECTS=1');

        $sql = "SELECT SQL_CALC_FOUND_ROWS $tasks_table.*, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS assigned_to_user,$maintask_clsifications.color AS cls_color,$maintask_clsifications.title AS cls_title, CONCAT($clients_contact.first_name, ' ',$clients_contact.last_name) AS clients_contact_name, $users_table.image as assigned_to_avatar, $users_table.user_type, $cities.city_name AS city_name,$projects.client_id as project_client_id,$clients.status AS client_status, $clients.company_name AS company_name,
                    $projects.title AS project_title, 
                    (SELECT COUNT($sub_tasks_table.id) from $sub_tasks_table where $sub_tasks_table.deleted=0 AND $sub_tasks_table.pnt_task_id=$tasks_table.id AND YEAR($sub_tasks_table.created_at)=$sub_tsk_selected_date  GROUP BY $tasks_table.id,$sub_tasks_table.pnt_task_id) AS sub_task_count,
                     notification_table.task_id AS unread, $select_labels_data_query $select_custom_fieds 
                        
        FROM $tasks_table
        LEFT JOIN $users_table ON $users_table.id= $tasks_table.created_by
        LEFT JOIN $projects ON $tasks_table.project_id=$projects.id 
        LEFT JOIN $cities ON $tasks_table.city_id=$cities.id 
        LEFT JOIN $maintask_clsifications ON $tasks_table.cls_id=$maintask_clsifications.id 
        LEFT JOIN $clients ON $tasks_table.client_id=$clients.id 
        LEFT JOIN $clients_contact ON $tasks_table.client_contact_id=$clients_contact.id 
        
        LEFT JOIN (SELECT $notifications_table.task_id FROM $notifications_table WHERE $notifications_table.deleted=0 AND $notifications_table.event='project_task_commented' AND !FIND_IN_SET('$unread_status_user_id', $notifications_table.read_by) AND $notifications_table.user_id!=$unread_status_user_id  GROUP BY $notifications_table.task_id) AS notification_table ON notification_table.task_id = $tasks_table.id
        $extra_left_join 
        $join_custom_fieds 
        WHERE $tasks_table.deleted=0  $where  $custom_fields_where 
        $order $limit_offset";

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


    function get_details_new($options = array())
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $task_status_table = $this->db->prefixTable('task_status');
        $users_table = $this->db->prefixTable('users');
        $projects = $this->db->prefixTable('projects');
        $cities = $this->db->prefixTable('cities');
        $suppliers = $this->db->prefixTable('suppliers');
        $drivers = $this->db->prefixTable('drivers');
        $task_priority_table = $this->db->prefixTable('task_priority');
        $cars_type_table = $this->db->prefixTable('cars_type');
        $clients = $this->db->prefixTable('clients');
        $clients_contact = $this->db->prefixTable('clients_contact');
        $notifications_table = $this->db->prefixTable("notifications");
        $maintask_clsifications = $this->db->prefixTable("maintask_clsifications");

        $where = "";

        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $sub_tasks_table.id=$id";
        }

        $project_id = $this->_get_clean_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $tasks_table.project_id=$project_id";
        }






        $task_ids = $this->_get_clean_value($options, "task_ids");
        if ($task_ids) {
            $where .= " AND $tasks_table.id IN($task_ids)";
        }

        $sub_tsk_selected_date = date("Y");
        $selected_year = $this->_get_clean_value($options, "selected_year");
        if ($selected_year) {
            $where .= " AND YEAR($tasks_table.created_date)=$selected_year";
            $sub_tsk_selected_date = $selected_year;
        } else {
            $this_year = date("Y");
            $where .= " AND YEAR($tasks_table.created_date)=$this_year";
        }



        $update_status = $this->_get_clean_value($options, "update_status");
        if ($update_status) {
            $where .= " AND $tasks_table.is_closed=$update_status";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $tasks_table.client_id=$client_id";
        }

        $exclude_status_id = $this->_get_clean_value($options, "exclude_status_id");
        if ($exclude_status_id) {
            $where .= " AND $tasks_table.status_id!=$exclude_status_id ";
        }

        $assigned_to = $this->_get_clean_value($options, "assigned_to");
        if ($assigned_to) {
            $where .= " AND $tasks_table.created_by=$assigned_to";
        }

        $cls_id = $this->_get_clean_value($options, "cls_id");
        if ($cls_id) {
            $where .= " AND $tasks_table.cls_id=$cls_id";
        }

        $deleted_client = $this->_get_clean_value($options, "deleted_client");
        if (!$deleted_client) {
            $where .= " AND $clients.deleted=0";
        }

        $client_status = $this->_get_clean_value($options, "client_status");
        if ($client_status) {
            $where .= " AND $clients.status=$client_status";
        }


        $specific_user_id = $this->_get_clean_value($options, "specific_user_id");
        if ($specific_user_id) {
            $where .= " AND ($tasks_table.created_by=$specific_user_id OR FIND_IN_SET('$specific_user_id', $tasks_table.collaborators))";
        }

        $show_assigned_tasks_only_user_id = $this->_get_clean_value($options, "show_assigned_tasks_only_user_id");
        if ($show_assigned_tasks_only_user_id) {
            $where .= " AND ($tasks_table.created_by=$show_assigned_tasks_only_user_id OR FIND_IN_SET('$show_assigned_tasks_only_user_id', $tasks_table.collaborators))";
        }




        $task_id = $this->_get_clean_value($options, "task_id");
        if ($task_id && $task_id != 0) {
            $where .= " AND $sub_tasks_table.pnt_task_id=$task_id";
        }

        $pnt_task_id = $this->_get_clean_value($options, "pnt_task_id");
        if ($pnt_task_id) {
            $where .= " AND $sub_tasks_table.pnt_task_id=$pnt_task_id";
        }

        $specific_user_id = $this->_get_clean_value($options, "specific_user_id");
        if ($specific_user_id) {
            $where .= " AND $sub_tasks_table.created_by=$specific_user_id";
        }

        $guest_nm_f = $this->_get_clean_value($options, "guest_nm_f");
        if ($guest_nm_f) {
            $where .= " AND $sub_tasks_table.guest_nm LIKE '%$guest_nm_f%' ESCAPE '!'";
        }

        $guest_phone_f = $this->_get_clean_value($options, "guest_phone_f");
        if ($guest_phone_f) {
            $where .= " AND $sub_tasks_table.guest_phone LIKE '%$guest_phone_f%' ESCAPE '!'";
        }


        $company_name_f = $this->_get_clean_value($options, "company_name_f");
        if ($company_name_f) {
            $where .= " AND $clients.company_name LIKE '%$company_name_f%' ESCAPE '!'";
        }
        $clients_contact_f = $this->_get_clean_value($options, "clients_contact_f");
        if ($clients_contact_f) {
            $where .= " AND CONCAT($clients_contact.first_name, ' ', $clients_contact.last_name) LIKE '%$clients_contact_f%' ESCAPE '!'";
        }
        $christ_num_f = $this->_get_clean_value($options, "christ_num_f");
        if ($christ_num_f) {
            $where .= " AND $task_table.christening_number LIKE '%$christ_num_f%' ESCAPE '!'";
        }

        $inv_num_f = $this->_get_clean_value($options, "inv_num_f");
        if ($inv_num_f) {
            $where .= " AND $task_table.invoice_number LIKE '%$inv_num_f%' ESCAPE '!'";
        }
        $city_name_f = $this->_get_clean_value($options, "city_name_f");
        if ($city_name_f) {
            $where .= " AND $cities.city_name LIKE '%$city_name_f%' ESCAPE '!'";
        }

        $driver_nm_f = $this->_get_clean_value($options, "driver_nm_f");
        if ($driver_nm_f) {
            $where .= " AND $drivers.driver_nm LIKE '%$driver_nm_f%' ESCAPE '!' ";
        }

        $car_type_f = $this->_get_clean_value($options, "car_type_f");
        if ($car_type_f) {
            $where .= " AND $cars_type_table.car_type LIKE '%$car_type_f%' ESCAPE '!' ";
        }

        $out_date_f = $this->_get_clean_value($options, "out_date_f");
        if ($out_date_f) {
            $where .= " AND $sub_tasks_table.out_date LIKE '%$out_date_f%' ESCAPE '!'";
        }

        $tmp_return_date_f = $this->_get_clean_value($options, "tmp_return_date_f");
        if ($tmp_return_date_f) {
            $where .= " AND $sub_tasks_table.tmp_return_date LIKE '%$tmp_return_date_f%' ESCAPE '!'";
        }

        $sales_act_return_date_f = $this->_get_clean_value($options, "sales_act_return_date_f");
        if ($sales_act_return_date_f) {
            $where .= " AND $sub_tasks_table.sales_act_return_date LIKE '%$sales_act_return_date_f%' ESCAPE '!'";
        }

        // $inv_day_count_f = $this->_get_clean_value($options, "inv_day_count_f");
        // if ($inv_day_count_f) {
        //     $where .= " AND $sub_tasks_table.inv_day_count=$inv_day_count_f";
        // }
        $booking_period_f = $this->_get_clean_value($options, "booking_period_f");
        if ($booking_period_f) {
            $where .= " AND $sub_tasks_table.booking_period=$booking_period_f";
        }

        $pnt_task_id_f = $this->_get_clean_value($options, "pnt_task_id_f");
        if ($pnt_task_id_f) {
            $where .= " AND $sub_tasks_table.pnt_task_id=$pnt_task_id_f";
        }

        $note_f = $this->_get_clean_value($options, "note_f");
        if ($note_f) {
            $where .= " AND $sub_tasks_table.note LIKE '%$note_f%' ESCAPE '!'";
        }

        $created_by_f = $this->_get_clean_value($options, "created_by_f");
        if ($created_by_f) {
            $where .= " AND CONCAT($users_table.first_name, ' ', $users_table.last_name) LIKE '%$created_by_f%' ESCAPE '!'";
        }

        $supplier_id = $this->_get_clean_value($options, "supplier_id");
        if ($supplier_id) {
            $where .= " AND $sub_tasks_table.supplier_id=$supplier_id";
        }




        $city_id = $this->_get_clean_value($options, "city_id");
        if ($city_id) {
            $where .= " AND $sub_tasks_table.city_id=$city_id";
        }

        $cls_id = $this->_get_clean_value($options, "cls_id");
        if ($cls_id) {
            $where .= " AND $task_table.cls_id=$cls_id";
        }

        $driver_id = $this->_get_clean_value($options, "driver_id");
        if ($driver_id) {
            $where .= " AND $sub_tasks_table.driver_id=$driver_id";
        }
        $car_type_id = $this->_get_clean_value($options, "car_type_id");
        if ($car_type_id) {
            $where .= " AND $sub_tasks_table.car_type_id=$car_type_id";
        }

        $service_type = $this->_get_clean_value($options, "service_type");
        if ($service_type) {
            $where .= " AND $sub_tasks_table.service_type='$service_type'";
        }

        $selected_year = 2024;
        if ($selected_year) {
            $where .= " AND ($sub_tasks_table.created_at IS NULL OR YEAR($sub_tasks_table.created_at)=$selected_year)";
        } else {
            $this_year = date("Y");
            $where .= " AND ($sub_tasks_table.created_at IS NULL OR YEAR($sub_tasks_table.created_at)=$this_year)";
        }


        $deleted_client = $this->_get_clean_value($options, "deleted_client");
        if (!$deleted_client) {
            $where .= " AND $clients.deleted=0";
        }

        $start_date = $this->_get_clean_value($options, "start_date");
        $end_date = $this->_get_clean_value($options, "end_date");
        if ($start_date && $end_date) {
            $where .= " AND ($sub_tasks_table.created_at BETWEEN '$start_date' AND '$end_date') ";
        }



        $filter = $this->_get_clean_value($options, "filter");
        if ($filter) {
            if ($filter == "no_supplier") {
                $where .= " AND ($sub_tasks_table.supplier_id=0 OR $sub_tasks_table.supplier_id IS NULL)";
            }
            if ($filter == "wait_inv") {
                $where .= " AND ($sub_tasks_table.rec_inv_status='wait_inv')";
            }
            if ($filter == "rec_inv") {
                $where .= " AND ($sub_tasks_table.rec_inv_status='rec_inv')";
            }
            if ($filter == "no_act_return_date") {
                $where .= " AND ($sub_tasks_table.act_return_date='0000-00-00' OR $sub_tasks_table.act_return_date IS NULL)";
            }
            if ($filter == "no_act_out_time") {
                $where .= " AND ($sub_tasks_table.act_out_time='00:00:01' OR $sub_tasks_table.act_out_time IS NULL)";
            }
            if ($filter == "24houer") {
                $where .= " AND ($sub_tasks_table.status_id=1 AND DATE($sub_tasks_table.act_out_date) - INTERVAL 24 HOUR <  NOW())";
            }
        }

        $mang = $this->_get_clean_value($options, "mang");
        $is_admin = $this->_get_clean_value($options, "is_admin");
        /*$drr=" IF $sub_tasks_table.status_id =5 THEN 5
               ELSEIF ($sub_tasks_table.out_date='0000-00-00' OR $sub_tasks_table.out_date is NULL) THEN 2
               ELSEIF (DATE_FORMAT(CONCAT($sub_tasks_table.out_date, ' ',$sub_tasks_table.exp_out_time),'%m/%d/%Y %H:%i') >  DATE_FORMAT(NOW(),'%m/%d/%Y %H:%i')) THEN 1
               ELSEIF (DATE($sub_tasks_table.tmp_return_date) >  NOW()) THEN 3

               DATE_FORMAT($sub_tasks_table.tmp_return_date,'%m/%d/%Y')
        ";*/

        $supplier_status = $mang == "reserv" ? " (CASE
            WHEN $sub_tasks_table.status_id =5 THEN 5
            WHEN ((($sub_tasks_table.out_date='0000-00-00' OR $sub_tasks_table.out_date is NULL OR $sub_tasks_table.out_date!='0000-00-00' OR $sub_tasks_table.out_date is NOT NULL)
            OR ($sub_tasks_table.exp_out_time='00:00:01' OR $sub_tasks_table.exp_out_time is NULL OR $sub_tasks_table.exp_out_time!='00:00:01' OR $sub_tasks_table.exp_out_time is NOT NULL)) 
            AND ($sub_tasks_table.act_out_date='0000-00-00' OR $sub_tasks_table.act_out_date is NULL)) THEN 1

            

            WHEN ((($sub_tasks_table.act_return_date!='0000-00-00' OR $sub_tasks_table.act_return_date is NOT NULL)
             OR ($sub_tasks_table.act_return_time!='00:00:01' OR $sub_tasks_table.act_return_time is NOT NULL))
             AND (($sub_tasks_table.tmp_return_date is NULL OR $sub_tasks_table.tmp_return_date='0000-00-00') 
             OR( Format($sub_tasks_table.tmp_return_date,'d/mm/yyyy') >  Format(NOW(),'d/mm/yyyy')))
             AND ($sub_tasks_table.sales_act_return_date='0000-00-00' OR $sub_tasks_table.sales_act_return_date is NULL)) THEN 3

             WHEN ($sub_tasks_table.tmp_return_date is NOT NULL AND $sub_tasks_table.tmp_return_date!='0000-00-00' AND Format($sub_tasks_table.tmp_return_date,'d/mm/yyyy') <=  Format(NOW(),'d/mm/yyyy') AND ($sub_tasks_table.sales_act_return_date='0000-00-00' OR $sub_tasks_table.sales_act_return_date is NULL)) 
              THEN 2

             WHEN ($sub_tasks_table.sales_act_return_date!='0000-00-00' AND $sub_tasks_table.sales_act_return_date is NOT NULL) AND  ($sub_tasks_table.service_type!='deliver' AND $sub_tasks_table.inv_day_count=0 OR $sub_tasks_table.car_type_id=0 ) THEN 6
             
             

            WHEN ($sub_tasks_table.sales_act_return_date!='0000-00-00' AND $sub_tasks_table.sales_act_return_date is NOT NULL)
             AND ($sub_tasks_table.out_date!='0000-00-00' AND $sub_tasks_table.out_date is NOT NULL)
             
             AND  ($sub_tasks_table.service_type='deliver' AND $sub_tasks_table.car_type_id!=0) OR ($sub_tasks_table.inv_day_count!=0 AND $sub_tasks_table.car_type_id!=0 ) THEN 4

           
            
            ELSE 6
            END )" : " (CASE
            WHEN $sub_tasks_table.status_id =5 THEN 5
            WHEN $sub_tasks_table.supplier_id =0 
            OR ($sub_tasks_table.act_out_date='0000-00-00' OR $sub_tasks_table.act_out_date is NULL)
            OR ($sub_tasks_table.act_out_time='00:00:01' OR $sub_tasks_table.act_out_time is NULL) THEN 1

            WHEN ($sub_tasks_table.act_out_date!='0000-00-00' OR $sub_tasks_table.act_out_date is NOT NULL)
            AND ($sub_tasks_table.act_out_time!='00:00:01' OR $sub_tasks_table.act_out_time is NOT NULL)
            AND (($sub_tasks_table.act_return_date='0000-00-00' OR $sub_tasks_table.act_return_date is NULL)
            OR ($sub_tasks_table.act_return_time='00:00:01' OR $sub_tasks_table.act_return_time is NULL)) THEN 3

            WHEN (($sub_tasks_table.act_return_date!='0000-00-00' OR $sub_tasks_table.act_return_date is NOT NULL)
            OR ($sub_tasks_table.act_return_time!='00:00:01' OR $sub_tasks_table.act_return_time is NOT NULL))
            AND ($sub_tasks_table.rec_inv_status='wait_inv' AND $sub_tasks_table.service_type!='deliver') THEN 2
            
            WHEN ($sub_tasks_table.day_count='0' AND $sub_tasks_table.rec_inv_status='wait_inv' AND $sub_tasks_table.service_type!='deliver')
            OR(($sub_tasks_table.rec_inv_status='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status='') THEN 6

            WHEN ($sub_tasks_table.day_count!='0' AND $sub_tasks_table.service_type!='deliver')
           
            OR(($sub_tasks_table.rec_inv_status!='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status!='') THEN 4
           
            
            ELSE 6
            END ) ";

        /*$supplier_status2=" (CASE
            WHEN $sub_tasks_table.status_id =5 THEN 5
            WHEN $sub_tasks_table.supplier_id =0 OR $sub_tasks_table.car_status is NULL 
            OR ($sub_tasks_table.act_out_date='0000-00-00' OR $sub_tasks_table.act_out_date is NULL)
            OR ($sub_tasks_table.act_out_time='00:00:01' OR $sub_tasks_table.act_out_time is NULL) 
            OR ($sub_tasks_table.act_return_date='0000-00-00' OR $sub_tasks_table.act_return_date is NULL)
            OR ($sub_tasks_table.act_return_time='00:00:01' OR $sub_tasks_table.act_return_time is NULL)
            OR $sub_tasks_table.rec_inv_status='wait_inv'
            OR ($sub_tasks_table.day_count='0' AND $sub_tasks_table.service_type!='deliver') THEN 2

            WHEN (DATE_FORMAT(CONCAT($sub_tasks_table.act_out_date, ' ',$sub_tasks_table.act_out_time),'%m/%d/%Y %H:%i') >  DATE_FORMAT(NOW(),'%m/%d/%Y %H:%i')) THEN 1

            WHEN ((DATE_FORMAT(CONCAT($sub_tasks_table.act_out_date, ' ',$sub_tasks_table.act_out_time),'%m/%d/%Y %H:%i') <=  DATE_FORMAT(NOW(),'%m/%d/%Y %H:%i')) AND (DATE_FORMAT(CONCAT($sub_tasks_table.act_return_date, ' ',$sub_tasks_table.act_return_time), '%m/%d/%Y %H:%i') >  DATE_FORMAT(NOW(),'%m/%d/%Y %H:%i')) )THEN 3

            WHEN (DATE_FORMAT(CONCAT($sub_tasks_table.act_return_date, ' ',$sub_tasks_table.act_return_time), '%m/%d/%Y %H:%i') <=  NOW()) THEN 4

            
            ELSE 4
            END ) ";*/



        $out_date = $this->_get_clean_value($options, "out_date");
        if ($out_date && is_date_exists($out_date) && $out_date != '1') {
            $where .= " AND (DATE($sub_tasks_table.out_date)='$out_date')";
        }
        $tmp_return_date = $this->_get_clean_value($options, "tmp_return_date");
        if ($tmp_return_date && is_date_exists($tmp_return_date) && $tmp_return_date != '1') {

            $where .= " AND (DATE($sub_tasks_table.tmp_return_date)='$tmp_return_date')";
        }
        $act_out_date = $this->_get_clean_value($options, "act_out_date");
        if ($act_out_date && is_date_exists($act_out_date) && $act_out_date != '1') {
            $where .= " AND ($sub_tasks_table.act_out_date IS NOT NULL  AND $sub_tasks_table.act_out_date='$act_out_date')";
        }

        $sales_act_return_date = $this->_get_clean_value($options, "sales_act_return_date");
        if ($sales_act_return_date && is_date_exists($sales_act_return_date) && $sales_act_return_date != '2') {

            $where .= " AND ($sub_tasks_table.sales_act_return_date IS NOT NULL  AND $sub_tasks_table.sales_act_return_date='$sales_act_return_date')";
        }

        $act_return_date = $this->_get_clean_value($options, "act_return_date");
        if ($act_return_date && is_date_exists($act_return_date) && $act_return_date != '1') {

            $where .= " AND (DATE($sub_tasks_table.act_return_date)='$act_return_date')";
        }




        $priority_id = $this->_get_clean_value($options, "priority_id");
        if ($priority_id) {
            $where .= " AND $sub_tasks_table.priority_id=$priority_id";
        }

        /*$start_date = $this->_get_clean_value($options, "start_date");
        $deadline = $this->_get_clean_value($options, "deadline");
        if ($start_date && $deadline) {
            $for_events = $this->_get_clean_value($options, "for_events");
            if ($for_events) {
                $deadline_for_events = $this->_get_clean_value($options, "deadline_for_events");
                $start_date_for_events = $this->_get_clean_value($options, "start_date_for_events");

                if ($start_date_for_events && $deadline_for_events) {
                    $where .= " AND ("
                            . "($tasks_table.created_date IS NOT NULL AND $tasks_table.created_date='$deadline')"
                            
                            . " )";
                } 
            } else {
                $where .= " AND ($tasks_table.created_date BETWEEN '$start_date' AND '$deadline') ";
            }
        }*/

        $exclude_reminder_date = $this->_get_clean_value($options, "exclude_reminder_date");
        if ($exclude_reminder_date) {
            $where .= " AND ($tasks_table.reminder_date !='$exclude_reminder_date') ";
        }


        $order = "";
        $sort_by_project = $this->_get_clean_value($options, "sort_by_project");
        if ($sort_by_project) {
            $order = " ORDER BY $tasks_table.project_id ASC";
        }

        $extra_left_join = "";
        /* $project_member_id = $this->_get_clean_value($options, "project_member_id");
        if ($project_member_id) {
            $where .= " AND $project_members_table.user_id=$project_member_id";
            $extra_left_join = " LEFT JOIN $project_members_table ON $tasks_table.project_id= $project_members_table.project_id AND $project_members_table.deleted=0 AND $project_members_table.user_id=$project_member_id";
        }*/

        $quick_filter = $this->_get_clean_value($options, "quick_filter");
        if ($quick_filter) {
            $where .= $this->make_quick_filter_query($quick_filter, $tasks_table);
        }

        $unread_status_user_id = $this->_get_clean_value($options, "unread_status_user_id");
        if (!$unread_status_user_id) {
            $unread_status_user_id = 0;
        }


        $select_labels_data_query = $this->get_labels_data_query();

        $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }


        $myorder_dir = "";
        $myorder_by = $this->_get_clean_value($options, "order_by");
        if ($myorder_by) {
            $myorder_dir = $this->_get_clean_value($options, "order_dir");
        }
        $available_order_by_list = array(
            "sub_task_id" => ($task_id && $task_id != 0) ? $sub_tasks_table . ".id " . $myorder_dir . "," . $sub_tasks_table . ".sub_task_id" : $sub_tasks_table . ".pnt_task_id " . $myorder_dir . "," . $sub_tasks_table . ".id",
            "guest_nm" => $sub_tasks_table . ".guest_nm",
            "supplier_id" => $sub_tasks_table . ".supplier_id",
            "guest_phone" => $sub_tasks_table . ".guest_phone",
            "out_date" => $sub_tasks_table . ".out_date",
            "car_type_id" => $sub_tasks_table . ".car_type_id",
            "tmp_return_date" => $sub_tasks_table . ".tmp_return_date",
            "sales_act_return_date" => $sub_tasks_table . ".sales_act_return_date",
            "act_out_date" => $sub_tasks_table . ".act_out_date",
            "status" => $supplier_status,
            "id" => $tasks_table . ".id",
            "title" => $tasks_table . ".title",
            "created_date" => $tasks_table . ".created_date",
            "client_name" => $tasks_table . ".client_id",
            "is_closed" => $tasks_table . ".is_closed",

            "created_by" => "assigned_to_user",
            "project" => $projects . ".title",
            "act_return_date" => $sub_tasks_table . ".act_return_date",
            "driver_id" => $sub_tasks_table . ".driver_id",
            "dres_number" => $sub_tasks_table . ".dres_number",
            "amount" => $sub_tasks_table . ".amount",
            "service_type" => $sub_tasks_table . ".service_type",
            "rec_inv_status" => $sub_tasks_table . ".rec_inv_status",
        );


        $order_by = get_array_value($available_order_by_list, $this->_get_clean_value($options, "order_by"));

        if ($order_by) {
            $order_dir = $this->_get_clean_value($options, "order_dir");
            $order = " ORDER BY $order_by $order_dir ";
        }



        $search_by = get_array_value($options, "search_by");
        if ($search_by) {
            $search_by = $this->db->escapeLikeString($search_by);

            if (strpos($search_by, '#') !== false) {
                //get sub tasks of this task
                $search_by = substr($search_by, 1);
                $where .= " AND ($sub_tasks_table.id='$search_by' OR $sub_tasks_table.sub_task_id='$search_by')";
            } else  if (strpos($search_by, '*') !== false) {
                //get sub tasks of this task
                $search_by = substr($search_by, 1);
                $where .= $mang == "reserv" ? " AND ($sub_tasks_table.out_date LIKE '%$search_by%' ESCAPE '!')" : " AND ($sub_tasks_table.act_out_date LIKE '%$search_by%' ESCAPE '!')";
            } else  if (strpos($search_by, '!') !== false) {
                //get sub tasks of this task
                $search_by = substr($search_by, 1);
                $where .= $mang == "reserv" ? " AND ($sub_tasks_table.tmp_return_date LIKE '%$search_by%' ESCAPE '!')" : " AND ($sub_tasks_table.act_return_date LIKE '%$search_by%' ESCAPE '!')";
            } else {
                $where .= " AND (";
                $where .= " $sub_tasks_table.id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR $sub_tasks_table.sub_task_id LIKE '%$search_by%' ESCAPE '!' ";
                if ($mang == "reserv" || $is_admin == "yes") {
                    $where .= " OR $sub_tasks_table.guest_nm LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.guest_phone LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $drivers.driver_nm LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $cars_type_table.car_type LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.driver_nm LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.car_type LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.inv_day_count LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $clients.company_name LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR CONCAT($clients_contact.first_name, ' ', $clients_contact.last_name) LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $task_table.invoice_number LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $task_table.christening_number LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $cities.city_name LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.out_date LIKE '%$search_by' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.tmp_return_date LIKE '%$search_by' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.note LIKE '%$search_by%' ESCAPE '!' ";
                }
                if ($mang == "supply" || $is_admin == "yes") {
                    $where .= " OR $sub_tasks_table.guest_nm LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $suppliers.name LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.car_status LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.car_number LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.dres_number LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.amount LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $drivers.driver_nm LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $cities.city_name LIKE '%$search_by%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.day_count LIKE '%$search_by%' ESCAPE '!'";
                    $where .= " OR $sub_tasks_table.act_out_date LIKE '%$search_by' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.act_return_date LIKE '%$search_by' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.note_2 LIKE '%$search_by%' ESCAPE '!' ";
                }

                $where .= $this->get_custom_field_search_query($sub_tasks_table, "sub_tasks", $search_by);
                $where .= " )";
            }
        }

        //prepare custom fild binding query
        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("tasks", $custom_fields, $tasks_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        $this->db->query('SET SQL_BIG_SELECTS=1');
        //id,sub_task_id,pnt_task_id,city_id,guest_nm,guest_phone,driver_id,service_type,driver_nm,car_type,car_type_id,car_number,supplier_id,dres_number,amount,out_date,act_out_date,exp_out_time,act_out_time,tmp_return_date,sales_act_return_date,act_return_date,act_return_time,day_count,inv_day_count,note,car_status,car_status_id,note_2,rec_inv_status,car_expens,car_expens_stmnt,status_id,priority_id,sort,is_closed,closed_user_id,closed_date,cancel_user_id,cancel_date,cancel_reason,created_by,updated_by,created_at,enterd_status,deleted

        $sql = "SELECT SQL_CALC_FOUND_ROWS $tasks_table.christening_number as chr_number,$tasks_table.invoice_number as inv_number,$tasks_table.is_closed AS main_task_status,$tasks_table.description AS main_description,$tasks_table.created_date AS main_created_date,$tasks_table.id as s_id,$sub_tasks_table.*,$cities.city_name AS city_name,$suppliers.name AS supplier_name,$drivers.driver_nm AS driver_name,$cars_type_table.car_type AS mycar_type, $task_status_table.key_name AS status_key_name, $task_status_table.title AS status_title,  $task_status_table.color AS status_color, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS assigned_to_user,$maintask_clsifications.color AS cls_color, $supplier_status AS dynamic_status_id,$maintask_clsifications.title AS cls_title, CONCAT($clients_contact.first_name, ' ',$clients_contact.last_name) AS clients_contact_name, $users_table.image as assigned_to_avatar, $users_table.user_type, $cities.city_name AS city_name,$projects.client_id as project_client_id,$clients.status AS client_status, $clients.company_name AS client_name,$projects.title AS project_title,$task_priority_table.title AS priority_title, $task_priority_table.icon AS priority_icon, $task_priority_table.color AS priority_color

        FROM $tasks_table 
        LEFT JOIN $sub_tasks_table ON $tasks_table.id=$sub_tasks_table.pnt_task_id 
        LEFT JOIN $users_table ON $users_table.id= $tasks_table.created_by
        LEFT JOIN $projects ON $tasks_table.project_id=$projects.id 
        LEFT JOIN $maintask_clsifications ON $tasks_table.cls_id=$maintask_clsifications.id 
        LEFT JOIN $clients ON $tasks_table.client_id=$clients.id 
        
        LEFT JOIN $clients_contact ON $tasks_table.client_contact_id=$clients_contact.id 
        LEFT JOIN $suppliers ON $sub_tasks_table.supplier_id=$suppliers.id 
        LEFT JOIN $drivers ON $sub_tasks_table.driver_id=$drivers.id 
        LEFT JOIN $cars_type_table ON $sub_tasks_table.car_type_id=$cars_type_table.id 
        LEFT JOIN $cities ON $sub_tasks_table.city_id=$cities.id
        LEFT JOIN $task_priority_table ON $sub_tasks_table.priority_id = $task_priority_table.id
        LEFT JOIN $task_status_table ON $task_status_table.id =$supplier_status 
        LEFT JOIN (SELECT $notifications_table.task_id FROM $notifications_table WHERE $notifications_table.deleted=0 AND $notifications_table.event='project_task_commented' AND !FIND_IN_SET('$unread_status_user_id', $notifications_table.read_by) AND $notifications_table.user_id!=$unread_status_user_id  GROUP BY $notifications_table.task_id) AS notification_table ON notification_table.task_id = $tasks_table.id
        $extra_left_join 
        $join_custom_fieds 
        WHERE $tasks_table.deleted=0  $where and ($sub_tasks_table.deleted=0 OR $sub_tasks_table.deleted IS NULL)  $custom_fields_where 
        $order $limit_offset";

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


    private function make_quick_filter_query($filter, $tasks_table)
    {
        $project_comments_table = $this->db->prefixTable("project_comments");
        $query = "";

        if ($filter == "recently_meaning") {
            return $query;
        }

        $users_model = model("App\Models\Users_model", false);
        $login_user_id = $users_model->login_user_id();

        $recently_updated_last_time = prepare_last_recently_date_time($login_user_id);

        $project_comments_table_query = "SELECT $project_comments_table.task_id 
                           FROM $project_comments_table 
                           WHERE $project_comments_table.deleted=0 AND $project_comments_table.task_id!=0";
        $project_comments_table_group_by = "GROUP BY $project_comments_table.task_id";

        if ($filter === "recently_updated") {
            $query = " AND ($tasks_table.status_changed_at IS NOT NULL AND $tasks_table.status_changed_at>='$recently_updated_last_time')";
        } else if ($filter === "recently_commented") {
            $query = " AND $tasks_table.id IN($project_comments_table_query AND $project_comments_table.created_at>='$recently_updated_last_time' $project_comments_table_group_by)";
        } else if ($filter === "mentioned_me" && $login_user_id) {
            $mention_string = ":" . $login_user_id . "]";
            $query = " AND $tasks_table.id IN($project_comments_table_query AND $project_comments_table.description LIKE '%$mention_string%' $project_comments_table_group_by)";
        } else if ($filter === "recently_mentioned_me" && $login_user_id) {
            $mention_string = ":" . $login_user_id . "]";
            $query = " AND $tasks_table.id IN($project_comments_table_query AND $project_comments_table.description LIKE '%$mention_string%' AND $project_comments_table.created_at>='$recently_updated_last_time' $project_comments_table_group_by)";
        } else if ($filter === "recurring_tasks") {
            $query = " AND ($tasks_table.recurring=1)";
        } else {
            $query = " AND ($tasks_table.status_changed_at IS NOT NULL AND $tasks_table.status_changed_at>='$recently_updated_last_time' AND $tasks_table.status_id=$filter)";
        }

        return $query;
    }

    function get_kanban_details($options = array())
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $users_table = $this->db->prefixTable('users');
        $projects = $this->db->prefixTable('projects');
        $suppliers = $this->db->prefixTable('suppliers');
        $labels_table = $this->db->prefixTable('labels');
        $project_members_table = $this->db->prefixTable('project_members');
        $task_priority_table = $this->db->prefixTable('task_priority');
        $clients_table = $this->db->prefixTable('clients');
        $notifications_table = $this->db->prefixTable("notifications");
        $checklist_items_table = $this->db->prefixTable('checklist_items');

        $where = "";

        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $tasks_table.id=$id";
        }

        $project_id = $this->_get_clean_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $tasks_table.project_id=$project_id";
        }

        $client_id = $this->_get_clean_value($options, "client_id");
        if ($client_id) {
            $where .= " AND $tasks_table.client_id=$client_id";
        }


        $status_ids = $this->_get_clean_value($options, "status_ids");
        if ($status_ids) {
            $where .= " AND FIND_IN_SET($tasks_table.is_closed,'$status_ids')";
        }

        $task_status_id = $this->_get_clean_value($options, "task_status_id");
        if ($task_status_id) {
            $where .= " AND $tasks_table.status_id=$task_status_id";
        }




        $supplier_id = $this->_get_clean_value($options, "supplier_id");
        if ($supplier_id) {
            $where .= " AND FIND_IN_SET($tasks_table.supplier_id,'$supplier_id')";
        }

        $selected_year = $this->_get_clean_value($options, "selected_year");
        $ci = new \App\Controllers\Security_Controller(false);
        $this_year = $ci->session->get('selected_year');
        if ($this_year != 1 && $selected_year) {
            $where .= " AND YEAR($tasks_table.created_date)=$this_year";
        }


        $assigned_to = $this->_get_clean_value($options, "assigned_to");
        if ($assigned_to) {
            $where .= " AND $tasks_table.created_by=$assigned_to";
        }

        $priority_id = $this->_get_clean_value($options, "priority_id");
        if ($priority_id) {
            $where .= " AND $tasks_table.priority_id=$priority_id";
        }

        $specific_user_id = $this->_get_clean_value($options, "specific_user_id");
        if ($specific_user_id) {
            $where .= " AND ($tasks_table.created_by=$specific_user_id OR FIND_IN_SET('$specific_user_id', $tasks_table.collaborators))";
        }

        $show_assigned_tasks_only_user_id = $this->_get_clean_value($options, "show_assigned_tasks_only_user_id");
        if ($show_assigned_tasks_only_user_id) {
            $where .= " AND ($tasks_table.created_by=$show_assigned_tasks_only_user_id OR FIND_IN_SET('$show_assigned_tasks_only_user_id', $tasks_table.collaborators))";
        }




        $search = get_array_value($options, "search");

        if ($search) {
            if (strpos($search, '#') !== false) {
                //get sub tasks of this task
                $search = $this->db->escapeString($search);
                $search = substr($search, 1);
                $where .= " AND ($tasks_table.id='$search' OR $tasks_table.parent_task_id='$search')";
            } else {
                //normal search
                $search = $this->db->escapeLikeString($search);
                $where .= " AND ($tasks_table.title LIKE '%$search%' ESCAPE '!' OR FIND_IN_SET((SELECT $labels_table.id FROM $labels_table WHERE $labels_table.deleted=0 AND $labels_table.context='task' AND $labels_table.title LIKE '%$search%' ESCAPE '!' LIMIT 1), $tasks_table.labels) OR $tasks_table.id='$search')";
            }
        }

        $extra_left_join = "";
        $project_member_id = $this->_get_clean_value($options, "project_member_id");
        if ($project_member_id) {
            $where .= " AND $project_members_table.user_id=$project_member_id";
            $extra_left_join = " LEFT JOIN $project_members_table ON $tasks_table.project_id= $project_members_table.project_id AND $project_members_table.deleted=0 AND $project_members_table.user_id=$project_member_id";
        }

        $quick_filter = $this->_get_clean_value($options, "quick_filter");
        if ($quick_filter) {
            $where .= $this->make_quick_filter_query($quick_filter, $tasks_table);
        }


        $custom_field_filter = $this->_get_clean_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("tasks", "", $tasks_table, $custom_field_filter);
        $custom_fields_where = $this->_get_clean_value($custom_field_query_info, "where_string");

        $unread_status_user_id = $this->_get_clean_value($options, "unread_status_user_id");
        if (!$unread_status_user_id) {
            $unread_status_user_id = 0;
        }

        $select_labels_data_query = $this->get_labels_data_query();

        $this->db->query("SET SQL_BIG_SELECTS=1");

        $sql = "SELECT $tasks_table.id,$tasks_table.is_closed, $tasks_table.title, $tasks_table.start_date, $tasks_table.deadline, $tasks_table.sort, $tasks_table.created_date, $clients_table.company_name AS client_name, IF($tasks_table.sort!=0, $tasks_table.sort, $tasks_table.id) AS new_sort, $tasks_table.assigned_to, $tasks_table.labels, $tasks_table.status_id, $tasks_table.project_id, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS assigned_to_user, $tasks_table.priority_id, $projects.title AS project_title,$users_table.image as assigned_to_avatar, $tasks_table.parent_task_id, sub_tasks_table.id AS has_sub_tasks, $notifications_table.id AS unread, 
                $select_labels_data_query
        FROM $tasks_table
        LEFT JOIN (
            SELECT $tasks_table.id, $tasks_table.parent_task_id
            FROM $tasks_table 
            WHERE $tasks_table.deleted=0 AND $tasks_table.parent_task_id!=0
        ) AS sub_tasks_table ON sub_tasks_table.parent_task_id=$tasks_table.id
        LEFT JOIN $users_table ON $users_table.id= $tasks_table.created_by
        LEFT JOIN $projects ON $tasks_table.project_id=$projects.id 
        LEFT JOIN $clients_table ON $tasks_table.client_id=$clients_table.id 
        LEFT JOIN $notifications_table ON $notifications_table.task_id = $tasks_table.id AND $notifications_table.deleted=0 AND $notifications_table.event='project_task_commented' AND !FIND_IN_SET('$unread_status_user_id', $notifications_table.read_by) AND $notifications_table.user_id!=$unread_status_user_id
        
        $extra_left_join
        WHERE $tasks_table.deleted=0 $where $custom_fields_where 
        GROUP BY $tasks_table.id
        ORDER BY new_sort ASC";

        return $this->db->query($sql);
    }

    function count_my_open_tasks($user_id)
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $projects_table = $this->db->prefixTable('projects');
        $sql = "SELECT COUNT($tasks_table.id) AS totals
        FROM $tasks_table
        WHERE $tasks_table.deleted=0 AND ($tasks_table.created_by=$user_id OR FIND_IN_SET('$user_id', $tasks_table.collaborators)) AND $tasks_table.status_id !=3
        AND $tasks_table.project_id IN(
            SELECT $projects_table.id
            FROM $projects_table
            WHERE $projects_table.deleted=0 AND FIND_IN_SET($projects_table.status,'open')
        )";

        return $this->db->query($sql)->getRow()->totals;
    }

    function get_label_suggestions($project_id)
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $sql = "SELECT GROUP_CONCAT(labels) as label_groups
        FROM $tasks_table
        WHERE $tasks_table.deleted=0 AND $tasks_table.project_id=$project_id";
        return $this->db->query($sql)->getRow()->label_groups;
    }

    function get_my_projects_dropdown_list($user_id = 0, $client_id = 0)
    {
        $projects_table = $this->db->prefixTable('projects');
        $clients = $this->db->prefixTable('clients');

        $where = "";
        if ($client_id) {
            $where .= " AND $projects_table.client_id=$client_id";
        }

        $sql = "SELECT $projects_table.id as project_id, $projects_table.title AS project_title
        FROM $projects_table
        LEFT JOIN $clients ON $projects_table.client_id=$clients.id 
        WHERE  $projects_table.deleted=0 AND $clients.deleted=0 $where
        GROUP BY $projects_table.id";
        return $this->db->query($sql);
    }
    function get_my_projects_dropdown_list2($user_id = 0, $client_id = 0)
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $projects_table = $this->db->prefixTable('projects');

        $where = "";
        if ($client_id) {
            $where .= " AND $projects_table.client_id=$client_id";
        }

        $sql = "SELECT $projects_table.id as project_id, $projects_table.title AS project_title
        FROM $tasks_table
        LEFT JOIN $projects_table ON $tasks_table.project_id=$projects_table.id 
        WHERE  $tasks_table.deleted=0 AND $projects_table.deleted=0 $where
        GROUP BY $projects_table.id";
        return $this->db->query($sql);
    }

    function get_task_statistics2($options = array())
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $users_table = $this->db->prefixTable('users');
        $projects = $this->db->prefixTable('projects');
        $project_id = $this->_get_clean_value($options, "project_id");

        $sql = "SELECT COUNT($tasks_table.id) AS count
                        
        FROM $tasks_table 
        LEFT JOIN $projects ON $tasks_table.project_id=$projects.id 
        
        WHERE $tasks_table.deleted=0 AND $tasks_table.project_id=$project_id ";

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


    function count_total_info($options = array())
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $sub_tasks_table =  $this->db->prefixTable('sub_tasks');

        $where = " ";
        $selected_year = $this->_get_clean_value($options, "selected_year");
        $ci = new \App\Controllers\Security_Controller(false);
        $this_year = $ci->session->get('selected_year');
        if ($this_year != 1 && $selected_year) {
            $where .= "  YEAR($tasks_table.created_date)=$this_year";
        }

        $no_invoice = $this->_get_clean_value($options, "no_invoice");
        if ($no_invoice) {
            $where .= " AND $tasks_table.invoice_number='' OR $tasks_table.invoice_number IS NULL";
        }
        // $deleted = $this->_get_clean_value($options, "deleted");
        // if ($deleted) {
        //     $where .= " AND $tasks_table.deleted=1";
        // }

        $no_christening_number = $this->_get_clean_value($options, "no_christening_number");
        if ($no_christening_number) {
            $where .= " AND $tasks_table.christening_number='' OR $tasks_table.christening_number IS NULL";
        }

        $no_project = $this->_get_clean_value($options, "no_project");
        if ($no_project) {
            $where .= " AND $tasks_table.project_id=0 OR $tasks_table.project_id IS NULL";
        }
        $tasks_deleted = $this->_get_clean_value($options, "tasks_deleted");
        if ($tasks_deleted) {
            $where .= " AND $tasks_table.deleted=1";
        }
        $tasks_unpaid_driver = $this->_get_clean_value($options, "tasks_unpaid_driver");
        if ($tasks_unpaid_driver) {
            $sub_tasks_query = "SELECT pnt_task_id FROM $sub_tasks_table where ($sub_tasks_table.car_expens_stmnt IS NULL OR $sub_tasks_table.car_expens_stmnt = '') AND $sub_tasks_table.deleted = 0 And $sub_tasks_table.service_type = 'with_driver'";
            $where .= " AND id IN ($sub_tasks_query)";
        }




        $sql = "SELECT COUNT($tasks_table.id) AS total
        FROM $tasks_table 
        WHERE  $where ";
        return $this->db->query($sql)->getRow()->total;
    }

    function get_task_statistics($options = array())
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $task_status_table = $this->db->prefixTable('task_status');
        $task_priority_table = $this->db->prefixTable('task_priority');
        $project_members_table = $this->db->prefixTable('project_members');

        try {
            $this->db->query("SET sql_mode = ''");
        } catch (\Exception $e) {
        }
        $where = "";

        $project_id = $this->_get_clean_value($options, "project_id");
        if ($project_id) {
            $where .= " AND $tasks_table.project_id=$project_id";
        }

        $user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $tasks_table.created_by=$user_id";
        }
        $selected_year = $this->_get_clean_value($options, "selected_year");
        $ci = new \App\Controllers\Security_Controller(false);
        $this_year = $ci->session->get('selected_year');
        if ($this_year != 1 && $selected_year) {
            $where .= " AND YEAR($tasks_table.created_date)=$this_year";
        }

        $show_assigned_tasks_only_user_id = $this->_get_clean_value($options, "show_assigned_tasks_only_user_id");
        if ($show_assigned_tasks_only_user_id) {
            $where .= " AND ($tasks_table.created_by=$show_assigned_tasks_only_user_id OR FIND_IN_SET('$show_assigned_tasks_only_user_id', $tasks_table.collaborators))";
        }

        $extra_left_join = "";
        $project_member_id = $this->_get_clean_value($options, "project_member_id");
        if ($project_member_id) {
            $where .= " AND $project_members_table.user_id=$project_member_id";
            $extra_left_join = " LEFT JOIN $project_members_table ON $tasks_table.project_id= $project_members_table.project_id AND $project_members_table.deleted=0 AND $project_members_table.user_id=$project_member_id";
        }

        $task_statuses = "SELECT COUNT($tasks_table.id) AS total, $tasks_table.is_closed AS is_closed
        FROM $tasks_table
       
        $extra_left_join
        WHERE $tasks_table.deleted=0 $where
        GROUP BY $tasks_table.is_closed";



        $info = new \stdClass();
        $info->task_statuses = $this->db->query($task_statuses)->getResult();

        return $info;
    }

    function set_task_comments_as_read($task_id, $user_id = 0)
    {
        $notifications_table = $this->db->prefixTable('notifications');

        $sql = "UPDATE $notifications_table SET $notifications_table.read_by = CONCAT($notifications_table.read_by,',',$user_id)
        WHERE $notifications_table.task_id=$task_id AND FIND_IN_SET($user_id, $notifications_table.read_by) = 0 AND $notifications_table.event='project_task_commented'";
        return $this->db->query($sql);
    }

    function save_reminder_date(&$data = array(), $id = 0)
    {
        if ($id) {
            $where = array("id" => $id);
            $this->update_where($data, $where);
        }
    }

    //get the recurring tasks which are ready to renew as on a given date
    function get_renewable_tasks($date)
    {
        $tasks_table = $this->db->prefixTable('tasks');

        $sql = "SELECT * FROM $tasks_table
                        WHERE $tasks_table.deleted=0 AND $tasks_table.recurring=1
                        AND $tasks_table.next_recurring_date IS NOT NULL AND $tasks_table.next_recurring_date='$date'
                        AND ($tasks_table.no_of_cycles < 1 OR ($tasks_table.no_of_cycles_completed < $tasks_table.no_of_cycles ))";

        return $this->db->query($sql);
    }

    function get_all_dependency_for_this_task($task_id, $type)
    {
        $tasks_table = $this->db->prefixTable('tasks');

        $where = "";
        if ($type == "blocked_by") {
            $where = "AND $tasks_table.blocking LIKE '%$task_id%'";
        } else {
            $where = "AND $tasks_table.blocked_by LIKE '%$task_id%'";
        }

        $sql = "SELECT GROUP_CONCAT($tasks_table.id) AS dependency_task_ids FROM $tasks_table WHERE $tasks_table.deleted=0 AND $tasks_table.id!=$task_id $where";

        return $this->db->query($sql)->getRow()->dependency_task_ids;
    }

    function update_custom_data(&$data = array(), $id = 0)
    {
        if ($id) {
            $where = array("id" => $id);
            $this->update_where($data, $where);

            return $id;
        }
    }

    function get_search_suggestion($search = "", $options = array())
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $projects = $this->db->prefixTable('projects');

        $clients = $this->db->prefixTable('clients');
        $where = "";



        if ($search) {
            $search = $this->db->escapeLikeString($search);
        }

        $sql = "SELECT $tasks_table.id, CONCAT($clients.company_name , ' ', $projects.title) AS task_title
        FROM $tasks_table  

        LEFT JOIN $projects ON $tasks_table.project_id=$projects.id         
        LEFT JOIN $clients ON $tasks_table.client_id=$clients.id 

        WHERE $tasks_table.deleted=0 AND ($clients.company_name LIKE '%$search%' ESCAPE '!' OR  $projects.title LIKE '%$search%' ESCAPE '!' OR $tasks_table.id LIKE '%$search%' ESCAPE '!') $where
        
        LIMIT 0, 10";

        return $this->db->query($sql);
    }

    function get_all_tasks_where_have_dependency($project_id)
    {
        $tasks_table = $this->db->prefixTable('tasks');

        $sql = "SELECT $tasks_table.id, $tasks_table.blocked_by, $tasks_table.blocking
        FROM $tasks_table  
        WHERE $tasks_table.deleted=0 AND $tasks_table.project_id=$project_id AND ($tasks_table.blocked_by!='' OR $tasks_table.blocking!='')";

        return $this->db->query($sql);
    }

    function save_gantt_task_date($data, $task_id)
    {
        parent::disable_log_activity();
        return $this->ci_save($data, $task_id);
    }

    function count_sub_task_status($options = array())
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');

        $where = "";
        $mang = $this->_get_clean_value($options, "is_reserv_mang");

        $supplier_status = $mang ? " (CASE
            WHEN $sub_tasks_table.status_id =5 THEN 5
            WHEN ((($sub_tasks_table.out_date='0000-00-00' OR $sub_tasks_table.out_date is NULL OR $sub_tasks_table.out_date!='0000-00-00' OR $sub_tasks_table.out_date is NOT NULL)
            OR ($sub_tasks_table.exp_out_time='00:00:01' OR $sub_tasks_table.exp_out_time is NULL OR $sub_tasks_table.exp_out_time!='00:00:01' OR $sub_tasks_table.exp_out_time is NOT NULL)) 
            AND ($sub_tasks_table.act_out_date='0000-00-00' OR $sub_tasks_table.act_out_date is NULL)) THEN 1

            

            WHEN ((($sub_tasks_table.act_return_date!='0000-00-00' OR $sub_tasks_table.act_return_date is NOT NULL)
             OR ($sub_tasks_table.act_return_time!='00:00:01' OR $sub_tasks_table.act_return_time is NOT NULL))
             AND (($sub_tasks_table.tmp_return_date is NULL OR $sub_tasks_table.tmp_return_date='0000-00-00') 
             OR( DATE_FORMAT($sub_tasks_table.tmp_return_date,'%m/%d/%Y') >  DATE_FORMAT(NOW(),'%m/%d/%Y')))
             AND ($sub_tasks_table.sales_act_return_date='0000-00-00' OR $sub_tasks_table.sales_act_return_date is NULL)) THEN 3

             WHEN ($sub_tasks_table.tmp_return_date is NOT NULL AND $sub_tasks_table.tmp_return_date!='0000-00-00' AND DATE_FORMAT($sub_tasks_table.tmp_return_date,'%m/%d/%Y') <=  DATE_FORMAT(NOW(),'%m/%d/%Y') AND ($sub_tasks_table.sales_act_return_date='0000-00-00' OR $sub_tasks_table.sales_act_return_date is NULL)) 
              THEN 2

             WHEN ($sub_tasks_table.sales_act_return_date!='0000-00-00' AND $sub_tasks_table.sales_act_return_date is NOT NULL) AND  ($sub_tasks_table.service_type!='deliver' AND $sub_tasks_table.inv_day_count=0 OR $sub_tasks_table.car_type_id=0 ) THEN 6
             
             

            WHEN ($sub_tasks_table.sales_act_return_date!='0000-00-00' AND $sub_tasks_table.sales_act_return_date is NOT NULL)
             AND ($sub_tasks_table.out_date!='0000-00-00' AND $sub_tasks_table.out_date is NOT NULL)
             
             AND  ($sub_tasks_table.service_type='deliver' AND $sub_tasks_table.car_type_id!=0) OR ($sub_tasks_table.inv_day_count!=0 AND $sub_tasks_table.car_type_id!=0 ) THEN 4

           
            
            ELSE 6
            END )" : " (CASE
            WHEN $sub_tasks_table.status_id =5 THEN 5
            WHEN $sub_tasks_table.supplier_id =0 
            OR ($sub_tasks_table.act_out_date='0000-00-00' OR $sub_tasks_table.act_out_date is NULL)
            OR ($sub_tasks_table.act_out_time='00:00:01' OR $sub_tasks_table.act_out_time is NULL) THEN 1

            WHEN ($sub_tasks_table.act_out_date!='0000-00-00' OR $sub_tasks_table.act_out_date is NOT NULL)
            AND ($sub_tasks_table.act_out_time!='00:00:01' OR $sub_tasks_table.act_out_time is NOT NULL)
            AND (($sub_tasks_table.act_return_date='0000-00-00' OR $sub_tasks_table.act_return_date is NULL)
            OR ($sub_tasks_table.act_return_time='00:00:01' OR $sub_tasks_table.act_return_time is NULL)) THEN 3

            WHEN (($sub_tasks_table.act_return_date!='0000-00-00' OR $sub_tasks_table.act_return_date is NOT NULL)
            OR ($sub_tasks_table.act_return_time!='00:00:01' OR $sub_tasks_table.act_return_time is NOT NULL))
            AND ($sub_tasks_table.rec_inv_status='wait_inv') THEN 2
            
            WHEN ($sub_tasks_table.day_count='0' AND $sub_tasks_table.service_type!='deliver')
            OR($sub_tasks_table.rec_inv_status='wait_inv' OR $sub_tasks_table.car_status='') THEN 6

            WHEN ($sub_tasks_table.day_count!='0' AND $sub_tasks_table.service_type!='deliver')
            OR($sub_tasks_table.rec_inv_status!='wait_inv' OR $sub_tasks_table.car_status!='') THEN 4

           
            
            ELSE 2
            END ) ";

        $parent_task_id = $this->_get_clean_value($options, "parent_task_id");
        if ($parent_task_id) {
            $where .= " AND $sub_tasks_table.pnt_task_id=$parent_task_id";
        }

        $status_id = $this->_get_clean_value($options, "status_id");
        if ($status_id) {
            $where .= " AND $supplier_status=$status_id";
        }
        $selected_year = $this->_get_clean_value($options, "selected_year");
        if ($selected_year) {
            $where .= " AND YEAR($sub_tasks_table.created_at)=$selected_year";
        } else {
            $this_year = date("Y");
            $where .= " AND YEAR($sub_tasks_table.created_at)=$this_year";
        }
        /* $supplier_id = $this->_get_clean_value($options, "supplier_id");
        if ($supplier_id) {
            $where .= " AND $tasks_table.supplier_id=$supplier_id";
        }*/

        $sql = "SELECT COUNT($sub_tasks_table.id) AS total ,$supplier_status AS dynamic_status_id
        FROM $sub_tasks_table
        WHERE $sub_tasks_table.deleted=0 $where";
        return $this->db->query($sql)->getRow()->total;
    }

    function count_supplier_tasks($supplier_id)
    {
        $tasks_table = $this->db->prefixTable('tasks');

        $sql = "SELECT COUNT($tasks_table.id) AS total
        FROM $tasks_table
        WHERE $tasks_table.deleted=0 AND $tasks_table.supplier_id=$supplier_id";
        return $this->db->query($sql)->getRow()->total;
    }

    function check_invoice_number($invoice_number, $task_id)
    {
        $query = "";
        if ($invoice_number) {

            if ($task_id) {

                $query = "SELECT * FROM tasks WHERE invoice_number = '$invoice_number' and id !=$task_id limit 1";
            } else {
                $query = "SELECT * FROM tasks WHERE invoice_number = '$invoice_number' limit 1";
            }
        }
        return $this->db->query($query)->getResult();
    }
    function check_completed_subtasks($task_id)
    {
        $tasks_table = $this->db->prefixTable('sub_tasks');

        $query = "";
        if ($task_id) {
            // $query = "SELECT * FROM $tasks_table WHERE pnt_task_id = $task_id and status_id != 4 limit 1";
            $query = "SELECT * FROM $tasks_table WHERE pnt_task_id = $task_id AND rec_inv_status != 'rec_inv' limit 1";
        }

        return $this->db->query($query)->getResult();
    }

    function delete_task_and_sub_items($task_id)
    {
        $tasks_table = $this->db->prefixTable('tasks');
        $project_comments_table = $this->db->prefixTable("project_comments");
        $checklist_items_table = $this->db->prefixTable("checklist_items");
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');

        //get task comment files info to delete the files from directory 


        //delete task
        $delete_task_sql = "UPDATE $tasks_table SET $tasks_table.deleted=1 WHERE $tasks_table.id=$task_id; ";
        $this->db->query($delete_task_sql);

        //delete checklists 
        $delete_checklists_sql = "UPDATE $sub_tasks_table SET $sub_tasks_table.deleted=1 WHERE $sub_tasks_table.pnt_task_id=$task_id; ";
        $this->db->query($delete_checklists_sql);

        //delete the task comment files from directory




        return true;
    }
}
