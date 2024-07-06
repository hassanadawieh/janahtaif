<?php

namespace App\Controllers;

class Projects extends Security_Controller {

    

    public function __construct() {
        parent::__construct();
        if ($this->has_all_projects_restricted_role()) {
            app_redirect("forbidden");
        }

        
    }

    

    /* load project view */

    function index() {
        app_redirect("projects/all_projects");
    }

    private function can_delete_projects($project_id = 0) {
        if ($this->login_user->is_admin == 1) {
          return true;
      }else{
            $can_delete_projects = get_array_value($this->login_user->permissions, "can_delete_projects");
            //$can_delete_only_own_created_projects = get_array_value($this->login_user->permissions, "can_delete_only_own_created_projects");

            if ($can_delete_projects) {
                return true;
            }

           /* if ($project_id) {
                $project_info = $this->Projects_model->get_one($project_id);
                if ($can_delete_only_own_created_projects && $project_info->created_by === $this->login_user->id) {
                    return true;
                }
            } else if ($can_delete_only_own_created_projects) { //no project given and the user has partial access
                return true;
            }*/
        }
    }

    function all_projects($status = "") {
        $view_data['project_labels_dropdown'] = json_encode($this->make_labels_dropdown("project", "", true));

        $view_data["can_create_projects"] = $this->can_create_projects();

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data["status"] = clean_data($status);

        if ($this->login_user->user_type === "staff") {
            $view_data["can_edit_projects"] = $this->can_edit_projects();
           // $view_data["can_delete_projects"] = $this->can_delete_projects();

            return $this->template->rander("projects/index", $view_data);
        } else {
            $view_data['client_id'] = $this->login_user->client_id;
            $view_data['page_type'] = "full";
            return $this->template->rander("clients/projects/index", $view_data);
        }
    }

    /* load project  add/edit modal */

    function modal_form() {
        $project_id = $this->request->getPost('id');
        $client_id = $this->request->getPost('client_id');

        if ($project_id) {
            if (!$this->can_edit_projects($project_id)) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_create_projects()) {
                app_redirect("forbidden");
            }
        }


        //$view_data["client_id"] = $client_id;
        $model_info = $this->Projects_model->get_one($project_id);
        $view_data['model_info']=$model_info;
        

        //check if it's from estimate. if so, then prepare for project
        

        //check if it's from order. If so, then prepare for project
        

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("projects", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        $view_data["clients_dropdown"] = $this->_get_myclients_dropdown();
        //$view_data['clients_dropdown'] = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));
        $view_data["contacts_dropdown"] = $project_id ? $this->get_client_contact_dropdown($model_info->client_id,$model_info->client_contact_id):null;


        $view_data['client_id'] = $client_id?$client_id:$model_info->client_id;
        $view_data['client_contact_id'] = $model_info->client_contact_id;
        $user_info = $this->Clients_contact_model->get_one($model_info->client_contact_id);
        $view_data['contact_name'] = $user_info->first_name.' '.$user_info->last_name;

        //$view_data['label_suggestions'] = $this->make_labels_dropdown("project", $view_data['model_info']->labels);

        return $this->template->view('projects/modal_form', $view_data);
    }



    function modal_form2() {
        $project_id = $this->request->getPost('id');
        $client_id = $this->request->getPost('client_id');

        if ($project_id) {
            if (!$this->can_edit_projects($project_id)) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_create_projects()) {
                app_redirect("forbidden");
            }
        }


        //$view_data["client_id"] = $client_id;
        $model_info = $this->Projects_model->get_one($project_id);
        $view_data['model_info']=$model_info;
        
        

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("projects", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        $view_data["clients_dropdown"] = $this->_get_myclients_dropdown();
        //$view_data['clients_dropdown'] = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));
        $view_data["contacts_dropdown"] = $project_id ? $this->get_client_contact_dropdown($model_info->client_id,$model_info->client_contact_id):null;


        $view_data['client_id'] = $client_id?$client_id:$model_info->client_id;
        $view_data['client_contact_id'] = $model_info->client_contact_id;
        $user_info = $this->Clients_contact_model->get_one($model_info->client_contact_id);
        $view_data['contact_name'] = $user_info->first_name.' '.$user_info->last_name;

        //$view_data['label_suggestions'] = $this->make_labels_dropdown("project", $view_data['model_info']->labels);

        return $this->template->view('projects/modal_form2', $view_data);
    }

    /* insert or update a project */


     function get_client_contact_dropdown($client_id,$contact_id) {


        

       $contacts = $this->Clients_contact_model->get_details(array("client_id" => $client_id, "deleted" => 0))->getResult();
       $selected=false;
            $contacts_dropdown = array(array("id" => "", "text" => "-"));
            foreach ($contacts as $contact) {
                 if($contact->id==$contact_id){
                    $selected=true;
                }else{
                    $selected=false;
                }
                $contacts_dropdown[] = array("id" => $contact->id, "text" => $contact->first_name.' '.$contact->last_name,"isSelected" =>$selected);
            }
            $related_data = array(
                "contacts_dropdown" => $contacts_dropdown,
               );


            return json_encode(array(
                "contacts_dropdown" => $related_data["contacts_dropdown"],
               
            ));
    }

    function save() {

        $id = $this->request->getPost('id');

        if ($id) {
            if (!$this->can_edit_projects($id)) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_create_projects()) {
                app_redirect("forbidden");
            }
        }

        $this->validate_submitted_data(array(
            "title" => "required"
        ));

        $status = $this->request->getPost('status');

