<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="<?= get_option('site_description'); ?>">
  <meta name="keywords" content="<?= get_option('keywords'); ?>">
  <meta name="author" content="<?= get_option('author'); ?>">

  <title><?= ($fg = get_user_first_group()) && isset($fg->name) ? $fg->name : 'User' ?> | <?= get_option('site_name'); ?> | <?= isset($template['title']) ? $template['title'] : ''; ?></title>
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/dist/css/AdminLTE.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/dist/css/skins/_all-skins.min.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/iCheck/flat/blue.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/morris/morris.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/jvectormap/jquery-jvectormap-1.2.2.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/datepicker/datepicker3.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/daterangepicker/daterangepicker.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>admin-lte/plugins/iCheck/all.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>sweet-alert/sweetalert.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>toastr/build/toastr.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>fancy-box/source/jquery.fancybox.css?v=2.1.5" media="screen" />
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>chosen/chosen.css">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>css/custom.css?v=<?= VERSION; ?>">
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>datetimepicker/jquery.datetimepicker.css"/>
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>js-scroll/style/jquery.jscrollpane.css" rel="stylesheet" media="all" />
  <link rel="stylesheet" href="<?= BASE_ASSET; ?>flag-icon/css/flag-icon.css" rel="stylesheet" media="all" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <?= $this->cc_html->getCssFileTop(); ?>

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <script src="<?= BASE_ASSET; ?>admin-lte/plugins/jQuery/jquery-2.2.3.min.js"></script>
  <script src="<?= BASE_ASSET; ?>admin-lte/plugins/iCheck/icheck.min.js"></script>
  <script src="<?= BASE_ASSET; ?>sweet-alert/sweetalert-dev.js"></script>
  <script src="<?= BASE_ASSET; ?>admin-lte/plugins/input-mask/jquery.inputmask.js"></script>
  <script src="<?= BASE_ASSET; ?>admin-lte/plugins/input-mask/jquery.inputmask.date.extensions.js"></script>
  <script src="<?= BASE_ASSET; ?>admin-lte/plugins/input-mask/jquery.inputmask.extensions.js"></script>
  <script src="<?= BASE_ASSET; ?>toastr/toastr.js"></script>
  <script src="<?= BASE_ASSET; ?>fancy-box/source/jquery.fancybox.js?v=2.1.5"></script>
  <script src="<?= BASE_ASSET; ?>datetimepicker/build/jquery.datetimepicker.full.js"></script>
  <script src="<?= BASE_ASSET; ?>editor/dist/js/medium-editor.js"></script>
  <script src="<?= BASE_ASSET; ?>js/cc-extension.js"></script>
  <script src="<?= BASE_ASSET; ?>js/cc-page-element.js"></script>
  <script>
    var BASE_URL = "<?= base_url(); ?>";
    var HTTP_REFERER = "<?= isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/' ; ?>";
    var csrf = '<?= $this->security->get_csrf_token_name(); ?>';
    var token = '<?= $this->security->get_csrf_hash(); ?>';

    function show_alert(message, type) {
      var alertType = type || 'info';
      if (alertType === 'error') alertType = 'warning';
      if (typeof toastr !== 'undefined' && typeof toastr[alertType] === 'function') {
        toastr[alertType](message);
      } else if (typeof swal === 'function') {
        swal('Pemberitahuan', message, alertType);
      } else {
        alert(message);
      }
    }

    function set_notif_message(message, type) {
      show_alert(message, type);
    }

    $(document).ready(function(){

      // Inject CSRF token into every AJAX POST request automatically
      $.ajaxSetup({
        data: { [csrf]: token },
        beforeSend: function(xhr, settings) {
          var reqType = (settings.type || settings.method || 'GET').toUpperCase();
          if (reqType === 'POST' && typeof settings.data === 'string' && settings.data.indexOf(csrf) === -1) {
            settings.data += '&' + csrf + '=' + token;
          }
        }
      });

      toastr.options = {
        "positionClass": "toast-top-center",
      }

      var f_message = '<?= str_replace("'", "\'", $this->session->flashdata('f_message') ?? ''); ?>';
      var f_type = '<?= $this->session->flashdata('f_type'); ?>';
      <?php 
      // Force clear flashdata to prevent it from persisting across pages due to session race conditions
      $this->session->unset_userdata('f_message'); 
      $this->session->unset_userdata('f_type'); 
      ?>

      if (f_message.length > 0) {
        toastr[f_type](f_message);
      }

      $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass: 'iradio_minimal-red'
      });
    });
  </script>
  <?= $this->cc_html->getScriptFileTop(); ?>
