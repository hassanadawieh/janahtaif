<?php if ($view_type == "details") { ?>

    <div id="page-content" class="page-wrapper pb0 clearfix task-view-modal-body task-preview">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="page-title clearfix">
                        <h1><?php echo app_lang("task_info") . " #$model_info->sub_task_id".' - '.app_lang("supply_mang"); ?></h1>
                        <?php 
                        $edit=0;
                        if($login_user->is_admin){
                            $edit=1;
                        }else{ $edit=$can_update_maintask_after_closed==1?1:$model_info->main_task_status; } ?>
                        <?php if($edit==1){
                            if(!$is_supplier){
                            if ($can_edit_tasks) { ?>
                            <div class="title-button-group">
                                <span class="dropdown inline-block">
                                    <button class="btn btn-default dropdown-toggle caret mr0 mb0" type="button" data-bs-toggle="dropdown" aria-expanded="true">
                                        <i data-feather='settings' class='icon-16'></i> <?php echo app_lang('actions'); ?>
                                    </button>
                                    <ul class="dropdown-menu float-end" role="menu">
                                        <li role="presentation"><?php echo modal_anchor(get_uri("subtasks/task_modal_form_supply"), "<i data-feather='edit-2' class='icon-16'></i> " . app_lang('edit_task'), array("title" => app_lang("supply_mang").' - '.app_lang('edit_task'), "data-post-id" => $model_info->id, "data-post-view_type" => "details", "id" => "task-details-edit-btn", "class" => "dropdown-item")); ?></li>
                                        
                                    </ul>
                                </span>
                            </div> 
                        <?php } 
                    }
                    }
                    ?>

                    </div>

                    <div class="card-body">
                        <?php echo view("subtasks/task_view_data"); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>

<?php } else { ?>

    <script type="text/javascript">
        $(document).ready(function () {
            //store existing url to retrieve back on modal close
            if (!window.existingUrl) {
                window.existingUrl = window.location.href;
            }

            //change browser address when opening task details modal
            var browserState = {Url: "<?php echo get_uri("subtasks/task_view_supply/" . $model_info->id); ?>"};
            history.pushState(browserState, "", browserState.Url);

            //restore previous url
            $('#ajaxModal').on('hidden.bs.modal', function (e) {
                if (window.existingUrl) {
                    var browserState = {Url: window.existingUrl};
                    history.pushState(browserState, "", browserState.Url);
                    window.existingUrl = "";
                }
            });
        });
    </script>
    
    <div class="modal-body clearfix general-form task-view-modal-body">
        <?php echo view("subtasks/task_view_data"); ?>
    </div>

    <div class="modal-footer">
        <?php
        $edits=0;
        if($login_user->is_admin){
            $edits=1;
        }else{ $edits=$can_update_maintask_after_closed==1 ? 1 : $model_info->main_task_status; } 

        if($edits==1 || $can_edit_subtasks_after_closed){
        if ($can_edit_tasks) {
            //echo modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='copy' class='icon-16'></i> " . app_lang('clone_task'), array("class" => "btn btn-default float-start", "data-post-is_clone" => true, "data-post-id" => $model_info->id, "data-post-mang" => $mang, "title" => app_lang('clone_task')));
            echo modal_anchor(get_uri("subtasks/task_modal_form_supply"), "<i data-feather='edit-2' class='icon-16'></i> " . app_lang('edit_task'), array("class" => "btn btn-default", "data-post-id" => $model_info->id, "data-post-mang" => "supplymang", "title" => app_lang("supply_mang").' - '.app_lang('sub_task'). " #$model_info->sub_task_id"));
        }
    }
    
        ?>
        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>
    </div>

<?php } ?>

<?php
$task_link = anchor(get_uri("subtasks/task_view_supply/$model_info->id"), '<i data-feather="external-link" class="icon-16 task-link-btn"></i>', array("target" => "_blank", "class" => "p15"));
?>

<script type="text/javascript">
    $(document).ready(function () {

      

        //add a clickable link in task title.
        $("#ajaxModalTitle").append('<?php echo $task_link ?>');

   

       


        

      

<?php if ($view_type == "details") { ?>
            $("#task-details-edit-btn").click(function () {
                window.refreshAfterAddTask = true;
            });
<?php } ?>

        /* Dependency */

        var $dependencyTasksForm = $("#dependency_tasks_form"),
                $dependencyArea = $("#dependency-area"),
                $blockedByArea = $("#blocked-by-area"),
                $blockingArea = $("#blocking-area");

        

        $('body').on('click', '.dependency-tasks-close', function () {
            hideFromAndArea();
        });

        $('body').on('click', '#dependency-area [data-act="ajax-request"]', function () {
            setTimeout(function () {
                hideFromAndArea();
            }, 800);
        });

        //hide form clone and area
        function hideFromAndArea(type) {
            var blockedByTasksLength = $("#blocked-by-tasks").html().length,
                    blockingTasksLength = $("#blocking-tasks").html().length;

            if (type === "blocked_by" || !type) {
                fadeAndRemove($blockedByArea.find("form"));


                if (!blockedByTasksLength) {
                    fadeAndHide($blockedByArea);
                }
            }

            if (type === "blocking" || !type) {
                fadeAndRemove($blockingArea.find("form"));

                if (!blockingTasksLength) {
                    fadeAndHide($blockingArea);
                }
            }

            if (!type && !blockedByTasksLength && !blockingTasksLength) {
                fadeAndHide($dependencyArea);
            }
        }

        function fadeAndRemove($selector) {
            $selector.fadeOut(300, function () {
                $(this).remove();
            });
        }

        function fadeAndHide($selector) {
            $selector.fadeOut(300, function () {
                $(this).css('display', '')
                $(this).addClass("hide");
            });
        }

        $('[data-bs-toggle="tooltip"]').tooltip();

        //change the add checklist template button attributes on clicking
        var checklistTemplateButton = $("#checklist-template-toggle-button");
     
    });



</script>

<?php
if ($can_edit_tasks) {
  //  echo view("subtasks/update_task_info_script");
}
?>