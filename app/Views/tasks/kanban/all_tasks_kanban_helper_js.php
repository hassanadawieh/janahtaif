<?php $statuses = array();
//for ($i=0;$i<=1;$i++) {
    $is_selected = true;

    /*if (isset($selected_status_id) && $selected_status_id) {
        //if there is any specific status selected, select only the status.
        if ($selected_status_id == $status->id) {
            $is_selected = true;
        }
    } else if ($status->key_name != "done") {
        $is_selected = true;
    }*/

    $statuses[] = array("text" => "Open", "value" => 1, "isChecked" => $is_selected);
    $statuses[] = array("text" => "Closed", "value" => 2, "isChecked" => $is_selected);
//}
?>
<script type="text/javascript">

    $(document).ready(function () {

        var scrollLeft = 0;
        $("#kanban-filters").appFilters({
            source: '<?php echo_uri("tasks/all_tasks_kanban_data") ?>',
            targetSelector: '#load-kanban',
            reloadSelector: "#reload-kanban-button",
            search: {name: "search"},
            filterDropdown: [
                {name: "specific_user_id", class: "w150", options: <?php echo $team_members_dropdown; ?>},
                {name: "client_id", class: "w150", options: <?php echo $clients_dropdown; ?>},
                
                {name: "project_id", class: "w200", options: <?php echo $projects_dropdown; ?>}, //reset milestone on changing of project  
                //{name: "quick_filter", class: "w200", showHtml: true, options: <?php //echo view("tasks/quick_filters_dropdown"); ?>},
                 <?php echo $custom_field_filters; ?>
            ],
            multiSelect: [
                {
                    name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>
                }
            ],
            /*singleDatepicker: [{name: "deadline", defaultText: "<?php //echo app_lang('created_date') ?>",
                    options: [
                    {value: "expired", text: "<?php //echo app_lang('created_date') ?>"},
                        
                    ]}],*/
            beforeRelaodCallback: function () {
                scrollLeft = $("#kanban-wrapper").scrollLeft();
            },
            afterRelaodCallback: function () {
                setTimeout(function () {
                    $("#kanban-wrapper").animate({scrollLeft: scrollLeft}, 'slow');
                }, 500);
                hideBatchTasksBtn();
            }
        });

    });

</script>

<?php echo view("tasks/sub_tasks_helper_js"); ?>