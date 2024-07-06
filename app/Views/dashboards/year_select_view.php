

<div id="invoice-overview-widget-container" style="margin: 0 0 0px 0;">
    <div class="card bg-whitd mb-2 px-3 py-1">
        <div class="card-dheader">
            <label >تحديد السنة</label>
                   <?php
                   $s_year=$session_selected_value? $session_selected_value:$selected_value;
                   echo js_anchor($s_year, array("style" => "background-color: #eee;color:#000;font-size:17px;", "class" => "badge", "data-id" => $s_year, "data-value" => $s_year, "data-act" => "selected-year-val"));
                        /*echo form_input(array(
                            "id" => "selected_year",
                            "name" => "selected_year",
                            "value" =>$selected_value,
                            "class" => "form-control",
                            //"data-rule-required" => true,
                            //"data-msg-required" => app_lang("field_required"),
                            "placeholder" => "السنة"
                        ));*/

                        /*

                        echo form_dropdown("selected_year", $years_dropdown, $model_info->service_type ?array($model_info->service_type):1, "class='select2' id='selected_year' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        */


                        ?>
        </div>

      
    </div>
</div>


<script type="text/javascript">
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

    $(document).ready(function () {

        $('body').on('click', '[data-act=selected-year-val]', function () {
            $(this).appModifier({
                value: $(this).attr('data-value'),
                actionUrl: '<?php echo_uri("subtasks/set_year") ?>',
                select2Option: {data: <?php echo $years_dropdown ?>},
                onSuccess: function (response) {
                    if (response.success) {
                        location.reload(true);
                       // $("#task-table").appTable({newData: response.data, dataId: response.id});
                    }
                }
            });

            return false;
        });
        initScrollbar('#invoice-overview-container', {
            setHeight: 280
        });
        //$('#selected_year').select2({data: <?=$years_dropdown?>});
    });
</script>