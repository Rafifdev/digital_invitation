<section class="content-header">
   <h1>
      Data Penggajian
      <small>Gaji Pokok & Tunjangan Karyawan</small>
   </h1>
   <ol class="breadcrumb">
      <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="#">Penggajian</a></li>
      <li class="active">Master Gaji</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-12">
         <div class="box box-warning" style="border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <div class="box-header with-border" style="padding: 15px 20px;">
               <h3 class="box-title"><i class="fa fa-money text-warning margin-r-5"></i> Daftar Master Gaji Karyawan</h3>
               <div class="box-tools pull-right">
                  <button type="button" class="btn btn-primary btn-sm btn-tambah-gaji" style="border-radius: 20px; padding: 5px 15px;">
                     <i class="fa fa-plus-circle"></i> Tambah Data Gaji
                  </button>
               </div>
            </div>
            
            <div class="box-body" style="padding: 20px;">
               <div class="table-responsive">
                  <table class="table table-bordered table-striped table-hover dataTable">
                     <thead>
                        <tr class="bg-gray-light">
                           <th width="3%">No</th>
                           <th width="20%">Nama Karyawan</th>
                           <th width="15%">Jabatan & Tipe</th>
                           <th width="15%" class="text-right">Gaji Pokok</th>
                           <th width="15%" class="text-right">Tunjangan Tetap</th>
                           <th width="15%" class="text-right">Tunjangan Kehadiran</th>
                           <th width="10%" class="text-center">Aksi</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php $no=1; foreach($karyawans_sudah_diset as $karyawan): ?>
                        <tr>
                           <td><?= $no++; ?></td>
                           <td>
                              <strong><?= _ent($karyawan->nama_lengkap); ?></strong><br>
                              <small class="text-muted">NIP: <?= _ent($karyawan->nik); ?></small>
                           </td>
                           <td>
                              <?= _ent($karyawan->jabatan); ?><br>
                              <?php if($karyawan->tipe_kerja == 'WFO'): ?>
                                 <span class="label label-success">WFO</span>
                              <?php elseif($karyawan->tipe_kerja == 'WFH'): ?>
                                 <span class="label label-warning">WFH</span>
                              <?php else: ?>
                                 <span class="label label-info">WFA</span>
                              <?php endif; ?>
                           </td>
                           <td class="text-right">
                              <strong>Rp <?= number_format((float)($karyawan->gaji_pokok ?? 0), 0, ',', '.'); ?></strong>
                           </td>
                           <td class="text-right">
                              Rp <?= number_format((float)($karyawan->tunjangan_tetap ?? 0), 0, ',', '.'); ?>
                           </td>
                           <td class="text-right">
                              <?php if($karyawan->tipe_kerja == 'WFO'): ?>
                                 Rp <?= number_format((float)($karyawan->tunjangan_kehadiran ?? 0), 0, ',', '.'); ?>
                              <?php else: ?>
                                 <span class="text-muted"><i class="fa fa-ban" title="Hanya untuk WFO"></i> N/A</span>
                              <?php endif; ?>
                           </td>
                           <td class="text-center">
                              <button type="button" class="btn btn-sm btn-warning btn-edit-gaji" 
                                      data-id="<?= $karyawan->id; ?>" 
                                      data-nama="<?= _ent($karyawan->nama_lengkap); ?>"
                                      data-pokok="<?= $karyawan->gaji_pokok ?? 0; ?>"
                                      data-tetap="<?= $karyawan->tunjangan_tetap ?? 0; ?>"
                                      data-kehadiran="<?= $karyawan->tunjangan_kehadiran ?? 0; ?>"
                                      data-tipe="<?= $karyawan->tipe_kerja; ?>"
                                      title="Edit Gaji">
                                 <i class="fa fa-pencil"></i> Edit Gaji
                              </button>
                           </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if(empty($karyawans_sudah_diset)): ?>
                        <tr>
                           <td colspan="7" class="text-center text-muted" style="padding: 30px;">
                              <i class="fa fa-inbox fa-3x" style="opacity:0.3"></i><br><br>Belum ada data karyawan.
                           </td>
                        </tr>
                        <?php endif; ?>
                     </tbody>
                  </table>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>

