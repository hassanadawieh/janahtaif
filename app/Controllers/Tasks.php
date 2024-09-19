<?php

namespace App\Controllers;

class Tasks extends Security_Controller
{

    protected $Project_settings_model;
    protected $Checklist_items_model;
    protected $Likes_model;
    protected $Pin_comments_model;
    protected $File_category_model;
    protected $Task_priority_model;

    public function __construct()
    {
        parent::__construct();
        if (!$this->can_show_tasks()) {
            app_redirect("forbidden");
        }

        $this->Project_settings_model = model('App\Models\Project_settings_model');
        $this->Checklist_items_model = model('App\Models\Checklist_items_model');
        $this->Likes_model = model('App\Models\Likes_model');
        $this->Pin_comments_model = model('App\Models\Pin_comments_model');
        $this->File_category_model = model('App\Models\File_category_model');
        $this->Task_priority_model = model("App\Models\Task_priority_model");
    }




    private function can_delete_projects($project_id = 0)
    {
        if ($this->login_user->user_type == "staff") {


            $can_delete_projects = get_array_value($this->login_user->permissions, "can_delete_projects");
            $can_delete_only_own_created_projects = get_array_value($this->login_user->permissions, "can_delete_only_own_created_projects");

            if ($can_delete_projects) {
                return true;
            }

            if ($project_id) {
                $project_info = $this->Projects_model->get_one($project_id);
                if ($can_delete_only_own_created_projects && $project_info->created_by === $this->login_user->id) {
                    return true;
                }
            } else if ($can_delete_only_own_created_projects) { //no project given and the user has partial access
                return true;
            }
        }
    }


