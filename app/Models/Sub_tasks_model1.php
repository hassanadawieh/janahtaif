<?php

namespace App\Models;

class Sub_tasks_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'sub_tasks';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $task_status_table = $this->db->prefixTable('task_status');
        $task_table = $this->db->prefixTable('tasks');
        $clients = $this->db->prefixTable('clients');
        $projects = $this->db->prefixTable('projects');
        $users_table = $this->db->prefixTable('users');
        $task_priority_table = $this->db->prefixTable('task_priority');
        $cities = $this->db->prefixTable('cities');
        $clients_contact = $this->db->prefixTable('clients_contact');
        $suppliers = $this->db->prefixTable('suppliers');
        $drivers = $this->db->prefixTable('drivers');
        $maintask_clsifications = $this->db->prefixTable("maintask_clsifications");

        $cars_type_table = $this->db->prefixTable('cars_type');


        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $sub_tasks_table.id=$id";
        }


        $task_id = $this->_get_clean_value($options, "task_id");
        if ($task_id && $task_id!=0) {
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


        $pnt_task_id_f = $this->_get_clean_value($options, "pnt_task_id_f");
        if ($pnt_task_id_f) {
            $where .= " AND $sub_tasks_table.pnt_task_id=$pnt_task_id_f";
        }

        $sub_tasks_id_f = $this->_get_clean_value($options, "sub_tasks_id_f");
        if ($sub_tasks_id_f) {
            $where .= " AND $sub_tasks_table.sub_task_id='$sub_tasks_id_f'";
        }

        $guest_nm_f = $this->_get_clean_value($options, "guest_nm_f");
        if ($guest_nm_f) {
            $guest_nm_fn = str_replace(' ', '',$guest_nm_f);
            $where .= " AND REPLACE($sub_tasks_table.guest_nm, ' ', '') LIKE '%$guest_nm_fn%' ESCAPE '!'";
        }

        $guest_phone_f = $this->_get_clean_value($options, "guest_phone_f");
        if ($guest_phone_f) {
            $where .= " AND $sub_tasks_table.guest_phone LIKE '%$guest_phone_f%' ESCAPE '!'";
        }

       
        $company_name_f = $this->_get_clean_value($options, "company_name_f");
        if ($company_name_f) {
            $company_name_fn = str_replace(' ', '',$company_name_f);
            $where .= " AND REPLACE($clients.company_name, ' ', '') LIKE '%$company_name_fn%' ESCAPE '!'";
            
        }

        $project_nm_f = $this->_get_clean_value($options, "project_nm_f");
        if ($project_nm_f) {
            $where .= " AND $projects.title LIKE '%$project_nm_f%' ESCAPE '!'";
            
        }

        $clients_contact_f = $this->_get_clean_value($options, "clients_contact_f");
        
        if ($clients_contact_f) {
            $clients_contact_fn = str_replace(' ', '',$clients_contact_f);
            $where .= " AND REPLACE(CONCAT($clients_contact.first_name, ' ', $clients_contact.last_name), ' ', '') LIKE '%$clients_contact_fn%' ESCAPE '!'";
            
        }
        $christ_num_f = $this->_get_clean_value($options, "christ_num_f");
        if ($christ_num_f) {
            $where .= " AND $task_table.christening_number LIKE '%$christ_num_f%' ESCAPE '!'";
        }

        $inv_num_f = $this->_get_clean_value($options, "inv_num_f");
        if ($inv_num_f) {
            $where .= " AND $task_table.invoice_number='$inv_num_f'";
            //$task_table.invoice_number LIKE '%$inv_num_f%' ESCAPE '!' OR 
        }
        $city_name_f = $this->_get_clean_value($options, "city_name_f");
        if ($city_name_f) {
            $where .= " AND $sub_tasks_table.city_id=$city_name_f";
        }

        $driver_nm_f = $this->_get_clean_value($options, "driver_nm_f");
        
        if ($driver_nm_f) {
            $driver_nm_fn = str_replace(' ', '',$driver_nm_f);
            $where .= " AND REPLACE($drivers.driver_nm, ' ', '') LIKE '%$driver_nm_fn%' ESCAPE '!' ";
        }

        $car_type_f = $this->_get_clean_value($options, "car_type_f");
        if ($car_type_f) {
            $where .= " AND $cars_type_table.car_type LIKE '%$car_type_f%' ESCAPE '!' ";
        }

        /*$out_date_f = $this->_get_clean_value($options, "out_date_f");
        if ($out_date_f) {
            $where .= " AND $sub_tasks_table.out_date LIKE '%$out_date_f%' ESCAPE '!'";
        }*/

        $out_date_f = $this->_get_clean_value($options, "out_date_f");
        $out_date_f_t = $this->_get_clean_value($options, "out_date_f_t");
        if ($out_date_f && $out_date_f_t) {
            $where .= " AND ($sub_tasks_table.out_date BETWEEN '$out_date_f' AND '$out_date_f_t') ";
        }

        /*$tmp_return_date_f = $this->_get_clean_value($options, "tmp_return_date_f");
        if ($tmp_return_date_f) {
            $where .= " AND $sub_tasks_table.tmp_return_date LIKE '%$tmp_return_date_f%' ESCAPE '!'";
        }*/

        $tmp_return_date_f = $this->_get_clean_value($options, "tmp_return_date_f");
        $tmp_return_date_f_t = $this->_get_clean_value($options, "tmp_return_date_f_t");
        if ($tmp_return_date_f && $tmp_return_date_f_t) {
            $where .= " AND ($sub_tasks_table.tmp_return_date BETWEEN '$tmp_return_date_f' AND '$tmp_return_date_f_t') ";
        }


        $sales_act_return_date_f = $this->_get_clean_value($options, "sales_act_return_date_f");
        if ($sales_act_return_date_f) {
            $where .= " AND $sub_tasks_table.sales_act_return_date LIKE '%$sales_act_return_date_f%' ESCAPE '!'";
        }

        $inv_day_count_f = $this->_get_clean_value($options, "inv_day_count_f");
        if ($inv_day_count_f) {
            $where .= " AND $sub_tasks_table.inv_day_count=$inv_day_count_f";
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

        /*$selected_year = $this->_get_clean_value($options, "selected_year");
        if ($selected_year) {
            $where .= " AND YEAR($sub_tasks_table.created_at)=$selected_year";
        }else{
            $dateTime = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
            $this_year = $dateTime->format("Y");
            $where .= " AND YEAR($sub_tasks_table.created_at)=$this_year";
            
        }*/
        $ci = new \App\Controllers\Security_Controller(false);
        $this_year = $ci->session->get('selected_year');
        $where .= " AND YEAR($sub_tasks_table.created_at)=$this_year";
        //$where .= " AND YEAR($task_table.created_date)=$this_year";


        $monthly_f = $this->_get_clean_value($options, "monthly_f");
        if ($monthly_f) {
            $where .= " AND MONTH($sub_tasks_table.created_at)=$monthly_f";
            //$where .= " AND DATE_FORMAT($sub_tasks_table.created_at, '%m/%Y')=$monthly_f";
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

        $supplier_f = $this->_get_clean_value($options, "supplier_f");
        if ($supplier_f) {
            $where .= " AND $suppliers.name LIKE '%$supplier_f%' ESCAPE '!'";
        }

        $car_status_f = $this->_get_clean_value($options, "car_status_f");
        if ($car_status_f) {
            $car_status_fn = str_replace(' ', '',$car_status_f);
            $where .= " AND REPLACE($sub_tasks_table.car_status, ' ', '') LIKE '%$car_status_fn%' ESCAPE '!'";
        }

        $car_number_f = $this->_get_clean_value($options, "car_number_f");
        if ($car_number_f) {
            $where .= " AND $sub_tasks_table.car_number='$car_number_f'";
        }
        /*$act_return_date_f = $this->_get_clean_value($options, "act_return_date_f");
        if ($act_return_date_f) {
            $act_return_date_fn = str_replace(' ', '',$act_return_date_f);
            $where .= " AND $sub_tasks_table.act_return_date='$act_return_date_fn'";
        }*/

        $act_return_date_f = $this->_get_clean_value($options, "act_return_date_f");
        $act_return_date_f_t = $this->_get_clean_value($options, "act_return_date_f_t");
        if ($act_return_date_f && $act_return_date_f_t) {
            $where .= " AND ($sub_tasks_table.act_return_date BETWEEN '$act_return_date_f' AND '$act_return_date_f_t') ";
        }

        /*$act_out_date_f = $this->_get_clean_value($options, "act_out_date_f");
        if ($act_out_date_f) {
            $act_out_date_fn = str_replace(' ', '',$act_out_date_f);
            $where .= " AND $sub_tasks_table.act_out_date='$act_out_date_fn'";
        }*/

        $act_out_date_f = $this->_get_clean_value($options, "act_out_date_f");
        $act_out_date_f_t = $this->_get_clean_value($options, "act_out_date_f_t");
        if ($act_out_date_f && $act_out_date_f_t) {
            $where .= " AND ($sub_tasks_table.act_out_date BETWEEN '$act_out_date_f' AND '$act_out_date_f_t') ";
        }


        $day_count_f = $this->_get_clean_value($options, "day_count_f");
        if ($day_count_f) {
            $where .= " AND $sub_tasks_table.day_count='$day_count_f'";
        }
        $dres_number_f = $this->_get_clean_value($options, "dres_number_f");
        if ($dres_number_f) {
            $where .= " AND $sub_tasks_table.dres_number='$dres_number_f'";
        }
        $amount_f = $this->_get_clean_value($options, "amount_f");
        if ($amount_f) {
            $where .= " AND $sub_tasks_table.amount='$amount_f'";
        }
        $note2_f = $this->_get_clean_value($options, "note2_f");
        if ($note2_f) {
            $where .= " AND $sub_tasks_table.note_2 LIKE '%$note2_f%' ESCAPE '!'";
        }

        $service_type_f = $this->_get_clean_value($options, "service_type_f");
        if ($service_type_f) {
            $where .= " AND $sub_tasks_table.service_type='$service_type_f'";
        }

        $rec_inv_status_f = $this->_get_clean_value($options, "rec_inv_status_f");
        if ($rec_inv_status_f) {
            $where .= " AND $sub_tasks_table.rec_inv_status='$rec_inv_status_f'";
        }
        $main_task_status_f = $this->_get_clean_value($options, "main_task_status_f");
        if ($main_task_status_f) {
            $where .= " AND $task_table.is_closed=$main_task_status_f";
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

        $main_filter = $this->_get_clean_value($options, "main_filter");
        if ($main_filter) {
            if ($main_filter=="no_invoice") {
            $where .= " AND ($task_table.invoice_number='' OR $task_table.invoice_number IS NULL)";
        } if ($main_filter=="no_christening_number") {
           $where .= " AND ($task_table.christening_number='' OR $task_table.christening_number IS NULL)";
        
        } if ($main_filter=="no_project") {
           $where .= " AND ($task_table.project_id=0 OR $task_table.project_id IS NULL)";
        } if ($main_filter=="tasks_deleted") {
            $where .= " AND ($sub_tasks_table.deleted=1)";
         } if ($main_filter=="tasks_unpaid_driver") {
            $where .= " AND ($sub_tasks_table.car_expens_stmnt IS NULL OR $sub_tasks_table.car_expens_stmnt = '')";
         }
        }



        $filter = $this->_get_clean_value($options, "filter");
        if ($filter) {
            if ($filter=="no_supplier") {
            $where .= " AND ($sub_tasks_table.supplier_id=0 OR $sub_tasks_table.supplier_id IS NULL)";
        } if ($filter=="wait_inv") {
           $where .= " AND ($sub_tasks_table.rec_inv_status='wait_inv')";
        
        }  if ($filter=="rec_inv") {
           $where .= " AND ($sub_tasks_table.rec_inv_status='rec_inv')";
        
        } if ($filter=="no_act_return_date") {
           $where .= " AND ($sub_tasks_table.act_return_date='0000-00-00' OR $sub_tasks_table.act_return_date IS NULL)";
        } if ($filter=="no_act_out_time") {
           $where .= " AND ($sub_tasks_table.act_out_time='00:00:01' OR $sub_tasks_table.act_out_time IS NULL)";
        } 
        if ($filter=="24houer") {
           $where .= " AND ($sub_tasks_table.status_id=1 AND DATE($sub_tasks_table.act_out_date) - INTERVAL 24 HOUR <  NOW())";
       }

        
        }

        $mang = $this->_get_clean_value($options, "mang");
        $is_admin=$this->_get_clean_value($options,"is_admin");
        /*$drr=" IF $sub_tasks_table.status_id =5 THEN 5
               ELSEIF ($sub_tasks_table.out_date='0000-00-00' OR $sub_tasks_table.out_date is NULL) THEN 2
               ELSEIF (DATE_FORMAT(CONCAT($sub_tasks_table.out_date, ' ',$sub_tasks_table.exp_out_time),'%m/%d/%Y %H:%i') >  DATE_FORMAT(NOW(),'%m/%d/%Y %H:%i')) THEN 1
               ELSEIF (DATE($sub_tasks_table.tmp_return_date) >  NOW()) THEN 3

               DATE_FORMAT($sub_tasks_table.tmp_return_date,'%m/%d/%Y')
        ";*/

        $supplier_status=$mang=="reserv" ? " (CASE
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
            
            WHEN (($sub_tasks_table.day_count='0' OR $sub_tasks_table.rec_inv_status='wait_inv') AND $sub_tasks_table.service_type!='deliver')
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

        $status_ids = $this->_get_clean_value($options, "status_ids");
        if ($status_ids) {
            $where .= " AND FIND_IN_SET($supplier_status,'$status_ids')";
        }

        /*$main_status_id = $this->_get_clean_value($options, "main_status_id");
        if ($main_status_id) {
            $where .= " AND FIND_IN_SET($task_table.is_closed,'$main_status_id')";
        }*/

        $out_date = $this->_get_clean_value($options, "out_date");
        if ($out_date && is_date_exists($out_date) && $out_date!='1') {
            $where .= " AND (DATE($sub_tasks_table.out_date)='$out_date')";
        }
        $tmp_return_date = $this->_get_clean_value($options, "tmp_return_date");
        if ($tmp_return_date && is_date_exists($tmp_return_date) && $tmp_return_date!='1') {

            $where .= " AND (DATE($sub_tasks_table.tmp_return_date)='$tmp_return_date')";
        }
        $act_out_date = $this->_get_clean_value($options, "act_out_date");
        if ($act_out_date && is_date_exists($act_out_date) && $act_out_date!='1') {
            $where .= " AND ($sub_tasks_table.act_out_date IS NOT NULL  AND $sub_tasks_table.act_out_date='$act_out_date')";
        }

        $sales_act_return_date = $this->_get_clean_value($options, "sales_act_return_date");
        if ($sales_act_return_date && is_date_exists($sales_act_return_date) && $sales_act_return_date!='2') {

            $where .= " AND ($sub_tasks_table.sales_act_return_date IS NOT NULL  AND $sub_tasks_table.sales_act_return_date='$sales_act_return_date')";
        }

        $act_return_date = $this->_get_clean_value($options, "act_return_date");
        if ($act_return_date && is_date_exists($act_return_date) && $act_return_date!='1') {
            
            $where .= " AND (DATE($sub_tasks_table.act_return_date)='$act_return_date')";
        }

        
     

        $priority_id = $this->_get_clean_value($options, "priority_id");
        if ($priority_id) {
            $where .= " AND $sub_tasks_table.priority_id=$priority_id";
        }


        $order = "";
        $myorder_dir="";
       $myorder_by = $this->_get_clean_value($options, "order_by");
        if ($myorder_by) {
            $myorder_dir = $this->_get_clean_value($options, "order_dir");
        }
        $available_order_by_list = array(
            "sub_task_id" => ($task_id && $task_id!=0)?$sub_tasks_table . ".id ".$myorder_dir.",".$sub_tasks_table . ".sub_task_id":$sub_tasks_table . ".pnt_task_id ".$myorder_dir.",".$sub_tasks_table . ".id",
            "guest_nm" => $sub_tasks_table . ".guest_nm",
            "supplier_id" => $sub_tasks_table . ".supplier_id",
            "guest_phone" => $sub_tasks_table . ".guest_phone",
            "out_date" => $sub_tasks_table . ".out_date",
            "car_type_id" => $sub_tasks_table . ".car_type_id",
            "tmp_return_date" => $sub_tasks_table . ".tmp_return_date",
            "sales_act_return_date" => $sub_tasks_table . ".sales_act_return_date",
            "act_out_date" => $sub_tasks_table . ".act_out_date",
            "status" => $supplier_status,
            "act_return_date" => $sub_tasks_table . ".act_return_date",
            "city_id" => $sub_tasks_table . ".city_id",
            "driver_id" => $sub_tasks_table . ".driver_id",
            "car_number" => $sub_tasks_table . ".car_number",
            "car_status" => $sub_tasks_table . ".car_status",
            "dres_number" => $sub_tasks_table .".dres_number",
            "note_2" => $sub_tasks_table .".note_2",
            "note" => $sub_tasks_table .".note",
            "service_type" => $sub_tasks_table .".service_type",
            "rec_inv_status" => $sub_tasks_table . ".rec_inv_status",
        );

        $order_by = get_array_value($available_order_by_list, $this->_get_clean_value($options, "order_by"));

        if ($order_by) {
            $order_dir = $this->_get_clean_value($options, "order_dir");
          
                $order = " ORDER BY $order_by $order_dir ";
            
            
        }
        /*$user_role_id = $this->_get_clean_value($options, "user_role_id");
        if($user_role_id){
              
              
                $order = " ORDER BY supplier_id asc ";
            
        }*/

        $limit_offset = "";
        $limit = $this->_get_clean_value($options, "limit");
        if ($limit) {
            $skip = $this->_get_clean_value($options, "skip");
            $offset = $skip ? $skip : 0;
            $limit_offset = " LIMIT $limit OFFSET $offset ";
        }

        $search_by = get_array_value($options, "search_by");
        if ($search_by) {
            $search_by = $this->db->escapeLikeString($search_by);

            if (strpos($search_by, '#') !== false) {
                //get sub tasks of this task
                $search_by = substr($search_by, 1);
                $where .= " AND ($sub_tasks_table.id='$search_by' OR $sub_tasks_table.sub_task_id='$search_by')";
            
            }else  if (strpos($search_by, '*') !== false) {
                //get sub tasks of this task
                $search_by = substr($search_by, 1);
                $where .= $mang=="reserv"?" AND ($sub_tasks_table.out_date LIKE '%$search_by%' ESCAPE '!')":" AND ($sub_tasks_table.act_out_date LIKE '%$search_by%' ESCAPE '!')";
                
                }else  if (strpos($search_by, '!') !== false) {
                //get sub tasks of this task
                $search_by = substr($search_by, 1);
                $where .= $mang=="reserv"?" AND ($sub_tasks_table.tmp_return_date LIKE '%$search_by%' ESCAPE '!')":" AND ($sub_tasks_table.act_return_date LIKE '%$search_by%' ESCAPE '!')";

            } else {
                $where .= " AND (";
                $where .= " $sub_tasks_table.id LIKE '%$search_by%' ESCAPE '!' ";
                $where .= " OR $sub_tasks_table.sub_task_id LIKE '%$search_by%' ESCAPE '!' ";
                if($mang=="reserv" || $is_admin=="yes"){
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
                if($mang=="supply" || $is_admin=="yes"){
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

        $custom_fields = get_array_value($options, "custom_fields");
        $custom_field_filter = get_array_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("sub_tasks", $custom_fields, $sub_tasks_table, $custom_field_filter);
        $select_custom_fieds = get_array_value($custom_field_query_info, "select_string");
        $join_custom_fieds = get_array_value($custom_field_query_info, "join_string");
        $custom_fields_where = get_array_value($custom_field_query_info, "where_string");

        



        $sql = "SELECT SQL_CALC_FOUND_ROWS $sub_tasks_table.*,$cities.city_name AS city_name,$suppliers.name AS supplier_name,$drivers.driver_nm AS driver_name,$cars_type_table.car_type AS mycar_type, $task_status_table.key_name AS status_key_name, $task_status_table.title AS status_title,  $task_status_table.color AS status_color,$task_table.title AS main_task_title,$maintask_clsifications.color AS cls_color, $clients.company_name AS client_name,$task_table.christening_number as chr_number,$task_table.invoice_number as inv_number,$task_table.is_closed AS main_task_status,$task_table.description AS main_description,$task_table.created_date AS main_created_date, CONCAT($clients_contact.first_name, ' ',$clients_contact.last_name) AS clients_contact_name, $supplier_status AS dynamic_status_id, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS assigned_to_user, (select CONCAT($users_table.first_name, ' ',$users_table.last_name) from $users_table where $sub_tasks_table.updated_by=$users_table.id) AS updated_by_nm  , $users_table.image as assigned_to_avatar, $users_table.user_type,$task_priority_table.title AS priority_title, $task_priority_table.icon AS priority_icon, $task_priority_table.color AS priority_color,$projects.title AS project_title
        FROM $sub_tasks_table
        LEFT JOIN $suppliers ON $sub_tasks_table.supplier_id=$suppliers.id 
        LEFT JOIN $drivers ON $sub_tasks_table.driver_id=$drivers.id 
        LEFT JOIN $cars_type_table ON $sub_tasks_table.car_type_id=$cars_type_table.id 
        LEFT JOIN $task_table ON $sub_tasks_table.pnt_task_id=$task_table.id 
        LEFT JOIN $clients ON $task_table.client_id=$clients.id
        LEFT JOIN $projects ON $task_table.project_id=$projects.id
        LEFT JOIN $clients_contact ON $task_table.client_contact_id=$clients_contact.id 
        LEFT JOIN $maintask_clsifications ON $task_table.cls_id=$maintask_clsifications.id 
        LEFT JOIN $cities ON $sub_tasks_table.city_id=$cities.id
        LEFT JOIN $users_table ON $sub_tasks_table.created_by=$users_table.id
        LEFT JOIN $task_priority_table ON $sub_tasks_table.priority_id = $task_priority_table.id
        LEFT JOIN $task_status_table ON $task_status_table.id =$supplier_status
        WHERE $sub_tasks_table.deleted=0 $where $custom_fields_where $order $limit_offset";


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

    function union_all(){
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $task_status_table = $this->db->prefixTable('task_status');
        $task_table = $this->db->prefixTable('tasks');

        $sql = "
        (SELECT distinct $task_table.id FROM $task_table)
        union ALL
        (SELECT distinct $sub_tasks_table.sub_task_id as ids FROM $sub_tasks_table 
        ORDER BY $sub_tasks_table.pnt_task_id DESC)";

        
        $raw_query = $this->db->query($sql);

        return $raw_query;
    }




    function get_kanban_details($options = array()) {
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $task_status_table = $this->db->prefixTable('task_status');
        $task_table = $this->db->prefixTable('tasks');
        $clients = $this->db->prefixTable('clients');
        $users_table = $this->db->prefixTable('users');
        $task_priority_table = $this->db->prefixTable('task_priority');
        $suppliers = $this->db->prefixTable('suppliers');
        $drivers = $this->db->prefixTable('drivers');
        $cities = $this->db->prefixTable('cities');
        $where = "";

        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where .= " AND $sub_tasks_table.id=$id";
        }


        $mang = $this->_get_clean_value($options, "mang");
        $is_admin=$this->_get_clean_value($options,"is_admin");
       

        $supplier_status=$mang=="reserv" ? " (CASE
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
            
            WHEN (($sub_tasks_table.day_count='0' OR $sub_tasks_table.rec_inv_status='wait_inv') AND $sub_tasks_table.service_type!='deliver')
            OR(($sub_tasks_table.rec_inv_status='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status='') THEN 6

            WHEN ($sub_tasks_table.day_count!='0' AND $sub_tasks_table.service_type!='deliver')
           
            OR(($sub_tasks_table.rec_inv_status!='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status!='') THEN 4
           
            
            ELSE 6
            END ) ";
        /*$supplier_status=" (CASE
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

        $status_ids = $this->_get_clean_value($options, "status_ids");
        if ($status_ids) {
            $where .= " AND FIND_IN_SET($supplier_status,'$status_ids')";
        }

         $task_id = $this->_get_clean_value($options, "task_id");
        if ($task_id && $task_id!=0) {
            $where .= " AND $sub_tasks_table.pnt_task_id=$task_id";
        }

        $task_status_id = $this->_get_clean_value($options, "task_status_id");
        if ($task_status_id) {
            $where .= " AND $supplier_status=$task_status_id";
        }


        $selected_year = $this->_get_clean_value($options, "selected_year");
        if ($selected_year) {
            $where .= " AND YEAR($sub_tasks_table.created_at)=$selected_year";
        }else{
            $this_year = date("Y");
            $where .= " AND YEAR($sub_tasks_table.created_at)=$this_year";
            
        }

        $supplier_id = $this->_get_clean_value($options, "supplier_id");
        if ($supplier_id) {
            $where .= " AND FIND_IN_SET($sub_tasks_table.supplier_id,'$supplier_id')";
        }
        $driver_id = $this->_get_clean_value($options, "driver_id");
        if ($driver_id) {
            $where .= " AND $sub_tasks_table.driver_id=$driver_id";
        }

        $service_type = $this->_get_clean_value($options, "service_type");
        if ($service_type) {
            $where .= " AND $sub_tasks_table.service_type='$service_type'";
        }

        $deleted_client = $this->_get_clean_value($options, "deleted_client");
        if (!$deleted_client) {
            $where .= " AND $clients.deleted=0";
        }

       

        $assigned_to = $this->_get_clean_value($options, "assigned_to");
        if ($assigned_to) {
            $where .= " AND $sub_tasks_table.created_by=$assigned_to";
        }

       

        $specific_user_id = $this->_get_clean_value($options, "specific_user_id");
        if ($specific_user_id) {
            $where .= " AND ($sub_tasks_table.created_by=$specific_user_id )";
        }


        $filter = $this->_get_clean_value($options, "filter");
        if ($filter) {
            if ($filter=="no_supplier") {
            $where .= " AND ($sub_tasks_table.supplier_id=0 OR $sub_tasks_table.supplier_id IS NULL)";
        } if ($filter=="wait_inv") {
           $where .= " AND ($sub_tasks_table.rec_inv_status='wait_inv')";
        
        } if ($filter=="no_act_return_date") {
           $where .= " AND ($sub_tasks_table.act_return_date='0000-00-00' OR $sub_tasks_table.act_return_date IS NULL)";
        } if ($filter=="no_act_out_time") {
           $where .= " AND ($sub_tasks_table.act_out_time='00:00:01' OR $sub_tasks_table.act_out_time IS NULL)";
        } 
        if ($filter=="24houer") {
           $where .= " AND ($sub_tasks_table.status_id=1 AND DATE($sub_tasks_table.act_out_date) - INTERVAL 24 HOUR <  NOW())";
       }

        
        }

        $priority_id = $this->_get_clean_value($options, "priority_id");
        if ($priority_id) {
            $where .= " AND $sub_tasks_table.priority_id=$priority_id";
        }

        


       

        $search = get_array_value($options, "search");

        if ($search) {
            $search = $this->db->escapeString($search);
            
            if (strpos($search, '#') !== false) {
                //get sub tasks of this task
                $search = substr($search, 1);
                $where .= " AND ($sub_tasks_table.id='$search' OR $sub_tasks_table.sub_task_id='$search')";
            
            }else  if (strpos($search, '*') !== false) {
                //get sub tasks of this task
                $search = substr($search, 1);
                $where .= $mang=="reserv"?" AND ($sub_tasks_table.out_date LIKE '%$search%' ESCAPE '!')":" AND ($sub_tasks_table.act_out_date LIKE '%$search%' ESCAPE '!')";
                
                }else  if (strpos($search, '!') !== false) {
                //get sub tasks of this task
                $search = substr($search, 1);
                $where .= $mang=="reserv"?" AND ($sub_tasks_table.tmp_return_date LIKE '%$search%' ESCAPE '!')":" AND ($sub_tasks_table.act_return_date LIKE '%$search%' ESCAPE '!')";

            } else {
                $where .= " AND (";
                $where .= " $sub_tasks_table.id LIKE '%$search%' ESCAPE '!' ";
                $where .= " OR $sub_tasks_table.sub_task_id LIKE '%$search%' ESCAPE '!' ";
                if($mang=="reserv" || $is_admin=="yes"){
                    $where .= " OR $sub_tasks_table.guest_nm LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.guest_phone LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $drivers.driver_nm LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.driver_nm LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.car_type LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.inv_day_count LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $clients.company_name LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.out_date LIKE '%$search' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.tmp_return_date LIKE '%$search' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.note LIKE '%$search%' ESCAPE '!' ";

                }
                if($mang=="supply" || $is_admin=="yes"){
                    $where .= " OR $sub_tasks_table.guest_nm LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $suppliers.name LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.car_status LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.car_number LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.dres_number LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.amount LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $drivers.driver_nm LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $cities.city_name LIKE '%$search%' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.day_count LIKE '%$search%' ESCAPE '!'";
                    $where .= " OR $sub_tasks_table.act_out_date LIKE '%$search' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.act_return_date LIKE '%$search' ESCAPE '!' ";
                    $where .= " OR $sub_tasks_table.note_2 LIKE '%$search%' ESCAPE '!' ";

                }
                $where .= " )";
            }
        }

        $extra_left_join = "";
       

        $quick_filter = $this->_get_clean_value($options, "quick_filter");
        if ($quick_filter) {
            $where .= $this->make_quick_filter_query($quick_filter, $sub_tasks_table);
        }


        $custom_field_filter = $this->_get_clean_value($options, "custom_field_filter");
        $custom_field_query_info = $this->prepare_custom_field_query_string("sub_tasks", "", $sub_tasks_table, $custom_field_filter);
        $custom_fields_where = $this->_get_clean_value($custom_field_query_info, "where_string");

        


        $this->db->query("SET SQL_BIG_SELECTS=1");

         $sql = "SELECT $sub_tasks_table.*,$suppliers.name AS supplier_name,$drivers.driver_nm AS driver_name, $task_status_table.key_name AS status_key_name, $task_status_table.title AS status_title,$supplier_status AS dynamic_status_id,  $task_status_table.color AS status_color,$task_table.title AS main_task_title,$task_table.is_closed AS main_task_status, IF($sub_tasks_table.sort!=0, $sub_tasks_table.sort, $sub_tasks_table.id) AS new_sort, CONCAT($users_table.first_name, ' ',$users_table.last_name) AS assigned_to_user, $users_table.image as assigned_to_avatar, $users_table.user_type
        FROM $sub_tasks_table
        LEFT JOIN $suppliers ON $sub_tasks_table.supplier_id=$suppliers.id 
        LEFT JOIN $drivers ON $sub_tasks_table.driver_id=$drivers.id 
        LEFT JOIN $task_table ON $sub_tasks_table.pnt_task_id=$task_table.id
        LEFT JOIN $clients ON $task_table.client_id=$clients.id
        LEFT JOIN $cities ON $sub_tasks_table.city_id=$cities.id
        LEFT JOIN $users_table ON $sub_tasks_table.created_by=$users_table.id
        LEFT JOIN $task_status_table ON $task_status_table.id =$supplier_status
        WHERE $sub_tasks_table.deleted=0 $where $custom_fields_where 
        GROUP BY $sub_tasks_table.id 
        ORDER BY new_sort ASC ";

        return $this->db->query($sql);
    }




    function get_task_statistics($options = array()) {
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $task_status_table = $this->db->prefixTable('task_status');
        $task_priority_table = $this->db->prefixTable('task_priority');
        $project_members_table = $this->db->prefixTable('project_members');

        try {
            $this->db->query("SET sql_mode = ''");
        } catch (\Exception $e) {
            
        }
        $where = "";

        $selected_year = $this->_get_clean_value($options, "selected_year");
        if ($selected_year) {
            $where .= " AND YEAR($sub_tasks_table.created_at)=$selected_year";
        }else{
            $this_year = date("Y");
            $where .= " AND YEAR($sub_tasks_table.created_at)=$this_year";
            
        }
        /*$user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $tasks_table.created_by=$user_id";
        }*/
        $mang = $this->_get_clean_value($options, "mang");
        
        $supplier_status=$mang=="reserv" ? " (CASE
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
            
            WHEN (($sub_tasks_table.day_count='0' OR $sub_tasks_table.rec_inv_status='wait_inv') AND $sub_tasks_table.service_type!='deliver')
            OR(($sub_tasks_table.rec_inv_status='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status='') THEN 6

            WHEN ($sub_tasks_table.day_count!='0' AND $sub_tasks_table.service_type!='deliver')
           
            OR(($sub_tasks_table.rec_inv_status!='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status!='') THEN 4
           
            
            ELSE 6
            END ) ";
        /*

        : " (CASE
            WHEN $sub_tasks_table.status_id =5 THEN 5
            WHEN $sub_tasks_table.supplier_id =0 
            OR ($sub_tasks_table.act_out_date='0000-00-00' OR $sub_tasks_table.act_out_date is NULL)
            OR ($sub_tasks_table.act_out_time='00:00:01' OR $sub_tasks_table.act_out_time is NULL) 
            OR ($sub_tasks_table.act_return_date='0000-00-00' OR $sub_tasks_table.act_return_date is NULL)
            OR ($sub_tasks_table.act_return_time='00:00:01' OR $sub_tasks_table.act_return_time is NULL)
            
            OR ($sub_tasks_table.day_count='0' AND $sub_tasks_table.service_type!='deliver') THEN 2

            WHEN (DATE_FORMAT(CONCAT($sub_tasks_table.act_out_date, ' ',$sub_tasks_table.act_out_time),'%m/%d/%Y %H:%i') >  DATE_FORMAT(NOW(),'%m/%d/%Y %H:%i')) THEN 1

            WHEN ((DATE_FORMAT(CONCAT($sub_tasks_table.act_out_date, ' ',$sub_tasks_table.act_out_time),'%m/%d/%Y %H:%i') <=  DATE_FORMAT(NOW(),'%m/%d/%Y %H:%i')) AND (DATE_FORMAT(CONCAT($sub_tasks_table.act_return_date, ' ',$sub_tasks_table.act_return_time), '%m/%d/%Y %H:%i') >  DATE_FORMAT(NOW(),'%m/%d/%Y %H:%i')) )THEN 3
            WHEN $sub_tasks_table.rec_inv_status='wait_inv' OR $sub_tasks_table.car_status=''  THEN 6

            WHEN (DATE_FORMAT(CONCAT($sub_tasks_table.act_return_date, ' ',$sub_tasks_table.act_return_time), '%m/%d/%Y %H:%i') <=  NOW()) AND $sub_tasks_table.car_status!='' AND $sub_tasks_table.rec_inv_status!='wait_inv' THEN 4

            
            ELSE 2
            END ) ";
        $supplier_status=" (CASE
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
        

        $extra_left_join = "";
        

        $task_statuses = "SELECT COUNT($sub_tasks_table.id) AS total, $sub_tasks_table.status_id,$supplier_status AS dynamic_status_id, $task_status_table.key_name, $task_status_table.title, $task_status_table.color
        FROM $sub_tasks_table
        LEFT JOIN $task_status_table ON $task_status_table.id = $supplier_status
        $extra_left_join
        WHERE $sub_tasks_table.deleted=0 $where
        GROUP BY $supplier_status
        ORDER BY $task_status_table.sort ASC";

       
        $info = new \stdClass();
        $info->task_statuses = $this->db->query($task_statuses)->getResult();

        return $info;
    }



    function check_subtask_status($options = array()) {
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $task_status_table = $this->db->prefixTable('task_status');
        $task_priority_table = $this->db->prefixTable('task_priority');
        $project_members_table = $this->db->prefixTable('project_members');

        try {
            $this->db->query("SET sql_mode = ''");
        } catch (\Exception $e) {
            
        }
        $where = "";

        $task_id = $this->_get_clean_value($options, "pnt_task_id");
        if ($task_id && $task_id!=0) {
            $where .= " AND $sub_tasks_table.pnt_task_id=$task_id";
        }

        
        /*$user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $tasks_table.created_by=$user_id";
        }*/
        $reserv_status=" (CASE
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
            END )";
            $supplier_status=" (CASE
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
            
            WHEN (($sub_tasks_table.day_count='0' OR $sub_tasks_table.rec_inv_status='wait_inv') AND $sub_tasks_table.service_type!='deliver')
            OR(($sub_tasks_table.rec_inv_status='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status='') THEN 6

            WHEN ($sub_tasks_table.day_count!='0' AND $sub_tasks_table.service_type!='deliver')
           
            OR(($sub_tasks_table.rec_inv_status!='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status!='') THEN 4
           
            
            ELSE 6
            END ) ";


        

        $extra_left_join = "";
        

        $task_statuses = "SELECT $reserv_status AS reserv_status, $supplier_status AS supplier_status
        FROM $sub_tasks_table
       
        WHERE $sub_tasks_table.deleted=0 $where";

       
        $info = new \stdClass();
        $info = $this->db->query($task_statuses);

        return $info;
    }


    function get_subtask_status($id,$mang) {
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');
        $task_status_table = $this->db->prefixTable('task_status');

        try {
            $this->db->query("SET sql_mode = ''");
        } catch (\Exception $e) {
            
        }
        $where = "";

        
            $where .= " AND $sub_tasks_table.id=$id";
        

        
        /*$user_id = $this->_get_clean_value($options, "user_id");
        if ($user_id) {
            $where .= " AND $tasks_table.created_by=$user_id";
        }*/
        $supplier_status= $mang=="reserv" ? " (CASE
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
            END )"
            :" (CASE
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
            
            WHEN (($sub_tasks_table.day_count='0' OR $sub_tasks_table.rec_inv_status='wait_inv') AND $sub_tasks_table.service_type!='deliver')
            OR(($sub_tasks_table.rec_inv_status='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status='') THEN 6

            WHEN ($sub_tasks_table.day_count!='0' AND $sub_tasks_table.service_type!='deliver')
           
            OR(($sub_tasks_table.rec_inv_status!='wait_inv' AND $sub_tasks_table.service_type!='deliver') OR $sub_tasks_table.car_status!='') THEN 4
           
            
            ELSE 6
            END ) ";


        

        $extra_left_join = "";
        

        $task_statuses = "SELECT  $supplier_status AS dynamic_status_id,$task_status_table.key_name AS status_key_name, $task_status_table.title AS status_title,  $task_status_table.color AS status_color
        FROM $sub_tasks_table
        LEFT JOIN $task_status_table ON $task_status_table.id =$supplier_status
        WHERE $sub_tasks_table.deleted=0 $where";

       
        $info = new \stdClass();
        $info = $this->db->query($task_statuses);

        return $info;
    }


    function get_task_befor24($options = array()) {
        $tasks_table = $this->db->prefixTable('sub_tasks');
        

        try {
            $this->db->query("SET sql_mode = ''");
        } catch (\Exception $e) {
            
        }
        $where = "";

       $selected_year = $this->_get_clean_value($options, "selected_year");
        if ($selected_year) {
            $where .= " AND YEAR($tasks_table.created_at)=$selected_year";
        }else{
            $this_year = date("Y");
            $where .= " AND YEAR($tasks_table.created_at)=$this_year";
            
        }

        $extra_left_join = "";
        

        $task_statuses = "SELECT id,guest_nm,CONCAT(out_date, ' ',act_out_time) AS o_date
        FROM $tasks_table
        
        WHERE deleted=0 AND status_id=1 AND DATE(out_date) - INTERVAL 24 HOUR <  NOW()";

       
        $info = new \stdClass();
        $info->task_statuses = $this->db->query($task_statuses)->getResult();

        return $info;
    }


    function get_search_suggestion($search = "", $options = array()) {
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');

        $where = "";

        
        if ($search) {
            $search = $this->db->escapeLikeString($search);
        }

        $sql = "SELECT $sub_tasks_table.id, $sub_tasks_table.guest_nm
        FROM $sub_tasks_table  
        WHERE $sub_tasks_table.deleted=0 AND ($sub_tasks_table.guest_nm LIKE '%$search%' ESCAPE '!' OR $sub_tasks_table.id LIKE '%$search%' ESCAPE '!') $where
        ORDER BY $sub_tasks_table.guest_nm ASC
        LIMIT 0, 10";

        return $this->db->query($sql);
    }

    function get_years() {
        $tasks_table = $this->db->prefixTable('sub_tasks');

        $sql = "SELECT YEAR(created_at) AS year
        FROM $tasks_table
        WHERE $tasks_table.deleted=0 GROUP BY YEAR(created_at)";
        //return $this->db->query($sql)->getRow()->total;

        $raw_query = $this->db->query($sql);


        
        return $raw_query;
        
    }


    function get_max_id($pnt_task_id) {
        $sub_tasks_table = $this->db->prefixTable('sub_tasks');

       

        $sql = "SELECT COUNT($sub_tasks_table.id) as t_count
        FROM $sub_tasks_table  
        WHERE $sub_tasks_table.pnt_task_id=$pnt_task_id";

       $info = new \stdClass();
        $info = $this->db->query($sql)->getRow();

        return $info;
    }

}
