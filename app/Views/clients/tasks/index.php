

    <div class="card rounded-bottom" id="my_cards">
      <div class="tab-title clearfix">
                <h4><?php echo app_lang('tasks'); ?></h4>
                <div class="title-button-group">
                   <?php
                
                if ($can_create_tasks) {
                   

                    /*task_modal_form in Projects Controller*/
                    
                    echo modal_anchor(get_uri("clients/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-default", "title" => app_lang('add_task'), "data-post-client_id" => $client_id, "data-post-add_type" => "view_subtask"));
                }
                ?>

                <?php 
                    echo modal_anchor(get_uri("tasks/post_cuses_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('update_status'), array("class" => "btn btn-default","style" => "display:none;","id" => "writeReson" , "data-post-id" => 0, "title" => app_lang('update_status')));

                    ?>
                </div>
            </div>

        <div class="table-responsive" id="client-projects-list">
            <table id="task-table" class="display" width="100%">            
            </table>
        </div>
    </div>
   
    
   


<?php
//if we get any task parameter, we'll show the task details modal automatically
$preview_task_id = get_array_value($_GET, 'task');
if ($preview_task_id) {
    echo modal_anchor(get_uri("tasks/task_view"), "", array("id" => "preview_task_link", "title" => app_lang('task_info') . " #$preview_task_id", "data-post-id" => $preview_task_id));
}

$statuses = array();
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

    $statuses[] = array("text" => app_lang('open'), "value" => 1, "isChecked" => $is_selected);
    $statuses[] = array("text" => app_lang('closed'), "value" => 2, "isChecked" => $is_selected);
//}
?>



<script type="text/javascript">
    $(document).ready(function () {

        var showOption = true,
                idColumnClass = "w10p",
                titleColumnClass = "";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w25p";
            titleColumnClass = "w75p";
        }

        var showOptions = true;
        var check_user='<?php echo $mang; ?>';
        

        <?php 
        $f_dropdown = array(array("id" => "", "text" => "- فلتر -"));
        $f_dropdown[] = array("id" => "no_invoice", "text" => "لا تحتوي على رقم فاتورة", "isSelected" => $filter=="no_invoice"?true:false);
        $f_dropdown[] = array("id" => "no_christening_number", "text" => "لا تحت وي على رقم تعميد", "isSelected" => $filter=="no_christening_number"?true:false);
        $f_dropdown[] = array("id" => "no_project", "text" => "مهام غير مربوطه ب مشروع", "isSelected" => $filter=="no_project"?true:false);
        $f_dropdown[] = array("id" => "tasks_deleted", "text" => "المهام المحزوفة", "isSelected" => $filter=="tasks_deleted"?true:false);
        ?>
      
        $("#task-table").appTable({
            source: '<?php echo_uri("clients/my_tasks_list_data/" . $client_id) ?>',
            serverSide: true,
            order: [[1, "desc"]],
            //responsive: false, //hide responsive (+) icon
            filterDropdown: [
                {name: "specific_user_id", class: "w170", options: <?php echo $team_members_dropdown; ?>},
                {name: "filter", class: "w150",options: <?php echo json_encode($f_dropdown) ; ?>},
                {name: "project_id", class: "w150", options: <?php echo $projects_dropdown; ?>}, //reset milestone on changing of project
                 
            ],
           
            multiSelect: [
                {
                    name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>
                }
            ],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": "text-center w50 all", order_by: "id"},
                {title: '<?php echo app_lang("project") ?>', order_by: "project"},
                {title: '<?php echo app_lang("christening_number") ?>', "iDataSort": 3, order_by: "christening_number"},
                {title: '<?php echo app_lang("invoice_number") ?>', "iDataSort": 4, order_by: "invoice_number"},
                {title: '<?php echo app_lang("created_date") ?>', "iDataSort": 5, order_by: "start_date"},
                
                {title: '<?php echo app_lang("created_by") ?>', order_by: "assigned_to"},
                {title: '<?php echo app_lang("status") ?>', order_by: "status"}
<?php echo $custom_field_headers; ?>,
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOptions},
            ],
            printColumns: combineCustomFieldsColumns([1, 2, 3,4,5,6,7,8], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 3,4,5,6, 7,8], '<?php echo $custom_field_headers; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");

                //add activated sub task filter class
                setTimeout(function () {
                    var searchValue = $('#task-table').closest(".dataTables_wrapper").find("input[type=search]").val();
                    if (searchValue.substring(0, 1) === "#") {
                        $('#task-table').find("[main-task-id='" + searchValue + "']").removeClass("filter-sub-task-button").addClass("remove-filter-button sub-task-filter-active");
                    }
                }, 50);
            },
            onRelaodCallback: function () {
                hideBatchTasksBtn(true);
            },
            onInitComplete: function () {
                if (!showOption) {
                    window.scrollTo(0, 210); //scroll to the content for mobile devices
                }
                setPageScrollable();
            }
        });




        //open task details modal automatically 

        if ($("#preview_task_link").length) {
            $("#preview_task_link").trigger("click");
        }

       

    <?php
    //$rec_inv_status["0"] =  "Opened";
    //$rec_inv_status["1"] =  "Closed";
    $rec_inv_status[] = array("id" => 1, "text" => app_lang('open'));
    $rec_inv_status[] = array("id" => 2, "text" => app_lang('closed'));
     ?>

   

$('body').on('click', '[data-act=update-mytask-status]', function () {
            $(this).appModifier({
                value: $(this).attr('data-value'),
                actionUrl: '<?php echo_uri("tasks/save_task_status2") ?>/' + $(this).attr('data-id'),
                select2Option: {data: <?php echo json_encode($rec_inv_status) ?>},
                onSuccess: function (response, newValue) {

                        if (response.data=="ok") {
                       
                        var d = document.getElementById("writeReson");  //   Javascript
                        d.setAttribute('data-post-id' , response.id); 
                        $("#writeReson").trigger("click");
                        $("#task-table").reload();
                    }else{
                    $("#task-table").appTable({newData: response.data, dataId: response.id});
                        
                    
                    }

                    
                }
            });

            return false;
        });


/*

$('body').on('click', '.client-widget-link', function (e) {
            e.preventDefault();

            var filter = $(this).attr("data-filter");
            if (filter) {
                var filterIndex = quick_filters_dropdown.findIndex(x => x.id === filter);
                alert(filterIndex);
                if ([filterIndex] > - 1){
                //match found
                //document.getElementById("oyd").selectedIndex = "2";
                quick_filters_dropdown[filterIndex].isSelected = true;
                $("[data-bs-target='#tasks_list']").attr("data-reload", "1").trigger("click");
                
            }
                //$(".oyd").attr("data-reload", "1").trigger("click");
                

            }
        });*/
    });
</script>

<?php //echo view("tasks/update_task_script"); ?>
<?php echo view("tasks/quick_filters_helper_js"); ?>