<!-- MODAL EDIT GAJI -->
<div class="modal fade" id="modalEditGaji" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
         <div class="modal-header" style="background: #f39c12; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
            <h4 class="modal-title"><i class="fa fa-money"></i> Edit Gaji Pokok & Tunjangan</h4>
         </div>
         <form id="form_edit_gaji" method="POST">
            <div class="modal-body" style="padding: 25px;">
               
               <div style="margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                  <strong style="font-size: 16px;">Karyawan: <span id="nama_karyawan_label" class="text-primary"></span></strong>
                  <br>
                  <small class="text-muted">Tipe Kerja: <span id="tipe_kerja_label"></span></small>
               </div>

               <input type="hidden" name="karyawan_id" id="modal_karyawan_id">

               <div class="form-group">
                  <label>Gaji Pokok (Bulanan) <span class="text-danger">*</span></label>
                  <div class="input-group">
                     <span class="input-group-addon">Rp</span>
                     <input type="text" class="form-control currency-input" name="gaji_pokok" id="modal_gaji_pokok" required>
                  </div>
                  <small class="text-muted">Akan dibagi jumlah hari kerja (Cut-off) untuk mendapat Gaji Harian.</small>
               </div>

               <div class="form-group">
                  <label>Tunjangan Tetap (Bulanan)</label>
                  <div class="input-group">
                     <span class="input-group-addon">Rp</span>
                     <input type="text" class="form-control currency-input" name="tunjangan_tetap" id="modal_tunjangan_tetap">
                  </div>
                  <small class="text-muted">Tetap dibayar penuh (contoh: Tunjangan Jabatan).</small>
               </div>

               <div class="form-group" id="div_tunjangan_kehadiran">
                  <label>Tunjangan Kehadiran (Bulanan)</label>
                  <div class="input-group">
                     <span class="input-group-addon">Rp</span>
                     <input type="text" class="form-control currency-input" name="tunjangan_kehadiran" id="modal_tunjangan_kehadiran">
                  </div>
                  <small class="text-muted text-warning"><i class="fa fa-info-circle"></i> Hanya dibayar jika hadir fisik (khusus karyawan WFO).</small>
               </div>
               
               <div id="alert_non_wfo" class="alert alert-info" style="display:none; margin-top: 10px;">
                  <i class="fa fa-info-circle"></i> Karyawan non-WFO tidak mendapatkan perhitungan Tunjangan Kehadiran Harian.
               </div>

            </div>
            <div class="modal-footer" style="background: #f9f9f9; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
               <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Simpan Gaji</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- MODAL TAMBAH GAJI -->
<div class="modal fade" id="modalTambahGaji" tabindex="-1" role="dialog" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
         <div class="modal-header" style="background: #3c8dbc; color: white; border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
            <h4 class="modal-title"><i class="fa fa-plus-circle"></i> Tambah Data Gaji Karyawan</h4>
         </div>
         <form id="form_tambah_gaji" method="POST" action="<?= site_url('administrator/hr_payroll/tambah_gaji_save') ?>">
            <div class="modal-body" style="padding: 25px;">
               
               <div class="form-group">
                  <label>Pilih Karyawan <span class="text-danger">*</span></label>
                  <select name="karyawan_id" id="tambah_karyawan_id" class="form-control" required style="width: 100%;">
                     <option value="">- Pilih Karyawan -</option>
                     <?php foreach($karyawans_belum_diset as $k): ?>
                     <option value="<?= $k->id ?>" data-tipe="<?= $k->tipe_kerja ?>">
                        <?= _ent($k->nama_lengkap) ?> (<?= _ent($k->nik) ?>)
                     </option>
                     <?php endforeach; ?>
                  </select>
               </div>

               <div class="form-group">
                  <label>Gaji Pokok (Bulanan) <span class="text-danger">*</span></label>
                  <div class="input-group">
                     <span class="input-group-addon">Rp</span>
                     <input type="text" class="form-control currency-input" name="gaji_pokok" id="tambah_gaji_pokok" placeholder="Contoh: 3.500.000" required>
                  </div>
               </div>

               <div class="form-group">
                  <label>Tunjangan Tetap (Bulanan)</label>
                  <div class="input-group">
                     <span class="input-group-addon">Rp</span>
                     <input type="text" class="form-control currency-input" name="tunjangan_tetap" id="tambah_tunjangan_tetap" placeholder="Contoh: 500.000">
                  </div>
               </div>

               <div class="form-group" id="tambah_div_kehadiran">
                  <label>Tunjangan Kehadiran (Bulanan)</label>
                  <div class="input-group">
                     <span class="input-group-addon">Rp</span>
                     <input type="text" class="form-control currency-input" name="tunjangan_kehadiran" id="tambah_tunjangan_kehadiran" placeholder="Contoh: 300.000">
                  </div>
                  <small class="text-muted text-warning"><i class="fa fa-info-circle"></i> Hanya dibayar jika hadir fisik (WFO).</small>
               </div>

            </div>
            <div class="modal-footer" style="background: #f9f9f9; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
               <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
               <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Data Gaji</button>
            </div>
         </form>
      </div>
   </div>
</div>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/dataTables.bootstrap.min.css">
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/dataTables.bootstrap.min.js"></script>

