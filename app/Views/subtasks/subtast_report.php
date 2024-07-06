<style type="text/css">

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
/*
#subtask-report-table.dataTable th {
   
    white-space: nowrap;

}*/
@media (max-width: 767px){
#page-content {
    padding: 15px 10px !important;
}
}
</style>
<div id="page-content" class="page-wrapper clearfix">


    <div class="card">

        
        

        <div class="table-responsive">
            <table id="subtask-report-table" class="display" cellspacing="0" width="100%">            
            </table>
        </div>
    </div>
</div>





<script type="text/javascript">
   

var showOption = true,
                idColumnClass = "w15p",
                titleColumnClass = "w15p";

        if (isMobile()) {
            showOption = false;
            idColumnClass = "w25p";
            titleColumnClass = "w45p";
        }

        var showOptions = true;
      

        //open task details modal automatically 

      





      loadInvoicesTable = function (selector, dateRange) {

        $(selector).appTable({
            source: '<?php echo_uri("subtasks/list_data_report")?>',
            
            serverSide: true,
            order: [[1, "desc"]],
            responsive: true, //hide responsive (+) icon
            
            
           
            columns: [
                {visible: false, searchable: false},
                {title: '<?php echo app_lang("id") ?>', "class": "text-center w100", order_by: "sub_task_id"},
                {title: '<?php echo app_lang("dres_number") ?>', "class": "text-center ", order_by: "dres_number"},
                {targets: [4],title: '<?php echo app_lang("amount") ?>', order_by: "amount", "class": "text-center"},
                {title: '<?php echo app_lang("client") ?>', "class": "text-center"},
                {title: '<?php echo app_lang("project") ?>', "class": "text-center"},
                
                
                //{title: '<?php //echo app_lang("status") ?>',visible:false}<?php //echo $custom_field_headers; ?>,
                //{visible: false, searchable: false},
               
            ],
            printColumns: combineCustomFieldsColumns([1, 2,3, 4], '<?php echo $custom_field_headers; ?>'),
            xlsColumns: combineCustomFieldsColumns([1, 2,3, 4], '<?php echo $custom_field_headers; ?>'),
            summation: [{column: 3, dataType: 'number'}],
            rowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $('td:eq(0)', nRow).attr("style", "border-right:5px solid " + aData[0] + " !important;");

              
            },
           
            onRelaodCallback: function () {
               // hideBatchTasksBtn(true);
            },
            onInitComplete: function () {
                if (!showOption) {
                    window.scrollTo(0, 210); //scroll to the content for mobile devices
                }
                setPageScrollable();

            }
        });
};

    $(document).ready(function () {

        loadInvoicesTable("#subtask-report-table", "custom");
        

        






 if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

    });
</script>


<?php echo view("subtasks/quick_filters_helper_js"); ?>