<?php

namespace App\Controllers;

class Cities extends Security_Controller {

    private $Company_model;

    function __construct() {
        parent::__construct();
        //$this->access_only_admin_or_settings_admin();

    }

    function index() {
        $view_data['can_add_city'] = $this->can_add_city();
        return $this->template->rander("cities/index", $view_data);
    }

    function modal_form() {

       
        $this->validate_submitted_data(array(
            "id" => "numeric"
        ));

        $id=$this->request->getPost('id');

        if ($id) {
            if (!$this->can_edit_city()) {
                app_redirect("forbidden");
            }
        } else {
            if (!$this->can_add_city()) {
                app_redirect("forbidden");
            }
        }

        $view_data['model_info'] = $this->Cities_model->get_one($id);
        return $this->template->view('cities/modal_form', $view_data);
    }

    function save() {
       
        $data = array(
            "city_name" => $this->request->getPost('city_name'),
            
        );

        $id = $this->request->getPost('id');

        $data = clean_data($data);

        $save_id = $this->Cities_model->ci_save($data, $id);

        if ($save_id) {
            
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'message' => app_lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('error_occurred')));
        }
    }

    function delete() {

        if (!$this->can_delete_city() && !$this->can_edit_city()) {
            app_redirect("forbidden");
        }
        $this->validate_submitted_data(array(
            "id" => "numeric|required"
        ));

        $id = $this->request->getPost('id');


        

        if ($this->Cities_model->my_delete($id)) {
            echo json_encode(array("success" => true, 'message' => app_lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => app_lang('record_cannot_be_deleted')));
        }
    }

    function list_data() {
        $list_data = $this->Cities_model->get_details()->getResult();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _row_data($id) {
        $options = array("id" => $id);
        $data = $this->Cities_model->get_details($options)->getRow();
        return $this->_make_row($data);
    }

    private function _make_row($data) {
       if($this->can_delete_city()){
        $row_data = $this->can_edit_city()?modal_anchor(get_uri("cities/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit'), "data-post-id" => $data->id)). js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("cities/delete"), "data-action" => "delete-confirmation")):"<i data-feather='edit' class='icon-16'></i>"
                . js_anchor("<i data-feather='x' class='icon-16'></i>", array('title' => app_lang('delete'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("cities/delete"), "data-action" => "delete-confirmation"));
           }else{
                $row_data[] = $this->can_edit_city()?modal_anchor(get_uri("cities/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang('edit'), "data-post-id" => $data->id)):"<i data-feather='edit' class='icon-16'></i>";

            }

        return array(
            '',
            $data->city_name,
           
            $row_data
            
        );
    }

}

/* End of file company.php */
/* Location: ./app/controllers/company.php */