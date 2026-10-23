
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
        Orders        <small><?= cclang('new', ['Orders']); ?> </small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/orders'); ?>">Orders</a></li>
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
                            <h3 class="widget-user-username">Orders</h3>
                            <h5 class="widget-user-desc"><?= cclang('new', ['Orders']); ?></h5>
                            <hr>
                        </div>
                        <?= form_open('', [
                            'name'    => 'form_orders', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_orders', 
                            'enctype' => 'multipart/form-data', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="invoice_number" class="col-sm-2 control-label">Nomor Invoice 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="invoice_number" id="invoice_number" placeholder="Invoice Number" value="<?= set_value('invoice_number'); ?>">
                                <small class="info help-block">
                                <b>Input Invoice Number</b> Max Length : 100.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="user_id" class="col-sm-2 control-label">Pelanggan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="user_id" id="user_id" data-placeholder="Pilih Pelanggan">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('aauth_users') as $row): ?>
                                    <option <?= $row->id == set_value('user_id') ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= !empty($row->full_name) ? $row->full_name . ' (' . $row->username . ')' : $row->username; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="template_id" class="col-sm-2 control-label">Template Pesanan 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="template_id" id="template_id" data-placeholder="Pilih Template Pesanan">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('templates') as $row): ?>
                                    <option <?= $row->id == set_value('template_id') ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->name . ' - Rp ' . number_format($row->price, 0, ',', '.'); ?></option>
                                    <?php endforeach; ?>  
                                </select>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="invitation_id" class="col-sm-2 control-label">ID Undangan (Opsional) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="invitation_id" id="invitation_id" placeholder="Invitation Id" value="<?= set_value('invitation_id'); ?>">
                                <small class="info help-block">
                                <b>Input Invitation Id</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                
                                                 
                                                <div class="form-group ">
                            <label for="amount" class="col-sm-2 control-label">Total Pembayaran (Rp) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="amount" id="amount" placeholder="Amount" value="<?= set_value('amount'); ?>">
                                <small class="info help-block">
                                <b>Input Amount</b> Max Length : 12.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="payment_method" class="col-sm-2 control-label">Metode Pembayaran</label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="payment_method" id="payment_method" data-placeholder="Pilih Metode Pembayaran">
                                    <option value="BCA Transfer">BCA Transfer</option>
                                    <option value="Mandiri Transfer">Mandiri Transfer</option>
                                    <option value="BRI Transfer">BRI Transfer</option>
                                    <option value="BNI Transfer">BNI Transfer</option>
                                    <option value="QRIS">QRIS</option>
                                    <option value="Manual Transfer">Transfer Bank Manual Lainnya</option>
                                </select>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="payment_proof" class="col-sm-2 control-label">Bukti Transfer (Nama File / URL) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="payment_proof" id="payment_proof" placeholder="Payment Proof" value="<?= set_value('payment_proof'); ?>">
                                <small class="info help-block">
                                <b>Input Payment Proof</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="payment_status" class="col-sm-2 control-label">Status Pembayaran 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="payment_status" id="payment_status">
                                    <option value="pending">Menunggu Pembayaran (Pending)</option>
                                    <option value="paid">Lunas (Paid / Settled)</option>
                                    <option value="failed">Gagal (Failed)</option>
                                    <option value="refunded">Dikembalikan (Refunded)</option>
                                </select>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="verified_by" class="col-sm-2 control-label">Diverifikasi Oleh</label>
                            <div class="col-sm-8">
                                <select class="form-control chosen chosen-select" name="verified_by" id="verified_by" data-placeholder="Pilih Admin Verifikator (Opsional)">
                                    <option value=""></option>
                                    <?php foreach (db_get_all_data('aauth_users') as $row): ?>
                                    <option <?= $row->id == set_value('verified_by') ? 'selected' : ''; ?> value="<?= $row->id ?>"><?= $row->username; ?></option>
                                    <?php endforeach; ?>  
                                </select>
                            </div>
                        </div>

                                                 
                                                <div class="form-group ">
                            <label for="paid_at" class="col-sm-2 control-label">Waktu Pembayaran (Opsional) 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datetimepicker" name="paid_at"  id="paid_at">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                        <div class="message"></div>
                        <hr style="margin: 30px 0 25px 0; border-top: 1px solid #e9ecef;">
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
<!-- Page script -->
<script>
    $(document).ready(function(){
                   
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
              window.location.href = BASE_URL + 'administrator/orders';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
            
        var form_orders = $('#form_orders');
        var data_post = form_orders.serializeArray();
        var save_type = $(this).attr('data-stype');

        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: BASE_URL + '/administrator/orders/add_save',
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
            resetForm();
            $('.chosen option').prop('selected', false).trigger('chosen:updated');
                
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