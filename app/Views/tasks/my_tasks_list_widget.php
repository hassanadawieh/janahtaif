<div class="widget-container col-md-8">
    <div class="card bg-white">
    <div class="card-header no-border">
        <i data-feather="list" class="icon-16"></i>&nbsp; <?php echo app_lang('my_tasks'); ?>
    </div>

    <div class="table-responsive" id="my-task-list-widget-table">
        <table id="task-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        initScrollbar('#my-task-list-widget-table', {
            setHeight: 330
        });

        var showOption = true,
                idColumnClass = "w70",
                titleColumnClass = "";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w25p";
            titleColumnClass = "w75p";
        }

        $("#task-table").appTable({
            source: '<?php echo_uri("tasks/dash_my_tasks_list_data") ?>',
            order: [[1, "desc"]],
            displayLength: 30,
            responsive: false, //hide responsive (+) icon
            columns: [
                {visible: false},
                {title: '<?php echo app_lang("id") ?>', "class": "text-center w50 all", order_by: "id"},
                {title: '<?php echo app_lang("project") ?>', "class": "all"},
                {title: '<?php echo app_lang("created_date") ?>'},
                
                
               
                {title: '<?php echo app_lang("status") ?>', order_by: "status"},
                
            ],
            onInitComplete: function () {
                $("#task-table_wrapper .datatable-tools").addClass("hide");
            },
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
            }
        });
    });
</script>
<?php echo view("tasks/update_task_script"); ?>