<div id="kanban-wrapper">
    <?php
    $columns_data = array();

    $show_in_kanban = get_setting("show_in_kanban");
    $show_in_kanban_items = explode(',', $show_in_kanban);

    foreach ($tasks as $task) {

        $exising_items = get_array_value($columns_data, $task->is_closed);
        if (!$exising_items) {
            $exising_items = "";
        }

        $task_labels = "";
        $task_checklist_status = "";
        $checklist_label_color = "#6690F4";

        

        if ($task->priority_id) {
            $task_labels .= "<div class='meta float-start mr5'><span class='sub-task-icon priority-badge' data-bs-toggle='tooltip' title='" . app_lang("priority") . ": " . $task->priority_title . "' style='background: $task->priority_color'><i data-feather='$task->priority_icon' class='icon-14'></i></span></div>";
        }

       

        $task_labels_data = make_labels_view_data($task->labels_list);
        $sub_task_icon = "";
        if ($task->parent_task_id) {
            $sub_task_icon = "<span class='sub-task-icon mr5' title='" . app_lang("sub_task") . "'><i data-feather='git-merge' class='icon-14'></i></span>";
        }

        if ($task_labels_data) {
            $task_labels .= "<div class='meta float-start mr5'>$task_labels_data</div>";
        }

        $unread_comments_class = "";
       
        $batch_operation_checkbox = "";
        if ($login_user->user_type == "staff" && $can_edit_tasks && $project_id) {
            $batch_operation_checkbox = "<span data-act='batch-operation-task-checkbox' title='" . app_lang("batch_update") . "' class='checkbox-blank-sm float-end invisible'></span>";
        }

        $toggle_sub_task_icon = "";

      
        $disable_dragging = can_edit_this_task_status($task->assigned_to) ? "" : "disable-dragging";

        //custom fields to show in kanban
        $kanban_custom_fields_data = "";
        $kanban_custom_fields = get_custom_variables_data("tasks", $task->id, $login_user->is_admin);
        if ($kanban_custom_fields) {
            foreach ($kanban_custom_fields as $kanban_custom_field) {
                $kanban_custom_fields_data .= "<div class='mt5 font-12'>" . get_array_value($kanban_custom_field, "custom_field_title") . ": " . view("custom_fields/output_" . get_array_value($kanban_custom_field, "custom_field_type"), array("value" => get_array_value($kanban_custom_field, "value"))) . "</div>";
            }
        }

        $created_date = "";
        if ($task->created_date) {
            $created_date = "<div class='mt10 font-12 float-start' title='" . app_lang("created_date") . "'><i data-feather='calendar' class='icon-14 text-off mr5'></i> " . format_to_date($task->created_date, false) . "</div>";
        }

       

        $task_id = "";
       // if (in_array("id", $show_in_kanban_items)) {
            $task_id = $task->id . ". ";
       // }

        $project_name = "";
        //if (in_array("project_name", $show_in_kanban_items)) {
        //$task->project_title ? ($project_title = $task->project_title) : $project_title=" ";

            $task->project_title ? $project_name = "<div class='clearfix mt5 text-truncate'><i data-feather='grid' class='icon-14 text-off mr5'></i> " . $task->project_title . "</div>": "";
        //}

        $client_name = "";
        //if (in_array("client_name", $show_in_kanban_items)) {
            $client_name = "<div class='clearfix mt5 text-truncate'><i data-feather='briefcase' class='icon-14 text-off mr5'></i> " . $task->client_name . "</div>";
        //}
            
            if($is_reserv_mang){
                $client_name =$task->client_name;
            }else{
                $client_name =$task->project_title;
            }
        $item = $exising_items . modal_anchor(get_uri("tasks/task_view"), "<span class='avatar'>" .
                        "<img src='" . get_avatar($task->assigned_to_avatar) . "'>" .
                        "</span>" . $sub_task_icon . $task_id . $client_name . $toggle_sub_task_icon . $batch_operation_checkbox . "<div class='clearfix'>" . $created_date . "</div>" . $project_name  . $kanban_custom_fields_data .
                        $task_labels . $task_checklist_status, array("class" => "kanban-item d-block $disable_dragging $unread_comments_class", "data-id" => $task->id, "data-project_id" => $task->project_id, "data-sort" => $task->new_sort, "data-post-id" => $task->id, "title" => app_lang('task_info') . " #$task->id", "data-modal-lg" => "1"));

        $columns_data[$task->is_closed] = $item;
    
    }
    ?>

    <ul id="kanban-container" class="kanban-container clearfix">

        <?php  foreach ($columns as $column) { ?>

            <li class="kanban-col kanban-<?php echo $column["value"]; ?>" >
                <div class="kanban-col-title" style="border-bottom: 3px solid <?php echo $column["color"]; ?>;"> <?php echo $column["id"] ?> <span class="<?php echo $column["value"]; ?>-task-count float-end"></span></div>

                <div class="kanban-input general-form hide">
                    <?php
                    echo form_input(array(
                        "id" => "title",
                        "name" => "title",
                        "value" => "",
                        "class" => "form-control",
                        "placeholder" => app_lang('add_a_task')
                    ));
                    ?>
                </div>

                <div  id="kanban-item-list-<?php echo $column["value"]; ?>" class="kanban-item-list" data-status_id="<?php echo $column["value"]; ?>">
                    <?php echo get_array_value($columns_data, $column["value"]); ?>
                </div>
            </li>
        <?php } ?>

    </ul>
