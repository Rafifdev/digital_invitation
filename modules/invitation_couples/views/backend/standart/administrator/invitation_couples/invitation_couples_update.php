
<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
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
        Invitation Couples        <small>Edit Invitation Couples</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/invitation_couples'); ?>">Invitation Couples</a></li>
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
                            <h3 class="widget-user-username">Invitation Couples</h3>
                            <h5 class="widget-user-desc">Edit Invitation Couples</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/invitation_couples/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_invitation_couples', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_invitation_couples', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="invitation_id" class="col-sm-2 control-label">Invitation Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="invitation_id" id="invitation_id" placeholder="Invitation Id" value="<?= set_value('invitation_id', $invitation_couples->invitation_id); ?>">
                                <small class="info help-block">
                                <b>Input Invitation Id</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="couple_order" class="col-sm-2 control-label">Couple Order 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="couple_order" id="couple_order" placeholder="Couple Order" value="<?= set_value('couple_order', $invitation_couples->couple_order); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="groom_nickname" class="col-sm-2 control-label">Groom Nickname 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="groom_nickname" id="groom_nickname" placeholder="Groom Nickname" value="<?= set_value('groom_nickname', $invitation_couples->groom_nickname); ?>">
                                <small class="info help-block">
                                <b>Input Groom Nickname</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="groom_full_name" class="col-sm-2 control-label">Groom Full Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="groom_full_name" id="groom_full_name" placeholder="Groom Full Name" value="<?= set_value('groom_full_name', $invitation_couples->groom_full_name); ?>">
                                <small class="info help-block">
                                <b>Input Groom Full Name</b> Max Length : 200.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="groom_child_order" class="col-sm-2 control-label">Groom Child Order 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="groom_child_order" id="groom_child_order" placeholder="Groom Child Order" value="<?= set_value('groom_child_order', $invitation_couples->groom_child_order); ?>">
                                <small class="info help-block">
                                <b>Input Groom Child Order</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="groom_father_name" class="col-sm-2 control-label">Groom Father Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="groom_father_name" id="groom_father_name" placeholder="Groom Father Name" value="<?= set_value('groom_father_name', $invitation_couples->groom_father_name); ?>">
                                <small class="info help-block">
                                <b>Input Groom Father Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="groom_mother_name" class="col-sm-2 control-label">Groom Mother Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="groom_mother_name" id="groom_mother_name" placeholder="Groom Mother Name" value="<?= set_value('groom_mother_name', $invitation_couples->groom_mother_name); ?>">
                                <small class="info help-block">
                                <b>Input Groom Mother Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="groom_instagram" class="col-sm-2 control-label">Groom Instagram 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="groom_instagram" id="groom_instagram" placeholder="Groom Instagram" value="<?= set_value('groom_instagram', $invitation_couples->groom_instagram); ?>">
                                <small class="info help-block">
                                <b>Input Groom Instagram</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="groom_photo" class="col-sm-2 control-label">Groom Photo 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="invitation_couples_groom_photo_galery"></div>
                                <input class="data_file data_file_uuid" name="invitation_couples_groom_photo_uuid" id="invitation_couples_groom_photo_uuid" type="hidden" value="<?= set_value('invitation_couples_groom_photo_uuid'); ?>">
                                <input class="data_file" name="invitation_couples_groom_photo_name" id="invitation_couples_groom_photo_name" type="hidden" value="<?= set_value('invitation_couples_groom_photo_name', $invitation_couples->groom_photo); ?>">
                                <small class="info help-block">
                                <b>Input Groom Photo</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="bride_nickname" class="col-sm-2 control-label">Bride Nickname 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bride_nickname" id="bride_nickname" placeholder="Bride Nickname" value="<?= set_value('bride_nickname', $invitation_couples->bride_nickname); ?>">
                                <small class="info help-block">
                                <b>Input Bride Nickname</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bride_full_name" class="col-sm-2 control-label">Bride Full Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bride_full_name" id="bride_full_name" placeholder="Bride Full Name" value="<?= set_value('bride_full_name', $invitation_couples->bride_full_name); ?>">
                                <small class="info help-block">
                                <b>Input Bride Full Name</b> Max Length : 200.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bride_child_order" class="col-sm-2 control-label">Bride Child Order 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bride_child_order" id="bride_child_order" placeholder="Bride Child Order" value="<?= set_value('bride_child_order', $invitation_couples->bride_child_order); ?>">
                                <small class="info help-block">
                                <b>Input Bride Child Order</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bride_father_name" class="col-sm-2 control-label">Bride Father Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bride_father_name" id="bride_father_name" placeholder="Bride Father Name" value="<?= set_value('bride_father_name', $invitation_couples->bride_father_name); ?>">
                                <small class="info help-block">
                                <b>Input Bride Father Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bride_mother_name" class="col-sm-2 control-label">Bride Mother Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bride_mother_name" id="bride_mother_name" placeholder="Bride Mother Name" value="<?= set_value('bride_mother_name', $invitation_couples->bride_mother_name); ?>">
                                <small class="info help-block">
                                <b>Input Bride Mother Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bride_instagram" class="col-sm-2 control-label">Bride Instagram 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="bride_instagram" id="bride_instagram" placeholder="Bride Instagram" value="<?= set_value('bride_instagram', $invitation_couples->bride_instagram); ?>">
                                <small class="info help-block">
                                <b>Input Bride Instagram</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="bride_photo" class="col-sm-2 control-label">Bride Photo 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="invitation_couples_bride_photo_galery"></div>
                                <input class="data_file data_file_uuid" name="invitation_couples_bride_photo_uuid" id="invitation_couples_bride_photo_uuid" type="hidden" value="<?= set_value('invitation_couples_bride_photo_uuid'); ?>">
                                <input class="data_file" name="invitation_couples_bride_photo_name" id="invitation_couples_bride_photo_name" type="hidden" value="<?= set_value('invitation_couples_bride_photo_name', $invitation_couples->bride_photo); ?>">
                                <small class="info help-block">
                                <b>Input Bride Photo</b> Max Length : 255.</small>
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
              window.location.href = BASE_URL + 'administrator/invitation_couples';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_invitation_couples = $('#form_invitation_couples');
        var data_post = form_invitation_couples.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_invitation_couples.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#invitation_couples_image_galery').find('li').attr('qq-file-id');
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
      
                     var params = {};
       params[csrf] = token;

       $('#invitation_couples_groom_photo_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/invitation_couples/upload_groom_photo_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/invitation_couples/delete_groom_photo_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/invitation_couples/get_groom_photo_file/<?= $invitation_couples->id; ?>',
             refreshOnRequest:true
           },
          multiple : false,
          validation: {
              allowedExtensions: ["*"],
              sizeLimit : 0,
                        },
          showMessage: function(msg) {
              toastr['error'](msg);
          },
          callbacks: {
              onComplete : function(id, name, xhr) {
                if (xhr.success) {
                   var uuid = $('#invitation_couples_groom_photo_galery').fineUploader('getUuid', id);
                   $('#invitation_couples_groom_photo_uuid').val(uuid);
                   $('#invitation_couples_groom_photo_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#invitation_couples_groom_photo_uuid').val();
                  $.get(BASE_URL + '/administrator/invitation_couples/delete_groom_photo_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#invitation_couples_groom_photo_uuid').val('');
                  $('#invitation_couples_groom_photo_name').val('');
                }
              }
          }
      }); /*end groom_photo galey*/
                            var params = {};
       params[csrf] = token;

       $('#invitation_couples_bride_photo_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/invitation_couples/upload_bride_photo_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/invitation_couples/delete_bride_photo_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/invitation_couples/get_bride_photo_file/<?= $invitation_couples->id; ?>',
             refreshOnRequest:true
           },
          multiple : false,
          validation: {
              allowedExtensions: ["*"],
              sizeLimit : 0,
                        },
          showMessage: function(msg) {
              toastr['error'](msg);
          },
          callbacks: {
              onComplete : function(id, name, xhr) {
                if (xhr.success) {
                   var uuid = $('#invitation_couples_bride_photo_galery').fineUploader('getUuid', id);
                   $('#invitation_couples_bride_photo_uuid').val(uuid);
                   $('#invitation_couples_bride_photo_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#invitation_couples_bride_photo_uuid').val();
                  $.get(BASE_URL + '/administrator/invitation_couples/delete_bride_photo_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#invitation_couples_bride_photo_uuid').val('');
                  $('#invitation_couples_bride_photo_name').val('');
                }
              }
          }
      }); /*end bride_photo galey*/
              
       
           
    
    }); /*end doc ready*/
</script>