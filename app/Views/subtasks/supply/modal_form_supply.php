<?php echo form_open(get_uri("subtasks/save_task"), array("id" => "task-form", "class" => "general-form", "role" => "form")); ?>

<?php
$dateTime = new DateTime('now', new DateTimeZone("Asia/Riyadh"));
$newDate2 = $dateTime->format(get_setting("date_format"));
?>
<div id="tasks-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $add_type == "multiple" ? "" : $model_info->id; ?>" />
            <?php if ($task_id && $task_id!=0) { ?><input type="hidden" name="task_id" value="<?php echo $task_id; ?>" /><?php } ?>
            <input type="hidden" name="add_type" value="<?php echo $add_type; ?>" />
            <input type="hidden" name="mang" value="supplymang" />
            <input type="hidden" name="car_status_id" id="car_status_id" value="<?php echo $model_info->car_status_id; ?>" />

            <?php if ($is_clone) { ?>
                <input type="hidden" name="is_clone" value="1" />
            <?php } ?>


            <div class="row" style="padding-right: 5px; padding-left: 5px;">
                

                        <div class="col-md-10 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('project') . ": "; ?></strong>
                            <label><?php echo $main_task->project_title; ?></label>
                        </div>

                    </div>

                      

                
            

            

            
            <?php
            if($login_user->role_id==get_setting("pur_role_id") || $mang=="supplymang"){ 
                echo view("subtasks/sal_pur_input/purchases_input");
             }
            ?>
        

                

          

          <?php if(!$model_info->id){ ?>
            <div class="form-group">
                <div class="row">
                    <label for="status_id" class=" col-md-2"><?php echo app_lang('status'); ?></label>
                    <div class="col-md-10">
                        <?php
                        foreach ($statuses as $status) {
                            $task_status[$status->id] = $status->key_name ? app_lang($status->key_name) : $status->title;
                        }

                        if ($is_clone) {
                            echo form_dropdown("status_id", $task_status, 1, "class='select2'");
                        } else {
                            echo form_dropdown("status_id", $task_status, array($model_info->status_id), "class='select2'");
                        }
                        ?>
                    </div>
            </div>
            </div>
        <?php } ?>




        <?php

       $dateTime1 = new DateTime($model_info->exp_out_time ? $model_info->exp_out_time:'00:00', new DateTimeZone("Asia/Riyadh"));



        $act_tmp_return_date = "-";
        if ($model_info->tmp_return_date && is_date_exists($model_info->tmp_return_date)) {
            $act_tmp_return_date = format_to_date($model_info->tmp_return_date, false);
            if (get_my_local_time("Y-m-d") > $model_info->tmp_return_date && $model_info->status_id == "1") {
                $act_tmp_return_date = "<span class='text-danger'>" . $act_tmp_return_date . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $model_info->tmp_return_date && $model_info->status_id == "1") {
                $act_tmp_return_date = "<span class='text-warning'>" . $act_tmp_return_date . "</span> ";
            }
        }

     ?>





                
                    
     <h4 class="modal-title m-1" id="ajaxModalTitle" data-title="<?php echo app_lang("reserv_mang"); ?>" style="font-size: 14px; font-weight: bold; text-align: center;margin-bottom: 10px;color: #252a9f;"><?php echo app_lang("reserv_mang"); ?> </h4>
               

                   <div class="row" style="padding-right: 5px; padding-left: 5px;">

                    <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('guest_nm') . ": "; ?></strong>
                            <label ><?php echo $model_info->guest_nm; ?></label>
                        </div>
                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('city_name') . ": "; ?></strong>
                            <label><?php echo $city_name?$city_name:'__'; ?></label>
                        </div>
                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('driver_nm') . ": "; ?></strong>
                            <label><?php echo $driver_name?$driver_name:'__'; ?></label>
                        </div>



                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('car_type') . ": "; ?></strong>
                            <label><?php echo $car_type; ?></label>
                        </div>

                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('dres_number') . ": "; ?></strong>
                            <label><?php echo $model_info->dres_number?$model_info->dres_number:' ___ '; ?></label>
                        </div>

                        <?php if($model_info->service_type=='with_driver'){ $service_type_txt='سيارة بسائق';$color='#ffa500'; } 
                        elseif ($model_info->service_type=='no_driver' ) {
                            $color='#52a100';
                            $service_type_txt="سيارة بدون سائق";
                        }elseif($model_info->service_type=='deliver'){
                            $color='#ff1f2d';
                            $service_type_txt="توصيلة";
                        }else{
                            $color='#ff1fa200';
                            $service_type_txt="سائق بدون سيارة";
                        }

                        ?>

                        
                        
                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('service_type') . ": "; ?></strong>
                            <label style="color: <?php echo $color; ?>"><?php echo $service_type_txt; ?></label>
                        </div>

                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('out_date') . ": "; ?></strong>
                            <label><?php echo format_to_date($model_info->out_date, false); ?></label>
                        </div>
                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('exp_out_time') . ": "; ?></strong>
                            <label><?php echo $model_info->exp_out_time && $model_info->exp_out_time!='00:00:01'?$dateTime1->format('h:i A'):'____'; ?></label>
                        </div>

                         
                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('sales_act_return_date') . ": "; ?></strong>
                            <label><?php echo is_date_exists($model_info->sales_act_return_date) ? $model_info->sales_act_return_date : '' ?></label>
                        </div>
                       

                        

                        <div class="col-md-4 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('inv_day_count') . ": "; ?></strong>
                            <label><?php echo $model_info->inv_day_count?$model_info->inv_day_count:'___'; ?></label>
                        </div>

                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('note') . ": "; ?></strong>
                            <label><?php echo $model_info->note; ?></label>
                        </div>

                    </div>

            

          
            
           
           

            

           
            

          

            <?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => "col-md-3", "field_column" => " col-md-9")); ?> 


        </div>
    </div>

    <div class="modal-footer">
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
</div>
<?php echo form_close(); ?>