</head>
<body class="sidebar-mini skin-black fixed web-body">
<div class="wrapper">

  <header class="main-header">

     <?php 
      $logo =  get_option('logo');
      if ($logo) {
        $logo = 'uploads/setting/'. get_option('logo');
      } else {
        $logo = 'asset/img/icon-wide.png';
      }

      if (!is_file(FCPATH .'/'. $logo)) {
        $logo = 'asset/img/icon-wide.png';
      }
      ?>

    <a href="<?= site_url('/'); ?>" class="logo">
      <span class="logo-mini"><b><img src="<?= BASE_ASSET ?>img/icon-small.png" style="max-height: 40px; width: auto;"></b></span>
      <span class="logo-lg"><b><img src="<?= base_url($logo) ?>" style="max-height: 40px; width: auto;"></b></span>
    </a>
    <nav class="navbar navbar-static-top">

      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <?php if ($this->aauth->get_user()): ?>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">

          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu" id="notif-approval-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="Notifikasi">
              <i class="fa fa-bell-o" style="font-size: 16px;"></i>
              <span class="label label-warning" id="notif-approval-badge" style="display:none; position: absolute; top: 9px; right: 7px; text-align: center; font-size: 9px; padding: 2px 5px; border-radius: 10px;">0</span>
            </a>
            <ul class="dropdown-menu" style="width: 320px; border-radius: 6px; box-shadow: 0 5px 25px rgba(0,0,0,0.15); border: none;">
              <li class="header" style="background: #f8f9fa; padding: 12px 15px; font-weight: 700; color: #2c3e50; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fa fa-bell text-yellow" style="margin-right: 5px;"></i> <span id="notif-header-title">Notifikasi</span></span>
                <span class="badge bg-yellow" id="notif-header-count">0</span>
              </li>
              <li>
                <ul class="menu" id="notif-approval-list" style="max-height: 280px; overflow-y: auto; padding: 0; margin: 0; list-style: none;">
                  <li style="text-align: center; padding: 20px; color: #95a5a6;"><i class="fa fa-spin fa-spinner"></i> Memuat notifikasi...</li>
                </ul>
              </li>
              <li class="footer" style="background: #f8f9fa; text-align: center; padding: 8px; border-top: 1px solid #eee;">
                <a href="<?= site_url('administrator/purchase_order'); ?>" id="notif-footer-link" style="color: #3498db; font-weight: 600; font-size: 12px;"><i class="fa fa-list"></i> Lihat Selengkapnya</a>
              </li>
            </ul>
          </li>

          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="<?= BASE_URL.'uploads/user/'.(!empty(get_user_data('avatar')) ? get_user_data('avatar') :'default.png'); ?>" class="user-image" alt="User Image" onerror="this.onerror=null; this.src='<?= BASE_ASSET; ?>admin-lte/dist/img/avatar.png';">
              <span class="hidden-xs"><?= _ent(ucwords(clean_snake_case(get_user_data('full_name')))); ?></span>
            </a>
            <ul class="dropdown-menu">
              <li class="user-header">
                <img src="<?= BASE_URL.'uploads/user/'.(!empty(get_user_data('avatar')) ? get_user_data('avatar') :'default.png'); ?>" class="img-circle" alt="User Image" onerror="this.onerror=null; this.src='<?= BASE_ASSET; ?>admin-lte/dist/img/avatar.png';">

                <p>
                  <?= _ent(ucwords(clean_snake_case($this->aauth->get_user()->full_name))); ?> 
                  <small>Last Login, <?= date('Y-M-D', strtotime(get_user_data('last_login'))); ?></small>
                </p>
              </li>
              
              <li class="user-footer">
                <div class="pull-left">
                  <a href="<?= site_url('administrator/user/profile'); ?>" class="btn btn-default btn-flat"><?= cclang('profile'); ?></a>
                </div>
                <div class="pull-right">
                  <a href="<?= site_url('administrator/auth/logout'); ?>" class="btn btn-default btn-flat"><?= cclang('sign_out'); ?></a>
                </div>
              </li>
            </ul>
          </li>
          <li class="dropdown ">
             <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
             <span class="flag-icon <?= get_current_initial_lang(); ?>"></span> <?= get_current_lang(); ?> </a>
             <ul class="dropdown-menu" role="menu">
             <?php foreach (get_langs() as $lang): ?>
                <li><a href="<?= site_url('web/switch_lang/'.$lang['folder_name']); ?>"><span class="flag-icon <?= $lang['icon_name']; ?>"></span> <?= $lang['name']; ?></a></li>
              <?php endforeach; ?>
             </ul>
          </li>
        </ul>
      </div>
      <?php endif ?>

    </nav>
  </header>
  <aside class="main-sidebar">

    <section class="sidebar" style="padding-top:0% !important">
      <ul class="sidebar-menu sidebar-admin tree" data-widget="tree" style="padding-bottom: 80px !important;">
        
        <?= display_menu_admin(0, 1); ?>
      </ul>
    </section>

  </aside>

  <div class="content-wrapper">
    <?php cicool()->eventListen('backend_content_top'); ?>
    <?= $template['partials']['content']; ?>
    <?php cicool()->eventListen('backend_content_bottom'); ?>

    <div class="modal fade  " id="modalPopUp">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"> <span class="popup-title"></span></h4>
          </div>
          <div class="modal-body">
           
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
$(document).ready(function(){
   // Global Automatic Thousands Separator Formatter (Indonesian Dot Format)
   $(document).on('keyup input', '.rupiah, .money-format', function(e) {
      if (e.which === 37 || e.which === 39) return;
      var rawVal = $(this).val().replace(/[^0-9]/g, '');
      if (rawVal.length > 0) {
         var formatted = parseInt(rawVal, 10).toLocaleString('id-ID');
         $(this).val(formatted);
      } else {
         $(this).val('');
      }
   });

   // Global Qty Integer Whole Number Enforcer (Always whole numbers min 1 for physical goods)
   $(document).on('blur change input', '.qty-input', function() {
      var raw = $(this).val();
      if (raw !== '') {
         var val = parseFloat(raw);
         if (isNaN(val) || val < 1) {
            $(this).val(1);
         } else if (val % 1 !== 0) {
            $(this).val(Math.round(val));
         }
      }
   });

   // Approval Notification Loader
   function loadApprovalNotifications() {
      $.ajax({
         url: BASE_URL + 'administrator/dashboard/get_notifications',
         type: 'GET',
         dataType: 'json',
         success: function(res) {
            if (res && res.success) {
               if (res.header_title) {
                  $('#notif-header-title').text(res.header_title);
               }
               if (res.footer_url && res.footer_label) {
                  $('#notif-footer-link').attr('href', res.footer_url).html('<i class="fa fa-list"></i> ' + res.footer_label);
               }

               var count = res.total_count || 0;
               if (count > 0) {
                  $('#notif-approval-badge').text(count > 99 ? '99+' : count).show();
                  $('#notif-header-count').text(count);
               } else {
                  $('#notif-approval-badge').hide();
                  $('#notif-header-count').text('0');
               }

               var html = '';
               if (res.items && res.items.length > 0) {
                  $.each(res.items, function(i, item) {
                     html += '<li style="border-bottom: 1px solid #f4f4f4;">';
                     html += '  <a href="' + item.url + '" style="display: flex; align-items: flex-start; padding: 10px 15px; color: #333; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.background=\'#f9f9f9\'" onmouseout="this.style.background=\'transparent\'">';
                     html += '    <div style="margin-right: 12px; font-size: 18px; line-height: 1;" class="' + item.icon_bg + '"><i class="fa ' + item.icon + '"></i></div>';
                     html += '    <div style="flex: 1; overflow: hidden;">';
                     html += '      <div style="font-weight: 600; font-size: 12px; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; color: #2c3e50;">' + item.title + '</div>';
                     html += '      <div style="font-size: 11px; color: #7f8c8d; margin-top: 2px;">' + item.subtitle + '</div>';
                     html += '    </div>';
                     html += '    <div style="font-size: 10px; color: #bdc3c7; margin-left: 8px; white-space: nowrap;">' + item.date + '</div>';
                     html += '  </a>';
                     html += '</li>';
                  });
               } else {
                  html = '<li style="text-align: center; padding: 25px 15px; color: #7f8c8d; font-size: 13px;"><i class="fa fa-check-circle-o text-green" style="font-size: 24px; display: block; margin-bottom: 5px;"></i> Tidak ada notifikasi baru</li>';
               }
               $('#notif-approval-list').html(html);
            }
         }
      });
   }

   if ($('#notif-approval-menu').length > 0) {
      loadApprovalNotifications();
      setInterval(loadApprovalNotifications, 30000);
   }
});
</script>
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b><?= cclang('version') ?></b> <?= VERSION ?>
    </div>
    <strong>Copyright &copy; 2020 -<?=date('Y'); ?> <a href="#"><?= get_option('site_name'); ?></a>.</strong> All rights
    reserved.
  </footer>
  
  <div class="control-sidebar-bg"></div>
