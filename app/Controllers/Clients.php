<?php

namespace App\Controllers;

class Clients extends Security_Controller {

    protected $Checklist_items_model;
    function __construct() {
        parent::__construct();
        //check permission to access this module
        $this->init_permission_checker("client");

        $this->Checklist_items_model = model('App\Models\Checklist_items_model');
    }

    /* load clients list view */

    function index($tab = "") {
        $this->access_only_allowed_members();

        $view_data = $this->make_access_permissions_view_data();

        $view_data['can_edit_clients'] = $this->can_edit_clients();
        $view_data["show_project_info"] = $this->can_manage_all_projects() && !$this->has_all_projects_restricted_role();

        $view_data["show_own_clients_only_user_id"] = $this->show_own_clients_only_user_id();
        $view_data["allowed_client_groups"] = $this->allowed_client_groups;

        $view_data['tab'] = clean_data($tab);

        return $this->template->rander("clients/index", $view_data);
    }

    //for team members, check only read_only permission here, since other permission will be checked accordingly
    private function can_edit_clients() {
        if ($this->login_user->user_type == "staff" && get_array_value($this->login_user->permissions, "client") === "read_only") {
            return false;
        }

        return true;
    }

    private function can_view_files() {
        if ($this->login_user->user_type == "staff") {
            $this->access_only_allowed_members();
        } else {
            if (!get_setting("client_can_view_files")) {
                app_redirect("forbidden");
            }
        }
    }

    private function can_add_files() {
        if ($this->login_user->user_type == "staff") {
            $this->access_only_allowed_members();
        } else {
            if (!get_setting("client_can_add_files")) {
                app_redirect("forbidden");
            }
        }
    }


    function get_client_contact_dropdown($client_id) {


        $contacts = $this->Clients_contact_model->get_details(array("client_id" => $client_id, "deleted" => 0))->getResult();
            $contacts_dropdown = array(array("id" => "", "text" => "-"));
            foreach ($contacts as $contact) {
                $contacts_dropdown[] = array("id" => $contact->id, "text" => $contact->first_name.' '.$contact->last_name);
            }

            $options = array("status" => "open");
            $options["client_id"] = $client_id;
        

        $projects = $this->Projects_model->get_details($options)->getResult();

            $projects_dropdown = array();
        foreach ($projects as $project) {

            $projects_dropdown[] = array("id" => $project->id, "text" => $project->title);
        }


            $related_data = array(
                "contacts_dropdown" => $contacts_dropdown,
                "projects_dropdown" => $projects_dropdown,
               );
            //$view_data["projects_dropdown"] = $this->_get_projects_client_dropdown($client_id);
            echo json_encode(array(
                "contacts_dropdown" => $related_data["contacts_dropdown"],
                "projects_dropdown" => $projects_dropdown,

               
            ));

        //$view_data["contacts_dropdown"] = $this->_get_myclients_contacts_dropdown($client_id);

        //return $view_data;
    }

    /* load client add/edit modal */

    function modal_form() {
        $this->access_only_allowed_members();
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $client_id = $this->request->getPost('id');
        $this->can_access_this_client($client_id);
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

        return $this->template->view('clients/modal_form', $view_data);
    }

    function my_modal_form() {
        $this->access_only_allowed_members();
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $client_id = $this->request->getPost('id');
        $this->can_access_this_client($client_id);
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

        return $this->template->view('clients/my_modal_form', $view_data);
    }

    /* insert or update a client */

