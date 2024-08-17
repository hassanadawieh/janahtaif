<?php echo form_open(get_uri("clients/save"), array("id" => "client-form", "class" => "general-form", "role" => "form")); ?>
<div id="clients-dropzone" class="post-dropzone">

    <div class="modal-body clearfix">
        <div class="container-fluid">
            <input type="hidden" name="ticket_id" value="<?php echo $ticket_id; ?>" />
            <?php echo view("clients/client_form_fields"); ?>
        </div>
        <div class="form-group">
                <div class="col-md-12">
                    <?php
                    echo view("includes/file_list", array("files" => $model_info->files));
                    ?>
                </div>
            </div>

            <?php echo view("includes/dropzone_preview"); ?>
        </div>

    <div class="modal-footer">
    <button class="btn btn-default upload-file-button float-start me-auto btn-sm round" type="button" style="color:#7988a2"><i data-feather="camera" class="icon-16"></i> <?php echo app_lang("upload_file"); ?></button>

        <div id="link-of-add-contact-modal" class="hide">
            <?php echo modal_anchor(get_uri("clients/add_new_contact_modal_form"), "", array()); ?>
        </div>

        <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x"
                class="icon-16"></span> <?php echo app_lang('close'); ?></button>
        <?php if (!$model_info->id) { ?>
            <button type="button" id="save-and-continue-button" class="btn btn-info text-white"><span
                    data-feather="check-circle" class="icon-16"></span>
                <?php echo app_lang('save_and_continue'); ?></button>
        <?php } ?>
        <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span>
            <?php echo app_lang('save'); ?></button>
    </div>
</dev>
    <?php echo form_close(); ?>

    <script type="text/javascript">
        $(document).ready(function () {
            var ticket_id = "<?php echo $ticket_id; ?>";
            var uploadUrl = "<?php echo get_uri("clients/upload_file"); ?>";
            var validationUri = "<?php echo get_uri("clients/validate_file"); ?>";
            var dropzone = attachDropzoneWithForm("#clients-dropzone", uploadUrl, validationUri);

            window.clientForm = $("#client-form").appForm({
                closeModalOnSuccess: false,
                onSuccess: function (result) {
                    var $addMultipleContactsLink = $("#link-of-add-contact-modal").find("a");

                    if (result.view === "details" || ticket_id) {
                        if (window.showAddNewModal) {
                            $addMultipleContactsLink.attr("data-post-client_id", result.id);
                            $addMultipleContactsLink.attr("data-title", "<?php echo app_lang('add_multiple_contacts') ?>");
                            $addMultipleContactsLink.attr("data-post-add_type", "multiple");

                            $addMultipleContactsLink.trigger("click");
                        } else {
                            appAlert.success(result.message, { duration: 10000 });
                            setTimeout(function () {
                                location.reload();
                            }, 500);
                        }
                    } else if (window.showAddNewModal) {
                        $addMultipleContactsLink.attr("data-post-client_id", result.id);
                        $addMultipleContactsLink.attr("data-title", "<?php echo app_lang('add_multiple_contacts') ?>");
                        $addMultipleContactsLink.attr("data-post-add_type", "multiple");

                        $addMultipleContactsLink.trigger("click");

                        $("#client-table").appTable({ newData: result.data, dataId: result.id });
                    } else {
                        $("#client-table").appTable({ newData: result.data, dataId: result.id });
                        window.clientForm.closeModal();
                    }
                }
            });
            setTimeout(function () {
                $("#company_name").focus();
            }, 200);

            //save and open add new contact member modal
            window.showAddNewModal = false;

            $("#save-and-continue-button").click(function () {
                window.showAddNewModal = true;
                $(this).trigger("submit");
            });
        });
</script>