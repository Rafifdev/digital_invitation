<!-- Fine Uploader Gallery CSS file
    ====================================================================== -->
<link href="<?= BASE_ASSET; ?>/fine-upload/fine-uploader-gallery.min.css" rel="stylesheet">
<!-- Fine Uploader jQuery JS file
    ====================================================================== -->
<script src="<?= BASE_ASSET; ?>/fine-upload/jquery.fine-uploader.js"></script>
<?php $this->load->view('core_template/fine_upload'); ?>
<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
    function domo() {

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
        Templates <small><?= cclang('new', ['Templates']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a href="<?= site_url('administrator/templates'); ?>">Templates</a></li>
        <li class="active"><?= cclang('new'); ?></li>
    </ol>
</section>
<!-- Main content -->
<section class="content">
    <div class="row">
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
                            <h3 class="widget-user-username">Templates</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Templates']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name' => 'form_templates',
                            'class' => 'form-horizontal',
                            'id' => 'form_templates',
                            'enctype' => 'multipart/form-data',
                            'method' => 'POST'
                        ]); ?>

                        <div class="form-group ">
                            <label for="name" class="col-sm-2 control-label">Nama Template
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="name" id="name" placeholder="Name"
                                    value="<?= set_value('name'); ?>">
                                <small class="info help-block">
                                    <b>Input Name</b> Max Length : 150.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="slug" class="col-sm-2 control-label">Slug / Kode URL
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="slug" id="slug" placeholder="Slug"
                                    value="<?= set_value('slug'); ?>">
                                <small class="info help-block">
                                    <b>Input Slug</b> Max Length : 150.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="preview_image" class="col-sm-2 control-label">Foto Sampul / Pratinjau
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <div id="templates_preview_image_galery"></div>
                                <input class="data_file" name="templates_preview_image_uuid"
                                    id="templates_preview_image_uuid" type="hidden"
                                    value="<?= set_value('templates_preview_image_uuid'); ?>">
                                <input class="data_file" name="templates_preview_image_name"
                                    id="templates_preview_image_name" type="hidden"
                                    value="<?= set_value('templates_preview_image_name'); ?>">
                                <small class="info help-block">
                                    <b>Input Preview Image</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="demo_url" class="col-sm-2 control-label">Tautan Demo Template
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="demo_url" id="demo_url"
                                    placeholder="Demo Url" value="<?= set_value('demo_url'); ?>">
                                <small class="info help-block">
                                    <b>Input Demo Url</b> Max Length : 255.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="category" class="col-sm-2 control-label">Category
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="category" id="category"
                                    data-placeholder="Pilih Kategori Template">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('template_categories') as $row): ?>
                                        <option <?= $row->name == set_value('category') ? 'selected' : ''; ?>
                                            value="<?= $row->name; ?>"><?= $row->name; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="info help-block">
                                    <b>Input Category</b> Max Length : 100.</small>
                            </div>
                        </div>

                        <div class="form-group ">
                            <label for="price" class="col-sm-2 control-label">Harga Jual (Rp)</label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="price" id="price" placeholder="0"
                                    value="<?= set_value('price', isset($templates) ? $templates->price : 0); ?>">
                                <small class="info help-block">Masukkan nominal Harga Jual template (Rp), isi 0 jika
                                    gratis.</small>
                            </div>
                        </div>
                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="default_config" class="col-sm-2 control-label">Konfigurasi JSON (Opsional)</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" name="default_config" id="default_config" rows="3"
                                    placeholder="{}"><?= set_value('default_config', isset($templates) ? $templates->default_config : '{}'); ?></textarea>
                                <small class="info help-block">Setelan bawaan tema (seperti palet warna atau urutan blok section). Otomatis disalin ke tema undangan saat user memilih template ini. Biarkan <code>{}</code> jika default.</small>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label for="tier" class="col-sm-2 control-label">Kelas / Tier
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="tier" id="tier"
                                    data-placeholder="Pilih Tier Template">
                                    <?php $current_tier = isset($templates) ? strtolower($templates->tier) : strtolower(set_value('tier')); ?>
                                    <option <?= $current_tier == 'free' ? 'selected' : ''; ?> value="free">Free / Gratis
                                    </option>
                                    <option <?= $current_tier == 'premium' ? 'selected' : ''; ?> value="premium">Premium
                                    </option>
                                    <option <?= $current_tier == 'exclusive' ? 'selected' : ''; ?> value="exclusive">
                                        Eksklusif</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 25px;">
                            <label for="is_active" class="col-sm-2 control-label">Status Aktif
                                <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="is_active" id="is_active">
                                    <option <?= (isset($templates) && $templates->is_active == 1) || set_value('is_active', 1) == 1 ? 'selected' : ''; ?> value="1">Aktif</option>
                                    <option <?= isset($templates) && $templates->is_active == 0 ? 'selected' : ''; ?>
                                        value="0">Nonaktif</option>
                                </select>
                            </div>
                        </div>

                        <hr style="margin: 30px 0 25px 0; border-top: 1px solid #e9ecef;">

                        <div class="message"></div>
                        <div class="row-fluid col-md-7" style="margin-bottom: 30px;">
                            <button class="btn btn-flat btn-primary btn_save btn_action" id="btn_save" data-stype='stay'
                                title="<?= cclang('save_button'); ?> (Ctrl+s)">
                                <i class="fa fa-save"></i> <?= cclang('save_button'); ?>
                            </button>
                            <a class="btn btn-flat btn-info btn_save btn_action btn_save_back" id="btn_save"
                                data-stype='back' title="<?= cclang('save_and_go_the_list_button'); ?> (Ctrl+d)">
                                <i class="ion ion-ios-list-outline"></i> <?= cclang('save_and_go_the_list_button'); ?>
                            </a>
                            <a class="btn btn-flat btn-default btn_action" id="btn_cancel"
                                title="<?= cclang('cancel_button'); ?> (Ctrl+x)">
                                <i class="fa fa-undo"></i> <?= cclang('cancel_button'); ?>
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
    $(document).ready(function () {

        $('#btn_cancel').click(function () {
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
                function (isConfirm) {
                    if (isConfirm) {
                        window.location.href = BASE_URL + 'administrator/templates';
                    }
                });

            return false;
        }); /*end btn cancel*/

        $('.btn_save').click(function () {
            $('.message').fadeOut();

            var form_templates = $('#form_templates');
            var data_post = form_templates.serializeArray();
            var save_type = $(this).attr('data-stype');

            data_post.push({ name: 'save_type', value: save_type });

            $('.loading').show();

            $.ajax({
                url: BASE_URL + '/administrator/templates/add_save',
                type: 'POST',
                dataType: 'json',
                data: data_post,
            })
                .done(function (res) {
                    if (res.success) {
                        var id_preview_image = $('#templates_preview_image_galery').find('li').attr('qq-file-id');

                        if (save_type == 'back') {
                            window.location.href = res.redirect;
                            return;
                        }

                        $('.message').printMessage({ message: res.message });
                        $('.message').fadeIn();
                        resetForm();
                        if (typeof id_preview_image !== 'undefined') {
                            $('#templates_preview_image_galery').fineUploader('deleteFile', id_preview_image);
                        }
                        $('.chosen option').prop('selected', false).trigger('chosen:updated');

                    } else {
                        $('.message').printMessage({ message: res.message, type: 'warning' });
                    }

                })
                .fail(function () {
                    $('.message').printMessage({ message: 'Error save data', type: 'warning' });
                })
                .always(function () {
                    $('.loading').hide();
                    $('html, body').animate({ scrollTop: $(document).height() }, 2000);
                });

            return false;
        }); /*end btn save*/

        var params = {};
        params[csrf] = token;

        $('#templates_preview_image_galery').fineUploader({
            template: 'qq-template-gallery',
            request: {
                endpoint: BASE_URL + '/administrator/templates/upload_preview_image_file',
                params: params
            },
            deleteFile: {
                enabled: true,
                endpoint: BASE_URL + '/administrator/templates/delete_preview_image_file',
            },
            thumbnails: {
                placeholders: {
                    waitingPath: BASE_URL + '/asset/fine-upload/placeholders/waiting-generic.png',
                    notAvailablePath: BASE_URL + '/asset/fine-upload/placeholders/not_available-generic.png'
                }
            },
            multiple: false,
            validation: {
                allowedExtensions: ["*"],
                sizeLimit: 0,
            },
            showMessage: function (msg) {
                toastr['error'](msg);
            },
            callbacks: {
                onComplete: function (id, name, xhr) {
                    if (xhr.success) {
                        var uuid = $('#templates_preview_image_galery').fineUploader('getUuid', id);
                        $('#templates_preview_image_uuid').val(uuid);
                        $('#templates_preview_image_name').val(xhr.uploadName);
                    } else {
                        toastr['error'](xhr.error);
                    }
                },
                onSubmit: function (id, name) {
                    var uuid = $('#templates_preview_image_uuid').val();
                    $.get(BASE_URL + '/administrator/templates/delete_preview_image_file/' + uuid);
                },
                onDeleteComplete: function (id, xhr, isError) {
                    if (isError == false) {
                        $('#templates_preview_image_uuid').val('');
                        $('#templates_preview_image_name').val('');
                    }
                }
            }
        }); /*end preview_image galery*/





    }); /*end doc ready*/
</script>