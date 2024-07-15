<style type="text/css">
.w5p{
    width: 5%;
}
::-webkit-scrollbar {
    width: 8px;
}
table.dataTable tbody td{
    vertical-align:middle;
}
table.dataTable tbody td:first-child:hover {
    background: #fff !important;
}
[dir=rtl] table.dataTable tbody td:first-child {
    padding-right: 0px !important;
}
table.dataTable tbody td:first-child {
    padding-left: auto !important;
}
.my_padding{
    margin-left:7px;
}
[dir=rtl] .my_padding{
    margin-right:7px;
}
[dir=rtl] td.details-control:before {
 
    
    right: 4px;
    height: .87em;
    width: .87em;
    /*margin-top: -9px;*/
    display: inline-block;
    
    color: white;
    border: 0.15em solid white;
    border-radius: 1em;
    box-shadow: 0 0 0.2em #444;
    box-sizing: content-box;
    text-align: center;
    text-indent: 0 !important;
    font-family: "Courier New",Courier,monospace;
    line-height: 1em;
    content: "+";
    background-color: #31b131;
    cursor: pointer;
 
}
td.details-control:before {
 
    
    left: 10px;
    height: .87em;
    width: .87em;
    /*margin-top: -9px;*/
    display: inline-block;
    
    color: white;
    border: 0.15em solid white;
    border-radius: 1em;
    box-shadow: 0 0 0.2em #444;
    box-sizing: content-box;
    text-align: center;
    text-indent: 0 !important;
    font-family: "Courier New",Courier,monospace;
    line-height: 1em;
    content: "+";
    background-color: #31b131;
    cursor: pointer;
 
}
.mysub_task > tbody > tr > td:nth-child(1){
    padding: 5px !important;
}
.mysub_task > tbody > tr > td:nth-child(2){
    min-width: 100px !important;
}
.small {
    font-size: .925em;
}
table.small.dataTable tbody td {
    padding-top: 3px !important;
    padding-bottom: 3px !important;
    padding-left: 5px !important;
    padding-right: 5px !important;
    border: 1px solid #dddddd;
}

#main-task-table.dataTable tbody td {
    padding: 7px 5px !important;
   
}
 
tr.shown td.details-control:before {
 
    /*background: transparent url(../images/details_close.png) no-repeat center center;*/
    content: '-';
    background-color: red;
 
}


.mytable-responsive {
    width: 100%;
    max-width: 100%;
    border: none !important;
    overflow: scroll !important;
    -webkit-overflow-scrolling: touch;
}
.mysub_task.dataTable th {
   
    white-space: nowrap;

}
div.dataTables_filter input{
    text-align: center;
}
.my_padding > div.datatable-tools:last-child {
    margin: 5px 0 !important;
}
.mysub_task.dataTable thead th{
    padding: 7px 8px !important;
    border: 1px solid #cecfd3 !important;
}



table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before{
    left: 5px;
}
[dir=rtl] .mysub_task.dataTable thead .sorting, .mysub_task.dataTable thead .sorting_asc, .mysub_task.dataTable thead .sorting_desc, .mysub_task.dataTable thead .sorting_asc_disabled, .mysub_task.dataTable thead .sorting_desc_disabled {
    background-repeat: no-repeat;
    background-position: center left;
}

.sub-task-icon {   
    padding: 0.2px 1.3px;
    font-size: 11px;
}

.dropdown-menu .list-group-item:not(.inline-loader) {
    padding: 5px 10px !important;
    text-align: center;
}

</style>
<div id="page-content" class="page-wrapper clearfix">
    <div class="mt20" style="display: none;">
    <div class="row">
        <div class="col-md-3">
            <?php echo tasks_count_widget("no_invoice","مهام لا تحتوي على رقم فاتورة"); ?>
        </div>

        <div class="col-md-3">
            <?php echo tasks_count_widget("no_christening_number","مهام لا تحتوي على رقم تعميد"); ?>
        </div>

        <div class="col-md-3">
            <?php echo tasks_count_widget("no_project","مهام غير مرتبطة ب مشروع"); ?>
        </div>
        <div class="col-md-3">
            <?php echo tasks_count_widget("tasks_deleted","المهام المحزوفة"); ?>
        </div>
    </div>
