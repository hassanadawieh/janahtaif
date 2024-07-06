<div id="page-content" class="page-wrapper pb0 clearfix">

    <ul class="nav nav-tabs bg-white title" role="tablist" style="padding-right: 10px;">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("sub_tasks"); ?></h4></li>

        <?php echo view("subtasks/tabs", array("active_tab" => "tasks_kanban", "selected_tab" => "", "task_id" => $task_id)); ?>       

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php
                if ($can_create_tasks) {
                    //echo modal_anchor(get_uri("subtasks/import_tasks_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_tasks'), array("class" => "btn btn-default", "title" => app_lang('import_tasks')));

                    /*task_modal_form in Projects Controller*/
                    if($main_task){
                    if($main_task->is_closed==1){
                    echo modal_anchor(get_uri("subtasks/task_modal_form/".$task_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_multiple_tasks'), array("class" => "btn btn-outline-light", "title" => app_lang('add_multiple_tasks'), "data-post-task_id" => $task_id, "data-post-add_type" => "multiple", "data-post-mang" => "reservmang"));
                
                echo modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-outline-light", "title" => app_lang('add_task'), "data-post-task_id" => $task_id, "data-post-mang" => "reservmang"));
            }else {
                echo modal_anchor("", "<i data-feather='plus-circle' class='icon-16'></i> المهمة الرئيسية مغلقة" , array("class" => "btn btn-outline-light", "title" => app_lang('add_task')));
            }
        }else{
            echo modal_anchor(get_uri("subtasks/task_modal_form/".$task_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_multiple_tasks'), array("class" => "btn btn-outline-light", "title" => app_lang('add_multiple_tasks'), "data-post-task_id" => $task_id, "data-post-add_type" => "multiple", "data-post-mang" => "reservmang"));
                
                echo modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-outline-light", "title" => app_lang('add_task'), "data-post-task_id" => $task_id, "data-post-mang" => "reservmang"));
        }
        
                }
                ?>
            </div>
        </div>
    </ul>
    <div class="bg-white kanban-filters-container">
        <div class="row">
            <div class="col-md-1 col-xs-2">
                <button class="btn btn-default" id="reload-kanban-button"><i data-feather="refresh-cw" class="icon-16"></i></button>
            </div>
            <div id="kanban-filters" class="col-md-11 col-xs-10"></div>
        </div>
    </div>

    <div id="load-kanban"></div>
</div>

<script>
    $(document).ready(function () {
        window.scrollToKanbanContent = true;
    });
</script>

<?php //echo view("subtasks/batch_update/batch_update_script"); ?>
<?php echo view("subtasks/kanban/all_tasks_kanban_helper_js"); ?>
<?php echo view("subtasks/quick_filters_helper_js"); ?>