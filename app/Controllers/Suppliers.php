<?php

namespace App\Controllers;

class Suppliers extends Security_Controller {

    protected $Task_priority_model;
    function __construct() {
        parent::__construct();
        if (!$this->supplier_permission()) {
            app_redirect("forbidden");
        }

        //check permission to access this module
        

        $this->Task_priority_model = model("App\Models\Task_priority_model");
    }

    /* load clients list view */

    function index($tab = "") {
        //$this->access_only_allowed_members();
if(!$this->supplier_permission()){
        app_redirect("forbidden");
    }

        $view_data['can_edit_suppliers'] = $this->can_edit_suppliers();
        //$view_data["show_project_info"] = $this->can_manage_all_projects() && !$this->has_all_projects_restricted_role();

        $view_data["show_own_suppliers_only_user_id"] = $this->show_own_suppliers_only_user_id();
        $view_data["allowed_client_groups"] = $this->allowed_client_groups;

        $view_data['tab'] = clean_data($tab);

        return $this->template->rander("suppliers/index", $view_data);
    }

   
    

    /* load client add/edit modal */

    function modal_form() {
       

        $supplier_id = $this->request->getPost('id');
        //$this->can_access_this_client($supplier_id);
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        if ($supplier_id) {
            if (!$this->can_edit_suppliers()) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_add_suppliers()) {
                app_redirect("forbidden");
            }
        }

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->request->getPost('view'); //view='details' needed only when loading from the client's details view
        //$view_data["ticket_id"] = $this->request->getPost('ticket_id'); //needed only when loading from the ticket's details view and created by unknown client
        
        $view_data['model_info'] = $this->Suppliers_model->get_one($supplier_id);
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();

        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("suppliers", $supplier_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $this->template->view('suppliers/modal_form', $view_data);
    }

