<?php

namespace App\Controllers;

class Drivers extends Security_Controller {


    function __construct() {
        parent::__construct();
        if (!$this->drivers_permission()) {
            app_redirect("forbidden");
        }

    }

    function index() {
        $view_data['can_add_city'] = $this->can_add_driver();
        return $this->template->rander("drivers/index", $view_data);
    }

    function modal_form() {

        
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id=$this->request->getPost('id');

        /*if ($id) {
            if (!$this->can_edit_city()) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_add_city()) {
                app_redirect("forbidden");
            }
        }*/

        $view_data['model_info'] = $this->Drivers_model->get_one($id);
        return $this->template->view('drivers/modal_form', $view_data);
    }

    

    function list_data() {
        $list_data = $this->Drivers_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Drivers_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    private function _make_row($data) {

         $unread_comments_class = "unread-comments-of-tasks";
        $checkbox_class = "";
            $icon = "<i data-feather='message-circle' class='icon-16  unread-comments-of-tasks-icon'></i>";
           

            
            $check_status = js_anchor("<span class='$checkbox_class  float-start' ></span>", array('title' => "", "class" => "js-task", "data-id" => $data->id,  "data-act" => "update-task-status-checkbox")) ."<span class=' float-end'>".$data->id."</span>";
       if($this->can_delete_driver()){
        $row_data = modal_anchor(get_uri("drivers/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit'), "data-post-id" => $data->id)). js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("drivers/delete"), "data-action" => "delete-confirmation"));
           }else{
                $row_data[] = $this->can_edit_driver()?modal_anchor(get_uri("drivers/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit'), "data-post-id" => $data->id)):"<i data-feather='edit' class='icon-16'></i>";

            }
            $status=$data->status==1? app_lang("open"):app_lang('closed');

        return array(
           $check_status,
            $data->driver_nm,
            $data->email,
            $data->phone,
            $status,
           
            $row_data
            
        );
    }


    function save() {
       
        $data = array(
            "driver_nm" => $this->request->getPost('driver_nm'),
            "email" => $this->request->getPost('email'),
            "phone" => $this->request->getPost('phone'),
            "status" => $this->request->getPost('status'),
            
            
        );

        $id = $this->request->getPost('id');

        $data = clean_data($data);

        $save_id = $this->Drivers_model->ci_save($data, $id);

        if ($save_id) {
            
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete() {
        /*if (!$this->can_delete_city() && !$this->can_edit_city()) {
            app_redirect("forbidden");
        }*/
        if (!$this->can_delete_driver()) {
            app_redirect("forbidden");
        };

        $this->validate_submitted_data(
            array(
                "id" => "numeric|required"
            )
        );


        $id = $this->request->getPost('id');


        

        if ($this->Drivers_model->my_delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

}

/* End of file company.php */
/* Location: ./app/controllers/company.php */