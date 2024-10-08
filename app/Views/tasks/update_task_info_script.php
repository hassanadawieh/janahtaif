<?php
$status_dropdown_for_update = array();
foreach ($statuses as $status) {
    $status_dropdown_for_update[] = array("id" => $status->id, "text" => $status->key_name ? app_lang($status->key_name) : $status->title);
}

$points_dropdown_for_update = array();
foreach ($points_dropdown as $key => $value) {
    $points_dropdown_for_update[] = array("id" => $key, "text" => $value);
}
?>


<script type="text/javascript">
    $(document).ready(function () {
        $('body').on('click', '[data-act=update-task-info]', function (e) {
            var $instance = $(this),
                    type = $(this).attr('data-act-type'),
                    source = "",
                    select2Option = {},
                    showbuttons = false,
                    placement = "bottom",
                    editableType = "select2",
                    datepicker = {};

            if (type === "status_id") {
                source = <?php echo json_encode($status_dropdown_for_update); ?>;
                select2Option = {data: source};
            
            } else if (type === "supplier_id") {
                source = <?php echo json_encode($suppliers_dropdown); ?>;
                select2Option = {data: source};

            } else if (type === "cls_id") {
                source = <?php echo json_encode($maintask_clsifications_dropdown); ?>;
                select2Option = {data: source};

            } else if (type === "client_id") {
                source = <?php echo json_encode($clients_dropdown); ?>;
                select2Option = {data: source};

            } else if (type === "is_closed") {
                
                source = <?php echo json_encode($is_closed_dropdown); ?>;
                select2Option = {data: source};

                
            } else if (type === "points") {
                source = <?php echo json_encode($points_dropdown_for_update); ?>;
                select2Option = {data: source};
            
            } else if (type === "labels") {
                e.stopPropagation();
                e.preventDefault();

                source = <?php echo json_encode($label_suggestions); ?>;
                showbuttons = true;
                select2Option = {data: source, multiple: true};
                placement = "bottom";
            } else if (type === "created_date") {
                editableType = "date";

                if (type === "deadline") {
                    datepicker["endDate"] = "";

                    //don't show dates before start date
<?php if (is_date_exists($model_info->created_date)) { ?>
                        datepicker["created_date"] = "<?php echo format_to_date($model_info->created_date); ?>";
<?php } ?>
                }
            } else if (type === "priority_id") {
                source = <?php echo json_encode($priorities_dropdown); ?>;
                select2Option = {data: source};
            }

            $(this).appModifier({
                actionType: editableType,
                value: $(this).attr('data-value'),
                actionUrl: '<?php echo_uri("tasks/update_task_info") ?>/' + $(this).attr('data-id') + '/' + $(this).attr('data-act-type'),
                showbuttons: showbuttons,
                datepicker: datepicker,
                select2Option: select2Option,
                placement: placement,
                onSuccess: function (response, newValue) {
                    if (response.success) {
                        if (type === "assigned_to" && response.assigned_to_avatar) {
                            $("#task-assigned-to-avatar").attr("src", response.assigned_to_avatar);

                            if (response.assigned_to_id === "0") {
                                setTimeout(function () {
                                    $instance.html("<span class='text-off'><?php echo app_lang("add") . " " . app_lang("assignee"); ?><span>");
                                }, 50);
                            }
                        }

                        if (type === "is_closed" && response.status_color) {
                            $instance.closest("span").css("background-color", response.is_closed==1?'blue':'red');
                        }

                        if (type === "milestone_id" && response.milestone_id === "0") {
                            setTimeout(function () {
                                $instance.html("<span class='text-off'><?php echo app_lang("add") . " " . app_lang("milestone"); ?><span>");
                            }, 50);
                        }

                        if (type === "points" && response.points) {
                            setTimeout(function () {
                                $instance.html(response.points);
                            }, 50);
                        }

                        if (type === "labels" && response.labels) {
                            setTimeout(function () {
                                $instance.html(response.labels);
                            }, 50);
                        }

                        if (type === "collaborators" && response.collaborators) {
                            setTimeout(function () {
                                $instance.html(response.collaborators);
                            }, 50);
                        }

                        if ((type === "created_date") && response.created_date) {
                            setTimeout(function () {
                                $instance.html(response.created_date);

                                if (type === "created_date") {
                                    $(".task-deadline-milestone-tooltip").remove();
                                }
                            }, 50);
                        }

                        if (type === "priority_id" && response.priority_pill) {
                            setTimeout(function () {
                                $instance.prepend(response.priority_pill);
                                feather.replace();
                            }, 50);
                        }

                        $("#main-task-table").appTable({newData: response.data, dataId: response.id});

                        appLoader.hide();

                        //reload gantt
                        if (typeof window.reloadGantt === "function") {
                            window.reloadGantt(true);
                        }

                        //reload kanban
                        $("#reload-kanban-button:visible").trigger("click");
                    }
                }
            });

            return false;
        });
    });
</script>