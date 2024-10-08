<div class="card mb0">
    <div class="tab-title clearfix">
        <h4><?php echo app_lang('tasks') . " " . app_lang('kanban'); ?></h4>
        <div class="title-button-group">
            <?php
            if ($login_user->user_type == "staff" && $can_edit_tasks) {
                echo modal_anchor("", "<i data-feather='edit' class='icon-16'></i> " . app_lang('batch_update'), array("class" => "btn btn-info text-white hide batch-update-btn", "title" => app_lang('batch_update'), "data-post-project_id" => $supplier_id));
                echo js_anchor("<i data-feather='check-square' class='icon-16'></i> " . app_lang("cancel_selection"), array("class" => "hide btn btn-default batch-cancel-btn"));
            }
            if ($can_create_tasks) {
                echo modal_anchor(get_uri("suppliers/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_multiple_tasks'), array("class" => "btn btn-outline-light", "title" => app_lang('add_multiple_tasks'), "data-post-supplier_id" => $supplier_id, "data-post-add_type" => "multiple"));
                
                echo modal_anchor(get_uri("suppliers/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-outline-light", "title" => app_lang('add_task'), "data-post-supplier_id" => $supplier_id));
            }
            ?>
        </div>
    </div>
    <div class="bg-white kanban-filters-container">
        <div class="row">
            <div class="col-md-1 col-xs-2">
                <button class="btn btn-default" id="reload-kanban-button"><i data-feather="refresh-cw" class="icon-16"></i></button>
            </div>
            <div id="kanban-filters" class="col-md-11 col-xs-10"></div>
        </div>
    </div>
</div>
<div id="load-kanban"></div>


<?php
//prepare status dropdown list
//select the non completed tasks for team members by default
//show all tasks for client by default.
$statuses = array();
foreach ($task_statuses as $status) {
    $is_selected = false;
    if ($login_user->user_type == "staff") {
        if ($status->key_name != "done") {
            $is_selected = true;
        }
    }

    $statuses[] = array("text" => ($status->key_name ? app_lang($status->key_name) : $status->title), "value" => $status->id, "isChecked" => $is_selected);
}
?>
<script type="text/javascript">

    $(document).ready(function () {
        var filterDropdown = [];

        if ("<?php echo $login_user->user_type ?>" == "staff") {
            filterDropdown = [
                //{name: "specific_user_id", class: "w200", options: <?php //echo $assigned_to_dropdown; ?>},
                //{name: "milestone_id", class: "w200", options: <?php //echo $milestone_dropdown; ?>},
                {name: "priority_id", class: "w200", options: <?php echo $priorities_dropdown; ?>},
                //{name: "quick_filter", class: "w200", showHtml: true, options: <?php // echo view("projects/tasks/quick_filters_dropdown"); ?>},
                 <?php echo $custom_field_filters; ?>
            ];
        } else {
<?php if ($show_milestone_info) { ?>
                filterDropdown = [
                    {name: "milestone_id", class: "w200", options: <?php echo $milestone_dropdown; ?>}
                    , <?php echo $custom_field_filters; ?>
                ];
<?php } else { ?>
                filterDropdown = [<?php echo $custom_field_filters; ?>];
<?php } ?>
        }
        

        var scrollLeft = 0;
        $("#kanban-filters").appFilters({
            source: '<?php echo_uri("suppliers/supplier_tasks_kanban_data/" . $supplier_id) ?>',
            targetSelector: '#load-kanban',
            reloadSelector: "#reload-kanban-button",
            search: {name: "search"},
            filterDropdown: filterDropdown,
            multiSelect: [
            {
            name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>,
                    saveSelection: true
            }
            ],
            singleDatepicker: [{name: "deadline", defaultText: "<?php echo app_lang('deadline') ?>",
                    options: [
                        {value: "expired", text: "<?php echo app_lang('expired') ?>"},
                        {value: moment().format("YYYY-MM-DD"), text: "<?php echo app_lang('today') ?>"},
                        {value: moment().add(1, 'days').format("YYYY-MM-DD"), text: "<?php echo app_lang('tomorrow') ?>"},
                        {value: moment().add(7, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(app_lang('in_number_of_days'), 7); ?>"},
                        {value: moment().add(15, 'days').format("YYYY-MM-DD"), text: "<?php echo sprintf(app_lang('in_number_of_days'), 15); ?>"}
                    ]}],
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
<?php //echo view("projects/tasks/batch_update/batch_update_script"); ?>
<?php echo view("projects/tasks/quick_filters_helper_js"); ?>
