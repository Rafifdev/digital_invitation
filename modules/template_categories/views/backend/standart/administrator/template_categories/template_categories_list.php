<script src="<?= BASE_ASSET; ?>/js/jquery.hotkeys.js"></script>

<script type="text/javascript">
function domo(){
   $('*').bind('keydown', 'Ctrl+a', function assets() {
       window.location.href = BASE_URL + '/administrator/Template_categories/add';
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
   <h1>Kategori Template <small>Daftar Kategori Tema Undangan</small></h1>
   <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Kategori Template</li>
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
                        <?php is_allowed('template_categories_add', function(){?>
                        <a class="btn btn-flat btn-success btn_add_new" id="btn_add_new" title="<?= cclang('add_new_button', [cclang('template_categories')]); ?>  (Ctrl+a)" href="<?=  site_url('administrator/template_categories/add'); ?>"><i class="fa fa-plus-square-o" ></i> <?= cclang('add_new_button', [cclang('template_categories')]); ?></a>
                        <?php }) ?>
                        <?php is_allowed('template_categories_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> XLS" href="<?= site_url('administrator/template_categories/export'); ?>"><i class="fa fa-file-excel-o" ></i> <?= cclang('export'); ?> XLS</a>
                        <?php }) ?>
                        <?php is_allowed('template_categories_export', function(){?>
                        <a class="btn btn-flat btn-success" title="<?= cclang('export'); ?> PDF" href="<?= site_url('administrator/template_categories/export_pdf'); ?>"><i class="fa fa-file-pdf-o" ></i> <?= cclang('export'); ?> PDF</a>
                        <?php }) ?>
                     </div>
                     <div class="widget-user-image">
                        <img class="img-circle" src="<?= BASE_ASSET; ?>/img/list.png" alt="User Avatar">
                     </div>
                     <h3 class="widget-user-username">Kategori Template</h3>
                     <h5 class="widget-user-desc">Daftar Kategori Tema Undangan <i class="label bg-yellow"><?= $template_categories_counts; ?> Data</i></h5>
                  </div>

                  <form name="form_template_categories" id="form_template_categories" action="<?= base_url('administrator/template_categories/index'); ?>">

                  <div class="table-responsive"> 
                  <table class="table table-bordered table-striped dataTable">
                     <thead>
                        <tr class="">
                           <th width="5">
                            <input type="checkbox" class="flat-red toltip" id="check_all" name="check_all" title="check all">
                           </th>
                           <th>Nama Kategori</th>
                           <th>Slug</th>
                           <th>Deskripsi</th>
                           <th>Dibuat</th>
                           <th style="min-width: 120px;">Aksi</th>
                        </tr>
                     </thead>
                     <tbody id="tbody_template_categories">
                     <?php foreach($template_categoriess as $template_categories): ?>
                        <tr>
                           <td width="5">
                              <input type="checkbox" class="flat-red check" name="id[]" value="<?= $template_categories->id; ?>">
                           </td>
                           <td><strong><?= _ent($template_categories->name); ?></strong></td> 
                           <td><code><?= _ent($template_categories->slug); ?></code></td> 
                           <td><?= !empty($template_categories->description) ? _ent($template_categories->description) : '<span class="text-muted">-</span>'; ?></td> 
                           <td><?= !empty($template_categories->created_at) ? date('d M Y', strtotime($template_categories->created_at)) : '-'; ?></td> 
                           <td>
                              <?php is_allowed('template_categories_view', function() use ($template_categories){?>
                                 <a href="<?= site_url('administrator/template_categories/view/' . $template_categories->id); ?>" class="label-default"><i class="fa fa-newspaper-o"></i> <?= cclang('view_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('template_categories_update', function() use ($template_categories){?>
                                 <a href="<?= site_url('administrator/template_categories/edit/' . $template_categories->id); ?>" class="label-default"><i class="fa fa-edit "></i> <?= cclang('update_button'); ?></a>
                              <?php }) ?>
                              <?php is_allowed('template_categories_delete', function() use ($template_categories){?>
                                 <a href="javascript:void(0);" data-href="<?= site_url('administrator/template_categories/delete/' . $template_categories->id); ?>" class="label-default remove-data"><i class="fa fa-close"></i> <?= cclang('remove_button'); ?></a>
                              <?php }) ?>
                           </td>
                        </tr>
                     <?php endforeach; ?>
                      <?php if ($template_categories_counts == 0) :?>
                         <tr>
                           <td colspan="100">
                           Data Kategori Template belum tersedia
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
                           <option <?= $this->input->get('f') == 'name' ? 'selected' :''; ?> value="name">Nama Kategori</option>
                           <option <?= $this->input->get('f') == 'slug' ? 'selected' :''; ?> value="slug">Slug</option>
                           <option <?= $this->input->get('f') == 'description' ? 'selected' :''; ?> value="description">Deskripsi</option>
                        </select>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <button type="submit" class="btn btn-flat" name="sbtn" id="sbtn" value="Apply" title="<?= cclang('filter_search'); ?>">
                        Filter
                        </button>
                     </div>
                     <div class="col-sm-1 padd-left-0 ">
                        <a class="btn btn-default btn-flat" name="reset" id="reset" value="Apply" href="<?= base_url('administrator/template_categories');?>" title="<?= cclang('reset_filter'); ?>">
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
         </div>
      </div>
   </div>
</section>

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
      var serialize_bulk = $('#form_template_categories').serialize();
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
               document.location.href = BASE_URL + '/administrator/template_categories/delete?' + serialize_bulk;      
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
    });

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
  });
</script>