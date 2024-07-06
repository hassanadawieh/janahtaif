<?php echo form_open(get_uri("subtasks/save_status"), array("id" => "update_task-form", "class" => "general-form", "role" => "form")); ?>


<div id="tasks-dropzone" class="post-dropzone">
    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <input type="hidden" name="mang" value="<?php echo $mang; ?>" />
           


             <div class="form-group">
                <div class="row">
                    <label for="closed_reason" class=" col-md-2"><?php echo app_lang('reason'); ?> </label>
                    <div class=" col-md-10">
                        <?php
                        echo form_textarea(array(
                            "id" => "closed_reason",
                            "name" => "closed_reason",
                            "class" => "form-control",
                            "placeholder" => app_lang('write_reason'),
                            "data-rich-text-editor" => true,
                            "autofocus" => true,
                            "data-rule-required" => true,
                            "data-msg-required" => app_lang("field_required"),
                        ));
                        ?>
                    </div>
                </div>
            </div>
            

           
            

          
        </div>
    </div>

    <div class="modal-footer">
        <div id="link-of-new-view" class="hide">
            <?php
            echo modal_anchor(get_uri("subtasks/task_view"), "", array("data-modal-lg" => "1"));
            ?>
        </div>

    

        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang('close'); ?></button>

       
            <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang('send').' & '.app_lang('save'); ?></button>
        
    </div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {



        

       

        window.taskForm = $("#update_task-form").appForm({
            closeModalOnSuccess: false,
            onSuccess: function (result) {
                $("#task-table").appTable({newData: result.data, dataId: result.id});
                $("#reload-kanban-button:visible").trigger("click");

                $("#save_and_show_value").append(result.save_and_show_link);

               window.taskForm.closeModal();

                
            },
            onAjaxSuccess: function (result) {
                if (!result.success) {
                    //$("#next_recurring_date").val(result.next_recurring_date_value);
                    //$("#next_recurring_date_container").removeClass("hide");

                    
                }
            }
        });
       

       
        //$('#priority_id').select2({data: <?php //echo json_encode($priorities_dropdown); ?>});
    });
</script>    

