
<?php load_js(array("assets/daterangepicker/daterangepicker.js")); ?>
<?php load_css(array("assets/daterangepicker/daterangepicker.css")); ?>
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
div.DTTT_container{
    margin-bottom: 7px !important;
    margin-left: 6px !important;
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
table.dataTable thead th{
    padding: 12px 8px !important;
}

#main-task-table.dataTable th {
   
    white-space: nowrap;

}
#main-task-table.dataTable tbody td {
    padding: 5px 4px !important;
}


#main-task-table > thead > tr:nth-child(2) > th{
    padding: 2px !important;
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

.dtrg-start{
    cursor: pointer;
}

.wmd-view-topscroll, .wmd-view {
    overflow-x: scroll;
    overflow-y: hidden;
    width: 300px;
    border: none 0px RED;
}

.wmd-view-topscroll { height: 10px; }
.scroll-div1 { 
    width: 1000px; 
    overflow-x: scroll;
    overflow-y: hidden;
    height:10px;
}
.wmd-view-topscroll::-webkit-scrollbar{
    width: 6px;
    height: 7px;

}


[dir=rtl] .select2-container.oyd-form > .select2-choice > .select2-chosen {
    padding: 6px 2px !important;
    margin-left: 15px !important;
}
.daterangepicker .ranges ul{
    width: 120px;
}
.daterangepicker .calendar {
    max-width: 240px;
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

                    echo modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_subtasks'), array("id" => "open_add_dialog","class" => "btn btn-warning","style" => "padding:3px 5px;", "title" => app_lang('add_subtasks'), "data-post-mang" => "reservmang"));

                }else{
                if ($mang=="yes") {
                    
                echo anchor(get_uri("subtasks/index/"), "<i data-feather='check-circle' class='icon-16'></i> " .app_lang("reserv_mang"),array("class"=>'btn btn-success',"style" => "padding:3px 5px;"));
                if ($can_create_subtasks) {

                        echo modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_subtasks'), array("id" => "open_add_dialog","class" => "btn btn-warning", "title" => app_lang('add_task'),"style" => "padding:3px 5px;", "data-post-task_id" => "0", "data-post-mang" => "reservmang"));
                    }

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

        <div class="wmd-view-topscroll">
    <div class="scroll-div1">
    </div>
</div>
<div class="col-md-6 text-center">
    <input type="checkbox" name="hide_top_section" id="hide_top_section" class="manage_project_section form-check-input">
    <label for="hide_top_section" class="mb-0">إخفاء الجزء العلوي</label>
</div>

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

    foreach ($task_statuses as $status) {
        $is_selected = false;
        $sub_statuses[] = array("text" => ($status->key_name ? app_lang($status->key_name) : $status->title), "value" => $status->id, "isChecked" => $is_selected);
    }
    $statuses2 = array(array("id" => "", "text" => "- ". app_lang("status"). " -"));
    $statuses2[] = array("id" => 1, "text" => app_lang('open'));
    $statuses2[] = array("id" => 2, "text" => app_lang('closed'));


//}


?>

<?php 
        

        $service_type_dropdown = array(array("id" => "", "text" => "- ". app_lang("service_type"). " -"));
        $service_type_dropdown[] = array("id" => "with_driver", "text" => "سيارة بسائق");
        $service_type_dropdown[] = array("id" => "no_driver", "text" => "بدون سائق");
        $service_type_dropdown[] = array("id" => "deliver", "text" => "توصيلة");
        $service_type_dropdown[] = array("id" => "no_car", "text" => "سائق بدون سيارة");

        
        $rec_inv_status_dropdown = array(array("id" => "", "text" => '- '. app_lang("rec_inv_status2"). ' -'));
        $rec_inv_status_dropdown[] = array("id" => "wait_inv", "text" => app_lang("wait_inv"));
        $rec_inv_status_dropdown[] = array("id" => "rec_inv", "text" => app_lang("rec_inv"));

        ?>


<script type="text/javascript">
    var cfil={deleted_client: '',sub_tasks_id_f:'',pnt_task_id_f:'',guest_nm_f:'',guest_phone_f:'',pnt_task_id:'',company_name_f:'',clients_contact_f:'',christ_num_f:'',inv_num_f:'',city_name_f:'',driver_nm_f:'',car_type_f:'',out_date_f:'',out_date_f_t:'',tmp_return_date_f:'',tmp_return_date_f_t:'',sales_act_return_date_f:'',sales_act_return_date_f_t:'',inv_day_count_f:'',note_f:'',created_by_f:'',main_task_status_f:''
        ,supplier_f:'',car_status_f:'',car_number_f:'',act_return_date_f:'',act_return_date_f_t:'',act_out_date_f:'',act_out_date_f_t:'',day_count_f:'',dres_number_f:'',amount_f:'',note2_f:'',project_nm_f:'',monthly_f:''};
    $(document).ready(function () {

        var showOption = true,
                idColumnClass = "w5p",
                titleColumnClass = "",
                titleColumnClass2 = "";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w20p";
            titleColumnClass = ""
            titleColumnClass2 = "";
        }

        

        var showOptions = true;
        var check_user='<?php echo $mang; ?>';
        
        var my_url='<?php echo_uri("subtasks/list_data/")?>';
        <?php 
        $f_dropdownd = array(array("id" => "", "text" => "- فلتر -"));
        $f_dropdownd[] = array("id" => "no_invoice", "text" => "لا تحتوي على رقم فاتورة", "isSelected" => $main_filter=="no_invoice"?true:false);
        $f_dropdownd[] = array("id" => "no_christening_number", "text" => "لا تحت وي على رقم تعميد", "isSelected" => $main_filter=="no_christening_number"?true:false);
        $f_dropdownd[] = array("id" => "no_project", "text" => "مهام غير مربوطه ب مشروع", "isSelected" => $main_filter=="no_project"?true:false);
        $f_dropdownd[] = array("id" => "tasks_deleted", "text" => "المهام المحزوفة", "isSelected" => $main_filter=="tasks_deleted"?true:false);
        ?>
        var coll=[],filter_col=[],datePicker=[];
        var main_task_col=[],main_task_filter_col=[];

        var collapsedGroups = {};
        var top = '';
        var group_index=2;

        if(check_user=="yes"){
            group_index=2;
            my_url='<?php echo_uri("subtasks/list_data/0")?>';
            main_task_col=[
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": "sub_tasks_id_f text-center "+idColumnClass+" all", order_by: "sub_task_id"},
                {title: '<?php echo app_lang("main_task") ?>',"class":'pnt_task_id_f', order_by: "pnt_task_id"},
                {title: '<?php echo app_lang("status") ?>',"class":'main_task_status_f'},
                {title: '<?php echo app_lang("client") ?>',"class":'company_name_f',render: function(data, type, full, meta) {
                    return showOption?"<div style='white-space: nowrap;max-width: 110px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,8' data-bs-placement='top' data-bs-custom-class='tooltip-success' title='"+data+"'>"+data+"</div>":data;
                } },
                {targets:5,visible: false,title: '<?php echo app_lang("project") ?>',"class":'project_nm_f'},
                {title: '<?php echo app_lang("contact") ?>',"class":'clients_contact_f',render: function(data, type, full, meta) {
                    return showOption?"<div style='white-space: nowrap;max-width: 90px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip'  title='"+data+"'>"+data+"</div>":data;
                } },
                {title: '<?php echo app_lang("christening_number") ?>',"class":'christ_num_f' ,render: function(data, type, full, meta) {
                    return showOption? "<div style='white-space: nowrap;max-width: 90px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,4' data-bs-placement='top' data-bs-html='true' title='"+data+"'>"+data+"</div>":data;
                } },
                
                {title: '<?php echo app_lang("invoice_number") ?>',"class":'inv_num_f'},
                {title: '<?php echo app_lang("description") ?>',"class":'description_f' ,render: function(data, type, full, meta) {
                    return showOption? "<div style='white-space: nowrap;max-width: 90px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,4' data-bs-placement='top' data-bs-html='true' title='"+data+"'>"+data+"</div>":data;
                } },

                {title: '<?php echo app_lang("guest_nm") ?>', "class": "guest_nm_f all "+titleColumnClass, order_by: "guest_nm",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:130px; '>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("guest_phone") ?>',"class":'guest_phone_f', order_by: "guest_phone",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:70px; '>"+data+"</div>";
                } },

                
                {title: '<?php echo app_lang("city") ?>',"class":'city_name_f', order_by: "city_id"},
                {title: '<?php echo app_lang("service_type") ?>',"class":'service_type_f', order_by: "service_type",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:90px; '>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("driver_nm") ?>',"class":'driver_nm_f', order_by: "driver_id",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:100px; '>"+data+"</div>";
                } },
                
                {title: '<?php echo app_lang("car_type") ?>',"class":'car_type_f', order_by: "car_type_id"},
                {title: '<?php echo app_lang("out_exp_date&time") ?>', "class": "out_date_f text-center ", order_by: "out_date",render: function(data, type, full, meta) {
                    return "<div dir='ltr'>"+data+"</div>";
                } },
                //{title: '<?php //echo app_lang("exp_out_time") ?>', "class": "text-center ", order_by: "exp_out_time"},
                {title: '<?php echo app_lang("tmp_return_date_2") ?>', "class": "tmp_return_date_f text-center ", order_by: "tmp_return_date"},
                {title: '<?php echo app_lang("act_return_date_2") ?>', "class": "sales_act_return_date_f text-center ", order_by: "sales_act_return_date"},
                {title: '<?php echo app_lang("inv_day_count") ?>',"class":'inv_day_count_f', order_by: "inv_day_count"},
                {title: '<?php echo app_lang("note") ?>', "class": "note_f text-center ", order_by: "note",render: function(data, type, full, meta) {
                    return "<div style='white-space: nowrap;max-width: 130px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,4' data-bs-placement='top' data-bs-html='true' title='"+data+"'>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("created_by") ?>',"class":'created_by_f', order_by: "created_by",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:90px; '>"+data+"</div>";
                } },
                {visible: false, searchable: false},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("status") ?>',"class": "status_f"}
                <?php echo $custom_field_headers2; ?>,
                //{visible: false, searchable: false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "options_f text-center option ", visible: showOption,render: function(data, type, full, meta) {
                    return "<div style='min-width:80px;'>"+data+"</div>";
                } },
            ];

           

                filter_col=[
                {name: "specific_user_id", class: "w170", options: <?php echo $team_members_dropdown; ?>},
                {name: "main_filter", class: "w150",options: <?php echo json_encode($f_dropdownd) ; ?>},
                {name: "cls_id", class: "w150", options: <?php echo $maintask_clsifications_dropdown; ?>},
                {name: "project_id", class: "w150", options: <?php echo $projects_dropdown; ?>},
                {name: "client_id", class: "w150", options: <?php echo $clients_dropdown; ?>},
                {name: "driver_id", class: "w150",options: <?php echo $drivers_dropdown; ?>},
                {name: "car_type_id", class: "w150",options: <?php echo $cars_type_dropdown; ?>},
                //{name: "city_id", class: "w150",options: <?php //echo $cities_dropdown; ?>},
                //{name: "service_type", class: "w150", options: <?php //echo json_encode($service_type_dropdown); ?>},
                {name: "specific_user_id", class: "w150", options: <?php echo $team_members_dropdown; ?>},
                {name: "priority_id", class: "w150", options: <?php echo $priorities_dropdown; ?>},
                
                ];
            datePicker=[{name: "out_date", defaultText: "<?php echo app_lang('out_date') ?>",
                    },{name: "tmp_return_date", defaultText: "<?php echo app_lang('tmp_return_date') ?>",
                    },{name: "sales_act_return_date", defaultText: "<?php echo app_lang('sales_act_return_date') ?>",
                    }];

        }else{
            group_index=2;
            my_url='<?php echo_uri("subtasks/list_data_supply/")?>'
            main_task_col=[
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class":"sub_tasks_id_f "+ idColumnClass, order_by: "sub_task_id"},
                
                {title: '<?php echo app_lang("main_task") ?>',"class":'pnt_task_id_f', order_by: "pnt_task_id"},
                
                {title: '<?php echo app_lang("status") ?>',"class":'main_task_status_f'},
                
                {targets:4,visible: false,title: '<?php echo app_lang("client") ?>',"class":'company_name_f',render: function(data, type, full, meta) {
                    return showOption?"<div style='white-space: nowrap;max-width: 110px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,8' data-bs-placement='top' data-bs-custom-class='tooltip-success' title='"+data+"'>"+data+"</div>":data;
                } },
                {title: '<?php echo app_lang("project") ?>',"class":'project_nm_f',render: function(data, type, full, meta) {
                    return showOption? "<div style='white-space: nowrap;max-width: 90px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,4' data-bs-placement='top' data-bs-html='true' title='"+data+"'>"+data+"</div>":data;
                } },
                {targets:6,visible: false,title: '<?php echo app_lang("christening_number") ?>',"class":'christ_num_f'},
                {targets:7,visible: false,title: '<?php echo app_lang("invoice_number") ?>',"class":'inv_num_f'},
                {title: '<?php echo app_lang("description") ?>',"class":'description_f' ,render: function(data, type, full, meta) {
                    return showOption? "<div style='white-space: nowrap;max-width: 90px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,4' data-bs-placement='top' data-bs-html='true' title='"+data+"'>"+data+"</div>":data;
                } },

                {title: '<?php echo app_lang("guest_nm") ?>', "class": "guest_nm_f all ", order_by: "guest_nm",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:90px; '>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("supplier") ?>', "class":"supplier_f "+ titleColumnClass, "iDataSort": 3, order_by: "supplier_id",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:110px; '>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("driver_nm") ?>',"class":'driver_nm_f', order_by: "driver_id",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:90px; '>"+data+"</div>";
                } },

                {title: '<?php echo app_lang("city") ?>',"class":'city_name_f', order_by: "city_id"},
                {title: '<?php echo app_lang("car_status") ?>',"class":'car_status_f', order_by: "car_status", visible: showOption},
                {title: '<?php echo app_lang("car_number") ?>',"class":'car_number_f', order_by: "car_number", visible: showOption},
                {title: '<?php echo app_lang("rec_inv_status2") ?>',"class":'rec_inv_status_f', order_by: "rec_inv_status"},
                {title: '<?php echo app_lang("act_out_date&time") ?>',"class": "act_out_date_f", order_by: "act_out_date"},
                {title: '<?php echo app_lang("act_return_date&time") ?>',"class": "act_return_date_f", visible: showOption, order_by: "act_return_date"},
                {title: '<?php echo app_lang("day_count") ?>',"class": "day_count_f", visible: showOption, order_by: "day_count"},
                {title: '<?php echo app_lang("dres_number") ?>',"class": "dres_number_f", visible: showOption, order_by: "dres_number"},
                {title: '<?php echo app_lang("amount") ?>',"class": "amount_f", visible: showOption, order_by: "amount"},
                {title: '<?php echo app_lang("note") ?>',"class": "note2_f", order_by: "note_2", visible: showOption,render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:150px;'>"+data+"</div>";
                } },
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("created_by") ?>', visible: showOption, order_by: "created_by"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("status") ?>',"class": "status_f", visible: showOption}
                <?php echo $custom_field_headers; ?>,
                //{visible: false, searchable: false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "options_f text-center option w70", visible: showOption},
            ];
            
                filter_col=[
                {name: "specific_user_id", class: "w170", options: <?php echo $team_members_dropdown; ?>},
                {name: "project_id", class: "w150", options: <?php echo $projects_dropdown; ?>},
                {name: "supplier_id", class: "w150", options: <?php echo $suppliers_dropdown; ?>},
                
                ];
            datePicker=[{name: "act_out_date", defaultText: "<?php echo app_lang('act_out_date') ?>"},
                     {name: "act_return_date", defaultText: "<?php echo app_lang('act_return_date') ?>"}];

        }
       
           
        $("#main-task-table").appTable({
            source: my_url,
            serverSide: true,
            order: [[1, "desc"]],
            responsive: false, //hide responsive (+) icon
            //filterParams: {deleted_client: ''},
            /*checkBoxes: [
                {text: '<?php //echo app_lang("deleted") ?>', name: "deleted_client", value: "true", isChecked: false}
            ],*/
            filterParams: cfil,
            dateRangeType: "",

            rangeDatepicker: [{startDate: {name: "start_date", value: ''}, endDate: {name: "end_date", value: ''}, showClearButton: true}],

            filterDropdown: filter_col,
            //singleDatepicker: datePicker,
            multiSelect: [
                /*{
                    name: "main_status_id",
                    text: "<?php //echo app_lang('status').' '.app_lang('main_task'); ?>",
                    options: <?php //echo json_encode($statuses); ?>
                },*/{
                    name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($sub_statuses); ?>
                }
            ],
            columns: main_task_col,

            printColumns: combineCustomFieldsColumns([1, 2, 3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2, 3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18], '<?php echo $custom_field_headers; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                /*if(aData[18]){
                    $('td:eq(3)',nRow).attr("style", "color:#000 !important; background-color:"+aData[18]+" !important;");
                    $('td:eq(4)',nRow).attr("style", "color:#000 !important; background-color:"+aData[18]+" !important;");
                    $('td:eq(5)',nRow).attr("style", "color:#000 !important; background-color:"+aData[18]+" !important;");
                    $('td:eq(6)',nRow).attr("style", "color:#000 !important; background-color:"+aData[18]+" !important;");
               // $(nRow).attr("style", "background-color:"+aData[9]+" !important;");
                //$('td',nRow).attr("style", "background-color:transparent !important; color:#000 !important;");
                //$('td:eq(1) a',nRow).attr("style", "color:#000 !important; ");
                
            }*/

                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");

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
            onRowGroup: {
      dataSrc: group_index,
            startRender: function(rows, group, level) {
                var all;

                if (level === 0) {
                    top = group;
                    all = group;
                } else {
                    // if parent collapsed, nothing to do
                    if (!!collapsedGroups[top]) {
                        return;
                    }
                    all = top + group;
                }

                var collapsed = !!collapsedGroups[all];
                var tdc;
                rows.nodes().each(function(r) {

                    r.style.display = collapsed ? 'none' : '';
                    //console.log(r.style.background.color);
                   // r.style.background-color= r.
                });
                let ae=rows.data()[0][5];
                var main_tit='<?php echo app_lang("contact") ?>';
                var main_val=rows.data()[0][6];
                if(check_user=="yes"){
                    main_tit='<?php echo app_lang("contact") ?>';
                    main_val=rows.data()[0][6];
                }else{
                    main_tit='<?php echo app_lang("project") ?>';
                    main_val=rows.data()[0][4];
                }
                if(check_user=="yes"){
                // Add category name to the <tr>. NOTE: Hardcoded colspan
                var add_btn='<a href="#" id="open_add_dialog" class="btn btn-light" style="margin: 0 15px 0 5px;border-radius: 8px;padding: 0.13rem 0.25rem;" title="<?=app_lang('add_subtasks')?>" data-post-task_id="'+group+'" data-post-mang="reservmang" data-act="ajax-modal" data-title="<?=app_lang('add_subtasks')?>" data-action-url="<?php echo get_uri("subtasks/task_modal_form")?>"><i data-feather="plus-circle" class="icon-16"></i></a>';

                return $('<tr/>')
                    .append('<td colspan="21" style="padding-top:4px !important;padding-bottom:4px !important;  background-color:'+rows.data()[0][22]+' !important; color:#000; font-size: 14px;">' + group+'-'+rows.data()[0][4] + ' <span class="badge badge-light  mt0" title="مهمة فرعية">' + rows.count() + '</span>-<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"> <?php echo app_lang("project") ?> </span> <div style="font-size: 13px;display: inline-block; vertical-align: middle;white-space: nowrap;max-width: 110px;overflow: hidden;text-overflow: ellipsis;" title="' +rows.data()[0][5]+ '">' +rows.data()[0][5]+ '</div> -<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"> <?php echo app_lang("contact") ?> </span> ' +rows.data()[0][6]+ '-<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"><?php echo app_lang("christening_number") ?> : </span>'+rows.data()[0][7]+'-<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"> <?php echo app_lang("invoice_number") ?> : </span>'+rows.data()[0][8]+'-<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"> <?php echo app_lang("created_date") ?> : </span>'+rows.data()[0][23]+' - '+rows.data()[0][3]+'  '+add_btn +' <a href="#"  class="edit" style="margin-right:2%;" title="معلومات المهمة #'+group+'" data-post-id="'+group+'" data-act="ajax-modal" data-title="معلومات المهمة #'+group+'" data-action-url="<?=get_uri("tasks/task_view")?>"><i data-feather="edit" class="icon-16" style="color: #fff;"></i></a></td>')
                    .attr('data-name', all)
                    .toggleClass('collapsed', collapsed);
                }else{

                    return $('<tr/>')
                    .append('<td colspan="22" style="padding-top:4px !important;padding-bottom:4px !important;  background-color:'+rows.data()[0][22]+' !important; color:#000; font-size: 14px;">' + group+' - '+rows.data()[0][5].toString().replace(/(<([^>]+)>)/ig, '') + ' <span class="badge badge-light  mt0" > '+' ' + rows.count() + '</span> &nbsp; -  <span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 14px;"> <?php echo app_lang("description") ?> : </span><div style="font-size: 13px;display: inline-block; vertical-align: middle;white-space: nowrap;max-width: 140px;overflow: hidden;text-overflow: ellipsis;" title="' +rows.data()[0][8]+ '">'+rows.data()[0][8].toString().replace(/(<([^>]+)>)/ig, '')+'</div>&nbsp;&nbsp;&nbsp;  -&nbsp;&nbsp;<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"> <?php echo app_lang("created_date") ?> : </span>'+rows.data()[0][24]+'&nbsp;&nbsp;&nbsp; - '+rows.data()[0][3]+' <a href="#"  class="edit" style="margin-right:2%;" title="معلومات المهمة #'+group+'" data-post-id="'+group+'" data-act="ajax-modal" data-title="معلومات المهمة #'+group+'" data-action-url="<?=get_uri("tasks/task_view")?>"><i data-feather="edit" class="icon-16" style="color: #fff;"></i></a></td>')
                    .attr('data-name', all)
                    .toggleClass('collapsed', collapsed);
                }
            }
      
    },
            onInitComplete: function () {
                $("body").tooltip({ selector: '[data-bs-toggle=tooltip]' });
                if (!showOption) {
                    window.scrollTo(0, 210); //scroll to the content for mobile devices
                }
                setPageScrollable();
            }
        });

