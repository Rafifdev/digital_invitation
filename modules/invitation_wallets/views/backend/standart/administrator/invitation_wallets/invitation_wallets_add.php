
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
        Invitation Wallets        <small><?= cclang('new', ['Invitation Wallets']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/invitation_wallets'); ?>">Invitation Wallets</a></li>
        <li class="active"><?= cclang('new'); ?></li>
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
                            <h3 class="widget-user-username">Invitation Wallets</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Invitation Wallets']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_invitation_wallets', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_invitation_wallets', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="invitation_id" class="col-sm-2 control-label">Invitation Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="invitation_id" id="invitation_id" placeholder="Invitation Id" value="<?= set_value('invitation_id'); ?>">
                                <small class="info help-block">
                                <b>Input Invitation Id</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="wallet_type" class="col-sm-2 control-label">Wallet Type 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="wallet_type" id="wallet_type" placeholder="Wallet Type" value="<?= set_value('wallet_type'); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="provider_name" class="col-sm-2 control-label">Provider Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="provider_name" id="provider_name" placeholder="Provider Name" value="<?= set_value('provider_name'); ?>">
                                <small class="info help-block">
                                <b>Input Provider Name</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="account_number" class="col-sm-2 control-label">Account Number 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="account_number" id="account_number" placeholder="Account Number" value="<?= set_value('account_number'); ?>">
                                <small class="info help-block">
                                <b>Input Account Number</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="account_holder" class="col-sm-2 control-label">Account Holder 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="account_holder" id="account_holder" placeholder="Account Holder" value="<?= set_value('account_holder'); ?>">
                                <small class="info help-block">
                                <b>Input Account Holder</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="qris_image" class="col-sm-2 control-label">Qris Image 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="invitation_wallets_qris_image_galery"></div>
                                <input class="data_file" name="invitation_wallets_qris_image_uuid" id="invitation_wallets_qris_image_uuid" type="hidden" value="<?= set_value('invitation_wallets_qris_image_uuid'); ?>">
                                <input class="data_file" name="invitation_wallets_qris_image_name" id="invitation_wallets_qris_image_name" type="hidden" value="<?= set_value('invitation_wallets_qris_image_name'); ?>">
                                <small class="info help-block">
                                <b>Input Qris Image</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="notes" class="col-sm-2 control-label">Notes 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="notes" name="notes" rows="5" cols="80"><?= set_value('Notes'); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sort_order" class="col-sm-2 control-label">Sort Order 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="sort_order" id="sort_order" placeholder="Sort Order" value="<?= set_value('sort_order'); ?>">
                                <small class="info help-block">
                                <b>Input Sort Order</b> Max Length : 11.</small>
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
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
    $(document).ready(function(){
            CKEDITOR.replace('notes'); 
      var notes = CKEDITOR.instances.notes;
                   
      $('#btn_cancel').click(function(){
        swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
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
              window.location.href = BASE_URL + 'administrator/invitation_wallets';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#notes').val(notes.getData());
                    
        var form_invitation_wallets = $('#form_invitation_wallets');
        var data_post = form_invitation_wallets.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/invitation_wallets/add_save',
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id_qris_image = $('#invitation_wallets_qris_image_galery').find('li').attr('qq-file-id');
            
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            resetForm();
            if (typeof id_qris_image !== 'undefined') {
                    $('#invitation_wallets_qris_image_galery').fineUploader('deleteFile', id_qris_image);
                }
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
            notes.setData('');
                
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

       $('#invitation_wallets_qris_image_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/invitation_wallets/upload_qris_image_file',
              params : params
          },
          deleteFile: {
              enabled: true, 
              endpoint: BASE_URL + '/administrator/invitation_wallets/delete_qris_image_file',
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
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
                   var uuid = $('#invitation_wallets_qris_image_galery').fineUploader('getUuid', id);
                   $('#invitation_wallets_qris_image_uuid').val(uuid);
                   $('#invitation_wallets_qris_image_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#invitation_wallets_qris_image_uuid').val();
                  $.get(BASE_URL + '/administrator/invitation_wallets/delete_qris_image_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#invitation_wallets_qris_image_uuid').val('');
                  $('#invitation_wallets_qris_image_name').val('');
                }
              }
          }
      }); /*end qris_image galery*/
              
 
       
    
    
    }); /*end doc ready*/
</script>