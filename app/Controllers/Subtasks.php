<?php

namespace App\Controllers;

class Subtasks extends Security_Controller {

    //private $Company_model;

    protected $Task_priority_model;
    function __construct() {
        parent::__construct();
        //$this->access_only_admin_or_settings_admin();
        if (!$this->can_show_subtasks()) {
            app_redirect("forbidden");
        }
        //$this->Company_model = model('App\Models\Company_model');
        $this->Task_priority_model = model("App\Models\Task_priority_model");
    }


     private function can_update_subtask_status() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_update_subtask_status") == "1") {
                //check is user a project member
                return true;
            }
        } 
    }

   

    /*function index() {
        return $this->template->rander("company/index");
    }*/

    function modal_form() {
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['model_info'] = $this->Company_model->get_one($this->request->getPost('id'));
        return $this->template->view('company/modal_form', $view_data);
    }

    

    function delete() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $id = $this->request->getPost('id');
        if($this->can_delete_subtasks()){
        

        if ($this->request->getPost('undo')) {
            if ($this->Sub_tasks_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Sub_tasks_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
        } else {
                echo json_encode(array("success" => false, 'message' => "ليس لديك صلاحية"));
            }
    }

    public function undo() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));
    
        $id = $this->request->getPost('id');

            if ($this->Sub_tasks_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            }
        // } else {
        //     echo json_encode(array("success" => false, 'message' => "ليس لديك صلاحية"));
        // }
    }

    function set_year(){
        
        $val=$this->request->getPost('value');
        $this->session->set('selected_year', $val);
         echo json_encode(array("success" => true, 'message' => "ok", "data" => $this->session->get('selected_year')));
    }
    function index($task_id=0,$tab = "", $status_id = 0,$open_add="no") {
        validate_numeric_value($task_id);

        if (!$this->can_show_tasks()) {

            app_redirect("forbidden");
        }
        if (!$this->is_reserv_mang()) {
            app_redirect("forbidden");
        }
        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("created_by") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            if ($status_id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            } else {
                if ($key == $this->login_user->id) {
                    $team_members_dropdown[] = array("id" => $key, "text" => $value/*, "isSelected" => true*/);
                } else {
                    $team_members_dropdown[] = array("id" => $key, "text" => $value);
                }
            }
        }
        $drivers = $this->Drivers_model->get_details(array( "deleted" => 0))->getResult();
            $drivers_dropdown = array(array("id" => "", "text" => app_lang("driver_name")));
            foreach ($drivers as $driver) {
                $drivers_dropdown[] = array("id" => $driver->id, "text" => $driver->driver_nm);
            }
        $cars_type = $this->Cars_type_model->get_details(array( "deleted" => 0))->getResult();
            $cars_type_dropdown = array(array("id" => "", "text" => app_lang("car_type")));
            foreach ($cars_type as $car_type) {
                $cars_type_dropdown[] = array("id" => $car_type->id, "text" => $car_type->car_type);
            }

            $cities = $this->Cities_model->get_details(array( "deleted" => 0))->getResult();
            $cities_dropdown = array(array("id" => "", "text" => "-".app_lang("city_name")."-"));
            foreach ($cities as $city) {
                $cities_dropdown[] = array("id" => $city->id, "text" => $city->city_name);
            }
            $maintask_cls = $this->Maintask_clsifications_model->get_details(array( "deleted" => 0))->getResult();
            $maintask_clsifications_dropdown = array(array("id" => "", "text" => "-".app_lang("cls_title")."-"));
            foreach ($maintask_cls as $maintask_clss) {
                $maintask_clsifications_dropdown[] = array("id" => $maintask_clss->id, "text" => $maintask_clss->title);
            }

            $clients = $this->Clients_model->get_details(array( "deleted" => 0/*,'client_status'=> "1"*/))->getResult();
            $clients_dropdown = array(array("id" => "", "text" => "-" . app_lang("client_name") . "-"));
            foreach ($clients as $client) {
                $clients_dropdown[] = array("id" => $client->id, "text" => $client->company_name);
            }

        

        $view_data['tab'] = $tab;
        
        $view_data['mang'] = 'reservmang';
        
        
        $view_data['task_id'] = $task_id;
        $view_data['view_type'] = "project_tasks";
        $view_data['status_id'] = $status_id;
        $view_data['open_add'] = $open_add;
        $view_data['filter'] = $this->request->getPost('filter');
        $view_data['main_filter'] = $this->request->getPost('main_filter');
        $view_data['user_role_id'] = $this->login_user->role_id;
        $view_data['main_task'] = $this->Tasks_model->get_details(array("id" => $task_id))->getRow();

        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data['can_create_tasks'] = $this->can_create_subtasks();
        $view_data['can_update_maintask_after_closed'] = $this->can_update_maintask_after_closed();


        $view_data['can_edit_tasks'] = $this->can_edit_subtasks();
        //$view_data['can_delete_tasks'] = $this->can_delete_tasks();
        //$view_data["show_milestone_info"] = $this->can_view_milestones();

        $view_data['drivers_dropdown'] = json_encode($drivers_dropdown);
        $view_data['cars_type_dropdown'] = json_encode($cars_type_dropdown);
        $view_data['clients_dropdown'] = json_encode($clients_dropdown);
        $view_data['cities_dropdown'] = json_encode($cities_dropdown);
        $view_data['maintask_clsifications_dropdown'] = json_encode($maintask_clsifications_dropdown);
        $view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();
        //$view_data['assigned_to_dropdown'] = $this->_get_project_members_dropdown_list($supplier_id);
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        //$exclude_status_ids = $this->get_removed_task_status_ids($supplier_id);
        $view_data['task_statuses'] = $this->Task_status_model->get_details()->getResult();

        $view_data["show_assigned_tasks_only"] = get_array_value($this->login_user->permissions, "show_assigned_tasks_only");

        return $this->template->rander("subtasks/my_tasks", $view_data);
    }

    function supply_mang($task_id=0,$tab = "", $status_id = 0) {
     
        validate_numeric_value($task_id);

        if (!$this->can_show_tasks()) {
            app_redirect("forbidden");
        }
        if (!$this->is_supply_mang()) {
            app_redirect("forbidden");
        }
        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("created_by") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            if ($status_id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            } else {
                if ($key == $this->login_user->id) {
                    $team_members_dropdown[] = array("id" => $key, "text" => $value/*, "isSelected" => true*/);
                } else {
                    $team_members_dropdown[] = array("id" => $key, "text" => $value);
                }
            }
        }

        $suppliers = $this->Suppliers_model->get_details(array( "deleted" => 0))->getResult();
            $suppliers_dropdown = array(array("id" => "", "text" => app_lang("supplier_name")));
            foreach ($suppliers as $supplier) {
                $suppliers_dropdown[] = array("id" => $supplier->id, "text" => $supplier->name);
            }
            $drivers = $this->Drivers_model->get_details(array( "deleted" => 0))->getResult();
            $drivers_dropdown = array(array("id" => "", "text" => app_lang("driver_name")));
            foreach ($drivers as $driver) {
                $drivers_dropdown[] = array("id" => $driver->id, "text" => $driver->driver_nm);
            }
            $cities = $this->Cities_model->get_details(array( "deleted" => 0))->getResult();
            $cities_dropdown = array(array("id" => "", "text" => "-".app_lang("city_name")."-"));
            foreach ($cities as $city) {
                $cities_dropdown[] = array("id" => $city->id, "text" => $city->city_name);
            }
            $cars_type = $this->Cars_type_model->get_details(array( "deleted" => 0))->getResult();
            $cars_type_dropdown = array(array("id" => "", "text" => app_lang("car_type")));
            foreach ($cars_type as $car_type) {
                $cars_type_dropdown[] = array("id" => $car_type->id, "text" => $car_type->car_type);
            }

        $view_data['tab'] = $tab;
        $view_data['task_id'] = $task_id;
        $view_data['mang'] = 'supplymang';
        $view_data['view_type'] = "project_tasks";
        $view_data['status_id'] = $status_id;
        $view_data['filter'] = $this->request->getPost('filter');
        $view_data['main_filter'] = $this->request->getPost('main_filter');
        $view_data['user_role_id'] = $this->login_user->role_id;
        $view_data['main_task'] = $this->Tasks_model->get_details(array("id" => $task_id))->getRow();

        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data['can_create_tasks'] = $this->can_create_subtasks();
        $view_data['selected_year'] = $this->session->get('selected_year');
        //$view_data['can_delete_tasks'] = $this->can_delete_tasks();
        //$view_data["show_milestone_info"] = $this->can_view_milestones();
        $view_data['suppliers_dropdown'] = json_encode($suppliers_dropdown);
        $view_data['cities_dropdown'] = json_encode($cities_dropdown);
        $view_data['drivers_dropdown'] = json_encode($drivers_dropdown);
        $view_data['cars_type_dropdown'] = json_encode($cars_type_dropdown);
        $view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();
        //$view_data['assigned_to_dropdown'] = $this->_get_project_members_dropdown_list($supplier_id);
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);


        //$exclude_status_ids = $this->get_removed_task_status_ids($supplier_id);
        $view_data['task_statuses'] = $this->Task_status_model->get_details()->getResult();

        $view_data["show_assigned_tasks_only"] = get_array_value($this->login_user->permissions, "show_assigned_tasks_only");

        return $this->template->rander("subtasks/supply/my_tasks_supply", $view_data);
    }


    function list_data($task_id) {

       
        $specific_user_id = $this->request->getPost('specific_user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $quick_filter = $this->request->getPost('quick_filter');
       // if ($quick_filter) {
        //    $status = "";
       // } else {
            $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
            $main_status_id = $this->request->getPost('main_status_id') ? implode(",", $this->request->getPost('main_status_id')) : "";
       // }

            /*$myfile = fopen("C:\\xampp\\htdocs\\digital-crm\\newfile.txt", "w") or die("Unable to open file!");
            foreach($_POST as $key => $value){
                $txt = "$key is $value \n";
                fwrite($myfile, $txt);
            }
            
            fclose($myfile);*/

        $options = array(
            "task_id" => $task_id,
            "specific_user_id" => $specific_user_id,
            "filter" => $this->request->getPost('filter'),
            "main_filter" => $this->request->getPost('main_filter'),
            "custom_fields" => $custom_fields,
            "deleted_client"=> $this->request->getPost('deleted_client'),
            "status_ids" => $status,
            //"main_status_id" => $main_status_id,
            "main_task_status_f" => $this->request->getPost("main_task_status_f"),
            "mang" => "supply",
            "is_admin" => $this->login_user->is_admin?"yes":"no",
            "supplier_id" => $this->request->getPost("supplier_id"),
            "service_type" => $this->request->getPost("service_type"),
            "driver_id" => $this->request->getPost("driver_id"),
            "sub_tasks_id_f" => $this->request->getPost("sub_tasks_id_f"),
            "pnt_task_id_f" => $this->request->getPost("pnt_task_id_f"),
            "guest_nm_f" => $this->request->getPost("guest_nm_f"),
            "description_f" => $this->request->getPost("description_f"),
            "guest_phone_f" => $this->request->getPost("guest_phone_f"),
            "company_name_f" => $this->request->getPost("company_name_f"),
            "clients_contact_f" => $this->request->getPost("clients_contact_f"),
            "christ_num_f" => $this->request->getPost("christ_num_f"),
            "inv_num_f" => $this->request->getPost("inv_num_f"),
            "start_date" => $this->request->getPost("start_date"),
            "end_date" => $this->request->getPost("end_date"),
            "city_name_f" => $this->request->getPost("city_name_f"),
            "driver_nm_f" => $this->request->getPost("driver_nm_f"),
            "car_type_f" => $this->request->getPost("car_type_f"),
            "selected_year" => $this->session->get('selected_year'),
            "pnt_task_id" => $this->request->getPost("pnt_task_id"),
            "out_date_f" => $this->request->getPost("out_date_f"),
            "out_date_f_t" => $this->request->getPost("out_date_f_t"),
            "tmp_return_date_f" => $this->request->getPost("tmp_return_date_f"),
            "tmp_return_date_f_t" => $this->request->getPost("tmp_return_date_f_t"),
            "sales_act_return_date_f" => $this->request->getPost("sales_act_return_date_f"),
            "sales_act_return_date_f_t" => $this->request->getPost("sales_act_return_date_f_t"),
            "inv_day_count_f" => $this->request->getPost("inv_day_count_f"),
            "note_f" => $this->request->getPost("note_f"),
            "project_nm_f" => $this->request->getPost("project_nm_f"),
            "created_by_f" => $this->request->getPost("created_by_f"),
            "monthly_f" => $this->request->getPost("monthly_f"),
            "service_type_f" => $this->request->getPost("service_type_f"),
            //"rec_inv_status_f" => $this->request->getPost("rec_inv_status_f"),
            //"monthly_f" => $this->session->get('selected_year').'/'.$this->request->getPost("monthly_f"),
            "car_type_id" => $this->request->getPost("car_type_id"),
            "city_id" => $this->request->getPost("city_id"),
            "cls_id" => $this->request->getPost("cls_id"),
            "priority_id" => $this->request->getPost("priority_id"),
            "out_date" => $this->request->getPost("out_date"),
            "tmp_return_date" => $this->request->getPost("tmp_return_date"),
            "sales_act_return_date" => $this->request->getPost("sales_act_return_date"),
            "unread_status_user_id" => $this->login_user->id,
            "quick_filter" => $quick_filter,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $all_options = append_server_side_filtering_commmon_params($options);
        //$this->Tasks_model->get_details($all_options);
        $result = $this->Sub_tasks_model->get_details_new($all_options);

         if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_row($data, $custom_fields);
        }

        $result["data"] = $result_data;
        echo json_encode($result);
    }

    private function _row_data($id) {
       
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id,"mang" => "reserv", "custom_fields" => $custom_fields);
        $data = $this->Sub_tasks_model->get_details_new($options)->getRow();

        //$this->init_project_permission_checker($data->project_id);

        return $this->_make_row($data, $custom_fields);
    }

    private function _make_row($data, $custom_fields) {

        $unread_comments_class = "unread-comments-of-tasks";
        $checkbox_class = "";
            $icon = "<i data-feather='message-circle' class='icon-16  unread-comments-of-tasks-icon'></i>";
            if ($data->status_key_name == "done") {
            $checkbox_class = "checkbox-checked";
            $title_class = "text-line-through text-off";
        }

            
            $check_status = js_anchor("<span class='$checkbox_class mr15 float-end' style='width:16px;height: 16px;border:none;margin-left:0px !important;margin-right:0px !important'></span>", array('title' => "", "class" => "js-task", "data-id" => $data->id,  "data-act" => "update-task-status-checkbox")) ."<span class=' float-end'>".modal_anchor(get_uri("subtasks/task_view"), $data->sub_task_id , array("title" => app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "reservmang",  "data-modal-lg" => "2"))."</span>";
        $guest_nm = $data->guest_nm ? modal_anchor(get_uri("subtasks/task_view"), $data->guest_nm , array("title" => app_lang('reserv_mang').'-'.app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "reservmang",  "data-modal-lg" => "2")) : '-';

        if ($data->priority_id) {
            $guest_nm .= "<span class='float-end' style='margin-left:5px; margin-right:5px;' title='" . app_lang('priority') . "'>
                            <span class='sub-task-icon priority-badge' style='background: $data->priority_color'><i data-feather='$data->priority_icon' class='icon-14'></i></span><span class='small'> $data->priority_title</span>
                      </span>";
        }

        $st = $data->status_key_name ? app_lang($data->status_key_name) : $data->status_title;
        $status = js_anchor($st, array("style" => "margin-top: 5px;background-color: $data->status_color",'title' => "إضغط لإلغاء المهمة", "class" => "badge", "data-id" => $data->id, "data-value" => $data->status_id, "data-act" => "update-task-status_2"));

       // $status .= $data->dynamic_status_id==4 ? js_anchor("إقفال", array("style" => "background-color: #000; color:#fff;",'title' => app_lang('close'), "class" => "badge", "data-odai" => 1, "data-id" => $data->id, "data-action-url" => get_uri("subtasks/delete"), "data-action" => "delete-confirmation")):'';

        

        $options = "";
        if($data->deleted == 0){
        if($this->can_update_maintask_after_closed()){
            if ($this->can_edit_subtasks($data->id)) {
                if($data->dynamic_status_id!=4 || $this->can_edit_subtasks_after_closed()){
            $options .= modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang("reserv_mang").' - '.app_lang('sub_task'). " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "reservmang"));
        }
        }
        if ($this->can_delete_subtasks()) {
            $options.=js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_tasks'), "class" => "delete", "data-odai" => 1, "data-id" => $data->id, "data-action-url" => get_uri("subtasks/delete"), "data-action" => "delete-confirmation"));

        }
        if (!$this->can_delete_subtasks()) {
            $options.=js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_tasks'), "class" => "delete", "data-odai" => 0, "data-id" => $data->id, "data-action-url" => get_uri("subtasks/delete"), "data-action" => "delete-confirmation"));

        }
        }else{
        if($data->main_task_status==1){
        if ($this->can_edit_subtasks($data->id)) {
            if($data->dynamic_status_id!=4 || $this->can_edit_subtasks_after_closed()){
            $options .= modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang("reserv_mang").' - '.app_lang('sub_task'). " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "reservmang"));
        }
        }
        if ($this->can_delete_subtasks()) {
            $options.=js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_tasks'), "class" => "delete", "data-odai" => 1, "data-id" => $data->id, "data-action-url" => get_uri("subtasks/delete"), "data-action" => "delete-confirmation"));
            
        }
    }
}
        }else{
            $options.=js_anchor("<i data-feather='check' class='icon-16'></i>", array('title' => app_lang('record_undone'), "class" => "undo", "data-odai" => 1, "data-id" => $data->id, "data-action-url" => get_uri("subtasks/undo"), "data-action" => "delete"));
        }
       



        $act_tmp_return_date = "-";
        if ($data->tmp_return_date && is_date_exists($data->tmp_return_date)) {
            $act_tmp_return_date = $data->tmp_return_date;//format_to_date($data->tmp_return_date, false);
            if (get_my_local_time("Y-m-d") > $data->tmp_return_date && $data->dynamic_status_id == "3") {
                $act_tmp_return_date = "<span class='text-danger'>" . $act_tmp_return_date . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->tmp_return_date && $data->dynamic_status_id == "3") {
                $act_tmp_return_date = "<span class='text-warning'>" . $act_tmp_return_date . "</span> ";
            }
        }


        $myout_date = "-";
        if ($data->out_date && is_date_exists($data->out_date)) {
            $myout_date = $data->out_date;//format_to_date($data->out_date, false);
            if (get_my_local_time("Y-m-d") > $data->out_date && $data->dynamic_status_id != "4") {
                $myout_date = "<span class='text-danger'>" . $data->out_date . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->out_date && $data->dynamic_status_id != "4") {
                $myout_date = "<span class='text-warning'>" . $data->out_date . "</span> ";
            }
        }

        $created_by = "-";

        if ($data->created_by) {
            $image_url = get_avatar($data->assigned_to_avatar);
            $assigned_to_user = "$data->assigned_to_user";
            $created_by = get_team_member_profile_link($data->created_by, $assigned_to_user);

                $created_by = get_team_member_profile_link($data->created_by, $assigned_to_user);
            
        }


       
        $exp_out_time_txt=date_create($data->exp_out_time);
        $supplier_name=$data->supplier_name ?$data->supplier_name:' - ';
        $main_task_status_color=$data->main_task_status==1 ? '#4a9d27':'#e50f16bd';
        $main_status_txt=$data->main_task_status==1 ? app_lang('open'):app_lang('closed');
        $main_task_status = "<a class='badge' data-id='".$data->s_id."' data-value='".$data->main_task_status."' data-act='update-mytask-status' style='background-color: ".$main_task_status_color."'>" . $main_status_txt . "</a> ";
        if($data->pnt_task_id!=null){
        $row_data =  array(
            $data->status_color,
            $check_status,
            $data->s_id,
            $main_task_status,
            $data->client_name ? $data->client_name : '-',
            $data->project_title,
            $data->clients_contact_name ? $data->clients_contact_name : '-',
            $data->chr_number ? $data->chr_number : '__',
            $data->inv_number ? $data->inv_number : '__',
            $data->main_description,
            $guest_nm,
            $data->guest_phone ? $data->guest_phone : '-',

            $data->city_name ? $data->city_name : '-',
            $this->get_service_type($data->service_type),
            $data->driver_name ? $data->driver_name : '-',
            
            $data->mycar_type?$data->mycar_type:'_',
            //$myout_date,
            $data->exp_out_time && $data->exp_out_time!='00:00:01'?$myout_date.' '.date_format($exp_out_time_txt,"h:i A"):$myout_date.' _',
            $act_tmp_return_date,
            $data->sales_act_return_date && is_date_exists($data->sales_act_return_date)?$data->sales_act_return_date:'_',
            $data->inv_day_count && $data->inv_day_count>0?$data->inv_day_count:'_',
            $data->note,
            $created_by,
            $data->cls_color,
            $data->main_created_date,
            $status
           
        );
    }else{
        $row_data =  array(
            "#fff",
            '',
            $data->s_id,
            $main_task_status,
            '-',
            "",
            '-',
            '__',
            '__',
            '',
            '',
            '-',

            $data->city_name ? $data->city_name : '-',
            $this->get_service_type($data->service_type),
            $data->driver_name ? $data->driver_name : '-',
            
            $data->mycar_type?$data->mycar_type:'_',
            //$myout_date,
            $data->exp_out_time && $data->exp_out_time!='00:00:01'?$myout_date.' '.date_format($exp_out_time_txt,"h:i A"):$myout_date.' _',
            $act_tmp_return_date,
            $data->sales_act_return_date && is_date_exists($data->sales_act_return_date)?$data->sales_act_return_date:'_',
            $data->inv_day_count && $data->inv_day_count>0?$data->inv_day_count:'_',
            $data->note,
            $created_by,
            $data->cls_color,
            $data->main_created_date,
            $status
           
        );
    }

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        //$row_data[] = modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit').' '.app_lang('sub_task'). " #$data->id", "data-post-id" => $data->id));






               

        $row_data[] = $options;

        return $row_data;
    }

    function union_all(){
        $result=$this->Sub_tasks_model->get_details()->getResult();
        $emparray = array();
        foreach ($result as $data) {
        $emparray[] = $data;
    }
        echo json_encode($emparray);
    }



    function list_data_supply() {

       
        $specific_user_id = $this->request->getPost('specific_user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $quick_filter = $this->request->getPost('quick_filter');
       // if ($quick_filter) {
        //    $status = "";
       // } else {
            $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
            $main_status_id = $this->request->getPost('main_status_id') ? implode(",", $this->request->getPost('main_status_id')) : "";
       // }

        $options = array(
            "task_id" => $this->request->getPost('task_id'),
            "specific_user_id" => $specific_user_id,
            "filter" => $this->request->getPost('filter'),
            "custom_fields" => $custom_fields,
            "deleted_client"=> $this->request->getPost('deleted_client'),
            "start_date" => $this->request->getPost("start_date"),
            "end_date" => $this->request->getPost("end_date"),
            "status_ids" => $status,
            //"main_status_id" => $main_status_id,
            "main_task_status_f" => $this->request->getPost("main_task_status_f"),
            "mang" => "supply",
            "selected_year" => $this->session->get('selected_year'),
            "is_admin" => $this->login_user->is_admin?"yes":"no",
            "supplier_id" => $this->request->getPost("supplier_id"),
            "service_type" => $this->request->getPost("service_type"),
            "priority_id" => $this->request->getPost("priority_id"),

            "sub_tasks_id_f" => $this->request->getPost("sub_tasks_id_f"),
            "supplier_f" => $this->request->getPost("supplier_f"),
            "car_status_f" => $this->request->getPost("car_status_f"),
            "car_number_f" => $this->request->getPost("car_number_f"),
            "act_return_date_f" => $this->request->getPost("act_return_date_f"),
            "act_return_date_f_t" => $this->request->getPost("act_return_date_f_t"),
            "act_out_date_f" => $this->request->getPost("act_out_date_f"),
            "act_out_date_f_t" => $this->request->getPost("act_out_date_f_t"),
            "day_count_f" => $this->request->getPost("day_count_f"),
            "dres_number_f" => $this->request->getPost("dres_number_f"),
            "amount_f" => $this->request->getPost("amount_f"),
            "note2_f" => $this->request->getPost("note2_f"),
            "guest_nm_f" => $this->request->getPost("guest_nm_f"),
            "description_f" => $this->request->getPost("description_f"),
            "project_nm_f" => $this->request->getPost("project_nm_f"),
            "company_name_f" => $this->request->getPost("company_name_f"),
            "pnt_task_id_f" => $this->request->getPost("pnt_task_id_f"),
            "driver_nm_f" => $this->request->getPost("driver_nm_f"),
            "city_name_f" => $this->request->getPost("city_name_f"),
            "monthly_f" => $this->request->getPost("monthly_f"),
            "created_by_f" => $this->request->getPost("created_by_f"),
            "reservmang_status" => $this->request->getPost("reservmang_status"),
            //"service_type_f" => $this->request->getPost("service_type_f"),
            "rec_inv_status_f" => $this->request->getPost("rec_inv_status_f"),
            "start_date" => $this->request->getPost("start_date"),
            "end_date" => $this->request->getPost("end_date"),
            "car_type_id" => $this->request->getPost("car_type_id"),
            "car_type_f" => $this->request->getPost("car_type_f"),

        
            "act_out_date" => $this->request->getPost("act_out_date")?$this->request->getPost("act_out_date"):'',
            "act_return_date" => $this->request->getPost("act_return_date")?$this->request->getPost("act_return_date"):'',
            "unread_status_user_id" => $this->login_user->id,
            "quick_filter" => $quick_filter,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Sub_tasks_model->get_details_new($all_options);

         if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_supply_row($data, $custom_fields);
        }

        $result["data"] = $result_data;
        echo json_encode($result);
    }

    private function _row_supply_data($id) {
       
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id,"mang" => "supply", "custom_fields" => $custom_fields);
        $data = $this->Sub_tasks_model->get_details_new($options)->getRow();

        //$this->init_project_permission_checker($data->project_id);

        return $this->_make_supply_row($data, $custom_fields);
    }

    private function _make_supply_row($data, $custom_fields) {

        $unread_comments_class = "unread-comments-of-tasks";
        $checkbox_class = "checkbox-blank";
            $icon = "<i data-feather='message-circle' class='icon-16  unread-comments-of-tasks-icon'></i>";
            if ($data->status_key_name == "done") {
            $checkbox_class = "checkbox-checked";
            $title_class = "text-line-through text-off";
        }

            
            $check_status = js_anchor("<span <span class='$checkbox_class mr15 float-end' style='width:16px;height: 16px;border:none;margin-left:0px !important;margin-right:0px !important'></span>", array('title' => "", "class" => "js-task", "data-id" => $data->id,  "data-act" => "update-task-status-checkbox")) ."<span class='mr10 float-end'>".modal_anchor(get_uri("subtasks/task_view_supply"), $data->sub_task_id , array("title" => app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "supplymang",  "data-modal-lg" => "2"))."</span>";

             //$supplier_name=$data->supplier_name ?$data->supplier_name:' - ';

            //$main_task = $this->Tasks_model->get_details(array("id" => $data->pnt_task_id))->getRow();

        $project_title = $data->project_title ? modal_anchor(get_uri("subtasks/task_view_supply"), $data->project_title , array("title" => $data->project_title, "data-post-id" => $data->id, "data-post-mang" => "supplymang",  "data-modal-lg" => "2")) : '__';

        $guest_nm = $data->guest_nm ? modal_anchor(get_uri("subtasks/task_view_supply"), $data->guest_nm , array("title" => app_lang('reserv_mang').'-'.app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "supplymang",  "data-modal-lg" => "2")) : '-';


        $supplier_name =  modal_anchor(get_uri("subtasks/task_view_supply"), $data->supplier_name ?$data->supplier_name: '__' , array("title" => app_lang("supply_mang").'-'.app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "supplymang",  "data-modal-lg" => "2")) ;
        /*if ($data->priority_id) {
            $guest_nm .= "<span class='float-end' style='margin-left:5px; margin-right:5px;' title='" . app_lang('priority') . "'>
                            <span class='sub-task-icon priority-badge' style='background: $data->priority_color'><i data-feather='$data->priority_icon' class='icon-14'></i></span><span class='small'> $data->priority_title</span>
                      </span>";
        }*/

        $st = $data->status_key_name ? app_lang($data->status_key_name) : $data->status_title;
        $status = js_anchor($st, array("style" => "background-color: $data->status_color",'title' => "إضغط لإلغاء المهمة", "class" => "badge", "data-id" => $data->id, "data-value" => $data->status_id, "data-act" => "update-task-status_1"));

        

        $options = "";
        if($this->can_update_maintask_after_closed()){
            if ($this->can_edit_subtasks()) {
                if($data->dynamic_status_id!=4 || $this->can_edit_subtasks_after_closed()){
            $options .= modal_anchor(get_uri("subtasks/task_modal_form_supply"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang("supply_mang").' - '.app_lang('sub_task'). " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-task_id" => $data->s_id, "data-post-mang" => "supplymang"));
        }
        }
        }else{
        if($data->main_task_status==1 ){
            if ($this->can_edit_subtasks()) {
                if($data->dynamic_status_id!=4 || $this->can_edit_subtasks_after_closed()){
            $options .= modal_anchor(get_uri("subtasks/task_modal_form_supply"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang("supply_mang").' - '.app_lang('sub_task'). " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-task_id" => $data->s_id, "data-post-mang" => "supplymang"));
        }
        }
        
        /*if ($this->can_delete_subtasks()) {
            $options.=js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_tasks'), "class" => "delete", "data-odai" => 1, "data-id" => $data->id, "data-action-url" => get_uri("subtasks/delete"), "data-action" => "delete-confirmation"));
            
        }*/
    }
}

        $act_return_date_text = "__";
        if ($data->act_return_date && is_date_exists($data->act_return_date)) {
            $act_return_date_text = $data->act_return_date;//format_to_date($data->act_return_date, false);
            if (get_my_local_time("Y-m-d") > $data->act_return_date && $data->dynamic_status_id != "4") {
                $act_return_date_text = "<span class='text-danger'>" . $act_return_date_text . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->act_return_date && $data->dynamic_status_id != "4") {
                $act_return_date_text = "<span class='text-warning'>" . $act_return_date_text . "</span> ";
            }
        }

       

        $myout_date = "__";
        if ($data->act_out_date && is_date_exists($data->act_out_date)) {
            $myout_date = $data->act_out_date;//format_to_date($data->act_out_date, false);
            if (get_my_local_time("Y-m-d") > $data->act_out_date && $data->dynamic_status_id == "1") {
                $myout_date = "<span class='text-danger'>" . $myout_date . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->act_out_date && $data->dynamic_status_id == "1") {
                $myout_date = "<span class='text-warning'>" . $myout_date . "</span> ";
            }
        }
        

       
        $act_out_time_txt=date_create($data->act_out_time);
        $act_return_time_txt=date_create($data->act_return_time);
         $created_by = "-";

        if ($data->created_by) {
            $image_url = get_avatar($data->assigned_to_avatar);
            $assigned_to_user = "$data->assigned_to_user";
            $created_by = get_team_member_profile_link($data->created_by, $assigned_to_user);

                $created_by = get_team_member_profile_link($data->created_by, $assigned_to_user);
            
        }

        $main_task_status_color=$data->main_task_status==1 ? '#4a9d27':'#e50f16bd';
        $main_status_txt=$data->main_task_status==1 ? app_lang('open'):app_lang('closed');
        $main_task_status = "<span class='badge' style='background-color: ".$main_task_status_color."'>" . $main_status_txt . "</span> ";
        

        if($data->pnt_task_id!=null){
        $row_data =  array(
            $data->status_color,
            $check_status,
            $data->s_id,
            $main_task_status,
            $data->client_name ? $data->client_name : '-',
            $project_title,
            $data->chr_number ? $data->chr_number : '-',
            $data->inv_number ? $data->inv_number : '-',
            $data->main_description,
            $guest_nm,
            $supplier_name,
            $data->driver_name ? $data->driver_name : '-',
            $data->mycar_type?$data->mycar_type:'_',
            $data->city_name ? $data->city_name : '-',
            $data->car_status ? $data->car_status:'_',
            $data->car_number ? $data->car_number:'_',
            $this->get_rec_inv_status($data->rec_inv_status),
            
            $data->act_out_time && $data->act_out_time!='00:00:01' ? $myout_date.' '.date_format($act_out_time_txt,"h:i A"):$myout_date.' '.'_',
            $data->act_return_time && $data->act_return_time!='00:00:01' ? $act_return_date_text.' '.date_format($act_return_time_txt,"h:i A"):$act_return_date_text.' '.'_',
            
            $data->day_count ? $data->day_count:'_',
            $data->dres_number ? $data->dres_number:'_',
            $data->amount ? $data->amount:'_',
            //$data->act_return_time && $data->act_return_time!='00:00:01'?date_format($data->act_return_time,"h:i A"):'_',
            $data->note_2,
            $data->cls_color,
            $created_by,
            $data->main_created_date,
            
            $status
           
        );
    }else{

        $row_data =  array(
            "#fff",
            '',
            $data->s_id,
            $main_task_status,
            $data->client_name ? $data->client_name : '-',
            '',
            $data->chr_number ? $data->chr_number : '-',
            $data->inv_number ? $data->inv_number : '-',
            $data->main_description,
            '',
            '',
            '-',

            '-',
            '_',
            '_',
            '',
            '',
            '_',
            '_',
            
            '_',
            '_',
            '_',
            //$data->act_return_time && $data->act_return_time!='00:00:01'?date_format($data->act_return_time,"h:i A"):'_',
            '',
            $data->cls_color,
            '',
            $data->main_created_date,

            ''
           
        );
    }

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }


               

        $row_data[] = $options;

        return $row_data;
    }

    /****************************************************************/

    function subtasks_report() {
        if (!$this->can_show_subtasks_report()) {
            app_redirect("forbidden");
        }
       
       
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

       

        return $this->template->rander("subtasks/subtast_report", $view_data);


    }
    function list_data_report() {

       
        $specific_user_id = $this->request->getPost('specific_user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $quick_filter = $this->request->getPost('quick_filter');
       // if ($quick_filter) {
        //    $status = "";
       // } else {
            $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
       // }
             if ($this->is_reserv_mang()) {
                $mang="reserv";
                
            }else{
                $mang="supply";
                
            }
            

        $options = array(
            "task_id" => $this->request->getPost('task_id'),
            "specific_user_id" => $specific_user_id,
            "filter" => $this->request->getPost('filter'),
            "custom_fields" => $custom_fields,
            "mang" => $mang,
            "is_admin" => $this->login_user->is_admin?"yes":"no",
            //'user_role_id' => $this->login_user->role_id,
            "service_type" => $this->request->getPost("service_type"),
            "selected_year" => $this->session->get('selected_year'),
            "priority_id" => $this->request->getPost("priority_id"),
            "act_out_date" => $this->request->getPost("act_out_date")?$this->request->getPost("act_out_date"):'',
            "act_return_date" => $this->request->getPost("act_return_date")?$this->request->getPost("act_return_date"):'',
            "unread_status_user_id" => $this->login_user->id,
            "quick_filter" => $quick_filter,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Sub_tasks_model->get_details($all_options);

         if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_report_row($data, $custom_fields);
        }

        $result["data"] = $result_data;
        echo json_encode($result);
    }

    private function _row_report_data($id) {
       
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id,"mang" => "supply", "custom_fields" => $custom_fields);
        $data = $this->Sub_tasks_model->get_details($options)->getRow();

        //$this->init_project_permission_checker($data->project_id);

        return $this->_make_report_row($data, $custom_fields);
    }

    private function _make_report_row($data, $custom_fields) {

        $unread_comments_class = "unread-comments-of-tasks";
        $checkbox_class = "checkbox-blank";
            $icon = "<i data-feather='message-circle' class='icon-16  unread-comments-of-tasks-icon'></i>";
            if ($data->status_key_name == "done") {
            $checkbox_class = "checkbox-checked";
            $title_class = "text-line-through text-off";
        }

            $mang="reservmang";
            $url="task_view";
            $tit="reserv_mang";
            if ($this->is_reserv_mang()) {
                $mang="reservmang";
                $url="task_view";
                $tit="reserv_mang";
            }else{
                $mang="supplymang";
                $url="task_view_supply";
                $tit="supply_mang";
            }
            $dres="";

           $check_status = modal_anchor(get_uri("subtasks/".$url), "<span class='$checkbox_class ml15 float-start' style='width:16px;height: 16px;border:none;'></span>"."<span class='mr10 float-end'>".$data->sub_task_id."</span>" , array("title" => app_lang($tit).'-'.app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => $mang,  "data-modal-lg" => "2"));

           $dres = modal_anchor(get_uri("subtasks/".$url), $data->dres_number , array("title" => app_lang($tit).'-'.app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => $mang,  "data-modal-lg" => "2"));


             //$supplier_name=$data->supplier_name ?$data->supplier_name:' - ';

            $main_task = $this->Tasks_model->get_details(array("id" => $data->pnt_task_id))->getRow();

        $project_title = $main_task->project_title ? modal_anchor(get_uri("subtasks/task_view_supply"), $main_task->project_title , array("title" => app_lang("supply_mang").'-'.app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "supplymang",  "data-modal-lg" => "2")) : '__';

        

        $st = $data->status_key_name ? app_lang($data->status_key_name) : $data->status_title;
        $status = js_anchor($st, array("style" => "background-color: $data->status_color",'title' => "إضغط لإلغاء المهمة", "class" => "badge", "data-id" => $data->id, "data-value" => $data->status_id, "data-act" => "update-task-status_1"));

        

        $options = "";
        

       

       
        
        $row_data =  array(
            $data->status_color,
            $check_status,
            $dres,
            $data->amount,
            $main_task->company_name,
            $project_title
            //$status
           
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }


               

        $row_data[] = $options;

        return $row_data;
    }





    /***************************************************************/


    function dfg(){
        $a=$this->Sub_tasks_model->get_task_befor24();
        $res='';
        $npw_date = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
        foreach ($a->task_statuses as $data) {
            $t1 = strtotime( $data->o_date );
            $t2 = strtotime( $npw_date->format('Y-m-d H:i:s') );
            $diff = $t1 - $t2;
            $hours = $diff / ( 60 * 60);
            $res .= $data->id.'  '.$hours.' - '. $data->guest_nm.' , ';
        }

        echo $res;
       /* task add/edit modal */
       
    }

    function task_modal_form()
    {

        $id = $this->request->getPost('id');
        $model_info = $this->Sub_tasks_model->get_one($id);

        $createBy = $this->Users_model->get_one($model_info->created_by);

        $login_user_id = $this->Users_model->login_user_id();
        if (!$login_user_id) {
            show_404();
        }

        $user_info = $this->Users_model->get_one($login_user_id);
        if ((!($createBy->first_name == $user_info->first_name) && !($createBy->last_name == $user_info->last_name)) && (!$this->login_user->is_admin)) {
            // $permession_data["permession"] = "Just who create this task and admin can edit";
            // return $this->template->view('subtasks/modal_form', $permession_data);
            return;
        }
        ;
        if (!$this->is_reserv_mang()) {
            app_redirect("forbidden");
        }
        // $id = $this->request->getPost('id');
        $add_type = $this->request->getPost('add_type');
        $mang = $this->request->getPost('mang') ? $this->request->getPost('mang') : 'reservmang';
        $last_id = $this->request->getPost('last_id');
        $task_id = $this->request->getPost('task_id');
        $deleted_client = $this->request->getPost('deleted_client');

        // $model_info = $this->Sub_tasks_model->get_one($id);
        $myoptions = array(
            "id" => $id
            ,
            "mang" => "reserv"
            ,
            "selected_year" => $this->session->get('selected_year')
        );
        $model_info2 = $this->Sub_tasks_model->get_details($myoptions)->getRow();

        $task_id = $this->request->getPost('task_id') ? $this->request->getPost('task_id') : $model_info->pnt_task_id;

        $final_project_id = $task_id;
        if ($add_type == "multiple" && $last_id) {
            //we've to show the lastly added information if it's the operation of adding multiple tasks
            $model_info = $this->Sub_tasks_model->get_one($last_id);

            //if we got lastly added task id, then we have to initialize all data of that in order to make dropdowns
            $final_project_id = $model_info->pnt_task_id;
        }

        $view_data = $this->_initialize_all_related_data_of_project();

        //$login_user->role_id
        if ($id) {
            if ($model_info2->dynamic_status_id == 4 && !$this->can_edit_subtasks_after_closed()) {
                app_redirect("forbidden");
            }
            if (!$this->can_edit_subtasks($id)) {
                app_redirect("forbidden");


            }

        } else {
            if (!$this->can_create_subtasks($task_id ? true : false)) {
                app_redirect("forbidden");
            }
        }

        $view_data['model_info'] = $model_info;

        $view_data['supplier_name'] = $this->Suppliers_model->get_one($model_info->supplier_id);//$data->supplier_name ?$data->supplier_name:' - ';
        $view_data['created_by_nm'] = $this->Users_model->get_one($model_info->created_by);
        $view_data['updated_by_nm'] = $this->Users_model->get_one($model_info->updated_by);
        $view_data['rec_inv_status'] = $this->get_rec_inv_status($model_info->rec_inv_status);
        //$view_data["suppliers_dropdown"] = $this->_get_suppliers_dropdown();

        if (!$id) {
            $view_data["cars_type_dropdown"] = $this->_get_cars_type_dropdown();
            $view_data["drivers_dropdown"] = $this->_get_drivers_dropdown();
        } else {
            $view_data["cars_type_dropdown"] = $this->_get_cars_type_dropdown2($model_info->car_type_id);
            $view_data["drivers_dropdown"] = $this->_get_drivers_dropdown2($model_info->driver_id);
        }
        $view_data["tasks_dropdown"] = $this->_get_tasks_dropdown();
        $view_data["cities_dropdown"] = $this->_get_cities_dropdown();


        //projects dropdown is necessary on add multiple tasks
        $view_data["add_type"] = $add_type;

        $view_data["mang"] = 'reservmang';

        //$view_data["mang"] = $mang;
        $view_data['task_id'] = $task_id;




        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("sub_tasks", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        //clone task
        $is_clone = $this->request->getPost('is_clone');
        $view_data['is_clone'] = $is_clone;

        $view_data['view_type'] = $this->request->getPost("view_type");

        //$view_data['has_checklist'] = $this->Checklist_items_model->get_details(array("task_id" => $id))->resultID->num_rows;
        //$view_data['has_sub_task'] = count($this->Tasks_model->get_all_where(array("parent_task_id" => $id, "deleted" => 0))->getResult());
        $priorities = $this->Task_priority_model->get_details()->getResult();
        $priorities_dropdown = array(array("id" => "", "text" => "-"));
        foreach ($priorities as $priority) {
            $priorities_dropdown[] = array("id" => $priority->id, "text" => $priority->title);
        }

        $view_data['priorities_dropdown'] = $priorities_dropdown;


        return $this->template->view('subtasks/modal_form', $view_data);
    }


    function task_modal_form_supply() {


            $id = $this->request->getPost('id');
            $model_info = $this->Sub_tasks_model->get_one($id);
    
            $createBy = $this->Users_model->get_one($model_info->created_by);
    
            $login_user_id = $this->Users_model->login_user_id();
            if (!$login_user_id) {
                show_404();
            }
    
            $user_info = $this->Users_model->get_one($login_user_id);
            if ((!($createBy->first_name == $user_info->first_name) && !($createBy->last_name == $user_info->last_name))&&(!$this->login_user->is_admin)) {
                // $permession_data["permession"] = "Just who create this task and admin can edit";
                // return $this->template->view('subtasks/modal_form', $permession_data);
                 return;
            };

        if (!$this->is_supply_mang()) {
            app_redirect("forbidden");
        }
        // $id = $this->request->getPost('id');
        $add_type = $this->request->getPost('add_type');
        //$mang = $this->request->getPost('mang') ? $this->request->getPost('mang') : 'reservmang';
        $last_id = $this->request->getPost('last_id');
        $task_id = $this->request->getPost('task_id');
        $deleted_client = $this->request->getPost('deleted_client');
        //$model_info = $this->Sub_tasks_model->get_details(array("id" => $id,"deleted_client" => $deleted_client))->getRow();
        // $model_info = $this->Sub_tasks_model->get_one($id);
        
        
        $task_id = $this->request->getPost('task_id') ? $this->request->getPost('task_id') : $model_info->pnt_task_id;

        $final_project_id = $task_id;
        if ($add_type == "multiple" && $last_id) {
            //we've to show the lastly added information if it's the operation of adding multiple tasks
            $model_info = $this->Sub_tasks_model->get_one($last_id);

            //if we got lastly added task id, then we have to initialize all data of that in order to make dropdowns
            $final_project_id = $model_info->pnt_task_id;
        }

        if ($model_info->rec_inv_status == "rec_inv" && !$this->login_user->is_admin) {
            return;
        }
        ;

        $view_data = $this->_initialize_all_related_data_of_project();
$myoptions = array(
            "id" => $id
            ,"mang" => "supply"
             );
        $model_info2 = $this->Sub_tasks_model->get_details($myoptions)->getRow();
       
        

        if ($id ) {
            $get_supply_status = $this->Sub_tasks_model->get_subtask_status($id,"supply")->getRow();
            //$get_reserv_status = $this->Sub_tasks_model->get_subtask_status($id,"reserv")->getRow();
            $after_review=1;
            if(!$this->can_edit_subtasks_after_review()){
                //($get_supply_status->dynamic_status_id==6 || $get_supply_status->dynamic_status_id==2)
              if($get_supply_status->dynamic_status_id==2){
                    $after_review=2;
                }else{
                    $after_review=1;
                }
            }else{
                $after_review=1;
            }

            if($model_info2->dynamic_status_id==4 && !$this->can_edit_subtasks_after_closed()){
                    app_redirect("forbidden");
                }

            if (!$this->can_edit_subtasks()) {
                app_redirect("forbidden");

            }
        } else {
            if (!$this->can_create_subtasks($task_id ? true : false)) {
                app_redirect("forbidden");
            }
        }

        $view_data['model_info'] = $model_info;
        $view_data['main_task'] = $this->Tasks_model->get_details(array("id" => $task_id))->getRow();
        $view_data["suppliers_dropdown"] = $this->_get_suppliers_dropdown();
        $view_data["tasks_dropdown"] = $this->_get_tasks_dropdown();

        
         //projects dropdown is necessary on add multiple tasks
        $view_data["add_type"] = $add_type;
            $view_data["mang"] = 'supplymang';

        
        //$view_data["mang"] = $mang;
        $view_data['task_id'] = $task_id;
        $view_data['after_review'] = $after_review;
        $view_data['supplier_id'] = $model_info->supplier_id;
        $view_data['created_by_nm']= $this->Users_model->get_one($model_info->created_by);
        $view_data['updated_by_nm']= $this->Users_model->get_one($model_info->updated_by);
        $view_data['driver_name']= $this->Drivers_model->get_one($model_info->driver_id)->driver_nm;
        $view_data['city_name']= $this->Cities_model->get_one($model_info->city_id)->city_name;
        $view_data['car_type']= $this->Cars_type_model->get_one($model_info->car_type_id)->car_type;



        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("sub_tasks", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        //clone task
        $is_clone = $this->request->getPost('is_clone');
        $view_data['is_clone'] = $is_clone;

        $view_data['view_type'] = $this->request->getPost("view_type");

        $car_status = $this->Car_status_model->get_details()->getResult();
        $car_status_dropdown = array();
        foreach ($car_status as $carstatus) {
            $car_status_dropdown[] = array("id" => $carstatus->id, "text" => $carstatus->car_status_txt);
        }


        $view_data['car_status_dropdown'] = $car_status_dropdown;



        //$view_data['has_checklist'] = $this->Checklist_items_model->get_details(array("task_id" => $id))->resultID->num_rows;
        //$view_data['has_sub_task'] = count($this->Tasks_model->get_all_where(array("parent_task_id" => $id, "deleted" => 0))->getResult());


        return $this->template->view('subtasks/supply/modal_form_supply', $view_data);
    }




    function get_car_status() {
        $car_status = $this->Car_status_model->get_details()->getResult();
        $car_status_dropdown = array();
        foreach ($car_status as $carstatus) {
            $car_status_dropdown[] = array("id" => $carstatus->id, "text" => $carstatus->car_status_txt);
        }
        return json_encode($car_status);

    }
    function task_view($task_id = 0) {
        validate_numeric_value($task_id);

        if (!$this->is_reserv_mang()) {
            app_redirect("forbidden");
        }

        $view_type = "";
        $is_supplier = $this->request->getPost('is_supplier');
        //$deleted_client = $this->request->getPost('deleted_client');
        if ($task_id) { //details page
            $view_type = "details";
        } else { //modal view
            $task_id = $this->request->getPost('id');
            
        }


        $model_info = $this->Sub_tasks_model->get_details(array("id" => $task_id,"mang" => "reserv","selected_year" => $this->session->get('selected_year')))->getRow();
        if (!$model_info->id) {
            show_404();
        }
        $get_status = $this->Sub_tasks_model->get_subtask_status($task_id,"supply")->getRow();
       

        $view_data = $this->_initialize_all_related_data_of_project();

        $view_data['show_assign_to_dropdown'] = true;
        if ($this->login_user->user_type == "client" && !get_setting("client_can_assign_tasks")) {
            $view_data['show_assign_to_dropdown'] = false;
        }

        $view_data['can_edit_tasks'] = $this->can_edit_subtasks($task_id);

        $view_data["suppliers_dropdown"] = $this->_get_suppliers_dropdown();
        $view_data['model_info'] = $model_info;
        $_task = $this->Tasks_model->get_details(array("id" => $model_info->pnt_task_id))->getRow();
        $view_data['main_task']=$_task;
        //$view_data['project_client_name'] = $this->Clients_model->get_one($_task->project_client_id)->company_name;
        
        $view_data['task_status'] = $model_info->status_key_name ? app_lang($model_info->status_key_name) : $model_info->status_title;
        $view_data['status_color'] = $model_info->status_color;

        $view_data['task_status2'] = $get_status->status_key_name ? app_lang($get_status->status_key_name) : $get_status->status_title;
        $view_data['status_color2'] = $get_status->status_color;

        $view_data['is_supplier'] = $is_supplier;
        $view_data['service_type_txt'] = $this->get_service_type($model_info->service_type);
        $view_data['rec_inv_status'] = $this->get_rec_inv_status($model_info->rec_inv_status);
      
            $view_data['mang'] = 'reservmang';
        
       

        
        $view_data['task_id'] = $task_id;

        $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("sub_tasks", $task_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        
        $view_data['can_create_tasks'] = $this->can_create_subtasks();
        $view_data['can_update_maintask_after_closed'] = $this->can_update_maintask_after_closed();
        $view_data['can_edit_subtasks_after_closed'] = $this->can_edit_subtasks_after_closed();

        //$view_data['parent_task_title'] = $this->Tasks_model->get_details($model_info->pnt_task_id)->getRow()->company_name;

        $view_data["view_type"] = $view_type;

    

        if ($view_type == "details") {
            return $this->template->rander('subtasks/view', $view_data);
        } else {
            return $this->template->view('subtasks/view', $view_data);
        }
    }



    function task_view_supply($task_id = 0) {
        validate_numeric_value($task_id);
        if (!$this->is_supply_mang()) {
            app_redirect("forbidden");
        }
        $view_type = "";
        $mang_type = "mang1";
        $is_supplier = $this->request->getPost('is_supplier');
        //$deleted_client = $this->request->getPost('deleted_client');
        if ($task_id) { //details page
            $view_type = "details";
        } else { //modal view
            $task_id = $this->request->getPost('id');
            
        }

        $model_info = $this->Sub_tasks_model->get_details(array("id" => $task_id,"mang" => "supply","selected_year" => $this->session->get('selected_year')))->getRow();
        if (!$model_info->id) {
            show_404();
        }
       $get_status = $this->Sub_tasks_model->get_subtask_status($task_id,"reserv")->getRow();

        $view_data = $this->_initialize_all_related_data_of_project();

        $view_data['show_assign_to_dropdown'] = true;
        if ($this->login_user->user_type == "client" && !get_setting("client_can_assign_tasks")) {
            $view_data['show_assign_to_dropdown'] = false;
        }

        $view_data['can_edit_tasks'] = $this->can_edit_subtasks();

        $view_data["suppliers_dropdown"] = $this->_get_suppliers_dropdown();
        $view_data['model_info'] = $model_info;
        $view_data['main_task'] = $this->Tasks_model->get_details(array("id" => $model_info->pnt_task_id))->getRow();
        $view_data['task_status'] = $model_info->status_key_name ? app_lang($model_info->status_key_name) : $model_info->status_title;
        $view_data['status_color'] = $model_info->status_color;

        $view_data['task_status2'] = $get_status->status_key_name ? app_lang($get_status->status_key_name) : $get_status->status_title;
        $view_data['status_color2'] = $get_status->status_color;

        $view_data['is_supplier'] = $is_supplier;
        $view_data['service_type_txt'] = $this->get_service_type($model_info->service_type);
        $view_data['rec_inv_status'] = $this->get_rec_inv_status($model_info->rec_inv_status);
            $view_data['mang'] = 'supplymang';
       
       

        
        $view_data['task_id'] = $task_id;

        $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("sub_tasks", $task_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        
        $view_data['can_create_tasks'] = $this->can_create_subtasks();
        $view_data['can_update_maintask_after_closed'] = $this->can_update_maintask_after_closed();
        $view_data['can_edit_subtasks_after_closed'] = $this->can_edit_subtasks_after_closed();

        //$view_data['parent_task_title'] = $this->Tasks_model->get_details($model_info->pnt_task_id)->getRow()->company_name;

        $view_data["view_type"] = $view_type;
        $view_data["mang_type"] = $mang_type;

    

        if ($view_type == "details") {
            return $this->template->rander('subtasks/supply/view_supply', $view_data);
        } else {
            return $this->template->view('subtasks/supply/view_supply', $view_data);
        }
    }



    private function _initialize_all_related_data_of_project() {
        //we have to check if any defined project exists, then go through with the project id
        
            $related_data = $this->get_all_related_data_of_project();

            
            $view_data['suppliers_dropdown'] = $related_data["suppliers_dropdown"];
            $view_data['servicetype_dropdown'] = array(
                array("id" => "with_driver", "text" => "سيارة بسائق","isSelected" => true),
                array("id" => "no_driver", "text" => "سيارة بدون سائق"),
                array("id" => "deliver", "text" => "توصيلة"),
                array("id" => "no_car", "text" => "سائق بدون سيارة")
            );



            /*

            */

        //$exclude_status_ids = $this->get_removed_task_status_ids($project_id);
        $view_data['statuses'] = $this->Task_status_model->get_details()->getResult();

       

        return $view_data;
    }


    private function get_all_related_data_of_project() {

       
            //$suppliers_model = model('App\Models\Suppliers_model');

            $suppliers = $this->Suppliers_model->get_details(array( "deleted" => 0))->getResult();
            $suppliers_dropdown = array(array("id" => "", "text" => "-"));
            foreach ($suppliers as $supplier) {
                $suppliers_dropdown[] = array("id" => $supplier->id, "text" => $supplier->name);
            }

           

            //get labels suggestion

            return array(
                
                "suppliers_dropdown" => $suppliers_dropdown,
                
            );
    }


    function update_sub_task_id(){
        //$ff=0;
        $result_main = $this->Tasks_model->get_details(array( "deleted" => 0))->getResult();
        
        foreach ($result_main as $main_data) {
            $mid= $main_data->id;
            $options = array(
            "pnt_task_id" => $mid
             );

            
            
            echo 'main_id:'.$mid.'<br>';
        $result = $this->Sub_tasks_model->get_details($options)->getResult();
        for ($i=0; $i < count($result); $i++) { 
            //$mxount=$this->Sub_tasks_model->get_max_id($task_id);
            $data["sub_task_id"] =$result[$i]->pnt_task_id.".".$i+1;
            
            $save_id = $this->Sub_tasks_model->ci_save($data, $result[$i]->id);
            echo $result[$i]->pnt_task_id.".".$i+1 .'<br>';
            
            //echo "<br>".$result[$i]->id."<br>";
        }
            
        }

        

    }

    function reupdate_sub_task_id($id){
        //$ff=0;
        $main_data = $this->Tasks_model->get_one($id);
        
        //foreach ($result_main as $main_data) {
            $mid= $main_data->id;
            $options = array(
            "pnt_task_id" => $mid
             );

            
            
            echo 'main_id:'.$mid.'<br>';
        $result = $this->Sub_tasks_model->get_details($options)->getResult();
        for ($i=0; $i < count($result); $i++) { 
            //$mxount=$this->Sub_tasks_model->get_max_id($task_id);
            $data["sub_task_id"] =$result[$i]->pnt_task_id.".".$i+1;
            
            $save_id = $this->Sub_tasks_model->ci_save($data, $result[$i]->id);
            echo $result[$i]->pnt_task_id.".".$i+1 .'<br>';
            
            //echo "<br>".$result[$i]->id."<br>";
        }
            
        //}

        

    }

    function save_task() {

        /*if (!$this->is_supply_mang()) {
            app_redirect("forbidden");
        }
        if (!$this->is_reserv_mang()) {
            app_redirect("forbidden");
        }*/
        $task_id = $this->request->getPost('task_id');
        $id = $this->request->getPost('id');
        $add_type = $this->request->getPost('add_type');
        //$supplier_id = $this->request->getPost('supplier_id');

        
        


        $now = get_current_utc_time();

        $is_clone = $this->request->getPost('is_clone');
        $main_task_id = "";
        if ($is_clone && $id) {
            $main_task_id = $id; //store main task id to get items later
            $id = ""; //on cloning task, save as new
        }

        //$this->init_project_permission_checker($project_id);

        if ($id) {

            if (!$this->can_edit_subtasks($id)) {
                app_redirect("forbidden");
            }

            //$this->check_sub_tasks_statuses($this->request->getPost('status_id'), $id,$this->request->getPost('service_type'),"with");
        } else {
            if (!$this->can_create_subtasks()) {
                app_redirect("forbidden");
            }
        }
       
            $mang = $this->request->getPost('mang');
        

        

        $guest_nm = $this->request->getPost('guest_nm');
        $guest_phone = $this->request->getPost('guest_phone');
        $status_id = $this->request->getPost('status_id') ? $this->request->getPost('status_id'):2;
        $car_expens = $this->request->getPost('car_expens');
        $car_expens_stmnt = $this->request->getPost('car_expens_stmnt');
        $city_id = $this->request->getPost('city_id');
        $priority_id = $this->request->getPost('priority_id');
        

        $driver_nm = $this->request->getPost('driver_nm');
        $driver_id = $this->request->getPost('driver_id');
        $out_date = $this->request->getPost('out_date');
        $tmp_return_date = $this->request->getPost('tmp_return_date');
        $sales_act_return_date = $this->request->getPost('sales_act_return_date');
        $exp_out_time = $this->request->getPost('exp_out_time')?convert_time_to_24hours_format($this->request->getPost('exp_out_time')):'00:00:01';
        $dres_number = $this->request->getPost('dres_number');
        $car_type = $this->request->getPost('car_type');
        $car_type_id = $this->request->getPost('car_type_id');
        $service_type = $this->request->getPost('service_type');
        $note = $this->request->getPost('note');
        


        $supplier_id = $this->request->getPost('supplier_id');
        $car_number = $this->request->getPost('car_number');
        $act_out_time = $this->request->getPost('act_out_time')?convert_time_to_24hours_format($this->request->getPost('act_out_time')):'00:00:01';

        $act_return_time = $this->request->getPost('act_return_time')?convert_time_to_24hours_format($this->request->getPost('act_return_time')):'00:00:01';
        $act_return_date = $this->request->getPost('act_return_date');

        $act_out_date = $this->request->getPost('act_out_date');
        $car_status = $this->request->getPost('car_status');
        $day_count = $this->request->getPost('day_count');
        $inv_day_count = $this->request->getPost('inv_day_count');
        $rec_inv_status = $this->request->getPost('rec_inv_status');
        $note_2 = $this->request->getPost('note_2');
        $amount = $this->request->getPost('amount');


        $start_date = $this->request->getPost('start_date');
        $end_date = $this->request->getPost('end_date');
        $start_time = $this->request->getPost('start_time')?convert_time_to_24hours_format($this->request->getPost('start_time')):'00:00:01';
        $end_time = $this->request->getPost('end_time')?convert_time_to_24hours_format($this->request->getPost('end_time')):'00:00:01';
        $sub_task_note = $this->request->getPost('sub_task_note');
        $booking_period = $this->request->getPost('booking_period');

        $enterd_status=1;



        $act_out_time_Time1 = new \DateTime($act_out_time ? $act_out_time:'', new \DateTimeZone("Asia/Riyadh"));
    
    if($mang=="reservmang" && $this->is_reserv_mang()){
        $this->validate_submitted_data(array(
            "task_id" => "numeric|required",
            "guest_nm" => "required",
            "service_type" => "required"
        ));

        /*if($id){
            $sub_task_info = $this->Sub_tasks_model->get_one($id);
            if($sub_task_info->enterd_status==1 || $sub_task_info->enterd_status==0){
                $enterd_status=1;
            }else if($sub_task_info->enterd_status==2 || $sub_task_info->enterd_status==12){
                $enterd_status=12;
            }
        }else{
            $enterd_status=1;
        }*/
        if($this->request->getPost('start_time')){
            $data["start_time"]= $start_time;
        }else{
            $data["start_time"]= "00:00:01";
        }

        if($this->request->getPost('end_time')){
            $data["end_time"]= $end_time;
        }else{
            $data["end_time"]= "00:00:01";
        }
        $data = array(
            "guest_nm" => $guest_nm,
            "guest_phone" => $guest_phone,
            "car_expens_stmnt" => $car_expens_stmnt,
            "city_id" => $city_id,
            "car_expens" => $car_expens,
            "pnt_task_id" => $task_id,
            "priority_id" => $priority_id,
            "inv_day_count" => $inv_day_count,
            "driver_nm" => $driver_nm,
            "driver_id" => $driver_id,
            "car_type" => $car_type,
            "car_type_id" => $car_type_id,
            "service_type" => $service_type,
            "out_date" => $out_date,
            "tmp_return_date" => $tmp_return_date,
            "sales_act_return_date" => $sales_act_return_date,
            "note" => $note,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "start_time" => $start_time,
            "end_time" => $end_time,
            "sub_task_note" => $sub_task_note,
            "booking_period" => $booking_period,
            
            
            
        );
        if($this->request->getPost('exp_out_time')){
            $data["exp_out_time"]= $exp_out_time;
        }else{
            $data["exp_out_time"]= "00:00:01";
        }
 

    }

    if($mang=="supplymang" && $this->is_supply_mang()){
        
       
        $car_status_id = $this->request->getPost('car_status_id');
        $car_status_list = $this->Car_status_model->get_one($car_status_id);
       
        if($car_status){
        if(!$car_status_id){
            $s_data = array(
            "car_status_txt" => $car_status,
        );
            $dsave_id = $this->Car_status_model->ci_save($s_data);
            $car_status_id=$dsave_id;
        
        }else{
            $car_status_id=$car_status_list->id;
        }
    }
        $data = array(

            "supplier_id" => $supplier_id,
            "act_out_date" => $act_out_date,
            "day_count" => $day_count,
            "car_status_id" => $car_status_id,
            "act_return_date" => $act_return_date,
            "dres_number" => $dres_number,
            "amount" => $amount,
            "note_2" => $note_2,
            "car_number" => $car_number,
            "car_status" => $car_status,
            "rec_inv_status" => $rec_inv_status,
            
            
        );

        if($this->request->getPost('act_out_time')){
            $data["act_out_time"]= $act_out_time;
        }else{
            $data["act_out_time"]= "00:00:01";
        }
        if($this->request->getPost('act_return_time')){
            $data["act_return_time"]= $act_return_time;
        }else{
            $data["act_return_time"]= "00:00:01";
        }
        if($id){
           
            $check_updated_by = $this->Sub_tasks_model->get_one($id);
            if(!$check_updated_by->updated_by){
                $data["updated_by"] = $this->login_user->id;
            }
        }

    }
   
           
           if(!$id){
            $mxount=$this->Sub_tasks_model->get_max_id($task_id);
            $data["sub_task_id"] =$task_id.'.'.$mxount->t_count+1;
            $data["created_by"] =$this->login_user->id;
            $data["status_id"] =2;//$status_id;
            $data["created_at"] = $now;
           }

       
        $data = clean_data($data);
        $copy_checklist = $this->request->getPost("copy_checklist");
        $next_recurring_date = "";
        $save_id = $this->Sub_tasks_model->ci_save($data, $id);
        if ($save_id) {

            

            $activity_log_id = get_array_value($data, "activity_log_id");

            $new_activity_log_id = save_custom_fields("sub_tasks", $save_id, $this->login_user->is_admin, $this->login_user->user_type, $activity_log_id);

            if ($id) {
                //updated
                log_notification("sub_task_updated", array("project_id" => 0, "sub_task_id" => $save_id, "activity_log_id" => $new_activity_log_id ? $new_activity_log_id : $activity_log_id));
                /*$sub_taskss = $this->Sub_tasks_model->get_one($id);
                if($sub_taskss->status_id != 5){
                    
                    $this->auto_save_task_status($id,4);
                }*/

                    
            } else {
                //created
                //$this->reupdate_sub_task_id($task_id);
                log_notification("sub_task_created", array("project_id" => 0, "sub_task_id" => $save_id));
                
            }
            $eres=$this->Sub_tasks_model->get_one($save_id);
            $main_task_updated=$this->save_maintask_status_new($eres->pnt_task_id,2);
            if($mang=="reservmang"){
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'pnt_id' => $eres->pnt_task_id, 'main_task_updated' => $main_task_updated, 'message' => app_lang('record_saved'), "add_type" => $add_type));
        }else {
            echo json_encode(array("success" => true, "data" => $this->_row_supply_data($save_id), 'id' => $save_id, 'pnt_id' => $eres->pnt_task_id, 'main_task_updated' => $main_task_updated, 'message' => app_lang('record_saved'), "add_type" => $add_type));
        }



        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }


    function save_maintask_status_new($id = 0,$status_id=1) {
        $closed_date = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
        $data = array(
            "is_closed" => $status_id,
            "closed_by"=> $this->login_user->id,
            "closed_reason" => "تم اغلاق المهمة تلقائيا لإكتمال البيانات",
            "closed_date"=> $closed_date->format('Y-m-d H:i:s'),
        );

        if($this->check_main_tasks_statuses3($status_id, $id)){

        $task_info = $this->Tasks_model->get_details(array("id" => $id))->getRow();

        if ($task_info->status_id !== $status_id) {
            $data["status_changed_at"] = get_current_utc_time();
        }

        $save_id = $this->Tasks_model->ci_save($data, $id);
        return true;
    }else{
        return false;
    }
        

    }

    private function check_main_tasks_statuses3($status_id = 0, $parent_task_id = 0) {
        $res=true;
     
        

        
        $tasks = $this->Tasks_model->get_one($parent_task_id);
        if($tasks->christening_number && $tasks->invoice_number){
            $sub_tasks = $this->Sub_tasks_model->check_subtask_status(array("pnt_task_id" => $parent_task_id, "deleted" => 0))->getResult();
            if(count($sub_tasks)>0){
        foreach ($sub_tasks as $sub_task) {
            if ($sub_task->reserv_status != 4 || $sub_task->supplier_status != 4) {
                if ($sub_task->reserv_status != 5 || $sub_task->supplier_status != 5) {
                //this sub task isn't done yet, show error and exit
                
                $res=false;
            }
        }
        }
    }else{
    $res=false;
  } 
    }else{
    $res=false;
  } 
    return $res;

    }

    function all_tasks_kanban($task_id) {

        if (!$this->is_reserv_mang()) {
            app_redirect("forbidden");
        }
        

        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("created_by") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            if ($key == $this->login_user->id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            } else {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            }
        }

        $suppliers = $this->Suppliers_model->get_details(array( "deleted" => 0))->getResult();
            $suppliers_dropdown = array(array("id" => "", "text" => app_lang("supplier")));
            foreach ($suppliers as $supplier) {
                $suppliers_dropdown[] = array("id" => $supplier->id, "text" => $supplier->name);
            }
      
            $view_data['mang'] = 'reservmang';
        

        $view_data['task_id'] = $task_id;
        $view_data['main_task'] = $this->Tasks_model->get_details(array("id" => $task_id))->getRow();
        //$view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();
        $view_data['filter'] = $this->request->getPost('filter');
        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        //$view_data['can_delete_tasks'] = $this->can_delete_tasks();
        //$view_data["show_milestone_info"] = $this->can_view_milestones();

        $view_data['suppliers_dropdown'] = json_encode($suppliers_dropdown);
        $view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();
        
        $view_data['can_create_tasks'] = $this->can_create_subtasks(false);

        $view_data['task_statuses'] = $this->Task_status_model->get_details()->getResult();
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        return $this->template->rander("subtasks/kanban/all_tasks", $view_data);
    }

    function all_tasks_kanban_supply($task_id) {
        if (!$this->is_supply_mang()) {
            app_redirect("forbidden");
        }
        
        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("created_by") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            if ($key == $this->login_user->id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            } else {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            }
        }

        $suppliers = $this->Suppliers_model->get_details(array( "deleted" => 0))->getResult();
            $suppliers_dropdown = array(array("id" => "", "text" => app_lang("supplier")));
            foreach ($suppliers as $supplier) {
                $suppliers_dropdown[] = array("id" => $supplier->id, "text" => $supplier->name);
            }


            $view_data['mang'] = 'supplymang';
       
        $view_data['task_id'] = $task_id;

        $view_data['filter'] = $this->request->getPost('filter');
        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data['suppliers_dropdown'] = json_encode($suppliers_dropdown);
        $view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();
        $view_data['main_task'] = $this->Tasks_model->get_details(array("id" => $task_id))->getRow();
        
        $view_data['can_create_tasks'] = $this->can_create_subtasks(false);

        $view_data['task_statuses'] = $this->Task_status_model->get_details()->getResult();
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        return $this->template->rander("subtasks/supply/kanban/all_tasks", $view_data);
    }




    function all_tasks_kanban_data($task_id=0) {


        $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
        
       

        $specific_user_id = $this->request->getPost('specific_user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

         $quick_filter = $this->request->getPost('quick_filter');
        if ($quick_filter) {
            $status = "";
        } else {
            $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
        }


        $options = array(
            "task_id" => $task_id,
            "specific_user_id" => $specific_user_id,
            "deleted_client"=> $this->request->getPost('deleted_client'),
            "filter" => $this->request->getPost('filter'),
            'user_role_id' => $this->login_user->role_id,
            "supplier_id" => $this->request->getPost("supplier_id"),
            "service_type" => $this->request->getPost("service_type"),
            "priority_id" => $this->request->getPost("priority_id"),
            "custom_fields" => $custom_fields,
            //"project_status" => "open",
            "mang" => "reserv",
            "selected_year" => $this->session->get('selected_year'),
            "is_admin" => $this->login_user->is_admin?"yes":"no",
            "search" => $this->request->getPost('search'),
            "status_ids" => $status,
            //"unread_status_user_id" => $this->login_user->id,
            //"show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id(),
            "quick_filter" => $quick_filter,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );



        $view_data["tasks"] = $this->Sub_tasks_model->get_kanban_details($options)->getResult();
        
      
            $view_data['mang'] = 'reservmang';
        

        $statuses = $this->Task_status_model->get_details(array("hide_from_kanban" => 0));

        $view_data["total_columns"] = $statuses->resultID->num_rows;
        $view_data["columns"] = $statuses->getResult();
        $view_data['can_edit_tasks'] = $this->can_edit_subtasks();

        return $this->template->view('subtasks/kanban/kanban_view', $view_data);
    }


    function all_tasks_kanban_supply_data($task_id=0) {


        $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
        
       

        $specific_user_id = $this->request->getPost('specific_user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

         $quick_filter = $this->request->getPost('quick_filter');
        if ($quick_filter) {
            $status = "";
        } else {
            $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
        }


        $options = array(
            "task_id" => $task_id,
            "specific_user_id" => $specific_user_id,
            "deleted_client"=> $this->request->getPost('deleted_client'),
            "filter" => $this->request->getPost('filter'),
            'user_role_id' => $this->login_user->role_id,
            "supplier_id" => $this->request->getPost("supplier_id"),
            "priority_id" => $this->request->getPost("priority_id"),
            "custom_fields" => $custom_fields,
            //"project_status" => "open",
            "mang" => "supply",
            "selected_year" => $this->session->get('selected_year'),
            "is_admin" => $this->login_user->is_admin?"yes":"no",
            "search" => $this->request->getPost('search'),
            "status_ids" => $status,
            //"unread_status_user_id" => $this->login_user->id,
            //"show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id(),
            "quick_filter" => $quick_filter,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );



        $view_data["tasks"] = $this->Sub_tasks_model->get_kanban_details($options)->getResult();

        $statuses = $this->Task_status_model->get_details(array("hide_from_kanban" => 0));

        $view_data['mang'] = 'supplymang';


        $view_data["total_columns"] = $statuses->resultID->num_rows;
        $view_data["columns"] = $statuses->getResult();
        $view_data['can_edit_tasks'] = $this->can_edit_subtasks();

        return $this->template->view('subtasks/supply/kanban/kanban_view', $view_data);
    }

    private function check_sub_tasks_statuses($status_id = 0, $id = 0,$service_type="",$isWith="m") {
     if ($status_id !== "4") {
            //parent task isn't marking as done
            return true;
        }
        $sub_tasks = $this->Sub_tasks_model->get_details(array("id" => $id, "deleted" => 0))->getResult();

        foreach ($sub_tasks as $sub_task) {
            $s=0;
            $myservice_type="";
            if($isWith=="with"){
                $myservice_type=$service_type;
            }else{
                $myservice_type=$sub_task->service_type;
            }
            if($myservice_type=="deliver"){
                $s=0;
            }elseif ($myservice_type=="with_driver" || $myservice_type=="no_driver") {
                if($sub_task->day_count && $sub_task->inv_day_count /*&& $sub_task->tmp_return_date*/){
                    $s=0;
                } else { $s=1; }
            }
            if (!$sub_task->guest_nm || !$sub_task->out_date || !$sub_task->car_type || !$sub_task->supplier_id || !$sub_task->exp_out_time || $sub_task->exp_out_time=='00:00:01' || !$sub_task->act_out_time || $sub_task->act_out_time=='00:00:01' || !$sub_task->act_return_date || $s!=0 || !$sub_task->car_status) {
                //this sub task isn't done yet, show error and exit
                echo json_encode(array("success" => false, 'message' => app_lang("sub_task_data_not_completing")));
                exit();
            
        }
        if ($sub_task->rec_inv_status=="wait_inv"){
            echo json_encode(array("success" => false, 'message' => app_lang("rec_inv_status_iswait")));
                exit();
        }

        }
    }



    private function auto_check_sub_tasks_statuses($status_id = 0, $id = 0,$service_type="",$isWith="m") {

     $result_val=false;
        $sub_task = $this->Sub_tasks_model->get_one($id);

        //foreach ($sub_tasks as $sub_task) {
            $s=0;
            $myservice_type="";
            if($isWith=="with"){
                $myservice_type=$service_type;
            }else{
                $myservice_type=$sub_task->service_type;
            }
            if($myservice_type=="deliver"){
                $s=0;
            }elseif ($myservice_type=="with_driver" || $myservice_type=="no_driver") {
                if($sub_task->day_count && $sub_task->inv_day_count /*&& $sub_task->tmp_return_date*/){
                    $s=0; 
                } else { $s=1; }
            }
            if (!$sub_task->guest_nm || !$sub_task->out_date || $sub_task->car_type_id==0 || !$sub_task->supplier_id || !$sub_task->exp_out_time || $sub_task->exp_out_time=='00:00:01' || !$sub_task->act_out_time || $sub_task->act_out_time=='00:00:01' || !$sub_task->act_return_date || $s!=0 || !$sub_task->car_status|| $sub_task->rec_inv_status=="wait_inv") {
                //this sub task isn't done yet, show error and exit
                $result_val=false;

                //exit();
            
        }else{
            $result_val=true;
        }
       

        //}
        return $result_val;
    }
    function msg(){
        echo json_encode(array("success" => false, 'message' => "fsfdsfsdf"));
    }



    function save_task_status($id = 0,$status=0,$mang) {
        validate_numeric_value($id);
        //$this->access_only_team_members();
        //$now = get_current_utc_time();
     $closed_date = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
            
        $status_id = $status;//$this->request->getPost('value');
        $data = array(
            "status_id" => $status_id,
            "closed_user_id"=> $this->login_user->id,
            "closed_date"=> $closed_date->format('Y-m-d H:i:s'),
            
        );
        if($this->can_update_subtask_status()){

        $this->check_sub_tasks_statuses($status_id, $id,"","n");

        $task_info = $this->Sub_tasks_model->get_details(array("id" => $id))->getRow();

        //$this->init_project_permission_checker($task_info->project_id);
        if (!$this->can_edit_subtasks()) {
            app_redirect("forbidden");
        }

        

        $save_id = $this->Sub_tasks_model->ci_save($data, $id);

        if ($save_id) {
            $task_info = $this->Sub_tasks_model->get_details(array("id" => $id))->getRow();
            if($mang=="supplymang"){
            echo json_encode(array("success" => true, "data" => ( $this->_row_supply_data($save_id)), 'id' => $save_id, "message" => app_lang('record_saved')));
        }else{
            echo json_encode(array("success" => true, "data" => ( $this->_row_data($save_id)), 'id' => $save_id, "message" => app_lang('record_saved')));

        }

            log_notification("sub_task_updated", array("project_id" => 0, "sub_task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang("you_can't_update_subtask_status")));
        }
    }

    function auto_save_task_status($id = 0,$status=0) {
        validate_numeric_value($id);
        //$this->access_only_team_members();
        //$now = get_current_utc_time();
     //$closed_date = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
            
        $status_id = $status;//$this->request->getPost('value');
        $data = array(
            "status_id" => $status_id,
            "closed_user_id"=> $this->login_user->id,
            //"closed_date"=> $closed_date->format('Y-m-d H:i:s'),
            
        );

        if($this->auto_check_sub_tasks_statuses($status_id, $id,"","n")){

        $task_info = $this->Sub_tasks_model->get_details(array("id" => $id))->getRow();

       

        

        $save_id = $this->Sub_tasks_model->ci_save($data, $id);

        if ($save_id) {
            
            log_notification("sub_task_updated", array("project_id" => 0, "sub_task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
        } 

        } 
    }


    function save_task_status2($id = 0,$mang) {
        validate_numeric_value($id);

        $status_id = $this->request->getPost('value');
        $this->check_sub_tasks_statuses($status_id, $id);

        if ($status_id == "5") {
            echo json_encode(array("success" => true, "data" => "ok", 'id' => $id, 'status_id' => $status_id));
        }else{

            $this->save_task_status($id,$status_id,$mang);

        }
       
        
    }

    function save_task_status_kanpane() {
        if($this->can_update_subtask_status()){
        
        $id = $this->request->getPost('id');
        validate_numeric_value($id);

        $mang = $this->request->getPost('mang');
        $status_id = $this->request->getPost('status_id');
        $sort = $this->request->getPost('sort');
        $this->check_sub_tasks_statuses($status_id, $id);

        if ($status_id == "5") {
            echo json_encode(array("success" => true, "data" => "ok", 'id' => $id, 'status_id' => $status_id));
        }else{

            //$this->save_task_status($id,$status_id,$mang);
            $this->save_task_sort_and_status($id,$status_id,$sort);

        }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang("you_can't_update_subtask_status")));
        }
       
        
    }

    function post_cuses_modal_form() {
        $id = $this->request->getPost('id');
        $mang = $this->request->getPost('mang');

        $view_data['id'] = $id;
        $view_data['mang'] = $mang;
        return $this->template->view('subtasks/close_modal_note', $view_data);
    }

    function save_status(){

        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));
        $cancel_date = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
        $id=$this->request->getPost('id');
        $mang=$this->request->getPost('mang');
        $data = array(
            "cancel_reason" => $this->request->getPost('closed_reason'),
            "status_id" => 5,
            "cancel_user_id"=> $this->login_user->id,
            "cancel_date"=> $cancel_date->format('Y-m-d H:i:s'),

        );
        if($this->can_update_subtask_status()){
        if($id){
        
        $save_id = $this->Sub_tasks_model->ci_save($data, $id);
        if ($save_id) {

        $task_info = $this->Sub_tasks_model->get_details(array("id" => $save_id))->getRow(); //get data after save

        //$id = $this->request->getPost('id');

            if($mang=="supplymang"){
            echo json_encode(array("success" => true, "data" => ( $this->_row_supply_data($save_id)), 'id' => $save_id, "message" => app_lang('record_saved')));
        }else{
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }

        } else {
            echo json_encode(array("success" => false, 'message' => app_lang("you_can't_update_subtask_status")));
        }
    }

    private function _get_priorities_dropdown_list($priority_id = 0) {
        $priorities = $this->Task_priority_model->get_details()->getResult();
        $priorities_dropdown = array(array("id" => "", "text" => "- " . app_lang("priority") . " -"));

        //if there is any specific priority selected, select only the priority.
        $selected_status = false;
        foreach ($priorities as $priority) {
            if (isset($priority_id) && $priority_id) {
                if ($priority->id == $priority_id) {
                    $selected_status = true;
                } else {
                    $selected_status = false;
                }
            }

            $priorities_dropdown[] = array("id" => $priority->id, "text" => $priority->title, "isSelected" => $selected_status);
        }
        return json_encode($priorities_dropdown);
    }

    function get_status_charts($mang){
        $statistics = all_tasks_overview_para_widget(array("mang" => $mang));

            if ($statistics) {
                echo json_encode(array("success" => true, "statistics" => $statistics));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            }
    }


    function save_task_sort_and_status($id,$status_id,$sort) {
        

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        //$id = $this->request->getPost('id');
        $task_info = $this->Sub_tasks_model->get_one($id);

        if(!$this->can_update_subtask_status()){
            app_redirect("forbidden");
        }

        //$status_id = $this->request->getPost('status_id');
        $this->check_sub_tasks_statuses($status_id, $id,"","n");
        $data = array(
            "sort" => $sort
        );

        if ($status_id) {
            $data["status_id"] = $status_id;

           /* if ($task_info->status_id !== $status_id) {
                $data["status_changed_at"] = get_current_utc_time();
            }*/
        }

        $save_id = $this->Sub_tasks_model->ci_save($data, $id);
        /*

        $activity_log_id = get_array_value($data, "activity_log_id");
        $new_activity_log_id = save_custom_fields("sub_tasks", $save_id, $this->login_user->is_admin, $this->login_user->user_type, $activity_log_id);
        log_notification("sub_task_updated", array("project_id" => 0, "sub_task_id" => $save_id, "activity_log_id" => $new_activity_log_id ? $new_activity_log_id : $activity_log_id));
        */
        if ($save_id) {
            if ($status_id) {
                log_notification("sub_task_updated", array("project_id" => 0, "sub_task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
            }
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }
   


    private function get_service_type($key) {

        $text="";
        switch ($key) {
            case "with_driver":
                return "سيارة بسائق";
                break;
            case "no_driver":
                return "بدون سائق";
                break;
            case "deliver":
                return "توصيلة";
                break;
            case "no_car":
                return "سائق بدون سيارة";
                break;
            
            default:
                return '';
                break;
        }
    }

    private function get_rec_inv_status($key) {

        $text="";
        switch ($key) {
            case "wait_inv":
                return app_lang("wait_inv");
                break;
            case "rec_inv":
                return app_lang("rec_inv");
                break;
           
            
            default:
                return '';
                break;
        }
    }

     /*private function get_removed_task_status_ids($project_id = 0) {
        if (!$project_id) {
            return "";
        }

        $this->init_project_settings($project_id);
        return get_setting("remove_task_statuses");
    }*/

}

/* End of file company.php */
/* Location: ./app/controllers/company.php */