$('#main-task-table tbody').on('click', 'tr', function(e) {
    var senderElementName = e.target.tagName.toLowerCase();
    if(senderElementName === 'a') {
        senderElementName.click()
e.stopPropagation(); 
    }else{
    if($(this).hasClass("dtrg-start")){
     var name = $(this).data('name');

        collapsedGroups[name] = !collapsedGroups[name];
        //$(this).addClass("collapsed");
        //$("#main-task-table").appTable({newData: null, dataId: 0});
        $('#main-task-table').DataTable().draw(false);
        //$('#main-task-table').DataTable().ajax.reload(null, false); 
    }
}
  });
var mob="",stl="";

if(isMobile()){
    mob=">.navbar-collapse";
    stl="width: 200px; margin:auto;"
}else{
    mob="";
    stl="";
}
$("#main-task-table_wrapper > div:nth-child(1) > div.w-filter-right.float-end.custom-toolbar.pr15"+mob).append('<div class="mr15 DTTT_container"><input type="text" name="monthly_search" id="monthly_search" class="form-control text-center " autocomplete="nope" readonly style="padding: 0.21rem 0.335rem; min-width: 70px;'+stl+'" placeholder="شهري" title="شهري" /></div>');

$('#main-task-table thead tr').clone(false).appendTo('#main-task-table thead');
 
        $('#main-task-table thead tr:eq(1) th').each(function (i) {

            var title = $(this).text();
            var clas=$(this).attr("class").split(/\s+/);
            if(clas[0]=="out_date_f"){
                title="<?php echo app_lang('out_date') ?> فقط";
            }else if(clas[0]=="tmp_return_date_f"){
                title="<?php echo app_lang('tmp_return_date') ?> فقط";
            }
            if(check_user==="yes"){
            if((clas[0]!=="service_type_f") && (clas[0]!=="status_f") && (clas[0]!=="city_name_f") && (clas[0]!=="options_f") && (clas[0]!=="main_task_status_f")){
            $(this).html('<input type="text" name="'+clas[0]+'" class="form-control text-center " autocomplete="nope" style="padding: 0.11rem 0.135rem; min-width: 70px;" placeholder="<?php echo app_lang('search'); ?> - ' + title + '" title="<?php echo app_lang('search'); ?> - ' + title + '" />');
        }else{  
            if((clas[0]=="service_type_f" || clas[0]=="city_name_f" || clas[0]=="main_task_status_f")){
            $(this).html('<input type="text" name="'+clas[0]+'" class="oyd-form text-center " autocomplete="nope" style="padding: 0.11rem 0.235rem; min-width: 70px;" data-select-main="oyd" data-select-name="'+clas[0]+'"   />');
        }else{
            
             $(this).html('');
        }
        }
        }else if((clas[0]!=="rec_inv_status_f") && (clas[0]!=="status_f") && (clas[0]!=="city_name_f") && (clas[0]!=="options_f") && (clas[0]!=="main_task_status_f")){
             $(this).html('<input type="text" name="'+clas[0]+'" class="form-control text-center " style="padding: 0.11rem 0.135rem; min-width: 70px;" placeholder="<?php echo app_lang('search'); ?> - ' + title + '" />');

        }else{
             if((clas[0]=="rec_inv_status_f" || clas[0]=="city_name_f" || clas[0]=="main_task_status_f")){
            $(this).html('<input type="text" name="'+clas[0]+'" class="oyd-form text-center " autocomplete="nope" style="padding: 0.11rem 0.235rem; min-width: 70px;" data-select-main="oyd" data-select-name="'+clas[0]+'"   />');
        }else{
            
             $(this).html('');
        }
        }

 $('input', this).unbind().bind("input", delayAction(function (a) {
    cfil.sub_tasks_id_f=$("input[name=sub_tasks_id_f]").val();
         cfil.guest_nm_f=$("input[name=guest_nm_f]").val();
         cfil.pnt_task_id_f=$("input[name=pnt_task_id_f]").val();
                cfil.guest_phone_f=$("input[name=guest_phone_f]").val();
                cfil.pnt_task_id=$("input[name=pnt_task_id]").val();
                cfil.company_name_f=$("input[name=company_name_f]").val();
                cfil.clients_contact_f=$("input[name=clients_contact_f]").val();
                cfil.christ_num_f=$("input[name=christ_num_f]").val();
                cfil.inv_num_f=$("input[name=inv_num_f]").val();
                cfil.city_name_f=$("input[name=city_name_f]").val();
                cfil.driver_nm_f=$("input[name=driver_nm_f]").val();
                cfil.car_type_f=$("input[name=car_type_f]").val();
                
                //cfil.out_date_f=$("input[name=out_date_f]").val();
                //cfil.tmp_return_date_f=$("input[name=tmp_return_date_f]").val();
                //cfil.sales_act_return_date_f=$("input[name=sales_act_return_date_f]").val();

                cfil.inv_day_count_f=$("input[name=inv_day_count_f]").val();
                cfil.note_f=$("input[name=note_f]").val();
                cfil.created_by_f=$("input[name=created_by_f]").val();

                cfil.supplier_f=$("input[name=supplier_f]").val();
                cfil.car_status_f=$("input[name=car_status_f]").val();
                cfil.car_number_f=$("input[name=car_number_f]").val();
                //cfil.act_return_date_f=$("input[name=act_return_date_f]").val();
                //cfil.act_out_date_f=$("input[name=act_out_date_f]").val();
                cfil.day_count_f=$("input[name=day_count_f]").val();
                cfil.dres_number_f=$("input[name=dres_number_f]").val();
                cfil.amount_f=$("input[name=amount_f]").val();
                cfil.note2_f=$("input[name=note2_f]").val();
                cfil.project_nm_f=$("input[name=project_nm_f]").val();

               
                //$('#main-task-table').dataTable()._fnReDraw(false);
                /*$("#main-task-table").appFilters({
                    reload: !0,
                    filterParams: cfil
                });*/
                $("#main-task-table").appTable({
                    reload: !0,
                    filterParams: cfil
                });
      }, 1e3));
            /*$('input', this).on('keyup', function () {
                
                
                
            });*/
        });

        if(check_user=="yes"){
        

        dateRangepicker('tmp_return_date_f','tmp_return_date_f','tmp_return_date_f_t');

        dateRangepicker('out_date_f','out_date_f','out_date_f_t');
        dateRangepicker('sales_act_return_date_f','sales_act_return_date_f','sales_act_return_date_f_t');

        




        /*$('input[name=out_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom top',
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.out_date_f=e.format();
            $("#main-task-table").appTable({ reload: !0,filterParams: cfil});
        });

        $('input[name=sales_act_return_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom top',
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.sales_act_return_date_f=e.format();
            $("#main-task-table").appTable({ reload: !0,filterParams: cfil});
        });*/
    }else{
        dateRangepicker('act_out_date_f','act_out_date_f','act_out_date_f_t');
        dateRangepicker('act_return_date_f','act_return_date_f','act_return_date_f_t');
        /*$('input[name=act_out_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom top',
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.act_out_date_f=e.format();
            $("#main-task-table").appTable({ reload: !0,filterParams: cfil});
        });

        $('input[name=act_return_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom top',
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.act_return_date_f=e.format();
            $("#main-task-table").appTable({ reload: !0,filterParams: cfil});
        });*/


    }
    $('#monthly_search').datepicker({
            format: "MM-yyyy",
            viewMode: 'months',
            minViewMode: 'months',
            autoclose: true,
            minViewMode: "months",
            orientation: 'bottom top',
            startDate: (new Date(new Date().getFullYear(), 0, 1)),
            endDate:(new Date(new Date().getFullYear(), 11, 1)),
            clearBtn: true
        }).on("changeDate", function(e) {
            if(e.format()){
            cfil.monthly_f=moment(e.format()).format('MM');
        }else{
            cfil.monthly_f="";
        }
            //alert(e.format());
            $("#main-task-table").appTable({ reload: !0,filterParams: cfil});
        });

        
