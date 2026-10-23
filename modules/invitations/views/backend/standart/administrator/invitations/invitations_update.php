<!-- Fine Uploader Gallery CSS file -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
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
    <h1>
        Semua Undangan        <small>Edit Data Undangan</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/invitations'); ?>">Semua Undangan</a></li>
        <li class="active">Edit Undangan</li>
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
                            <h3 class="widget-user-username">Semua Undangan</h3>
                            <h5 class="widget-user-desc">Edit Data Undangan: <?= _ent($invitations->title); ?></h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/invitations/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_invitations', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_invitations', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                        <!-- 1. Pemilik Undangan -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="user_id" class="col-sm-2 control-label">Pemilik Undangan <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="user_id" id="user_id" data-placeholder="Pilih Pemilik Akun">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('aauth_users') as $row): ?>
                                    <option <?= (set_value('user_id', $invitations->user_id) == $row->id) ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= !empty($row->full_name) ? $row->full_name . ' (' . $row->username . ')' : $row->username; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">Pilih akun member pengguna yang memiliki undangan ini.</small>
                            </div>
                        </div>

                        <!-- 2. Template Desain -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="template_id" class="col-sm-2 control-label">Template Desain <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="template_id" id="template_id" data-placeholder="Pilih Tema Template">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('templates') as $row): ?>
                                    <option <?= (set_value('template_id', $invitations->template_id) == $row->id) ? 'selected' : ''; ?> value="<?= $row->id; ?>"><?= $row->name . ' (' . $row->category . ')'; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                                <small class="info help-block">Pilih template desain tampilan undangan yang diinginkan.</small>
                            </div>
                        </div>

                        <!-- 3. Judul Undangan -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="title" class="col-sm-2 control-label">Judul Undangan <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="title" id="title" placeholder="Contoh: The Wedding of Romeo & Juliet" value="<?= set_value('title', $invitations->title); ?>">
                                <small class="info help-block">Nama atau judul undangan yang akan ditampilkan.</small>
                            </div>
                        </div>

                        <!-- 4. Slug / URL Publik -->
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="slug" class="col-sm-2 control-label">Slug / URL Publik <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="slug" id="slug" placeholder="Contoh: romeo-juliet" value="<?= set_value('slug', $invitations->slug); ?>">
                                <small class="info help-block">Digunakan untuk tautan undangan publik: <code><?= site_url('invitation/'); ?>[slug]</code></small>
                            </div>
                        </div>

                        <!-- 5. Foto Sampul (Cover Photo) -->
                        <div class="form-group" style="margin-bottom: 25px;">
                            <label for="cover_photo" class="col-sm-2 control-label">Foto Sampul (Opsional)</label>
                            <div class="col-sm-8">
                                <div id="invitations_cover_photo_galery"></div>
                                <input class="data_file" name="invitations_cover_photo_uuid" id="invitations_cover_photo_uuid" type="hidden" value="<?= set_value('invitations_cover_photo_uuid'); ?>">
                                <input class="data_file" name="invitations_cover_photo_name" id="invitations_cover_photo_name" type="hidden" value="<?= set_value('invitations_cover_photo_name', $invitations->cover_photo); ?>">
                                <small class="info help-block" style="margin-top: 8px;">Upload foto cover pasangan (JPG/PNG maks. 5MB).</small>
                                <div class="clearfix" style="margin-bottom: 10px;"></div>
                            </div>
                        </div>

                        <!-- 6. Status Publikasi -->
                        <div class="form-group" style="margin-top: 15px; margin-bottom: 25px;">
                            <label for="status" class="col-sm-2 control-label">Status Publikasi <i class="required">*</i></label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="status" id="status" data-placeholder="Pilih Status Publikasi">
                                    <option <?= (set_value('status', $invitations->status) == 'draft') ? 'selected' : ''; ?> value="draft">Draft (Belum Dipublikasikan)</option>
                                    <option <?= (set_value('status', $invitations->status) == 'published') ? 'selected' : ''; ?> value="published">Published (Aktif & Bisa Diakses Tamu)</option>
                                    <option <?= (set_value('status', $invitations->status) == 'archived') ? 'selected' : ''; ?> value="archived">Archived (Diarsipkan / Selesai Acara)</option>
                                </select>
                                <small class="info help-block" style="margin-top: 8px;">Tentukan status: <b>Draft</b> (belum tayang publik), <b>Published</b> (aktif dan dapat diakses tamu), atau <b>Archived</b> (selesai acara).</small>
                            </div>
                        </div>

                        <hr style="margin: 30px 0 25px 0; border-top: 1px solid #e9ecef;">

                        <div class="message"></div>
                        <div class="row-fluid col-md-7" style="margin-bottom: 30px;">
                            <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay' title="Simpan (Ctrl+s)">
                                <i class="fa fa-save" ></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save" data-stype='back' title="Simpan & Kembali (Ctrl+d)">
                                <i class="ion ion-ios-list-outline" ></i> <?= cclang('save_and_go_the_list_button'); ?>
                            </a>
                            <a class="btn btn-flat btn-default btn_action" id="btn_cancel" title="Batal (Ctrl+x)" href="<?= site_url('administrator/invitations'); ?>">
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
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function(){
    $('#btn_cancel').click(function(){
        swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Ya, Batalkan",
            cancelButtonText: "Tidak",
            closeOnConfirm: true,
            closeOnCancel: true
        }, function(isConfirm){
            if (isConfirm) {
                window.location.href = BASE_URL + 'administrator/invitations';
            }
        });
        return false;
    });

    $('.btn_save').click(function(){
        $('.message').fadeOut();
        var form_invitations = $('#form_invitations');
        var data_post = form_invitations.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
        $('.loading').show();

        $.ajax({
            url: form_invitations.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: data_post,
        })
        .done(function(res) {
            if(res.success) {
                if (save_type == 'back') {
                    window.location.href = res.redirect;
                    return;
                }
                $('.message').printMessage({message : res.message});
                $('.message').fadeIn();
                $('.data_file_uuid').val('');
            } else {
                $('.message').printMessage({message : res.message, type : 'warning'});
                $('.message').fadeIn();
            }
        })
        .fail(function() {
            $('.message').printMessage({message : 'Gagal menyimpan data', type : 'warning'});
            $('.message').fadeIn();
        })
        .always(function() {
            $('.loading').hide();
            $('html, body').animate({ scrollTop: $(document).height() }, 2000);
        });

        return false;
    });

    var params = {};
    params[csrf] = token;

    $('#invitations_cover_photo_galery').fineUploader({
        template: 'qq-template-gallery',
        request: {
            endpoint: BASE_URL + '/administrator/invitations/upload_cover_photo_file',
            params : params
        },
        deleteFile: {
            enabled: true,
            endpoint: BASE_URL + '/administrator/invitations/delete_cover_photo_file'
        },
        thumbnails: {
            placeholders: {
                waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
            }
        },
        session : {
            endpoint: BASE_URL + 'administrator/invitations/get_cover_photo_file/<?= $invitations->id; ?>',
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
                    var uuid = $('#invitations_cover_photo_galery').fineUploader('getUuid', id);
                    $('#invitations_cover_photo_uuid').val(uuid);
                    $('#invitations_cover_photo_name').val(xhr.uploadName);
                } else {
                    toastr['error'](xhr.error);
                }
            },
            onSubmit : function(id, name) {
                var uuid = $('#invitations_cover_photo_uuid').val();
                $.get(BASE_URL + '/administrator/invitations/delete_cover_photo_file/' + uuid);
            },
            onDeleteComplete : function(id, xhr, isError) {
                if (isError == false) {
                    $('#invitations_cover_photo_uuid').val('');
                    $('#invitations_cover_photo_name').val('');
                }
            }
        }
    });

    });
</script>