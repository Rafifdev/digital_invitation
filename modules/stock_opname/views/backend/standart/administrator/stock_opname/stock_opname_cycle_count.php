<section class="content-header">
   <h1>Cycle Counting Harian <small>Audit Produk Fast Moving</small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/stock_opname'); ?>"><i class="fa fa-adjust"></i> Stock Opname</a></li>
      <li class="active">Cycle Count</li>
   </ol>
</section>

<section class="content">
<div class="row"><div class="col-md-6">
   <div class="box box-info">
      <div class="box-header with-border">
         <h3 class="box-title"><i class="fa fa-refresh"></i> Buat Cycle Count Harian</h3>
      </div>
      <form action="<?= current_url(); ?>" method="GET">
         <div class="box-body">
            <div class="callout callout-info">
               <h4><i class="fa fa-info-circle"></i> Info Cycle Counting</h4>
               <p>Cycle Counting secara otomatis memilih <strong>10 Produk Fast-Moving</strong> (paling sering diperjualbelikan) untuk diadit stok fisiknya secara harian tanpa menghentikan operasional gudang.</p>
            </div>
            <div class="form-group">
               <label>Pilih Gudang Audit <span class="text-danger">*</span></label>
               <select class="form-control chosen" name="gudang_id" required>
                  <option value="">-- Pilih Gudang --</option>
                  <?php foreach($gudangs as $g): ?>
                     <option value="<?= $g->id; ?>"><?= _ent($g->nama_gudang); ?></option>
                  <?php endforeach; ?>
               </select>
            </div>
         </div>
         <div class="box-footer">
            <button type="submit" class="btn btn-info btn-flat"><i class="fa fa-play"></i> Mulai Cycle Counting</button>
            <a href="<?= base_url('administrator/stock_opname'); ?>" class="btn btn-default btn-flat"><i class="fa fa-arrow-left"></i> Kembali</a>
         </div>
      </form>
   </div>
</div></div>
</section>
