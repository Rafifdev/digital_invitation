
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
        Invitation Rsvps        <small>Edit Invitation Rsvps</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/invitation_rsvps'); ?>">Invitation Rsvps</a></li>
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
                            <h3 class="widget-user-username">Invitation Rsvps</h3>
                            <h5 class="widget-user-desc">Edit Invitation Rsvps</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/invitation_rsvps/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_invitation_rsvps', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_invitation_rsvps', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="invitation_id" class="col-sm-2 control-label">Invitation Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="invitation_id" id="invitation_id" placeholder="Invitation Id" value="<?= set_value('invitation_id', $invitation_rsvps->invitation_id); ?>">
                                <small class="info help-block">
                                <b>Input Invitation Id</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="guest_id" class="col-sm-2 control-label">Guest Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="guest_id" id="guest_id" placeholder="Guest Id" value="<?= set_value('guest_id', $invitation_rsvps->guest_id); ?>">
                                <small class="info help-block">
                                <b>Input Guest Id</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sender_name" class="col-sm-2 control-label">Sender Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="sender_name" id="sender_name" placeholder="Sender Name" value="<?= set_value('sender_name', $invitation_rsvps->sender_name); ?>">
                                <small class="info help-block">
                                <b>Input Sender Name</b> Max Length : 200.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="attendance_status" class="col-sm-2 control-label">Attendance Status 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="attendance_status" id="attendance_status" placeholder="Attendance Status" value="<?= set_value('attendance_status', $invitation_rsvps->attendance_status); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="pax" class="col-sm-2 control-label">Pax 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="pax" id="pax" placeholder="Pax" value="<?= set_value('pax', $invitation_rsvps->pax); ?>">
                                <small class="info help-block">
                                <b>Input Pax</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="message" class="col-sm-2 control-label">Message 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="message" name="message" rows="10" cols="80"> <?= set_value('message', $invitation_rsvps->message); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="gift_amount" class="col-sm-2 control-label">Gift Amount 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="gift_amount" id="gift_amount" placeholder="Gift Amount" value="<?= set_value('gift_amount', $invitation_rsvps->gift_amount); ?>">
                                <small class="info help-block">
                                <b>Input Gift Amount</b> Max Length : 12.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="reply_message" class="col-sm-2 control-label">Reply Message 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="reply_message" name="reply_message" rows="10" cols="80"> <?= set_value('reply_message', $invitation_rsvps->reply_message); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_approved" class="col-sm-2 control-label">Is Approved 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="is_approved" id="is_approved" placeholder="Is Approved" value="<?= set_value('is_approved', $invitation_rsvps->is_approved); ?>">
                                <small class="info help-block">
                                <b>Input Is Approved</b> Max Length : 1.</small>
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
      
      CKEDITOR.replace('message'); 
      var message = CKEDITOR.instances.message;
            CKEDITOR.replace('reply_message'); 
      var reply_message = CKEDITOR.instances.reply_message;
                   
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
              window.location.href = BASE_URL + 'administrator/invitation_rsvps';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#message').val(message.getData());
                $('#reply_message').val(reply_message.getData());
                    
        var form_invitation_rsvps = $('#form_invitation_rsvps');
        var data_post = form_invitation_rsvps.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_invitation_rsvps.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#invitation_rsvps_image_galery').find('li').attr('qq-file-id');
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