</div>
    <ul class="nav nav-tabs bg-white title" role="tablist">
        <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("tasks"); ?></h4></li>

        <?php echo view("tasks/tabs", array("active_tab" => "tasks_list", "selected_tab" => $tab)); ?>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group">
                <?php
                if ($is_admin) {
                    echo anchor(get_uri("subtasks/supply_mang/"), "<i data-feather='check-circle' class='icon-16'></i> " .app_lang("supply_mang"),array("class"=>'btn btn-success',"style" => "padding:3px 5px;"));
                    echo anchor(get_uri("subtasks/index/"), "<i data-feather='check-circle' class='icon-16'></i> " .app_lang("reserv_mang"),array("class"=>'btn btn-success',"style" => "padding:3px 5px;"));

                }else{
                if ($mang=="yes") {
                    // code...
                echo anchor(get_uri("subtasks/index/"), "<i data-feather='check-circle' class='icon-16'></i> " .app_lang("reserv_mang"),array("class"=>'btn btn-success',"style" => "padding:3px 5px;"));
            }
            if ($mang=="no") {
                    // code...
                echo anchor(get_uri("subtasks/supply_mang/"), "<i data-feather='check-circle' class='icon-16'></i> " .app_lang("supply_mang"),array("class"=>'btn btn-success',"style" => "padding:3px 5px;"));
            }
        }
                
                if ($can_create_tasks) {
                   

                    /*task_modal_form in Projects Controller*/
                    echo modal_anchor(get_uri("tasks/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_multiple_tasks'), array("class" => "btn btn-default", "title" => app_lang('add_multiple_tasks'), "data-post-add_type" => "multiple"));
                    echo modal_anchor(get_uri("tasks/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("class" => "btn btn-default", "title" => app_lang('add_task'), "data-post-add_type" => "view_subtask"));
                }
                ?>

                <?php 


                    echo modal_anchor(get_uri("tasks/post_cuses_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('update_status'), array("class" => "btn btn-default","style" => "display:none;","id" => "writeReson" , "data-post-id" => 0, "title" => app_lang('update_status')));

                    ?>
            </div>
        </div>

    </ul>
    <div class="card" id="myCards">
        <div class="table-responsive clearfix" style="overflow-y: auto;" >
            <table id="main-task-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<?php
//if we get any task parameter, we'll show the task details modal automatically
$preview_task_id = get_array_value($_GET, 'task');
if ($preview_task_id) {
    echo modal_anchor(get_uri("tasks/task_view"), "", array("id" => "preview_task_link", "title" => app_lang('task_info') . " #$preview_task_id", "data-post-id" => $preview_task_id));
}

$statuses = array();
    $is_selected = true;

  

    $statuses[] = array("text" => app_lang('open'), "value" => 1, "isChecked" => $is_selected);
    $statuses[] = array("text" => app_lang('closed'), "value" => 2, "isChecked" => $is_selected);
//}
?>



<script type="text/javascript">
    $(document).ready(function () {

        var showOption = true,
                idColumnClass = "w5p",
                titleColumnClass = "",
                titleColumnClass2 = "";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w20p";
            titleColumnClass = "w60p"
            titleColumnClass2 = "w20p";
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
        var main_task_col=[],main_task_filter_col=[];
        if(check_user=="yes"){
            main_task_col=[
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": idColumnClass+" text-center details-control", order_by: "id"},
                {title: '<?php echo app_lang("client") ?>', "class": titleColumnClass, order_by: "client_id"},
                {title: '<?php echo app_lang("contacts") ?>', visible: showOption, order_by: "client_contact_id"},
                {title: '<?php echo app_lang("project") ?>', visible: showOption, order_by: "project"},
                {title: '<?php echo app_lang("christening_number") ?>', visible: showOption, "iDataSort": 3, order_by: "christening_number"},
                {title: '<?php echo app_lang("invoice_number") ?>', visible: showOption, "iDataSort": 4, order_by: "invoice_number"},
                 {title: '<?php echo app_lang("cls_title") ?>', visible: showOption},
                 {title: '<?php echo app_lang("description") ?>', visible: showOption, order_by: "description",render: function(data, type, full, meta) {
                    return showOption?"<div style='white-space: nowrap;max-width: 110px;overflow: hidden;text-overflow: ellipsis;'  title='"+data+"'>"+data+"</div>":data;
                } },
                {title: '<?php echo app_lang("created_date") ?>', visible: showOption, "iDataSort": 5, order_by: "created_date"},
                
                {title: '<?php echo app_lang("created_by") ?>', visible: showOption, order_by: "assigned_to"},
                {targets:10,visible: false, searchable: false},
                {title: '<?php echo app_lang("status") ?>', "class": titleColumnClass2, order_by: "is_closed"}
<?php echo $custom_field_headers; ?>,
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOption},
            ];

            main_task_filter_col=[
                {name: "specific_user_id", class: "w170", options: <?php echo $team_members_dropdown; ?>},
                {name: "filter", class: "w150",options: <?php echo json_encode($f_dropdown) ; ?>},
                {name: "client_id", class: "w150", options: <?php echo $clients_dropdown; ?>},
                {name: "cls_id", class: "w150", options: <?php echo $maintask_clsifications_dropdown; ?>},
                {name: "project_id", class: "w150", options: <?php echo $projects_dropdown; ?>},
                ];

        }else{

            main_task_col=[
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": idColumnClass+" text-center details-control", order_by: "id"},
                {title: '<?php echo app_lang("project") ?>',"class": titleColumnClass, order_by: "project"},
                {targets:3,visible: false, title: '<?php echo app_lang("contacts") ?>', order_by: "client_contact_id"},
                {targets:4,visible: false,title: '<?php echo app_lang("christening_number") ?>', order_by: "christening_number"},
                {targets:5,visible: false,title: '<?php echo app_lang("invoice_number") ?>', order_by: "invoice_number"},
                {title: '<?php echo app_lang("cls_title") ?>', visible: showOption},
                {targets:6,visible: false,title: '<?php echo app_lang("description") ?>', order_by: "description"},
                {title: '<?php echo app_lang("created_date") ?>', visible: showOption, "iDataSort": 5, order_by: "created_date"},
                {title: '<?php echo app_lang("created_by") ?>', visible: showOption, order_by: "assigned_to"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("status") ?>', "class": titleColumnClass, order_by: "is_closed"}
                <?php echo $custom_field_headers; ?>,
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100", visible: showOption},
            ];
            main_task_filter_col=[
                {name: "specific_user_id", class: "w170", options: <?php echo $team_members_dropdown; ?>},
                {name: "project_id", class: "w150", options: <?php echo $projects_dropdown; ?>},
                ];

        }
       
           
        $("#main-task-table").appTable({
            source: '<?php echo_uri("tasks/my_tasks_list_data") ?>',
            serverSide: true,
            order: [[1, "desc"]],
            responsive: false, //hide responsive (+) icon
            //filterParams: {deleted_client: ''},
            /*checkBoxes: [
                {text: '<?php //echo app_lang("deleted") ?>', name: "deleted_client", value: "true", isChecked: false}
            ],*/
           

            filterDropdown: main_task_filter_col,
            
            multiSelect: [
                {
                    name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>
                }
            ],
            columns: main_task_col,

            printColumns: combineCustomFieldsColumns([1, 2, 3,4,5,6,7,8], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 3,4,5,6, 7,8], '<?php echo $custom_field_headers; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                //$('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
                if(check_user=="yes"){
                if(aData[11]){

                $(nRow).attr("style", "background-color:"+aData[11]+" !important;");
                $('td',nRow).attr("style", "background-color:transparent !important; color:#000 !important;");
                $('td:eq(1) a',nRow).attr("style", "color:#000 !important; ");
            }
                
            }else{
                if(aData[9]){

                $(nRow).attr("style", "background-color:"+aData[9]+" !important;");
                $('td',nRow).attr("style", "background-color:transparent !important; color:#000 !important;");
                $('td:eq(1) a',nRow).attr("style", "color:#000 !important; ");
            }
                
            
            }
            $('td:eq(0)', nRow).attr("style", "background-color:#ffffff !important; border-left:5px solid " + aData[0] + " !important;");
                //add activated sub task filter class
                setTimeout(function () {
                    var searchValue = $('#main-task-table').closest(".dataTables_wrapper").find("input[type=search]").val();
                    if (searchValue.substring(0, 1) === "#") {
                        $('#main-task-table').find("[main-task-id='" + searchValue + "']").removeClass("filter-sub-task-button").addClass("remove-filter-button sub-task-filter-active");
                    }
                }, 50);
            },
            onRelaodCallback: function () {
                hideBatchTasksBtn(true);
            },
            onInitComplete: function () {
                $("body").tooltip({ selector: '[data-bs-toggle=tooltip]' });
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

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "tasks_list") {
                $("[data-tab='#tasks_list']").trigger("click");

                //save the selected tab in browser cookie
                setCookie("selected_tab_" + "<?php echo $login_user->id; ?>", "tasks_list");
            }
        }, 210);

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
                        $("#main-task-table").reload();
                    }else{
                    $("#main-task-table").appTable({newData: response.data, dataId: response.id});
                        
                    
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
<?php echo view("tasks/subtask_tree_script"); ?>
<?php echo view("tasks/batch_update/batch_update_script"); ?>
<?php echo view("tasks/update_task_script"); ?>
<?php echo view("tasks/update_task_read_comments_status_script"); ?>
<?php echo view("tasks/quick_filters_helper_js"); ?>