    private function can_view_tasks()
    {
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

    private function can_delete_tasks()
    {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_delete_tasks") == "1") {
                //check is user a project member
                return true;
            }
        }
    }

    private function can_update_maintask_status()
    {
        if ($this->login_user->user_type == "staff") {
            if ($this->can_manage_all_projects()) {
                return true;
            } else if (get_array_value($this->login_user->permissions, "can_update_maintask_status") == "1") {
                //check is user a project member
                return true;
            }
        }
    }





    /* load the project settings into ci settings */

    private function init_project_settings($project_id)
    {
        $settings = $this->Project_settings_model->get_all_where(array("project_id" => $project_id))->getResult();
        foreach ($settings as $setting) {
            config('Rise')->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }



    /* load project view */

    function index()
    {
        app_redirect("projects/all_projects");
    }





    /* Show a modal to clone a project */

    function clone_project_modal_form()
    {

        $project_id = $this->request->getPost('id');

        if (!$this->can_create_projects()) {
            app_redirect("forbidden");
        }


        $view_data['model_info'] = $this->Projects_model->get_one($project_id);

        $view_data['clients_dropdown'] = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));

        $view_data['label_suggestions'] = $this->make_labels_dropdown("project", $view_data['model_info']->labels);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("projects", $view_data['model_info']->id, 1, "staff")->getResult(); //we have to keep this regarding as an admin user because non-admin user also can acquire the access to clone a project

        return $this->template->view('projects/clone_project_modal_form', $view_data);
    }



    private function _prepare_new_task_data_on_cloning_project($new_project_id, $milestones_array, $task, $copy_same_assignee_and_collaborators, $copy_tasks_start_date_and_deadline, $move_all_tasks_to_to_do, $change_the_tasks_start_date_and_deadline_based_on_project_start_date, $old_project_info, $project_start_date)
    {
        //prepare new task data. 
        $task->project_id = $new_project_id;
        $milestone_id = get_array_value($milestones_array, $task->milestone_id);
        $task->milestone_id = $milestone_id ? $milestone_id : "";
        $task->status = "to_do";

        if (!$copy_same_assignee_and_collaborators) {
            $task->assigned_to = "";
            $task->collaborators = "";
        }

        $task_data = (array) $task;
        unset($task_data["id"]); //remove id from existing data

        if ($move_all_tasks_to_to_do) {
            $task_data["status"] = "to_do";
            $task_data["status_id"] = 1;
        }

        if (!$copy_tasks_start_date_and_deadline && !$change_the_tasks_start_date_and_deadline_based_on_project_start_date) {
            $task->start_date = NULL;
            $task->deadline = NULL;
        } else if ($change_the_tasks_start_date_and_deadline_based_on_project_start_date) {
            $old_project_start_date = $old_project_info->start_date;
            $old_task_start_date = $task->start_date;
            $old_task_end_date = $task->deadline;
            $task_start_date_diff = abs(strtotime($old_task_start_date ? $old_task_start_date : "") - strtotime($old_project_start_date ? $old_project_start_date : ""));
            $task_end_date_diff = abs(strtotime($old_task_end_date ? $old_task_end_date : "") - strtotime($old_project_start_date ? $old_project_start_date : ""));

            // 1 day = 24 hours
            // 24 * 60 * 60 = 86400 seconds
            $start_date_day_diff = $task_start_date_diff / 86400;
            $end_date_day_diff = $task_end_date_diff / 86400;

            $task_data["start_date"] = add_period_to_date($project_start_date, $start_date_day_diff, "days");
            $task_data["deadline"] = add_period_to_date($project_start_date, $end_date_day_diff, "days");
        }

        return $task_data;
    }

    private function _save_custom_fields_on_cloning_project($task, $new_taks_id)
    {
        $old_custom_fields = $this->Custom_field_values_model->get_all_where(array("related_to_type" => "tasks", "related_to_id" => $task->id, "deleted" => 0))->getResult();

        //prepare new custom fields data
        foreach ($old_custom_fields as $field) {
            $field->related_to_id = $new_taks_id;

            $fields_data = (array) $field;
            unset($fields_data["id"]); //remove id from existing data
            //save custom field
            $this->Custom_field_values_model->ci_save($fields_data);
        }
    }



    /* list of projcts, prepared for datatable  */

    function list_data()
    {

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $statuses = $this->request->getPost('status') ? implode(",", $this->request->getPost('status')) : "";

        $options = array(
            "statuses" => $statuses,
            "project_label" => $this->request->getPost("project_label"),
            "custom_fields" => $custom_fields,
            "deadline" => $this->request->getPost('deadline'),
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

    /* list of projcts, prepared for datatable  */

    function projects_list_data_of_team_member($team_member_id = 0)
    {
        validate_numeric_value($team_member_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("projects", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array(
            "status" => $this->request->getPost("status"),
            "custom_fields" => $custom_fields,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("projects", $this->login_user->is_admin, $this->login_user->user_type)
        );

        //add can see all members projects but team members can see only ther own projects
        /*if (!$this->can_manage_all_projects() && $team_member_id != $this->login_user->id) {
            app_redirect("forbidden");
        }*/

        $options["user_id"] = $team_member_id;

        $list_data = $this->Projects_model->get_details($options)->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }



    private function can_edit_timesheet_settings($project_id)
    {
        $this->init_project_permission_checker($project_id);
        if ($project_id && $this->login_user->user_type === "staff" && $this->can_view_timesheet($project_id)) {
            return true;
        }
    }

    private function can_edit_slack_settings()
    {
        if ($this->login_user->user_type === "staff" && $this->can_create_projects()) {
            return true;
        }
    }




    private function _get_project_members_dropdown_list_for_filter($project_id)
    {

        $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id)->getResult();
        $project_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("member") . " -"));
        foreach ($project_members as $member) {
            $project_members_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);
        }
        return $project_members_dropdown;
    }

    /* load timelog add/edit modal */

    function timelog_modal_form()
    {
        $view_data['time_format_24_hours'] = get_setting("time_format") == "24_hours" ? true : false;
        $model_info = $this->Timesheets_model->get_one($this->request->getPost('id'));
        $project_id = $this->request->getPost('project_id') ? $this->request->getPost('project_id') : $model_info->project_id;

        //set the login user as a default selected member
        if (!$model_info->user_id) {
            $model_info->user_id = $this->login_user->id;
        }

        //get related data
        $related_data = $this->_prepare_all_related_data_for_timelog($project_id);
        $show_porject_members_dropdown = get_array_value($related_data, "show_porject_members_dropdown");
        $view_data["tasks_dropdown"] = get_array_value($related_data, "tasks_dropdown");
        $view_data["project_members_dropdown"] = get_array_value($related_data, "project_members_dropdown");

        $view_data["model_info"] = $model_info;

        if ($model_info->id) {
            $show_porject_members_dropdown = false; //don't allow to edit the user on update.
        }

        $view_data["project_id"] = $project_id;
        $view_data['show_porject_members_dropdown'] = $show_porject_members_dropdown;
        $view_data["projects_dropdown"] = $this->_get_projects_dropdown();

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("timesheets", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();

        return $this->template->view('projects/timesheets/modal_form', $view_data);
    }

    private function _prepare_all_related_data_for_timelog($project_id = 0)
    {
        //we have to check if any defined project exists, then go through with the project id
        $show_porject_members_dropdown = false;
        if ($project_id) {
            $tasks_dropdown = $this->_get_timesheet_tasks_dropdown($project_id, true);

            //prepare members dropdown list
            $allowed_members = $this->_get_members_to_manage_timesheet();
            $project_members = "";

            if ($allowed_members === "all") {
                $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id)->getResult(); //get all members of this project
            } else {
                $project_members = $this->Project_members_model->get_project_members_dropdown_list($project_id, $allowed_members)->getResult();
            }

            $project_members_dropdown = array();
            if ($project_members) {
                foreach ($project_members as $member) {

                    if ($member->user_id !== $this->login_user->id) {
                        $show_porject_members_dropdown = true; //user can manage other users time.
                    }

                    $project_members_dropdown[] = array("id" => $member->user_id, "text" => $member->member_name);
                }
            }
        } else {
            //we have show an empty dropdown when there is no project_id defined
            $tasks_dropdown = json_encode(array(array("id" => "", "text" => "-")));
            $project_members_dropdown = array(array("id" => "", "text" => "-"));
            $show_porject_members_dropdown = true;
        }

        return array(
            "project_members_dropdown" => $project_members_dropdown,
            "tasks_dropdown" => $tasks_dropdown,
            "show_porject_members_dropdown" => $show_porject_members_dropdown
        );
    }

    function get_all_related_data_of_selected_project_for_timelog($project_id = "")
    {
        validate_numeric_value($project_id);
        if ($project_id) {
            $related_data = $this->_prepare_all_related_data_for_timelog($project_id);

            echo json_encode(array(
                "project_members_dropdown" => get_array_value($related_data, "project_members_dropdown"),
                "tasks_dropdown" => json_decode(get_array_value($related_data, "tasks_dropdown"))
            ));
        }
    }

    /* insert/update a timelog */

    function save_timelog()
    {
        $id = $this->request->getPost('id');

        $start_date_time = "";
        $end_date_time = "";
        $hours = "";

        $start_time = $this->request->getPost('start_time');
        $end_time = $this->request->getPost('end_time');
        $note = $this->request->getPost("note");
        $task_id = $this->request->getPost("task_id");

        if ($start_time) {
            //start time and end time mode
            //convert to 24hrs time format
            if (get_setting("time_format") != "24_hours") {
                $start_time = convert_time_to_24hours_format($start_time);
                $end_time = convert_time_to_24hours_format($end_time);
            }

            //join date with time
            $start_date_time = $this->request->getPost('start_date') . " " . $start_time;
            $end_date_time = $this->request->getPost('end_date') . " " . $end_time;

            //add time offset
            $start_date_time = convert_date_local_to_utc($start_date_time);
            $end_date_time = convert_date_local_to_utc($end_date_time);
        } else {
            //date and hour mode
            $date = $this->request->getPost("date");
            $start_date_time = $date . " 00:00:00";
            $end_date_time = $date . " 00:00:00";

            //prepare hours
            $hours = convert_humanize_data_to_hours($this->request->getPost("hours"));
            if (!$hours) {
                echo json_encode(array("success" => false, 'message' => app_lang("hour_log_time_error_message")));
                return false;
            }
        }

        $project_id = $this->request->getPost('project_id');
        $data = array(
            "project_id" => $project_id,
            "start_time" => $start_date_time,
            "end_time" => $end_date_time,
            "note" => $note ? $note : "",
            "task_id" => $task_id ? $task_id : 0,
            "hours" => $hours
        );

        //save user_id only on insert and it will not be editable
        if (!$id) {
            //insert mode
            $data["user_id"] = $this->request->getPost('user_id') ? $this->request->getPost('user_id') : $this->login_user->id;
        }

        $this->check_timelog_update_permission($id, $project_id, get_array_value($data, "user_id"));

        $save_id = $this->Timesheets_model->ci_save($data, $id);
        if ($save_id) {

            save_custom_fields("timesheets", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* delete/undo a timelog */

    function delete_timelog()
    {

        $id = $this->request->getPost('id');

        $this->check_timelog_update_permission($id);

        if ($this->request->getPost('undo')) {
            if ($this->Timesheets_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_timesheet_row_data($id), "message" => app_lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            if ($this->Timesheets_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        }
    }

    private function check_timelog_update_permission($log_id = null, $project_id = null, $user_id = null)
    {
        if ($log_id) {
            $info = $this->Timesheets_model->get_one($log_id);
            $user_id = $info->user_id;
        }

        if (!$log_id && $user_id === $this->login_user->id) { //adding own timelogs
            return true;
        }

        $members = $this->_get_members_to_manage_timesheet();

        if ($members === "all") {
            return true;
        } else if (is_array($members) && count($members) && in_array($user_id, $members)) {
            //permission: no / own / specific / specific_excluding_own
            $timesheet_manage_permission = get_array_value($this->login_user->permissions, "timesheet_manage_permission");

            if (!$timesheet_manage_permission && $log_id) { //permission: no
                app_redirect("forbidden");
            }

            if ($timesheet_manage_permission === "specific_excluding_own" && $log_id && $user_id === $this->login_user->id) { //permission: specific_excluding_own
                app_redirect("forbidden");
            }

            //permission: own / specific
            return true;
        } else if ($members === "own_project_members" || $members === "own_project_members_excluding_own") {
            if (!$project_id) { //there has $log_id or $project_id
                $project_id = $info->project_id;
            }

            if ($this->Project_members_model->is_user_a_project_member($project_id, $user_id) || $this->Project_members_model->is_user_a_project_member($project_id, $this->login_user->id)) { //check if the login user and timelog user is both on same project
                if ($members === "own_project_members") {
                    return true;
                } else if ($this->login_user->id !== $user_id) {
                    //can't edit own but can edit other user's of project
                    //no need to check own condition here for new timelogs since it's already checked before
                    return true;
                }
            }
        }

        app_redirect("forbidden");
    }




    /* get all projects list */

    private function _get_all_projects_dropdown_list()
    {
        $projects = $this->Projects_model->get_dropdown_list(array("title"));

        $projects_dropdown = array(array("id" => "", "text" => "- " . app_lang("project") . " -"));
        foreach ($projects as $id => $title) {
            $projects_dropdown[] = array("id" => $id, "text" => $title);
        }
        return $projects_dropdown;
    }



    /*
     * admin can manage all members timesheet
     * allowed member can manage other members timesheet accroding to permission
     */

    private function _get_members_to_manage_timesheet()
    {
        $access_info = $this->get_access_info("timesheet_manage_permission");
        $access_type = $access_info->access_type;

        if (!$access_type || $access_type === "own") {
            return array($this->login_user->id); //permission: no / own
        } else if (($access_type === "specific" || $access_type === "specific_excluding_own") && count($access_info->allowed_members)) {
            return $access_info->allowed_members; //permission: specific / specific_excluding_own
        } else {
            return $access_type; //permission: all / own_project_members / own_project_members_excluding_own
        }
    }

    /* prepare dropdown list */






    /* load task list view tab */


    private function get_removed_task_status_ids($project_id = 0)
    {
        if (!$project_id) {
            return "";
        }

        $this->init_project_settings($project_id);
        return get_setting("remove_task_statuses");
    }









    private function _get_priorities_dropdown_list($priority_id = 0)
    {
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

    private function _get_project_members_dropdown_list($project_id = 0)
    {
        if ($this->login_user->user_type === "staff") {
            $assigned_to_dropdown = array(array("id" => "", "text" => "- " . app_lang("assigned_to") . " -"));
            $assigned_to_list = $this->Project_members_model->get_project_members_dropdown_list($project_id, array(), true, true)->getResult();
            foreach ($assigned_to_list as $assigned_to) {
                $assigned_to_dropdown[] = array("id" => $assigned_to->user_id, "text" => $assigned_to->member_name);
            }
        } else {
            $assigned_to_dropdown = array(
                array("id" => "", "text" => app_lang("all_tasks")),
                array("id" => $this->login_user->id, "text" => app_lang("my_tasks"))
            );
        }

        return json_encode($assigned_to_dropdown);
    }

    function all_tasks($tab = "", $filter = "")
    {
        if (!$this->can_view_tasks()) {
            app_redirect("forbidden");
        }
        $myoptions = array(
            "update_status" => 0
        );
        $list_data = $this->Tasks_model->get_details($myoptions)->getResult();
        foreach ($list_data as $data) {
            $this->save_task_status_new($data->id, 2);
        }


        $view_data['project_id'] = 0;
        $projects = $this->Tasks_model->get_my_projects_dropdown_list($this->login_user->id)->getResult();
        $projects_dropdown = array(array("id" => "", "text" => "- " . app_lang("project") . " -"));
        foreach ($projects as $project) {
            if ($project->project_id && $project->project_title) {
                $projects_dropdown[] = array("id" => $project->project_id, "text" => $project->project_title);
            }
        }

        $clients = $this->Clients_model->get_details(array("deleted" => 0/*,'client_status'=> "1"*/))->getResult();
        $clients_dropdown = array(array("id" => "", "text" => "-" . app_lang("client_name") . "-"));
        foreach ($clients as $client) {
            $clients_dropdown[] = array("id" => $client->id, "text" => $client->company_name);
        }

        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("created_by") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0));
        foreach ($assigned_to_list as $key => $value) {

            $team_members_dropdown[] = array("id" => $key, "text" => $value/*, "isSelected" => true*/);
        }

        $drivers_dropdown = array(array("id" => "", "text" => ' - ' . app_lang("driver_name") . ' - '));
        $drivers = $this->Drivers_model->get_details(array("deleted" => 0))->getResult();

        foreach ($drivers as $driver) {
            $drivers_dropdown[] = array("id" => $driver->id, "text" => $driver->driver_nm);
        }


        $cars_type = $this->Cars_type_model->get_details(array("deleted" => 0))->getResult();
        $cars_type_dropdown = array(array("id" => "", "text" => app_lang("car_type")));
        foreach ($cars_type as $car_type) {
            $cars_type_dropdown[] = array("id" => $car_type->id, "text" => $car_type->car_type);
        }
        $cities = $this->Cities_model->get_details(array("deleted" => 0))->getResult();
        $cities_dropdown = array(array("id" => "", "text" => "-" . app_lang("city_name") . "-"));
        foreach ($cities as $city) {
            $cities_dropdown[] = array("id" => $city->id, "text" => $city->city_name);
        }

        $suppliers_dropdown = array(array("id" => "", "text" => app_lang("supplier_name")));

        if (!$this->is_reserv_mang()) {


            $suppliers = $this->Suppliers_model->get_details(array("deleted" => 0))->getResult();

            foreach ($suppliers as $supplier) {
                $suppliers_dropdown[] = array("id" => $supplier->id, "text" => $supplier->name);
            }
        }



        $maintask_cls = $this->Maintask_clsifications_model->get_details(array("deleted" => 0))->getResult();
        $maintask_clsifications_dropdown = array(array("id" => "", "text" => "-" . app_lang("cls_title") . "-"));
        foreach ($maintask_cls as $maintask_clss) {
            $maintask_clsifications_dropdown[] = array("id" => $maintask_clss->id, "text" => $maintask_clss->title);
        }

        $no_invoice = $filter;

        $view_data['tab'] = $tab;
        $view_data['filter'] = $this->request->getPost('filter'); //$no_invoice;
        $view_data['main_filter'] = $this->request->getPost('filter');
        //$view_data['selected_status_id'] = $status_id;
        $view_data['suppliers_dropdown'] = json_encode($suppliers_dropdown);
        $view_data['drivers_dropdown'] = json_encode($drivers_dropdown);
        $view_data['cars_type_dropdown'] = json_encode($cars_type_dropdown);
        $view_data['maintask_clsifications_dropdown'] = json_encode($maintask_clsifications_dropdown);
        $view_data['cities_dropdown'] = json_encode($cities_dropdown);
        $view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();
        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data["custom_field_headers2"] = $this->Custom_fields_model->get_custom_field_headers_for_table("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);
        $view_data["custom_field_filters2"] = $this->Custom_fields_model->get_custom_field_filters("sub_tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['task_statuses'] = $this->Task_status_model->get_details()->getResult();
        $view_data['mang'] = $this->is_reserv_mang() ? 'yes' : 'no';
        $view_data['is_admin'] = $this->login_user->is_admin;
        $view_data['projects_dropdown'] = json_encode($projects_dropdown);


        $view_data['clients_dropdown'] = $this->is_reserv_mang() ? json_encode($clients_dropdown) : json_encode(array(array("id" => "", "text" => "-" . app_lang("client") . "-")));
        $view_data['can_create_tasks'] = $this->can_create_tasks(false);
        $view_data['can_create_subtasks'] = $this->can_create_subtasks();

        //$view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list($priority_id);

        return $this->template->rander("tasks/my_tasks", $view_data);
    }

    function all_tasks_kanban()
    {

        $projects = $this->Tasks_model->get_my_projects_dropdown_list($this->login_user->id)->getResult();
        $projects_dropdown = array(array("id" => "", "text" => "- " . app_lang("project") . " -"));
        foreach ($projects as $project) {
            if ($project->project_id && $project->project_title) {
                $projects_dropdown[] = array("id" => $project->project_id, "text" => $project->project_title);
            }
        }

        $team_members_dropdown = array(array("id" => "", "text" => "- " . app_lang("team_member") . " -"));
        $assigned_to_list = $this->Users_model->get_dropdown_list(array("first_name", "last_name"), "id", array("deleted" => 0, "user_type" => "staff"));
        foreach ($assigned_to_list as $key => $value) {

            if ($key == $this->login_user->id) {
                $team_members_dropdown[] = array("id" => $key, "text" => $value/*, "isSelected" => true*/);
            } else {
                $team_members_dropdown[] = array("id" => $key, "text" => $value);
            }
        }

        $clients = $this->Clients_model->get_details(array("deleted" => 0))->getResult();
        $clients_dropdown = array(array("id" => "", "text" => "-" . app_lang("client") . "-"));
        foreach ($clients as $client) {
            $clients_dropdown[] = array("id" => $client->id, "text" => $client->company_name);
        }

        $view_data['team_members_dropdown'] = json_encode($team_members_dropdown);
        $view_data['priorities_dropdown'] = $this->_get_priorities_dropdown_list();

        $view_data['projects_dropdown'] = json_encode($projects_dropdown);
        $view_data['clients_dropdown'] = $this->is_reserv_mang() ? json_encode($clients_dropdown) : json_encode(array(array("id" => "", "text" => "-" . app_lang("client") . "-")));

        $view_data['can_create_tasks'] = $this->can_create_tasks(false);

        $view_data['task_statuses'] = $this->Task_status_model->get_details()->getResult();
        $view_data["custom_field_filters"] = $this->Custom_fields_model->get_custom_field_filters("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        return $this->template->rander("tasks/kanban/all_tasks", $view_data);
    }

    //check user's task editting permission on changing of project
    function can_edit_task_of_the_project($project_id = 0)
    {
        validate_numeric_value($project_id);
        if ($project_id) {
            $this->init_project_permission_checker($project_id);

            if ($this->can_edit_tasks()) {
                echo json_encode(array("success" => true));
            } else {
                echo json_encode(array("success" => false));
            }
        }
    }

    function all_tasks_kanban_data()
    {


        $status = $this->request->getPost('status_id') ? implode(",", $this->request->getPost('status_id')) : "";
        $project_id = $this->request->getPost('project_id');

        //$this->init_project_permission_checker($project_id);

        $specific_user_id = $this->request->getPost('specific_user_id');

        $options = array(
            "specific_user_id" => $specific_user_id,
            "status_ids" => $status,
            "project_id" => $project_id,
            "milestone_id" => $this->request->getPost('milestone_id'),
            "priority_id" => $this->request->getPost('priority_id'),
            "deadline" => $this->request->getPost('deadline'),
            "search" => $this->request->getPost('search'),
            "client_id" => $this->request->getPost('client_id'),
            "project_status" => "open",
            "unread_status_user_id" => $this->login_user->id,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id(),
            "quick_filter" => $this->request->getPost("quick_filter"),
            "custom_field_filter" => $this->prepare_custom_field_filter_values("tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );



        $view_data["tasks"] = $this->Tasks_model->get_kanban_details($options)->getResult();
        $view_data["is_reserv_mang"] = $this->is_reserv_mang();

        $exclude_status_ids = $this->get_removed_task_status_ids($project_id);
        //$statuses = $this->Task_status_model->get_details(array("hide_from_kanban" => 0, "exclude_status_ids" => $exclude_status_ids));

        $statuses = array();
        $statuses[] = array("id" => "Open", "value" => 1, "color" => "#4a9d27");
        $statuses[] = array("id" => "Closed", "value" => 2, "color" => "#e50f16bd");

        $view_data["total_columns"] = 2;
        $view_data["columns"] = $statuses;
        $view_data['can_edit_tasks'] = $this->can_edit_tasks();
        $view_data['project_id'] = $project_id;

        return $this->template->view('tasks/kanban/kanban_view', $view_data);
    }

    /* prepare data for the projuect view's kanban tab  */





    function set_task_comments_as_read($task_id = 0)
    {
        if ($task_id) {
            validate_numeric_value($task_id);
            $this->Tasks_model->set_task_comments_as_read($task_id, $this->login_user->id);
        }
    }

    function task_view($task_id = 0)
    {
        validate_numeric_value($task_id);
        $view_type = "";

        if ($task_id) { //details page
            $view_type = "details";
        } else { //modal view
            $task_id = $this->request->getPost('id');
        }

        $model_info = $this->Tasks_model->get_details(array("id" => $task_id/*,"selected_year" => $this->session->get('selected_year')*/))->getRow();
        if (!$model_info->id) {
            show_404();
        }

        if (!$this->can_show_tasks($model_info->project_id, $task_id)) {
            app_redirect("forbidden");
        }

        $view_data = $this->_initialize_all_related_data_of_project($model_info->project_id, $model_info->collaborators, $model_info->labels);

        $view_data['show_assign_to_dropdown'] = true;
        if ($this->login_user->user_type == "client" && !get_setting("client_can_assign_tasks")) {
            $view_data['show_assign_to_dropdown'] = false;
        }

        $view_data['can_edit_tasks'] = $this->can_edit_tasks();

        $view_data['model_info'] = $model_info;

        $view_data['labels'] = make_labels_view_data($model_info->labels_list);

        $options = array("task_id" => $task_id, "login_user_id" => $this->login_user->id);
        $view_data['task_id'] = $task_id;

        $view_data['custom_fields_list'] = $this->Custom_fields_model->get_combined_details("tasks", $task_id, $this->login_user->is_admin, $this->login_user->user_type)->getResult();



        //get checklist items
        $checklist_items_array = array();
        $checklist_items = $this->Checklist_items_model->get_details(array("task_id" => $task_id))->getResult();

        $view_data["checklist_items"] = json_encode($checklist_items_array);
        $view_data["is_reserv_mang"] = $this->is_reserv_mang();

        //get sub tasks
        $sub_tasks_array = array();
        $art = $this->is_reserv_mang() ? 'reserv' : 'supply';
        $sub_tasks = $this->Sub_tasks_model->get_details(array("task_id" => $task_id, "mang" => $art/*,"selected_year" => $this->session->get('selected_year')*/))->getResult();
        foreach ($sub_tasks as $sub_task) {
            $sub_tasks_array[] = $this->_make_sub_task_row($sub_task);
        }
        $view_data["sub_tasks"] = json_encode($sub_tasks_array);

        $view_data["total_sub_tasks"] = $this->Tasks_model->count_sub_task_status(array("parent_task_id" => $task_id, "is_reserv_mang" => $this->is_reserv_mang()/*,"selected_year" => $this->session->get('selected_year')*/));
        $view_data["completed_sub_tasks"] = $this->Tasks_model->count_sub_task_status(array("parent_task_id" => $task_id, "status_id" => 4, "is_reserv_mang" => $this->is_reserv_mang()/*,"selected_year" => $this->session->get('selected_year')*/));

        $view_data["show_timer"] = get_setting("module_project_timesheet") ? true : false;

        if ($this->login_user->user_type === "client") {
            $view_data["show_timer"] = false;
        }

        //disable the start timer button if user has any timer in this project or if it's an another project and the setting is disabled
        $view_data["disable_timer"] = true;
        $user_has_any_timer = $this->Timesheets_model->user_has_any_timer($this->login_user->id);
        if ($user_has_any_timer && !get_setting("users_can_start_multiple_timers_at_a_time")) {
            $view_data["disable_timer"] = true;
        }

        $timer = $this->Timesheets_model->get_task_timer_info($task_id, $this->login_user->id)->getRow();
        if ($timer) {
            $view_data['timer_status'] = "open";
        } else {
            $view_data['timer_status'] = "";
        }

        $view_data['project_id'] = $model_info->project_id ? $model_info->project_id : 0;

        $view_data['can_create_tasks'] = $this->can_create_tasks();

        $view_data['parent_task_title'] = $this->Tasks_model->get_one($model_info->parent_task_id)->title;

        $view_data["view_type"] = $view_type;







        if ($view_type == "details") {
            return $this->template->rander('tasks/view', $view_data);
        } else {
            return $this->template->view('tasks/view', $view_data);
        }
    }





    private function _initialize_all_related_data_of_project($project_id = 0, $collaborators = "", $task_labels = "")
    {
        //we have to check if any defined project exists, then go through with the project id

        //$this->init_project_permission_checker($project_id);

        $related_data = $this->get_all_related_data_of_project($project_id, $collaborators, $task_labels);

        $view_data['suppliers_dropdown'] = $related_data["suppliers_dropdown"];

        $view_data['maintask_clsifications_dropdown'] = $related_data["maintask_clsifications_dropdown"];
        $view_data['is_closed_dropdown'] = $related_data["is_closed_dropdown"];

        $view_data['clients_dropdown'] = $related_data["clients_dropdown"];
        $view_data['label_suggestions'] = $related_data["label_suggestions"];

        $view_data["projects_dropdown"] = $this->_get_projects_dropdown();


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

    /* task add/edit modal */

    function task_modal_form()
    {
        $id = $this->request->getPost('id');
        $add_type = $this->request->getPost('add_type');
        $last_id = $this->request->getPost('last_id');

        $model_info = $this->Tasks_model->get_one($id);
        $project_id = $this->request->getPost('project_id') ? $this->request->getPost('project_id') : $model_info->project_id;

        $final_project_id = $project_id;
        if ($add_type == "multiple" && $last_id) {
            //we've to show the lastly added information if it's the operation of adding multiple tasks
            $model_info = $this->Tasks_model->get_one($last_id);

            //if we got lastly added task id, then we have to initialize all data of that in order to make dropdowns
            $final_project_id = $model_info->project_id;
        }

        $view_data = $this->_initialize_all_related_data_of_project($final_project_id, $model_info->collaborators, $model_info->labels);

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
        $view_data["suppliers_dropdown"] = $this->_get_suppliers_dropdown();

        $view_data["maintask_clsifications_dropdown"] = $this->_get_maintask_clsifications_dropdown();

        $view_data["clients_dropdown"] = $this->_get_myclients_dropdown();
        $view_data["contacts_dropdown"] = $id ? $this->get_client_contact_dropdown($model_info->client_id, $model_info->client_contact_id) : null;

        //projects dropdown is necessary on add multiple tasks
        $view_data["add_type"] = $add_type;
        $view_data['project_id'] = $project_id;
        $view_data['supplier_id'] = $model_info->supplier_id;
        $view_data['client_id'] = $model_info->client_id;
        $view_data['client_contact_id'] = $model_info->client_contact_id;
        $user_info = $this->Clients_contact_model->get_one($model_info->client_contact_id);
        $view_data['contact_name'] = $user_info->first_name . ' ' . $user_info->last_name;
        $view_data['is_admin'] = $this->login_user->is_admin ? true : false;



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


        return $this->template->view('tasks/modal_form', $view_data);
    }


    function get_client_contact_dropdown($client_id, $contact_id)
    {




        $contacts = $this->Clients_contact_model->get_details(array("client_id" => $client_id, "deleted" => 0))->getResult();
        $selected = false;
        $contacts_dropdown = array(array("id" => "", "text" => "-"));
        foreach ($contacts as $contact) {
            if ($contact->id == $contact_id) {
                $selected = true;
            } else {
                $selected = false;
            }
            $contacts_dropdown[] = array("id" => $contact->id, "text" => $contact->first_name . ' ' . $contact->last_name, "isSelected" => $selected);
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

    private function get_all_related_data_of_project($project_id, $collaborators = "", $task_labels = "")
    {



        //get milestone dropdown

        //$suppliers_model = model('App\Models\Suppliers_model');

        $suppliers = $this->Suppliers_model->get_details(array("deleted" => 0))->getResult();
        $suppliers_dropdown = array(array("id" => "", "text" => "-"));
        foreach ($suppliers as $supplier) {
            $suppliers_dropdown[] = array("id" => $supplier->id, "text" => $supplier->name);
        }

        $maintask_cls = $this->Maintask_clsifications_model->get_details(array("deleted" => 0))->getResult();
        $maintask_clsifications_dropdown = array(array("id" => "", "text" => "-"));
        foreach ($maintask_cls as $maintask_clss) {
            $maintask_clsifications_dropdown[] = array("id" => $maintask_clss->id, "text" => $maintask_clss->title);
        }

        $is_closed_dropdown = array(array("id" => "", "text" => "-"));
        $is_closed_dropdown[] = array("text" => app_lang('open'), "value" => 1);
        $is_closed_dropdown[] = array("text" => app_lang('closed'), "value" => 2);



        $clients = $this->Clients_model->get_details(array("deleted" => 0, 'client_status' => "1"))->getResult();
        $clients_dropdown = array(array("id" => "", "text" => "-"));
        foreach ($clients as $client) {
            $clients_dropdown[] = array("id" => $client->id, "text" => $client->company_name);
        }

        //get project members and collaborators dropdown
        // $show_client_contacts = $this->can_access_clients();
        //if ($this->login_user->user_type === "client" && get_setting("client_can_assign_tasks")) {
        $show_client_contacts = true;
        // }


        //get labels suggestion
        $label_suggestions = $this->make_labels_dropdown("task", $task_labels);

        return array(

            "suppliers_dropdown" => $suppliers_dropdown,
            "maintask_clsifications_dropdown" => $maintask_clsifications_dropdown,
            "is_closed_dropdown" => $is_closed_dropdown,
            "clients_dropdown" => $clients_dropdown,
            "label_suggestions" => $label_suggestions
        );
    }

    /* get all related data of selected project */

    function get_all_related_data_of_selected_project($project_id)
    {

        if ($project_id) {
            validate_numeric_value($project_id);
            $related_data = $this->get_all_related_data_of_project($project_id);

            echo json_encode(array(
                "assign_to_dropdown" => $related_data["assign_to_dropdown"],
                "collaborators_dropdown" => $related_data["collaborators_dropdown"],
                "label_suggestions" => $related_data["label_suggestions"],
            ));
        }
    }



    function save_task()
    {

        $this->validate_submitted_data(array(
            "client_id" => "numeric|required",
            "project_id" => "numeric|required"
        ));

        $project_id = $this->request->getPost('project_id');
        $id = $this->request->getPost('id');
        $add_type = $this->request->getPost('add_type');
        $supplier_id = $this->request->getPost('supplier_id');



        $client_id = $this->request->getPost('client_id');

        $client_contact_id = $this->request->getPost('contact_id');
        $now = get_current_utc_time();

        $is_clone = $this->request->getPost('is_clone');
        $main_task_id = "";
        if ($is_clone && $id) {
            $main_task_id = $id; //store main task id to get items later
            $id = ""; //on cloning task, save as new
        }

        //$this->init_project_permission_checker($project_id);

        if ($id) {
            if (!$this->can_edit_tasks()) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_create_tasks()) {
                app_redirect("forbidden");
            }
        }

        $start_date = $this->request->getPost('start_date');
        $assigned_to = $this->request->getPost('assigned_to');
        $collaborators = $this->request->getPost('collaborators');
        $recurring = $this->request->getPost('recurring') ? 1 : 0;
        $repeat_every = $this->request->getPost('repeat_every');
        $repeat_type = $this->request->getPost('repeat_type');
        $no_of_cycles = $this->request->getPost('no_of_cycles');
        $status_id = $this->request->getPost('status_id');
        $priority_id = $this->request->getPost('priority_id');
        //$this->save_task_status2($id,$this->request->getPost('status_id'));


        $client_id = $this->request->getPost('client_id');
        $client_contact_id = $this->request->getPost('contact_id');
        $cls_id = $this->request->getPost('cls_id');
        $christening_number = $this->request->getPost('christening_number');
        $invoice_number = $this->request->getPost('invoice_number');
        $ref_number = $this->request->getPost('ref_number');

        if ($invoice_number) {
            $check_invoice_number = $this->Tasks_model->check_invoice_number($invoice_number, $id);
            if ($check_invoice_number) {
                return json_encode(array("success" => false, 'message' => "Invoice number already exists."));
            }
        }
        if ($id && $invoice_number) {

            $check_completed_subtasks = $this->list_data_status($id);
            if (!$check_completed_subtasks) {
                return json_encode(array("success" => false, 'message' => "The subtasks not completed. "));
            }
        }

        $data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            "project_id" => $project_id,
            "client_id" => $client_id,
            "client_contact_id" => $client_contact_id,
            "cls_id" => $cls_id,
            "christening_number" => $christening_number,
            "invoice_number" => $invoice_number,
            "ref_number" => $ref_number,


            "status_id" => 1, //$status_id,

            "start_date" => $start_date,
            "deadline" => $this->request->getPost('deadline'),
            "recurring" => $recurring,
            "repeat_every" => $repeat_every ? $repeat_every : 0,
            "repeat_type" => $repeat_type ? $repeat_type : NULL,
            "no_of_cycles" => $no_of_cycles ? $no_of_cycles : 0,
        );

        $created_date = $this->request->getPost('created_date');

        if (!$id) {
            $data["created_by"] = $this->login_user->id;
            $created_date ? $data["created_date"] = $created_date : $data["created_date"] = $now;
        } else {
            $data["created_date"] = $created_date;
        }






        $data = clean_data($data);




        //deadline must be greater or equal to start date
        if ($data["start_date"] && $data["deadline"] && $data["deadline"] < $data["start_date"]) {
            echo json_encode(array("success" => false, 'message' => app_lang('deadline_must_be_equal_or_greater_than_start_date')));
            return false;
        }

        $copy_checklist = $this->request->getPost("copy_checklist");

        $next_recurring_date = "";



        //save status changing time for edit mode
        if ($id) {
            $task_info = $this->Tasks_model->get_one($id);
            if ($task_info->status_id !== $status_id) {
                $data["status_changed_at"] = $now;
            }

            //$this->check_sub_tasks_statuses($status_id, $id);
        }

        $save_id = $this->Tasks_model->ci_save($data, $id);
        if ($save_id) {





            $activity_log_id = get_array_value($data, "activity_log_id");

            $new_activity_log_id = save_custom_fields("tasks", $save_id, $this->login_user->is_admin, $this->login_user->user_type, $activity_log_id);

            if ($id) {
                //updated
                log_notification("project_task_updated", array("project_id" => $project_id, "task_id" => $save_id, "activity_log_id" => $new_activity_log_id ? $new_activity_log_id : $activity_log_id));
                $this->save_task_status_new($id, 2);
            } else {
                //created
                log_notification("project_task_created", array("project_id" => $project_id, "task_id" => $save_id));
            }


            echo json_encode(array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved'), "add_type" => $add_type));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    /* insert/upadate/clone a task */

    function save_task2()
    {

        $project_id = $this->request->getPost('project_id');
        $id = $this->request->getPost('id');
        $add_type = $this->request->getPost('add_type');
        $ticket_id = $this->request->getPost('ticket_id');
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
            if (!$this->can_edit_tasks()) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_create_tasks()) {
                app_redirect("forbidden");
            }
        }

        $start_date = $this->request->getPost('start_date');
        $assigned_to = $this->request->getPost('assigned_to');
        $collaborators = $this->request->getPost('collaborators');
        $recurring = $this->request->getPost('recurring') ? 1 : 0;
        $repeat_every = $this->request->getPost('repeat_every');
        $repeat_type = $this->request->getPost('repeat_type');
        $no_of_cycles = $this->request->getPost('no_of_cycles');
        //$status_id = $this->request->getPost('status_id');
        $priority_id = $this->request->getPost('priority_id');


        $client_id = $this->request->getPost('client_id');
        $client_contact_id = $this->request->getPost('contact_id');
        $city_id = $this->request->getPost('city_id');
        $christening_number = $this->request->getPost('christening_number');
        $invoice_number = $this->request->getPost('invoice_number');
        $ref_number = $this->request->getPost('ref_number');

        $data = array(
            "title" => $this->request->getPost('title'),
            "description" => $this->request->getPost('description'),
            "project_id" => $project_id,
            //"supplier_id" => $supplier_id,
            "client_id" => $client_id,
            "client_contact_id" => $client_contact_id,
            "city_id" => $city_id,
            "christening_number" => $christening_number,
            "invoice_number" => $invoice_number,
            "ref_number" => $ref_number,
            "created_by" => $this->login_user->id,
            "assigned_to" => $this->login_user->id,

            "milestone_id" => 0, //$this->request->getPost('milestone_id'),
            "points" => $this->request->getPost('points'),
            "status_id" => 1, //$status_id,
            "priority_id" => $priority_id ? $priority_id : 0,
            "labels" => $this->request->getPost('labels'),
            "start_date" => $now,
            //"deadline" => $this->request->getPost('deadline'),
            "recurring" => $recurring,
            "repeat_every" => $repeat_every ? $repeat_every : 0,
            "repeat_type" => $repeat_type ? $repeat_type : NULL,
            "no_of_cycles" => $no_of_cycles ? $no_of_cycles : 0,
            "created_date" => $this->request->getPost('created_date'),
        );



        $data = clean_data($data);



        $copy_checklist = $this->request->getPost("copy_checklist");

        $next_recurring_date = "";

        if ($recurring && get_setting("enable_recurring_option_for_tasks")) {
            //set next recurring date for recurring tasks

            if ($id) {
                //update
                if ($this->request->getPost('next_recurring_date')) { //submitted any recurring date? set it.
                    $next_recurring_date = $this->request->getPost('next_recurring_date');
                } else {
                    //re-calculate the next recurring date, if any recurring fields has changed.
                    $task_info = $this->Tasks_model->get_one($id);
                    if ($task_info->recurring != $data['recurring'] || $task_info->repeat_every != $data['repeat_every'] || $task_info->repeat_type != $data['repeat_type'] || $task_info->start_date != $data['start_date']) {
                        $recurring_start_date = $start_date ? $start_date : $task_info->created_date;
                        $next_recurring_date = add_period_to_date($recurring_start_date, $repeat_every, $repeat_type);
                    }
                }
            } else {
                //insert new
                $recurring_start_date = $start_date ? $start_date : get_array_value($data, "created_date");
                $next_recurring_date = add_period_to_date($recurring_start_date, $repeat_every, $repeat_type);
            }


            //recurring date must have to set a future date
            if ($next_recurring_date && get_today_date() >= $next_recurring_date) {
                echo json_encode(array("success" => false, 'message' => app_lang('past_recurring_date_error_message_title_for_tasks'), 'next_recurring_date_error' => app_lang('past_recurring_date_error_message'), "next_recurring_date_value" => $next_recurring_date));
                return false;
            }
        }

        //save status changing time for edit mode
        if ($id) {
            $task_info = $this->Tasks_model->get_one($id);
            if ($task_info->status_id !== $status_id) {
                $data["status_changed_at"] = $now;
            }

            //$this->check_sub_tasks_statuses($status_id, $id);
        }

        $save_id = $this->Tasks_model->ci_save($data, $id);
        if ($save_id) {

            if ($is_clone && $main_task_id) {
                //clone task checklist
                if ($copy_checklist) {
                    $checklist_items = $this->Checklist_items_model->get_all_where(array("task_id" => $main_task_id, "deleted" => 0))->getResult();
                    foreach ($checklist_items as $checklist_item) {
                        //prepare new checklist data
                        $checklist_item_data = (array) $checklist_item;
                        unset($checklist_item_data["id"]);
                        $checklist_item_data['task_id'] = $save_id;

                        $checklist_item = $this->Checklist_items_model->ci_save($checklist_item_data);
                    }
                }
            }

            //save next recurring date 
            if ($next_recurring_date) {
                $recurring_task_data = array(
                    "next_recurring_date" => $next_recurring_date
                );
                $this->Tasks_model->save_reminder_date($recurring_task_data, $save_id);
            }

            // if created from ticket then save the task id


            $activity_log_id = get_array_value($data, "activity_log_id");

            $new_activity_log_id = save_custom_fields("tasks", $save_id, $this->login_user->is_admin, $this->login_user->user_type, $activity_log_id);

            if ($id) {
                //updated
                log_notification("project_task_updated", array("project_id" => $project_id, "task_id" => $save_id, "activity_log_id" => $new_activity_log_id ? $new_activity_log_id : $activity_log_id));
            } else {
                //created
                log_notification("project_task_created", array("project_id" => $project_id, "task_id" => $save_id));

                //save uploaded files as comment
                $target_path = get_setting("timeline_file_path");
                $files_data = move_files_from_temp_dir_to_permanent_dir($target_path, "project_comment");
            }

            echo json_encode(array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved'), "add_type" => $add_type));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    //parent task can't be marked as done if there is any sub task which is not done yet
    private function check_sub_tasks_statuses($status_id = 0, $parent_task_id = 0)
    {
        if ($status_id !== "2") {
            //parent task isn't marking as done
            return true;
        }
        $sub_tasks = $this->Sub_tasks_model->check_subtask_status(array("pnt_task_id" => $parent_task_id, "deleted" => 0))->getResult();

        if (count($sub_tasks) > 0) {
            $tasks = $this->Tasks_model->get_one($parent_task_id);
            if ($tasks->christening_number && $tasks->invoice_number) {
                foreach ($sub_tasks as $sub_task) {
                    // if ($sub_task->reserv_status != 4 || $sub_task->supplier_status != 4) {
                    //     if ($sub_task->reserv_status != 5 || $sub_task->supplier_status != 5) {
                    if ($sub_task->reserv_status != 4) {
                        if ($sub_task->reserv_status != 5) {
                            //this sub task isn't done yet, show error and exit
                            echo json_encode(array("success" => false, 'message' => app_lang("parent_task_completing_error_message") . '  ' . count($sub_tasks)));
                            exit();
                        }
                    }
                }
            } else {
                echo json_encode(array("success" => false, 'message' => "تاكد من كتابة رقم التعميد ورقم الفانوره"));
                exit();
            }
        } else {
            echo json_encode(array("success" => false, 'message' => "لا يوجد مهام فرعية لهذه المهمة"));
            exit();
        }
    }

    private function check_sub_tasks_statuses3($status_id = 0, $parent_task_id = 0)
    {
        $res = true;




        $tasks = $this->Tasks_model->get_one($parent_task_id);
        if ($tasks->christening_number && $tasks->invoice_number) {
            $sub_tasks = $this->Sub_tasks_model->check_subtask_status(array("pnt_task_id" => $parent_task_id, "deleted" => 0))->getResult();
            if (count($sub_tasks) > 0) {
                foreach ($sub_tasks as $sub_task) {
                    // if ($sub_task->reserv_status != 4 || $sub_task->supplier_status != 4) {
                    //     if ($sub_task->reserv_status != 5 || $sub_task->supplier_status != 5) {
                    if ($sub_task->reserv_status != 4) {
                        if ($sub_task->reserv_status != 5) {
                            //this sub task isn't done yet, show error and exit

                            $res = false;
                        }
                    }
                }
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }
        return $res;
    }



    private function _make_sub_task_row($data, $return_type = "row")
    {

        $checkbox_class = "checkbox-blank";
        $title_class = "";

        if ($data->status_key_name == "done") {
            $checkbox_class = "checkbox-checked";
            $title_class = "text-line-through text-off";
        }

        $status = "";

        $status = js_anchor("<span class='$checkbox_class mr15 float-start'></span>", array('title' => "", "data-id" => $data->id, "data-value" => $data->status_key_name === "done" ? "1" : "3", "data-act" => "update-sub-task-status-checkbox"));


        // $title = anchor(get_uri("subtasks/index/$data->pnt_task_id"), $data->guest_nm, array("class" => "font-13", "target" => "_blank"));
        if ($this->can_show_subtasks()) {
            if ($this->is_reserv_mang()) {

                $title = modal_anchor(get_uri("subtasks/task_view"), $data->sub_task_id . ' # ' . $data->guest_nm, array("title" => app_lang('reserv_mang') . ' - ' . app_lang('task_info') . " #$data->id", "data-post-mang" => "reservmang", "data-post-id" => $data->id,  "data-modal-lg" => "2"));
            } elseif ($this->is_supply_mang()) {
                //$main_task = $this->Tasks_model->get_details(array("id" => $data->pnt_task_id))->getRow();
                $title = modal_anchor(get_uri("subtasks/task_view_supply"), $data->sub_task_id . ' # ' . $data->project_title, array("title" => app_lang('supply_mang') . ' - ' . app_lang('task_info') . " #$data->id", "data-post-mang" => "supplymang", "data-post-id" => $data->id,  "data-modal-lg" => "2"));
            } else {
                $title = $data->sub_task_id . ' # ' . $data->guest_nm;
            }
        } else {
            $title = $data->sub_task_id . ' # ' . $data->guest_nm;
        }

        $status_label = "<span class='float-end'><span class='badge mt0' style='background: $data->status_color;'>" . ($data->status_key_name ? app_lang($data->status_key_name) : $data->status_title) . "</span></span>";

        if ($return_type == "data") {
            return $status . $title . $status_label;
        }

        return "<div class='list-group-item mb5 b-a rounded sub-task-row' data-id='$data->id'>" . $status . $title . $status_label . "</div>";
    }

    /* upadate a task status */

    function save_task_status_new($id = 0, $status_id = 1)
    {
        $closed_date = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
        $data = array(
            "is_closed" => $status_id,
            "closed_by" => $this->login_user->id,
            "closed_reason" => "تم اغلاق المهمة تلقائيا لإكتمال البيانات",
            "closed_date" => $closed_date->format('Y-m-d H:i:s'),
        );

        if ($this->check_sub_tasks_statuses3($status_id, $id)) {

            $task_info = $this->Tasks_model->get_details(array("id" => $id))->getRow();

            if ($task_info->status_id !== $status_id) {
                $data["status_changed_at"] = get_current_utc_time();
            }

            $save_id = $this->Tasks_model->ci_save($data, $id);
        }
    }
    function save_task_status($id = 0, $status_id = 1)
    {
        validate_numeric_value($id);
        $closed_date = new \DateTime('now', new \DateTimeZone("Asia/Riyadh"));
        //$status_id = $this->request->getPost('value');
        $data = array(
            "is_closed" => $status_id,
            "closed_by" => $this->login_user->id,
            "closed_date" => $closed_date->format('Y-m-d H:i:s'),
        );
        //echo modal_anchor(get_uri("subtasks/task_modal_form/".$id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_multiple_tasks'), array("class" => "btn btn-outline-light", "title" => app_lang('add_multiple_tasks'), "data-post-task_id" => $id, "data-post-add_type" => "multiple"));
        if ($this->can_update_maintask_status()) {
            $this->check_sub_tasks_statuses($status_id, $id);


            $task_info = $this->Tasks_model->get_details(array("id" => $id))->getRow();



            if ($task_info->status_id !== $status_id) {
                $data["status_changed_at"] = get_current_utc_time();
            }

            $save_id = $this->Tasks_model->ci_save($data, $id);


            if ($save_id) {
                $task_info = $this->Tasks_model->get_details(array("id" => $id))->getRow();
                echo json_encode(array("success" => true, "data" => ($this->_task_row_data($save_id)), 'id' => $save_id, "message" => app_lang('record_saved')));

                log_notification("project_task_updated", array("project_id" => $task_info->project_id, "task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
            } else {
                echo json_encode(array("success" => false, app_lang('error_occurred')));
            }
        } else {
            echo json_encode(array("success" => false, 'message' => "لا يسمح لك بتعديل حالة المهمة"));
        }
    }


    function save_task_status2($id = 0)
    {
        validate_numeric_value($id);

        $status_id = $this->request->getPost('value');
        $this->check_sub_tasks_statuses($status_id, $id);

        if ($status_id == "2") {
            echo json_encode(array("success" => true, "data" => "ok", 'id' => $id, 'status_id' => $status_id));
        } else {

            $this->save_task_status($id, 1);
        }

        // echo json_encode(array("success" => true, "data" => "ok", 'id' => $id));
    }

    function post_cuses_modal_form()
    {
        $id = $this->request->getPost('id');

        $view_data['id'] = $id;
        return $this->template->view('tasks/close_modal_note', $view_data);
    }
    function save_status()
    {

        $id = $this->request->getPost('id');
        $data = array(
            "closed_reason" => $this->request->getPost('closed_reason'),
            "is_closed" => 2

        );
        if ($this->can_update_maintask_status()) {
            if ($id) {

                $save_id = $this->Tasks_model->ci_save($data, $id);
                if ($save_id) {

                    $task_info = $this->Tasks_model->get_details(array("id" => $save_id))->getRow(); //get data after save

                    $id = $this->request->getPost('id');
                    echo json_encode(array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
                } else {
                    echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
                }
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
            }
        } else {
            echo json_encode(array("success" => false, 'message' => "لا يسمح لك بتعديل حالة المهمة"));
        }
    }

    function update_task_info($id = 0, $data_field = "")
    {
        if (!$id) {
            return false;
        }

        validate_numeric_value($id);
        $task_info = $this->Tasks_model->get_one($id);
        $this->init_project_permission_checker($task_info->project_id);

        if (!$this->can_edit_tasks()) {
            app_redirect("forbidden");
        }

        $value = $this->request->getPost('value');

        //deadline must be greater or equal to start date
        if ($data_field == "deadline" && $task_info->start_date && $value < $task_info->start_date) {
            echo json_encode(array("success" => false, 'message' => app_lang('deadline_must_be_equal_or_greater_than_start_date')));
            return false;
        }

        $data = array(
            $data_field => $value
        );

        if ($data_field === "status_id" && $task_info->status_id !== $value) {
            $data["status_changed_at"] = get_current_utc_time();
        }

        if ($data_field == "status_id") {
            $this->check_sub_tasks_statuses($value, $id);
        }
        if ($data_field == "city_id") {
            $data["city_id"] = $value;
        }

        if ($data_field == "created_date") {
            $data["created_date"] = $value;
        }

        $save_id = $this->Tasks_model->ci_save($data, $id);
        if (!$save_id) {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
            return false;
        }

        $task_info = $this->Tasks_model->get_details(array("id" => $save_id))->getRow(); //get data after save

        $success_array = array("success" => true, "data" => $this->_task_row_data($save_id), 'id' => $save_id, "message" => app_lang('record_saved'));






        if ($data_field == "created_date") {
            if (is_date_exists($task_info->created_date)) {
                $date = format_to_date($task_info->created_date, false);
                $success_array["created_date"] = $date;
            }
        }



        if ($data_field == "status_id") {
            $success_array["status_color"] = $task_info->status_color;
        }



        echo json_encode($success_array);

        log_notification("project_task_updated", array("project_id" => $task_info->project_id, "task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
    }


    function save_task_status_kanpane()
    {
        if ($this->can_update_maintask_status()) {

            $id = $this->request->getPost('id');
            validate_numeric_value($id);

            //$mang = $this->request->getPost('mang');
            $status_id = $this->request->getPost('status_id');
            $sort = $this->request->getPost('sort');
            $this->check_sub_tasks_statuses($status_id, $id);

            if ($status_id == "2") {
                echo json_encode(array("success" => true, "data" => "ok", 'id' => $id, 'status_id' => $status_id));
            } else {

                //$this->save_task_status($id,$status_id,$mang);
                $this->save_task_sort_and_status($id, $status_id, $sort);
            }
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang("you_can't_update_subtask_status")));
        }
    }

    /* upadate a task status */

    function save_task_sort_and_status($id, $status_id, $sort)
    {
        //$project_id = $this->request->getPost('project_id');
        //$this->init_project_permission_checker($project_id);

        $this->validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->request->getPost('id');
        $task_info = $this->Tasks_model->get_one($id);

        if (!$this->can_update_maintask_status()) {
            app_redirect("forbidden");
        }

        //$status_id = $this->request->getPost('status_id');
        $this->check_sub_tasks_statuses($status_id, $id);
        $data = array(
            "sort" => $sort
        );

        if ($status_id) {
            $data["is_closed"] = $status_id;

            if ($task_info->status_id !== $status_id) {
                $data["status_changed_at"] = get_current_utc_time();
            }
        }

        $save_id = $this->Tasks_model->ci_save($data, $id);

        if ($save_id) {
            if ($status_id) {
                log_notification("project_task_updated", array("project_id" => $task_info->project_id, "task_id" => $save_id, "activity_log_id" => get_array_value($data, "activity_log_id")));
            }
        } else {
            echo json_encode(array("success" => false, app_lang('error_occurred')));
        }
    }

    /* delete or undo a task */

    function delete_task()
    {

        $id = $this->request->getPost('id');
        $info = $this->Tasks_model->get_one($id);

        $this->init_project_permission_checker($info->project_id);

        if ($this->can_delete_tasks()) {
            //app_redirect("forbidden");
            //}

            if ($this->Tasks_model->delete_task_and_sub_items($id)) {
                echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));

                $task_info = $this->Tasks_model->get_one($id);
                log_notification("project_task_deleted", array("project_id" => $task_info->project_id, "task_id" => $id));

                try {
                    app_hooks()->do_action("app_hook_data_delete", array(
                        "id" => $id,
                        "table" => get_db_prefix() . "tasks"
                    ));
                } catch (\Exception $ex) {
                    log_message('error', '[ERROR] {exception}', ['exception' => $ex]);
                }
            } else {
                echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
            }
        } else {
            echo json_encode(array("success" => false, 'message' => "ليس لديك صلاحية"));
        }
    }



    /* list of tasks, prepared for datatable  */

    function my_tasks_list_data()
    {

        $project_id = $this->request->getPost('project_id');


        $specific_user_id = $this->request->getPost('specific_user_id');
        $client_id = $this->request->getPost('client_id');

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
            "deleted_client" => $this->request->getPost('deleted_client'),
            "client_id" => $client_id,
            //"selected_year" => $this->session->get('selected_year'),
            "cls_id" => $this->request->getPost('cls_id'),
            "custom_fields" => $custom_fields,
            "filter" => $this->request->getPost('filter'),
            "main_filter" => $this->request->getPost('main_filter'),
            "status_ids" => $status,
            "unread_status_user_id" => $this->login_user->id,
            "show_assigned_tasks_only_user_id" => $this->show_assigned_tasks_only_user_id(),
            "quick_filter" => $quick_filter,
            "custom_field_filter" => $this->prepare_custom_field_filter_values("tasks", $this->login_user->is_admin, $this->login_user->user_type)
        );



        /*if (!$this->can_manage_all_projects()) {
            $options["project_member_id"] = $this->login_user->id; //don't show all tasks to non-admin users
        }*/

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Tasks_model->get_details($all_options);

        //by this, we can handel the server side or client side from the app table prams.
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







    function dash_my_tasks_list_data()
    {

        $project_id = $this->request->getPost('project_id');


        $specific_user_id = $this->request->getPost('specific_user_id');
        $client_id = $this->request->getPost('client_id');

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



        /*if (!$this->can_manage_all_projects()) {
            $options["project_member_id"] = $this->login_user->id; //don't show all tasks to non-admin users
        }*/

        $all_options = append_server_side_filtering_commmon_params($options);

        $result = $this->Tasks_model->get_details($all_options);

        //by this, we can handel the server side or client side from the app table prams.
        if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        $result_data = array();
        foreach ($list_data as $data) {
            $result_data[] = $this->_make_task_row2($data, $custom_fields);
        }

        $result["data"] = $result_data;

        echo json_encode($result);
    }

    /* return a row of task list table */

    private function _task_row_data($id)
    {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("tasks", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("id" => $id, "custom_fields" => $custom_fields);
        $data = $this->Tasks_model->get_details($options)->getRow();

        $this->init_project_permission_checker($data->project_id);

        return $this->_make_task_row($data, $custom_fields);
    }

    /* prepare a row of task list table */

    private function _make_task_row($data, $custom_fields)
    {

        $unread_comments_class = "";
        $icon = "";
        if (isset($data->unread) && $data->unread && $data->unread != "0") {
            $unread_comments_class = "unread-comments-of-tasks";
            $icon = "<i data-feather='message-circle' class='icon-16 ml5 unread-comments-of-tasks-icon'></i>";
        }

        //get sub tasks of this task

        $title = "";
        $main_task_id = "#" . $data->id;
        $project_title = "";
        $project_t = $data->project_title ? $data->project_title : " - ";


        /*$toggle_sub_task_icon = "";

        if ($sub_tasks) {
            $toggle_sub_task_icon = "<span class='filter-sub-task-button clickable ml5' title='" . app_lang("show_sub_tasks") . "' main-task-id= '$main_task_id'><i data-feather='filter' class='icon-16'></i></span>";

        }*/
        $project_title = '';
        //if($data->client_status==1){
        if ($this->can_manage_all_projects()) {
            $cn = $data->client_id ? ($data->client_status == 2 ? "<span style='color:red'>" . $data->company_name . ' - (' .  app_lang("disabled") . ")</span>" : $data->company_name) : 'تحديد عميل';
            $htm = '<a class=" p-0" href="javascript:void(0);"  id="historyList" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $data->company_name . '
            </a>
            <div class="dropdown-menu " aria-labelledby="historyList" style="">
            ' . anchor(get_uri("subtasks/index/" . $data->id), app_lang("reserv_mang"), array("class" => 'dropdown-item')) . '
            ' . anchor(get_uri("subtasks/supply_mang/" . $data->id), app_lang("supply_mang"), array("class" => 'dropdown-item')) . '
            ' . modal_anchor(get_uri("tasks/task_view"), app_lang('task_info'), array("class" => "dropdown-item", "title" => app_lang('task_info') . " #$data->id", "data-post-id" => $data->id)) . '
            
          </div>';

            $title .= $data->client_status == 1 ? $htm : $cn;
        } elseif ($this->is_reserv_mang()) {
            $cn = $data->client_id ? ($data->client_status == 2 ? "<span style='color:red'>" . $data->company_name . ' - (' .  app_lang("disabled") . ")</span>" : $data->company_name) : 'تحديد عميل';
            $htm = '<a class=" p-0" href="javascript:void(0);"  id="historyList" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $data->company_name . '
            </a>
            <div class="dropdown-menu " aria-labelledby="historyList" style="">
            ' . anchor(get_uri("subtasks/index/" . $data->id), app_lang("reserv_mang"), array("class" => 'dropdown-item')) . '
            ' . modal_anchor(get_uri("tasks/task_view"), app_lang('task_info'), array("class" => "dropdown-item", "title" => app_lang('task_info') . " #$data->id", "data-post-id" => $data->id)) . '
            
          </div>';

            //$title .=$data->client_status==1?anchor(get_uri("subtasks/index/".$data->id), $cn):$cn;
            $title .= $data->client_status == 1 ? $htm : $cn;
        } elseif ($this->is_supply_mang()) {
            $cn = $data->client_id ? ($data->client_status == 2 ? "<span style='color:red'>" . $data->project_title . ' - (' .  app_lang("disabled") . ")</span>" : $data->project_title) : 'تحديد عميل';
            //$project_title .=$data->client_status==1?anchor(get_uri("subtasks/supply_mang/".$data->id), $cn):$cn;
            $htm = '<a class=" p-0" href="javascript:void(0);"  id="historyList" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . $data->project_title . '
            </a>
            <div class="dropdown-menu " aria-labelledby="historyList" style="">
            ' . anchor(get_uri("subtasks/supply_mang/" . $data->id), app_lang("supply_mang"), array("class" => 'dropdown-item')) . '
            ' . modal_anchor(get_uri("tasks/task_view"), app_lang('task_info'), array("class" => "dropdown-item", "title" => app_lang('task_info') . " #$data->id", "data-post-id" => $data->id)) . '
            
          </div>';
            $project_title .= $data->client_status == 1 ? $htm : $cn;
            //$project_title .=anchor($data->client_status==1?get_uri("subtasks/supply_mang/".$data->id):'', $data->client_id? ($data->client_status==2?"<span style='color:red'>".$data->project_title.' - ('.  app_lang("disabled").")</span>":$data->project_title):'تحديد مشروع');

        } else {
            $title .= $data->client_id ? $data->company_name : 'تحديد عميل';
        }




        if ($data->sub_task_count) {
            //this is a sub task
            if ($this->is_reserv_mang()) {

                $title .= "<span class='badge badge-light  mt0' title='" . app_lang('sub_task') . "'>" . $data->sub_task_count . "</span>";
                $project_title = $data->project_title ? $data->project_title : " - ";
            } else {
                $project_title .= "<span class='badge badge-light  mt0' title='" . app_lang('sub_task') . "'>" . $data->sub_task_count . "</span>";
            }
        } else {
            if ($this->is_reserv_mang()) {
                $title .= "<span class='badge badge-light  mt0' title='" . app_lang('sub_task') . "'>0</span>";
                $project_title = $data->project_title ? $data->project_title : " - ";
            } else {
                $project_title .= "<span class='badge badge-light  mt0' title='" . app_lang('sub_task') . "'>0</span>";
            }
        }



        //$project_title =$data->project_title ? $data->project_title:" - ";



        $created_by = "-";

        if ($data->created_by) {
            $image_url = get_avatar($data->assigned_to_avatar);
            $other_img = get_avatar("_file613bc2b05fbe1-avatar.jpg");
            $r = "this.src='$other_img';";
            $r2 = 'onerror="this.onerror=null;' . $r . '"';

            $assigned_to_user = "<span class='avatar avatar-xs mr10'><img src='$image_url' $r2 style='max-width: 30px;' alt='...'></span> $data->assigned_to_user";
            $created_by = get_team_member_profile_link($data->created_by, $data->assigned_to_user);

            //  $created_by = get_team_member_profile_link($data->created_by, $assigned_to_user);

        }


        $checkbox_class = "checkbox-checked";



        $created_date = "-";
        if (is_date_exists($data->created_date)) {
            $created_date = format_to_date($data->created_date, false);
        }

        $options = "";
        if ($this->can_edit_tasks()) {
            $options .= modal_anchor(get_uri("tasks/task_view"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('task_info') . " #$data->id", "data-post-id" => $data->id));
        }
        /*if ($this->can_delete_tasks()) {
            $options .= js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_task'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("projects/delete_task"), "data-action" => "delete-confirmation"));
        }*/


        $client_name = $data->client_id != 0 ? anchor(get_uri("clients/view/" . $data->client_id), $data->company_name) : 'لم يتم التحديد';
        $status_color = $data->is_closed == 1 ? '#4a9d27' : '#e50f16bd';
        $check_status = js_anchor("<span class=' ml10 float-end'>" . $data->id . "</span>", array('title' => "", "class" => "js-task", "data-id" => $data->id));
        $check_status2 = "<label class=' mr15 float-start'>" . $data->id . "</label>";

        if ($this->is_reserv_mang()) {
            $row_data = array(
                $status_color,
                $check_status,
                $title,
                $data->clients_contact_name ? $data->clients_contact_name : '___',
                $project_title,
                $data->christening_number ? $data->christening_number : '___',
                $data->invoice_number ? $data->invoice_number : '___',
                $data->cls_title,
                $data->description,
                $data->created_date,

                $created_by,
                $data->cls_color,
                //$data->is_closed==0 ? 'Open':'Closed'
            );
        } else {
            $row_data = array(
                $status_color,
                $check_status,
                $project_title,
                '*****',
                '*****',
                '*****',
                $data->cls_title,
                '',
                $data->created_date,
                $data->cls_color,

                $created_by,
                //$data->is_closed==0 ? 'Open':'Closed'
            );
        }


        $row_data[] = js_anchor($data->is_closed == 1 ? app_lang('open') : app_lang('closed'), array("style" => "background-color: $status_color", "class" => "badge", "data-id" => $data->id, "data-value" => $data->is_closed, "data-act" => "update-mytask-status"));

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }

        if ($data->client_status == 2) {
            $row_data[] = modal_anchor('', "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('task_info') . " #$data->id", "data-post-id" => $data->id))
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("tasks/delete_task"), "data-action" => "delete-confirmation"));
        } else {
            $row_data[] = modal_anchor(get_uri("tasks/task_view"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('task_info') . " #$data->id", "data-post-id" => $data->id)) . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete_client'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("tasks/delete_task"), "data-action" => "delete-confirmation"));
        }






        $row_data[] = $options;

        return $row_data;
    }






    private function _make_task_row2($data, $custom_fields)
    {
        $unread_comments_class = "";
        $icon = "";
        if (isset($data->unread) && $data->unread && $data->unread != "0") {
            $unread_comments_class = "unread-comments-of-tasks";
            $icon = "<i data-feather='message-circle' class='icon-16 ml5 unread-comments-of-tasks-icon'></i>";
        }

        //get sub tasks of this task

        $title = "";
        $main_task_id = "#" . $data->id;
        $project_title = "";
        $project_t = $data->project_title ? $data->project_title : " - ";


        if ($this->is_reserv_mang()) {
            $cn = $data->client_id ? ($data->client_status == 2 ? "<span style='color:red'>" . $project_t . ' - (' .  app_lang("disabled") . ")</span>" : $project_t) : 'تحديد عميل';

            $project_title .= $data->client_status == 1 ? anchor(get_uri("subtasks/index/" . $data->id), $cn) : $cn;
            //$project_title .=anchor(get_uri("subtasks/index/".$data->id), $project_t);
        } elseif ($this->is_supply_mang()) {
            $cn = $data->client_id ? ($data->client_status == 2 ? "<span style='color:red'>" . $project_t . ' - (' .  app_lang("disabled") . ")</span>" : $project_t) : 'تحديد عميل';
            $project_title .= $data->client_status == 1 ? anchor(get_uri("subtasks/supply_mang/" . $data->id), $cn) : $cn;
        } else {
            $project_title .= $project_t;
        }




        if ($data->sub_task_count) {
            //this is a sub task
            $project_title .= "<span class='sub-task-icon mr5' style='border-radius: 25%; padding: 1px 3px 1px 5px;' title='" . app_lang("sub_task") . "'> " . $data->sub_task_count . " </span>";
        } else {
            $project_title .= "<span class='sub-task-icon mr5' style='border-radius: 25%; padding: 1px 3px 1px 5px;' title='" . app_lang("sub_task") . "'> 0 </span>";
        }








        $checkbox_class = "checkbox-checked";



        $start_date = "-";
        if (is_date_exists($data->start_date)) {
            $start_date = format_to_date($data->start_date, false);
        }

        $options = "";
        if ($this->can_edit_tasks()) {
            $options .= modal_anchor(get_uri("tasks/task_view"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('task_info') . " #$data->id", "data-post-id" => $data->id));
        }


        //$client_name = $data->client_id!=0?anchor(get_uri("clients/view/" . $data->client_id), $data->company_name):'لم يتم التحديد';
        $status_color = $data->is_closed == 1 ? '#4a9d27' : '#e50f16bd';
        $check_status = js_anchor("<span class=' mr15 float-start'></span>", array('title' => "", "class" => "js-task", "data-id" => $data->id,  "data-act" => "update-task-status-checkbox")) . $data->id;
        $row_data = array(
            $status_color,
            $check_status,
            $project_title,
            $data->created_date,

            //$data->is_closed==0 ? 'Open':'Closed'
        );


        $row_data[] = js_anchor($data->is_closed == 1 ? app_lang('open') : app_lang('closed'), array("style" => "background-color: $status_color", "class" => "badge", "data-id" => $data->id, "data-value" => $data->is_closed, "data-act" => "update-mytask-status"));

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->template->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id));
        }





        $row_data[] = $options;

        return $row_data;
    }













    /* upload a post file */

    function upload_file()
    {
        upload_file_to_temp();
    }

    /* check valid file for project */

    function validate_project_file()
    {
        return validate_post_file($this->request->getPost("file_name"));
    }

    /* delete a file */



    /* download a file */

    function download_file($id)
    {

        $file_info = $this->Project_files_model->get_one($id);

        $this->init_project_permission_checker($file_info->project_id);
        if (!$this->can_view_files()) {
            app_redirect("forbidden");
        }

        //serilize the path
        $file_data = serialize(array(array("file_name" => $file_info->project_id . "/" . $file_info->file_name, "file_id" => $file_info->file_id, "service_type" => $file_info->service_type)));

        //delete the file
        return $this->download_app_files(get_setting("project_file_path"), $file_data);
    }

    /* download multiple files as zip */

    function download_multiple_files($files_ids = "")
    {

        if ($files_ids) {


            $files_ids_array = explode('-', $files_ids);

            $files = $this->Project_files_model->get_files($files_ids_array);

            if ($files) {
                $file_path_array = array();
                $project_id = 0;

                foreach ($files->getResult() as $file_info) {

                    //we have to check the permission for each file
                    //initialize the permission check only if the project id is different

                    if ($project_id != $file_info->project_id) {
                        $this->init_project_permission_checker($file_info->project_id);
                        $project_id = $file_info->project_id;
                    }

                    if (!$this->can_view_files()) {
                        app_redirect("forbidden");
                    }

                    $file_path_array[] = array("file_name" => $file_info->project_id . "/" . $file_info->file_name, "file_id" => $file_info->file_id, "service_type" => $file_info->service_type);
                }

                $serialized_file_data = serialize($file_path_array);

                return $this->download_app_files(get_setting("project_file_path"), $serialized_file_data);
            }
        }
    }

    /* batch update modal form */

    function batch_update_modal_form($task_ids = "")
    {
        $project_id = $this->request->getPost("project_id");

        if ($task_ids && $project_id) {
            $view_data = $this->_initialize_all_related_data_of_project($project_id);
            $view_data["task_ids"] = clean_data($task_ids);
            $view_data["project_id"] = $project_id;

            return $this->template->view("tasks/batch_update/modal_form", $view_data);
        } else {
            show_404();
        }
    }

    /* save batch tasks */

    function save_batch_update()
    {

        $this->validate_submitted_data(array(
            "project_id" => "required|numeric"
        ));

        $project_id = $this->request->getPost('project_id');

        if (!$this->can_edit_tasks()) {
            app_redirect("forbidden");
        }

        $batch_fields = $this->request->getPost("batch_fields");
        if ($batch_fields) {
            $fields_array = explode('-', $batch_fields);

            $data = array();
            foreach ($fields_array as $field) {
                if ($field != "project_id") {
                    $data[$field] = $this->request->getPost($field);
                }
            }

            $data = clean_data($data);

            $task_ids = $this->request->getPost("task_ids");
            if ($task_ids) {
                $tasks_ids_array = explode('-', $task_ids);
                $now = get_current_utc_time();

                foreach ($tasks_ids_array as $id) {
                    unset($data["activity_log_id"]);
                    unset($data["status_changed_at"]);

                    //check user's permission on this task's project
                    $task_info = $this->Tasks_model->get_one($id);
                    $this->init_project_permission_checker($task_info->project_id);
                    if (!$this->can_edit_tasks()) {
                        app_redirect("forbidden");
                    }

                    if (array_key_exists("status_id", $data) && $task_info->status_id !== get_array_value($data, "status_id")) {
                        $data["status_changed_at"] = $now;
                    }

                    $save_id = $this->Tasks_model->ci_save($data, $id);

                    if ($save_id) {
                        //we don't send notification if the task is changing on the same position
                        $activity_log_id = get_array_value($data, "activity_log_id");
                        if ($activity_log_id) {
                            log_notification("project_task_updated", array("project_id" => $project_id, "task_id" => $save_id, "activity_log_id" => $activity_log_id));
                        }
                    }
                }

                echo json_encode(array("success" => true, 'message' => app_lang('record_saved')));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => app_lang('no_field_has_selected')));
            return false;
        }
    }

    /* download files by zip */



    /* list of files, prepared for datatable  */








    //save project status
    function change_status($project_id, $status)
    {
        if ($project_id && $this->can_create_projects() && ($status == "completed" || $status == "hold" || $status == "canceled" || $status == "open")) {
            validate_numeric_value($project_id);
            $status_data = array("status" => $status);
            $save_id = $this->Projects_model->ci_save($status_data, $project_id);

            //send notification
            if ($status == "completed") {
                log_notification("project_completed", array("project_id" => $save_id));
            }
        }
    }






    //get clients dropdown
    private function _get_clients_dropdown()
    {
        $clients_dropdown = array(array("id" => "", "text" => "- " . app_lang("client") . " -"));
        $clients = $this->Clients_model->get_dropdown_list(array("company_name"), "id", array("is_lead" => 0));
        foreach ($clients as $key => $value) {
            $clients_dropdown[] = array("id" => $key, "text" => $value);
        }
        return $clients_dropdown;
    }









    /* delete multiple files */






    function validate_import_tasks_file()
    {
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



    private function _get_existing_custom_field_id($title = "")
    {
        if (!$title) {
            return false;
        }

        $custom_field_data = array(
            "title" => $title,
            "related_to" => "tasks"
        );

        $existing = $this->Custom_fields_model->get_one_where(array_merge($custom_field_data, array("deleted" => 0)));
        if ($existing->id) {
            return $existing->id;
        }
    }

    private function _prepare_headers_for_submit($headers_row, $headers)
    {
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



    private function _save_custom_fields_of_task($task_id, $custom_field_values_array)
    {
        if (!$custom_field_values_array) {
            return false;
        }

        foreach ($custom_field_values_array as $key => $custom_field_value) {
            $field_value_data = array(
                "related_to_type" => "tasks",
                "related_to_id" => $task_id,
                "custom_field_id" => $key,
                "value" => $custom_field_value
            );

            $field_value_data = clean_data($field_value_data);

            $this->Custom_field_values_model->ci_save($field_value_data);
        }
    }

    private function _get_project_id($project = "")
    {
        if (!$project) {
            return false;
        }

        $existing_project = $this->Projects_model->get_one_where(array("title" => $project, "deleted" => 0));
        if ($existing_project->id) {
            //project exists, check permission to access this project
            $this->init_project_permission_checker($existing_project->id);
            if ($this->can_create_tasks()) {
                return $existing_project->id;
            }
        } else {
            return false;
        }
    }



    private function _get_assigned_to_id($assigned_to = "")
    {
        $assigned_to = trim($assigned_to);
        if (!$assigned_to) {
            return false;
        }

        $existing_user = $this->Users_model->get_user_from_full_name($assigned_to);
        if ($existing_user) {
            return $existing_user->id;
        } else {
            return false;
        }
    }

    private function _check_task_points($points = "")
    {
        if (!$points) {
            return false;
        }

        if (get_setting("task_point_range") >= $points) {
            return $points;
        } else {
            return false;
        }
    }

    private function _get_collaborators_ids($collaborators_data)
    {
        $explode_collaborators = explode(", ", $collaborators_data);
        if (!($explode_collaborators && count($explode_collaborators))) {
            return false;
        }

        $groups_ids = "";

        foreach ($explode_collaborators as $collaborator) {
            $collaborator = trim($collaborator);

            $existing_user = $this->Users_model->get_user_from_full_name($collaborator);
            if ($existing_user) {
                //user exists, add the user id to collaborator ids
                if ($groups_ids) {
                    $groups_ids .= ",";
                }
                $groups_ids .= $existing_user->id;
            } else {
                //flag error that anyone of the list isn't exists
                return false;
            }
        }

        if ($groups_ids) {
            return $groups_ids;
        }
    }

    private function _get_status_id($status = "")
    {
        if (!$status) {
            return false;
        }

        $existing_status = $this->Task_status_model->get_one_where(array("title" => $status, "deleted" => 0));
        if ($existing_status->id) {
            //status exists, add the status id
            return $existing_status->id;
        } else {
            return false;
        }
    }

    private function _get_label_ids($labels = "")
    {
        $explode_labels = explode(", ", $labels);
        if (!($explode_labels && count($explode_labels))) {
            return false;
        }

        $labels_ids = "";

        foreach ($explode_labels as $label) {
            $label = trim($label);
            $labels_id = "";

            $existing_label = $this->Labels_model->get_one_where(array("title" => $label, "context" => "task", "deleted" => 0));
            if ($existing_label->id) {
                //existing label, add the labels id
                $labels_id = $existing_label->id;
            } else {
                //not exists, create new
                $label_data = array("title" => $label, "context" => "task", "color" => "#83c340");
                $labels_id = $this->Labels_model->ci_save($label_data);
            }

            if ($labels_ids) {
                $labels_ids .= ",";
            }
            $labels_ids .= $labels_id;
        }

        return $labels_ids;
    }

    private function _get_allowed_headers()
    {
        return array(
            "title",
            "description",
            "project",
            "points",
            "milestone",
            "assigned_to",
            "collaborators",
            "status",
            "labels",
            "start_date",
            "deadline"
        );
    }

    private function _store_headers_position($headers_row = array())
    {
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

    function validate_import_tasks_file_data($check_on_submit = false)
    {
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

    private function _row_data_validation_and_get_error_message($key, $data, $headers = array())
    {
        $allowed_headers = $this->_get_allowed_headers();
        $header_value = get_array_value($allowed_headers, $key);

        //required fields
        if (($header_value == "title" || $header_value == "project" || $header_value == "points" || $header_value == "status") && !$data) {
            return sprintf(app_lang("import_error_field_required"), app_lang($header_value));
        }

        //check dates
        if (($header_value == "start_date" || $header_value == "end_date") && !$this->_check_valid_date($data)) {
            return app_lang("import_date_error_message");
        }

        //existance required on this fields
        if ($data && (
            ($header_value == "project" && !$this->_get_project_id($data)) ||
            ($header_value == "status" && !$this->_get_status_id($data)) ||
            ($header_value == "assigned_to" && !$this->_get_assigned_to_id($data)) ||
            ($header_value == "collaborators" && !$this->_get_collaborators_ids($data))
        )) {
            if ($header_value == "assigned_to" || $header_value == "collaborators") {
                return sprintf(app_lang("import_not_exists_error_message"), app_lang("user"));
            } else {
                return sprintf(app_lang("import_not_exists_error_message"), app_lang($header_value));
            }
        }

        //valid points is required
        if ($header_value == "points" && !$this->_check_task_points($data)) {
            return app_lang("import_task_points_error_message");
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
    public function list_data_status($task_id)
    {
        // $task_id = "377";
        $options = array(
            "task_id" => $task_id,
            "mang" => "reserv",
            "is_admin" => $this->login_user->is_admin ? "yes" : "no",
        );

        $all_options = append_server_side_filtering_commmon_params($options);
        $result = $this->Sub_tasks_model->get_details_new($all_options);

        if (get_array_value($all_options, "server_side")) {
            $list_data = get_array_value($result, "data");
        } else {
            $list_data = $result->getResult();
            $result = array();
        }

        // Check each item in the data
        foreach ($list_data as $item) {
            // Check conditions
            if (
                (empty($item->act_out_date) || $item->act_out_date == '0000-00-00') ||
                (empty($item->sales_act_return_date) || $item->sales_act_return_date == '0000-00-00')||
                (empty($item->act_out_time) || $item->act_out_time == '00:00:01') ||
                // (empty($item->car_type_id) || $item->car_type_id == '0') ||
                (empty($item->inv_day_count) || $item->inv_day_count =='0')
            ) {
                // If any condition is met, return false and stop further processing
                // echo json_encode($list_data);
                // echo json_encode($list_data );
                // echo json_encode(false);
                return false;
            }
        }

        // If none of the items meet the conditions, return true
        // echo json_encode($list_data);

        // echo json_encode($list_data );
        // echo json_encode(true);
        return true;
    }
}

/* End of file projects.php */
/* Location: ./app/controllers/projects.php */