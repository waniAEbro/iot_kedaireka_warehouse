<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<style>
    td.details-control {
        background: url("<?= base_url('assets/img/details_open.png') ?>") no-repeat center center;
        cursor: pointer;
    }

    tr.shown {
        background: #FCFF43;
    }

    tr.shown td.details-control {
        background: url("<?= base_url('assets/img/details_close.png') ?>") no-repeat center center;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Monitoring aluminium</h3>

                <div class="box-tools pull-right">
                    <?php
                    $sesi = from_session('level');
                    if ($sesi == '1' || $sesi == '2') {
                        //echo button('load_silent("wrh/aluminium/formAdd","#content")', 'Input Stock', 'btn btn-success');
                    } else {
                        # code...
                    }
                    ?>
                    <input type="button" target="_blank" class="btn btn-default" onclick="printDiv('printableArea')" value="Print Page" />
                </div>
            </div>
            <div class="box-body" id="printableArea">
                <style type="text/css" media="screen">
                    .large-table-container-3 {
                        /*max-width: 800px;*/
                        overflow-x: scroll;
                        overflow-y: auto;
                    }

                    .large-table-container-3 table {}

                    .large-table-fake-top-scroll-container-3 {
                        /*max-width: 800px;*/
                        overflow-x: scroll;
                        overflow-y: auto;
                    }

                    .large-table-fake-top-scroll-container-3 div {
                        background-color: red;
                        font-size: 1px;
                        line-height: 1px;
                    }

                    /*misc*/
                    td {
                        border: 1px solid gray;
                    }
                </style>
                <div class="large-table-fake-top-scroll-container-3">
                    <div>&nbsp;</div>
                </div>
                <div class="large-table-container-3">
                    <table width="100%" id="tableku" class="table table-striped">
                        <thead>
                            <th width="5%"></th>
                            <th width="5%">No</th>
                            <th>Section ATA</th>
                            <th>Section Allure</th>
                            <th>Temper</th>
                            <th>Kode Warna</th>
                            <th>Ukuran</th>
                            <th>Stock Awal Bulan</th>
                            <th>Total In Per Bulan</th>
                            <th>Total Out Per Bulan</th>
                            <th>Stock Akhir Bulan</th>
                            <th>Free Stock</th>
                            <th>Fungsi</th>
                            <th>OTS Persiapan</th>
                            <th>Fitur</th>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($aluminium->result() as $row) :
                                $ada = 1;
                                $qtyTotalIn = @$total_in[$row->section_ata];
                                $qtyTotalOut = @$total_out[$row->section_ata];
                                $qtyTotalBom = @$total_bom[$row->section_ata];
                                $qtySelisihBomOut = $qtyTotalBom - $qtyTotalOut;

                                if ($qtySelisihBomOut < 1) {
                                    $qtydikurangi = $qtyTotalBom;
                                } else {
                                    $qtydikurangi = $qtyTotalOut;
                                }

                                $stock_akhir_bulan = $qtyTotalIn - $qtyTotalOut;
                                $ots = $qtyTotalBom - $qtydikurangi;
                            ?>
                                <tr>
                                    <td class="details-control" id="<?= $i ?>"><input type="hidden" id="id_<?= $i ?>" value="<?= $row->section_ata ?>"></td>
                                    <td align="center"><?= $i ?></td>
                                    <td><?= $row->section_ata ?></td>
                                    <td><?= $row->section_allure ?></td>
                                    <td><?= $row->temper ?></td>
                                    <td><?= $row->kode_warna ?></td>
                                    <td><?= $row->ukuran ?></td>
                                    <td><?= $row->stock_awal_bulan ?></td>
                                    <td><?= $qtyTotalIn ?></td>
                                    <td><?= $qtyTotalOut ?></td>
                                    <td><?= $stock_akhir_bulan ?></td>
                                    <td><?= $stock_akhir_bulan - $ots ?></td>
                                    <td></td>
                                    <td><?= $ots ?></td>
                                    <td>
                                        <?= button('load_silent("wrh/aluminium/mutasi_stock_add/' . $row->section_ata . '","#content")', 'mutasi', 'btn btn-xs btn-primary', 'data-toggle="tooltip" title="Mutasi"'); ?>
                                        <?= button('load_silent("wrh/aluminium/mutasi_stock_history/' . $row->section_ata . '","#content")', 'history mutasi', 'btn btn-xs btn-default', 'data-toggle="tooltip" title="History Mutasi"'); ?></td>
                                </tr>

                            <?php $i++;
                            endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        var tableContainer = $(".large-table-container-3");
        var table = $(".large-table-container-3 table");
        var fakeContainer = $(".large-table-fake-top-scroll-container-3");
        var fakeDiv = $(".large-table-fake-top-scroll-container-3 div");

        var tableWidth = table.width();
        fakeDiv.width(tableWidth);

        fakeContainer.scroll(function() {
            tableContainer.scrollLeft(fakeContainer.scrollLeft());
        });
        tableContainer.scroll(function() {
            fakeContainer.scrollLeft(tableContainer.scrollLeft());
        });
    })

    function printDiv(divName) {
        // var printContents = document.getElementById(divName).innerHTML;
        // var originalContents = document.body.innerHTML;

        // document.body.innerHTML = printContents;
        // window.print();

        // document.body.innerHTML = originalContents;

        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        mywindow.document.write('<html><head><title>' + document.title + '</title>');
        mywindow.document.write('</head><body >');
        mywindow.document.write('<h1>' + document.title + '</h1>');
        mywindow.document.write(document.getElementById(divName).innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
    // function cetak (id) {
    //   var win = window.open("<?php echo base_url('wrh/aluminium/cetak/'); ?>/"+id, "_blank");
    //             if (win) {
    //                 //Browser has allowed it to be opened
    //                 win.focus();
    //             } else {
    //                 //Browser has blocked it
    //                 alert('Please allow popups for this website');
    //             }
    // }

    function setFilter() {
        var store = $('#store').val();
        if (store != '') {
            var id_store = store;
        } else {
            var id_store = 'x';
        };
        var bulan = $('#bulan').val();
        if (bulan != '') {
            var id_bulan = bulan;
        } else {
            var id_bulan = 'x';
        };
        var tahun = $('#tahun').val();
        if (tahun != '') {
            var id_tahun = tahun;
        } else {
            var id_tahun = 'x';
        };
        var status = $('#status').val();
        var jne = $('#jne').val();
        load_silent("wrh/aluminium/filter/" + id_store + "/" + id_bulan + "/" + id_tahun + "/" + status + "/" + jne + "/", "#content");

    }

    function cetakExcel() {
        var left = (screen.width / 2) - (640 / 2);
        var top = (screen.height / 2) - (480 / 2);
        var store = $('#store').val();
        if (store != '') {
            var id_store = store;
        } else {
            var id_store = 'x';
        };
        var bulan = $('#bulan').val();
        if (bulan != '') {
            var id_bulan = bulan;
        } else {
            var id_bulan = 'x';
        };
        var tahun = $('#tahun').val();
        if (tahun != '') {
            var id_tahun = tahun;
        } else {
            var id_tahun = 'x';
        };
        var status = $('#status').val();
        var url = "<?= site_url('wrh/aluminium/excel/"+id_store+"/"+id_bulan+"/"+id_tahun+"/"+status+"') ?>";
        window.open(url, "", "width=640, height=480, scrollbars=yes, left=" + left + ", top=" + top);
    }

    $(document).ready(function() {
        $("select").select2();
        var table = $('#tableku').DataTable({
            "ordering": true,
            // "scrollX": true,
        });


        $('#tableku tbody').on('click', 'td.details-control', function(e) {
            var tr = $(this).closest('tr');
            var td = $(this).closest('td');
            var row = table.row(tr);
            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                dataRow = format(td[0].id, row, tr);
            }
        });

        function format(id, row, tr) {

            infoTable = '<table id="infoTable" class="table table-striped" border="1px" style="font-size: smaller;">' +
                '<tr>' +
                '<th bgcolor="#bfbfbf">No</th>' +
                '<th bgcolor="#bfbfbf">Divisi</th>' +
                '<th bgcolor="#bfbfbf">Gudang</th>' +
                '<th bgcolor="#bfbfbf">Keranjang</th>' +
                '<th bgcolor="#bfbfbf">Stock Awal Bulan</th>' +
                '<th bgcolor="#bfbfbf">Total In Per Bulan</th>' +
                '<th bgcolor="#bfbfbf">Total Out Per Bulan</th>' +
                '<th bgcolor="#bfbfbf">Stock Akhir Bulan</th>' +
                '<th bgcolor="#bfbfbf">Rata2 Pemakaian</th>' +
                '<th bgcolor="#bfbfbf">Min Stock</th>' +
                '</tr>';
            $.ajax({
                    url: "<?= site_url('wrh/aluminium/getDetailTabel') ?>",
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        id: $('#id_' + id).val(),
                    },
                })
                .done(function(data) {
                    for (var i = 0; i < data.detail.length; i++) {
                        var no = i + 1;
                        var color = "white";
                        var fontcolor = "black";
                        if (data.detail[i].tot_out == null) {
                            var qty_out = 0;
                        } else {
                            var qty_out = data.detail[i].tot_out;
                        }

                        infoTable += '<tr bgcolor="' + color + '">' +
                            '<td><font color="' + fontcolor + '">' + no + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + data.detail[i].divisi + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + data.detail[i].gudang + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + data.detail[i].keranjang + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + data.detail[i].stok_awal_bulan + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + data.detail[i].tot_in + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + qty_out + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + data.detail[i].stok_akhir_bulan + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + data.detail[i].rata_pemakaian + '</font></td>' +
                            '<td><font color="' + fontcolor + '">' + data.detail[i].min_stock + '</font></td>' +
                            '</tr>';

                    };

                    infoTable += '</table>';
                    row.child(infoTable).show();
                    tr.addClass('shown');
                })
                .fail(function() {
                    console.log("error");
                })
                .always(function() {
                    // console.log("complete");
                });

            return infoTable;
        }
    });
</script>