<script>
$(document).ready(function() {
   $('.dataTable').DataTable({
      "ordering": true,
      "pageLength": 25,
      "language": {
         "search": "Cari Karyawan:",
         "lengthMenu": "Tampilkan _MENU_ data",
         "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ karyawan",
         "infoEmpty": "Data kosong",
         "zeroRecords": "Tidak ada data yang cocok",
         "paginate": {
            "first": "Awal",
            "last": "Akhir",
            "next": "Selanjutnya",
            "previous": "Sebelumnya"
         }
      }
   });

   // Formatter uang
   function formatRupiah(angka) {
      if (!angka && angka !== 0) return '';
      var number_string = angka.toString().replace(/[^,\d]/g, '').toString();
      
      // Hapus angka 0 di depan
      if(number_string.length > 1) {
          number_string = number_string.replace(/^0+/, '');
          if(number_string === '') number_string = '0';
      }

      var split = number_string.split(','),
         sisa  = split[0].length % 3,
         rupiah  = split[0].substr(0, sisa),
         ribuan  = split[0].substr(sisa).match(/\d{3}/gi);

      if (ribuan) {
         var separator = sisa ? '.' : '';
         rupiah += separator + ribuan.join('.');
      }
      return rupiah;
   }

   $(document).on('keyup', '.currency-input', function(){
      $(this).val(formatRupiah($(this).val()));
   });

   // Tombol Edit Gaji
   $('.btn-edit-gaji').on('click', function() {
      var id = $(this).data('id');
      var nama = $(this).data('nama');
      var pokok = $(this).data('pokok');
      var tetap = $(this).data('tetap');
      var kehadiran = $(this).data('kehadiran');
      var tipe = $(this).data('tipe');

      $('#modal_karyawan_id').val(id);
      $('#nama_karyawan_label').text(nama);
      $('#tipe_kerja_label').text(tipe);
      
      $('#modal_gaji_pokok').val(formatRupiah(pokok));
      $('#modal_tunjangan_tetap').val(formatRupiah(tetap));
      
      if (tipe !== 'WFO') {
         $('#div_tunjangan_kehadiran').hide();
         $('#alert_non_wfo').show();
         $('#modal_tunjangan_kehadiran').val('0');
      } else {
         $('#div_tunjangan_kehadiran').show();
         $('#alert_non_wfo').hide();
         $('#modal_tunjangan_kehadiran').val(formatRupiah(kehadiran));
      }

      $('#form_edit_gaji').attr('action', "<?= site_url('administrator/hr_payroll/edit_gaji_save/') ?>" + id);
      $('#modalEditGaji').modal('show');
   });

   // Submit form Edit
   $('#form_edit_gaji').on('submit', function(e) {
      e.preventDefault();
      var btn = $(this).find('button[type="submit"]');
      var originalText = btn.html();
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

      $.ajax({
         url: $(this).attr('action'),
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res) {
            if (res.success) {
               $('#modalEditGaji').modal('hide');
               // reload halaman untuk memperbarui data di tabel
               location.reload();
            } else {
               alert(res.message);
               btn.prop('disabled', false).html(originalText);
            }
         },
         error: function() {
            alert('Terjadi kesalahan jaringan.');
            btn.prop('disabled', false).html(originalText);
         }
      });
   });

   // Tombol Tambah Gaji
   $('.btn-tambah-gaji').on('click', function() {
      $('#form_tambah_gaji')[0].reset();
      $('#tambah_div_kehadiran').show();
      $('#modalTambahGaji').modal('show');
   });

   // Deteksi perubahan karyawan di dropdown Tambah
   $('#tambah_karyawan_id').on('change', function() {
      var tipe = $(this).find(':selected').data('tipe');
      if(tipe !== 'WFO' && tipe !== undefined && tipe !== '') {
         $('#tambah_div_kehadiran').hide();
         $('#tambah_tunjangan_kehadiran').val('0');
      } else {
         $('#tambah_div_kehadiran').show();
      }
   });

   // Submit form Tambah
   $('#form_tambah_gaji').on('submit', function(e) {
      e.preventDefault();
      var btn = $(this).find('button[type="submit"]');
      var originalText = btn.html();
      btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Menyimpan...');

      $.ajax({
         url: $(this).attr('action'),
         type: 'POST',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(res) {
            if (res.success) {
               $('#modalTambahGaji').modal('hide');
               location.reload();
            } else {
               alert(res.message);
               btn.prop('disabled', false).html(originalText);
            }
         },
         error: function() {
            alert('Terjadi kesalahan jaringan.');
            btn.prop('disabled', false).html(originalText);
         }
      });
   });

});
</script>