    function my_modal_form() {
        if (!$this->can_edit_suppliers()) {
            app_redirect("forbidden");
        }

        $client_id = $this->request->getPost('id');
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));



        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->request->getPost('view'); //view='details' needed only when loading from the client's details view
        $view_data["ticket_id"] = $this->request->getPost('ticket_id'); //needed only when loading from the ticket's details view and created by unknown client
        $view_data['model_info'] = $this->Clients_model->get_one($client_id);
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();

        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $this->template->view('suppliers/my_modal_form', $view_data);
    }

    /* insert or update a client */

    function save() {
        $supplier_id = $this->request->getPost('id');
        if (!$this->can_edit_suppliers()) {
            app_redirect("forbidden");
        }

     

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "name" => "required"
        ));

        $company_name = $this->request->getPost('name');

        $data = array(
            "name" => $company_name,
            //"type" => $this->request->getPost('account_type'),
            "address" => $this->request->getPost('address'),
            "phone" => $this->request->getPost('phone'),
            "email" => $this->request->getPost('email'),
            //"zip" => $this->request->getPost('zip'),
            //"country" => $this->request->getPost('country'),
            //"phone" => $this->request->getPost('phone'),
            //"website" => $this->request->getPost('website'),
            //"vat_number" => $this->request->getPost('vat_number')
        );

        

       
        
        if ($this->login_user->is_admin || get_array_value($this->login_user->permissions, "client") === "all") {
            //user has access to change created by
            $data["created_by"] = $this->request->getPost('created_by') ? $this->request->getPost('created_by') : $this->login_user->id;
        } else if (!$supplier_id) {
            //the user hasn't permission to change created by but s/he can create new client
            $data["created_by"] = $this->login_user->id;
        }

        $data = clean_data($data);

       
        //$Suppliers_model = model('App\Models\Suppliers_model');

        $save_id = $this->Suppliers_model->ci_save($data, $supplier_id);

        if ($save_id) {
            save_custom_fields("clients", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            
           

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->request->getPost('view'), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo a client */

    function delete() {
        //$this->access_only_allowed_members();
        if (!$this->can_delete_suppliers() && !$this->can_edit_suppliers()) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');

        if ($this->Suppliers_model->delete_supplier_and_sub_items($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    /* list of clients, prepared for datatable  */

    function list_data() {
        //$this->access_only_allowed_members();
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("suppliers", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "custom_fields" => $custom_fields,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("suppliers", $this->login_user->is_admin, $this->login_user->user_type),
            //"group_id" => $this->request->getPost("group_id"),
            //"show_own_clients_only_user_id" => $this->show_own_clients_only_user_id(),
            "quick_filter" => $this->request->getPost("quick_filter"),
            "created_by" => $this->request->getPost("created_by"),
            //"client_groups" => $this->allowed_client_groups
            
        );

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Suppliers_model->get_details($all_options);

        //by this, we can handel the server side or client side from the app table prams.
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

    /* return a row of client list  table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("suppliers", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );
        $data = $this->Suppliers_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of client list table */

    private function _make_row($data, $custom_fields) {


        //$image_url = get_avatar($data->contact_avatar);
        //$contact = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->primary_contact";
        //$primary_contact = get_client_contact_profile_link($data->primary_contact_id, $contact);

       

      
        

        $row_data = array($data->id,
            
            anchor(get_uri("suppliers/view/" . $data->id), $data->name),
            $data->address,
            $data->phone,
            $data->email
            
            
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }
        if($this->can_delete_suppliers()){
        $row_data[] = $this->can_edit_suppliers()?modal_anchor(get_uri("suppliers/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_supplier'), "data-post-id" => $data->id)). js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("suppliers/delete"), "data-action" => "delete-confirmation")):"<i data-feather='edit' class='icon-16'></i>"
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("suppliers/delete"), "data-action" => "delete-confirmation"));
            }else{
                $row_data[] = $this->can_edit_suppliers()?modal_anchor(get_uri("suppliers/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_supplier'), "data-post-id" => $data->id)):"<i data-feather='edit' class='icon-16'></i>";

            }

        return $row_data;
    }

    /* load client details view */

    function view($supplier_id = 0, $tab = "") {
        //$this->access_only_allowed_members();
        //$this->can_access_this_client($supplier_id);

        if ($supplier_id) {
            $options = array("id" => $supplier_id);
            $supplier_info = $this->Suppliers_model->get_details($options)->getRow();
            if ($supplier_info) {


                $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;
                $view_data["show_event_info"] = (get_setting("module_event")) ? true : false;

                $access_info = $this->get_access_info("expense");
                $view_data["show_expense_info"] = (get_setting("module_expense") && $access_info->access_type == "all") ? true : false;

                $view_data['supplier_info'] = $supplier_info;

                //$view_data["is_starred"] = strpos($supplier_info->starred_by, ":" . $this->login_user->id . ":") ? true : false;

                $view_data["tab"] = clean_data($tab);

                $view_data["view_type"] = "";

                //even it's hidden, admin can view all information of client
                $view_data['hidden_menu'] = array("");

                return $this->template->rander("suppliers/view", $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /* add-remove start mark from client */

    function add_remove_star($client_id, $type = "add") {
        if ($client_id) {
            $view_data["client_id"] = clean_data($client_id);

            if ($type === "add") {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "add");
                return $this->template->view('suppliers/star/starred', $view_data);
            } else {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "remove");
                return $this->template->view('suppliers/star/not_starred', $view_data);
            }
        }
    }







     function company_info_tab($client_id = 0) {
        if ($client_id) {
            //$this->access_only_allowed_members_or_client_contact($client_id);
            //$this->can_access_this_client($client_id);

            //$supplier_id = $this->request->getPost('id');
            $view_data['model_info'] = $this->Suppliers_model->get_one($client_id);
            $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("suppliers", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $view_data['can_edit_suppliers'] = $this->can_edit_suppliers();

            $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();
            $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();

            return $this->template->view('suppliers/company_info_tab', $view_data);
        }
    }

    function show_my_starred_clients() {
        $view_data["clients"] = $this->Clients_model->get_starred_clients($this->login_user->id, $this->allowed_client_groups)->getResult();
        return $this->template->view('suppliers/star/clients_list', $view_data);
    }

    /* load projects tab  */

   

    function tasks($supplier_id) {
        validate_numeric_value($supplier_id);

        //$this->init_project_permission_checker($supplier_id);

        if (!$this->can_show_subtasks($supplier_id)) {
            app_redirect("forbidden");
        }
        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("created_by") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            $team_members_dropdown[] = array("id" => $key, "text" => $value/*, "isSelected" => true*/);
                
        }

        $view_data['supplier_id'] = $supplier_id;
        $view_data['view_type'] = "project_tasks";

        $view_data['can_create_tasks'] = $this->can_create_subtasks();
        $view_data['can_edit_tasks'] = $this->can_edit_subtasks();
        $view_data['can_delete_tasks'] = $this->can_delete_tasks();
        //$view_data["show_milestone_info"] = $this->can_view_milestones();
        $view_data['mang'] = $this->is_reserv_mang() ? 'yes':'no';
        $view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();
        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['task_statuses'] = $this->Task_status_model->get_details()->getResult();

        $view_data["show_assigned_tasks_only"] = get_array_value($this->login_user->permissions, "show_assigned_tasks_only");

        return $this->template->view("suppliers/task/my_tasks", $view_data);
    }

    function tasks_list_data($supplier_id = 0) {

        //$this->access_only_allowed_members_or_client_contact($supplier_id);
        //$this->can_access_this_client($supplier_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        
            $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";

        $options = array(
            "specific_user_id" => $this->request->getPost('specific_user_id'),
            //"milestone_id" => $this->request->getPost('milestone_id'),
            "priority_id" => $this->request->getPost("priority_id"),
            //"deadline" => $this->request->getPost('deadline'),
            "status_ids" => $status,
            "start_date" => $this->request->getPost('start_date'),
            "search" => $this->request->getPost('search'),
            "supplier_id" => $supplier_id,
            "mang" => $this->is_reserv_mang()?"reserv":"supply",

            "custom_fields" => $custom_fields,
            "quick_filter" => $this->request->getPost('quick_filter'),

            "custom_field_filter" => $this->prepare_custom_field_filter_values("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );
        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Sub_tasks_model->get_details($all_options);

        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $hide_primary_contact_label = false;
        if (!$supplier_id) {
            $hide_primary_contact_label = true;
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_task_row($data, $custom_fields, $hide_primary_contact_label);
        }

        $result["data"] = $result_data;

        echo json_encode($result);
    }

    /* return a row of contact list table */

    private function _task_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id, "custom_fields" => $custom_fields);
        $data = $this->Sub_tasks_model->get_details($options)->getRow();
        return $this->_make_task_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_task_row($data, $custom_fields) {
        $unread_comments_class = "unread-comments-of-tasks";
            $icon = "<i data-feather='message-circle' class='icon-16 ml5 unread-comments-of-tasks-icon'></i>";


            $checkbox_class = "checkbox-blank";
            $check_status = js_anchor("<span class='$checkbox_class mr15 float-start'></span>", array('title' => "", "class" => "js-task", "data-id" => $data->id,  "data-act" => "update-task-status-checkbox")) . $data->sub_task_id;

        

        if ($this->is_reserv_mang()) {
            $guest_phone=$data->guest_phone;
            $guest_nm = modal_anchor(get_uri("subtasks/task_view"), $data->guest_nm , array("title" => app_lang('reserv_mang').' - '.app_lang('task_info') . " #$data->sub_task_id", "data-post-mang" => "reservmang", "data-post-id" => $data->id,  "data-modal-lg" => "2"));

        }
        elseif ($this->is_supply_mang()) {
            $main_task = $this->Tasks_model->get_details(array("id" => $data->pnt_task_id))->getRow();
            $guest_phone='_______';
            $guest_nm = $main_task->project_title ? modal_anchor(get_uri("subtasks/task_view_supply"), $main_task->project_title , array("class"=>'float-start',"title" => app_lang("supply_mang").'-'.app_lang('task_info') . " #$data->sub_task_id", "data-post-id" => $data->id, "data-post-mang" => "supplymang",  "data-modal-lg" => "2")) : '-';
        if ($data->priority_id) {
            $guest_nm .= "<span class='float-start' style='margin-left:5px; margin-right:5px;' title='" . app_lang('priority') . "'>
                            <span class='sub-task-icon priority-badge' style='background: $data->priority_color'><i data-feather='$data->priority_icon' class='icon-14'></i></span><span class='small'> $data->priority_title</span>
                      </span>";
        }

        }else{
            $guest_nm =$data->guest_nm;
        }

        $st = $data->status_key_name ? app_lang($data->status_key_name) : $data->status_title;
        $status = js_anchor($st, array("style" => "background-color: $data->status_color", "class" => "badge", "data-id" => $data->id, "data-value" => $data->status_id, "data-act" => "update-task-status"));

        

        $options = "";
        /*if ($this->can_edit_subtasks()) {
            $options .= modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('subtasks'). " #$data->id", "data-post-id" => $data->id));
        }*/
        $act_return_date_text = "-";
        if ($data->act_return_date && is_date_exists($data->act_return_date)) {
            $act_return_date_text = format_to_date($data->act_return_date, false);
            if (get_my_local_time("Y-m-d") > $data->act_return_date /*&& $data->status_id != "1"*/) {
                $act_return_date_text = "<span class='text-danger'>" . $act_return_date_text . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->act_return_date /*&& $data->status_id != "3"*/) {
                $act_return_date_text = "<span class='text-warning'>" . $act_return_date_text . "</span> ";
            }
        }

        $act_tmp_return_date = "-";
        if ($data->tmp_return_date && is_date_exists($data->tmp_return_date)) {
            $act_tmp_return_date = format_to_date($data->tmp_return_date, false);
            if (get_my_local_time("Y-m-d") > $data->tmp_return_date && $data->status_id == "3") {
                $act_tmp_return_date = "<span class='text-danger'>" . $act_tmp_return_date . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $data->tmp_return_date && $data->status_id == "3") {
                $act_tmp_return_date = "<span class='text-warning'>" . $act_tmp_return_date . "</span> ";
            }
        }

        $row_data =  array(
            $data->status_color,
            $check_status,
            $guest_nm,
            
            //$guest_phone,
            $data->driver_nm,
            $this->get_service_type($data->service_type),
            
            
            
            $act_tmp_return_date,
            $act_return_date_text,
            
            $status
            //modal_anchor(get_uri("company/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_company'), "data-post-id" => $data->id))
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        //$row_data[] = modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit').' '.app_lang('sub_task'). " #$data->id", "data-post-id" => $data->id));






               

        $row_data[] = $options;

        return $row_data;
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
                if($sub_task->day_count && $sub_task->inv_day_count && $sub_task->tmp_return_date){
                    $s=0;
                } else { $s=1; }
            }
            if (!$sub_task->guest_nm || !$sub_task->out_date || !$sub_task->car_type || !$sub_task->supplier_id || !$sub_task->exp_out_time || !$sub_task->act_out_time || !$sub_task->act_return_date || $s!=0 || !$sub_task->car_status) {
                //this sub task isn't done yet, show error and exit
                echo json_encode(array("success" => false, 'message' => app_lang("sub_task_data_not_completing").'  '.$service_type));
                exit();
            
        }
        }
    }

    function save_task_status($id = 0) {
        validate_numeric_value($id);
        //$this->access_only_team_members();
        //$now = get_current_utc_time();
     $closed_date = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
            
        $status_id = $this->request->getPost('value');
        $data = array(
            "status_id" => $status_id,
            "closed_user_id"=> $this->login_user->id,
            "closed_date"=> $closed_date->format('Y-m-d H:i:s'),
        );

        $this->check_sub_tasks_statuses($status_id, $id,"","n");

        $task_info = $this->Sub_tasks_model->get_details(array("id" => $id))->getRow();

        //$this->init_project_permission_checker($task_info->project_id);
        if (!$this->can_edit_subtasks()) {
            app_redirect("forbidden");
        }

        

        $save_id = $this->Sub_tasks_model->ci_save($data, $id);

        if ($save_id) {
            $task_info = $this->Sub_tasks_model->get_details(array("id" => $id))->getRow();
            echo json_encode(array("success" => true, "data" => ( $this->_task_row_data($save_id)), 'id' => $save_id, "message" => app_lang('record_saved')));

            log_notification("sub_task_updated", array("project_id" => 0, "sub_task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
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
                return "سيارة بدون سائق";
                break;
            case "deliver":
                return "توصيلة";
                break;
            
            default:
                return '';
                break;
        }
    }




    function supplier_tasks_kanban($supplier_id) {
        validate_numeric_value($supplier_id);
        $this->init_project_permission_checker($supplier_id);

        if (!$this->can_show_subtasks($supplier_id)) {
            app_redirect("forbidden");
        }

        $view_data['supplier_id'] = $supplier_id;

        $view_data['can_create_tasks'] = $this->can_create_subtasks();
        $view_data["show_milestone_info"] = $this->can_view_milestones();

        $view_data['milestone_dropdown'] = $this->_get_milestones_dropdown_list($supplier_id);
        $view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();
        $view_data['assigned_to_dropdown'] = $this->_get_project_members_dropdown_list(1);

        $exclude_status_ids = $this->get_removed_task_status_ids($supplier_id);
        $view_data['task_statuses'] = $this->Task_status_model->get_details(array("exclude_status_ids" => $exclude_status_ids))->getResult();
        $view_data['can_edit_tasks'] = $this->can_edit_subtasks();
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        return $this->template->view("suppliers/task/kanban/supplier_tasks", $view_data);
    }


     function supplier_tasks_kanban_data($supplier_id = 0) {
        //validate_numeric_value($supplier_id);
        //$this->init_project_permission_checker($supplier_id);

        if (!$this->can_show_subtasks($supplier_id)) {
            app_redirect("forbidden");
        }

         $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
            

        $specific_user_id = $this->request->getPost('specific_user_id');

        $options = array(
            "supplier_id" => $supplier_id,
            
            "priority_id" => $this->request->getPost('priority_id'),
            "deadline" => $this->request->getPost('deadline'),
            "search" => $this->request->getPost('search'),
            "status_ids" => $status,
            "quick_filter" => $this->request->getPost('quick_filter'),
            "custom_field_filter" => $this->prepare_custom_field_filter_values("tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $view_data["tasks"] = $this->Tasks_model->get_kanban_details($options)->getResult();

        $exclude_status_ids = $this->get_removed_task_status_ids($supplier_id);
        $statuses = $this->Task_status_model->get_details(array("hide_from_kanban" => 0, "exclude_status_ids" => $exclude_status_ids));

        $view_data["total_columns"] = $statuses->resultID->num_rows;
        $view_data["columns"] = $statuses->getResult();
        $view_data['can_edit_tasks'] = $this->can_edit_subtasks();
        $view_data['supplier_id'] = $supplier_id;

        return $this->template->view('suppliers/task/kanban/kanban_view', $view_data);
    }


    private function _initialize_all_related_data_of_project($project_id = 0, $collaborators = "", $task_labels = "") {
        //we have to check if any defined project exists, then go through with the project id
        if ($project_id) {
            $this->init_project_permission_checker($project_id);

            $related_data = $this->get_all_related_data_of_project($project_id, $collaborators, $task_labels);

            $view_data['milestones_dropdown'] = $related_data["milestones_dropdown"];
            $view_data['assign_to_dropdown'] = $related_data["assign_to_dropdown"];
            $view_data['collaborators_dropdown'] = $related_data["collaborators_dropdown"];
            $view_data['label_suggestions'] = $related_data["label_suggestions"];
        } else {
            $view_data["projects_dropdown"] = $this->_get_projects_dropdown();

            //we have to show an empty dropdown when there is no project_id defined
            $view_data['milestones_dropdown'] = array(array("id" => "", "text" => "-"));
            $view_data['assign_to_dropdown'] = array(array("id" => "", "text" => "-"));
            $view_data['collaborators_dropdown'] = array();
            $view_data['label_suggestions'] = array();
        }

        $task_points = array();
        for ($i = 1; $i <= get_setting("task_point_range"); $i++) {
            if ($i == 1) {
                $task_points[$i] = $i . " " . app_lang('point');
            } else {
                $task_points[$i] = $i . " " . app_lang('points');
            }
        }

        $view_data['points_dropdown'] = $task_points;

        $exclude_status_ids = $this->get_removed_task_status_ids($project_id);
        $view_data['statuses'] = $this->Task_status_model->get_details(array("exclude_status_ids" => $exclude_status_ids))->getResult();

        $priorities = $this->Task_priority_model->get_details()->getResult();
        $priorities_dropdown = array(array("id" => "", "text" => "-"));
        foreach ($priorities as $priority) {
            $priorities_dropdown[] = array("id" => $priority->id, "text" => $priority->title);
        }

        $view_data['priorities_dropdown'] = $priorities_dropdown;

        return $view_data;
    }


    function task_modal_form() {
        $id = $this->request->getPost('id');
        $add_type = $this->request->getPost('add_type');
        $last_id = $this->request->getPost('last_id');
        $ticket_id = $this->request->getPost('ticket_id');

        $model_info = $this->Tasks_model->get_one($id);
        $supplier_id =$this->request->getPost('supplier_id');
        $project_id = $this->request->getPost('project_id') ? $this->request->getPost('project_id') : $model_info->project_id;

        $final_supplier_id = $project_id;
        if ($add_type == "multiple" && $last_id) {
            //we've to show the lastly added information if it's the operation of adding multiple tasks
            $model_info = $this->Tasks_model->get_one($last_id);

            //if we got lastly added task id, then we have to initialize all data of that in order to make dropdowns
            $final_supplier_id = $model_info->project_id;
        }

        $view_data = $this->_initialize_all_related_data_of_project($final_supplier_id, $model_info->collaborators, $model_info->labels);

        if ($id) {
            if (!$this->can_edit_subtasks()) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_create_subtasks($supplier_id ? true : false)) {
                app_redirect("forbidden");
            }
        }

        $view_data['model_info'] = $model_info;
        $view_data["projects_dropdown"] = $this->_get_projects_dropdown();
        $view_data["suppliers_dropdown"] = $this->_get_suppliers_dropdown();
        $view_data["clients_dropdown"] = $this->_get_myclients_dropdown();
         //projects dropdown is necessary on add multiple tasks
        $view_data["add_type"] = $add_type;
        $view_data['supplier'] = $this->Suppliers_model->get_one($supplier_id);//$supplier_id;

        $view_data['project_id'] = $project_id;
        $view_data['ticket_id'] = $ticket_id;

       

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("tasks", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        //clone task
        $is_clone = $this->request->getPost('is_clone');
        $view_data['is_clone'] = $is_clone;

        $view_data['view_type'] = $this->request->getPost("view_type");
        $Checklist_items_model = model('App\Models\Checklist_items_model');
        $view_data['has_checklist'] = $Checklist_items_model->get_details(array("task_id" => $id))->resultID->num_rows;
        $view_data['has_sub_task'] = count($this->Tasks_model->get_all_where(array("parent_task_id" => $id, "deleted" => 0))->getResult());

        $view_data["project_deadline"] = $this->_get_project_deadline_for_task($project_id);

        return $this->template->view('suppliers/task/modal_form', $view_data);
    }


    private function can_delete_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_tasks") == "1") {
                //check is user a project member
                return $this->is_user_a_project_member;
            }
        }
    }

    

   
   



    private function _row_data_validation_and_get_error_message($key, $data, $has_contact_first_name, $headers = array()) {
        $allowed_headers = $this->_get_allowed_headers();
        $header_value = get_array_value($allowed_headers, $key);

        //company name field is required
        if ($header_value == "company_name" && !$data) {
            return app_lang("import_client_error_company_name_field_required");
        }

        //if there is contact first name then the contact last name and email is required
        //the email should be unique then
        if ($has_contact_first_name) {
            if ($header_value == "contact_last_name" && !$data) {
                return app_lang("import_client_error_contact_name");
            }

            if ($header_value == "contact_email") {
                if ($data) {
                    if ($this->Users_model->is_email_exists($data)) {
                        return app_lang("duplicate_email");
                    }
                } else {
                    return app_lang("import_client_error_contact_email");
                }
            }
        }

        //there has no date field on default import fields
        //check on custom fields
        if (((count($allowed_headers) - 1) < $key) && $data) {
            $header_info = get_array_value($headers, $key);
            $custom_field_info = $this->Custom_fields_model->get_one(get_array_value($header_info, "custom_field_id"));
            if ($custom_field_info->field_type === "date" && !$this->_check_valid_date($data)) {
                return app_lang("import_date_error_message");
            }
        }
    }

    function download_sample_excel_file() {
        $this->access_only_allowed_members();
        return $this->download_app_files(get_setting("system_file_path"), serialize(array(array("file_name" => "import-clients-sample.xlsx"))));
    }

    function gdpr() {
        $view_data["user_info"] = $this->Users_model->get_one($this->login_user->id);
        return $this->template->view("suppliers/contacts/gdpr", $view_data);
    }

    function export_my_data() {
        if (get_setting("enable_gdpr") && get_setting("allow_clients_to_export_their_data")) {
            $user_info = $this->Users_model->get_one($this->login_user->id);

            $txt_file_name = $user_info->first_name . " " . $user_info->last_name . ".txt";

            $data = $this->_make_export_data($user_info);

            $handle = fopen($txt_file_name, "w");
            fwrite($handle, $data);
            fclose($handle);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($txt_file_name));
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($txt_file_name));
            readfile($txt_file_name);

            //delete local file
            if (file_exists($txt_file_name)) {
                unlink($txt_file_name);
            }

            exit;
        }
    }

    private function _make_export_data($user_info) {
        $required_general_info_array = array("first_name", "last_name", "email", "job_title", "phone", "gender", "skype", "created_at");

        $data = strtoupper(app_lang("general_info")) . "\n";

        //add general info
        foreach ($required_general_info_array as $field) {
            if ($user_info->$field) {
                if ($field == "created_at") {
                    $data .= app_lang("created") . ": " . format_to_datetime($user_info->$field) . "\n";
                } else if ($field == "gender") {
                    $data .= app_lang($field) . ": " . ucfirst($user_info->$field) . "\n";
                } else if ($field == "skype") {
                    $data .= "Skype: " . ucfirst($user_info->$field) . "\n";
                } else {
                    $data .= app_lang($field) . ": " . $user_info->$field . "\n";
                }
            }
        }

        $data .= "\n\n";
        $data .= strtoupper(app_lang("client_info")) . "\n";

        //add company info
        $client_info = $this->Clients_model->get_one($user_info->client_id);
        $required_client_info_array = array("company_name", "address", "city", "state", "zip", "country", "phone", "website", "vat_number");
        foreach ($required_client_info_array as $field) {
            if ($client_info->$field) {
                $data .= app_lang($field) . ": " . $client_info->$field . "\n";
            }
        }

        $data .= "\n\n";
        $data .= strtoupper(app_lang("social_links")) . "\n";

        //add social links
        $social_links = $this->Social_links_model->get_one($user_info->id);

        unset($social_links->id);
        unset($social_links->user_id);
        unset($social_links->deleted);

        foreach ($social_links as $key => $value) {
            if ($value) {
                $data .= ucfirst($key) . ": " . $value . "\n";
            }
        }

        return $data;
    }

   

   


   
    function suppliers_list() {
        //$this->access_only_allowed_members();

        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("clients", $this->login_user->is_admin, $this->login_user->user_type);

        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['groups_dropdown'] = json_encode($this->_get_groups_dropdown_select2_data(true));
        $view_data['can_edit_suppliers'] = $this->can_edit_suppliers();
        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown(true);

        return $this->template->view("suppliers/suppliers_list", $view_data);
    }

    

    function proposals($client_id) {
        validate_numeric_value($client_id);
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("proposals", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("proposals", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("suppliers/proposals/proposals", $view_data);
        }
    }

    function switch_account($user_id) {
        validate_numeric_value($user_id);
        $this->access_only_clients();

        $options = array(
            'id' => $user_id,
            'email' => $this->login_user->email,
            'status' => 'active',
            'deleted' => 0,
            'disable_login' => 0,
            'user_type' => 'client'
        );

        $user_info = $this->Users_model->get_one_where($options);
        if (!$user_info->id) {
            show_404();
        }

        $session = \Config\Services::session();
        $session->set('user_id', $user_info->id);

        app_redirect('dashboard/view');
    }

}

/* End of file clients.php */
/* Location: ./app/controllers/clients.php */