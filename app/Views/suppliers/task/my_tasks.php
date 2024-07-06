<div class="card">
    <div class="card-header title-tab">
        <h4 class="float-start"><?php echo app_lang('tasks'); ?></h4>
        <div class="title-button-group">
            
        </div>
    </div>
    <div class="table-responsive">
        <table id="task-table" class="display" width="100%">            
        </table>
    </div>    
</div>

<?php
//prepare status dropdown list
//select the non completed tasks for team members by default
//show all tasks for client by default.
$statuses = array();
foreach ($task_statuses as $status) {
    $is_selected = true;
    

    $statuses[] = array("text" => ($status->key_name ? app_lang($status->key_name) : $status->title), "value" => $status->id, "isChecked" => $is_selected);
}
?>

<script type="text/javascript">
    $(document).ready(function () {

    var userType = "<?php echo $login_user->user_type; ?>";
    var optionVisibility = false;
    if ("<?php echo ($can_edit_tasks || $can_delete_tasks); ?>") {
    optionVisibility = true;
    }

    var milestoneVisibility = false;
    
    //milestoneVisibility = true;
    

    var showOptions = true,
            idColumnClass = "w10p",
            titleColumnClass = "",
            optionColumnClass = "w100";
    if (isMobile()) {
    showOptions = false;
    milestoneVisibility = false;
    idColumnClass = "w20p";
    titleColumnClass = "w60p";
    optionColumnClass = "w20p";
    }

    var tit="<?php echo $mang=='yes'?app_lang('guest_nm'):app_lang('project') ?>";
    var tit_order="<?php echo $mang=='yes'?'guest_nm':'project_id' ?>";


    $("#task-table").appTable({
    source: '<?php echo_uri("suppliers/tasks_list_data/" . $supplier_id) ?>',
            serverSide: true,
            order: [[1, "desc"]],
            //responsive: false, //hide responsive (+) icon
            filterDropdown: [
            {name: "specific_user_id", class: "w200", options: <?php echo $team_members_dropdown; ?>},
            {name: "priority_id", class: "w200", options: <?php echo $priorities_dropdown; ?>},
             <?php echo $custom_field_filters; ?>
            ],
           
            multiSelect: [
            {
            name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>,
                    //saveSelection: true
            }
            ],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": "text-center w65 all", order_by: "id"},
                {title: tit, "class": "all", order_by: tit_order},
                {title: '<?php echo app_lang("driver_nm") ?>', "iDataSort": 3, order_by: "driver_nm"},
                //{visible: false, searchable: false, order_by: "deadline"},
                {title: '<?php echo app_lang("service_type") ?>', "iDataSort": 5, order_by: "service_type"},
                {title: '<?php echo app_lang("tmp_return_date") ?>'},
                {title: '<?php echo app_lang("act_return_date") ?>'},
                {title: '<?php echo app_lang("status") ?>', order_by: "status"}
                <?php echo $custom_field_headers; ?>,
                //{visible: false, searchable: false},
              
            ],
            printColumns: combineCustomFieldsColumns([1, 2,3,4, 5,6, 7], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2,3,4, 5,6, 7], '<?php echo $custom_field_headers; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
            //add activated sub task filter class
            setTimeout(function(){
            var searchValue = $('#task-table').closest(".dataTables_wrapper").find("input[type=search]").val();
            if (searchValue.substring(0, 1) === "#") {
            $('#task-table').find("[main-task-id='" + searchValue + "']").removeClass("filter-sub-task-button").addClass("remove-filter-button sub-task-filter-active");
            }
            }, 50);
            },
            onRelaodCallback: function () {
            hideBatchTasksBtn();
            },
            onInitComplete: function () {
                
                setPageScrollable();
            }
    });

 <?php
$statuses = array();
foreach ($task_statuses as $status) {
    $st = $status->key_name ? app_lang($status->key_name) : $status->title;
    $statuses[] = array("id" => $status->id, "text" => $st);

}
?>

   
    });
</script>

