
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
        Backgrounds        <small>Edit Backgrounds</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/backgrounds'); ?>">Backgrounds</a></li>
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
                            <h3 class="widget-user-username">Backgrounds</h3>
                            <h5 class="widget-user-desc">Edit Backgrounds</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/backgrounds/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_backgrounds', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_backgrounds', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="name" class="col-sm-2 control-label">Nama Background 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="<?= set_value('name', $backgrounds->name); ?>">
                                <small class="info help-block">
                                <b>Input Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="image_url" class="col-sm-2 control-label">File Gambar Background 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="backgrounds_image_url_galery"></div>
                                <input class="data_file data_file_uuid" name="backgrounds_image_url_uuid" id="backgrounds_image_url_uuid" type="hidden" value="<?= set_value('backgrounds_image_url_uuid'); ?>">
                                <input class="data_file" name="backgrounds_image_url_name" id="backgrounds_image_url_name" type="hidden" value="<?= set_value('backgrounds_image_url_name', $backgrounds->image_url); ?>">
                                <small class="info help-block">
                                <b>Input Image Url</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                  
                                                <div class="form-group ">
                            <label for="category" class="col-sm-2 control-label">Kategori Latar</label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="category" id="category" data-placeholder="Pilih Kategori Visual">
                                    <?php $current_cat = isset($backgrounds) ? $backgrounds->category : set_value('category'); ?>
                                    <option <?= $current_cat == 'Floral & Botanical' ? 'selected' : ''; ?> value="Floral & Botanical">Floral & Botanical</option>
                                    <option <?= $current_cat == 'Tekstur & Kertas' ? 'selected' : ''; ?> value="Tekstur & Kertas">Tekstur & Kertas</option>
                                    <option <?= $current_cat == 'Ornamen Islami' ? 'selected' : ''; ?> value="Ornamen Islami">Ornamen Islami</option>
                                    <option <?= $current_cat == 'Geometris & Minimalis' ? 'selected' : ''; ?> value="Geometris & Minimalis">Geometris & Minimalis</option>
                                    <option <?= $current_cat == 'Tradisional Nusantara' ? 'selected' : ''; ?> value="Tradisional Nusantara">Tradisional Nusantara</option>
                                    <option <?= $current_cat == 'Lainnya' ? 'selected' : ''; ?> value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="is_active" class="col-sm-2 control-label">Status Aktif 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="is_active" id="is_active">
                                    <option <?= (isset($backgrounds) && $backgrounds->is_active == 1) || set_value('is_active', 1) == 1 ? 'selected' : ''; ?> value="1">Aktif</option>
                                    <option <?= isset($backgrounds) && $backgrounds->is_active == 0 ? 'selected' : ''; ?> value="0">Nonaktif</option>
                                </select>
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
              window.location.href = BASE_URL + 'administrator/backgrounds';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_backgrounds = $('#form_backgrounds');
        var data_post = form_backgrounds.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_backgrounds.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#backgrounds_image_galery').find('li').attr('qq-file-id');
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

       $('#backgrounds_image_url_galery').fineUploader({
          template: 'qq-template-gallery',
          request: {
              endpoint: BASE_URL + '/administrator/backgrounds/upload_image_url_file',
              params : params
          },
          deleteFile: {
              enabled: true, // defaults to false
              endpoint: BASE_URL + '/administrator/backgrounds/delete_image_url_file'
          },
          thumbnails: {
              placeholders: {
                  waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                  notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
              }
          },
           session : {
             endpoint: BASE_URL + 'administrator/backgrounds/get_image_url_file/<?= $backgrounds->id; ?>',
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
                   var uuid = $('#backgrounds_image_url_galery').fineUploader('getUuid', id);
                   $('#backgrounds_image_url_uuid').val(uuid);
                   $('#backgrounds_image_url_name').val(xhr.uploadName);
                } else {
                   toastr['error'](xhr.error);
                }
              },
              onSubmit : function(id, name) {
                  var uuid = $('#backgrounds_image_url_uuid').val();
                  $.get(BASE_URL + '/administrator/backgrounds/delete_image_url_file/' + uuid);
              },
              onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                  $('#backgrounds_image_url_uuid').val('');
                  $('#backgrounds_image_url_name').val('');
                }
              }
          }
      }); /*end image_url galey*/
              
       
           
    
    }); /*end doc ready*/
</script>