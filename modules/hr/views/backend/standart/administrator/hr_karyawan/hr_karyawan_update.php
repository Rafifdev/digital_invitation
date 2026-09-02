
<section class="content-header">
   <h1>Edit Karyawan</h1>
</section>
<section class="content">
   <div class="box box-warning">
      <div class="box-body">
          <form id="form_karyawan" method="POST" action="<?= site_url('administrator/hr_karyawan/edit_save/'.$hr_karyawan->id); ?>">
             
             <!-- BAGIAN AKUN LOGIN & USERNAME -->
             <div class="panel panel-primary" style="border-radius: 8px; border-color: #3c8dbc; margin-bottom: 25px;">
                 <div class="panel-heading" style="background: #3c8dbc; color: white; border-top-left-radius: 6px; border-top-right-radius: 6px; padding: 12px 15px;">
                     <h4 style="margin: 0; font-size: 16px; font-weight: bold;">
                         <i class="fa fa-key"></i> Akun Akses & Username Login
                     </h4>
                 </div>
                 <div class="panel-body" style="background: #f8fbfd; padding: 20px;">
                     <div class="row">
                        <div class="col-md-3">
                           <div class="form-group" style="margin-bottom:0;">
                              <label for="email" class="control-label">Email Karyawan <span class="text-danger">*</span></label>
                              <input type="email" class="form-control" name="email" id="email" value="<?= _ent($user_login->email ?? ''); ?>" autocomplete="off" required>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" style="margin-bottom:0;">
                              <label for="username" class="control-label">Username Login <span class="text-danger">*</span></label>
                              <input type="text" class="form-control" name="username" id="username" value="<?= _ent($user_login->username ?? ''); ?>" autocomplete="off" required>
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" style="margin-bottom:0;">
                              <label for="password" class="control-label">Password Baru <small class="text-muted">(Kosongkan jika tidak diubah)</small></label>
                              <input type="password" class="form-control" name="password" id="password" placeholder="Minimal 5 karakter" autocomplete="new-password">
                           </div>
                        </div>
                        <div class="col-md-3">
                           <div class="form-group" style="margin-bottom:0;">
                              <label for="group_id" class="control-label">Role / Hak Akses <span class="text-danger">*</span></label>
                              <select class="form-control" name="group_id" id="group_id" required>
                                 <option value="1" <?= (isset($current_group_id) && $current_group_id == 1) ? 'selected' : ''; ?>>Direktur / Superadmin (Akses Full)</option>
                                 <option value="6" <?= (isset($current_group_id) && $current_group_id == 6) ? 'selected' : ''; ?>>Manager / Atasan Utama (Lvl 2)</option>
                                 <option value="4" <?= (isset($current_group_id) && $current_group_id == 4) ? 'selected' : ''; ?>>Staff Karyawan (Lvl 1)</option>
                              </select>
                           </div>
                        </div>
                     </div>
                 </div>
             </div>

             <!-- BAGIAN DATA PRIBADI & PEKERJAAN -->
             <div class="panel panel-default" style="border-radius: 8px; margin-bottom: 25px;">
                 <div class="panel-heading" style="background: #f4f6f9; padding: 12px 15px;">
                     <h4 style="margin: 0; font-size: 16px; font-weight: bold; color: #333;">
                         <i class="fa fa-user text-success"></i> Data Informasi Karyawan
                     </h4>
                 </div>
                  <div class="panel-body" style="padding: 20px;">
                     <div class="row">
                        <!-- KOLOM KIRI: DATA PRIBADI -->
                        <div class="col-md-6">
                           <h5 style="margin-bottom: 15px; font-weight: bold; color: #555; border-bottom: 1px solid #eee; padding-bottom: 10px;">Profil Pribadi</h5>
                           <div class="form-group">
                              <label>Nama Asli Karyawan (Sesuai KTP) <span class="text-danger">*</span></label>
                              <div class="input-group">
                                 <span class="input-group-addon"><i class="fa fa-user"></i></span>
                                 <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap" value="<?= _ent($hr_karyawan->nama_lengkap); ?>" placeholder="Nama lengkap karyawan" required>
                              </div>
                           </div>
                           <div class="form-group">
                              <label>NIP <span class="text-danger">*</span></label>
                              <div class="input-group">
                                 <span class="input-group-addon"><i class="fa fa-id-card"></i></span>
                                 <input type="text" class="form-control" name="nik" id="nik" value="<?= _ent($hr_karyawan->nik); ?>" placeholder="Nomor Induk Pegawai (NIP)" required>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <div class="input-group">
                                       <span class="input-group-addon"><i class="fa fa-venus-mars"></i></span>
                                       <select class="form-control" name="jenis_kelamin" id="jenis_kelamin">
                                          <option value="">- Pilih -</option>
                                          <option value="L" <?= $hr_karyawan->jenis_kelamin == 'L' ? 'selected' : '' ?>>Laki-Laki</option>
                                          <option value="P" <?= $hr_karyawan->jenis_kelamin == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                       </select>
                                    </div>
                                 </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <div class="input-group">
                                       <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                       <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" value="<?= _ent($hr_karyawan->tanggal_lahir); ?>">
                                    </div>
                                 </div>
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Nomor Telepon / WA</label>
                              <div class="input-group">
                                 <span class="input-group-addon"><i class="fa fa-whatsapp"></i></span>
                                 <input type="text" class="form-control" name="no_telepon" id="no_telepon" value="<?= _ent($hr_karyawan->no_telepon); ?>" placeholder="Contoh: 08123456789">
                              </div>
                           </div>
                           <div class="form-group">
                              <label>Alamat Domisili</label>
                              <div class="input-group">
                                 <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                                 <textarea class="form-control" name="alamat" id="alamat" rows="2" placeholder="Alamat lengkap tempat tinggal"><?= _ent($hr_karyawan->alamat); ?></textarea>
                              </div>
                           </div>
                        </div>

                        <!-- KOLOM KANAN: DATA PEKERJAAN -->
                        <div class="col-md-6">
                           <h5 style="margin-bottom: 15px; font-weight: bold; color: #555; border-bottom: 1px solid #eee; padding-bottom: 10px;">Penempatan & Pekerjaan</h5>
                           <div class="form-group">
                              <label>Jabatan Karyawan <span class="text-danger">*</span></label>
                              <div class="input-group">
                                 <span class="input-group-addon"><i class="fa fa-briefcase"></i></span>
                                 <input list="list_jabatan" class="form-control" name="jabatan" id="jabatan" value="<?= _ent($hr_karyawan->jabatan); ?>" placeholder="- Pilih atau Ketik Jabatan (Contoh: Manager, Supervisor, Staff) -" required autocomplete="off">
                              </div>
                              <datalist id="list_jabatan">
                                 <option value="Manager / Atasan Utama">
                                 <option value="Manager Pembelian / Procurement">
                                 <option value="Manager Operasional / Warehouse">
                                 <option value="Manager Keuangan">
                                 <option value="Manager HRD & Personalia">
                                 <option value="Supervisor Pembelian">
                                 <option value="Supervisor Gudang">
                                 <option value="Staff Pembelian / Procurement">
                                 <option value="Staff Gudang & Warehouse">
                                 <option value="Staff Karyawan">
                              </datalist>
                           </div>
                     <div class="form-group">
                        <label>Grup Departemen <span class="text-danger">*</span></label>
                        <?php $dept_current = $hr_karyawan->departemen; ?>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-sitemap"></i></span>
                           <input list="list_departemen" class="form-control" name="departemen" id="departemen" value="<?= _ent($dept_current); ?>" placeholder="- Pilih atau Ketik Departemen -" required autocomplete="off">
                        </div>
                        <datalist id="list_departemen">
                           <option value="IT & Teknologi">
                           <option value="HRD & Personalia">
                           <option value="Marketing & Sales">
                           <option value="Keuangan & Akuntansi">
                           <option value="Operasional">
                           <option value="Gudang & Logistik">
                           <option value="Pembelian & Procurement">
                           <option value="Produksi & Manufaktur">
                           <option value="General & Umum">
                           <option value="Fullstack Developer">
                        </datalist>
                     </div>
                     <div class="row">
                        <div class="col-md-6">
                           <div class="form-group">
                              <label>Tanggal Bergabung</label>
                              <div class="input-group">
                                 <span class="input-group-addon"><i class="fa fa-calendar-check-o"></i></span>
                                 <input type="date" class="form-control" name="tanggal_bergabung" id="tanggal_bergabung" value="<?= _ent($hr_karyawan->tanggal_bergabung); ?>">
                              </div>
                           </div>
                        </div>
                        <div class="col-md-6">
                           <div class="form-group">
                              <label>Jam Masuk Standar <i class="text-danger">*</i></label>
                              <div class="input-group">
                                 <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                 <input type="time" class="form-control" name="jam_masuk_standar" id="jam_masuk_standar" value="<?= _ent($hr_karyawan->jam_masuk_standar ?? '08:00'); ?>" required>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="form-group">
                        <label>Tipe Kerja</label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-laptop"></i></span>
                           <select class="form-control" name="tipe_kerja" id="tipe_kerja">
                              <option value="WFO" <?= $hr_karyawan->tipe_kerja=="WFO"?"selected":""; ?>>WFO - Absen di Kantor</option>
                              <option value="WFH" <?= $hr_karyawan->tipe_kerja=="WFH"?"selected":""; ?>>WFH - Bebas Lokasi</option>
                              <option value="WFA" <?= $hr_karyawan->tipe_kerja=="WFA"?"selected":""; ?>>WFA - Bebas Lokasi</option>
                           </select>
                        </div>
                     </div>
                     <div class="form-group">
                        <label>Jadwal Shift <i class="text-danger">*</i></label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-exchange"></i></span>
                           <select class="form-control" name="shift_id" id="shift_id">
                              <option value="">- Pilih Jadwal Shift -</option>
                              <?php foreach (db_get_all_data('hr_shift') as $row): ?>
                              <option value="<?= $row->id ?>" <?= $hr_karyawan->shift_id == $row->id ? 'selected' : ''; ?>><?= $row->nama_shift; ?> (<?= substr($row->jam_masuk,0,5) ?> - <?= substr($row->jam_pulang,0,5) ?>)</option>
                              <?php endforeach; ?>  
                           </select>
                        </div>
                     </div>
                     <div class="form-group">
                        <label>Penempatan Kantor (Cabang) <i class="text-danger">*</i></label>
                        <div class="input-group">
                           <span class="input-group-addon"><i class="fa fa-building"></i></span>
                           <select class="form-control" name="kantor_id" id="kantor_id">
                              <?php foreach (db_get_all_data('hr_kantor') as $row): ?>
                              <option value="<?= $row->id ?>" <?= $hr_karyawan->kantor_id == $row->id ? 'selected' : ''; ?>><?= $row->nama_kantor; ?> (Radius: <?= $row->radius_meter ?>m)</option>
                              <?php endforeach; ?>  
                           </select>
                        </div>
                     </div>
                        </div> <!-- end col-md-6 -->
                     </div> <!-- end row -->
                  </div>
             </div>



             <button type="submit" class="btn btn-primary btn-lg" style="border-radius:6px; padding:8px 25px;"><i class="fa fa-save"></i> Perbarui Data Karyawan</button>
             <a href="<?= site_url('administrator/hr_karyawan'); ?>" class="btn btn-default btn-lg" style="border-radius:6px; padding:8px 20px;"><i class="fa fa-undo"></i> Batal</a>
          </form>
      </div>
   </div>
