<?php load_js(array("assets/daterangepicker/daterangepicker.js")); ?>
<?php load_css(array("assets/daterangepicker/daterangepicker.css")); ?>
<style type="text/css">
  .w5p{
    width: 5.6%;
}
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
div.DTTT_container{
    margin-bottom: 7px !important;
    margin-left: 6px !important;
}
.dtrg-start{
    cursor: pointer;
}
#subtask-table.dataTable th {
   
    white-space: nowrap;

}
#subtask-table.dataTable tbody td {
    padding: 5px 4px !important;
}
#subtask-table > thead > tr:nth-child(2) > th{
    padding: 2px !important;
}
.sub-task-icon {   
    padding: 0.2px 1.3px;
    font-size: 11px;
}
.dropdown-menu .list-group-item:not(.inline-loader) {
    padding: 5px 10px !important;
    text-align: center;
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
        <li class="title-tab"><h4 class="pl15 pt10 pr15" style="font-size: 15px;"><?php echo app_lang("sub_tasks").' - '.app_lang("supply_mang");  ?>
            
        </h4></li>




        <?php echo view("subtasks/tabs", array("active_tab" => "tasks_list", "selected_tab" => $tab, "task_id" => $task_id)); ?>

        <div class="tab-title clearfix no-border">
            <div class="title-button-group"><?php //echo $status_ids; ?>
               




                <?php 
                    echo modal_anchor(get_uri("subtasks/post_cuses_modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('update_status'), array("class" => "btn btn-default","style" => "display:none;","id" => "writeReson" , "data-post-id" => 0, "data-post-mang" => "supplymang", "title" => app_lang('update_status')));

                    ?>
            </div>
        </div>

    </ul>
    <div class="col-md-12 card" id="myCard" >

        
        

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
    echo modal_anchor(get_uri("subtasks/task_view_supply"), "", array("id" => "preview_task_link", "title" => app_lang('task_info') . " #$preview_task_id", "data-post-id" => $preview_task_id));
}

$statuses = array();
$statuses3 = array();
$statuses3 = array(array("id" => "", "text" => "- ". app_lang('status').' '.app_lang('reserv_mang'). " -"));
foreach ($task_statuses as $status) {
    if($status_id && $status_id!=0){
    if($status_id==$status->id){
        $is_selected = true;
    }else{$is_selected = false;}
}else{
$is_selected = true;
}

    /*if (isset($selected_status_id) && $selected_status_id) {
        //if there is any specific status selected, select only the status.
        if ($selected_status_id == $status->id) {
            $is_selected = true;
        }
    } else if ($status->key_name != "done") {
        $is_selected = true;
    }*/

    $statuses[] = array("text" => ($status->key_name ? app_lang($status->key_name) : $status->title), "value" => $status->id);
    $statuses3[] = array("id" =>$status->id , "text" => ($status->key_name ? app_lang($status->key_name) : $status->title));
}
?>



<script type="text/javascript">
   

var showOption = true,
                idColumnClass = "w5p",
                titleColumnClass = "w15p",
                titleColumnClass2 = "w20p";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w15p";
            titleColumnClass = "w15p";
            titleColumnClass2 = "";
        }
        var showOptions = true;
      

        //open task details modal automatically 

        if ($("#preview_task_link").length) {
            $("#preview_task_link").trigger("click");
        }


        <?php 
        $statuses2 = array(array("id" => "", "text" => "- ". app_lang("status"). " -"));
    $statuses2[] = array("id" => 1, "text" => app_lang('open'));
    $statuses2[] = array("id" => 2, "text" => app_lang('closed'));

        $rec_inv_status_dropdown = array(array("id" => "", "text" => '- '. app_lang("rec_inv_status2"). ' -'));
        $rec_inv_status_dropdown[] = array("id" => "wait_inv", "text" => app_lang("wait_inv"));
        $rec_inv_status_dropdown[] = array("id" => "rec_inv", "text" => app_lang("rec_inv"));

        $f_dropdown = array(array("id" => "", "text" => '- '. app_lang("filters"). ' -'));
        $f_dropdown[] = array("id" => "no_supplier", "text" => app_lang("tasks_without_supplier"), "isSelected" => $filter=="no_supplier"?true:false);

        $f_dropdown[] = array("id" => "wait_inv", "text" => app_lang("tasks_without_supplier_invoice"), "isSelected" => $filter=="wait_inv"?true:false);
        $f_dropdown[] = array("id" => "no_act_return_date", "text" => app_lang("tasks_without_return_date"), "isSelected" => $filter=="no_act_return_date"?true:false);

        $f_dropdown[] = array("id" => "no_act_out_time", "text" => app_lang("tasks_without_out_time"), "isSelected" => $filter=="no_act_out_time"?true:false);

        $f_dropdown[] = array("id" => "24houer", "text" => app_lang("subtasks_to_go"), "isSelected" => $filter=="24houer"?true:false);
        ?>

        
var cfil={task_id: '<?php echo $task_id ?>',deleted_client: '',sub_tasks_id_f:'',pnt_task_id_f:'',guest_nm_f:'',guest_phone_f:'',pnt_task_id:'',company_name_f:'',clients_contact_f:'',christ_num_f:'',inv_num_f:'',city_name_f:'',driver_nm_f:'',car_type_f:'',inv_day_count_f:'',note_f:'',created_by_f:''
        ,supplier_f:'',car_status_f:'',description_f:'',car_number_f:'',act_return_date_f:'',act_return_date_f_t:'',act_out_date_f:'',act_out_date_f_t:'',day_count_f:'',dres_number_f:'',amount_f:'',note2_f:'',project_nm_f:'',monthly_f:''};
    $(document).ready(function () {

        var collapsedGroups = {};
        var top = '';

        $('#subtask-table').appTable({
            source: '<?php echo_uri("subtasks/list_data_supply") ?>',
            responsive: false,
            serverSide: true,
            order: [[1, "desc"]],
           // responsive: true, //hide responsive (+) icon
            filterParams: cfil,
            dateRangeType: "",

            rangeDatepicker: [{startDate: {name: "start_date", value: ''}, endDate: {name: "end_date", value: ''}, showClearButton: true}],

            filterDropdown: [
                {name: "reservmang_status", class: "w200", options: <?php echo json_encode($statuses3); ?>},
                //{name: "supplier_id", class: "w100", options: <?php echo $suppliers_dropdown; ?>},
                {name: "filter", class: "w150",options: <?php echo json_encode($f_dropdown) ; ?>},
                // {name: "car_type_id", class: "w150",options: <?php// echo $cars_type_dropdown; ?>},
                //{name: "mang", visible: false,value: <?php //echo $mang ; ?>},
                {name: "priority_id", class: "w100", options: <?php echo $priorities_dropdown; ?>},
                //{name: "project_id", class: "w200", options: <?php //echo $projects_dropdown; ?>, dependent: ["milestone_id"]}, //reset milestone on changing of project

                 
            ],

            /*singleDatepicker:[{name: "act_out_date", defaultText: "<?php //echo app_lang('act_out_date') ?>"},
                     {name: "act_return_date", defaultText: "<?php //echo app_lang('act_return_date') ?>"}],*/
           
            
           
            multiSelect: [
                {
                    name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>
                }
            ],
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class":"sub_tasks_id_f text-center "+ idColumnClass, order_by: "sub_task_id"},
                
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
                {title: '<?php echo app_lang("supplier_name") ?>', "class":"supplier_f "+ titleColumnClass, "iDataSort": 3, order_by: "supplier_id",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:90px; '>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("driver_name") ?>',"class":'driver_nm_f', order_by: "driver_id",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:90px; '>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("car_type") ?>',"class":'car_type_f', order_by: "car_type_id"},
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
                {title: '<?php echo app_lang("created_by") ?>',"class":"created_by_f", order_by: "created_by"},
                {visible: false, searchable: false},

                {title: '<?php echo app_lang("status") ?>', "class": "status_f"}
                <?php echo $custom_field_headers; ?>,
                //{visible: false, searchable: false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "options_f text-center option ", visible: showOptions},
            ],
            printColumns: combineCustomFieldsColumns([1, 2,3,5,8,9,10,11,12,14,15,16,17,18,19,20,21,24,26], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2,3,5,8,9,10,11,12,14,15,16,17,18,19,20,21,24,26], '<?php echo $custom_field_headers; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");

                //add activated sub task filter class
               if(!aData[1]){
                $(nRow).attr("style", "display:none !important;");
            }
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

                // Add category name to the <tr>. NOTE: Hardcoded colspan
                return $('<tr/>')
                    .append('<td colspan="22" style="padding-top:4px !important;padding-bottom:4px !important;  background-color:'+rows.data()[0][23]+' !important; color:#000; font-size: 14px;">' + group+' - '+rows.data()[0][5].toString().replace(/(<([^>]+)>)/ig, '') + ' <span class="badge badge-light  mt0" > '+' ' + rows.count() + '</span> &nbsp; -  <span style="text-shadow: 1px 1px 4px black;color:#fff;font-size: 14px;"> <?php echo app_lang("description") ?> : </span>'+rows.data()[0][8].toString().replace(/(<([^>]+)>)/ig, '')+'&nbsp;&nbsp;&nbsp;&nbsp; - '+rows.data()[0][3]+' <a href="#"  class="edit" style="margin-right:2%;" title="معلومات المهمة #'+group+'" data-post-id="'+group+'" data-act="ajax-modal" data-title="معلومات المهمة #'+group+'" data-action-url="<?=get_uri("tasks/task_view")?>"><i data-feather="edit" class="icon-16" style="color: #fff;"></i></a></td>')
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
$("#subtask-table_wrapper > div:nth-child(1) > div.w-filter-right.float-end.custom-toolbar.pr15"+mob).append('<div class="mr15 DTTT_container"><input type="text" name="monthly_search" id="monthly_search" class="form-control text-center " autocomplete="off" readonly style="padding: 0.21rem 0.335rem; min-width: 70px; max-width: 100px;'+stl+'" placeholder="شهري" title="شهري" /></div>');

$('#subtask-table thead tr').clone(false).appendTo('#subtask-table thead');
 
        $('#subtask-table thead tr:eq(1) th').each(function (i) {

            var title = $(this).text();
            var clas=$(this).attr("class").split(/\s+/);
            var typ="text";
            typ= clas[0]!=="day_count_f"?"text":"number";
            if((clas[0]!=="rec_inv_status_f") && (clas[0]!=="created_by_f") && (clas[0]!=="supplier_f") && (clas[0]!=="driver_nm_f") && (clas[0]!=="car_type_f") && (clas[0]!=="status_f") && (clas[0]!=="city_name_f") && (clas[0]!=="options_f") && (clas[0]!=="main_task_status_f")){
             $(this).html('<input type="'+typ+'" name="'+clas[0]+'" class="form-control text-center " style="padding: 0.11rem 0.135rem; min-width: 70px; " placeholder="<?php echo app_lang('search'); ?> - ' + title + '" />');

        }else{
             if((clas[0]=="rec_inv_status_f" || clas[0]=="created_by_f" || clas[0]=="car_type_f"  || clas[0]=="supplier_f" || clas[0]=="driver_nm_f" || clas[0]=="city_name_f" || clas[0]=="main_task_status_f")){
            $(this).html('<input type="text" name="'+clas[0]+'" class="oyd-form text-center " autocomplete="nope" style="padding: 0.11rem 0.235rem; min-width: 70px; max-width:120px;" data-select-main="oyd" data-select-name="'+clas[0]+'"   />');
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
                cfil.description_f=$("input[name=description_f]").val();
                cfil.city_name_f=$("input[name=city_name_f]").val();
                cfil.driver_nm_f=$("input[name=driver_nm_f]").val();
                // cfil.car_type_f=$("input[name=car_type_f]").val();
                

                //cfil.inv_day_count_f=$("input[name=inv_day_count_f]").val();
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
       /* $('input[name=act_out_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom top',
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.act_out_date_f=e.format();
            $("#subtask-table").appTable({ reload: !0,filterParams: cfil});
        });

        $('input[name=act_return_date_f]').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            orientation: 'bottom top',
            clearBtn: true
        }).on("changeDate", function(e) {
            cfil.act_return_date_f=e.format();
            $("#subtask-table").appTable({ reload: !0,filterParams: cfil});
        });*/

        dateRangepicker('act_out_date_f','act_out_date_f','act_out_date_f_t');
        dateRangepicker('act_return_date_f','act_return_date_f','act_return_date_f_t');

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


        $('input[name=rec_inv_status_f]').select2({data: <?php echo json_encode($rec_inv_status_dropdown) ?>,minimumResultsForSearch: Infinity});
        $('input[name=supplier_f]').select2({data: <?php echo $suppliers_dropdown ?>});
        $('input[name=main_task_status_f]').select2({data: <?php echo json_encode($statuses2) ?>,minimumResultsForSearch: Infinity});
$('input[name=driver_nm_f]').select2({data: <?php echo $drivers_dropdown ?>});
$('input[name=city_name_f]').select2({data: <?php echo $cities_dropdown ?>,minimumResultsForSearch: Infinity});
$('input[name=created_by_f]').select2({data: <?php echo $team_members_dropdown ?>});
$('input[name=car_type_f]').select2({data: <?php echo  $cars_type_dropdown ?>,minimumResultsForSearch: Infinity});
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

//$('#myCard').attr("style", "min-height: " + document.getElementById("page-content").clientHeight + "px;");

   




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