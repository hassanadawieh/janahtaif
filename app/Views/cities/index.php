<div id="page-content" class="page-wrapper clearfix">
    <div class="row">
        

        <div class="col-sm-12 col-lg-12">
            <div class="card">
                <div class="page-title clearfix">
                    <h4> <?php echo app_lang('city_settings'); ?></h4>
                    <?php if($can_add_city){ ?>
                    <div class="title-button-group">
                        <?php echo modal_anchor(get_uri("cities/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_city'), array("class" => "btn btn-default", "title" => app_lang('add_city'))); ?>
                    </div>
                <?php } ?>
                </div>
                <div class="table-responsive">
                    <table id="cities-table" class="display" cellspacing="0" width="100%">            
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#cities-table").appTable({
            source: ' <?php echo_uri("cities/list_data") ?>',
            columns: [
                {title: ''},
                {title: '<?php echo app_lang("city_name"); ?>'},
               
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>