</section>
<script>
$(document).ready(function(){
   
   // Formatter uang
   $('.currency-input').on('keyup', function(){
      var val = $(this).val().replace(/[^0-9]/g, '');
      if(val != ''){
         val = parseInt(val, 10).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
         $(this).val(val);
      } else {
         $(this).val('');
      }
   });

   $("#form_karyawan").submit(function(e){
      e.preventDefault();
      var btn = $(this).find('button[type="submit"]');
      btn.prop('disabled', true);



      var data_post = {
        email: $('#email').val(),
        username: $('#username').val(),
        password: $('#password').val(),
        group_id: $('#group_id').val(),
        nama_lengkap: $('#nama_lengkap').val(),
        jenis_kelamin: $('#jenis_kelamin').val(),
        tanggal_lahir: $('#tanggal_lahir').val(),
        alamat: $('#alamat').val(),
        no_telepon: $('#no_telepon').val(),
        tanggal_bergabung: $('#tanggal_bergabung').val(),
        nik: $('#nik').val(),
        jabatan: $('#jabatan').val(),
        departemen: $('#departemen').val(),
        tipe_kerja: $('#tipe_kerja').val(),
        shift_id: $('#shift_id').val(),
        kantor_id: $('#kantor_id').val()
      };
      $.ajax({
         url: $(this).attr('action'),
         type: 'POST',
         data: data_post,
         dataType: 'json',
         success: function(res){
            if(res.success){
               window.location.href = res.redirect;
            }else{
               alert(res.message);
               btn.prop('disabled', false);
            }
         }
      });
   });


});
</script>
