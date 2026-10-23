
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
        Assets        <small>Edit Assets</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/assets'); ?>">Assets</a></li>
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
                            <h3 class="widget-user-username">Assets</h3>
                            <h5 class="widget-user-desc">Edit Assets</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/assets/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_assets', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_assets', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="category_id" class="col-sm-2 control-label">Category Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select  class="form-control chosen chosen-select-deselect" name="category_id" id="category_id" data-placeholder="Select Category Id" >
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('asset_categories') as $row): ?>
                                    <option <?=  $row->id ==  $assets->category_id ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">
                                <b>Input Category Id</b> Max Length : 10.</small>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="uploaded_by" class="col-sm-2 control-label">Uploaded By 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="uploaded_by" id="uploaded_by" placeholder="Uploaded By" value="<?= set_value('uploaded_by', $assets->uploaded_by); ?>">
                                <small class="info help-block">
                                <b>Input Uploaded By</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="title" class="col-sm-2 control-label">Title 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="title" id="title" placeholder="Title" value="<?= set_value('title', $assets->title); ?>">
                                <small class="info help-block">
                                <b>Input Title</b> Max Length : 200.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="file_url" class="col-sm-2 control-label">File Url 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="assets_file_url_galery"></div>
                                <input class="data_file data_file_uuid" name="assets_file_url_uuid" id="assets_file_url_uuid" type="hidden" value="<?= set_value('assets_file_url_uuid'); ?>">
                                <input class="data_file" name="assets_file_url_name" id="assets_file_url_name" type="hidden" value="<?= set_value('assets_file_url_name', $assets->file_url); ?>">
                                <small class="info help-block">
                                <b>Input File Url</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="thumbnail_url" class="col-sm-2 control-label">Thumbnail Url 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="thumbnail_url" id="thumbnail_url" placeholder="Thumbnail Url" value="<?= set_value('thumbnail_url', $assets->thumbnail_url); ?>">
                                <small class="info help-block">
                                <b>Input Thumbnail Url</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="tags" class="col-sm-2 control-label">Tags 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="tags" id="tags" placeholder="Tags" value="<?= set_value('tags', $assets->tags); ?>">
                                <small class="info help-block">
                                <b>Input Tags</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="metadata" class="col-sm-2 control-label">Metadata 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="metadata" id="metadata" placeholder="Metadata" value="<?= set_value('metadata', $assets->metadata); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_public" class="col-sm-2 control-label">Is Public 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="is_public" id="is_public" placeholder="Is Public" value="<?= set_value('is_public', $assets->is_public); ?>">
                                <small class="info help-block">
                                <b>Input Is Public</b> Max Length : 1.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_active" class="col-sm-2 control-label">Is Active 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="is_active" id="is_active" placeholder="Is Active" value="<?= set_value('is_active', $assets->is_active); ?>">
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
              window.location.href = BASE_URL + 'administrator/assets';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_assets = $('#form_assets');
        var data_post = form_assets.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_assets.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#assets_image_galery').find('li').attr('qq-file-id');
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

       $('#assets_file_url_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/assets/upload_file_url_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/assets/delete_file_url_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/assets/get_file_url_file/<?= $assets->id; ?>',
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
                   var uuid = $('#assets_file_url_galery').fineUploader('getUuid', id);
                   $('#assets_file_url_uuid').val(uuid);
                   $('#assets_file_url_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#assets_file_url_uuid').val();
                  $.get(BASE_URL + '/administrator/assets/delete_file_url_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#assets_file_url_uuid').val('');
                  $('#assets_file_url_name').val('');
                }
              }
          }
      }); /*end file_url galey*/
              
       
           
    
    }); /*end doc ready*/
</script>