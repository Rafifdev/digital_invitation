<section class="content-header">
    <h1>Edit Manual Payroll <small style="font-weight: 400; color: #777;"><?= _ent($payroll->nama_lengkap) ?></small></h1>
    <ol class="breadcrumb" style="background: transparent;">
        <li><a href="<?= site_url('administrator/dashboard'); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('administrator/hr_payroll'); ?>">Rekap Payroll</a></li>
        <li class="active">Edit Manual</li>
    </ol>
</section>

<style>
    .modern-box { border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-top: 4px solid #f39c12; background: #fff; margin-bottom: 25px; }
    .modern-box-header { padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; background: #fdfbf7; border-radius: 8px 8px 0 0; }
    .modern-box-title { margin: 0; font-size: 18px; font-weight: 600; color: #b06d09; }
    
    .form-group label { font-weight: 600; color: #444; }
    .form-control { border-radius: 6px; box-shadow: none; border-color: #ddd; }
    .form-control:focus { border-color: #f39c12; box-shadow: 0 0 0 3px rgba(243, 156, 18, 0.1); }
    
    .summary-box { background: #f8f9fa; border-radius: 8px; padding: 20px; border: 1px solid #eee; }
    .summary-item { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 15px; }
    .summary-item.total { border-top: 2px dashed #ddd; padding-top: 10px; font-weight: 700; font-size: 18px; margin-top: 15px; }
    .text-plus { color: #00a65a; }
    .text-minus { color: #dd4b39; }
    .btn-modern { border-radius: 20px; padding: 8px 25px; font-weight: 600; transition: all 0.3s; }
</style>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box modern-box">
                <div class="modern-box-header">
                    <h3 class="modern-box-title"><i class="fa fa-pencil margin-r-5"></i> Form Penyesuaian Nilai Gaji - Periode <?= $payroll->periode_bulan ?></h3>
                    <div class="box-tools">
                        <a href="<?= site_url('administrator/hr_payroll?periode=' . $payroll->periode_bulan) ?>" class="btn btn-default btn-sm btn-modern"><i class="fa fa-arrow-left"></i> Kembali</a>
                    </div>
                </div>
                
                <div class="box-body" style="padding: 25px;">
                    <div class="row">
                        <div class="col-md-8">
                            <form id="form_edit_payroll" action="<?= site_url('administrator/hr_payroll/edit_save/'.$payroll->id) ?>" method="POST">
                                <h4 style="border-bottom: 1px solid #eee; padding-bottom:10px; margin-bottom:20px; color:#333;">A. Komponen Penghasilan</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Gaji Pokok Diterima</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="border-radius: 6px 0 0 6px;">Rp</span>
                                                <input type="text" name="gaji_pokok_diterima" class="form-control rupiah hitung-ulang" value="<?= number_format((float)$payroll->gaji_pokok_diterima, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Uang Lembur (Manual)</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="border-radius: 6px 0 0 6px;">Rp</span>
                                                <input type="text" name="uang_lembur" class="form-control rupiah hitung-ulang text-plus" value="<?= number_format((float)$payroll->uang_lembur, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tunjangan Tetap Diterima</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="border-radius: 6px 0 0 6px;">Rp</span>
                                                <input type="text" name="tunj_tetap_diterima" class="form-control rupiah hitung-ulang" value="<?= number_format((float)$payroll->tunj_tetap_diterima, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tunjangan Kehadiran Diterima</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="border-radius: 6px 0 0 6px;">Rp</span>
                                                <input type="text" name="tunj_kehadiran_diterima" class="form-control rupiah hitung-ulang" value="<?= number_format((float)$payroll->tunj_kehadiran_diterima, 0, ',', '.') ?>">
                                            </div>
                                            <small class="text-muted">Untuk karyawan WFH/WFA, biasanya Rp 0.</small>
                                        </div>
                                    </div>
                                </div>

                                <h4 style="border-bottom: 1px solid #eee; padding-bottom:10px; margin-top:30px; margin-bottom:20px; color:#333;">B. Komponen Potongan</h4>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Potongan Alfa / Mangkir</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="border-radius: 6px 0 0 6px;">Rp</span>
                                                <input type="text" name="potongan_alfa" class="form-control rupiah hitung-ulang text-minus" value="<?= number_format((float)$payroll->potongan_alfa, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Potongan Telat (Denda)</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="border-radius: 6px 0 0 6px;">Rp</span>
                                                <input type="text" name="potongan_terlambat" class="form-control rupiah hitung-ulang text-minus" value="<?= number_format((float)$payroll->potongan_terlambat, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Potongan Sakit/Izin Hangus</label>
                                            <div class="input-group">
                                                <span class="input-group-addon" style="border-radius: 6px 0 0 6px;">Rp</span>
                                                <input type="text" name="potongan_izin_sakit" class="form-control rupiah hitung-ulang text-minus" value="<?= number_format((float)$payroll->potongan_izin_sakit, 0, ',', '.') ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 30px;">
                                    <button type="button" id="btn-save" class="btn btn-warning btn-modern"><i class="fa fa-save margin-r-5"></i> Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="summary-box">
                                <h4 style="margin-top:0; border-bottom:1px solid #ddd; padding-bottom:10px;"><i class="fa fa-calculator margin-r-5"></i> Preview Kalkulasi</h4>
                                
                                <div class="summary-item">
                                    <span class="text-muted">Gaji Pokok:</span>
                                    <strong id="lbl-gapok">Rp 0</strong>
                                </div>
                                <div class="summary-item">
                                    <span class="text-muted">Tunjangan:</span>
                                    <strong id="lbl-tunjangan">Rp 0</strong>
                                </div>
                                <div class="summary-item text-plus">
                                    <span>Lembur:</span>
                                    <strong id="lbl-lembur">Rp 0</strong>
                                </div>
                                <div class="summary-item" style="border-top: 1px solid #ddd; padding-top: 5px;">
                                    <strong>Gaji Kotor:</strong>
                                    <strong id="lbl-gaji-kotor" class="text-primary">Rp 0</strong>
                                </div>
                                
                                <br>
                                
                                <div class="summary-item text-minus">
                                    <span>Potongan Alfa:</span>
                                    <strong id="lbl-pot-alfa">Rp 0</strong>
                                </div>
                                <div class="summary-item text-minus">
                                    <span>Potongan Telat:</span>
                                    <strong id="lbl-pot-telat">Rp 0</strong>
                                </div>
                                <div class="summary-item text-minus">
                                    <span>Potongan Izin:</span>
                                    <strong id="lbl-pot-izin">Rp 0</strong>
                                </div>
                                <div class="summary-item" style="border-top: 1px solid #ddd; padding-top: 5px;">
                                    <strong>Total Potongan:</strong>
                                    <strong id="lbl-total-potongan" class="text-danger">Rp 0</strong>
                                </div>
                                
                                <div class="summary-item total">
                                    <span>Take Home Pay:</span>
                                    <span id="lbl-thp" class="text-success" style="font-size:22px;">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
$(document).ready(function(){
    function parseRupiah(val) {
        if(!val) return 0;
        return parseInt(val.toString().replace(/[^0-9]/g, '')) || 0;
    }
    
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function kalkulasi_ulang() {
        var gapok = parseRupiah($('input[name="gaji_pokok_diterima"]').val());
        var tunj_tetap = parseRupiah($('input[name="tunj_tetap_diterima"]').val());
        var tunj_kehadiran = parseRupiah($('input[name="tunj_kehadiran_diterima"]').val());
        var lembur = parseRupiah($('input[name="uang_lembur"]').val());
        
        var pot_alfa = parseRupiah($('input[name="potongan_alfa"]').val());
        var pot_telat = parseRupiah($('input[name="potongan_terlambat"]').val());
        var pot_izin = parseRupiah($('input[name="potongan_izin_sakit"]').val());
        
        var tunjangan = tunj_tetap + tunj_kehadiran;
        var gaji_kotor = gapok + tunjangan + lembur;
        var total_pot = pot_alfa + pot_telat + pot_izin;
        var thp = gaji_kotor - total_pot;
        if(thp < 0) thp = 0;
        
        $('#lbl-gapok').text(formatRupiah(gapok));
        $('#lbl-tunjangan').text(formatRupiah(tunjangan));
        $('#lbl-lembur').text(formatRupiah(lembur));
        $('#lbl-gaji-kotor').text(formatRupiah(gaji_kotor));
        
        $('#lbl-pot-alfa').text('- ' + formatRupiah(pot_alfa));
        $('#lbl-pot-telat').text('- ' + formatRupiah(pot_telat));
        $('#lbl-pot-izin').text('- ' + formatRupiah(pot_izin));
        $('#lbl-total-potongan').text('- ' + formatRupiah(total_pot));
        
        $('#lbl-thp').text(formatRupiah(thp));
    }

    $('.hitung-ulang').on('keyup input change', function() {
        kalkulasi_ulang();
    });
    
    // Init first calculation
    kalkulasi_ulang();

    $("#btn-save").click(function(e){
        e.preventDefault();
        var form = $("#form_edit_payroll");
        var btn = $(this);
        var originalHtml = btn.html();

        swal({
            title: "Simpan Perubahan?",
            text: "Pastikan nominal yang diubah sudah benar. Data akan tersimpan sebagai Draft.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#f39c12",
            confirmButtonText: "Ya, Simpan!",
            cancelButtonText: "Batal",
            closeOnConfirm: false
        }, function() {
            btn.html('<i class="fa fa-spinner fa-spin margin-r-5"></i> Menyimpan...').prop('disabled', true);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(res){
                    if(res.success){
                        swal.close();
                        window.location.href = res.redirect;
                    } else {
                        swal("Gagal Menyimpan", res.message, "error");
                        btn.html(originalHtml).prop('disabled', false);
                    }
                },
                error: function(){
                    swal("Error Server", "Terjadi kesalahan pada koneksi server.", "error");
                    btn.html(originalHtml).prop('disabled', false);
                }
            });
        });
    });
});
</script>
