<?php
$progress = 0;
if ($total_sub_tasks) {
    $progress = round($completed_sub_tasks / $total_sub_tasks * 100);
}
?>

<div class="row">
    <div class="col-lg-4 order-lg-last">
        <div class="clearfix">
            <div class="container-fluid">
                <div class="row">
                    <div  class="col-md-12 mb15 task-title-right d-none">
                        <strong><?php echo $model_info->title; ?></strong>
                    </div>

                    <div class="d-flex m0">
                        <div class="flex-shrink-0">
                            <span class="avatar avatar-sm">
                                <img id="task-assigned-to-avatar" src="<?php echo get_avatar("$model_info->assigned_to_avatar"); ?>" onerror="this.onerror=null;this.src='<?php echo get_avatar("_file613bc2b05fbe1-avatar.jpg"); ?>';" style="max-width: 38px;" alt="..." />
                            </span>
                        </div>
                        
                    </div>

                    <?php if ($total_sub_tasks) { ?>
                        <div class="col-md-12 mb15 mt15">
                            <span class=""><?php echo $completed_sub_tasks . "/" . $total_sub_tasks; ?> <?php echo app_lang("sub_tasks_completed"); ?></span>
                            <div class="progress mt5" style="height: 6px;" title="<?php echo $progress; ?>%">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $progress; ?>%;" aria-valuenow="<?php echo $progress; ?>%" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    <?php } ?>

                   

                   

                    <div class="col-md-12 mb15 mt15">
                        <strong><?php echo app_lang('cls_title') . ": "; ?></strong> <?php echo get_update_task_info_anchor_data($model_info, "cls_id", $can_edit_tasks); ?>
                    </div>

                    <!--<div class="col-md-12 mb15 mt15">
                        <strong><?php //echo app_lang('status') . ": "; ?></strong> <?php
                                //echo "<span class='badge' style='background:$model_info->is_closed==1 ? blue : red '>" . get_update_task_info_anchor_data($model_info, "is_closed", $can_edit_tasks) . "</span>";
                                ?>
                    </div>-->


                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang('task_date') . ": "; ?></strong> <?php echo get_update_task_info_anchor_data($model_info, "created_date", $can_edit_tasks); ?>
                    </div>
                    <?php if ($is_reserv_mang) { ?>
                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang('christening_number') . ": "; ?></strong> <?php echo $model_info->christening_number?$model_info->christening_number: '    --    ' ?>
                    </div>
                    <div class="col-md-12 mb15">
                        <strong><?php echo app_lang('invoice_number') . ": "; ?></strong> <?php echo $model_info->invoice_number?$model_info->invoice_number: '    --    ' ?>
                    </div>
                <?php } ?>

                   


                    

                    
                    

                   
                   

                    

                    

                    <?php app_hooks()->do_action('app_hook_task_view_right_panel_extension'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8 mb15">
        <div class="clearfix">
            <div class="container-fluid">
                <div class="row">
                    <!--<div  class="col-md-12 mb15 task-title-left">
                        <strong><?php // echo app_lang('client') . ": "; ?></strong> <?php //echo get_update_task_info_anchor_data($model_info, "client", $can_edit_tasks); ?>
                    </div>-->

                   

                    <?php if ($model_info->description) { ?>
                        <div class="col-md-12 mb15 text-wrap">
                            <?php echo $model_info->description ? nl2br(link_it($model_info->description)) : ""; ?>
                        </div>
                    <?php } ?>
                    <?php if ($is_reserv_mang) { ?>
                    <div class="col-md-12 mb15">
                        <?php if($model_info->client_id!=0){ ?><strong><?php echo app_lang('client') . ": "; ?> </strong> <?php echo $model_info->company_name; } ?>
                    </div>
                    <?php } ?>

                    <div class="col-md-12 mb15">
                        <?php if($model_info->project_title!=0){ ?><strong><?php echo app_lang('project') . ": "; ?> </strong> <?php echo $model_info->project_title; } ?>
                    </div>
                    <div class="col-md-12 mb5" >
                        <strong><?php echo app_lang('task_status') . ": "; ?></strong>
                        
                        <?php
                        $status_color=$model_info->is_closed==1 ? '#4a9d27':'#e50f16bd';

                         echo js_anchor($model_info->is_closed==1 ? app_lang('open'):app_lang('closed'), array("style" => "background-color: $status_color", "class" => "badge", "data-id" => $model_info->id, "data-value" => $model_info->is_closed, "data-act" => "update-mytask-status")); 
                         ?>
                    </div>

                    <?php

                    if (count($custom_fields_list)) {
                        foreach ($custom_fields_list as $data) {
                            if ($data->value) {
                                ?>
                                <div class="col-md-12 mb15">
                                    <strong><?php echo $data->title . ": "; ?> </strong> <?php echo view("custom_fields/output_" . $data->field_type, array("value" => $data->value)); ?>
                                </div>
                                <?php
                            }
                        }
                    }
                    ?>

                    <!--checklist-->
                   <div class="checklist-items" id="checklist-items" >

                        </div>
                    <!--Sub tasks-->
                    <?php echo form_open(get_uri("tasks/save_sub_task"), array("id" => "sub_task_form", "class" => "general-form", "role" => "form")); ?>
                    <div class="col-md-12 mb15 b-t">
                        <div class="pb10 pt10">
                            <?php if ($is_reserv_mang) { ?>
                            <strong><?php echo app_lang("sub_tasks").' - '.app_lang("reserv_mang"); ?></strong>
                        <?php } else{ ?>
                            <strong><?php echo app_lang("sub_tasks").' - '.app_lang("supply_mang"); ?></strong>

                        <?php } ?>
                        </div>
                        <input type="hidden" name="project_id" value="<?php echo $model_info->project_id; ?>" />
                        <input type="hidden" name="parent_task_id" value="<?php echo $task_id; ?>" />
                        <input type="hidden" name="milestone_id" value="<?php echo $model_info->milestone_id; ?>" />

                        <div class="checklist-items" id="sub-tasks" style="max-height: 320px;overflow-x: auto;">

                        </div>
                       
                    </div>
                    <?php echo form_close(); ?>

                  

                    <!--Task comment section-->
                   
                </div>
            </div>
        </div>
    </div>
</div>

