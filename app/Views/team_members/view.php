<?php echo view("includes/cropbox"); ?>
<div id="page-content" class="clearfix">
    <div class="bg-success clearfix">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="row p20">
                        <?php echo view("users/profile_image_section"); ?>
                    </div>
                </div>

                <div class="col-md-6 text-center cover-widget">
                    <div class="row p20">
                        <?php
                        if ($show_projects_count) {
                           // echo count_project_status_widget($user_info->id);
                        }

                       // echo count_total_time_widget($user_info->id);
                        ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>


    <ul id="team-member-view-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs scrollable-tabs rounded-0" role="tablist">

        
        <?php if ($show_general_info) { ?>
            <li><a  role="presentation" data-bs-toggle="tab" href="<?php echo_uri("team_members/general_info/" . $user_info->id); ?>" data-bs-target="#tab-general-info"> <?php echo app_lang('general_info'); ?></a></li>
        <?php } ?>


        <?php if ($show_account_settings) { ?>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo_uri("team_members/account_settings/" . $user_info->id); ?>" data-bs-target="#tab-account-settings"> <?php echo app_lang('account_settings'); ?></a></li>
        <?php } ?>

      

        <?php if ($show_general_info) { ?>
            <!--<li><a  role="presentation" data-bs-toggle="tab" href="<?php //echo_uri("team_members/files/" . $user_info->id); ?>" data-bs-target="#tab-files"> <?php //echo app_lang('files'); ?></a></li>-->
        <?php } ?>

        

        <?php
        $hook_tabs = array();
        $hook_tabs = app_hooks()->apply_filters('app_filter_staff_profile_ajax_tab', $hook_tabs, $user_info->id);
        $hook_tabs = is_array($hook_tabs) ? $hook_tabs : array();
        foreach ($hook_tabs as $hook_tab) {
            ?>
            <li><a role="presentation" data-bs-toggle="tab" href="<?php echo get_array_value($hook_tab, 'url') ?>" data-bs-target="#<?php echo get_array_value($hook_tab, 'target') ?>"><?php echo get_array_value($hook_tab, 'title') ?></a></li>
            <?php
        }
        ?>

    </ul>

    <div class="tab-content">
        
        <div role="tabpanel" class="tab-pane fade" id="tab-general-info"></div>
        <!--<div role="tabpanel" class="tab-pane fade" id="tab-files"></div>-->
        <div role="tabpanel" class="tab-pane fade" id="tab-account-settings"></div>
        <?php
        foreach ($hook_tabs as $hook_tab) {
            ?>
            <div role="tabpanel" class="tab-pane fade" id="<?php echo get_array_value($hook_tab, 'target') ?>"></div>
            <?php
        }
        ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".upload").change(function () {
            if (typeof FileReader == 'function' && !$(this).hasClass("hidden-input-file")) {
                showCropBox(this);
            } else {
                $("#profile-image-form").submit();
            }
        });
        $("#profile_image").change(function () {
            $("#profile-image-form").submit();
        });


        $("#profile-image-form").appForm({
            isModal: false,
            beforeAjaxSubmit: function (data) {
                $.each(data, function (index, obj) {
                    if (obj.name === "profile_image") {
                        var profile_image = replaceAll(":", "~", data[index]["value"]);
                        data[index]["value"] = profile_image;
                    }
                });
            },
            onSuccess: function (result) {
                if (typeof FileReader == 'function' && !result.reload_page) {
                    appAlert.success(result.message, {duration: 10000});
                } else {
                    location.reload();
                }
            }
        });

        setTimeout(function () {
            var tab = "<?php echo $tab; ?>";
            if (tab === "general") {
                $("[data-bs-target='#tab-general-info']").trigger("click");
            } else if (tab === "account") {
                $("[data-bs-target='#tab-account-settings']").trigger("click");
            } /*else if (tab === "social") {
                $("[data-bs-target='#tab-social-links']").trigger("click");
            } else if (tab === "job_info") {
                $("[data-bs-target='#tab-job-info']").trigger("click");
            } else if (tab === "my_preferences") {
                $("[data-bs-target='#tab-my-preferences']").trigger("click");
            } else if (tab === "left_menu") {
                $("[data-bs-target='#tab-user-left-menu']").trigger("click");
            }*/
        }, 210);

    });
</script>