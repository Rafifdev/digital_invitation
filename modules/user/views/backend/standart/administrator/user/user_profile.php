<?php /* user_profile.php - Sleek High-End User Profile Layout */ ?>
<style>
.profile-cover {
   background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e3a8a 100%);
   height: 140px;
   border-top-left-radius: 8px;
   border-top-right-radius: 8px;
   position: relative;
   padding: 20px;
}
.profile-avatar-wrapper {
   position: absolute;
   bottom: -40px;
   left: 30px;
}
.profile-avatar-img {
   width: 90px;
   height: 90px;
   border-radius: 50%;
   border: 4px solid #ffffff;
   box-shadow: 0 4px 12px rgba(0,0,0,0.25);
   object-fit: cover;
   background: #fff;
}
.profile-header-info {
   margin-left: 130px;
   padding-top: 15px;
}
.profile-title {
   font-size: 22px;
   font-weight: 700;
   color: #ffffff;
   margin: 0;
   letter-spacing: 0.5px;
}
.profile-subtitle {
   font-size: 13px;
   color: #94a3b8;
   margin-top: 2px;
}
.profile-card-body {
   background: #ffffff;
   border-bottom-left-radius: 8px;
   border-bottom-right-radius: 8px;
   padding: 50px 25px 25px 25px;
   border: 1px solid #e2e8f0;
   border-top: none;
   box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}
.info-label {
   font-size: 12px;
   text-transform: uppercase;
   color: #64748b;
   font-weight: 600;
   margin-bottom: 2px;
}
.info-value {
   font-size: 14px;
   color: #1e293b;
   font-weight: 600;
}
</style>

<section class="content-header">
   <h1>Profil Pengguna <small><?= _ent(ucwords($user->full_name)); ?></small></h1>
   <ol class="breadcrumb">
      <li><a href="<?= base_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
      <li><a href="<?= site_url('administrator/user'); ?>">User</a></li>
      <li class="active">Profil</li>
   </ol>
</section>

<section class="content">
   <div class="row">
      <div class="col-md-8 col-md-offset-2">
         <div style="border-radius:8px; overflow:hidden;">
            <!-- Profile Banner -->
            <div class="profile-cover">
               <div class="profile-avatar-wrapper">
                  <img class="profile-avatar-img" src="<?= BASE_URL.'uploads/user/'.(!empty($user->avatar) ? $user->avatar :'default.png'); ?>" alt="User Avatar">
               </div>
               <div class="profile-header-info">
                  <h2 class="profile-title"><?= _ent(ucwords($user->full_name)); ?></h2>
                  <div class="profile-subtitle"><i class="fa fa-user-circle"></i> @<?= _ent($user->username); ?> | <span class="badge bg-blue"><?= _ent($user->email ?? 'Developer Account'); ?></span></div>
               </div>
            </div>

            <!-- Profile Body -->
            <div class="profile-card-body">
               <div class="row">
                  <!-- Group User Roles -->
                  <div class="col-md-5" style="border-right: 1px solid #f1f5f9; padding-right: 20px;">
                     <h4 style="margin-top:0; font-weight:700; color:#334155;"><i class="fa fa-shield text-primary"></i> Hak Akses &amp; Peran</h4>
                     <ul class="nav nav-pills nav-stacked" style="margin-top:15px;">
                        <?php foreach($this->aauth->get_user_groups() as $row): ?>
                           <li style="margin-bottom:5px;">
                              <a href="javascript:void(0);" style="background:#f8fafc; border:1px solid #e2e8f0; color:#334155; font-weight:600; border-radius:6px;">
                                 <i class="fa fa-check-circle text-success"></i> <?= _ent($row->name); ?>
                              </a>
                           </li>
                        <?php endforeach; ?>
                     </ul>
                  </div>

                  <!-- Detail Stats & Metadata -->
                  <div class="col-md-7" style="padding-left: 25px;">
                     <h4 style="margin-top:0; font-weight:700; color:#334155;"><i class="fa fa-info-circle text-info"></i> Informasi Detail Akun</h4>
                     
                     <table class="table table-hover table-striped" style="margin-top:15px; font-size:13px;">
                        <tr>
                           <th width="40%"><i class="fa fa-toggle-on text-success"></i> Status Akun</th>
                           <td>
                              <?php if ($user->banned) :?>
                                 <span class="label label-danger">Banned / Nonaktif</span>
                              <?php else: ?>
                                 <span class="label label-success" style="font-size:11px;">Aktif</span>
                              <?php endif; ?>
                           </td>
                        </tr>
                        <tr>
                           <th><i class="fa fa-history text-warning"></i> Log Masuk Terakhir</th>
                           <td><strong><?= date('d M Y, H:i', strtotime($user->last_login)); ?></strong></td>
                        </tr>
                        <tr>
                           <th><i class="fa fa-clock-o text-info"></i> Aktivitas Terakhir</th>
                           <td><?= date('d M Y, H:i', strtotime($user->last_activity)); ?></td>
                        </tr>
                        <tr>
                           <th><i class="fa fa-calendar-check-o text-primary"></i> Tanggal Dibuat</th>
                           <td><?= date('d M Y', strtotime($user->date_created)); ?></td>
                        </tr>
                        <tr>
                           <th><i class="fa fa-desktop text-muted"></i> Alamat IP</th>
                           <td><code style="font-size:12px;"><?= _ent($user->ip_address); ?></code></td>
                        </tr>
                     </table>
                  </div>
               </div>

               <hr style="border-top:1px solid #f1f5f9; margin:20px 0 15px 0;">

               <div class="row">
                  <div class="col-md-12 text-right">
                     <?php is_allowed('user_update_profile', function() use ($user){?>
                        <a class="btn btn-warning btn-flat" id="btn_edit" style="border-radius:4px; font-weight:600;" href="<?= site_url('administrator/user/edit_profile/'.$user->id); ?>">
                           <i class="fa fa-edit"></i> Edit Profil Pengguna
                        </a>
                     <?php }) ?>
                     <a href="<?= base_url('administrator/dashboard'); ?>" class="btn btn-default btn-flat" style="border-radius:4px;"><i class="fa fa-arrow-left"></i> Kembali ke Dashboard</a>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>
