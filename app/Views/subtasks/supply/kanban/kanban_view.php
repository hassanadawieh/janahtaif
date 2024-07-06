<div id="kanban-wrapper">
    <?php
    $columns_data = array();

    $show_in_kanban = get_setting("show_in_kanban");
    $show_in_kanban_items = explode(',', $show_in_kanban);
    $ci = new \App\Controllers\Security_Controller(false);
    foreach ($tasks as $task) {
        $exising_items = get_array_value($columns_data, $task->dynamic_status_id);
        if (!$exising_items) {
            $exising_items = "";
        }
        $main_task = $ci->Tasks_model->get_details(array("id" => $task->pnt_task_id))->getRow();

        
        $task_labels = "";
        $task_checklist_status = "";
        $checklist_label_color = "#6690F4";

       
            $checklist_label_color = "#E18A00";
       


        $sub_task_icon = "";
       

       

        $unread_comments_class = "";
       
        $batch_operation_checkbox = "";


        $toggle_sub_task_icon = "";

       
            $toggle_sub_task_icon = "<span class='filter-sub-task-kanban-button clickable float-end ml5' title='" . app_lang("show_sub_tasks") . "' main-task-id= '#$task->id'><i data-feather='filter' class='icon-14'></i></span>";
        

        $disable_dragging = can_edit_this_task_status($task->created_by) ? "" : "disable-dragging";

        //custom fields to show in kanban
        $kanban_custom_fields_data = "";
        $kanban_custom_fields = get_custom_variables_data("tasks", $task->id, $login_user->is_admin);
        if ($kanban_custom_fields) {
            foreach ($kanban_custom_fields as $kanban_custom_field) {
                $kanban_custom_fields_data .= "<div class='mt5 font-12'>" . get_array_value($kanban_custom_field, "custom_field_title") . ": " . view("custom_fields/output_" . get_array_value($kanban_custom_field, "custom_field_type"), array("value" => get_array_value($kanban_custom_field, "value"))) . "</div>";
            }
        }

        $act_return_date = "";
        if ($task->act_return_date && $task->act_return_date!="0000-00-00") {
            $act_return_date = "<div class='mt10 font-12 float-start' title='" . app_lang("act_return_date") . "'><i data-feather='calendar' class='icon-14 text-off mr5'></i> " . format_to_date($task->act_return_date, false) . "</div>";
        }

        $out_date = "";
        if ($task->act_out_date && $task->act_out_date!="0000-00-00") {
            $out_date = "<div class='mt10 font-12 float-end' title='" . app_lang("tmp_return_date") . "'><i data-feather='calendar' class='icon-14 text-off mr5'></i> " . format_to_date($task->act_out_date, false) . "</div>";
        }

        $task_id = "";
        if (in_array("id", $show_in_kanban_items)) {
            $task_id = $task->id . ". ";
        }

        

        $supplier_name = "";
        if ($task->supplier_name) {
            $supplier_name = "<div class='clearfix mt5 text-truncate'><i data-feather='briefcase' class='icon-14 text-off mr5'></i> " . $task->supplier_name . "</div>";
        }

        $item =  $exising_items .modal_anchor(get_uri("subtasks/task_view_supply"), "<span class='avatar'>" .
                        "<img src='" . get_avatar($task->assigned_to_avatar) . "'>" .
                        "</span>" . $task->sub_task_id.' '. $main_task->project_title  . "<div class='clearfix'>" . $act_return_date . $out_date . "</div>". $supplier_name . $kanban_custom_fields_data .
                        $task_labels , array("class" => "kanban-item d-block $disable_dragging $unread_comments_class","data-sort" => $task->new_sort, "data-id" => $task->id, "data-project_id" => $task->pnt_task_id, "data-post-id" => $task->id, "data-post-mang" => "supplymang", "title" => app_lang("supply_mang").' - '.app_lang('task_info') . " #$task->sub_task_id", "data-modal-lg" => "1"));

        $columns_data[$task->dynamic_status_id] = $item;
    }
    ?>

    <ul id="kanban-container" class="kanban-container clearfix">

        <?php foreach ($columns as $column) { ?>
            <li class="kanban-col kanban-<?php echo $column->id; ?>" >
                <div class="kanban-col-title" style="border-bottom: 3px solid <?php echo $column->color ? $column->color : "#2e4053"; ?>;"> <?php echo $column->key_name ? app_lang($column->key_name) : $column->title; ?> <span class="<?php echo $column->id; ?>-task-count float-end"></span></div>

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

                <div  id="kanban-item-list-<?php echo $column->id; ?>" class="kanban-item-list" data-status_id="<?php echo $column->id; ?>">
                    <?php echo get_array_value($columns_data, $column->id); ?>
                </div>
            </li>
        <?php } ?>

    </ul>
</div>

<?php 
                    echo modal_anchor(get_uri("subtasks/post_cuses_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('update_status'), array("class" => "btn btn-default","style" => "display:none;","id" => "writeReson" , "data-post-id" => 0, "data-post-mang" => "reservmang", "title" => app_lang('update_status')));

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
            url: '<?php echo_uri("subtasks/save_task_status_kanpane") ?>',
            type: "POST",
            data: {id: id, sort: newSort, status_id: status,mang:'supplymang'},
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
                        //alert(obj2.message);
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
            $('.<?php echo $column->id; ?>-task-count').html($('.kanban-<?php echo $column->id; ?>').find('.kanban-item').length);
<?php } ?>
    }

    $(window).resize(function () {
        adjustViewHeightWidth();
    });

</script>

<?php echo view("tasks/update_task_read_comments_status_script"); ?>