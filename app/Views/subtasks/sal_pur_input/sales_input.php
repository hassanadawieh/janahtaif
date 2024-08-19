
<?php
$dateTime = new DateTime('now', new DateTimeZone("Asia/Riyadh"));
$dateTime1 = new DateTime($model_info->exp_out_time ? $model_info->exp_out_time:'now', new DateTimeZone("Asia/Riyadh"));
// $dateTime2 = new DateTime($model_info->start_time ? $model_info->start_time:'now', new DateTimeZone("Asia/Riyadh"));
// $dateTime3 = new DateTime($model_info->end_time ? $model_info->end_time:'now', new DateTimeZone("Asia/Riyadh"));

?>

<h4 class="modal-title" id="ajaxModalTitle" data-title="المبيعات" style="font-size: 14px; font-weight: bold;text-align: center;margin-bottom: 10px;color: #252a9f;"><?php echo app_lang("reserv_mang"); ?></h4>

          <div class="form-group">
                    <div class="row">
                         
                        <div class=" col-md-4  mb5 mt5 floating-label" >
                            <?php
                        $service_type["with_driver"] =  "سيارة بسائق";
                        $service_type["no_driver"] =  "سيارة بدون سائق";
                        $service_type["deliver"] = "توصيلة";
                        $service_type["no_car"] = "سائق بدون سيارة";

                        echo form_dropdown("service_type", $service_type, $model_info->service_type ?array($model_info->service_type):1, "class='select2' id='service_type' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                        <label for="service_type"><?php echo app_lang('service_type'); ?></label>
                        </div>
                        

                        <!-- balbis -->
                        <div class=" col-md-4 mb5 mt5 floating-label" >
                            <?php
                            //$dis = $model_info->service_type == "deliver" ? false : true;
                           $dis3 = $model_info->service_type == "no_car"?"disabled":"enabled";
                        echo form_dropdown("car_type_id", $cars_type_dropdown, array($model_info->car_type_id), "class='select2 validate-hidden ' id='car_type_id'  $dis3  value='".$model_info->car_type_id."' ",$model_info->car_type_id );
                        ?>
                        <label for="car_type" ><?php echo app_lang('car_type'); ?></label>
                        </div>




                                  
                        <div class=" col-md-4 mb5 mt5 floating-label" >
                        <?php
                            $dis2=$model_info->service_type=="no_driver" ? "disabled" : "enabled";
                            $options = $drivers_dropdown[0];
                            $statuese = $drivers_dropdown[1];
                            echo form_dropdown("driver_id", $options, array($model_info->driver_id), "class='select2 validate-hidden' id='driver_id' ".$dis2 , $statuese);
                            
                        
                        ?>
                        <label for="driver_nm"><?php echo app_lang('driver_nm'); ?></label>
                        </div>
                    </div>
                </div>

                        <div class="form-group">
                <div class="row">

                        <input type="hidden" name="myact_return_date" id="myact_return_date">
                <div class=" col-md-6 mb5 mt5 floating-label" >
                    <?php
                    
                    
                    echo form_input(array(
                        "id" => "start_date",
                        "name" => "start_date",
                        "value" => is_date_exists($model_info->start_date) ? $model_info->start_date : '',
                        "class" => "form-control",
                        "placeholder" => app_lang('start_date'),
                        "autocomplete" => "off",
                        "data-rule-notNull" => "#start_date",
                        //"data-msg-notNull" => "يجب كتبة تاريخ الخروج",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <label for="start_date" ><?php echo app_lang('start_date'); ?></label>
                </div>

                
                


                <div class=" col-md-6 mb5 mt5 floating-label" >
                    <?php
                    
                    echo form_input(array(
                        "id" => "end_date",
                        "name" => "end_date",
                        "value" => is_date_exists($model_info->end_date) ? $model_info->end_date : '',
                        "class" => "form-control",
                        "placeholder" => app_lang('end_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rule-notNull" => "#end_date",
                        //"data-rule-required" => $model_info->service_type=="deliver"? false : true,
                        // "data-msg-required" => app_lang("field_required"),
                        // "data-rule-greaterThanOrEqual" => "#out_date",
                        //    "data-msg-greaterThanOrEqual" => app_lang("return_must_be_equal_or_greater_than_out_date")
                    ));
                    ?>
                    <label for="end_date"><?php echo app_lang('end_date'); ?></label>
                    
                </div>
                <div class=" col-md-6 mb5 mt5 floating-label" >
                    <?php
                    
                    echo form_input(array(
                        "id" => "ten_out_date",
                        "name" => "ten_out_date",
                        "value" => is_date_exists($model_info->end_date) ? $model_info->end_date : '',
                        "class" => "form-control",
                        "placeholder" => app_lang('ten_out_date'),
                        "autocomplete" => "off",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rule-notNull" => "#ten_out_date",
                        //"data-rule-required" => $model_info->service_type=="deliver"? false : true,
                        // "data-msg-required" => app_lang("field_required"),
                        // "data-rule-greaterThanOrEqual" => "#out_date",
                        //    "data-msg-greaterThanOrEqual" => app_lang("return_must_be_equal_or_greater_than_out_date")
                    ));
                    ?>
                    <label for="ten_out_date"><?php echo app_lang('ten_out_date'); ?></label>
                    
                </div>
        
            
                <div class=" col-md-6 mb5 mt5 floating-label" >
                            <?php 
                            $dis=$model_info->service_type=="deliver" ? "disabled" : "enabled";
                        echo form_input(array(
                            "id" => "booking_period",
                            "name" => "booking_period",
                            "value" => $model_info->service_type!="deliver" ? $model_info->booking_period ? $model_info->booking_period : '' :'',
                            "type" => "number",
                            "class" => "form-control",
                            //"data-rule-notNull" => "#act_return_date",
                            //"data-msg-notNull" => "يجب كتبة عدد الايام",
                            
                            "placeholder" => app_lang('booking_period'),
                            $dis => true,

                            //"data-rule-required" => $model_info->service_type=="deliver"? false:true,
                            //"data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        <label for="booking_period"  ><?php echo app_lang('booking_period'); ?></label>
                        </div>
                        
                    
                    
                </div>
            </div>

           



            <!-- <div class="form-group">
            <div class="row">
               
               
                
                


                   

                        
            </div>
        </div> -->


        <div class="form-group">
            <div class="row">
                
                         
                        

                        
                    <div class=" col-md-12 mb-3 floating-label">
                        <?php
                        echo form_textarea(array(
                            "id" => "sub_task_note",
                            "name" => "sub_task_note",
                            "value" => $add_type == "multiple" ? "" : $model_info->sub_task_note,
                            "class" => "form-control",
                            "placeholder" => app_lang('sub_task_note'),
                            "data-rich-text-editor" => true
                        ));
                        ?>
                        <label for="sub_task_note"><?php echo app_lang('sub_task_note'); ?></label>
                    </div>

               

                </div>
                </div>
                
                <div class="form-group">
                <div class="row">

                        <input type="hidden" name="myact_return_date" id="myact_return_date">
                <div class=" col-md-4 mb5 mt5 floating-label" >
                    <?php
                    
                    
                    echo form_input(array(
                        "id" => "out_date",
                        "name" => "out_date",
                        "value" => is_date_exists($model_info->out_date) ? $model_info->out_date : '',
                        "class" => "form-control",
                        "placeholder" => app_lang('out_date'),
                        "autocomplete" => "off",
                        //"data-rule-notNull" => "#act_return_date",
                        //"data-msg-notNull" => "يجب كتبة تاريخ الخروج",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <label for="out_date" ><?php echo app_lang('out_date'); ?></label>
                </div>

                
                

                <div class=" col-md-4 mb5 mt5 floating-label" >
                    <?php
                    
                    
                    echo form_input(array(
                        "id" => "exp_out_time",
                        "name" => "exp_out_time",
                        "value" => $model_info->exp_out_time && $model_info->exp_out_time!='00:00:01'?$dateTime1->format('h:i A'):'',
                        "class" => "form-control",
                        "placeholder" => app_lang('exp_out_time'),
                        "autocomplete" => "off",
                        "data-rule-notNull" => "#exp_out_time",
                        "data-msg-notNull" => "يجب تحديد  وقت الخروج التوقع",
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required"),
                        
                    ));
                    ?>
                    <label for="exp_out_time" ><?php echo app_lang('exp_out_time'); ?></label>
                </div>

                <div class=" col-md-4 mb5 mt5 floating-label" >
                    <?php
                    
                    echo form_input(array(
                        "id" => "sales_act_return_date",
                        "name" => "sales_act_return_date",
                        "value" => is_date_exists($model_info->sales_act_return_date) ? $model_info->sales_act_return_date : '',
                        "class" => "form-control",
                        "placeholder" => app_lang('sales_act_return_date'),
                        "autocomplete" => "off",
                        "data-rule-notNull" => "#exp_out_time",
                        //"data-rule-required" => $model_info->service_type=="deliver"? false : true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rule-greaterThanOrEqual" => "#out_date",
                           "data-msg-greaterThanOrEqual" => app_lang("return_must_be_equal_or_greater_than_out_date")
                    ));
                    ?>
                    <label for="act_return_date"><?php echo app_lang('act_return_date'); ?></label>
                    
                </div>

                        
                    
                    
                </div>
            </div>

           



            <div class="form-group">
            <div class="row">
               
               
                <div class=" col-md-6 mb5 mt5 floating-label">
                    <?php
                    $dis=$model_info->service_type=="deliver" ? "disabled" : "enabled";
                    
                    echo form_input(array(
                        "id" => "tmp_return_date",
                        "name" => "tmp_return_date",
                        "value" => $model_info->service_type!="deliver"? (is_date_exists($model_info->tmp_return_date) ? $model_info->tmp_return_date : '') :'',
                        "class" => "form-control",
                        "placeholder" => app_lang('tmp_return_date'),
                        "autocomplete" => "off",
                        $dis => true,
                        "data-rule-required" => $model_info->service_type=="deliver"? false : true,
                        "data-msg-required" => app_lang("field_required"),
                        "data-rule-greaterThanOrEqual" => "#out_date",
                            "data-msg-greaterThanOrEqual" => app_lang("return_must_be_equal_or_greater_than_out_date")
                    ));
                    ?>
                    <label for="tmp_return_date" ><?php echo app_lang('tmp_return_date'); ?></label>
                </div>
                


                   

                        <div class=" col-md-6 mb5 mt5 floating-label" >
                            <?php 
                            $dis=$model_info->service_type=="deliver" ? "disabled" : "enabled";
                        echo form_input(array(
                            "id" => "inv_day_count",
                            "name" => "inv_day_count",
                            "value" => $model_info->service_type!="deliver" ? $model_info->inv_day_count ? $model_info->inv_day_count : '' :'',
                            "type" => "number",
                            "class" => "form-control",
                            "data-rule-notNull" => "#inv_day_count",
                            "data-msg-notNull" => "يجب كتبة عدد الايام",
                            
                            "placeholder" => app_lang('inv_day_count'),
                            $dis => true,

                            "data-rule-required" => $model_info->service_type=="deliver"? false:true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        <label for="inv_day_count"  ><?php echo app_lang('inv_day_count'); ?></label>
                        </div>
            </div>
        </div>
        <div class="form-group">
            <div class="row">
                
                         
                        

                        
                    <div class=" col-md-12 mb-3 floating-label">
                        <?php
                        echo form_textarea(array(
                            "id" => "note",
                            "name" => "note",
                            "value" => $add_type == "multiple" ? "" : $model_info->note,
                            "class" => "form-control",
                            "placeholder" => app_lang('note'),
                            "data-rich-text-editor" => true
                        ));
                        ?>
                        <label for="note"><?php echo app_lang('note'); ?></label>
                    </div>

               

                </div>
                </div>

<script>
    $(document).ready(function () {
        $('#service_type').change(function () {
            var selectedService = $(this).val();

            if (selectedService === 'no_car') {
                $('#car_type_id').prop('disabled', true);
            } else {
                $('#car_type_id').prop('disabled', false);
            }
        });

        // Trigger change event on page load to handle preselected value
        $('#service_type').trigger('change');
    });
</script>


               