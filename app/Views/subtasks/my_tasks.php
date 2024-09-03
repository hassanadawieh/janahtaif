<?php load_js(array("assets/daterangepicker/daterangepicker.js")); ?>
<?php load_css(array("assets/daterangepicker/daterangepicker.css")); ?>
<style type="text/css">

table.dataTable tbody td {
    padding: 7px 6px !important;
}
[dir=rtl] table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
    top: 50%;
    right: 3px;
    height: 1em;
    width: 1em;
    margin-top: -9px;
    display: block;
    position: absolute;
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
}

table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before, table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
    top: 50%;
    left: 3px;
    height: 1em;
    width: 1em;
    margin-top: -5px;
    display: block;
    position: absolute;
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
}
.txt_color{
    color: #366fdc;
}
/*
#subtask-table.dataTable th {
   
    white-space: nowrap;

}*/
@media (max-width: 767px){
#page-content {
    padding: 15px 10px !important;
}
}
#subtask-table.dataTable th {
   
    white-space: nowrap;

}
#subtask-table.dataTable tbody td {
    padding: 5px 4px !important;
}
.sub-task-icon {   
    padding: 0.2px 1.3px;
    font-size: 11px;
}

.dropdown-menu .list-group-item:not(.inline-loader) {
    padding: 5px 10px !important;
    text-align: center;
}
#subtask-table > thead > tr:nth-child(2) > th{
    padding: 2px !important;
}

.dtrg-start{
    cursor: pointer;
}

