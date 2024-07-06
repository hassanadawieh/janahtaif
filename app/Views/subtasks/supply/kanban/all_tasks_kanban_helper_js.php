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
?>
<script type="text/javascript">

    <?php 
        $f_dropdown = array(array("id" => "", "text" => "- ".app_lang('filters')." -"));
        $f_dropdown[] = array("id" => "no_supplier", "text" => app_lang("tasks_without_supplier"), "isSelected" => $filter=="no_supplier"?true:false);

        $f_dropdown[] = array("id" => "wait_inv", "text" => app_lang("tasks_without_supplier_invoice"), "isSelected" => $filter=="wait_inv"?true:false);

        $f_dropdown[] = array("id" => "no_act_return_date", "text" => app_lang("tasks_without_return_date"), "isSelected" => $filter=="no_act_return_date"?true:false);

        $f_dropdown[] = array("id" => "no_act_out_time", "text" => app_lang("tasks_without_out_time"), "isSelected" => $filter=="no_act_out_time"?true:false);

        $f_dropdown[] = array("id" => "24houer", "text" => app_lang("subtasks_to_go"), "isSelected" => $filter=="24houer"?true:false);


        ?>

    $(document).ready(function () {

        var scrollLeft = 0;
        $("#kanban-filters").appFilters({
            source: '<?php echo_uri("subtasks/all_tasks_kanban_supply_data/" . $task_id) ?>',
            targetSelector: '#load-kanban',
            reloadSelector: "#reload-kanban-button",
            search: {name: "search"},
            filterDropdown: [
                {name: "specific_user_id", class: "w150", options: <?php echo $team_members_dropdown; ?>},
                {name: "supplier_id", class: "w150", options: <?php echo $suppliers_dropdown; ?>},
                {name: "filter", class: "w200",options: <?php echo json_encode($f_dropdown) ; ?>},
                //{name: "mang", visible: false,value: <?php //echo $mang ; ?>},
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