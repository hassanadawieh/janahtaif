 <h4 class="modal-title" id="ajaxModalTitle" data-title="إدارة التوريد" style="font-size: 14px; font-weight: bold; text-align: center;margin-bottom: 10px;color: #252a9f;"><?php echo app_lang("supply_mang"); ?> </h4>
 <?php
$dateTime = new DateTime('9:08 PM', new DateTimeZone("Asia/Riyadh"));

$dateTime1 = new DateTime('now', new DateTimeZone("Asia/Riyadh"));
$newDate2 = $dateTime->format('d/m/Y');

if($model_info->id){
    $date_t = new DateTime($model_info->act_out_time, new DateTimeZone("Asia/Riyadh"));
    $act_o_t=$model_info->act_out_time && $model_info->act_out_time!='00:00:01' ? $date_t->format('h:i A'):'';

    $date_act_re_t = new DateTime($model_info->act_return_time, new DateTimeZone("Asia/Riyadh"));
    $date_act_re_time=$model_info->act_return_time && $model_info->act_return_time!='00:00:01' ? $date_act_re_t->format('h:i A'):'';
}else{
    $act_o_t='';//$dateTime1->format('h:i A');
    $date_act_re_time='';
    
}
?>

<input type="hidden" name="out_date_1" id="out_date_1" value="<?php echo $model_info->out_date; ?>" />

                  <div class="form-group">
                    <div class="row">

                        <?php
                          $disabled="enabled";
                          $requerdOrNot="dg";
                          $requerdOrNot2="dg";
                           if($after_review!=1){
                            $disabled="disabled";
                            $requerdOrNot="required";
                            $requerdOrNot2=$model_info->service_type!="deliver"?"required":"dg";
                            ?>
                            <input type="hidden" name="supplier_id" value="<?php echo $model_info->supplier_id; ?>" />
                            <input type="hidden" name="car_number" value="<?php echo $model_info->car_number; ?>" />
                            <input type="hidden" name="act_out_date" value="<?php echo $model_info->act_out_date; ?>" />
                            <input type="hidden" name="act_out_time" value="<?php echo $act_o_t; ?>" />
                            <input type="hidden" name="act_return_date" value="<?php echo $model_info->act_return_date; ?>" />
                            <input type="hidden" name="act_return_time" value="<?php echo $date_act_re_time; ?>" />
                            
                            <input type="hidden" name="dres_number" value="<?php echo $model_info->dres_number; ?>" />
                            <input type="hidden" name="amount" value="<?php echo $model_info->amount; ?>" />
                            <input type="hidden" name="note_2" value="<?php echo $model_info->note_2; ?>" />

                            <?php 
                           }

                        ?>
                        <div class=" col-md-4 mb-3 floating-label">
                            <?php
                            echo form_dropdown("supplier_id", $suppliers_dropdown, array($model_info->supplier_id), "class='select2 validate-hidden' id='supplier_id' ".$disabled);
                            ?>
                            <label for="supplier_id"><?php echo app_lang('supplier'); ?></label>
                        </div>
                        
                        <div class=" col-md-4 mb-3 floating-label">
                            <?php
                        echo form_input(array(
                            "id" => "car_number",
                            "name" => "car_number",
                            "value" =>  $model_info->car_number?$model_info->car_number:'',
                            "class" => "form-control",
                            
                            "placeholder" => app_lang('car_number'),
                            $disabled => true,
                            //"data-rule-required" => true,
                            //"data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        <label for="car_number" ><?php echo app_lang('car_number'); ?></label>
                        </div>


                        <div class=" col-md-4 mb-3 form-floating" id="dropdown-apploader-section">

                            <?php
                        echo form_input(array(
                            "id" => "car_status",
                            "name" => "car_status",
                            "value" =>  $model_info->car_status?$model_info->car_status:'',
                            "class" => "form-control typeahead",
                            
                            //"placeholder" => app_lang('car_status'),
                            $requerdOrNot=>"1",
                            
                        ));
                        ?>
                        <label for="car_status"><?php echo app_lang('car_status'); ?></label>
                        </div>

                        
                    </div>
                </div>



                <div class="form-group">
            <div class="row">
                <div class=" col-md-4 mb-3 floating-label">
                    <?php
                    
                    
                    echo form_input(array(
                        "id" => "act_out_date",
                        "name" => "act_out_date",
                        "value" => is_date_exists($model_info->act_out_date) ? $model_info->act_out_date : "",
                        "class" => "form-control",
                        "placeholder" => app_lang('act_out_date'),
                        "autocomplete" => "off",
                        $disabled => true,
                        //"data-rule-notNull" => "#act_out_time",
                        //"data-msg-notNull" => "يجب تحديد تاريخ الخروج او قم بمسخ وقت الخروج"
                        //"data-rule-required" => true,
                        //"data-msg-required" => app_lang("field_required"),
                    ));
                    ?>
                    <label for="act_out_date" ><?php echo app_lang('act_out_date'); ?></label>
                </div>

                <div class=" col-md-4 mb-3 floating-label">
                    <?php
                echo form_input(array(
                    "id" => "act_out_time",
                    "name" => "act_out_time",
                    "value" => $act_o_t,
                    "class" => "form-control",
                    "placeholder" => app_lang('act_out_time'),
                    "autocomplete" => "off",
                    "data-rule-notNull" => "#act_out_date",
                     "data-msg-notNull" => "يجب تحديد عوقت الخروج الفعلي",
                     $disabled => true,
                    //"data-rule-required" => true,
                    //"data-msg-required" => app_lang("field_required"),
                ));
                ?>
                <label for="act_out_time" ><?php echo app_lang('act_out_time'); ?></label>
                </div>
                
                <div class=" col-md-4 mb-3 floating-label">
                    <?php
                    
                    echo form_input(array(
                        "id" => "act_return_date",
                        "name" => "act_return_date",
                        "value" => is_date_exists($model_info->act_return_date) ? $model_info->act_return_date : '',
                        "class" => "form-control",
                        "placeholder" => app_lang('act_return_date'),
                        "autocomplete" => "off",
                        //"data-rule-required" => $model_info->service_type=="deliver"? false : true,
                        // "data-msg-required" => app_lang("field_required"),
                        $disabled => true,
                        "data-rule-greaterThanOrEqual" => "#act_out_date",
                           "data-msg-greaterThanOrEqual" => app_lang("return_must_be_equal_or_greater_than_out_date")
                    ));
                    ?>
                    <label for="act_return_date"><?php echo app_lang('act_return_date'); ?></label>
                </div>

            </div>
        </div>
      


        <div class="form-group">
                    <div class="row">
                        <div class=" col-md-4 mb-3 floating-label">
                    <?php
                echo form_input(array(
                    "id" => "act_return_time",
                    "name" => "act_return_time",
                    "value" => $date_act_re_time,
                    "class" => "form-control",
                    "placeholder" => app_lang('act_return_time'),
                    "autocomplete" => "off",
                    $disabled => true,
                    //"data-rule-notNull" => "#act_return_date",
                    //"data-msg-notNull" => "يجب تحديد وقت العودة الفعلي"
                    //"data-rule-required" => true,
                    //"data-msg-required" => app_lang("field_required"),
                ));
                ?>
                <label for="act_return_time" ><?php echo app_lang('act_return_time'); ?></label>
                </div>
                
                        <div class=" col-md-4 mb-3 floating-label">
                            <?php
                        $rec_inv_status["wait_inv"] =  app_lang("wait_inv");
                        $rec_inv_status["rec_inv"] =  app_lang("rec_inv");
                        

                        echo form_dropdown("rec_inv_status", $rec_inv_status, $model_info->rec_inv_status ?array($model_info->rec_inv_status):1, "class='select2' id='rec_inv_status' data-rule-required='true', data-msg-required='" . app_lang('field_required') . "'");
                        ?>
                        <label for="rec_inv_status"><?php echo app_lang('rec_inv_status'); ?></label>
                        </div>

                        
                        <div class=" col-md-4 mb-3 floating-label">
                            <?php
                            $dis=$model_info->service_type=="deliver" ? "disabled" : "enabled";
                        echo form_input(array(
                            "id" => "day_count",
                            "name" => "day_count",
                            "value" =>  $model_info->service_type!="deliver" ? $model_info->day_count?$model_info->day_count:'' :'',
                            "class" => "form-control",
                            "type" => "number",
                            "placeholder" => app_lang('day_count'),
                            //"autofocus" => true,
                            $dis => true,
                            $requerdOrNot2=>"1",
                            "data-msg-required" => app_lang("field_required"),
                            //"data-rule-notNull" => "#act_return_date",
                           //"data-msg-notNull" => "يجب تحديد عدد الايام"
                            //"data-rule-required" => $model_info->service_type=="deliver"? false:true,
                            //"data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                        <label for="day_count"><?php echo app_lang('day_count'); ?></label>
                        </div>                        

                        
                    </div>
                </div>




                <div class="form-group">
                <div class="row">
                    <div class=" col-md-6 mb5 mt2 floating-label">
                        <?php
                        echo form_input(array(
                            "id" => "dres_number",
                            "name" => "dres_number",
                            "value" => $model_info->dres_number,
                            "placeholder" => app_lang('dres_number'),
                            "class" => "form-control",
                            $disabled => true,
                            
                            
                        ));
                        ?>
                        <label for="dres_number" ><?php echo app_lang('dres_number'); ?></label>
                        </div>

                        <div class=" col-md-6 mb5 mt2 floating-label">
                        <?php
                        echo form_input(array(
                            "id" => "amount",
                            "name" => "amount",
                            "value" => $model_info->amount,
                            "placeholder" => app_lang('amount'),
                            "class" => "form-control",
                            $disabled => true,
                            
                            
                            
                        ));
                        ?>
                        <label for="amount" ><?php echo app_lang('amount'); ?></label>
                        </div>
                        </div>
                </div>

                        <div class="form-group">
                <div class="row">
                    <div class=" col-md-12 mb-3 floating-label">
                        <?php
                        echo form_textarea(array(
                            "id" => "note_2",
                            "name" => "note_2",
                            "value" => $add_type == "multiple" ? "" : $model_info->note_2,
                            "class" => "form-control",
                            "placeholder" => app_lang('note'),
                            "data-rich-text-editor" => true,
                            $disabled => true,
                        ));
                        ?>
                        <label for="note_2"><?php echo app_lang('note'); ?></label>
                    </div>
                </div>
            </div>

            <script>
                $(document).ready(function () {
                   function checkFields() {
                       var actOutDate = $('#act_out_date').val();
                       var actOutTime = $('#act_out_time').val();

                       if (actOutDate && actOutTime) {
                           $('#dres_number').attr('required', 'required');
                       } else {
                           $('#dres_number').removeAttr('required');
                       }
                   }

                   // Check fields on page load
                   checkFields();

                   // Check fields when either act_out_date or act_out_time changes
                   $('#act_out_date, #act_out_time').on('change', function () {
                       checkFields();
                   });
                });

             </script>