    function save() {
        $client_id = $this->request->getPost('id');
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->can_access_this_client($client_id);

        $this->access_only_allowed_members_or_client_contact($client_id);

        $this->validate_submitted_data(array(
            "id" => "numeric",
            "company_name" => "required"
        ));

        $company_name = $this->request->getPost('company_name');

        $data = array(
            "company_name" => $company_name,
            //"type" => $this->request->getPost('account_type'),
            "address" => $this->request->getPost('address'),
            "city" => $this->request->getPost('city'),
            //"state" => $this->request->getPost('state'),
            //"zip" => $this->request->getPost('zip'),
            "country" => $this->request->getPost('country'),
            "phone" => $this->request->getPost('phone'),
            "website" => $this->request->getPost('website'),
            "vat_number" => $this->request->getPost('vat_number')
        );
        if($this->request->getPost('status')){
          $data["status"]=2;
        }else{
            $data["status"]=1;
        }



        if ($this->login_user->user_type === "staff") {
            $data["group_ids"] = $this->request->getPost('group_ids') ? $this->request->getPost('group_ids') : "";
        }


        if (!$client_id) {
            $data["created_date"] = get_current_utc_time();
            $data["created_by"] = $this->login_user->id;
        }


        

        $data = clean_data($data);

        //check duplicate company name, if found then show an error message
        if (get_setting("disallow_duplicate_client_company_name") == "1" && $this->Clients_model->is_duplicate_company_name($data["company_name"], $client_id)) {
            echo json_encode(array("success" => false, 'message' => app_lang("account_already_exists_for_your_company_name")));
            exit();
        }

        $save_id = $this->Clients_model->ci_save($data, $client_id);

        if ($save_id) {
            save_custom_fields("clients", $save_id, $this->login_user->is_admin, $this->login_user->user_type);


            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->request->getPost('view'), 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete or undo a client */

    function delete() {
        $this->access_only_allowed_members();
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));
        $id = $this->request->getPost('id');
        $this->can_access_this_client($id);

        //$tasks_table = $this->db->prefixTable('tasks');
        //$check = "SELECT id FROM $tasks_table WHERE $tasks_table.deleted=0 AND $tasks_table.client_id=$id; ";

        
        $check =$this->Tasks_model->get_details(array("client_id" => $id,"selected_year" => $this->session->get('selected_year')))->getResult();
        if(count($check)>0){
            if ($this->Clients_model->update_client_status($id)) {
            echo json_encode(array("success" => false, 'message' => "هذا العميل علية حركة - تم تعطيل العميل"));
        }else {
            echo json_encode(array("success" => false, 'message' => "هذا العميل علية حركة"));
        }

        }else{
        if ($this->Clients_model->delete_client_and_sub_items($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    }

    /* list of clients, prepared for datatable  */

    function list_data() {

        $this->access_only_allowed_members();
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "custom_fields" => $custom_fields,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("clients", $this->login_user->is_admin, $this->login_user->user_type),
            "group_id" => $this->request->getPost("group_id"),
            "show_own_clients_only_user_id" => $this->show_own_clients_only_user_id(),
            "quick_filter" => $this->request->getPost("quick_filter"),
            "created_by" => $this->request->getPost("created_by"),
            "client_groups" => $this->allowed_client_groups
        );

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Clients_model->get_details($all_options);

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
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields
        );
        $data = $this->Clients_model->get_details($options)->getRow();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of client list table */

    private function _make_row($data, $custom_fields) {


        $image_url = get_avatar('a:1:{s:9:"file_name";s:29:"_file613bc2b05fbe1-avatar.jpg";}');
        $contact = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->primary_contact";
        $primary_contact = $contact;

        $group_list = "";
        if ($data->client_groups) {
            $groups = explode(",", $data->client_groups);
            foreach ($groups as $group) {
                if ($group) {
                    $group_list .= "<li>" . $group . "</li>";
                }
            }
        }

        if ($group_list) {
            $group_list = "<ul class='pl15'>" . $group_list . "</ul>";
        }


        $due = 0;
        if ($data->invoice_value) {
            $due = ignor_minor_value($data->invoice_value - $data->payment_received);
        }
        if($data->status==1){
            $row_data = array($data->id,
            
            anchor(get_uri("clients/view/" . $data->id), $data->company_name),
            $data->primary_contact ? $primary_contact : "",
            $group_list,
        );
        }else{
            $row_data = array($data->id,
            
            "<span style='color:red'>".$data->company_name.' - ('.  app_lang("disabled").")</span>" ,
            $data->primary_contact ? $primary_contact : "",
            $group_list,
        );

        }

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = modal_anchor(get_uri("clients/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit_client'), "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("clients/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load client details view */

    function view($client_id = 0, $tab = "") {
        //$this->access_only_allowed_members();
        //$this->can_access_this_client($client_id);

        if ($client_id) {
            $options = array("id" => $client_id);
            $client_info = $this->Clients_model->get_details($options)->getRow();

            if ($client_info ) {

                $view_data = $this->make_access_permissions_view_data();

                $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;
                $view_data["show_event_info"] = (get_setting("module_event")) ? true : false;

                $access_info = $this->get_access_info("expense");
                $view_data["show_expense_info"] = (get_setting("module_expense") && $access_info->access_type == "all") ? true : false;

                $view_data['client_info'] = $client_info;

                $view_data["is_starred"] = strpos($client_info->starred_by, ":" . $this->login_user->id . ":") ? true : false;

                $view_data["tab"] = clean_data($tab);

                $view_data["view_type"] = "";

                //even it's hidden, admin can view all information of client
                $view_data['hidden_menu'] = array("");
                if($client_info->status==1){

                return $this->template->rander("clients/view", $view_data);
            }else{ 

               

         app_redirect("clients");

                 }
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
                return $this->template->view('clients/star/starred', $view_data);
            } else {
                $this->Clients_model->add_remove_star($client_id, $this->login_user->id, $type = "remove");
                return $this->template->view('clients/star/not_starred', $view_data);
            }
        }
    }

    function show_my_starred_clients() {
        $view_data["clients"] = $this->Clients_model->get_starred_clients($this->login_user->id, $this->allowed_client_groups)->getResult();
        return $this->template->view('clients/star/clients_list', $view_data);
    }

    /* load projects tab  */

    function projects($client_id=0) {
        //$this->access_only_allowed_members();
        //$this->can_access_this_client($client_id);

        $view_data['can_create_projects'] = $this->can_create_projects();
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['client_id'] = clean_data($client_id);
        return $this->template->view("clients/projects/index", $view_data);
    }

    function all_projectsss($status = "") {
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

  


    function notes($client_id) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data['client_id'] = clean_data($client_id);
            return $this->template->view("clients/notes/index", $view_data);
        }
    }

    

    function files($client_id, $view_type = "") {
        $this->can_view_files();
        $this->can_access_this_client($client_id);

        if ($this->login_user->user_type == "client") {
            $client_id = $this->login_user->client_id;
        }

        $view_data['client_id'] = clean_data($client_id);
        $view_data['page_view'] = false;

        if ($view_type == "page_view") {
            $view_data['page_view'] = true;
            return $this->template->rander("clients/files/index", $view_data);
        } else {
            return $this->template->view("clients/files/index", $view_data);
        }
    }

    /* file upload modal */

    function file_modal_form() {
        $this->can_add_files();

        $view_data['model_info'] = $this->General_files_model->get_one($this->request->getPost('id'));
        $client_id = $this->request->getPost('client_id') ? $this->request->getPost('client_id') : $view_data['model_info']->client_id;
        $this->can_access_this_client($client_id);

        $view_data['client_id'] = $client_id;
        return $this->template->view('clients/files/modal_form', $view_data);
    }



    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for client */

    function validate_file() {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->request->getPost('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id || ($this->login_user->user_type == "client" && $info->uploaded_by !== $this->login_user->id)) {
            app_redirect("forbidden");
        }

        $this->can_access_this_client($info->client_id);

        if ($this->General_files_model->delete($id)) {

            //delete the files
            delete_app_files(get_general_file_path("client", $info->client_id), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    function contact_profile($contact_id = 0, $tab = "") {
        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->can_access_this_client($view_data['user_info']->client_id);
        $view_data['client_info'] = $this->Clients_model->get_one($view_data['user_info']->client_id);
        $view_data['tab'] = clean_data($tab);
        if ($view_data['user_info']->user_type === "client") {

            $view_data['show_cotact_info'] = true;
            $view_data['show_social_links'] = true;
            $view_data['social_link'] = $this->Social_links_model->get_one($contact_id);
            return $this->template->rander("clients/contacts/view", $view_data);
        } else {
            show_404();
        }
    }

    //show account settings of a user
    function account_settings($contact_id) {
        $this->access_only_allowed_members_or_contact_personally($contact_id);
        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $view_data['can_edit_clients'] = $this->can_edit_clients();
        $this->can_access_this_client($view_data['user_info']->client_id);
        return $this->template->view("users/account_settings", $view_data);
    }

    //show my preference settings of a team member
    function my_preferences() {
        $view_data["user_info"] = $this->Users_model->get_one($this->login_user->id);

        //language dropdown
        $view_data['language_dropdown'] = array();
        if (!get_setting("disable_language_selector_for_clients")) {
            $view_data['language_dropdown'] = get_language_list();
        }

        $view_data["hidden_topbar_menus_dropdown"] = $this->get_hidden_topbar_menus_dropdown();

        return $this->template->view("clients/contacts/my_preferences", $view_data);
    }

    function save_my_preferences() {
        //setting preferences
        $settings = array("notification_sound_volume", "disable_push_notification", "disable_keyboard_shortcuts", "reminder_sound_volume", "reminder_snooze_length");

        if (!get_setting("disable_topbar_menu_customization")) {
            array_push($settings, "hidden_topbar_menus");
        }

        foreach ($settings as $setting) {
            $value = $this->request->getPost($setting);
            if (is_null($value)) {
                $value = "";
            }

            $this->Settings_model->save_setting("user_" . $this->login_user->id . "_" . $setting, $value, "user");
        }

        //there was 3 settings in users table.
        //so, update the users table also

        $user_data = array(
            "enable_web_notification" => $this->request->getPost("enable_web_notification"),
            "enable_email_notification" => $this->request->getPost("enable_email_notification"),
        );

        if (!get_setting("disable_language_selector_for_clients")) {
            $user_data["language"] = $this->request->getPost("personal_language");
        }

        $user_data = clean_data($user_data);

        $this->Users_model->ci_save($user_data, $this->login_user->id);

        try {
            app_hooks()->do_action("app_hook_clients_my_preferences_save_data");
        } catch (\Exception $ex) {
            log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
        }

        echo json_encode(array("success" => true, 'message' => app_lang('settings_updated')));
    }

    function save_personal_language($language) {
        if (!get_setting("disable_language_selector_for_clients") && ($language || $language === "0")) {

            $language = clean_data($language);
            $data["language"] = strtolower($language);

            $this->Users_model->ci_save($data, $this->login_user->id);
        }
    }

    /* load contacts tab  */

    function contacts($client_id = 0) {
        $this->access_only_allowed_members();
        $this->can_access_this_client($client_id);

        if ($client_id) {
            $view_data["client_id"] = clean_data($client_id);
            $view_data["view_type"] = "";
        } else {
            $view_data["client_id"] = "";
            $view_data["view_type"] = "list_view";
        }
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("client_contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['can_edit_clients'] = $this->can_edit_clients();

        return $this->template->view("clients/contacts/index", $view_data);
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->access_only_allowed_members();
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }
        $id=$this->request->getPost('id');

        $view_data['model_info'] = $this->Clients_contact_model->get_one($id);
        $view_data['client_id'] = $this->request->getPost('client_id');

        $view_data['add_type'] = $this->request->getPost('add_type');

        $this->can_access_this_client($view_data['model_info']->client_id);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("client_contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();
        return $this->template->view('clients/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $this->can_access_this_client($view_data['model_info']->client_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("client_contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $view_data['can_edit_clients'] = $this->can_edit_clients();
            return $this->template->view('clients/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    function company_info_tab($client_id = 0) {
        if ($client_id) {
            $this->access_only_allowed_members_or_client_contact($client_id);
            $this->can_access_this_client($client_id);

            $view_data['model_info'] = $this->Clients_model->get_one($client_id);
            $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("clients", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $view_data['can_edit_clients'] = $this->can_edit_clients();

            $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();
            $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();

            return $this->template->view('clients/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    function contact_social_links_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members_or_contact_personally($contact_id);

            $contact_info = $this->Users_model->get_one($contact_id);
            $this->can_access_this_client($contact_info->client_id);

            $view_data['user_id'] = clean_data($contact_id);
            $view_data['user_type'] = "client";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            $view_data['can_edit_clients'] = $this->can_edit_clients();
            return $this->template->view('users/social_links', $view_data);
        }
    }

    /* insert/upadate a contact */

    function save_contact() {
        $contact_id = $this->request->getPost('contact_id');
        $client_id = $this->request->getPost('client_id');
        $this->can_access_this_client($client_id);
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $user_data = array(
            "first_name" => $this->request->getPost('first_name'),
            "last_name" => $this->request->getPost('last_name'),
            "phone" => $this->request->getPost('phone'),
            "address" => $this->request->getPost('address'),
            "job_title" => $this->request->getPost('job_title'),
            "gender" => is_null($this->request->getPost('gender')) ? "" : $this->request->getPost('gender')
            //"note" => $this->request->getPost('note')
        );

        $this->validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "client_id" => "required|numeric"
        ));

        if (!$contact_id) {
            //inserting new contact. client_id is required

           
            //we'll save following fields only when creating a new contact from this form
            $user_data["client_id"] = $client_id;
            //$user_data["email"] = trim($this->request->getPost('email'));
            //$user_data["password"] = password_hash($this->request->getPost("login_password"), PASSWORD_DEFAULT);
            $user_data["created_at"] = get_current_utc_time();

            //validate duplicate email address
           
        }

        //by default, the first contact of a client is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->Clients_model->get_primary_contact($client_id);
        if (!$primary_contact) {
            $user_data['is_primary_contact'] = 1;
        }

        //only admin can change existing primary contact
        $is_primary_contact = $this->request->getPost('is_primary_contact');
        if ($is_primary_contact && $this->login_user->is_admin) {
            $user_data['is_primary_contact'] = 1;
        }

        $user_data = clean_data($user_data);

        $save_id = $this->Clients_contact_model->ci_save($user_data, $contact_id);
        if ($save_id) {

            save_custom_fields("client_contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);
            save_custom_fields("clients_contact", $save_id, $this->login_user->is_admin, $this->login_user->user_type);
            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Clients_contact_model->ci_save($user_data, $primary_contact);
            }

            //send login details to user only for first time. when creating  a new contact
            

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, "client_id" => $client_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save social links of a contact
    function save_contact_social_links($contact_id = 0) {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $contact_id = clean_data($contact_id);

        $this->access_only_allowed_members_or_contact_personally($contact_id);

        $contact_info = $this->Users_model->get_one($contact_id);
        $this->can_access_this_client($contact_info->client_id);

        $id = 0;

        //find out, the user has existing social link row or not? if found update the row otherwise add new row.
        $has_social_links = $this->Social_links_model->get_one($contact_id);
        if (isset($has_social_links->id)) {
            $id = $has_social_links->id;
        }

        $social_link_data = array(
            "facebook" => $this->request->getPost('facebook'),
            "twitter" => $this->request->getPost('twitter'),
            "linkedin" => $this->request->getPost('linkedin'),
            "digg" => $this->request->getPost('digg'),
            "youtube" => $this->request->getPost('youtube'),
            "pinterest" => $this->request->getPost('pinterest'),
            "instagram" => $this->request->getPost('instagram'),
            "github" => $this->request->getPost('github'),
            "tumblr" => $this->request->getPost('tumblr'),
            "vine" => $this->request->getPost('vine'),
            "whatsapp" => $this->request->getPost('whatsapp'),
            "user_id" => $contact_id,
            "id" => $id ? $id : $contact_id
        );

        $social_link_data = clean_data($social_link_data);

        $this->Social_links_model->ci_save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
    }

    //save account settings of a client contact (user)
    function save_account_settings($user_id) {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }
        $this->access_only_allowed_members_or_contact_personally($user_id);

        $contact_info = $this->Users_model->get_one($user_id);
        $this->can_access_this_client($contact_info->client_id);

        $this->validate_submitted_data(array(
            "email" => "required|valid_email"
        ));

        $email = $this->request->getPost('email');
        $password = $this->request->getPost("password");

        if ($this->Users_model->is_email_exists($email, $user_id, $contact_info->client_id)) {
            echo json_encode(array("success" => false, 'message' => app_lang('duplicate_email')));
            exit();
        }

        $account_data = array(
            "email" => $email
        );

        //don't reset password if user doesn't entered any password
        if ($password) {
            $this->Users_model->update_password($email, password_hash($password, PASSWORD_DEFAULT));
        }

        //only admin can disable other users login permission
        if ($this->login_user->is_admin) {
            $account_data['disable_login'] = $this->request->getPost('disable_login');
        }


        if ($this->Users_model->ci_save($account_data, $user_id)) {

            //resend new password to client contact
            if ($this->request->getPost('email_login_details')) {
                $email_template = $this->Email_templates_model->get_final_template("login_info", true);

                $user_language = $this->Users_model->get_one($user_id)->language;
                $parser_data["SIGNATURE"] = get_array_value($email_template, "signature_$user_language") ? get_array_value($email_template, "signature_$user_language") : get_array_value($email_template, "signature_default");
                $parser_data["USER_FIRST_NAME"] = $this->request->getPost('first_name');
                $parser_data["USER_LAST_NAME"] = $this->request->getPost('last_name');
                $parser_data["USER_LOGIN_EMAIL"] = $account_data["email"];
                $parser_data["USER_LOGIN_PASSWORD"] = $password;
                $parser_data["DASHBOARD_URL"] = base_url();
                $parser_data["LOGO_URL"] = get_logo_url();

                $message = get_array_value($email_template, "message_$user_language") ? get_array_value($email_template, "message_$user_language") : get_array_value($email_template, "message_default");
                $subject = get_array_value($email_template, "subject_$user_language") ? get_array_value($email_template, "subject_$user_language") : get_array_value($email_template, "subject_default");

                $message = $this->parser->setData($parser_data)->renderString($message);
                $subject = $this->parser->setData($parser_data)->renderString($subject);
                send_app_mail($email, $subject, $message);
            }

            echo json_encode(array("success" => true, 'message' => app_lang('record_updated')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //save profile image of a contact
    function save_profile_image($user_id = 0) {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->access_only_allowed_members_or_contact_personally($user_id);
        $user_info = $this->Users_model->get_one($user_id);
        $this->can_access_this_client($user_info->client_id);

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->request->getPost("profile_image"));

        if ($profile_image) {
            $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $profile_image));

            //delete old file
            delete_app_files(get_setting("profile_image_path"), array(@unserialize($user_info->image)));

            $image_data = array("image" => $profile_image);
            $this->Users_model->ci_save($image_data, $user_id);
            echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed')));
        }

        //process the the file which has uploaded using manual file submit
        if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "profile_image_file");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            if ($image_file_name) {
                if (!$this->check_profile_image_dimension($image_file_name)) {
                    echo json_encode(array("success" => false, 'message' => app_lang('profile_image_error_message')));
                    exit();
                }

                $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name));

                //delete old file
                delete_app_files(get_setting("profile_image_path"), array(@unserialize($user_info->image)));

                $image_data = array("image" => $profile_image);
                $this->Users_model->ci_save($image_data, $user_id);
                echo json_encode(array("success" => true, 'message' => app_lang('profile_image_changed'), "reload_page" => true));
            }
        }
    }

    /* delete or undo a contact */

    function delete_contact() {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->access_only_allowed_members();

        $id = $this->request->getPost('id');

        $contact_info = $this->Users_model->get_one($id);
        $this->can_access_this_client($contact_info->client_id);

        if ($this->request->getPost('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of contacts, prepared for datatable  */

    function contacts_list_data($client_id = 0) {

        $this->access_only_allowed_members_or_client_contact($client_id);
        $this->can_access_this_client($client_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients_contact", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            //"user_type" => "client",
            "client_id" => $client_id,
            "custom_fields" => $custom_fields
            /*"show_own_clients_only_user_id" => $this->show_own_clients_only_user_id(),
            "custom_field_filter" => $this->prepare_custom_field_filter_values("client_contacts", $this->login_user->is_admin, $this->login_user->user_type),
            "quick_filter" => $this->request->getPost("quick_filter"),
            "client_groups" => $this->allowed_client_groups*/
        );

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Clients_contact_model->get_details($all_options);

        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $hide_primary_contact_label = false;
        if (!$client_id) {
            $hide_primary_contact_label = true;
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_contact_row($data, $custom_fields, $hide_primary_contact_label);
        }

        $result["data"] = $result_data;

        echo json_encode($result);
    }

    /* return a row of contact list table */

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("clients_contact", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            //"user_type" => "client",
            "custom_fields" => $custom_fields
        );
        $data = $this->Clients_contact_model->get_details($options)->getRow();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields, $hide_primary_contact_label = false) {
        $image_url = "";//get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1" && !$hide_primary_contact_label) {
            $primary_contact = "<span class='bg-info badge text-white'>" . app_lang('primary_contact') . "</span>";
        }

        

        $contact_link = modal_anchor(get_uri("clients/add_new_contact_modal_form"), $full_name, array("class" => "edit", "title" => app_lang('edit_contact'), "data-post-client_id" => $data->client_id, "data-post-id" => $data->id));

        $client_info = $this->Clients_model->get_one($data->client_id);

        $row_data = array(
            $data->id,
            $contact_link,
            //anchor(get_uri("clients/view/" . $data->client_id), $data->company_name),
            $data->job_title,
            $data->address,
            $data->phone ? $data->phone : "-"
            
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        $row_data[] = js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("clients/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }



    private function can_view_tasks() {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else {
                if (get_array_value($this->login_user->permissions, "can_show_tasks") == "1") {
                    
                        return true;
                    
                } 
            }
        }
        
    }
    function clients_tasks($client_id) {
        if (!$this->can_view_tasks()) {
            app_redirect("forbidden");
        }
        $view_data['project_id'] = 0;
        $projects = $this->Tasks_model->get_my_projects_dropdown_list($this->login_user->id,$client_id)->getResult();
        $projects_dropdown = array(array("id" => "", "text" => "- " . app_lang("project") . " -"));
        foreach ($projects as $project) {
            if ($project->project_id && $project->project_title) {
                $projects_dropdown[] = array("id" => $project->project_id, "text" => $project->project_title);
            }
        }

       

        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("created_by") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0));
        foreach ($assigned_to_list as $key => $value) {

            $team_members_dropdown[] = array("id" => $key, "text" => $value/*, "isSelected" => true*/);
        }
        

        $view_data['filter'] = $this->request->getPost('filter');
        $view_data['client_id'] = $client_id;
        //$view_data['selected_status_id'] = $status_id;

        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['task_statuses'] = $this->Task_status_model->get_details()->getResult();
        $view_data['mang'] = $this->is_reserv_mang() ? 'yes':'no';

        $view_data['projects_dropdown'] = json_encode($projects_dropdown);
        $view_data['can_create_tasks'] = $this->can_create_tasks(false);
        //$view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list($priority_id);

        return $this->template->view("clients/tasks/index", $view_data);
    }


    function my_tasks_list_data($client_id) {

        $project_id = $this->request->getPost('project_id');


        $specific_user_id = $this->request->getPost('specific_user_id');

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $quick_filter = $this->request->getPost('quick_filter');
        if ($quick_filter) {
            $status = "";
        } else {
            $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
        }

        $options = array(
            "specific_user_id" => $specific_user_id,
            "project_id" => $project_id,
            "selected_year" => $this->session->get('selected_year'),
            "priority_id" => $this->request->getPost('priority_id'),
            "deadline" => $this->request->getPost('deadline'),
            "client_id" => $client_id,
            "custom_fields" => $custom_fields,
            "filter" => $this->request->getPost('filter'),
            "status_ids" => $status,
            "unread_status_user_id" => $this->login_user->id,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id(),
            "quick_filter" => $quick_filter,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );

        

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Tasks_model->get_details($all_options);

        if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_task_row($data, $custom_fields);
        }

        $result["data"] = $result_data;

        echo json_encode($result);
    }



    private function _task_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id, "custom_fields" => $custom_fields);
        $data = $this->Tasks_model->get_details($options)->getRow();

        $this->init_project_permission_checker($data->project_id);

        return $this->_make_task_row($data, $custom_fields);
    }

    /* prepare a row of task list table */

    private function _make_task_row($data, $custom_fields) {
        $unread_comments_class = "";
        $icon = "";
        if (isset($data->unread) && $data->unread && $data->unread != "0") {
            $unread_comments_class = "unread-comments-of-tasks";
            $icon = "<i data-feather='message-circle' class='icon-16 ml5 unread-comments-of-tasks-icon'></i>";
        }


        $title = "";
        $main_task_id = "#" . $data->id;
        $project_title="";
        $project_t =$data->project_title ? $data->project_title:" - ";
        

        $project_title ='';
        if ($this->is_reserv_mang()) {
            $project_title .=anchor(get_uri("subtasks/index/".$data->id), $data->project_id? $data->project_title:'تحديد المشروع');
        }
        elseif ($this->is_supply_mang()) {
            $project_title .=anchor(get_uri("subtasks/supply_mang/".$data->id), $data->project_id? $data->project_title:'تحديد المشروع');
        }else{
            $project_title .= $data->client_id? $data->company_name:'تحديد عميل';
        }
        
       
         
        if ($data->sub_task_count) {
            //this is a sub task
            if ($this->is_reserv_mang()) {
            
            $project_title.= "<span class='badge badge-light  mt0' title='".app_lang('sub_task')."'>".$data->sub_task_count."</span>";
        }else{
            $project_title .= "<span class='badge badge-light  mt0' title='".app_lang('sub_task')."'>".$data->sub_task_count."</span>";
        }
        }else{
            $project_title.="<span class='badge badge-light  mt0' title='".app_lang('sub_task')."'>0</span>";
        }

        

        //$project_title =$data->project_title ? $data->project_title:" - ";

        

        $assigned_to = "-";

        if ($data->created_by) {
            $image_url = get_avatar($data->assigned_to_avatar);
            $assigned_to_user = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->assigned_to_user";
            $assigned_to = get_team_member_profile_link($data->created_by, $assigned_to_user);

                $assigned_to = get_team_member_profile_link($data->created_by, $assigned_to_user);
            
        }


            $checkbox_class = "checkbox-checked";
        $created_date = "-";
        if (is_date_exists($data->created_date)) {
            $created_date = format_to_date($data->created_date, false);
        }

        $options = "";
        if ($this->can_edit_tasks()) {
            $options .= modal_anchor(get_uri("tasks/task_view"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('task_info'). " #$data->id", "data-post-id" => $data->id));
        }
        


        $client_name = $data->client_id!=0?anchor(get_uri("clients/view/" . $data->client_id), $data->company_name):'لم يتم التحديد';
        $status_color=$data->is_closed==1 ? '#4a9d27':'#e50f16bd';
        $check_status = js_anchor("<span class=' mr15 float-start'></span>", array('title' => "", "class" => "js-task", "data-id" => $data->id,  "data-act" => "update-task-status-checkbox")) . $data->id;
      
        $row_data = array(
            $status_color,
            $check_status,
            $project_title,
            $data->christening_number?$data->christening_number:'___',
            $data->invoice_number?$data->invoice_number:'___',
            $data->created_date,
            
            
            $assigned_to,
            //$data->is_closed==0 ? 'Open':'Closed'
        );

    


    $row_data[] = js_anchor($data->is_closed==1 ? app_lang('open'):app_lang('closed'), array("style" => "background-color: $status_color", "class" => "badge", "data-id" => $data->id, "data-value" => $data->is_closed, "data-act" => "update-mytask-status"));

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }


        $row_data[] = modal_anchor(get_uri("clients/task_modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('task_info'). " #$data->id", "data-post-id" => $data->id, "data-post-client_id" => $data->client_id));



        $row_data[] = $options;

        return $row_data;
    }



    function task_modal_form() {
        $id = $this->request->getPost('id');
        $add_type = $this->request->getPost('add_type');
        $last_id = $this->request->getPost('last_id');
        $client_id = $this->request->getPost('client_id');

        $model_info = $this->Tasks_model->get_one($id);
        $project_id = $this->request->getPost('project_id') ? $this->request->getPost('project_id') : $model_info->project_id;

        $final_project_id = $project_id;
        if ($add_type == "multiple" && $last_id) {
            //we've to show the lastly added information if it's the operation of adding multiple tasks
            $model_info = $this->Tasks_model->get_one($last_id);

            //if we got lastly added task id, then we have to initialize all data of that in order to make dropdowns
            $final_project_id = $model_info->project_id;
        }

        //$view_data = $this->_initialize_all_related_data_of_project($final_project_id, $model_info->collaborators, $model_info->labels);

        if ($id) {
            if (!$this->can_edit_tasks()) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_create_tasks($project_id ? true : false)) {
                app_redirect("forbidden");
            }
        }

        $view_data['model_info'] = $model_info;
        $view_data["projects_dropdown"] = $this->_get_projects_dropdown();

        $view_data["cities_dropdown"] = $this->_get_cities_dropdown();

        $view_data["clients_dropdown"] = $this->_get_myclients_dropdown();
        $view_data["contacts_dropdown"] = $client_id ? $this->get_client_contact_dropdown_new($client_id?$client_id:$model_info->client_id,$model_info->client_contact_id):null;

         //projects dropdown is necessary on add multiple tasks
        $view_data["add_type"] = $add_type;
        $view_data['project_id'] = $project_id;
        $view_data['supplier_id'] = $model_info->supplier_id;
        $view_data['client_id'] = $client_id?$client_id:$model_info->client_id;
        $view_data['client_contact_id'] = $model_info->client_contact_id;
        $user_info = $this->Clients_contact_model->get_one($model_info->client_contact_id);
        $view_data['contact_name'] = $user_info->first_name.' '.$user_info->last_name;
        

        $view_data['show_assign_to_dropdown'] = true;
        if ($this->login_user->user_type == "client") {
            if (!get_setting("client_can_assign_tasks")) {
                $view_data['show_assign_to_dropdown'] = false;
            }
        } else {
            //set default assigne to for new tasks
            if (!$id && !$view_data['model_info']->created_by) {
                $view_data['model_info']->created_by = $this->login_user->id;
            }
        }

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("tasks", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        //clone task
        $is_clone = $this->request->getPost('is_clone');
        $view_data['is_clone'] = $is_clone;

        $view_data['view_type'] = $this->request->getPost("view_type");

        $view_data['has_checklist'] = $this->Checklist_items_model->get_details(array("task_id" => $id))->resultID->num_rows;
        $view_data['has_sub_task'] = count($this->Tasks_model->get_all_where(array("parent_task_id" => $id, "deleted" => 0))->getResult());


        return $this->template->view('clients/tasks/modal_form', $view_data);
    }

    function get_client_contact_dropdown_new($client_id,$contact_id=0) {


        

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


            $options = array("status" => "open");
            $options["client_id"] = $client_id;
        

        $projects = $this->Projects_model->get_details($options)->getResult();

            $projects_dropdown = array();
        foreach ($projects as $project) {
            $projects_dropdown[] = array("id" => $project->id, "text" => $project->title);
        }
            $related_data = array(
                "contacts_dropdown" => $contacts_dropdown,
                "projects_dropdown" => $projects_dropdown,
               );


            return json_encode(array(
                "contacts_dropdown" => $related_data["contacts_dropdown"],
                "projects_dropdown" => $projects_dropdown,
               
            ));
    }

    /* open invitation modal */

    function invitation_modal() {
        if (get_setting("disable_user_invitation_option_by_clients") && $this->login_user->user_type == "client") {
            app_redirect("forbidden");
        }

        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $this->validate_submitted_data(array(
            "client_id" => "required|numeric"
        ));

        $client_id = $this->request->getPost('client_id');
        $this->can_access_this_client($client_id);

        $this->access_only_allowed_members_or_client_contact($client_id);

        $view_data["client_info"] = $this->Clients_model->get_one($client_id);
        return $this->template->view('clients/contacts/invitation_modal', $view_data);
    }

    //send a team member invitation to an email address
    function send_invitation() {
        if (get_setting("disable_user_invitation_option_by_clients") && $this->login_user->user_type == "client") {
            app_redirect("forbidden");
        }

        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        $client_id = $this->request->getPost('client_id');
        $this->can_access_this_client($client_id);

        $email = trim($this->request->getPost('email'));

        $this->validate_submitted_data(array(
            "client_id" => "required|numeric",
            "email" => "required|valid_email|trim"
        ));

        $this->access_only_allowed_members_or_client_contact($client_id);

        $email_template = $this->Email_templates_model->get_final_template("client_contact_invitation"); //use default template since sending new invitation

        $parser_data["INVITATION_SENT_BY"] = $this->login_user->first_name . " " . $this->login_user->last_name;
        $parser_data["SIGNATURE"] = $email_template->signature;
        $parser_data["SITE_URL"] = get_uri();
        $parser_data["LOGO_URL"] = get_logo_url();

        $verification_data = array(
            "type" => "invitation",
            "code" => make_random_string(),
            "params" => serialize(array(
                "email" => $email,
                "type" => "client",
                "client_id" => $client_id,
                "expire_time" => time() + (24 * 60 * 60) //make the invitation url with 24hrs validity
            ))
        );

        $save_id = $this->Verification_model->ci_save($verification_data);
        $verification_info = $this->Verification_model->get_one($save_id);

        $parser_data['INVITATION_URL'] = get_uri("signup/accept_invitation/" . $verification_info->code);

        //send invitation email
        $message = $this->parser->setData($parser_data)->renderString($email_template->message);
        if (send_app_mail($email, $email_template->subject, $message)) {
            echo json_encode(array('success' => true, 'message' => app_lang("invitation_sent")));
        } else {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* only visible to client  */

    function users() {
        if ($this->login_user->user_type === "client") {
            $view_data['client_id'] = $this->login_user->client_id;
            return $this->template->rander("clients/contacts/users", $view_data);
        }
    }

    /* show keyboard shortcut modal form */

    function keyboard_shortcut_modal_form() {
        return $this->template->view('team_members/keyboard_shortcut_modal_form');
    }

    function upload_excel_file() {
        upload_file_to_temp(true);
    }

    function import_clients_modal_form() {
        $this->access_only_allowed_members();
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        return $this->template->view("clients/import_clients_modal_form");
    }

    private function _prepare_client_data($data_row, $allowed_headers) {
        //prepare client data
        $client_data = array();
        $client_contact_data = array("user_type" => "client", "is_primary_contact" => 1);
        $custom_field_values_array = array();

        foreach ($data_row as $row_data_key => $row_data_value) { //row values
            if (!$row_data_value) {
                continue;
            }

            $header_key_value = get_array_value($allowed_headers, $row_data_key);
            if (strpos($header_key_value, 'cf') !== false) { //custom field
                $explode_header_key_value = explode("-", $header_key_value);
                $custom_field_id = get_array_value($explode_header_key_value, 1);

                //modify date value
                $custom_field_info = $this->Custom_fields_model->get_one($custom_field_id);
                if ($custom_field_info->field_type === "date") {
                    $row_data_value = $this->_check_valid_date($row_data_value);
                }

                $custom_field_values_array[$custom_field_id] = $row_data_value;
            } else if ($header_key_value == "client_groups") { //we've to make client groups data differently
                $client_data["group_ids"] = $this->_get_client_group_ids($row_data_value);
            } else if ($header_key_value == "contact_first_name") {
                $client_contact_data["first_name"] = $row_data_value;
            } else if ($header_key_value == "contact_last_name") {
                $client_contact_data["last_name"] = $row_data_value;
            } else if ($header_key_value == "contact_email") {
                $client_contact_data["email"] = $row_data_value;
            } else {
                $client_data[$header_key_value] = $row_data_value;
            }
        }

        return array(
            "client_data" => $client_data,
            "client_contact_data" => $client_contact_data,
            "custom_field_values_array" => $custom_field_values_array
        );
    }

    private function _get_existing_custom_field_id($title = "") {
        if (!$title) {
            return false;
        }

        $custom_field_data = array(
            "title" => $title,
            "related_to" => "clients"
        );

        $existing = $this->Custom_fields_model->get_one_where(array_merge($custom_field_data, array("deleted" => 0)));
        if ($existing->id) {
            return $existing->id;
        }
    }

    private function _prepare_headers_for_submit($headers_row, $headers) {
        foreach ($headers_row as $key => $header) {
            if (!((count($headers) - 1) < $key)) { //skip default headers
                continue;
            }

            //so, it's a custom field
            //check if there is any custom field existing with the title
            //add id like cf-3
            $existing_id = $this->_get_existing_custom_field_id($header);
            if ($existing_id) {
                array_push($headers, "cf-$existing_id");
            }
        }

        return $headers;
    }

    function save_client_from_excel_file() {
        if (!$this->can_edit_clients()) {
            app_redirect("forbidden");
        }

        if (!$this->validate_import_clients_file_data(true)) {
            echo json_encode(array('success' => false, 'message' => app_lang('error_occurred')));
        }

        $file_name = $this->request->getPost('file_name');
        require_once(APPPATH . "ThirdParty/PHPOffice-PhpSpreadsheet/vendor/autoload.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = \PhpOffice\PhpSpreadsheet\IOFactory::load($temp_file_path . $file_name);
        $excel_file = $excel_file->getActiveSheet()->toArray();

        $allowed_headers = $this->_get_allowed_headers();
        $now = get_current_utc_time();

        foreach ($excel_file as $key => $value) { //rows
            if ($key === 0) { //first line is headers, modify this for custom fields and continue for the next loop
                $allowed_headers = $this->_prepare_headers_for_submit($value, $allowed_headers);
                continue;
            }

            $client_data_array = $this->_prepare_client_data($value, $allowed_headers);
            $client_data = get_array_value($client_data_array, "client_data");
            $client_contact_data = get_array_value($client_data_array, "client_contact_data");
            $custom_field_values_array = get_array_value($client_data_array, "custom_field_values_array");

            //couldn't prepare valid data
            if (!($client_data && count($client_data))) {
                continue;
            }

            //found information about client, add some additional info
            $client_data["created_date"] = $now;
            $client_data["created_by"] = $this->login_user->id;
            $client_contact_data["created_at"] = $now;

            //save client data
            $client_save_id = $this->Clients_model->ci_save($client_data);
            if (!$client_save_id) {
                continue;
            }

            //save custom fields
            $this->_save_custom_fields_of_client($client_save_id, $custom_field_values_array);

            //add client id to contact data
            $client_contact_data["client_id"] = $client_save_id;
            $this->Users_model->ci_save($client_contact_data);
        }

        delete_file_from_directory($temp_file_path . $file_name); //delete temp file

        echo json_encode(array('success' => true, 'message' => app_lang("record_saved")));
    }

    private function _save_custom_fields_of_client($client_id, $custom_field_values_array) {
        if (!$custom_field_values_array) {
            return false;
        }

        foreach ($custom_field_values_array as $key => $custom_field_value) {
            $field_value_data = array(
                "related_to_type" => "clients",
                "related_to_id" => $client_id,
                "custom_field_id" => $key,
                "value" => $custom_field_value
            );

            $field_value_data = clean_data($field_value_data);

            $this->Custom_field_values_model->ci_save($field_value_data);
        }
    }

    private function _get_client_group_ids($client_groups_data) {
        $explode_client_groups = explode(", ", $client_groups_data);
        if (!($explode_client_groups && count($explode_client_groups))) {
            return false;
        }

        $groups_ids = "";

        foreach ($explode_client_groups as $group) {
            $group_id = "";
            $existing_group = $this->Client_groups_model->get_one_where(array("title" => $group, "deleted" => 0));
            if ($existing_group->id) {
                //client group exists, add the group id
                $group_id = $existing_group->id;
            } else {
                //client group doesn't exists, create a new one and add group id
                $group_data = array("title" => $group);
                $group_id = $this->Client_groups_model->ci_save($group_data);
            }

            //add the group id to group ids
            if ($groups_ids) {
                $groups_ids .= ",";
            }
            $groups_ids .= $group_id;
        }

        if ($groups_ids) {
            return $groups_ids;
        }
    }

    private function _get_allowed_headers() {
        return array(
            "company_name",
            "contact_first_name",
            "contact_last_name",
            "contact_email",
            "address",
            "city",
            "state",
            "zip",
            "country",
            "phone",
            "website",
            "vat_number",
            "client_groups",
            "currency",
            "currency_symbol"
        );
    }

    private function _store_headers_position($headers_row = array()) {
        $allowed_headers = $this->_get_allowed_headers();

        //check if all headers are correct and on the right position
        $final_headers = array();
        foreach ($headers_row as $key => $header) {
            if (!$header) {
                continue;
            }

            $key_value = str_replace(' ', '_', strtolower(trim($header, " ")));
            $header_on_this_position = get_array_value($allowed_headers, $key);
            $header_array = array("key_value" => $header_on_this_position, "value" => $header);

            if ($header_on_this_position == $key_value) {
                //allowed headers
                //the required headers should be on the correct positions
                //the rest headers will be treated as custom fields
                //pushed header at last of this loop
            } else if (((count($allowed_headers) - 1) < $key) && $key_value) {
                //custom fields headers
                //check if there is any existing custom field with this title
                $existing_id = $this->_get_existing_custom_field_id(trim($header, " "));
                if ($existing_id) {
                    $header_array["custom_field_id"] = $existing_id;
                } else {
                    $header_array["has_error"] = true;
                    $header_array["custom_field"] = true;
                }
            } else { //invalid header, flag as red
                $header_array["has_error"] = true;
            }

            if ($key_value) {
                array_push($final_headers, $header_array);
            }
        }

        return $final_headers;
    }

    function validate_import_clients_file() {
        $this->access_only_allowed_members();

        $file_name = $this->request->getPost("file_name");
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => app_lang('invalid_file_type')));
            exit();
        }

        if ($file_ext == "xlsx") {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('please_upload_a_excel_file') . " (.xlsx)"));
        }
    }

    function validate_import_clients_file_data($check_on_submit = false) {
        $this->access_only_allowed_members();

        $table_data = "";
        $error_message = "";
        $headers = array();
        $got_error_header = false; //we've to check the valid headers first, and a single header at a time
        $got_error_table_data = false;

        $file_name = $this->request->getPost("file_name");

        require_once(APPPATH . "ThirdParty/PHPOffice-PhpSpreadsheet/vendor/autoload.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = \PhpOffice\PhpSpreadsheet\IOFactory::load($temp_file_path . $file_name);
        $excel_file = $excel_file->getActiveSheet()->toArray();

        $table_data .= '<table class="table table-responsive table-bordered table-hover" style="width: 100%; color: #444;">';

        $table_data_header_array = array();
        $table_data_body_array = array();

        foreach ($excel_file as $row_key => $value) {
            if ($row_key == 0) { //validate headers
                $headers = $this->_store_headers_position($value);

                foreach ($headers as $row_data) {
                    $has_error_class = false;
                    if (get_array_value($row_data, "has_error") && !$got_error_header) {
                        $has_error_class = true;
                        $got_error_header = true;

                        if (get_array_value($row_data, "custom_field")) {
                            $error_message = app_lang("no_such_custom_field_found");
                        } else {
                            $error_message = sprintf(app_lang("import_client_error_header"), app_lang(get_array_value($row_data, "key_value")));
                        }
                    }

                    array_push($table_data_header_array, array("has_error_class" => $has_error_class, "value" => get_array_value($row_data, "value")));
                }
            } else { //validate data
                if (!array_filter($value)) {
                    continue;
                }

                $error_message_on_this_row = "<ol class='pl15'>";
                $has_contact_first_name = get_array_value($value, 1) ? true : false;

                foreach ($value as $key => $row_data) {
                    $has_error_class = false;

                    if (!$got_error_header) {
                        $row_data_validation = $this->_row_data_validation_and_get_error_message($key, $row_data, $has_contact_first_name, $headers);
                        if ($row_data_validation) {
                            $has_error_class = true;
                            $error_message_on_this_row .= "<li>" . $row_data_validation . "</li>";
                            $got_error_table_data = true;
                        }
                    }

                    if (count($headers) > $key) {
                        $table_data_body_array[$row_key][] = array("has_error_class" => $has_error_class, "value" => $row_data);
                    }
                }

                $error_message_on_this_row .= "</ol>";

                //error messages for this row
                if ($got_error_table_data) {
                    $table_data_body_array[$row_key][] = array("has_error_text" => true, "value" => $error_message_on_this_row);
                }
            }
        }

        //return false if any error found on submitting file
        if ($check_on_submit) {
            return ($got_error_header || $got_error_table_data) ? false : true;
        }

        //add error header if there is any error in table body
        if ($got_error_table_data) {
            array_push($table_data_header_array, array("has_error_text" => true, "value" => app_lang("error")));
        }

        //add headers to table
        $table_data .= "<tr>";
        foreach ($table_data_header_array as $table_data_header) {
            $error_class = get_array_value($table_data_header, "has_error_class") ? "error" : "";
            $error_text = get_array_value($table_data_header, "has_error_text") ? "text-danger" : "";
            $value = get_array_value($table_data_header, "value");
            $table_data .= "<th class='$error_class $error_text'>" . $value . "</th>";
        }
        $table_data .= "</tr>";

        //add body data to table
        foreach ($table_data_body_array as $table_data_body_row) {
            $table_data .= "<tr>";
            $error_text = "";

            foreach ($table_data_body_row as $table_data_body_row_data) {
                $error_class = get_array_value($table_data_body_row_data, "has_error_class") ? "error" : "";
                $error_text = get_array_value($table_data_body_row_data, "has_error_text") ? "text-danger" : "";
                $value = get_array_value($table_data_body_row_data, "value");
                $table_data .= "<td class='$error_class $error_text'>" . $value . "</td>";
            }

            if ($got_error_table_data && !$error_text) {
                $table_data .= "<td></td>";
            }

            $table_data .= "</tr>";
        }

        //add error message for header
        if ($error_message) {
            $total_columns = count($table_data_header_array);
            $table_data .= "<tr><td class='text-danger' colspan='$total_columns'><i data-feather='alert-triangle' class='icon-16'></i> " . $error_message . "</td></tr>";
        }

        $table_data .= "</table>";

        echo json_encode(array("success" => true, 'table_data' => $table_data, 'got_error' => ($got_error_header || $got_error_table_data) ? true : false));
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
        return $this->template->view("clients/contacts/gdpr", $view_data);
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

    function request_my_account_removal() {
        if (get_setting("enable_gdpr") && get_setting("clients_can_request_account_removal")) {

            $user_id = $this->login_user->id;
            $data = array("requested_account_removal" => 1);
            $this->Users_model->ci_save($data, $user_id);

            $client_id = $this->Users_model->get_one($user_id)->client_id;
            log_notification("client_contact_requested_account_removal", array("client_id" => $client_id), $user_id);

            $this->session->setFlashdata("success_message", app_lang("estimate_submission_message"));
            app_redirect("clients/contact_profile/$user_id/gdpr");
        }
    }

    /* load expenses tab  */

    

    function contracts($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("contracts", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("contracts", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("clients/contracts/contracts", $view_data);
        }
    }

    function clients_list() {
        $this->access_only_allowed_members();

        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("clients", $this->login_user->is_admin, $this->login_user->user_type);

        $access_info = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_info->access_type == "all") ? true : false;
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("clients", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['groups_dropdown'] = json_encode($this->_get_groups_dropdown_select2_data(true));
        $view_data['can_edit_clients'] = $this->can_edit_clients();
        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown(true);

        return $this->template->view("clients/clients_list", $view_data);
    }

    private function make_access_permissions_view_data() {

        $access_invoice = $this->get_access_info("invoice");
        $view_data["show_invoice_info"] = (get_setting("module_invoice") && $access_invoice->access_type == "all") ? true : false;

        $access_estimate = $this->get_access_info("estimate");
        $view_data["show_estimate_info"] = (get_setting("module_estimate") && $access_estimate->access_type == "all") ? true : false;

        $access_estimate_request = $this->get_access_info("estimate_request");
        $view_data["show_estimate_request_info"] = (get_setting("module_estimate_request") && $access_estimate_request->access_type == "all") ? true : false;

        $access_order = $this->get_access_info("order");
        $view_data["show_order_info"] = (get_setting("module_order") && $access_order->access_type == "all") ? true : false;

        $access_proposal = $this->get_access_info("proposal");
        $view_data["show_proposal_info"] = (get_setting("module_proposal") && $access_proposal->access_type == "all") ? true : false;

        $access_ticket = $this->get_access_info("ticket");
        $view_data["show_ticket_info"] = (get_setting("module_ticket") && $access_ticket->access_type == "all") ? true : false;

        $access_contract = $this->get_access_info("contract");
        $view_data["show_contract_info"] = (get_setting("module_contract") && $access_contract->access_type == "all") ? true : false;
        $view_data["show_project_info"] = !$this->has_all_projects_restricted_role();

        return $view_data;
    }

    function proposals($client_id) {
        validate_numeric_value($client_id);
        $this->access_only_allowed_members();

        if ($client_id) {
            $view_data["client_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("proposals", $this->login_user->is_admin, $this->login_user->user_type);
            $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("proposals", $this->login_user->is_admin, $this->login_user->user_type);

            return $this->template->view("clients/proposals/proposals", $view_data);
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