</div>

<?= $this->cc_html->getHtmlFileBottom(); ?>

<?= $this->cc_html->getCssFileBottom(); ?>
<?= $this->cc_html->getScriptFileBottom(); ?>
<script>
        var AdminLTEOptions = {
          sidebarExpandOnHover: false,
           navbarMenuSlimscroll: false,
        };
      </script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.6.2/chosen.jquery.min.js" type="text/javascript"></script>
<script src="<?= BASE_ASSET; ?>jquery-ui/jquery-ui.js"></script>
<script src="<?= BASE_ASSET; ?>jquery-switch-button/jquery.switchButton.js"></script>
<script src="<?= BASE_ASSET; ?>js/jquery.ui.touch-punch.js"></script>
<script src="<?= BASE_ASSET; ?>admin-lte/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= BASE_ASSET; ?>admin-lte/plugins/slimScroll/jquery.slimscroll.min.js"></script>
<script src="<?= BASE_ASSET; ?>admin-lte/plugins/fastclick/fastclick.js"></script>
<script src="<?= BASE_ASSET; ?>admin-lte/dist/js/app.min.js"></script>
<script src="<?= BASE_ASSET; ?>js-scroll/script/jquery.jscrollpane.min.js"></script>
<script src="<?= BASE_ASSET; ?>js/custom.js"></script>
</body>
</html>
