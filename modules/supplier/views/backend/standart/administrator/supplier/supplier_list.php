<?php
/**
 * supplier_list.php
 * View: Daftar Supplier
 */
?>
<section class="content-header">
   <h1>Suppliers <small>Register All Suppliers</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= base_url('administrator/supplier'); ?>">Master Data</a></li>
      <li class="active">Suppliers</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-primary">
            <div class="box-header with-border">
               <h3 class="box-title"><i class="fa fa-truck"></i> Register All Suppliers <span class="label label-warning" style="font-size:11px;"><?= $supplier_counts; ?> Item</span></h3>
               <div class="box-tools pull-right">
                  <?php is_allowed('supplier_add', function(){ ?>
                  <a href="<?= site_url('administrator/supplier/add'); ?>" class="btn btn-success btn-sm btn-flat">
                     <i class="fa fa-plus"></i> Tambah Supplier Baru
                  </a>
                  <?php }); ?>
                  <?php is_allowed('supplier_export', function(){ ?>
                  <a href="<?= site_url('administrator/supplier/export'); ?>" class="btn btn-primary btn-sm btn-flat">
                     <i class="fa fa-file-excel-o"></i> Export XLS
                  </a>
                  <a href="<?= site_url('administrator/supplier/export_pdf'); ?>" class="btn btn-info btn-sm btn-flat">
                     <i class="fa fa-file-pdf-o"></i> Export PDF
                  </a>
                  <?php }); ?>
               </div>
            </div>
            <div class="box-body">
               <!-- Filter -->
               <form id="form_supplier" action="<?= base_url('administrator/supplier/index'); ?>">
                  <div class="row margin-bottom">
                     <div class="col-md-4">
                        <div class="input-group">
                           <input type="text" class="form-control" name="q" id="filter" placeholder="Cari supplier..." value="<?= $this->input->get('q'); ?>">
                           <span class="input-group-btn">
                              <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              <a href="<?= base_url('administrator/supplier'); ?>" class="btn btn-default"><i class="fa fa-undo"></i></a>
                           </span>
                        </div>
                     </div>
                  </div>

                  <div class="table-responsive">
                     <table class="table table-bordered table-striped dataTable">
                        <thead>
                           <tr>
                              <th>Supplier Code</th>
                              <th>Supplier Name</th>
                              <th>Contact Person</th>
                              <th>Phone</th>
                              <th>E-mail</th>
                              <th>Address</th>
                              <th>Country</th>
                              <th>Currency</th>
                              <th>Bank Info</th>
                              <th>Notes</th>
                              <th class="text-center">Status</th>
                              <th class="text-center" width="120">Action</th>
                           </tr>
                        </thead>
                        <tbody>
                           <?php foreach($suppliers as $s): ?>
                           <tr>
                              <td><span class="label label-default"><?= _ent($s->kode_supplier); ?></span></td>
                              <td><strong><a href="<?= site_url('administrator/supplier/view/'.$s->id); ?>"><?= _ent($s->nama_supplier); ?></a></strong></td>
                              <td><?= _ent($s->kontak_person ?? '-'); ?></td>
                              <td><i class="fa fa-phone"></i> <?= _ent($s->telepon ?? '-'); ?></td>
                              <td><small><?= _ent($s->email ?? '-'); ?></small></td>
                              <td><small><?= _ent($s->alamat ?? '-'); ?></small></td>
                              <td><?= _ent($s->negara ?? 'Indonesia'); ?></td>
                              <td><span class="badge bg-purple"><?= _ent($s->mata_uang ?? 'IDR'); ?></span></td>
                              <td>
                                 <small>
                                    <strong><?= _ent($s->bank_name ?? 'BCA'); ?></strong><br>
                                    <?= _ent($s->bank_account ?? '-'); ?>
                                 </small>
                              </td>
                              <td><small class="text-muted"><?= _ent($s->catatan ?? '-'); ?></small></td>
                              <td class="text-center">
                                 <span class="label <?= $s->status == 'aktif' ? 'label-success' : 'label-danger'; ?>">
                                    <?= strtoupper($s->status); ?>
                                 </span>
                              </td>
                              <td class="text-center">
                                 <?php is_allowed('supplier_view', function() use ($s){ ?>
                                 <a href="<?= site_url('administrator/supplier/view/'.$s->id); ?>" class="btn btn-xs btn-default" title="Detail"><i class="fa fa-eye"></i></a>
                                 <?php }); ?>
                                 <?php is_allowed('supplier_update', function() use ($s){ ?>
                                 <a href="<?= site_url('administrator/supplier/edit/'.$s->id); ?>" class="btn btn-xs btn-warning" title="Edit"><i class="fa fa-edit"></i></a>
                                 <?php }); ?>
                                 <?php is_allowed('supplier_delete', function() use ($s){ ?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/supplier/delete/'.$s->id); ?>" class="btn btn-xs btn-danger remove-data" title="Hapus"><i class="fa fa-trash"></i></a>
                                 <?php }); ?>
                              </td>
                           </tr>
                           <?php endforeach; ?>
                           <?php if(count($suppliers) == 0): ?>
                           <tr><td colspan="12" class="text-center text-muted"><i class="fa fa-inbox"></i> Tidak ada data supplier</td></tr>
                           <?php endif; ?>
                        </tbody>
                      </table>
                   </div>
                </form>

                <div class="row" style="margin-top:15px;">
                   <div class="col-md-6">
                      <span class="text-muted">Total: <strong><?= $supplier_counts; ?></strong> supplier</span>
                   </div>
                   <div class="col-md-6">
                      <div class="pull-right"><?= $pagination; ?></div>
                   </div>
                </div>
             </div>
          </div>
       </div>
    </div>
</section>

<script>
$(document).ready(function(){
   $('.remove-data').click(function(){
      var url = $(this).attr('data-href');
      swal({ title: "Hapus Supplier?", text: "Data tidak dapat dikembalikan!", type: "warning",
         showCancelButton: true, confirmButtonColor: "#DD6B55",
         confirmButtonText: "Ya, Hapus!", cancelButtonText: "Batal"
      }, function(ok){ if(ok) window.location.href = url; });
   });
});
</script>