<?php
load_css(array(
        "assets/js/typeahead-js/typeahead.css"
    ));


    load_js(array(
        "assets/js/typeahead-js/typeahead.css"
    ));
     ?>
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
           }catch(er){ }
                $("#reload-kanban-button:visible").trigger("click");

                $("#save_and_show_value").append(result.save_and_show_link);

                if (window.showAddNewModal) {
                    var $taskViewLink = $("#link-of-new-view").find("a");

                    if (addType === "multiple") {
                        //add multiple tasks
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("subtasks/task_modal_form_supply"); ?>");
                        $taskViewLink.attr("data-title", multipleTaskAddText);
                        $taskViewLink.attr("data-post-last_id", result.id);
                        $taskViewLink.attr("data-post-task_id", "<?php echo $task_id; ?>");
                        $taskViewLink.attr("data-post-add_type", "multiple");
                    } else {
                        //save and show
                        $taskViewLink.attr("data-action-url", "<?php echo get_uri("subtasks/task_view_supply"); ?>");
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

        //setDatePicker("#tmp_return_date");
        setDatePicker("#act_return_date");
        //setTimePicker("#act_return_time");
        //setTimePicker("#act_out_time");
        setDatePicker("#act_out_date");

        $('#act_return_time').timepicker({
                minuteStep: 5,
                template:'dropdown',
                defaultTime: "",
                showInputs: true,
                appendWidgetTo: 'body',
                //modalBackdrop: true,
                showSeconds: false,
                showMeridian: true
            });

        $('#act_out_time').timepicker({
                minuteStep: 5,
                template:'dropdown',
                showInputs: true,
                //modalBackdrop: true,
                defaultTime: "",
                showSeconds: false,
                showMeridian: true
            });

        <?php $dateTime20 = new DateTime('now', new DateTimeZone("Asia/Riyadh"));
        $dateTime3 = new DateTime($model_info->act_out_date, new DateTimeZone("Asia/Riyadh"));
        $ddt=is_date_exists($model_info->act_out_date) ? $dateTime3->format(get_setting("date_format")) : $dateTime20->format(get_setting("date_format"));

        ?>
        var we='<?php echo $ddt; ?>';
        


        $('#act_return_date').datepicker('setStartDate', ''+we+'');

        $("#act_out_date").datepicker().on('changeDate', function(selected){
   
       startDate = new Date(selected.date.valueOf());
       //alert(startDate);
       $('#act_return_date').datepicker('setStartDate', startDate);
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

        //$("#car_status").select2({data: <?php echo json_encode($car_status_dropdown); ?>});




        
       var ser=<?php echo json_encode($car_status_dropdown); ?>;
       var isSelect=false;

        $("input[name=car_status]").typeahead({
         hint: true,
         
         minLength: 0,
        autoSelect: true,
        showHintOnFocus: true,
      }, {
         name: "states",
         source: (s = ser, function (ser, process) {
            var t = [],
               o = new RegExp(ser, "i");
               map = {};
               
            $.each(s, function (i, a) {
                map[a.text] = a;
               o.test(a.text) && t.push(a.text)
            }), process(t)
         })
    }).on('typeahead:selected', function (e, suggestion, name) {
        //alert(map[suggestion].id);
        $("#car_status_id").val(map[suggestion].id);
        isSelect=true;
        console.log(suggestion);
    });

    $("#car_status").on("change input", function () {
        if(!isSelect){
        $("#car_status_id").val('');
    }
        isSelect=false;
    
});

    /*$('input[name=car_status]').typeahead({
    source: function(query, process) {
        objects = [];
        map = {};
        var data = [{"id":1,"label":"machin"},{"id":2,"label":"truc"}] // Or get your JSON dynamically and load it into this variable
        $.each(data, function(i, object) {
            map[object.label] = object;
            objects.push(object.label);
        });
        process(objects);
    },
    updater: function(item) {
        $('hiddenInputElement').val(map[item].id);
        return item;
    }
});    */   

     $('input[name=cdxar_status]').typeahead({
        hint: false
    }, {
        source: function (query, cb) {
            objects = [];
            map = {};
            var data = [{"id":1,"label":"machin"},{"id":2,"label":"truc"}];
             $.each(data, function(i, object) {
            map[object.label] = object;
            objects.push(object['label']);
        });
             cb(objects);
        },
        name: 'addresses',
        displayKey: 'text'
    }).on('typeahead:selected', function (e, suggestion, name) {
        window.location.href = '/' + suggestion.id;
    });

    });
</script>    

<?php echo view("subtasks/get_related_data_of_project_script"); ?>