div.DTTT_container{
    margin-bottom: 7px !important;
    margin-left: 6px !important;
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

    <ul class="nav nav-tabs bg-white title" role="tablist" style="padding-right: 10px;">
        <li class="title-tab"><h4 class="pl15 pt10 pr15" style="font-size: 15px;"><?php echo app_lang("sub_tasks").' - '.app_lang("reserv_mang"); ?>
            
        </h4></li>




        <?php echo view("subtasks/tabs", array("active_tab" => "tasks_list", "selected_tab" => $tab, "task_id" => $task_id)); ?>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group"><?php //echo $status_ids; ?>
                <?php
                
                if ($can_create_tasks) {
                    $check_add=false;
                    if ($can_update_maintask_after_closed) {
                        $check_add=true;
                    }else{
                        if($main_task->is_closed==1){
                            $check_add=true;
                        }else{
                            $check_add=false;
                        }

                    }
                    //echo modal_anchor(get_uri("subtasks/import_tasks_modal_form"), "<i data-feather='upload' class='icon-16'></i> " . app_lang('import_tasks'), array("class" => "btn btn-default", "title" => app_lang('import_tasks')));

                    /*task_modal_form in Projects Controller*/

                    if($main_task){
                    if($check_add){
                    echo modal_anchor(get_uri("subtasks/task_modal_form/".$task_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_multiple_tasks'), array("class" => "btn btn-outline-light", "title" => app_lang('add_multiple_tasks'), "data-post-task_id" => $task_id, "data-post-add_type" => "multiple", "data-post-mang" => "reservmang"));
                
                echo modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("id" => "open_add_dialog","class" => "btn btn-outline-light", "title" => app_lang('add_task'), "data-post-task_id" => $task_id, "data-post-mang" => "reservmang"));
            }else {
                echo modal_anchor("", "<i data-feather='plus-circle' class='icon-16'></i> المهمة الرئيسية مغلقة" , array("class" => "btn btn-outline-light", "title" => app_lang('add_task')));
            }
        }else{
            echo modal_anchor(get_uri("subtasks/task_modal_form/".$task_id), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_multiple_tasks'), array("class" => "btn btn-outline-light", "title" => app_lang('add_multiple_tasks'), "data-post-task_id" => $task_id, "data-post-add_type" => "multiple", "data-post-mang" => "reservmang"));
                
                echo modal_anchor(get_uri("subtasks/task_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_task'), array("id" => "open_add_dialog","class" => "btn btn-outline-light", "title" => app_lang('add_task'), "data-post-task_id" => $task_id, "data-post-mang" => "reservmang"));
        }
        
                }
                ?>

                
            
      




                <?php 
                    echo modal_anchor(get_uri("subtasks/post_cuses_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('update_status'), array("class" => "btn btn-default","style" => "display:none;","id" => "writeReson" , "data-post-id" => 0, "data-post-mang" => "reservmang", "title" => app_lang('update_status')));

                    ?>



                  
            </div>
        </div>

    </ul>

    <div class="card">

        
        

        <div class="table-responsive" style="overflow-y: scroll;">
            <table id="subtask-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>

<?php
//if we get any task parameter, we'll show the task details modal automatically
$preview_task_id = get_array_value($_GET, 'task');
if ($preview_task_id) {
    echo modal_anchor(get_uri("subtasks/task_view"), "", array("id" => "preview_task_link", "title" => app_lang('task_info') . " #$preview_task_id", "data-post-id" => $preview_task_id));
}

$main_task_statuses = array();
    $is_selected1 = true;
    $main_task_statuses[] = array("text" => app_lang('open'), "value" => 1, "isChecked" => $is_selected1);
    $main_task_statuses[] = array("text" => app_lang('closed'), "value" => 2, "isChecked" => $is_selected1);


$statuses = array();
foreach ($task_statuses as $status) {
    if($status_id && $status_id!=0){
    if($status_id==$status->id){
        $is_selected = true;
    }else{$is_selected = false;}
}else{
$is_selected = false;
}

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
   

var showOption = true,
                idColumnClass = "w5p",
                titleColumnClass = "w10p";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w25p";
            titleColumnClass = "w45p";
        }

        var showOptions = true;
      

        //open task details modal automatically 

        if ($("#preview_task_link").length) {
            $("#preview_task_link").trigger("click");
        }


        <?php 
        

        $service_type_dropdown = array(array("id" => "", "text" => "- ". app_lang("service_type"). " -"));
        $service_type_dropdown[] = array("id" => "with_driver", "text" => "سيارة بسائق");
        $service_type_dropdown[] = array("id" => "no_driver", "text" => "بدون سائق");
        $service_type_dropdown[] = array("id" => "deliver", "text" => "توصيلة");
        $service_type_dropdown[] = array("id" => "no_car", "text" => "سائق بدون سيارة");

        $statuses2 = array(array("id" => "", "text" => "- ". app_lang("status"). " -"));
    $statuses2[] = array("id" => 1, "text" => app_lang('open'));
    $statuses2[] = array("id" => 2, "text" => app_lang('closed'));


        ?>
        var mphone='';

      loadInvoicesTable = function (selector, dateRange) {

         
};
var mytable;
//,out_date_f:'',out_date_f_t:''
var cfil={deleted_client: '',sub_tasks_id_f:'',pnt_task_id_f:'',guest_nm_f:'',guest_phone_f:'',pnt_task_id:'',company_name_f:'',clients_contact_f:'',christ_num_f:'',inv_num_f:'',city_name_f:'',driver_nm_f:'',car_type_f:'',tmp_return_date_f:'',tmp_return_date_f_t:'',end_date_f:'',end_date_f_t:'',inv_day_count_f:'',note_f:'', sub_task_note_f:'',created_by_f:'',main_task_status_f:''
        ,supplier_f:'',car_status_f:'',car_number_f:'',act_return_date_f:'',act_return_date_f_t:'',act_out_date_f:'',act_out_date_f_t:'',description_f:'',day_count_f:'',dres_number_f:'',amount_f:'',note2_f:'',project_nm_f:'',monthly_f:''};
    $(document).ready(function () {


    
var collapsedGroups = {};
var top = '';

 //$("#subtask-table").tooltip({ selector: '[data-bs-toggle=tooltip]' });
         mytable=$("#subtask-table").appTable({
            source: '<?php echo_uri("subtasks/list_data/" . $task_id)?>',
            serverSide: true,
            dateRangeType: "",
            order: [[1, "desc"]],
            responsive: false, //hide responsive (+) icon
            filterParams: cfil,
            filterDropdown: [
                //{name: "driver_id", class: "w150", options: <?php //echo $drivers_dropdown; ?>},
               // {name: "car_type_id", class: "w150",options: <?php //echo $cars_type_dropdown; ?>},
                //{name: "city_id", class: "w150",options: <?php //echo $cities_dropdown; ?>},
                //{name: "service_type", class: "w150", options: <?php //echo json_encode($service_type_dropdown); ?>},
                //{name: "specific_user_id", class: "w150", options: <?php //echo $team_members_dropdown; ?>},
                {name: "priority_id", class: "w100", options: <?php echo $priorities_dropdown; ?>},
                {name: "cls_id", class: "w100", options: <?php echo $maintask_clsifications_dropdown; ?>},
                 //reset milestone on changing of project

                 
            ],
            rangeDatepicker: [{startDate: {name: "start_date", value: ''}, endDate: {name: "end_date", value: ''}, showClearButton: true}],
            /*singleDatepicker:[{name: "out_date", defaultText: "<?php //echo app_lang('out_date') ?>",
                    },{name: "tmp_return_date", defaultText: "<?php //echo app_lang('tmp_return_date') ?>",
                    },{name: "sales_act_return_date", defaultText: "<?php //echo app_lang('sales_act_return_date') ?>",
                    }],*/
            
            multiSelect: [
                /*{
                    name: "main_status_id",
                    text: "<?php echo app_lang('status').' '.app_lang('main_task'); ?>",
                    options: <?php echo json_encode($main_task_statuses); ?>
                },*/{
                    name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>
                }
            ],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": "sub_tasks_id_f text-center "+idColumnClass+" all", order_by: "sub_task_id"},
                {title: '<?php echo app_lang("main_task") ?>',"class":'pnt_task_id_f', order_by: "pnt_task_id"},
                {title: '<?php echo app_lang("status") ?>',"class":'main_task_status_f'},
                {title: '<?php echo app_lang("client") ?>',"class":'company_name_f',render: function(data, type, full, meta) {
                    return showOption?"<div style='white-space: nowrap;max-width: 110px;overflow: hidden;text-overflow: ellipsis;'  title='"+data+"'>"+data+"</div>":data;
                } },
                {targets:5,visible: false,title: '<?php echo app_lang("project") ?>',"class":'project_nm_f'},
                {title: '<?php echo app_lang("contact") ?>',"class":'clients_contact_f',render: function(data, type, full, meta) {
                    return showOption?"<div style='white-space: nowrap;max-width: 90px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip'  title='"+data+"'>"+data+"</div>":data;
                } },
                {title: '<?php echo app_lang("christening_number") ?>',"class":'christ_num_f' ,render: function(data, type, full, meta) {
                    return showOption? "<div style='white-space: nowrap;max-width: 90px;overflow: hidden;text-overflow: ellipsis;' title='"+data+"'>"+data+"</div>":data;
                } },
                
                {title: '<?php echo app_lang("invoice_number") ?>',"class":'inv_num_f'},
                {title: '<?php echo app_lang("description") ?>',"class":'description_f' ,render: function(data, type, full, meta) {
                    return showOption? "<div style='white-space: nowrap;max-width: 90px;overflow: hidden;text-overflow: ellipsis;'  title='"+data+"'>"+data+"</div>":data;
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
                {title: '<?php echo app_lang("driver_name") ?>',"class":'driver_nm_f', order_by: "driver_id",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:100px; '>"+data+"</div>";
                } },
                
                {title: '<?php echo app_lang("car_type") ?>',"class":'car_type_f', order_by: "car_type_id"},
                // {title: '<?php //echo app_lang("out_exp_date&time") ?>', "class": "out_date_f text-center ", order_by: "out_date",render: function(data, type, full, meta) {
                //     return "<div dir='ltr'>"+data+"</div>";
                // } },
                //{title: '<?php //echo app_lang("exp_out_time") ?>', "class": "text-center ", order_by: "exp_out_time"},
                {title: '<?php echo app_lang("start_date") ?>', "class": "start_date_f text-center ", order_by: "start_date"},
                {title: '<?php echo app_lang("ten_out_date") ?>', "class": "tmp_return_date_f text-center ", order_by: "tmp_return_date"},
                {title: '<?php echo app_lang("end_date") ?>', "class": "end_date_f text-center ", order_by: "end_date"},
                {title: '<?php echo app_lang("booking_period") ?>',"class":'booking_period_f', order_by: "booking_period"},
                {title: '<?php echo app_lang("note") ?>', "class": "sub_task_note_f text-center ", order_by: "note",render: function(data, type, full, meta) {
                    return "<div style='white-space: nowrap;max-width: 130px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,4' data-bs-placement='top' data-bs-html='true' title='"+data+"'>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("created_by") ?>',"class":'created_by_f', order_by: "created_by",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:90px; '>"+data+"</div>";
                } },
                {visible: false, searchable: false},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("status") ?>',"class": "status_f"}
                <?php echo $custom_field_headers; ?>,
                //{visible: false, searchable: false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "options_f text-center option ", visible: showOptions,render: function(data, type, full, meta) {
                    return "<div style='white-space:nowrap;max-width:120px; '>"+data+"</div>";
                } },
            ],
            printColumns: combineCustomFieldsColumns([1, 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,23,24], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,23,24], '<?php echo $custom_field_headers; ?>'),
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
            if(!aData[1]){
                $(nRow).attr("style", "display:none !important;");
            }
            console.log("h");

                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");

                //add activated sub task filter class
                setTimeout(function () {
                    var searchValue = $('#subtask-table').closest(".dataTables_wrapper").find("input[type=search]").val();
                    if (searchValue.substring(0, 1) === "#") {
                        $('#subtask-table').find("[main-task-id='" + searchValue + "']").removeClass("filter-sub-task-button").addClass("remove-filter-button sub-task-filter-active");
                    }
                }, 50);
            },
            onRelaodCallback: function () {
                //hideBatchTasksBtn(true);
            },
            /*onRowGroup: {
      dataSrc: 2,
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

                var add_btn='<a href="#" id="open_add_dialog" class="btn btn-light transparent" style="margin: 0 15px 0 5px;border-radius: 8px;padding: 0.13rem 0.25rem;" title="<?=app_lang('add_subtasks')?>" data-post-task_id="'+group+'" data-post-mang="reservmang" data-act="ajax-modal" data-title="<?=app_lang('add_subtasks')?>" data-action-url="<?php echo get_uri("subtasks/task_modal_form")?>"><i data-feather="plus-circle" class="icon-16"></i></a>';
                // Add category name to the <tr>. NOTE: Hardcoded colspan
                return $('<tr/>')
                    .append('<td colspan="21" style="padding-top:4px !important;padding-bottom:4px !important;  background-color:'+rows.data()[0][22]+' !important; color:#000; font-size: 14px;">' + group+'-'+rows.data()[0][4] + ' <span class="badge badge-light  mt0" title="مهمة فرعية">' + rows.count() + '</span>-<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"> <?php echo app_lang("project") ?> </span> <div style="font-size: 13px;display: inline-block; vertical-align: middle;white-space: nowrap;max-width: 110px;overflow: hidden;text-overflow: ellipsis;" title="' +rows.data()[0][5]+ '">' +rows.data()[0][5]+ '</div> -<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"> <?php echo app_lang("contact") ?> </span> ' +rows.data()[0][6]+ '-<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"><?php echo app_lang("christening_number") ?> : </span>'+rows.data()[0][7]+'-<span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 13px;"> <?php echo app_lang("invoice_number") ?> : </span>'+rows.data()[0][8]+' - '+rows.data()[0][3]+'  '+add_btn +' <a href="#"  class="edit" style="margin-right:2%;" title="معلومات المهمة #'+rows.data()[0][2]+'" data-post-id="'+rows.data()[0][2]+'" data-act="ajax-modal" data-title="معلومات المهمة #'+rows.data()[0][2]+'" data-action-url="<?=get_uri("tasks/task_view")?>"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit icon-16" style="color: #fff;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg></a></td>')
                    .attr('data-name', all)
                    .toggleClass('collapsed', collapsed);
            }
      
    },*/
            onInitComplete: function () {
               
                if (!showOption) {
                    window.scrollTo(0, 210); //scroll to the content for mobile devices
                }
                setPageScrollable();

            }
        });

$('#subtask-table tbody').on('click', 'tr', function(e) {
    var senderElementName = e.target.tagName.toLowerCase();
    if(senderElementName === 'a') {
        senderElementName.click()
e.stopPropagation(); 
    }else{
    if($(this).hasClass("dtrg-start")){
     var name = $(this).data('name');

        collapsedGroups[name] = !collapsedGroups[name];
        //$(this).addClass("collapsed");
        //$("#subtask-table").appTable({newData: null, dataId: 0});
        $('#subtask-table').DataTable().draw(false);
        //$('#subtask-table').DataTable().ajax.reload(null, false); 
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
$("#subtask-table_wrapper > div:nth-child(1) > div.w-filter-right.float-end.custom-toolbar.pr15"+mob).append('<div class="mr15 DTTT_container"><input type="text" name="monthly_search" id="monthly_search" class="form-control text-center " autocomplete="off" readonly style="padding: 0.21rem 0.335rem; min-width: 70px;max-width: 100px;'+stl+'" placeholder="شهري" title="شهري" /></div>');


/*console.log("hdddddddddddddddello");
                         var api = this.oApi._fnGetTrNodes( settings ); // Had to change this from this.api();
                         var rows = api.rows({page:'current'}).nodes(); // Giving an error
                         var last=null;
                         api.column(4, {page:'current'} ).data().each( function ( group, i ) {
                            if ( last !== group ) {
                                $(rows).eq( i ).before('<tr class="group"><td colspan="4">'+group+'</td></tr>');
 
                last = group;
            }
        });*/
        var t_id="<?php echo $open_add; ?>";
        if(t_id=="yes"){
            //alert("dfsfsd");
            $('#open_add_dialog').trigger("click");
        }

        $('#subtask-table thead tr').clone(false).appendTo('#subtask-table thead');
 
        $('#subtask-table thead tr:eq(1) th').each(function (i) {

            var title = $(this).text();
            var clas=$(this).attr("class").split(/\s+/);
            if(clas[0]=="out_date_f"){
                title="<?php echo app_lang('out_date') ?> فقط";
            }else if(clas[0]=="tmp_return_date_f"){
                title="<?php echo app_lang('tmp_return_date') ?> فقط";
            }
            var typ="text";
            typ= clas[0]!=="inv_day_count_f"?"text":"number";

            
            if((clas[0]!=="service_type_f") && (clas[0]!=="created_by_f") && (clas[0]!=="car_type_f") && (clas[0]!=="company_name_f") && (clas[0]!=="driver_nm_f") && (clas[0]!=="status_f") && (clas[0]!=="city_name_f") && (clas[0]!=="options_f") && (clas[0]!=="main_task_status_f")){
            $(this).html('<input type="'+typ+'" name="'+clas[0]+'" class="form-control text-center " autocomplete="nope" style="padding: 0.11rem 0.135rem; min-width: 80px; " title="بحث - ' + title + '" placeholder="بحث -' + title + '" />');
        }else{
             if((clas[0]=="service_type_f" || clas[0]=="created_by_f" || clas[0]=="car_type_f" || clas[0]=="company_name_f" || clas[0]=="driver_nm_f" || clas[0]=="city_name_f" || clas[0]=="main_task_status_f")){
            $(this).html('<input type="text" name="'+clas[0]+'" class="oyd-form text-center " autocomplete="nope" style="padding: 0.11rem 0.235rem; min-width: 80px;max-width:120px;" data-select-main="oyd" data-select-name="'+clas[0]+'"   />');
        }else{
            
             $(this).html('');
        }
        }

 $('input', this).unbind().bind("input", delayAction(function (a) {
         cfil.guest_nm_f=$("input[name=guest_nm_f]").val();
         cfil.pnt_task_id_f=$("input[name=pnt_task_id_f]").val();
                cfil.guest_phone_f=$("input[name=guest_phone_f]").val();
                cfil.pnt_task_id=$("input[name=pnt_task_id]").val();
                //cfil.company_name_f=$("input[name=company_name_f]").val();
                cfil.clients_contact_f=$("input[name=clients_contact_f]").val();
                cfil.christ_num_f=$("input[name=christ_num_f]").val();
                cfil.inv_num_f=$("input[name=inv_num_f]").val();
               cfil.description_f=$("input[name=description_f]").val();

                cfil.inv_day_count_f=$("input[name=inv_day_count_f]").val();
                cfil.note_f=$("input[name=note_f]").val();
                cfil.sub_task_note_f=$("input[name=sub_task_note_f]").val();
                cfil.created_by_f=$("input[name=created_by_f]").val();

                //$('#subtask-table').dataTable()._fnReDraw(false);
                /*$("#subtask-table").appFilters({
                    reload: !0,
                    filterParams: cfil
                });*/
                $("#subtask-table").appTable({
                    reload: !0,
                    filterParams: cfil
                });
      }, 1e3));
            /*$('input', this).on('keyup', function () {
                
                
                
            });*/
        });

        dateRangepicker('tmp_return_date_f','tmp_return_date_f','tmp_return_date_f_t');
        dateRangepicker('end_date_f','end_date_f','end_date_f_t');
        dateRangepicker('start_date_f','start_date_f','start_date_f_t');

        // dateRangepicker('out_date_f','out_date_f','out_date_f_t');
        // dateRangepicker('sales_act_return_date_f','sales_act_return_date_f','sales_act_return_date_f_t');

        /*$('input[name=tmp_return_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.tmp_return_date_f=e.format();
            $("#subtask-table").appTable({ reload: !0,filterParams: cfil});
        });


        $('input[name=out_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.out_date_f=e.format();
            $("#subtask-table").appTable({ reload: !0,filterParams: cfil});
        });

        $('input[name=sales_act_return_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.sales_act_return_date_f=e.format();
            $("#subtask-table").appTable({ reload: !0,filterParams: cfil});
        });*/

        $('#monthly_search').datepicker({
            format: "MM",
            viewMode: 'months',
            minViewMode: 'months',
            startView: 'months',
            autoclose: true,
            maxViewMode: 'months',
            orientation: 'bottom top',
            startDate: (new Date(new Date().getFullYear(), 0, 1)),
            endDate:(new Date(new Date().getFullYear(), 11, 1)),
            clearBtn: true
        }).on("changeDate", function(e) {
            if(e.format()){
            cfil.monthly_f=moment(e.format()+'-2024','MMM-YYYY').format('MM');
        }else{
            cfil.monthly_f="";
        }
            //alert(e.format());
            $("#subtask-table").appTable({ reload: !0,filterParams: cfil});
        });

        $('input[name=service_type_f]').select2({data: <?php echo json_encode($service_type_dropdown) ?>,minimumResultsForSearch: Infinity});
$('input[name=city_name_f]').select2({data: <?php echo $cities_dropdown ?>,minimumResultsForSearch: Infinity});
$('input[name=main_task_status_f]').select2({data: <?php echo json_encode($statuses2) ?>,minimumResultsForSearch: Infinity});
$('input[name=driver_nm_f]').select2({data: <?php echo $drivers_dropdown ?>});
$('input[name=company_name_f]').select2({data: <?php echo $clients_dropdown ?>});
$('input[name=car_type_f]').select2({data: <?php echo $cars_type_dropdown ?>,minimumResultsForSearch: Infinity});
$('input[name=created_by_f]').select2({data: <?php echo $team_members_dropdown ?>});


$("[data-select-main='oyd']").click(function () {

var mkey=$(this).attr("data-select-name");
var mval=$('input[name='+mkey+']').val();
cfil[mkey]=mval;
$("#subtask-table").appTable({ reload: !0,filterParams: cfil});
});

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "tasks_list") {
                $("[data-tab='#tasks_list']").trigger("click");

                //save the selected tab in browser cookie
                setCookie("sub_selected_tab_" + "<?php echo $login_user->id; ?>", "tasks_list");
            }
        }, 210);





 if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

    });

function dateRangepicker(id,index,index2){

     $('input[name='+id+']').daterangepicker({
            showDropdowns: true,
            "opens": "center",
            autoUpdateInput: false,
            "drops": "auto",
            minYear: 2023,
            maxYear: parseInt(moment().endOf("year").format("YYYY"))+1,
             "minDate": moment().subtract(1, "year").startOf("year").format("YYYY-MM-DD"),
             "maxDate": moment().endOf("year").format("YYYY-MM-DD"),
            locale: {
                format: "YYYY-MM-DD",
                cancelLabel: 'مسح',
                applyLabel: 'موافق',
                "direction": 'rtl',
                "fromLabel": "من",
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
           $("#subtask-table").appTable({ reload: !0,filterParams: cfil});
            $('input[name='+id+']').val(start.format('YYYY-MM-DD')+" الى "+end.format('YYYY-MM-DD'));
            
        });
        $('input[name='+id+']').on('cancel.daterangepicker', function(ev, picker) {
            $('input[name='+id+']').val('');
            cfil[index]="";
           cfil[index2]="";
            $("#subtask-table").appTable({ reload: !0,filterParams: cfil});
        });
        $('input[name='+id+']').val('');
        $('input[name='+id+']').prop('readonly', true);
}
</script>

<?php load_js(array("assets/dataTables.rowGroup.min.js")); ?>
<?php echo view("subtasks/quick_filters_helper_js"); ?>