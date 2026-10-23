
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
        Invitation Events        <small>Edit Invitation Events</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class=""><a  href="<?= site_url('administrator/invitation_events'); ?>">Invitation Events</a></li>
        <li class="active">Edit</li>
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
                            <h3 class="widget-user-username">Invitation Events</h3>
                            <h5 class="widget-user-desc">Edit Invitation Events</h5>
                            <hr>
                        </div>
                        <?= form_open(base_url('administrator/invitation_events/edit_save/'.$this->uri->segment(4)), [
                            'name'    => 'form_invitation_events', 
                            'class'   => 'form-horizontal', 
                            'id'      => 'form_invitation_events', 
                            'method'  => 'POST'
                            ]); ?>
                         
                                                <div class="form-group ">
                            <label for="invitation_id" class="col-sm-2 control-label">Invitation Id 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="invitation_id" id="invitation_id" placeholder="Invitation Id" value="<?= set_value('invitation_id', $invitation_events->invitation_id); ?>">
                                <small class="info help-block">
                                <b>Input Invitation Id</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="event_name" class="col-sm-2 control-label">Event Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="event_name" id="event_name" placeholder="Event Name" value="<?= set_value('event_name', $invitation_events->event_name); ?>">
                                <small class="info help-block">
                                <b>Input Event Name</b> Max Length : 150.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="event_date" class="col-sm-2 control-label">Event Date 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right datepicker" name="event_date"  placeholder="Event Date" id="event_date" value="<?= set_value('invitation_events_event_date_name', $invitation_events->event_date); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                       
                                                 
                                                <div class="form-group ">
                            <label for="time_start" class="col-sm-2 control-label">Time Start 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="time_start" id="time_start" value="<?= set_value('invitation_events_time_start_name', $invitation_events->time_start); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="time_end" class="col-sm-2 control-label">Time End 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-6">
                            <div class="input-group date col-sm-8">
                              <input type="text" class="form-control pull-right timepicker" name="time_end" id="time_end" value="<?= set_value('invitation_events_time_end_name', $invitation_events->time_end); ?>">
                            </div>
                            <small class="info help-block">
                            </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="timezone" class="col-sm-2 control-label">Timezone 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="timezone" id="timezone" placeholder="Timezone" value="<?= set_value('timezone', $invitation_events->timezone); ?>">
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="venue_name" class="col-sm-2 control-label">Venue Name 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="venue_name" id="venue_name" placeholder="Venue Name" value="<?= set_value('venue_name', $invitation_events->venue_name); ?>">
                                <small class="info help-block">
                                <b>Input Venue Name</b> Max Length : 200.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="address" class="col-sm-2 control-label">Address 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="address" name="address" rows="10" cols="80"> <?= set_value('address', $invitation_events->address); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="latitude" class="col-sm-2 control-label">Latitude 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="latitude" id="latitude" placeholder="Latitude" value="<?= set_value('latitude', $invitation_events->latitude); ?>">
                                <small class="info help-block">
                                <b>Input Latitude</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="longitude" class="col-sm-2 control-label">Longitude 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="longitude" id="longitude" placeholder="Longitude" value="<?= set_value('longitude', $invitation_events->longitude); ?>">
                                <small class="info help-block">
                                <b>Input Longitude</b> Max Length : 10.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="google_maps_url" class="col-sm-2 control-label">Google Maps Url 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <textarea id="google_maps_url" name="google_maps_url" rows="10" cols="80"> <?= set_value('google_maps_url', $invitation_events->google_maps_url); ?></textarea>
                                <small class="info help-block">
                                </small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="live_streaming_url" class="col-sm-2 control-label">Live Streaming Url 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="live_streaming_url" id="live_streaming_url" placeholder="Live Streaming Url" value="<?= set_value('live_streaming_url', $invitation_events->live_streaming_url); ?>">
                                <small class="info help-block">
                                <b>Input Live Streaming Url</b> Max Length : 255.</small>
                            </div>
                        </div>
                                                 
                                                <div class="form-group ">
                            <label for="sort_order" class="col-sm-2 control-label">Sort Order 
                            <i class="required">*</i>
                            </label>
                            <div class="col-sm-8">
                                <input type="number" class="form-control" name="sort_order" id="sort_order" placeholder="Sort Order" value="<?= set_value('sort_order', $invitation_events->sort_order); ?>">
                                <small class="info help-block">
                                <b>Input Sort Order</b> Max Length : 11.</small>
                            </div>
                        </div>
                                                
                        <hr style="margin: 30px 0 25px 0; border-top: 1px solid #e9ecef;">
                        <div class="message"></div>
                        <div class="row-fluid col-md-7">
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
<script src="<?= BASE_ASSET; ?>ckeditor/ckeditor.js"></script>
<!-- Page script -->
<script>
    $(document).ready(function(){
      
      CKEDITOR.replace('address'); 
      var address = CKEDITOR.instances.address;
            CKEDITOR.replace('google_maps_url'); 
      var google_maps_url = CKEDITOR.instances.google_maps_url;
                   
      $('#btn_cancel').click(function(){
        swal({
            title: "Are you sure?",
            text: "the data that you have created will be in the exhaust!",
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
              window.location.href = BASE_URL + 'administrator/invitation_events';
            }
          });
    
        return false;
      }); /*end btn cancel*/
    
      $('.btn_save').click(function(){
        $('.message').fadeOut();
        $('#address').val(address.getData());
                $('#google_maps_url').val(google_maps_url.getData());
                    
        var form_invitation_events = $('#form_invitation_events');
        var data_post = form_invitation_events.serializeArray();
        var save_type = $(this).attr('data-stype');
        data_post.push({name: 'save_type', value: save_type});
    
        $('.loading').show();
    
        $.ajax({
          url: form_invitation_events.attr('action'),
          type: 'POST',
          dataType: 'json',
          data: data_post,
        })
        .done(function(res) {
          if(res.success) {
            var id = $('#invitation_events_image_galery').find('li').attr('qq-file-id');
            if (save_type == 'back') {
              window.location.href = res.redirect;
              return;
            }
    
            $('.message').printMessage({message : res.message});
            $('.message').fadeIn();
            $('.data_file_uuid').val('');
    
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