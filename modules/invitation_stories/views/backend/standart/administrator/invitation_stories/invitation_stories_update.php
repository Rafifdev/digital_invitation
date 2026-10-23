
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
        Invitation Stories        <small>Edit Invitation Stories</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/invitation_stories'); ?>">Invitation Stories</a></li>
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
                            <h3 class="widget-user-username">Invitation Stories</h3>
                            <h5 class="widget-user-desc">Edit Invitation Stories</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/invitation_stories/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_invitation_stories', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_invitation_stories', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="invitation_id" class="col-sm-2 control-label">Invitation Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="invitation_id" id="invitation_id" placeholder="Invitation Id" value="<?= set_value('invitation_id', $invitation_stories->invitation_id); ?>">
                                <small class="info help-block">
                                <b>Input Invitation Id</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="year_date" class="col-sm-2 control-label">Year Date 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="year_date" id="year_date" placeholder="Year Date" value="<?= set_value('year_date', $invitation_stories->year_date); ?>">
                                <small class="info help-block">
                                <b>Input Year Date</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="title" class="col-sm-2 control-label">Title 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="<?= set_value('title', $invitation_stories->title); ?>">
                                <small class="info help-block">
                                <b>Input Title</b> Max Length : 200.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="story" class="col-sm-2 control-label">Story 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="story" name="story" rows="10" cols="80"> <?= set_value('story', $invitation_stories->story); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="image_url" class="col-sm-2 control-label">Image Url 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="invitation_stories_image_url_galery"></div>
                                <input class="data_file data_file_uuid" name="invitation_stories_image_url_uuid" id="invitation_stories_image_url_uuid" type="hidden" value="<?= set_value('invitation_stories_image_url_uuid'); ?>">
                                <input class="data_file" name="invitation_stories_image_url_name" id="invitation_stories_image_url_name" type="hidden" value="<?= set_value('invitation_stories_image_url_name', $invitation_stories->image_url); ?>">
                                <small class="info help-block">
                                <b>Input Image Url</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="sort_order" class="col-sm-2 control-label">Sort Order 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="sort_order" id="sort_order" placeholder="Sort Order" value="<?= set_value('sort_order', $invitation_stories->sort_order); ?>">
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
      
      CKEDITOR.replace('story'); 
      var story = CKEDITOR.instances.story;
                   
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
              window.location.href = BASE_URL + 'administrator/invitation_stories';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#story').val(story.getData());
                    
        var form_invitation_stories = $('#form_invitation_stories');
        var data_post = form_invitation_stories.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_invitation_stories.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#invitation_stories_image_galery').find('li').attr('qq-file-id');
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

       $('#invitation_stories_image_url_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/invitation_stories/upload_image_url_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/invitation_stories/delete_image_url_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/invitation_stories/get_image_url_file/<?= $invitation_stories->id; ?>',
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
                   var uuid = $('#invitation_stories_image_url_galery').fineUploader('getUuid', id);
                   $('#invitation_stories_image_url_uuid').val(uuid);
                   $('#invitation_stories_image_url_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#invitation_stories_image_url_uuid').val();
                  $.get(BASE_URL + '/administrator/invitation_stories/delete_image_url_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#invitation_stories_image_url_uuid').val('');
                  $('#invitation_stories_image_url_name').val('');
                }
              }
          }
      }); /*end image_url galey*/
              
       
           
    
    }); /*end doc ready*/
</script>