        $data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            
            "title_en" => $this->request->getPost('title_en'),
            "client_contact_id" => $this->request->getPost('contact_id'),
            "client_id" => $this->request->getPost('client_id'),
            "status" => $status ? $status : "open",
            
        );

        if (!$id) {
            $data["created_date"] = get_current_utc_time();
            $data["created_by"] = $this->login_user->id;
        }


        


        $data = clean_data($data);

       

        $save_id = $this->Projects_model->ci_save($data, $id);
        if ($save_id) {

            save_custom_fields("projects", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //send notification
           /* if ($status == "completed") {
                log_notification("project_completed", array("project_id" => $save_id));
            }*/

            if (!$id) {

               
                
               
            }
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }



    /* delete a project */

   /* function delete() {
        $id = $this->request->getPost('id');

        if ($this->Projects_model->delete_project_and_sub_items($id)) {
            //log_notification("project_deleted", array("project_id" => $id));

            try {
                app_hooks()->do_action("app_hook_data_delete", array(
                    "id" => $id,
                    "table" => get_db_prefix() . "projects"
                ));
            } catch (\Exception $ex) {
                log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
            }

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }*/


    function delete() {
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $id = $this->request->getPost('id');
        if($this->can_delete_projects()){
        

        if ($this->request->getPost('undo')) {
            if ($this->Projects_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Projects_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
        } else {
                echo json_encode(array("success" => false, 'message' => "ليس لديك صلاحية"));
            }
    }

    /* list of projcts, prepared for datatable  */

    function list_data() {

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $statuses = $this->request->getPost('status') ? implode(",", $this->request->getPost('status')) : "";

        $options = array(
            "statuses" => $statuses,
            
            "custom_field_filter" => $this->prepare_custom_field_filter_values("projects", $this->login_user->is_admin, $this->login_user->user_type)
        );

        //only admin/ the user has permission to manage all projects, can see all projects, other team mebers can see only their own projects.
       

        $list_data = $this->Projects_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }



    function projects_list_data_of_client($client_id = 0) {
        validate_numeric_value($client_id);

        //$this->access_only_team_members_or_client_contact($client_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $statuses = $this->request->getPost('status') ? implode(",", $this->request->getPost('status')) : "";

        $options = array(
            "client_id" => $client_id,
            "statuses" => $statuses,
            "custom_fields" => $custom_fields,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("projects", $this->login_user->is_admin, $this->login_user->user_type)
        );

        $list_data = $this->Projects_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    
    /* return a row of project list  table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );

        $data = $this->Projects_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of project list table */

    private function _make_row($data, $custom_fields) {

       

        //has deadline? change the color of date based on status
        

        $title =modal_anchor(get_uri("projects/modal_form"), $data->title, array("class" => "edit", "title" => app_lang('edit_project'), "data-post-id" => $data->id));

        

        $optoins = "";
        if ($this->can_edit_projects()) {
            $optoins .= modal_anchor(get_uri("projects/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_project'), "data-post-id" => $data->id))
            . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_projects'), "class" => "delete", "data-odai" => 1, "data-id" => $data->id, "data-action-url" => get_uri("projects/delete"), "data-action" => "delete-confirmation"));
        }

        /*if ($this->can_delete_projects($data->id)) {
            $optoins .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_project'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete"), "data-action" => "delete-confirmation"));
        }*/


            $client=$data->company_name;
        

       

       

        $row_data = array(
            $data->id,
            $title,

            $data->title_en,
            $client,
            $data->user_name,
            app_lang($data->status)
        );

       

        $row_data[] = $optoins;

        return $row_data;
    }

    /* load project details view */

    function view($project_id = 0, $tab = "") {
        validate_numeric_value($project_id);
        $this->init_project_permission_checker($project_id);

        //$view_data = $this->_get_project_info_data($project_id);

        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $this->can_view_invoices()) ? true : false;

        $expense_access_info = $this->get_access_info("expense");
        $view_data["show_expense_info"] = (get_setting("module_expense") && $expense_access_info->access_type == "all") ? true : false;

        $access_contract = $this->get_access_info("contract");
        $view_data["show_contract_info"] = (get_setting("module_contract") && $access_contract->access_type == "all") ? true : false;

        $view_data["show_actions_dropdown"] = $this->can_create_projects();

        $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;

        $view_data["show_timmer"] = get_setting("module_project_timesheet") ? true : false;

        //$this->init_project_settings($project_id);
        //$view_data["show_timesheet_info"] = $this->can_view_timesheet($project_id);

        $view_data["show_tasks"] = true;

        $view_data["show_gantt_info"] = $this->can_view_gantt();
        $view_data["show_milestone_info"] = $this->can_view_milestones();

        if ($this->login_user->user_type === "client") {
            $view_data["show_timmer"] = false;
            $view_data["show_tasks"] = $this->can_view_tasks();

            if (!get_setting("client_can_edit_projects")) {
                $view_data["show_actions_dropdown"] = false;
            }
        }

        $view_data["show_files"] = $this->can_view_files();

        $view_data["tab"] = clean_data($tab);

        $view_data["is_starred"] = strpos($view_data['project_info']->starred_by, ":" . $this->login_user->id . ":") ? true : false;

        $view_data['can_edit_timesheet_settings'] = $this->can_edit_timesheet_settings($project_id);
        $view_data['can_edit_slack_settings'] = $this->can_edit_slack_settings();
        $view_data["can_create_projects"] = $this->can_create_projects();

        $ticket_access_info = $this->get_access_info("ticket");
        $view_data["show_ticket_info"] = (get_setting("module_ticket") && get_setting("project_reference_in_tickets") && $ticket_access_info->access_type == "all") ? true : false;

        return $this->template->rander("projects/details_view", $view_data);
    }

    


}

/* End of file projects.php */
/* Location: ./app/controllers/projects.php */