<?php echo form_open(get_uri("subtasks/save_task"), array("id" => "task-form", "class" => "general-form", "role" => "form")); ?>
<?php
   $dateTime = new DateTime('now', new DateTimeZone("Asia/Riyadh"));
   $newDate2 = $dateTime->format(get_setting("date_format"));
   ?>
<div class="modal-body clearfix">
   <div class="container-fluid">
      <input type="hidden" name="id" value="<?php echo $add_type == "multiple" ? "" : $model_info->id; ?>" />
      <?php if ($task_id && $task_id!=0) { ?><input type="hidden" name="task_id" value="<?php echo $task_id; ?>" /><?php } ?>
      <input type="hidden" name="add_type" value="<?php echo $add_type; ?>" />
      <input type="hidden" name="mang" value="reservmang" />
      <?php if ($is_clone) { ?>
      <input type="hidden" name="is_clone" value="1" />
      <?php } ?>
      <?php if (!$task_id || $task_id==0) { ?>
      <div class="form-group">
         <div class="row">
            <label for="task_id" class=" col-md-2"><?php echo app_lang('main_task'); ?></label>
            <div class="col-md-10">
               <?php
                  echo form_dropdown("task_id", $tasks_dropdown, array($model_info->pnt_task_id), "class='select2 validate-hidden' id='task_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                  ?>
            </div>
         </div>
      </div>
      <?php } ?>
      <div class="form-group">
         <div class="row">
            <div class=" col-md-4 mb5 mt5 floating-label">
               <?php
                  echo form_input(array(
                      "id" => "guest_nm",
                      "name" => "guest_nm",
                      "value" =>  $model_info->guest_nm,
                      "class" => "form-control",
                      "placeholder" => app_lang('guest_nm'),
                      "autocomplete" => "off",
                      "autofocus" => true,
                      "data-rule-required" => true,
                      "data-msg-required" => app_lang("field_required"),
                  ));
                  ?>
               <label for="guest_nm" ><?php echo app_lang('guest_nm'); ?></label>
            </div>
            <div class=" col-md-4 mb5 mt5 floating-label">
               <?php
                  echo form_input(array(
                      "id" => "guest_phone",
                      "name" => "guest_phone",
                      "value" =>  $model_info->guest_phone?$model_info->guest_phone:'',
                      "class" => "form-control",
                      "autocomplete" => "off",
                      "placeholder" => app_lang('guest_phone'),
                      //"autofocus" => true,
                      //"data-rule-required" => true,
                      //"data-msg-required" => app_lang("field_required"),
                  ));
                  ?>
               <label for="guest_phone"><?php echo app_lang('guest_phone'); ?></label>
            </div>
            <div class=" col-md-4 mb5 mt5 floating-label">
               <?php
                  echo form_input(array(
                      "id" => "car_expens",
                      "name" => "car_expens",
                      "value" =>  $model_info->car_expens?$model_info->car_expens:'',
                      "class" => "form-control",
                      "type" => "number",
                      "autocomplete" => "off",
                      "placeholder" => app_lang('car_expens'),
                      //"autofocus" => true,
                      //"data-rule-required" => true,
                      //"data-msg-required" => app_lang("field_required"),
                  ));
                  ?>
               <label for="car_expens" ><?php echo app_lang('car_expens'); ?></label>
            </div>
            
         </div>
      </div>
      <div class="form-group">
         <div class="row">
            <div class=" col-md-6 mb5 mt5 floating-label">
               <?php
                  echo form_input(array(
                      "id" => "car_expens_stmnt",
                      "name" => "car_expens_stmnt",
                      "value" =>  $model_info->car_expens_stmnt?$model_info->car_expens_stmnt:'',
                      "class" => "form-control",
                      "autocomplete" => "off",
                      "placeholder" => app_lang('car_expens_stmnt'),
                      //"autofocus" => true,
                      //"data-rule-required" => true,
                      //"data-msg-required" => app_lang("field_required"),
                  ));
                  ?>
               <label for="car_expens_stmnt"><?php echo app_lang('car_expens_stmnt'); ?></label>
            </div>
      <div class=" col-md-6 mb5 mt5 floating-label">
                <?php
                echo form_dropdown("city_id", $cities_dropdown, array($model_info->city_id), "class='select2 validate-hidden' id='city_id' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                            ?>
               <label for="city_id"><?php echo app_lang('city'); ?></label>
            </div>
         </div>
      </div>
      <?php //if($login_user->role_id==get_setting("sales_role_id") || $mang=="reservmang"){ 
         echo view("subtasks/sal_pur_input/sales_input");
         /* }
         if($login_user->role_id==get_setting("pur_role_id") || $mang=="supplymang"){ 
         echo view("subtasks/sal_pur_input/purchases_input");
         } */
         ?>
      <div class="form-group">
         <div class="row">

            <div class="col-md-12 mb5 mt3 floating-label">
               <?php
                  echo form_input(array(
                      "id" => "priority_id",
                      "name" => "priority_id",
                      "value" => $model_info->priority_id,
                      "class" => "form-control",
                      "placeholder" => app_lang('priority')
                  ));
                  ?>
               <label for="priority_id"><?php echo app_lang('priority'); ?></label>
            </div>
            <?php //if(!$model_info->id){ ?>
            <!--<div class=" col-md-6 mb-2 floating-label">-->
            <?php
               /* foreach ($statuses as $status) {
                    $task_status[$status->id] = $status->key_name ? app_lang($status->key_name) : $status->title;
                }
                
                if ($is_clone) {
                    echo form_dropdown("status_id", $task_status, 1, "class='select2'");
                } else {
                    echo form_dropdown("status_id", $task_status, array($model_info->status_id), "class='select2'");
                }*/
                ?>
            <!--<label for="status_id" class=" col-md-2"><?php //echo app_lang('status'); ?></label>-->
            <!--</div>-->
            <?php //} ?>
         </div>
      </div>
      <?php
         $act_out_time_dateTime = new DateTime($model_info->act_out_time? $model_info->act_out_time:'00:00', new DateTimeZone("Asia/Riyadh"));
         $act_return_time_dateTime = new DateTime($model_info->act_return_time? $model_info->act_return_time:'00:00', new DateTimeZone("Asia/Riyadh"));
         
         ?>
      <?php
         $act_return_date_text='___';
           if ($model_info->act_return_date && is_date_exists($model_info->act_return_date)) {
            $act_return_date_text = format_to_date($model_info->act_return_date, false);
            if (get_my_local_time("Y-m-d") > $model_info->act_return_date /*&& $data->status_id != "1"*/) {
                $act_return_date_text = "<span class='text-danger'>" . $act_return_date_text . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $model_info->act_return_date /*&& $data->status_id != "3"*/) {
                $act_return_date_text = "<span class='text-warning'>" . $act_return_date_text . "</span> ";
            }
         }
                
            
             ?>
      <div class="row" style="padding-right: 5px; padding-left: 5px;">
         <div class="col-md-6 mb5 mt5 text-wrap" >
            <strong><?php echo app_lang('created_by') . ": "; ?></strong>
            <label><?php echo $created_by_nm->first_name.' '.$created_by_nm->last_name; ?></label>
         </div>
         <div class="col-md-6 mb5 mt5 text-wrap" >
            <strong><?php echo app_lang('updated_by') . ": "; ?></strong>
            <label><?php echo  $updated_by_nm->first_name ? $updated_by_nm->first_name.' '.$updated_by_nm->last_name:'__'; ?></label>
         </div>
      </div>
      <h4 class="modal-title" id="ajaxModalTitle" data-title="<?php echo app_lang("supply_mang"); ?>" style="font-size: 14px; font-weight: bold; text-align: center;margin-bottom: 10px;color: #252a9f;"><?php echo app_lang("supply_mang"); ?> </h4>
      <div class="row" style="padding-right: 5px; padding-left: 5px;">
         <div class="col-md-4 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('supplier') . ": "; ?></strong>
            <label><?php echo $supplier_name->name?$supplier_name->name:' ___ ' ; ?></label>
         </div>
         <div class="col-md-4 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('car_number') . ": "; ?></strong>
            <label><?php echo $model_info->car_number?$model_info->car_number:' ___ '; ?></label>
         </div>
         <div class="col-md-4 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('act_out_date') . ": "; ?></strong>
            <label><?php echo is_date_exists($model_info->act_out_date)?format_to_date($model_info->act_out_date, false):'___'; ?></label>
         </div>
         <div class="col-md-4 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('act_out_time') . ": "; ?></strong>
            <label><?php $act_out_time_txt=date_create($model_info->act_out_time); echo $model_info->act_out_time && $model_info->act_out_time!='00:00:01'?date_format($act_out_time_txt,"h:i A"):'___'; ?></label>
         </div>
         <div class="col-md-4 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('act_return_date') . ": "; ?></strong>
            <label><?php echo $act_return_date_text; ?></label>
         </div>
         <div class="col-md-4 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('act_return_time') . ": "; ?></strong>
            <label><?php echo $model_info->act_return_time && $model_info->act_return_time!='00:00:01'?$act_return_time_dateTime->format('h:i A'):'___'; ?></label>
         </div>
         <div class="col-md-4 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('day_count') . ": "; ?></strong>
            <label><?php echo $model_info->day_count?$model_info->day_count:' __ '; ?></label>
         </div>
         <div class="col-md-8 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('car_status') . ": "; ?></strong>
            <label><?php echo $model_info->car_status?$model_info->car_status:' __ '; ?></label>
         </div>
         <div class="col-md-6 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('rec_inv_status') . ": "; ?></strong>
            <label><?php echo $rec_inv_status; ?></label>
         </div>
         <div class="col-md-6 mb5 mt5 text-wrap">
            <strong><?php echo app_lang('note') . ": "; ?></strong>
            <label><?php echo $model_info->note_2; ?></label>
         </div>
      </div>
      <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 
   </div>
</div>
<di   v class="modal-footer">
   <div id="link-of-new-view" class="hide">
      <?php
         echo modal_anchor(get_uri("subtasks/task_view"), "", array("data-modal-lg" => "1"));
         ?>
   </div>
   <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
   <?php if ($add_type == "multiple") { ?>
   <button id="save-and-add-button" type="button" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_add_more'); ?></button>
   <?php } else { ?>
   <?php if ($view_type !== "details") { ?>
   <button id="save-and-show-button" type="button" class="btn btn-info text-white"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save_and_show'); ?></button>
   <?php } ?>
   <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('save'); ?></button>
   <?php } ?>
</div>
<?php echo form_close(); ?>
<script type="text/javascript">
   $(document).ready(function () {
   
   
   
       //send data to show the task after save
       window.showAddNewModal = false;
   
       $("#save-and-show-button, #save-and-add-button").click(function () {
           window.showAddNewModal = true;
           $(this).trigger("submit");
       });
   
       var taskShowText = "<?php echo app_lang('task_info') ?>",
               multipleTaskAddText = "<?php echo app_lang('add_multiple_tasks') ?>",
               addType = "<?php echo $add_type; ?>";
   
       window.taskForm = $("#task-form").appForm({
           closeModalOnSuccess: false,
           onSuccess: function (result) {
            console.log(result.data);
            try{
               $("#subtask-table").appTable({newData: result.data, dataId: result.id});
           }catch(er){ }

           try{
               $("#main-task-table").appTable({newData: result.data, dataId: result.id});

           }catch(er){ }
   
           try{
               $("#mytask-table_"+result.pnt_id).appTable({newData: result.data, dataId: result.id});
               if(result.main_task_updated){
               $("#main-task-table").appTable({
                reload: !0,
                //filterParams: t.filterParams
            });
           }
               }catch(er){
               }
   
               try{
               $("#subtask-report-table").appTable({newData: result.data, dataId: result.id});
               }catch(er){
               }
               //alert(result.pnt_id);
               $("#reload-kanban-button:visible").trigger("click");
   
               $("#save_and_show_value").append(result.save_and_show_link);
   
               if (window.showAddNewModal) {
                   var $taskViewLink = $("#link-of-new-view").find("a");
   
                   if (addType === "multiple") {
                       //add multiple tasks
                       $taskViewLink.attr("data-action-url", "<?php echo get_uri("subtasks/task_modal_form"); ?>");
                       $taskViewLink.attr("data-title", multipleTaskAddText);
                       $taskViewLink.attr("data-post-last_id", result.id);
                       $taskViewLink.attr("data-post-task_id", "<?php echo $task_id; ?>");
                       $taskViewLink.attr("data-post-add_type", "multiple");
                   } else {
                       //save and show
                       $taskViewLink.attr("data-action-url", "<?php echo get_uri("subtasks/task_view"); ?>");
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
   
       setDatePicker("#out_date");
       
       //setDatePicker("#tmp_return_date");
       //setTimePicker("#exp_out_time");
   
      
       setDatePicker("#tmp_return_date");
       setDatePicker("#sales_act_return_date");
       setDatePicker("#start_date");
       setDatePicker("#end_date");
       setDatePicker("#ten_out_date");
       
   
      
   
       $('#exp_out_time').parents('.timepicker').timepicker().on('changeTime.timepicker', function(e) {
   $('#exp_out_time').val(e.time.value);
   
   });
   
   
   $('#exp_out_time').timepicker({
                minuteStep: 5,
                
                template:'dropdown',
                showInputs: true,
                defaultTime: "",
                //modalBackdrop: true,
                showSeconds: false,
                showMeridian: true
            });
   
   $('#return_time').timepicker({
                minuteStep: 5,
                
                template:'dropdown',
                showInputs: true,
                defaultTime: "",
                //modalBackdrop: true,
                showSeconds: false,
                showMeridian: true
            });
   
   $('#start_time').timepicker({
                minuteStep: 5,
                
                template:'dropdown',
                showInputs: true,
                defaultTime: "",
                //modalBackdrop: true,
                showSeconds: false,
                showMeridian: true
            });
   
   $('#exp_out_time').timepicker({
                minuteStep: 5,
                
                template:'dropdown',
                showInputs: true,
                defaultTime: "",
                //modalBackdrop: true,
                showSeconds: false,
                showMeridian: true
            });
   
      
   
      
       
   
   
   
   
   
       <?php $dateTime20 = new DateTime('now', new DateTimeZone("Asia/Riyadh"));
      $dateTime3 = new DateTime($model_info->out_date, new DateTimeZone("Asia/Riyadh"));
      $ddt=is_date_exists($model_info->out_date) ? $dateTime3->format(get_setting("date_format")) : $dateTime20->format(get_setting("date_format"));
      
      ?>
       var we='<?php echo $ddt; ?>';
      
   
       $('#tmp_return_date').datepicker('setStartDate', ''+we+'');
       $('#sales_act_return_date').datepicker('setStartDate', ''+we+'');
   
   
     
   
   $("#out_date").datepicker().on('changeDate', function(selected){
   
       startDate = new Date(selected.date.valueOf());
       //alert(startDate);
       $('#tmp_return_date').datepicker('setStartDate', startDate);
       $('#sales_act_return_date').datepicker('setStartDate', startDate);
   });
   
   
      
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
   
       $('#priority_id').select2({data: <?php echo json_encode($priorities_dropdown); ?>});
   });
</script>    
<?php echo view("subtasks/get_related_data_of_project_script"); ?>