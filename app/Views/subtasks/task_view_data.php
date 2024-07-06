
<?php
$dateTime1 = new DateTime($model_info->exp_out_time ? $model_info->exp_out_time:'00:00:00', new DateTimeZone("Asia/Riyadh"));
$act_return_date_dateTime = new DateTime($model_info->act_return_date? $model_info->act_return_date:'00/00/000', new DateTimeZone("Asia/Riyadh"));
$act_out_time_dateTime = new DateTime($model_info->act_out_time? $model_info->act_out_time:'00:00', new DateTimeZone("Asia/Riyadh"));
$act_return_time_dateTime = new DateTime($model_info->act_return_time? $model_info->act_return_time:'00:00', new DateTimeZone("Asia/Riyadh"));
?>

<div class="row">


    <div class="col-lg-12 ">
        <div class="clearfix">
            <div class="container-fluid">
                <div class="row">

                    
                    <?php //if($mang=='reservmang'){ ?>

                        <?php if ($model_info->pnt_task_id) { ?>
                        <div class="col-md-6 mb5 mt5" >
                            <strong><?php echo '('.$model_info->pnt_task_id.') '.app_lang("client"). ": "; ?></strong><?php echo modal_anchor(get_uri("tasks/task_view"), $main_task?$main_task->company_name:'_____', array("title" => app_lang('task_info') . " #$model_info->pnt_task_id", "data-post-id" => $model_info->pnt_task_id, "data-modal-lg" => "1")); ?>

                                
                        </div>

                        <div class="col-md-6 mb5 mt5" >
                            <strong><?php echo app_lang("project2").' '.app_lang("main_task") . ": "; ?></strong><?php echo modal_anchor(get_uri("tasks/task_view"), $main_task?$main_task->project_title.'('.$model_info->pnt_task_id.')':'_____', array("title" => app_lang('task_info') . " #$model_info->pnt_task_id", "data-post-id" => $model_info->pnt_task_id, "data-modal-lg" => "1")); ?>

                                
                        </div>
                        <?php } ?>
                        <?php if($mang=='reservmang'){ ?>
                        <div class="col-md-6 mb5 mt5">
                        <strong><?php echo app_lang('guest_nm') . ": "; ?></strong>
                        <label><?php echo $model_info->guest_nm; ?></label>
                        <?php //echo get_update_task_info_anchor_data($model_info, "client", $can_edit_tasks); ?>
                    </div>


                    <div class="col-md-6 mb5 mt5">
                        <strong><?php echo app_lang('guest_phone') . ": "; ?></strong>
                        <label><?php echo $model_info->guest_phone?$model_info->guest_phone:' ___ '; ?></label>
                         <?php //echo get_update_task_info_anchor_data($model_info, "city", $can_edit_tasks); ?>
                    </div>
                         <?php } ?>
                    <?php //}else { ?>
                        <!--<div class="col-md-12 mb5 mt5" >-->
                            <strong><?php //echo app_lang("project2").' '.app_lang("main_task") . ": "; ?></strong><?php //echo modal_anchor(get_uri("tasks/task_view"), $main_task->project_title.'('.$model_info->pnt_task_id.')', array("title" => app_lang('task_info') . " #$model_info->pnt_task_id", "data-post-id" => $model_info->pnt_task_id, "data-modal-lg" => "1")); ?>

                                
                        <!--</div>-->

                        <?php //} ?>

                                  

                    <div class="col-md-6 mb5 mt5">
                        <strong><?php echo app_lang('car_expens') . ": "; ?></strong>
                        <label><?php echo $model_info->car_expens?$model_info->car_expens:' ___ '; ?></label>
                        <?php //echo get_update_task_info_anchor_data($model_info, "priority", $can_edit_tasks); ?>
                    </div>

                    <div class="col-md-6 mb5 mt5" >
                        <strong><?php echo app_lang('car_expens_stmnt') . ": "; ?></strong>
                        <label><?php echo $model_info->car_expens_stmnt?$model_info->car_expens_stmnt:' ___ '; ?></label>
                        
                        <?php //echo get_update_task_info_anchor_data($model_info, "created_date", $can_edit_tasks); ?>
                    </div>
                    


                    

                    <div class="col-md-6" >
                        <strong><?php echo app_lang('priority') . ": "; ?></strong> <?php 

                        $priority = "<span class='sub-task-icon priority-badge' style='background: $model_info->priority_color'><i data-feather='$model_info->priority_icon' class='icon-14'></i></span> $model_info->priority_title";
                 
                        echo $can_edit_tasks ? js_anchor($model_info->priority_id ? $priority : "<span class='text-off'>" . app_lang("add") . " " . app_lang("priority") . "<span>", array('title' => "", "class" => "", "data-id" => $model_info->id, "data-value" => $model_info->priority_id, "data-act" => "update-task-info", "data-act-type" => "priority_id")) : ($model_info->priority_id ? $priority : ""); ?>
                        <?php //echo get_update_task_info_anchor_data($model_info, "created_date", $can_edit_tasks); ?>
                    </div>

                    <div class="col-md-6 mb2 mt5" >
                        <strong><?php echo app_lang('created_date') . ": "; ?></strong>
                        <label dir="ltr"><?php echo $model_info->created_at; ?></label>
                        
                        <?php //echo get_update_task_info_anchor_data($model_info, "created_date", $can_edit_tasks); ?>
                    </div>

                    <div class="col-md-6  mb2 mt2" >
                        <strong><?php echo app_lang('task_status') . ": "; ?></strong>
                        <a href="#" style="background-color: <?=$status_color?>" class="badge" ><?php echo $task_status; ?></a>
                        <?php //echo get_update_task_info_anchor_data($model_info, "created_date", $can_edit_tasks); ?>
                    </div>


                     </div>
            </div>
        </div>
    </div>

    <?php

    $act_return_date_text = "-";
        if ($model_info->act_return_date && is_date_exists($model_info->act_return_date)) {
            $act_return_date_text = $model_info->act_return_date;
            if (get_my_local_time("Y-m-d") > $model_info->act_return_date /*&& $data->status_id != "1"*/) {
                $act_return_date_text = "<span class='text-danger'>" . $act_return_date_text . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $model_info->act_return_date /*&& $data->status_id != "3"*/) {
                $act_return_date_text = "<span class='text-warning'>" . $act_return_date_text . "</span> ";
            }
        }

        $act_tmp_return_date = "-";
        if ($model_info->tmp_return_date && is_date_exists($model_info->tmp_return_date)) {
            $act_tmp_return_date = $model_info->tmp_return_date;//format_to_date($model_info->tmp_return_date, false);
            if (get_my_local_time("Y-m-d") > $model_info->tmp_return_date && $model_info->status_id == "1") {
                $act_tmp_return_date = "<span class='text-danger'>" . $act_tmp_return_date . "</span> ";
            } else if (get_my_local_time("Y-m-d") == $model_info->tmp_return_date && $model_info->status_id == "1") {
                $act_tmp_return_date = "<span class='text-warning'>" . $act_tmp_return_date . "</span> ";
            }
        }

     ?>





    <div class="col-lg-6 mb15">
        <div class="clearfix">
            <div class="container-fluid px-1">
                <div class="row px-1">
                    <div class="col-md-12 mb5 mt5 text-wrap" style="text-align:center; margin-top: 10px;">
                    <a href="#" style="min-width: 50%;background: linear-gradient(199deg, rgb(232 232 232) 45%, rgb(244 244 244) 100%);font-size: 14px;border-radius: 20px;box-shadow: 0px 0px 3px 0px #929292;font-weight: bold;color: #000000;padding: 5px 10px 7px 10px" class="badge" > <?php echo app_lang("reserv_mang"); ?></a>
                </div>

                     <div class="col-md-12 mb5 mt5 text-wrap" style="text-align:center;">
                        <strong style="color:#6683bd;"><?php echo app_lang('created_by') . ": "; ?></strong>
                        <label><?php echo $model_info->assigned_to_user?$model_info->assigned_to_user:' ___ '; ?></label>
                    </div>

                    <?php if($mang!='reservmang'){ ?>
                    <div class="col-md-12" style="text-align:center;">
                        <strong><?php echo app_lang('task_status') . ": "; ?></strong>
                        <a href="#" style="background-color: <?=$status_color2?>" class="badge" ><?php echo $task_status2; ?></a>
                        <?php //echo get_update_task_info_anchor_data($model_info, "created_date", $can_edit_tasks); ?>
                    </div>
                <?php } ?>
                        <div class="col-md-6 mb5 mt5  text-wrap">
                            <strong><?php echo app_lang('driver_nm') . ": "; ?></strong>
                            <label><?php echo $model_info->driver_name; ?></label>
                        </div>


                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('car_type') . ": "; ?></strong>
                            <label><?php echo $model_info->mycar_type; ?></label>
                        </div>

                        <div class="col-md-6 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('city') . ": "; ?></strong>
                            <label><?php echo $model_info->city_name; ?></label>
                        </div>


                        

                        <?php if($model_info->service_type=='with_driver'){ $color='#ffa500'; } 
                        elseif ($model_info->service_type=='no_driver' ) {
                            $color='#52a100';
                        }else{
                            $color='#ff1f2d';
                        }

                        ?>
                        <div class="col-md-6 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('service_type') . ": "; ?></strong>
                            <label style="color: <?php echo $color; ?>"><?php echo $service_type_txt; ?></label>
                        </div>
                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('inv_day_count') . ": "; ?></strong>
                            <label><?php echo $model_info->inv_day_count?$model_info->inv_day_count:' __ '; ?></label>
                        </div>

                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('out_date') . ": "; ?></strong>
                            <label><?php echo is_date_exists($model_info->out_date)?$model_info->out_date.' ':' __ '; ?>

                        </div>
                        
                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('tmp_return_date') . ": "; ?></strong>
                            <label><?php echo $act_tmp_return_date; ?></label>
                        </div>

                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('act_return_date') . ": "; ?></strong>
                            <label><?php echo is_date_exists($model_info->sales_act_return_date)?$model_info->sales_act_return_date.' ':' __ '; ?>

                        </div>

                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('exp_out_time') . ": "; ?></strong>
                            <label><?php echo $model_info->exp_out_time && $model_info->exp_out_time!='00:00:01'?$dateTime1->format('h:i A'):' __'; ?></label>
                        </div>
                        

                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('note') . ": "; ?></strong>
                            <label><?php echo $model_info->note; ?></label>
                        </div>

                   


                   


                   

             

                    
                </div>
            </div>
        </div>
    </div>


    

    <div class="col-lg-6 order-lg-last">
        <div class="clearfix">
            <div class="container-fluid">
                <div class="row" style="padding-right: 5px; padding-left: 5px;">
                    <div class="col-md-12 mb5 mt5 text-wrap" style="text-align:center; margin-top: 10px;">
                    <a href="#" style="min-width: 50%;background: linear-gradient(199deg, rgb(232 232 232) 45%, rgb(244 244 244) 100%);font-size: 14px;border-radius: 20px;box-shadow: 0px 0px 3px 0px #929292;font-weight: bold;color: #000000;padding: 5px 10px 7px 10px" class="badge" ><?php echo app_lang("supply_mang"); ?></a>
                </div>

                  <div class="col-md-12 mb5 mt5 text-wrap" style="text-align:center;">
                            <strong style="color:#6683bd;"><?php echo app_lang('updated_by') . ": "; ?></strong>
                            <label><?php echo $model_info->updated_by_nm?$model_info->updated_by_nm:'___'; ?></label>
                        </div>

                    <?php if($mang=='reservmang'){ ?>
                    <div class="col-md-12" style="text-align:center;">
                        <strong><?php echo app_lang('task_status') . ": "; ?></strong>
                        <a href="#" style="background-color: <?=$status_color2?>" class="badge" ><?php echo $task_status2; ?></a>
                        <?php //echo get_update_task_info_anchor_data($model_info, "created_date", $can_edit_tasks); ?>
                    </div>
                <?php } ?>


                    <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('supplier') . ": "; ?></strong>
                            <label><?php echo $model_info->supplier_name?$model_info->supplier_name:' ___ ' ; ?></label>
                        </div>


                        <div class="col-md-6 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('car_number') . ": "; ?></strong>
                            <label><?php echo $model_info->car_number?$model_info->car_number:' ___ '; ?></label>
                        </div>
                       


                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('act_return_date&time') . ": "; ?></strong>
                            <label dir="ltr"><?php echo $act_return_date_text.' '; ?> <?php echo $model_info->act_return_time && $model_info->act_return_time!='00:00:01'?$act_return_time_dateTime->format('h:i A'):' __ '; ?></label>
                        </div>
                       
                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('act_out_date&time') . ": "; ?></strong>
                            <label dir="ltr"> <?php echo is_date_exists($model_info->act_out_date)?$model_info->act_out_date.' ':' __ '; ?> <?php echo $model_info->act_out_time && $model_info->act_out_time!='00:00:01'?$act_out_time_dateTime->format('h:i A').' ':' __ '; ?></label>
                        </div>

                       
                        <div class="col-md-6 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('day_count') . ": "; ?></strong>
                            <label><?php echo $model_info->day_count?$model_info->day_count:' __ '; ?></label>
                        </div>
                         <div class="col-md-6 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('dres_number') . ": "; ?></strong>
                            <label><?php echo $model_info->dres_number?$model_info->dres_number:' ___ '; ?></label>
                        </div>

                        <div class="col-md-6 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('amount') . ": "; ?></strong>
                            <label><?php echo $model_info->amount?$model_info->amount:' ___ '; ?></label>
                        </div>


                        

                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('car_status') . ": "; ?></strong>
                            <label><?php echo $model_info->car_status?$model_info->car_status:' __ '; ?></label>
                        </div>
                        

                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('rec_inv_status') . ": "; ?></strong>
                            <label ><?php echo $rec_inv_status; ?></label>
                        </div>



                        <div class="col-md-12 mb5 mt5 text-wrap">
                            <strong><?php echo app_lang('note') . ": "; ?></strong>
                            <label><?php echo $model_info->note_2; ?></label>
                        </div>

                    
                    

                   
                   

                    

                    

                    <?php app_hooks()->do_action('app_hook_task_view_right_panel_extension'); ?>
                </div>
            </div>
        </div>
    </div>
    
</div>
