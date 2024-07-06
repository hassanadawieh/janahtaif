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

<?php echo view("projects/tasks/sub_tasks_helper_js"); ?>