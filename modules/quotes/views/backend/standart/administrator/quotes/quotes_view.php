<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>
<script type="text/javascript">
function domo(){
   $('*').bind('keydown', 'Ctrl+e', function assets() {
      $('#btn_edit').trigger('click');
       return false;
   });
   $('*').bind('keydown', 'Ctrl+x', function assets() {
      $('#btn_back').trigger('click');
       return false;
   });
}
jQuery(document).ready(domo);
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Master Kutipan & Ayat <small>Detail Kutipan</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class=""><a href="<?= site_url('administrator/quotes'); ?>">Kutipan & Ayat</a></li>
      <li class="active"><?= cclang('detail'); ?></li>
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
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/view.png" alt="User Avatar">
                     </div>
                     <h3 class="widget-user-username">Kutipan & Ayat Suci</h3>
                     <h5 class="widget-user-desc">Detail Informasi Kutipan</h5>
                     <hr>
                  </div>

                  <div class="form-horizontal" name="form_quotes" id="form_quotes" >
                   
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">ID Kutipan </label>
                        <div class="col-sm-8">
                           <?= _ent($quotes->id); ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Kategori / Agama </label>
                        <div class="col-sm-8">
                           <span class="label bg-blue"><?= _ent($quotes->category); ?></span>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Sumber / Ayat </label>
                        <div class="col-sm-8">
                           <strong><?= _ent($quotes->source); ?></strong>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Teks Asli / Lafadz Arab </label>
                        <div class="col-sm-8">
                           <?= !empty($quotes->content_original) ? '<p style="font-size: 1.3em; line-height: 1.8; direction: rtl; text-align: right; background: #fafafa; padding: 12px; border-radius: 4px; border: 1px solid #eee;">' . nl2br(_ent($quotes->content_original)) . '</p>' : '<span class="text-muted">(Tidak ada teks asli)</span>'; ?>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Isi Terjemahan / Kutipan </label>
                        <div class="col-sm-8">
                           <p style="font-size: 1.05em; line-height: 1.6; font-style: italic; background: #fafafa; padding: 12px; border-radius: 4px; border: 1px solid #eee;">
                              "<?= nl2br(_ent($quotes->content)); ?>"
                           </p>
                        </div>
                    </div>
                                         
                    <div class="form-group ">
                        <label for="content" class="col-sm-2 control-label">Status </label>
                        <div class="col-sm-8">
                           <?= $quotes->is_active ? '<span class="label bg-green">Aktif</span>' : '<span class="label bg-red">Nonaktif</span>'; ?>
                        </div>
                    </div>
                                        
                    <br>
                    <br>

                    <div class="view-nav">
                        <?php is_allowed('quotes_update', function() use ($quotes){?>
                        <a class="btn btn-flat btn-info btn_edit btn_action" id="btn_edit" data-stype='back' title="edit quotes (Ctrl+e)" href="<?= site_url('administrator/quotes/edit/'.$quotes->id); ?>"><i class="fa fa-edit" ></i> <?= cclang('update', ['Kutipan']); ?> </a>
                        <?php }) ?>
                        <a class="btn btn-flat btn-default btn_action" id="btn_back" title="back (Ctrl+x)" href="<?= site_url('administrator/quotes/'); ?>"><i class="fa fa-undo" ></i> <?= cclang('go_list_button', ['Kutipan']); ?></a>
                     </div>
                    
                  </div>
               </div>
            </div>
            <!--/box body -->
         </div>
         <!--/box -->
      </div>
   </div>
</section>
<!-- /.content -->