$('input[name=service_type_f]').select2({data: <?php echo json_encode($service_type_dropdown) ?>,minimumResultsForSearch: Infinity});
$('input[name=rec_inv_status_f]').select2({data: <?php echo json_encode($rec_inv_status_dropdown) ?>,minimumResultsForSearch: Infinity});
$('input[name=city_name_f]').select2({data: <?php echo $cities_dropdown ?>,minimumResultsForSearch: Infinity});
$('input[name=main_task_status_f]').select2({data: <?php echo json_encode($statuses2) ?>,minimumResultsForSearch: Infinity});

$("[data-select-main='oyd']").click(function () {

var mkey=$(this).attr("data-select-name");
var mval=$('input[name='+mkey+']').val();
cfil[mkey]=mval;
$("#main-task-table").appTable({ reload: !0,filterParams: cfil});
});


    $(".scroll-div1").css({
            "width": $(".table-responsive").width(),
        });

$(".wmd-view-topscroll").scroll(function(){
        $(".table-responsive")
            .scrollLeft($(".wmd-view-topscroll").scrollLeft());
    });
    $(".table-responsive").scroll(function(){
        $(".wmd-view-topscroll")
            .scrollLeft($(".table-responsive").scrollLeft());
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

     

     $("#hide_top_section").click(function () {
            if ($(this).is(":checked")) {
                $(".float-end.custom-toolbar").hide();
                $(".float-start.toolbar-left-top").hide();
            } else {
                $(".float-end.custom-toolbar").show();
                $(".float-start.toolbar-left-top").show();
            }
        });

   

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
                    $("#main-task-table").appTable({
                reload: !0,
                //filterParams: t.filterParams
            });//$("#main-task-table").appTable({newData: response.data, dataId: response.id});
                        
                    
                    }

                    
                }
            });

            return false;
        });



