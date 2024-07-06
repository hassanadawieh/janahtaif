<script>
    var check_user='<?php echo $mang; ?>';

    $(document).ready(function () {
        var showOption = true,
                idColumnClass = "w5p",
                titleColumnClass = "w15p",
                titleColumnClass2 = "w10p";

        /*if (isMobile()) {
            showOption = false;
            idColumnClass = "w20p";
            titleColumnClass = "w15p";
            titleColumnClass2 = "35";
        }*/
        $('#main-task-table tbody').on('click', 'td.details-control', function (event) {
        
        event.stopImmediatePropagation();

        var tr  = $(this).closest('tr'),
           row = $('#main-task-table').DataTable().row(tr);
           
           var er=row.data();
          

           //alert($(er[1]).attr('data-id'));
 
         
       if (row.child.isShown()) {
           destroyChild(row,$(er[1]).attr('data-id'));
           tr.removeClass('shown');
           //alert('shown # shown removed');
 
       }
       else {

            displayChildOfTable1(row,$(er[1]).attr('data-id'));
            tr.addClass('shown');
            //tr.css({"overflow-y":"scroll"});
             
       }
        
 
    });
      function displayChildOfTable1 ( row,$Find2 ) {

        string='<table id="mytask-table_'+$Find2+'" class="display mysub_task table table-striped table-bordered small" cellspacing="0" width="99%" style="margin: auto auto;">';
    
    string = string + '</table>';
    var table = $(string);
     
 
    // Display it in the child row
    row.child(table).show();
    var coll=[],filter_col=[],datePicker=[];
    var my_url='<?php echo_uri("subtasks/list_data/")?>'+$Find2;

    <?php 
    $statuses = array();
    foreach ($task_statuses as $status) {
        $is_selected = false;
        $statuses[] = array("text" => ($status->key_name ? app_lang($status->key_name) : $status->title), "value" => $status->id, "isChecked" => $is_selected);
    }

    $service_type_dropdown = array(array("id" => "", "text" => "- ". app_lang("service_type"). " -"));
        $service_type_dropdown[] = array("id" => "with_driver", "text" => "سيارة بسائق");
        $service_type_dropdown[] = array("id" => "no_driver", "text" => "بدون سائق");
        $service_type_dropdown[] = array("id" => "deliver", "text" => "توصيلة");


        $f_dropdown = array(array("id" => "", "text" => "- ".app_lang('filters')." -"));
        $f_dropdown[] = array("id" => "no_supplier", "text" => app_lang("tasks_without_supplier"), "isSelected" => $filter=="no_supplier"?true:false);

        $f_dropdown[] = array("id" => "wait_inv", "text" => app_lang("tasks_without_supplier_invoice"), "isSelected" => $filter=="wait_inv"?true:false);

        $f_dropdown[] = array("id" => "rec_inv", "text" => app_lang("rec_inv"), "isSelected" => $filter=="rec_inv"?true:false);

        $f_dropdown[] = array("id" => "no_act_return_date", "text" => app_lang("tasks_without_return_date"), "isSelected" => $filter=="no_act_return_date"?true:false);

        $f_dropdown[] = array("id" => "no_act_out_time", "text" => app_lang("tasks_without_out_time"), "isSelected" => $filter=="no_act_out_time"?true:false);

        $f_dropdown[] = array("id" => "24houer", "text" => app_lang("subtasks_to_go"), "isSelected" => $filter=="24houer"?true:false);


        
?>
    if(check_user=="yes"){
        my_url='<?php echo_uri("subtasks/list_data/")?>'+$Find2;
        coll=[
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": idColumnClass, order_by: "sub_task_id",render: function(data, type, full, meta) {
                    return "<div style='min-width:55px; '>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("guest_nm") ?>', "class": titleColumnClass, order_by: "guest_nm",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:150px;'>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("guest_phone") ?>', visible: showOption, order_by: "guest_phone"},
                {targets:4,visible: false,title: '<?php echo app_lang("client") ?>'},
                {targets:5,visible: false,title: '<?php echo app_lang("client") ?>'},
                {targets:6,visible: false,title: '<?php echo app_lang("contacts") ?>'},
                {targets:7,visible: false,title: '<?php echo app_lang("christening_number") ?>'},
                {targets:8,visible: false,title: '<?php echo app_lang("invoice_number") ?>'},

                {title: '<?php echo app_lang("city") ?>', order_by: "city_id"},
                {title: '<?php echo app_lang("service_type") ?>', visible: showOption, order_by: "service_type",render: function(data, type, full, meta) {
                    return "<div style='min-width:69px; '>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("driver_nm") ?>', "class": "text-center "+ titleColumnClass2, order_by: "driver_id",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:97px;'>"+data+"</div>";
                } },                
                {title: '<?php echo app_lang("car_type") ?>',"class": "text-center", visible: showOption, order_by: "car_type_id",render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:85px;'>"+data+"</div>";
                } },                
                {title: '<?php echo app_lang("out_exp_date&time") ?>', "class": "text-center ", order_by: "out_date",render: function(data, type, full, meta) {
                    return "<div dir='ltr'>"+data+"</div>";
                } },
                //{title: '<?php //echo app_lang("exp_out_time") ?>', visible: showOption, order_by: "exp_out_time"},
                {title: '<?php echo app_lang("tmp_return_date_2") ?>', visible: showOption, order_by: "tmp_return_date"},
                {title: '<?php echo app_lang("act_return_date_2") ?>', visible: showOption, order_by: "sales_act_return_date"},
                {title: '<?php echo app_lang("inv_day_count") ?>', order_by: "inv_day_count", visible: showOption},
                {title: '<?php echo app_lang("note") ?>', order_by: "note", visible: showOption,render: function(data, type, full, meta) {
                    return "<div style='white-space: nowrap;max-width: 130px;overflow: hidden;text-overflow: ellipsis;' data-bs-toggle='tooltip' data-bs-offset='0,4' data-bs-placement='top' data-bs-html='true' title='"+data+"'>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("created_by") ?>', visible: showOption, order_by: "created_by"},
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("status") ?>', visible: showOption, order_by: "status"}
                <?php echo $custom_field_headers2; ?>,
                //{visible: false, searchable: false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option ", visible: showOption,render: function(data, type, full, meta) {
                    return "<div style='min-width:80px;'>"+data+"</div>";
                } },
            ];
            filter_col=[
                
                {name: "driver_id", class: "w150",options: <?php echo $drivers_dropdown; ?>},
                {name: "car_type_id", class: "w150",options: <?php echo $cars_type_dropdown; ?>},
                {name: "city_id", class: "w150",options: <?php echo $cities_dropdown; ?>},
                {name: "service_type", class: "w150", options: <?php echo json_encode($service_type_dropdown); ?>},
                {name: "specific_user_id", class: "w150", options: <?php echo $team_members_dropdown; ?>},
                {name: "priority_id", class: "w150", options: <?php echo $priorities_dropdown; ?>},
                
                ];
            datePicker=[{name: "out_date", defaultText: "<?php echo app_lang('out_date') ?>",
                    },{name: "tmp_return_date", defaultText: "<?php echo app_lang('tmp_return_date') ?>",
                    },{name: "sales_act_return_date", defaultText: "<?php echo app_lang('sales_act_return_date') ?>",
                    }];
    }else{
        my_url='<?php echo_uri("subtasks/list_data_supply/")?>'+$Find2;
            coll=[
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": idColumnClass, order_by: "sub_task_id"},
                
                {title: '<?php //echo app_lang("project") ?>',visible: false},
                //{visible: false, searchable: false, order_by: "deadline"},
                {title: '<?php echo app_lang("supplier") ?>', "class": titleColumnClass, "iDataSort": 3, order_by: "supplier_id"},
                {title: '<?php echo app_lang("city") ?>', order_by: "city_id"},
                {title: '<?php echo app_lang("car_status") ?>', visible: showOption},
                {title: '<?php echo app_lang("car_number") ?>', visible: showOption},
                {title: '<?php echo app_lang("rec_inv_status2") ?>', "class": titleColumnClass2, order_by: "rec_inv_status"},
                {title: '<?php echo app_lang("act_out_date&time") ?>', order_by: "act_out_date"},
                {title: '<?php echo app_lang("act_return_date&time") ?>', visible: showOption, order_by: "act_return_date"},
                {title: '<?php echo app_lang("day_count") ?>', visible: showOption, order_by: "day_count"},
                {title: '<?php echo app_lang("dres_number") ?>', visible: showOption, order_by: "dres_number"},
                {title: '<?php echo app_lang("amount") ?>', visible: showOption, order_by: "amount"},
                {title: '<?php echo app_lang("note") ?>', order_by: "note", visible: showOption,render: function(data, type, full, meta) {
                    return "<div style='white-space:normal;min-width:150px;'>"+data+"</div>";
                } },
                {title: '<?php echo app_lang("created_by") ?>', visible: showOption, order_by: "created_by"},

                {title: '<?php echo app_lang("status") ?>', visible: showOption}
                <?php echo $custom_field_headers; ?>,
                //{visible: false, searchable: false},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w70", visible: showOption},
            ];
            filter_col=[
                {name: "specific_user_id", class: "w150", options: <?php echo $team_members_dropdown; ?>},
                {name: "filter", class: "w150",options: <?php echo json_encode($f_dropdown) ; ?>},
                {name: "supplier_id", class: "w150", options: <?php echo $suppliers_dropdown; ?>},
                
                ];
            datePicker=[{name: "act_out_date", defaultText: "<?php echo app_lang('act_out_date') ?>"},
                     {name: "act_return_date", defaultText: "<?php echo app_lang('act_return_date') ?>"}];
    }
    
    //$(row).addClass('gradeN');
        table.appTable({
            source: my_url,
            
            serverSide: true,
            order: [[1, "desc"]],
            responsive: false, //hide responsive (+) icon
            filterParams: {deleted_client: '',task_id: $Find2},
            paginate: false,
            showInfo: false,

           
            
            filterDropdown: filter_col,
            multiSelect: [
                {
                    name: "status_id",
                    text: "<?php echo app_lang('status'); ?>",
                    options: <?php echo json_encode($statuses); ?>
                }
            ],
            singleDatepicker: datePicker,

            columns: coll,
            printColumns: combineCustomFieldsColumns([1, 2,3, 4,5, 6, 7,8,9], '<?php echo $custom_field_headers2; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2,3, 4,5, 6, 7,8,9], '<?php echo $custom_field_headers2; ?>'),
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
                /*if(aData[3]=="-"){

                $(nRow).attr("style", "background-color:#d15c5c !important;");
                $('td',nRow).attr("style", "background-color:transparent !important; color:#fff !important; border: 1px solid #d42828;");
                $('td a',nRow).attr("style", "color:#fff !important; ");
                $('td a.edit',nRow).attr("style", "color:#000 !important; ");
                $('td a.delete',nRow).attr("style", "color:#000 !important; ");
                $('td:eq(0)', nRow).attr("style", "border-left:5px solid " + aData[0] + " !important;");
            }*/


            },
           
            onRelaodCallback: function () {
                hideBatchTasksBtn(false);
            },
            onInitComplete: function () {

                setPageScrollable();
            }
        });

         $("#mytask-table_"+$Find2+"_wrapper").addClass("mytable-responsive");
         $("#mytask-table_"+$Find2+"_wrapper").addClass("my_padding");
        //$("#mytask-table_"+$Find2+"_wrapper").css({"background": "#fdfdfd","box-shadow": "rgb(46 64 83) 0px 0px 1px 0px", "border-radius": "10px","overflow-y":"scroll","width": "95%"});
        var my_width=$("#myCards").width()-32;
        $("#mytask-table_"+$Find2+"_wrapper").attr('style', 'background: rgb(253, 253, 253);box-shadow: rgb(46, 64, 83) 0px 0px 1px 0px;border-radius: 10px;overflow: auto !important;max-width: '+my_width+'px; max-height: 500px;');
        $("#mytask-table_"+$Find2+"_wrapper > div:nth-child(1) > div.w-filter-right.float-end.custom-toolbar.pr15 > div.datatable-export.DTTT_container > div > .buttons-excel").remove();
            $("#mytask-table_"+$Find2+"_wrapper > div:nth-child(1) > div.w-filter-right.float-end.custom-toolbar.pr15 > div.datatable-export.DTTT_container > div > .buttons-print").remove();
        if(check_user=="yes"){
            
        var add_btn='<a href="#" id="open_add_dialog" class="btn btn-outline-light" style="margin: auto 3px;border-radius: 8px;" title="<?=app_lang('add_task')?>" data-post-task_id="'+$Find2+'" data-post-mang="reservmang" data-act="ajax-modal" data-title="<?=app_lang('add_task')?>" data-action-url="<?php echo get_uri("subtasks/task_modal_form")?>"><i data-feather="plus-circle" class="icon-16"></i><?=app_lang('add_task')?></a>';

        $("#mytask-table_"+$Find2+"_wrapper > div:nth-child(1) > div.w-filter-right.float-end.custom-toolbar.pr15 > div.datatable-export.DTTT_container > div").append(add_btn);
        $("#mytask-table_"+$Find2+"_wrapper > div:nth-child(1) > div.w-filter-right.float-end.custom-toolbar.pr15 > div.datatable-export.DTTT_container").attr('style', 'display: block !important; background:#fff !important;');
       }
       $("#mytask-table_"+$Find2+"_filter").css({"margin": "0 0px 2px 5px"});

       $("#mytask-table_"+$Find2+"_wrapper > div:nth-child(1) > div > div.DTTT_container").attr('style', 'margin-bottom: 5px !important;');

       $("#mytask-table_"+$Find2+"_wrapper > div:nth-child(1)").attr('style', 'margin-top: 0px !important;padding-top: 3px;');
       //$("#mytask-table_"+$Find2+"_wrapper > div:nth-child(1)").prepend('<div style="text-align: center;font-size: 14px;font-weight: bold;"><span style="background: #2e4053;border-radius: 20px;padding: 3px 10px;color: #fff;">قائمة المهام الفرعية</span></div>');
       

     }


     function destroyChild(row,id) {
    var table = $("mytask-table_"+id, row.child());
    table.detach();
    table.DataTable().destroy();
  
    // And then hide the row
    row.child.hide();
}


         
    });
</script>