<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>

<script type="text/javascript">
function domo(){
   $('*').bind('keydown', 'Ctrl+a', function assets() {
       window.location.href = BASE_URL + '/administrator/Quotes/add';
       return false;
   });
   $('*').bind('keydown', 'Ctrl+f', function assets() {
       $('#sbtn').trigger('click');
       return false;
   });
   $('*').bind('keydown', 'Ctrl+x', function assets() {
       $('#reset').trigger('click');
       return false;
   });
   $('*').bind('keydown', 'Ctrl+b', function assets() {
       $('#reset').trigger('click');
       return false;
   });
}
jQuery(document).ready(domo);
</script>

<!-- Content Header (Page header) -->
<section class="content-header">
   <h1>Master Kutipan & Ayat <small>Daftar Kutipan & Ayat Suci Undangan</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Kutipan & Ayat</li>
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
                     <div class="row pull-right">
                        <?php is_allowed('quotes_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', ['Kutipan']); ?>  (Ctrl+a)" href="<?= site_url('administrator/quotes/add'); ?>"><i class="fa fa-plus-square-o"></i> <?= cclang('add_new_button', ['Kutipan']); ?></a>
                        <?php }) ?>
                        <?php is_allowed('quotes_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> XLS" href="<?= site_url('administrator/quotes/export'); ?>"><i class="fa fa-file-excel-o"></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('quotes_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> PDF" href="<?= site_url('administrator/quotes/export_pdf'); ?>"><i class="fa fa-file-pdf-o"></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <h3 class="widget-user-username">Kutipan & Ayat Suci</h3>
                     <h5 class="widget-user-desc">Daftar Kutipan & Ayat Suci Undangan <i class="label bg-yellow"><?= $quotes_counts; ?> Data</i></h5>
                  </div>

                  <form name="form_quotes" id="form_quotes" action="<?= base_url('administrator/quotes/index'); ?>">

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                           <th width="5">
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th width="90">Kategori</th>
                           <th width="140">Sumber / Ayat</th>
                           <th width="220">Teks Asli / Lafadz Arab</th>
                           <th>Isi Terjemahan / Kutipan</th>
                           <th width="75">Status</th>
                           <th style="min-width: 120px;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_quotes">
                     <?php foreach($quotess as $quotes): ?>
                        <tr>
                           <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $quotes->id; ?>">
                           </td>
                           <td><span class="label bg-blue"><?= _ent($quotes->category); ?></span></td> 
                           <td><strong><?= _ent($quotes->source); ?></strong></td> 
                           <td style="direction: rtl; text-align: right; font-size: 1.1em; line-height: 1.4;">
                              <?= !empty($quotes->content_original) ? mb_strimwidth(_ent($quotes->content_original), 0, 70, '...') : '<span class="text-muted" style="direction: ltr; font-size: 0.9em;">-</span>'; ?>
                           </td>
                           <td><?= mb_strimwidth(_ent($quotes->content), 0, 90, '...'); ?></td> 
                           <td><?= $quotes->is_active ? '<span class="label bg-green">Aktif</span>' : '<span class="label bg-red">Nonaktif</span>'; ?></td> 
                           <td>
                              <?php is_allowed('quotes_view', function() use ($quotes){?>
                                 <a href="<?= site_url('administrator/quotes/view/' . $quotes->id); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('quotes_update', function() use ($quotes){?>
                                 <a href="<?= site_url('administrator/quotes/edit/' . $quotes->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('quotes_delete', function() use ($quotes){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/quotes/delete/' . $quotes->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                      <?php if ($quotes_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Data Kutipan & Ayat belum tersedia
                           </td>
                         </tr>
                      <?php endif; ?>
                     </tbody>
                  </table>
                  </div>
               </div>
               <hr>
               <!-- /.widget-user -->
               <div class="row">
                  <div class="col-md-8">
                     <div class="col-sm-2 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="bulk" id="bulk" placeholder="Bulk" >
                           <option value="">Bulk</option>
                           <option value="delete">Delete</option>
                        </select>
                     </div>
                     <div class="col-sm-2 padd-left-0 ">
                        <button type="button" class="btn btn-flat" name="apply" id="apply" title="<?= cclang('apply_bulk_action'); ?>"><?= cclang('apply_button'); ?></button>
                     </div>
                     <div class="col-sm-3 padd-left-0  " >
                        <input type="text" class="form-control" name="q" id="filter" placeholder="<?= cclang('filter'); ?>" value="<?= $this->input->get('q'); ?>">
                     </div>
                     <div class="col-sm-3 padd-left-0 " >
                        <select type="text" class="form-control chosen chosen-select" name="f" id="field" >
                           <option value=""><?= cclang('all'); ?></option>
                           <option <?= $this->input->get('f') == 'category' ? 'selected' :''; ?> value="category">Kategori</option>
                           <option <?= $this->input->get('f') == 'source' ? 'selected' :''; ?> value="source">Sumber / Ayat</option>
                           <option <?= $this->input->get('f') == 'content_original' ? 'selected' :''; ?> value="content_original">Teks Asli</option>
                           <option <?= $this->input->get('f') == 'content' ? 'selected' :''; ?> value="content">Isi Kutipan</option>
                        </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/quotes');?>" title="<?= cclang('reset_filter'); ?>">
                        <i class="fa fa-undo"></i>
                        </a>
                     </div>
                  </div>
                  </form>
                  <div class="col-md-4">
                     <div class="dataTables_paginate paging_simple_numbers pull-right" id="example2_paginate" >
                        <?= $pagination; ?>
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

<!-- Page script -->
<script>
  $(document).ready(function(){
   
    $('.remove-data').click(function(){

      var url = $(this).attr('data-href');

      swal({
          title: "<?= cclang('are_you_sure'); ?>",
          text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
          cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
          closeOnConfirm: true,
          closeOnCancel: true
        },
        function(isConfirm){
          if (isConfirm) {
            document.location.href = url;            
          }
        });

      return false;
    });

    $('#apply').click(function(){

      var bulk = $('#bulk');
      var serialize_bulk = $('#form_quotes').serialize();

      if (bulk.val() == 'delete') {
         swal({
            title: "<?= cclang('are_you_sure'); ?>",
            text: "<?= cclang('data_to_be_deleted_can_not_be_restored'); ?>",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "<?= cclang('yes_delete_it'); ?>",
            cancelButtonText: "<?= cclang('no_cancel_plx'); ?>",
            closeOnConfirm: true,
            closeOnCancel: true
          },
          function(isConfirm){
            if (isConfirm) {
               document.location.href = BASE_URL + '/administrator/quotes/delete?' + serialize_bulk;      
            }
          });

        return false;

      } else if(bulk.val() == '')  {
          swal({
            title: "Upss",
            text: "<?= cclang('please_choose_bulk_action_first'); ?>",
            type: "warning",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Okay!",
            closeOnConfirm: true,
            closeOnCancel: true
          });

        return false;
      }

      return false;

    });/*end apply click*/

    //check all
    var checkAll = $('#check_all');
    var checkboxes = $('input.check');

    checkAll.on('ifChecked ifUnchecked', function(event) {   
        if (event.type == 'ifChecked') {
            checkboxes.iCheck('check');
        } else {
            checkboxes.iCheck('uncheck');
        }
    });

    checkboxes.on('ifChanged', function(event){
        if(checkboxes.filter(':checked').length == checkboxes.length) {
            checkAll.prop('checked', 'checked');
        } else {
            checkAll.removeProp('checked');
        }
        checkAll.iCheck('update');
    });

  }); /*end doc ready*/
</script>