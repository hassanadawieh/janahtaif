<div class="tab-content">
    <?php echo form_open(get_uri("roles/save_permissions"), array("id" => "permissions-form", "class" => "general-form dashed-row", "role" => "form")); ?>
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
    <div class="card">
        <div class="card-header">
            <h4><?php echo app_lang('permissions') . ": " . $model_info->title; ?></h4>
        </div>
        <div class="card-body">

            <ul class="permission-list">
                <li>
                    <span data-feather="key" class="icon-14 ml-20"></span>
                    <h5><?php echo app_lang("set_project_permissions"); ?>:</h5>
                   

                    <div id="dd" class="form-group ">
                        <div class="row">
                        

                        <div class="col-md-12">
                            <?php
                            echo form_checkbox("supply_mang", "1", $supply_mang ? true : false, "id='supply_mang' class='manage_project_section form-check-input'");
                            ?>
                            <label for="supply_mang"><?php echo app_lang("supply_mang"); ?></label>
                        </div>



                        <div class="col-md-12">
                            <?php
                            echo form_checkbox("reserv_mang", "1", $reserv_mang ? true : false, "id='reserv_mang' class='manage_project_section form-check-input'");
                            ?>
                            <label for="reserv_mang"><?php echo app_lang("reserv_mang"); ?></label>
                        </div>

                        <div class="col-md-12">
                            <?php
                            echo form_checkbox("can_manage_all_projects", "1", $can_manage_all_projects ? true : false, "id='can_manage_all_projects' class='manage_project_section form-check-input'");
                            ?>
                            <label for="can_manage_all_projects"><?php echo app_lang("can_manage_all_projects"); ?></label>
                        </div>

                        <div id="all_tasks_details_area" class="row <?php echo $can_manage_all_projects ? "hide" : ""; ?>">
                        <div class="col-md-6" style="margin-top:10px;">
                            <?php
                            echo form_checkbox("can_create_subtasks", "1", $can_create_subtasks ? true : false, "id='can_create_subtasks' class='manage_project_section form-check-input'");
                            ?>
                            <label for="can_create_subtasks"><?php echo app_lang("can_create_subtasks"); ?></label>
                        </div>

                       <div class="col-md-6" style="margin-top:10px;">
                            <?php
                            echo form_checkbox("can_show_subtasks", "1", $can_show_subtasks ? true : false, "id='can_show_subtasks' class='form-check-input'");
                            ?>
                            <label for="can_show_subtasks"><?php echo app_lang("can_show_subtasks"); ?></label>
                        </div>

                        <div class="col-md-6 <?php echo $can_edit_only_own_subtasks ?  'hide' : '' ?>" id="can_edit_subtasksD">
                            <?php
                            echo form_checkbox("can_edit_subtasks", "1", $can_edit_subtasks ? true : false, "id='can_edit_subtasks' class='form-check-input'");
                            ?>
                            <label for="can_edit_subtasks"><?php echo app_lang("can_edit_subtasks"); ?></label>
                        </div>

                        <?php if($reserv_mang){ ?>
                        <div class="col-md-12">
                            <?php
                            echo form_checkbox("can_edit_only_own_subtasks", "1", $can_edit_only_own_subtasks ? true : false, "id='can_edit_only_own_subtasks' class='form-check-input'");
                            ?>
                            <label for="can_edit_only_own_subtasks" style="color: var(--bs-orange);"><?php echo app_lang("can_edit_only_own_subtasks"); ?></label>
                        </div>
                    <?php } ?>

                    <div class="col-md-12">
                            <?php
                            echo form_checkbox("can_edit_subtasks_after_closed", "1", $can_edit_subtasks_after_closed ? true : false, "id='can_edit_subtasks_after_closed' class='form-check-input'");
                            ?>
                            <label for="can_edit_subtasks_after_closed" style="color: var(--bs-orange);"><?php echo app_lang("can_edit_subtasks_after_closed"); ?></label>
                        </div>

                        <div class="col-md-12">
                            <?php
                            echo form_checkbox("can_edit_subtasks_after_review", "1", $can_edit_subtasks_after_review ? true : false, "id='can_edit_subtasks_after_review' class='form-check-input'");
                            ?>
                            <label for="can_edit_subtasks_after_review" style="color: var(--bs-orange);"><?php echo app_lang("can_edit_subtasks_after_review"); ?></label>
                        </div>


                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_delete_subtasks", "1", $can_delete_subtasks ? true : false, "id='can_delete_subtasks' class='form-check-input'");
                            ?>
                            <label for="can_delete_subtasks"><?php echo app_lang("can_delete_subtasks"); ?></label>
                        </div>

                        


                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_update_subtask_status", "1", $can_update_subtask_status ? true : false, "id='can_update_subtask_status' class='form-check-input'");
                            ?>
                            <label for="can_update_subtask_status"><?php echo app_lang("can_update_subtask_status"); ?></label>
                        </div>


                         <div class="col-md-6" style="margin-top:10px;">
                            <?php
                            echo form_checkbox("can_create_tasks", "1", $can_create_tasks ? true : false, "id='can_create_tasks' class='manage_project_section form-check-input'");
                            ?>
                            <label for="can_create_tasks"><?php echo app_lang("can_create_tasks"); ?></label>
                        </div>

                        <div class="col-md-6" style="margin-top:10px;">
                            <?php
                            echo form_checkbox("can_show_tasks", "1", $can_show_tasks ? true : false, "id='can_show_tasks' class='form-check-input'");
                            ?>
                            <label for="can_show_tasks"><?php echo app_lang("can_show_tasks"); ?></label>
                        </div>

                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_edit_tasks", "1", $can_edit_tasks ? true : false, "id='can_edit_tasks' class='form-check-input'");
                            ?>
                            <label for="can_edit_tasks"><?php echo app_lang("can_edit_tasks"); ?></label>
                        </div>

                      


                       
                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_delete_tasks", "1", $can_delete_tasks ? true : false, "id='can_delete_tasks' class='form-check-input'");
                            ?>
                            <label for="can_delete_tasks"><?php echo app_lang("can_delete_tasks"); ?></label>
                        </div>

                    

                        <div class="col-md-12">
                            <?php
                            echo form_checkbox("can_update_maintask_status", "1", $can_update_maintask_status ? true : false, "id='can_update_maintask_status' class='form-check-input'");
                            ?>
                            <label for="can_update_maintask_status"><?php echo app_lang("can_update_maintask_status"); ?></label>
                        </div>

                    </div>

                    <div class="col-md-12" style="margin-top:20px;">
                            <?php
                            echo form_checkbox("can_show_subtasks_report", "1", $can_show_subtasks_report ? true : false, "id='can_show_subtasks_report' class='form-check-input'");
                            ?>
                            <label for="can_show_subtasks_report"><?php echo app_lang("can_show_subtasks_report"); ?></label>
                        </div>



                    <div class="col-md-12">
                            <?php
                            echo form_checkbox("can_update_maintask_after_closed", "1", $can_update_maintask_after_closed ? true : false, "id='can_update_maintask_after_closed' class='form-check-input'");
                            ?>
                            <label for="can_update_maintask_after_closed"><?php echo app_lang("can_update_maintask_after_closed"); ?></label>
                        </div>
                        

                        <div class="col-md-12" style="margin-top:10px;" >
                        <?php
                        echo form_checkbox("do_not_show_projects", "1", $do_not_show_projects ? true : false, "id='do_not_show_projects' class='manage_project_section form-check-input'");
                        ?>
                        <label for="do_not_show_projects"><?php echo app_lang("do_not_show_projects"); ?></label>
                    </div>


                        <div id="project_permission_details_area" class="row <?php echo $do_not_show_projects ? "hide" : ""; ?>">

                        
                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_create_projects", "1", $can_create_projects ? true : false, "id='can_create_projects' class='manage_project_section form-check-input'");
                            ?>
                            <label for="can_create_projects"><?php echo app_lang("can_create_projects"); ?></label>
                        </div>
                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_edit_projects", "1", $can_edit_projects ? true : false, "id='can_edit_projects' class='manage_project_section form-check-input'");
                            ?>
                            <label for="can_edit_projects"><?php echo app_lang("can_edit_projects"); ?></label>
                        </div>
                        
                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_delete_projects", "1", $can_delete_projects ? true : false, "id='can_delete_projects' class='manage_project_section form-check-input'");
                            ?>
                            <label for="can_delete_projects"><?php echo app_lang("can_delete_projects"); ?></label>
                        </div>
                    </div>

                    

                        
                       


                       



                        

                      <div class="col-md-6" style="margin-top:10px;">
                            <?php
                            echo form_checkbox("supplier_permission", "1", $supplier_permission ? true : false, "id='supplier_permission' class='form-check-input'");
                            ?>
                            <label for="supplier_permission"><?php echo app_lang("suppliers"); ?></label>
                        </div>

                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_add_suppliers", "1", $can_add_suppliers ? true : false, "id='can_add_suppliers' class='form-check-input'");
                            ?>
                            <label for="can_add_suppliers"><?php echo app_lang("can_add_suppliers"); ?></label>
                        </div>

                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_edit_suppliers", "1", $can_edit_suppliers ? true : false, "id='can_edit_suppliers' class='form-check-input'");
                            ?>
                            <label for="can_edit_suppliers"><?php echo app_lang("can_edit_suppliers"); ?></label>
                        </div>


                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_delete_suppliers", "1", $can_delete_suppliers ? true : false, "id='can_delete_suppliers' class='form-check-input'");
                            ?>
                            <label for="can_delete_suppliers"><?php echo app_lang("can_delete_suppliers"); ?></label>
                        </div>

                    </div>

                    <div class="col-md-6" style="margin-top:10px;">
                            <?php
                            echo form_checkbox("drivers_permission", "1", $drivers_permission ? true : false, "id='drivers_permission' class='form-check-input'");
                            ?>
                            <label for="drivers_permission"><?php echo app_lang("drivers_permissions"); ?></label>
                        </div>
                        <!-- hassan -->
                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_delete_driver", "1", $can_delete_driver ? true : false, "id='can_delete_driver' class='form-check-input'");
                            ?>
                            <label for="can_delete_driver"><?php echo app_lang("can_delete_driver"); ?></label>
                        </div>
                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_edit_driver", "1", $can_edit_driver ? true : false, "id='can_edit_driver' class='form-check-input'");
                            ?>
                            <label for="can_edit_driver"><?php echo app_lang("can_edit_driver"); ?></label>
                        </div>
                        <div class="col-md-6">
                            <?php
                            echo form_checkbox("can_add_driver", "1", $can_edit_driver ? true : false, "id='can_add_driver' class='form-check-input'");
                            ?>
                            <label for="can_add_driver"><?php echo app_lang("can_add_driver"); ?></label>
                        </div>

                    <div class="col-md-6" >
                            <?php
                            echo form_checkbox("car_type_permission", "1", $car_type_permission ? true : false, "id='car_type_permission' class='form-check-input'");
                            ?>
                            <label for="car_type_permission"><?php echo app_lang("cartype_permissions"); ?></label>
                        </div>







                        <div style="margin-top:10px;">
                            <?php
                            echo form_checkbox("can_add_city", "1", $can_add_city ? true : false, "id='can_add_city' class='form-check-input'");
                            ?>
                            <label for="can_add_city"><?php echo app_lang("can_add_city"); ?></label>
                        </div>



                        <div>
                            <?php
                            echo form_checkbox("can_edit_city", "1", $can_edit_city ? true : false, "id='can_edit_city' class='form-check-input'");
                            ?>
                            <label for="can_edit_city"><?php echo app_lang("can_edit_city"); ?></label>
                        </div>


                        <div>
                            <?php
                            echo form_checkbox("can_delete_city", "1", $can_delete_city ? true : false, "id='can_delete_city' class='form-check-input'");
                            ?>
                            <label for="can_delete_city"><?php echo app_lang("can_delete_city"); ?></label>
                        </div>
                         --------------------------------------------------------------------------
                        <div>
                            <?php
                            echo form_checkbox("can_edit_rec_inv_status", "1", $can_edit_rec_inv_status ? true : false, "id='can_edit_rec_inv_status' class='form-check-input'");
                            ?>
                            <label for="can_edit_rec_inv_status"><?php echo app_lang("can_edit_rec_inv_status"); ?></label>
                        </div>
                        <div>
                            <?php
                            echo form_checkbox("can_edit_car_status", "1", $can_edit_car_status ? true : false, "id='can_edit_car_status' class='form-check-input'");
                            ?>
                            <label for="can_edit_car_status"><?php echo app_lang("can_edit_car_status"); ?></label>
                        </div>
                        
                        
                    </div>

                </li>
                <?php if ($login_user->is_admin) { ?>
                    <li>
                        <span data-feather="key" class="icon-14 ml-20"></span>
                        <h5><?php echo app_lang("administration_permissions"); ?>:</h5>
                        <div>
                            <?php
                            echo form_checkbox("can_manage_all_kinds_of_settings", "1", $can_manage_all_kinds_of_settings ? true : false, "id='can_manage_all_kinds_of_settings' class='form-check-input'");
                            ?>
                            <label for="can_manage_all_kinds_of_settings"><?php echo app_lang("can_manage_all_kinds_of_settings"); ?></label>
                        </div>
                        <div id="can_manage_user_role_and_permissions_container" class="<?php echo $can_manage_all_kinds_of_settings ? "" : "hide"; ?>">
                            <?php
                            echo form_checkbox("can_manage_user_role_and_permissions", "1", $can_manage_user_role_and_permissions ? true : false, "id='can_manage_user_role_and_permissions' class='form-check-input'");
                            ?>
                            <label for="can_manage_user_role_and_permissions"><?php echo app_lang("can_manage_user_role_and_permissions"); ?></label>
                        </div>
                        <div>
                            <?php
                            echo form_checkbox("can_add_or_invite_new_team_members", "1", $can_add_or_invite_new_team_members ? true : false, "id='can_add_or_invite_new_team_members' class='form-check-input'");
                            ?>
                            <label for="can_add_or_invite_new_team_members"><?php echo app_lang("can_add_or_invite_new_team_members"); ?></label>
                        </div>
                    </li>
                <?php } ?>



                <li>
                    <span data-feather="key" class="icon-14 ml-20"></span>
                    <h5><?php echo app_lang("can_access_clients_information"); ?> <span class="help" data-bs-toggle="tooltip" title="Hides all information of clients except company name."><i data-feather="help-circle" class="icon-14"></i></span></h5>
                    <div>
                        <?php
                        if (is_null($client)) {
                            $client = "";
                        }
                        echo form_radio(array(
                            "id" => "client_no",
                            "name" => "client_permission",
                            "value" => "",
                            "class" => "client_permission toggle_specific form-check-input",
                                ), $client, ($client === "") ? true : false);
                        ?>
                        <label for="client_no"><?php echo app_lang("no"); ?> </label>
                    </div>
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "client_yes",
                            "name" => "client_permission",
                            "value" => "all",
                            "class" => "client_permission toggle_specific form-check-input",
                                ), $client, ($client === "all") ? true : false);
                        ?>
                        <label for="client_yes"><?php echo app_lang("yes_all_clients"); ?></label>
                    </div>
                   
                    <div>
                        <?php
                        echo form_radio(array(
                            "id" => "client_read_only",
                            "name" => "client_permission",
                            "value" => "read_only",
                            "class" => "client_permission toggle_specific form-check-input",
                                ), $client, ($client === "read_only") ? true : false);
                        ?>
                        <label for="client_read_only"><?php echo app_lang("read_only"); ?></label>
                    </div>
                    <div class="form-group">
                        <?php
                        echo form_radio(array(
                            "id" => "client_specific",
                            "name" => "client_permission",
                            "value" => "specific",
                            "class" => "client_permission toggle_specific form-check-input",
                                ), $client, ($client === "specific") ? true : false);
                        ?>
                        <label for="client_specific"><?php echo app_lang("yes_specific_client_groups"); ?>:</label>
                        <div class="specific_dropdown">
                            <input type="text" value="<?php echo $client_specific; ?>" name="client_permission_specific" id="client_groups_specific_dropdown" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" placeholder="<?php echo app_lang('choose_client_groups'); ?>"  />
                        </div>
                    </div>
                </li>

                <li>
                    <span data-feather="key" class="icon-14 ml-20"></span>
                    <h5><?php echo app_lang("set_team_members_permission"); ?>:</h5>


                    <div>
                        <?php
                        echo form_checkbox("hide_team_members_list", "1", $hide_team_members_list ? true : false, "id='hide_team_members_list' class='form-check-input'");
                        ?>
                        <label for="hide_team_members_list"><?php echo app_lang("hide_team_members_list"); ?></label>
                    </div>

                    <div>
                        <?php
                        echo form_checkbox("can_view_team_members_contact_info", "1", $can_view_team_members_contact_info ? true : false, "id='can_view_team_members_contact_info' class='form-check-input'");
                        ?>
                        <label for="can_view_team_members_contact_info"><?php echo app_lang("can_view_team_members_contact_info"); ?></label>
                    </div>

                    <div>
                        <?php
                        echo form_checkbox("can_view_team_members_social_links", "1", $can_view_team_members_social_links ? true : false, "id='can_view_team_members_social_links' class='form-check-input'");
                        ?>
                        <label for="can_view_team_members_social_links"><?php echo app_lang("can_view_team_members_social_links"); ?></label>
                    </div>

                    <div>
                        <label for="can_update_team_members_general_info_and_social_links"><?php echo app_lang("can_update_team_members_general_info_and_social_links"); ?></label>
                        <div class="ml15">
                            <div>
                                <?php
                                if (is_null($team_member_update_permission)) {
                                    $team_member_update_permission = "";
                                }
                                echo form_radio(array(
                                    "id" => "team_member_update_permission_no",
                                    "name" => "team_member_update_permission",
                                    "value" => "",
                                    "class" => "team_member_update_permission toggle_specific form-check-input",
                                        ), $team_member_update_permission, ($team_member_update_permission === "") ? true : false);
                                ?>
                                <label for="team_member_update_permission_no"><?php echo app_lang("no"); ?></label>
                            </div>
                            <div>
                                <?php
                                echo form_radio(array(
                                    "id" => "team_member_update_permission_all",
                                    "name" => "team_member_update_permission",
                                    "value" => "all",
                                    "class" => "team_member_update_permission toggle_specific form-check-input",
                                        ), $team_member_update_permission, ($team_member_update_permission === "all") ? true : false);
                                ?>
                                <label for="team_member_update_permission_all"><?php echo app_lang("yes_all_members"); ?></label>
                            </div>
                            <div class="form-group">
                                <?php
                                echo form_radio(array(
                                    "id" => "team_member_update_permission_specific",
                                    "name" => "team_member_update_permission",
                                    "value" => "specific",
                                    "class" => "team_member_update_permission toggle_specific form-check-input",
                                        ), $team_member_update_permission, ($team_member_update_permission === "specific") ? true : false);
                                ?>
                                <label for="team_member_update_permission_specific"><?php echo app_lang("yes_specific_members_or_teams"); ?>:</label>
                                <div class="specific_dropdown">
                                    <input type="text" value="<?php echo $team_member_update_permission_specific; ?>" name="team_member_update_permission_specific" id="team_member_update_permission_specific_dropdown" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" placeholder="<?php echo app_lang('choose_members_and_or_teams'); ?>"  />    
                                </div>
                            </div>
                        </div>
                    </div>

                </li>

                <li>
                    <span data-feather="key" class="icon-14 ml-20"></span>
                    <h5><?php echo app_lang("set_message_permissions"); ?>:</h5>
                    <div>
                        <?php
                        echo form_checkbox("message_permission_no", "1", ($message_permission == "no") ? true : false, "id='message_permission_no' class='form-check-input'");
                        ?>
                        <label for="message_permission_no"><?php echo app_lang("cant_send_any_messages"); ?></label>
                    </div>
                    <div id="message_permission_specific_area" class="form-group <?php echo ($message_permission == "no") ? "hide" : ""; ?>">
                        <?php
                        echo form_checkbox("message_permission_specific_checkbox", "1", ($message_permission == "specific") ? true : false, "id='message_permission_specific_checkbox' class='message_permission_specific toggle_specific form-check-input'");
                        ?>
                        <label for="message_permission_specific_checkbox"><?php echo app_lang("can_send_messages_to_specific_members_or_teams"); ?></label>
                        <div class="specific_dropdown">
                            <input type="text" value="<?php echo $message_permission_specific; ?>" name="message_permission_specific" id="message_permission_specific_dropdown" class="w100p validate-hidden"  data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" placeholder="<?php echo app_lang('choose_members_and_or_teams'); ?>"  />    
                        </div>
                    </div>
                </li>

               
                </li>

               

                


               
               
               
               
                
           


                <?php app_hooks()->do_action('app_hook_role_permissions_extension'); ?>

            </ul>

        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary mr10"><span data-feather="check-circle" class="icon-14"></span> <?php echo app_lang('save'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#permissions-form").appForm({
            isModal: false,
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 10000});
            }
        });

        $("#leave_specific_dropdown, #attendance_specific_dropdown, #timesheet_manage_permission_specific_dropdown, #timesheet_manage_permission_specific_excluding_own_dropdown, #team_member_update_permission_specific_dropdown, #message_permission_specific_dropdown, #timeline_permission_specific_dropdown").select2({
            multiple: true,
            formatResult: teamAndMemberSelect2Format,
            formatSelection: teamAndMemberSelect2Format,
            data: <?php echo ($members_and_teams_dropdown); ?>
        }).on('select2-open change', function (e) {
            feather.replace();
        });

        feather.replace();

        $("#ticket_types_specific_dropdown").select2({
            multiple: true,
            data: <?php echo ($ticket_types_dropdown); ?>
        });

        $('[data-bs-toggle="tooltip"]').tooltip();

        $(".toggle_specific").click(function () {
            toggle_specific_dropdown();
        });

        toggle_specific_dropdown();

        function toggle_specific_dropdown() {
            var selectors = [".leave_permission", ".attendance_permission", ".timesheet_manage_permission", ".team_member_update_permission", ".ticket_permission", ".message_permission_specific", ".timeline_permission_specific", ".client_permission"];
            $.each(selectors, function (index, element) {
                var $element = $(element + ":checked");
                if (((element !== ".message_permission_specific" && $element.val() === "specific") || (element === ".message_permission_specific" && $element.is(":checked") && !$("#message_permission_specific_area").hasClass("hide")))
                        || ((element !== ".timeline_permission_specific" && $element.val() === "specific") || (element === ".timeline_permission_specific" && $element.is(":checked") && !$("#timeline_permission_specific_area").hasClass("hide")))
                        || ($element.val() === "specific_excluding_own" && $element.is(":checked"))) {

                    $(element).closest("li").find(".specific_dropdown").hide().find("input").removeClass("validate-hidden"); //hide other active dropdown first
                    $element.closest("div").find(".specific_dropdown").show().find("input").addClass("validate-hidden");
                } else {
                    $(element).closest("div").find(".specific_dropdown").hide().find("input").removeClass("validate-hidden");
                }
            });

        }

        //show/hide message permission checkbox
        $("#message_permission_no").click(function () {
            if ($(this).is(":checked")) {
                $("#message_permission_specific_area").addClass("hide");
            } else {
                $("#message_permission_specific_area").removeClass("hide");
            }

            toggle_specific_dropdown();
        });

        //show/hide role permission setting
        $("#can_manage_all_kinds_of_settings").click(function () {
            if ($(this).is(":checked")) {
                $("#can_manage_user_role_and_permissions_container").removeClass("hide");
            } else {
                $("#can_manage_user_role_and_permissions_container").addClass("hide");
            }
        });

        $("#do_not_show_projects").click(function () {
            if ($(this).is(":checked")) {
                $("#project_permission_details_area").addClass("hide");
            } else {
                $("#project_permission_details_area").removeClass("hide");
            }
        });


        $("#can_manage_all_projects").click(function () {
            if ($(this).is(":checked")) {
                $("#all_tasks_details_area").addClass("hide");
                $("#supply_mang").prop("checked", true);
                $("#reserv_mang").prop("checked", true);
            } else {
                $("#all_tasks_details_area").removeClass("hide");
            }
        });

        $("#can_edit_only_own_subtasks").click(function () {
            if ($(this).is(":checked")) {
                $("#can_edit_subtasks").prop("checked", false);
                $("#can_edit_subtasksD").addClass("hide");
            } else {
                $("#can_edit_subtasksD").removeClass("hide");
            }
        });


         $("#supply_mang").click(function () {
            if ($(this).is(":checked")) {
                $("#can_show_subtasks").prop("checked", true);
                $("#can_edit_subtasks").prop("checked", true);
            }
        });

         $("#reserv_mang").click(function () {
            if ($(this).is(":checked")) {
                $("#can_show_subtasks").prop("checked", true);
                $("#can_edit_subtasks").prop("checked", true);
                $("#can_create_subtasks").prop("checked", true);
                $("#can_show_tasks").prop("checked", true);
            }
        });

        var manageProjectSection = "#can_manage_all_projects, #can_create_projects, #can_edit_projects, #can_delete_projects, #can_add_remove_project_members, #can_create_tasks";
        var manageAssignedTasks = "#show_assigned_tasks_only, #can_update_only_assigned_tasks_status";
        var manageAssignedTasksSection = "#show_assigned_tasks_only_section, #can_update_only_assigned_tasks_status_section";

        if ($(manageProjectSection).is(':checked')) {
            $(manageAssignedTasksSection).addClass("hide");
        }

        $(manageProjectSection).click(function () {
            if ($(this).is(":checked")) {
                $(manageAssignedTasks).prop("checked", false);
                $(manageAssignedTasksSection).addClass("hide");
                if ($(this).attr("id") === "can_edit_projects") {
                    $("#can_edit_only_own_created_projects_section").addClass("hide");
                } else if ($(this).attr("id") === "can_delete_projects") {
                    $("#can_delete_only_own_created_projects_section").addClass("hide");
                }
            } else {
                if ($(this).attr("id") === "can_edit_projects") {
                    $("#can_edit_only_own_created_projects_section").removeClass("hide");
                } else if ($(this).attr("id") === "can_delete_projects") {
                    $("#can_delete_only_own_created_projects_section").removeClass("hide");
                }
            }
        });

        $('.manage_project_section').change(function () {
            var checkedStatus = $('.manage_project_section:checkbox:checked').length > 0;
            if (!checkedStatus) {
                $(manageAssignedTasksSection).removeClass("hide");
            }
        }).change();

        //show/hide timeline permission checkbox
        $("#timeline_permission_no").click(function () {
            if ($(this).is(":checked")) {
                $("#timeline_permission_specific_area").addClass("hide");
            } else {
                $("#timeline_permission_specific_area").removeClass("hide");
            }

            toggle_specific_dropdown();
        });
        $("#client_groups_specific_dropdown").select2({
            multiple: true,
            data: <?php echo ($client_groups_dropdown); ?>
        });

    });
</script>