</div>


<?php 
                    echo modal_anchor(get_uri("tasks/post_cuses_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('update_status'), array("class" => "btn btn-default","style" => "display:none;","id" => "writeReson" , "data-post-id" => 0, "data-post-mang" => "reservmang", "title" => app_lang('update_status')));

                    ?>

<img id="move-icon" class="hide" src="<?php echo get_file_uri("assets/images/move.png"); ?>" alt="..." />

<script type="text/javascript">
    var kanbanContainerWidth = "";

    adjustViewHeightWidth = function () {

        if (!$("#kanban-container").length) {
            return false;
        }


        var totalColumns = "<?php echo $total_columns ?>";
        var columnWidth = (335 * totalColumns) + 5;

        if (columnWidth > kanbanContainerWidth) {
            $("#kanban-container").css({width: columnWidth + "px"});
        } else {
            $("#kanban-container").css({width: "100%"});
        }


        //set wrapper scroll
        if ($("#kanban-wrapper")[0].offsetWidth < $("#kanban-wrapper")[0].scrollWidth) {
            $("#kanban-wrapper").css("overflow-x", "scroll");
        } else {
            $("#kanban-wrapper").css("overflow-x", "hidden");
        }


        //set column scroll

        var columnHeight = $(window).height() - $(".kanban-item-list").offset().top - 57;
        if (isMobile()) {
            columnHeight = $(window).height() - 30;
        }

        $(".kanban-item-list").height(columnHeight);

        $(".kanban-item-list").each(function (index) {

            //set scrollbar on column... if requred
            if ($(this)[0].offsetHeight < $(this)[0].scrollHeight) {
                $(this).css("overflow-y", "scroll");
            } else {
                $(this).css("overflow-y", "hidden");
            }

        });
    };


    saveStatusAndSort = function ($item, status) {
        appLoader.show();
        adjustViewHeightWidth();

        var $prev = $item.prev(),
                $next = $item.next(),
                prevSort = 0, nextSort = 0, newSort = 0,
                step = 100000, stepDiff = 500,
                id = $item.attr("data-id"),
                project_id = $item.attr("data-project_id");

        if ($prev && $prev.attr("data-sort")) {
            prevSort = $prev.attr("data-sort") * 1;
        }

        if ($next && $next.attr("data-sort")) {
            nextSort = $next.attr("data-sort") * 1;
        }


        if (!prevSort && nextSort) {
            //item moved at the top
            newSort = nextSort - stepDiff;

        } else if (!nextSort && prevSort) {
            //item moved at the bottom
            newSort = prevSort + step;

        } else if (prevSort && nextSort) {
            //item moved inside two items
            newSort = (prevSort + nextSort) / 2;

        } else if (!prevSort && !nextSort) {
            //It's the first item of this column
            newSort = step * 100; //set a big value for 1st item
        }

        $item.attr("data-sort", newSort);


        $.ajax({
            url: '<?php echo_uri("tasks/save_task_status_kanpane") ?>',
            type: "POST",
            data: {id: id, sort: newSort, status_id: status},
            success: function (response) {
                appLoader.hide();

                var obj = JSON.parse(response);
                if (obj.data=="ok") {
                        //alert('gfffffffffffffff'+obj.status_id);
                       
                        var d = document.getElementById("writeReson");  //   Javascript
                        d.setAttribute('data-post-id' , obj.id); 
                        $("#writeReson").trigger("click");
                        
                        //appAlert.success(result.message, {duration: 10000});

                    }else{
                        var obj2 = JSON.parse(response);
                        if(!obj2.success){
                        appAlert.error(obj2.message, {duration: 10000});
                        $("#reload-kanban-button").trigger("click");
                    }

                if (isMobile()) {
                    adjustViewHeightWidth();
                }
            }
            }
        });

    };



    $(document).ready(function () {
        kanbanContainerWidth = $("#kanban-container").width();

        if (isMobile() && window.scrollToKanbanContent) {
            window.scrollTo(0, 220); //scroll to the content for mobile devices
            window.scrollToKanbanContent = false;
        }

        var isChrome = !!window.chrome && !!window.chrome.webstore;


<?php if ($login_user->user_type == "staff" || ($login_user->user_type == "client" && $can_edit_tasks)) { ?>
            $(".kanban-item-list").each(function (index) {
                var id = this.id;

                var options = {
                    animation: 150,
                    group: "kanban-item-list",
                    filter: ".disable-dragging",
                    cancel: ".disable-dragging",
                    onAdd: function (e) {
                        //moved to another column. update bothe sort and status
                        saveStatusAndSort($(e.item), $(e.item).closest(".kanban-item-list").attr("data-status_id"));

                        update_counts();
                    },
                    onUpdate: function (e) {
                        //updated sort
                        saveStatusAndSort($(e.item));

                        update_counts();
                    }
                };

                //apply only on chrome because this feature is not working perfectly in other browsers.
                if (isChrome) {
                    options.setData = function (dataTransfer, dragEl) {
                        var img = document.createElement("img");
                        img.src = $("#move-icon").attr("src");
                        img.style.opacity = 1;
                        dataTransfer.setDragImage(img, 5, 10);
                    };

                    options.ghostClass = "kanban-sortable-ghost";
                    options.chosenClass = "kanban-sortable-chosen";
                }

                Sortable.create($("#" + id)[0], options);
            });
<?php } ?>

        //add activated sub task filter class
        if ($(".custom-filter-search").val().substring(0, 1) === "#") {
            $("#kanban-container").find("[main-task-id='" + $(".custom-filter-search").val() + "']").addClass("sub-task-filter-kanban-active");
        }

        adjustViewHeightWidth();

        update_counts();

        $('[data-bs-toggle="tooltip"]').tooltip();

    });


    function update_counts() {
<?php foreach ($columns as $column) { ?>
            $('.<?php echo $column["value"]; ?>-task-count').html($('.kanban-<?php echo $column["value"]; ?>').find('.kanban-item').length);
<?php } ?>
    }

    $(window).resize(function () {
        adjustViewHeightWidth();
    });

</script>

<?php echo view("tasks/update_task_read_comments_status_script"); ?>