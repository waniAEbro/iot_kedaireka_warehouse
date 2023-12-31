<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="row" id="form_pembelian">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Master pvc</h3>
                <div class="box-tools pull-right">
                    <a class="btn btn-primary" onclick="cetakExcel()">Cetak</a>
                    <?php
                    echo button('load_silent("master/pvc/form/base","#modal")', 'Add', 'btn btn-info', 'data-toggle="tooltip" title="Add"');
                    ?>
                </div>
            </div>
            <div class="box-body">
                <table width="100%" id="tableku" class="table table-striped">
                    <thead>
                        <th width="5%">No</th>
                        <th>Item code</th>
                        <th>Deskripsi</th>
                        <th>Satuan</th>
                        <th>Supplier</th>
                        <th>Lead Time</th>
                        <th>Act</th>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($pvc->result() as $row) : ?>
                            <tr id="output_data_<?= $row->id ?>" class="output_data">
                                <td align="center"><?= $i++ ?></td>
                                <td><?= $row->item_code ?></td>
                                <td><?= $row->deskripsi ?></td>
                                <td><?= $row->satuan ?></td>
                                <td><?= $row->supplier ?></td>
                                <td><?= $row->lead_time ?></td>
                                <td align="center">
                                    <?php
                                    
                                    echo button('load_silent("master/pvc/form/sub/' . $row->id . '","#modal")', 'Edit', 'btn btn-xs btn-info', 'data-toggle="tooltip" title="Edit"');
                                    ?>
                                    <a class="btn btn-xs btn-danger" href="javascript:void(0)" onClick="hapus(<?= $row->id ?>)">
                                        hapus
                                    </a>
                                    <a target="_blank" href="<?= base_url('master/pvc/cetak_barcode/' . $row->id); ?>" class="btn btn-xs btn-primary">Cetak Barcode</a>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#tableku').DataTable({
            "ordering": true,
            "scrollX": true,
        });
    });

    function cetakExcel() {
        var url = "<?= site_url('master/pvc/cetakExcel') ?>";
        window.open(url, "_blank");
    }

    function hapus(i) {
        if (confirm('Lanjutkan Proses Hapus?')) {
            $.ajax({
                    type: "POST",
                    url: "<?= site_url('master/pvc/delete') ?>",
                    dataType: 'json',
                    data: {
                        'id': i
                    }
                })
                .success(function(datasaved) {
                    $.growl.notice({
                        title: 'Sukses',
                        message: datasaved.msg
                    });
                    $('#output_data_' + i).remove();
                });
        }
    }
</script>