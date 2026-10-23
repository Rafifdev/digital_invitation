<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
    function domo(){
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
    <h1>Master Kutipan & Ayat <small>Edit Kutipan</small></h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/quotes'); ?>">Kutipan & Ayat</a></li>
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
                        <div class="widget-user-header ">
                            <div class="widget-user-image">
                                <img class="img-circle" src="<?= BASE_ASSET; ?>/img/add2.png" alt="User Avatar">
                            </div>
                            <h3 class="widget-user-username">Kutipan & Ayat Suci</h3>
                            <h5 class="widget-user-desc">Edit Kutipan / Ayat</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/quotes/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_quotes', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_quotes', 
                            'method'  => 'POST'
                            ]); ?>
                         
                        <div class="form-group ">
                            <label for="category" class="col-sm-2 control-label">Kategori / Agama 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="category" id="category" data-placeholder="Pilih Kategori Agama / Jenis">
                                    <option <?= (isset($quotes) && $quotes->category == 'Islam') || set_value('category') == 'Islam' ? 'selected' : ''; ?> value="Islam">Islam</option>
                                    <option <?= (isset($quotes) && $quotes->category == 'Kristen') || set_value('category') == 'Kristen' ? 'selected' : ''; ?> value="Kristen">Kristen</option>
                                    <option <?= (isset($quotes) && $quotes->category == 'Katolik') || set_value('category') == 'Katolik' ? 'selected' : ''; ?> value="Katolik">Katolik</option>
                                    <option <?= (isset($quotes) && $quotes->category == 'Hindu') || set_value('category') == 'Hindu' ? 'selected' : ''; ?> value="Hindu">Hindu</option>
                                    <option <?= (isset($quotes) && $quotes->category == 'Buddha') || set_value('category') == 'Buddha' ? 'selected' : ''; ?> value="Buddha">Buddha</option>
                                    <option <?= (isset($quotes) && $quotes->category == 'Umum') || set_value('category') == 'Umum' ? 'selected' : ''; ?> value="Umum">Umum / Mutiara Kata</option>
                                </select>
                                <small class="info help-block">Pilih kategori agama atau jenis kutipan</small>
                            </div>
                        </div>
                                                 
                        <div class="form-group ">
                            <label for="source" class="col-sm-2 control-label">Sumber / Nama Ayat 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="source" id="source" placeholder="Contoh: QS. Ar-Rum: 21, 1 Korintus 13: 4-7, Kahlil Gibran" value="<?= set_value('source', $quotes->source); ?>">
                                <small class="info help-block">Nama surat, pasal, atau tokoh sumber kutipan</small>
                            </div>
                        </div>
                                                 
                        <div class="form-group ">
                            <label for="content_original" class="col-sm-2 control-label">Teks Asli / Lafadz Arab</label>
                            <div class="col-sm-8">
                                <textarea id="content_original" name="content_original" rows="4" class="form-control" placeholder="Tulis lafadz tulisan Arab (khusus ayat Al-Qur'an). Boleh dikosongkan jika tidak ada."><?= set_value('content_original', $quotes->content_original); ?></textarea>
                                <small class="info help-block"><b>Opsional:</b> Boleh dikosongkan. Khusus Al-Qur'an isi dengan tulisan Arab.</small>
                            </div>
                        </div>
                                                 
                        <div class="form-group ">
                            <label for="content" class="col-sm-2 control-label">Isi Terjemahan / Kutipan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="content" name="content" rows="5" class="form-control" placeholder="Teks terjemahan atau mutiara kata yang akan ditampilkan di undangan digital..."><?= set_value('content', $quotes->content); ?></textarea>
                                <small class="info help-block">Isi teks bahasa Indonesia yang akan dibaca oleh tamu undangan</small>
                            </div>
                        </div>
                                                 
                        <div class="form-group ">
                            <label for="is_active" class="col-sm-2 control-label">Status Aktif 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="is_active" id="is_active">
                                    <option <?= (isset($quotes) && $quotes->is_active == 1) || set_value('is_active') == '1' ? 'selected' : ''; ?> value="1">Aktif</option>
                                    <option <?= (isset($quotes) && $quotes->is_active == 0) || set_value('is_active') === '0' ? 'selected' : ''; ?> value="0">Nonaktif</option>
                                </select>
                                <small class="info help-block">Kutipan yang aktif dapat dipilih oleh pengguna saat membuat undangan</small>
                            </div>
                        </div>
                                                
                        <hr style="margin: 30px 0 25px 0; border-top: 1px solid #e9ecef;">
                        <div class="message"></div>
                        <div class="row-fluid col-md-7" style="margin-bottom: 30px;">
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
              window.location.href = BASE_URL + 'administrator/quotes';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_quotes = $('#form_quotes');
        var data_post = form_quotes.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_quotes.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#quotes_image_galery').find('li').attr('qq-file-id');
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