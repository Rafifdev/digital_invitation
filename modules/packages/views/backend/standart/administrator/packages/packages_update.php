
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
    function domo(){
     
       // Binding keys
       $('*').bind('keydown', 'Ctrl+s', function assets() {
          $('#btn_save').trigger('click');
           return false;
       });
    
       $('*').bind('keydown', 'Ctrl+x', function assets() {
          $('#btn_cancel').trigger('click');
           return false;
       });
    
      $('*').bind('keydown', 'Ctrl+d', function assets() {
          $('.btn_save_back').trigger('click');
           return false;
       });
        
    }
    
    jQuery(document).ready(domo);
</script>
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Packages        <small>Edit Packages</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/packages'); ?>">Packages</a></li>
        <li class="active">Edit</li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row" >
        <div class="col-md-12">
            <div class="box box-warning">
                <div class="box-body ">
                    <!-- Widget: user widget style 1 -->
                    <div class="box box-widget widget-user-2">
                        <!-- Add the bg color to the header using any of the bg-* classes -->
                        <div class="widget-user-header ">
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
                            </div>
                            <!-- /.widget-user-image -->
                            <h3 class="widget-user-username">Packages</h3>
                            <h5 class="widget-user-desc">Edit Packages</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/packages/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_packages', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_packages', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="name" class="col-sm-2 control-label">Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?= set_value('name', $packages->name); ?>">
                                <small class="info help-block">
                                <b>Input Name</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="price" class="col-sm-2 control-label">Price 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="price" id="price" placeholder="Price" value="<?= set_value('price', $packages->price); ?>">
                                <small class="info help-block">
                                <b>Input Price</b> Max Length : 12.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="admin_assist_fee" class="col-sm-2 control-label">Admin Assist Fee 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="admin_assist_fee" id="admin_assist_fee" placeholder="Admin Assist Fee" value="<?= set_value('admin_assist_fee', $packages->admin_assist_fee); ?>">
                                <small class="info help-block">
                                <b>Input Admin Assist Fee</b> Max Length : 12.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="includes_admin_assist" class="col-sm-2 control-label">Includes Admin Assist 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="includes_admin_assist" id="includes_admin_assist" placeholder="Includes Admin Assist" value="<?= set_value('includes_admin_assist', $packages->includes_admin_assist); ?>">
                                <small class="info help-block">
                                <b>Input Includes Admin Assist</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="max_photos" class="col-sm-2 control-label">Max Photos 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="max_photos" id="max_photos" placeholder="Max Photos" value="<?= set_value('max_photos', $packages->max_photos); ?>">
                                <small class="info help-block">
                                <b>Input Max Photos</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="max_videos" class="col-sm-2 control-label">Max Videos 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="max_videos" id="max_videos" placeholder="Max Videos" value="<?= set_value('max_videos', $packages->max_videos); ?>">
                                <small class="info help-block">
                                <b>Input Max Videos</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="max_guests" class="col-sm-2 control-label">Max Guests 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="max_guests" id="max_guests" placeholder="Max Guests" value="<?= set_value('max_guests', $packages->max_guests); ?>">
                                <small class="info help-block">
                                <b>Input Max Guests</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="allow_custom_music" class="col-sm-2 control-label">Allow Custom Music 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="allow_custom_music" id="allow_custom_music" placeholder="Allow Custom Music" value="<?= set_value('allow_custom_music', $packages->allow_custom_music); ?>">
                                <small class="info help-block">
                                <b>Input Allow Custom Music</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="allow_rsvp" class="col-sm-2 control-label">Allow Rsvp 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="allow_rsvp" id="allow_rsvp" placeholder="Allow Rsvp" value="<?= set_value('allow_rsvp', $packages->allow_rsvp); ?>">
                                <small class="info help-block">
                                <b>Input Allow Rsvp</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="allow_digital_gift" class="col-sm-2 control-label">Allow Digital Gift 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="allow_digital_gift" id="allow_digital_gift" placeholder="Allow Digital Gift" value="<?= set_value('allow_digital_gift', $packages->allow_digital_gift); ?>">
                                <small class="info help-block">
                                <b>Input Allow Digital Gift</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="has_watermark" class="col-sm-2 control-label">Has Watermark 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="has_watermark" id="has_watermark" placeholder="Has Watermark" value="<?= set_value('has_watermark', $packages->has_watermark); ?>">
                                <small class="info help-block">
                                <b>Input Has Watermark</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="active_days" class="col-sm-2 control-label">Active Days 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="active_days" id="active_days" placeholder="Active Days" value="<?= set_value('active_days', $packages->active_days); ?>">
                                <small class="info help-block">
                                <b>Input Active Days</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_active" class="col-sm-2 control-label">Is Active 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="is_active" id="is_active" placeholder="Is Active" value="<?= set_value('is_active', $packages->is_active); ?>">
                                <small class="info help-block">
                                <b>Input Is Active</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                
                        <hr style="margin: 30px 0 25px 0; border-top: 1px solid #e9ecef;">
                        <div class="message"></div>
                        <div class="row-fluid col-md-7">
                            <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="<?= cclang('save_button'); ?> (Ctrl+s)">
                            <i class="fa fa-save" ></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
                            <i class="ion ion-ios-list-outline" ></i> <?= cclang('save_and_go_the_list_button'); ?>
                            </a>
                            <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                            <i class="fa fa-undo" ></i> <?= cclang('cancel_button'); ?>
                            </a>
                            <span class="loading loading-hide">
                            <img src="<?= BASE_ASSET; ?>/img/loading-spin-primary.svg"> 
                            <i><?= cclang('loading_saving_data'); ?></i>
                            </span>
                        </div>
                        <?= form_close(); ?>
                    </div>
                </div>
                <!--/box body -->
            </div>
            <!--/box -->
        </div>
    </div>
</section>
<!-- /.content -->
<!-- Page script -->
<script>
    $(document).ready(function(){
      
             
      $('#btn_cancel').click(function(){
        swal({
            title: "Are you sure?",
            text: "the data that you have created will be in the exhaust!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes!",
            cancelButtonText: "No!",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
              window.location.href = BASE_URL + 'administrator/packages';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_packages = $('#form_packages');
        var data_post = form_packages.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_packages.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#packages_image_galery').find('li').attr('qq-file-id');
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            $('.data_file_uuid').val('');
    
          } else {
            $('.message').printMessage({message : res.message, type : 'warning'});
          }
    
        })
        .fail(function() {
          $('.message').printMessage({message : 'Error save data', type : 'warning'});
        })
        .always(function() {
          $('.loading').hide();
          $('html, body').animate({ scrollTop: $(document).height() }, 2000);
        });
    
        return false;
      }); /*end btn save*/
      
       
       
           
    
    }); /*end doc ready*/
</script>