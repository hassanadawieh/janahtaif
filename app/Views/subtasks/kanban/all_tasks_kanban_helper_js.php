<?php
$statuses = array();
foreach ($task_statuses as $status) {
    $is_selected = true;

    /*if (isset($selected_status_id) && $selected_status_id) {
        //if there is any specific status selected, select only the status.
        if ($selected_status_id == $status->id) {
            $is_selected = true;
        }
    } else if ($status->key_name != "done") {
        $is_selected = true;
    }*/

    $statuses[] = array("text" => ($status->key_name ? app_lang($status->key_name) : $status->title), "value" => $status->id, "isChecked" => $is_selected);
}

$service_type_dropdown = array(array("id" => "", "text" => "- ". app_lang("service_type"). " -"));
        $service_type_dropdown[] = array("id" => "with_driver", "text" => "سيارة بسائق");
        $service_type_dropdown[] = array("id" => "no_driver", "text" => "سيارة بدون سائق");
        $service_type_dropdown[] = array("id" => "deliver", "text" => "توصيلة");
?>
<script type="text/javascript">

   
    $(document).ready(function () {

        var scrollLeft = 0;
        $("#kanban-filters").appFilters({
            source: '<?php echo_uri("subtasks/all_tasks_kanban_data/" . $task_id)?>',
            //source: '<?php //echo_uri("subtasks/all_tasks_kanban_data/".$task_id) ?>',
            targetSelector: '#load-kanban',
            reloadSelector: "#reload-kanban-button",
            search: {name: "search"},
            filterDropdown: [
                {name: "specific_user_id", class: "w200", options: <?php echo $team_members_dropdown; ?>},
                {name: "service_type", class: "w150", options: <?php echo json_encode($service_type_dropdown); ?>},
                {name: "priority_id", class: "w150", options: <?php echo $priorities_dropdown; ?>},
                
                <?php echo $custom_field_filters; ?>
            ],
            multiSelect: [
                {
                    name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>
                }
            ],
            
            beforeRelaodCallback: function () {
                scrollLeft = $("#kanban-wrapper").scrollLeft();
            },
            afterRelaodCallback: function () {
                setTimeout(function () {
                    $("#kanban-wrapper").animate({scrollLeft: scrollLeft}, 'slow');
                }, 500);
                //hideBatchTasksBtn();
            }
        });

    });

</script>

<?php echo view("subtasks/sub_tasks_helper_js"); ?>