function dateRangepicker(id,index,index2){

     $('input[name='+id+']').daterangepicker({
            showDropdowns: true,
            "opens": "center",
            autoUpdateInput: false,
            "drops": "auto",
            minYear: 2022,
            maxYear: parseInt(moment().endOf("year").format("YYYY"))+1,
             "minDate": moment().subtract(1, "year").startOf("year").format("YYYY-MM-DD"),
             "maxDate": moment().endOf("year").format("YYYY-MM-DD"),
            locale: {
                format: "YYYY-MM-DD",
                cancelLabel: 'مسح',
                applyLabel: 'موافق',
                "fromLabel": "من",
                "direction": 'rtl',
                "toLabel": "الى",
                "customRangeLabel": "مخصص",
            },
            ranges: {
                "اليوم": [moment(), moment()],
                "أمس": [moment().subtract(1, "days"), moment().subtract(1, "days")],
                "اخر 7 ايام": [moment().subtract(6, "days"), moment()],
                "اخر 30 يوم": [moment().subtract(29, "days"), moment()],
                
            }
        }, function(start, end, label) {
           
           cfil[index]=start.format('YYYY-MM-DD');
           cfil[index2]=end.format('YYYY-MM-DD');
           $("#main-task-table").appTable({ reload: !0,filterParams: cfil});
            $('input[name='+id+']').val(start.format('YYYY-MM-DD')+" الى "+end.format('YYYY-MM-DD'));
            
        });
        $('input[name='+id+']').on('cancel.daterangepicker', function(ev, picker) {
            $('input[name='+id+']').val('');
            cfil[index]="";
           cfil[index2]="";
            $("#main-task-table").appTable({ reload: !0,filterParams: cfil});
        });
        $('input[name='+id+']').val('');
        $('input[name='+id+']').prop('readonly', true);
}
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

<?php //echo view("tasks/subtask_tree_script"); ?>
<?php load_js(array("assets/dataTables.rowGroup.min.js")); ?>

<?php echo view("tasks/batch_update/batch_update_script"); ?>
<?php echo view("tasks/update_task_script"); ?>
<?php echo view("tasks/update_task_read_comments_status_script"); ?>
<?php echo view("tasks/quick_filters_helper_js"); ?>