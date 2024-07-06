<style type="text/css">
[dir=rtl] .page-title h4 {
    float: right;
}

</style>
<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        

        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="page-title clearfix" style="float: right;">
                    <h4> <?php echo app_lang('driver_settings'); ?></h4>
                    <?php if($can_add_city){ ?>
                    <div class="title-button-group">
                        <?php echo modal_anchor(get_uri("drivers/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_driver'), array("class" => "btn btn-default", "title" => app_lang('add_driver'))); ?>
                    </div>
                <?php } ?>
                </div>
                <div class="table-responsive">
                    <table id="drivers-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#drivers-table").appTable({
            source: ' <?php echo_uri("drivers/list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("id"); ?>'},
                {title: '<?php echo app_lang("driver_name"); ?>'},
                {title: '<?php echo app_lang("email"); ?>'},
                {title: '<?php echo app_lang("phone"); ?>'},
                {title: '<?php echo app_lang("status"); ?>'},
               
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>