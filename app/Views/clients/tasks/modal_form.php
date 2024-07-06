<?php echo form_open(get_uri("tasks/save_task"), array("id" => "task-form", "class" => "general-form", "role" => "form")); ?>
<div id="tasks-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $add_type == "multiple" ? "" : $model_info->id; ?>" />
            
            <input type="hidden" name="add_type" value="<?php echo $add_type; ?>" />
            <?php if($client_id){ ?>
            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
        <?php } ?>
            <input type="hidden" name="title" value="" />

            <?php if ($is_clone) { ?>
                <input type="hidden" name="is_clone" value="1" />
            <?php } ?>


            <?php if(!$client_id){ ?>
            <div class="form-group">
                    <div class="row">
                        <label for="client_id" class=" col-md-2"><?php echo app_lang('client'); ?></label>
                        <div class="col-md-10">
                            <?php
                            echo form_dropdown("client_id", $clients_dropdown, array($add_type == "multiple"?0:$model_info->client_id), "class='select2 validate-hidden' id='client_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                            ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <div class="form-group">
                <div class="row">
                    <label for="contact_id" class=" col-md-2"><?php echo app_lang('contacts'); ?></label>
                    <div class="col-md-10" id="dropdown-apploader-section">
                        <?php
                        echo form_input(array(
                            "id" => "contact_id",
                            "name" => "contact_id",
                            "value" => $add_type != "multiple"?$model_info->client_contact_id:'',
                            "class" => "form-control",
                            //"data-rule-required" => true,
                            //"data-msg-required" => app_lang("field_required"),
                            "placeholder" => app_lang('contacts')
                        ));


                        ?>
                    </div>
                    
                    
                </div>
            </div>


           
                <div class="form-group">
                    <div class="row">
                        <label for="project_id" class=" col-md-2"><?php echo app_lang('project'); ?></label>
                        

                        <div class="col-md-10" id="dropdown-apploader-section">
                        <?php
                        echo form_dropdown(array(
                            "id" => "project_id",
                            "name" => "project_id",
                            "value" => $add_type != "multiple"?$model_info->project_id:'',
                            "class" => "form-control",
                            "data-rule-required" => true,
                            
                            "data-msg-required" => app_lang("field_required"),
                            "placeholder" => app_lang('project')
                        ));


                        ?>

                       

                        
                    </div>

                        
                    </div>
                </div>




                <div class="form-group">
                    <div class="row">
                        <label for="city_id" class=" col-md-2" style="padding-top: 5px;"><?php echo app_lang('city'); ?></label>
                        <div class="col-md-4">
                            <?php
                            echo form_dropdown("city_id", $cities_dropdown, array($model_info->city_id), "class='select2 validate-hidden' id='city_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                            ?>
                        </div>

                        <label for="christening_number" class=" col-md-2" style="padding-top: 5px; text-align: center;"><?php echo app_lang('christening_number'); ?></label>
                        <div class="col-md-4" >
                            <?php
                        echo form_input(array(
                            "id" => "christening_number",
                            "name" => "christening_number",
                            "value" =>  $model_info->christening_number?$model_info->christening_number:'',
                            "class" => "form-control",
                            
                            "placeholder" => app_lang('christening_number'),
                            //"autofocus" => true,
                            //"data-rule-required" => true,
                            //"data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        </div>

                        
                    </div>
                </div>


                <div class="form-group">
                    <div class="row">
                        <label for="invoice_number" class=" col-md-2" ><?php echo app_lang('invoice_number'); ?></label>
                        <div class="col-md-4">
                            <?php
                        echo form_input(array(
                            "id" => "invoice_number",
                            "name" => "invoice_number",
                            "value" =>  $model_info->invoice_number?$model_info->invoice_number:'',
                            "class" => "form-control",
                            'type' => 'number',
                            "placeholder" => app_lang('invoice_number'),
                            //"autofocus" => true,
                            //"data-rule-required" => true,
                            //"data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        </div>

                        <label for="ref_number" class=" col-md-2" style="padding-top: 5px; text-align: center;"><?php echo app_lang('ref_number'); ?></label>
                        <div class="col-md-4">
                            <?php
                        echo form_input(array(
                            "id" => "ref_number",
                            "name" => "ref_number",
                            "value" =>  $model_info->ref_number?$model_info->ref_number:'',
                            "class" => "form-control",
                            
                            "placeholder" => app_lang('ref_number'),
                            //"autofocus" => true,
                            //"data-rule-required" => true,
                            //"data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        </div>

                        
                    </div>
                </div>
            


          <div class="form-group">
            <div class="row">
                <label for="created_date" class=" col-md-2"><?php echo app_lang('task_date'); ?></label>
                <div class=" col-md-10">
                    <?php
                    //$now = get_current_utc_time();
                    $dateTime = new DateTime('now', new DateTimeZone("Asia/Riyadh"));
                    $newDate2 = $dateTime->format('d/m/Y');
                    
                    
                    echo form_input(array(
                        "id" => "created_date",
                        "name" => "created_date",
                        "value" => is_date_exists($model_info->created_date) ? $model_info->created_date : $dateTime->format('d/m/Y'),
                        "class" => "form-control",
                        "placeholder" => app_lang('task_date'),
                        "autocomplete" => "off"
                    ));
                    ?>
                </div>
            </div>
        </div>
        

            <div class="form-group">
                <div class="row">
                    <label for="description" class=" col-md-2"><?php echo app_lang('description'); ?></label>
                    <div class=" col-md-10">
                        <?php
                        echo form_textarea(array(
                            "id" => "description",
                            "name" => "description",
                            "value" => $add_type == "multiple" ? "" : $model_info->description,
                            "class" => "form-control",
                            "placeholder" => app_lang('description'),
                            "data-rich-text-editor" => true
                        ));
                        ?>
                    </div>
                </div>
            </div>

          
          
           
            

          

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 


            <?php if ($is_clone) { ?>
                <?php if ($has_checklist) { ?>
                    <div class="form-group">
                        <label for="copy_checklist" class=" col-md-12">
                            <?php
                            echo form_checkbox("copy_checklist", "1", true, "id='copy_checklist' class='float-start mr15 form-check-input'");
                            ?>    
                            <?php echo app_lang('copy_checklist'); ?>
                        </label>
                    </div>
                <?php } ?>

                <?php if ($has_sub_task) { ?>
                    <div class="form-group">
                        <label for="copy_sub_tasks" class=" col-md-12">
                            <?php
                            echo form_checkbox("copy_sub_tasks", "1", false, "id='copy_sub_tasks' class='float-start mr15 form-check-input'");
                            ?>    
                            <?php echo app_lang('copy_sub_tasks'); ?>
                        </label>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <div class="modal-footer">
        <div id="link-of-new-view" class="hide">
            <?php
            echo modal_anchor(get_uri("tasks/task_view"), "xx", array("data-modal-lg" => "1"));
            ?>
        </div>

       

        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

        <?php if ($add_type == "multiple") { ?>
            <button id="save-and-add-button" type="button" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_add_more'); ?></button>
        <?php } else { ?>
            <?php if ($view_type !== "details") { ?>
                <button id="save-and-show-button" type="button" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_show'); ?></button>
            <?php } ?>
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo $model_info->id?app_lang('save'):app_lang('save_and_continue'); ?></button>
        <?php } ?>
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">

    function function_name(c_id,contact_id) {
        $.ajax({
                    url: "<?php echo get_uri('clients/get_client_contact_dropdown_new') ?>" + "/" + c_id+"/"+contact_id,
                    dataType: "json",
                    success: function (result) {
                        
                        
                        $('#contact_id').select2({data: result.contacts_dropdown});


                        var htm="";
                        var p_id="<?php echo $model_info->project_id; ?>";
                        var selected_o='';
                        for (var i = 0; i < result.projects_dropdown.length; i++) {
                            if(p_id){
                            if(p_id==result.projects_dropdown[i].id){
                                selected_o='selected';
                            }else{
                                selected_o='';
                            }
                        }else{
                            if(i==0){
                                selected_o='selected';
                                p_id=result.projects_dropdown[i].id;
                            }else{
                                selected_o='';
                            }
                        }
                            htm+="<option value='"+result.projects_dropdown[i].id+"' "+selected_o+">"+result.projects_dropdown[i].text+"</option>";
                        

                        }
                        $("#project_id").html(htm);
                        $("#project_id").select2().select2('val',p_id);

                        
                        
                    }
                });
    }
     
    $(document).ready(function () {

       

        <?php  if($contacts_dropdown ){ ?>
        function_name(<?php echo $client_id?$client_id:$model_info->client_id; ?>,<?php echo $model_info->client_contact_id; ?>);
    <?php  } ?>



        //send data to show the task after save
        window.showAddNewModal = false;
        viewType="yes";
        $("#save-and-show-button, #save-and-add-button").click(function () {
            window.showAddNewModal = true;
            $(this).trigger("submit");
            viewType = "no";
        });

        var taskShowText = "<?php echo app_lang('task_info') ?>",
                multipleTaskAddText = "<?php echo app_lang('add_multiple_tasks') ?>",
                addType = "<?php echo $add_type; ?>",
                taskID = "<?php echo $model_info->id?"no":"yes"; ?>";
                
                

        window.taskForm = $("#task-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                if (addType != "multiple" && viewType != "no" && taskID != "no") {

                    window.taskForm.closeModal();
                window.location.replace("<?php echo get_uri("subtasks/index/"); ?>"+result.id+"/tasks_list/0/yes");

                }
                $("#task-table").appTable({newData: result.data, dataId: result.id});
                $("#reload-kanban-button:visible").trigger("click");

                $("#save_and_show_value").append(result.save_and_show_link);

                if (window.showAddNewModal) {
                    var $taskViewLink = $("#link-of-new-view").find("a");

                    if (addType === "multiple") {
                        //add multiple tasks
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("tasks/task_modal_form"); ?>");
                        $taskViewLink.attr("data-title", multipleTaskAddText);
                        $taskViewLink.attr("data-post-last_id", result.id);
                        $taskViewLink.attr("data-post-project_id", "<?php echo $project_id; ?>");
                        $taskViewLink.attr("data-post-add_type", "multiple");
                    } else {
                        //save and show
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("tasks/task_view"); ?>");
                        $taskViewLink.attr("data-title", taskShowText + " #" + result.id);
                        $taskViewLink.attr("data-post-id", result.id);
                    }

                    $taskViewLink.trigger("click");
                } else {
                    window.taskForm.closeModal();

                    if (window.refreshAfterAddTask) {
                        window.refreshAfterAddTask = false;
                        location.reload();
                    }
                }

                if (typeof window.reloadGantt === "function") {
                    window.reloadGantt(true);
                }
            },
            onAjaxSuccess: function (result) {
                if (!result.success && result.next_recurring_date_error) {
                    $("#next_recurring_date").val(result.next_recurring_date_value);
                    $("#next_recurring_date_container").removeClass("hide");

                    $("#task-form").data("validator").showErrors({
                        "next_recurring_date": result.next_recurring_date_error
                    });
                }
            }
        });
        $("#task-form .select2").select2();
        setTimeout(function () {
            $("#title").focus();
        }, 200);

        setDatePicker("#created_date");

       

        $('[data-bs-toggle="tooltip"]').tooltip();

        //show/hide recurring fields
        $("#recurring").click(function () {
            if ($(this).is(":checked")) {
                $("#recurring_fields").removeClass("hide");
            } else {
                $("#recurring_fields").addClass("hide");
            }
        });

        setDatePicker("#next_recurring_date", {
            startDate: moment().add(1, 'days').format("YYYY-MM-DD") //set min date = tomorrow
        });

        //$('#priority_id').select2({data: <?php //echo json_encode($priorities_dropdown); ?>});
    });
</script>    

