<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row">
    <div class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">List BOM Aluminium MF</h3>
                <div class="box-tools pull-right">
                    <?php //echo button('load_silent("klg/fppp","#content")', 'Kembali', 'btn btn-success'); 
                    ?>
                </div>
            </div>
            <div class="box-body">
                <form method="post" class="form-vertical form_faktur" role="form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>No FPPP</label>
                                <input type="hidden" class="form-control" id="id_fppp" value="<?= $id_fppp ?>" readonly>
                                <input type="text" class="form-control" id="no_fppp" value="<?= $rowFppp->no_fppp ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Proyek</label>
                                <input type="text" class="form-control" id="nama_proyek" value="<?= $rowFppp->nama_proyek ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Alamat Proyek</label>
                                <input type="text" class="form-control" id="alamat_proyek" value="<?= $rowFppp->alamat_proyek ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nama Sales</label>
                                <input type="text" class="form-control" id="sales" value="<?= $rowFppp->sales ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Deadline Pengiriman</label>
                                <input type="text" class="form-control" id="deadline_pengiriman" value="<?= $rowFppp->deadline_pengiriman ?>" readonly>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-footer">
                <!-- <button type="submit" id="update" onclick="update()" id="proses" class="btn btn-success">Update</button> -->
            </div>
            <div class="box-footer">
                <table width="100%" id="tableku" class="table">
                    <thead>
                        <th width="5%">No</th>
                        <th>Section ATA</th>
                        <th>Section Allure</th>
                        <th>Temper</th>
                        <th>Kode Warna</th>
                        <th>Ukuran</th>
                        <th>Keterangan</th>
                        <th>Qty BOM</th>
                        <th>Kekurangan</th>
                        <th>Qty Gudang</th>
                        <th>Qty Aktual</th>
                        <th>Area Gudang</th>
                        <th>Keranjang</th>
                        <th>Produksi</th>
                        <th>Lapangan</th>
                        <th>created</th>
                        <th>updated</th>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($list_bom->result() as $row) :
                            $gudang = $this->m_aluminium->getGudangBomItem($id_jenis_item, $row->id_item);
                            $keranjang = $this->m_aluminium->getKeranjangBomItem($id_jenis_item, $row->id_item);


                            $qtyTotalOut = $this->m_aluminium->getQtyOutFppp($row->id_fppp, $row->id_item);
                            $id_gudang_stock = $this->m_aluminium->getQtyTerbanyakStockGudang($row->id_item);
                            $keranjang_stock = $this->m_aluminium->getQtyTerbanyakStockKeranjang($row->id_item);
                            $qty_stock = $this->m_aluminium->getQtyTerbanyakStockQtyMf($row->id_item);

                            $qtyBOM = $row->qty_bom;
                            if ($row->set_parsial == 0) {
                                $kurang = $qtyBOM - $row->qty_out;
                            } else {
                                $kurang = $qtyBOM - $row->qty_out;
                            }
                            $cekproduksi = ($row->produksi == 1) ? 'checked' : '';
                            $ceklapangan = ($row->lapangan == 1) ? 'checked' : '';
                            $qty_gudang = $qty_stock;
                            $totalgudang = $qty_gudang;

                            $qty_aktual = $row->qty_out;

                            $bgrow = ($qty_gudang == 0) ? "#ffb6a3" : "";
                            if ($qty_gudang < $qtyBOM) {
                                $this->m_aluminium->updateIsKurang($row->id_stock);
                                // $this->m_aluminium->updatekeMf($row->id_stock, $id_fppp);
                            }

                            if ($row->id_surat_jalan == 0) {
                        ?>
                                <tr bgcolor="<?= $bgrow ?>">
                                    <td align="center"><?= $i++ ?></td>
                                    <td><?= $row->section_ata ?>
                                        <br><?php echo button_confirm("Anda yakin mengirim parsial item " . $row->section_ata . "-" . $row->section_allure . "?", "wrh_h/aluminium/kirim_parsial/" . $id_fppp . "/" . $row->id_stock, "#content", 'Kirim Parsial', 'btn btn-xs btn-default', 'data-toggle="tooltip" title="Kirim Parsial"'); ?>
                                        <br><?php //echo button_confirm("Anda yakin membuat di MF item " . $row->section_ata . "-" . $row->section_allure . "?", "wrh_h/aluminium/buat_mf/" . $id_fppp . "/" . $row->id_stock, "#content", 'Buat di MF', 'btn btn-xs btn-info', 'data-toggle="tooltip" title="Buat di MF"'); 
                                            ?>
                                        <?php if ($row->is_parsial == 1) {
                                            echo button_confirm("Anda yakin mengirim parsial item " . $row->section_ata . "-" . $row->section_allure . "?", "wrh_h/aluminium/hapus_parsial/" . $id_fppp . "/" . $row->id_stock, "#content", 'Hapus Parsial', 'btn btn-xs btn-danger', 'data-toggle="tooltip" title="Hapus Parsial"');
                                        }
                                        ?>
                                    </td>
                                    <td><?= $row->section_allure ?></td>
                                    <td><?= $row->temper ?></td>
                                    <td><?= $row->warna ?></td>
                                    <td><?= $row->ukuran ?></td>
                                    <td><?= $row->keterangan ?></td>
                                    <td align="center"><span id="qty_bom_<?= $row->id_stock ?>"><?= $qtyBOM ?></span></td>
                                    <td align="center"><span id="qty_kurang_<?= $row->id_stock ?>"><?= $kurang ?></span></td>
                                    <td align="center">
                                        <span style="display:none;" id="qty_gudang_asli_<?= $row->id_stock ?>"><?= $totalgudang ?></span>
                                        <span id="qty_gudang_<?= $row->id_stock ?>"><?= $totalgudang ?></span>
                                    </td>
                                    <td style="background-color:#ffd45e" align="center"><span id="qty_aktual_<?= $row->id_stock ?>" class='edit'><?= $qty_aktual ?></span>
                                        <input type='text' class='txtedit' data-id='<?= $row->id_stock ?>' data-field='qty_out' id='txt_<?= $row->id_stock ?>' value='<?= $qty_aktual ?>'>
                                    </td>
                                    <td style="background-color:#ffd45e">
                                        <select id="id_gudang_<?= $row->id_stock ?>" onchange="gudang(<?= $row->id_stock ?>)" data-id="<?= $row->id_stock ?>" data-field="id_gudang" class="form-control">
                                            <option id="">Pilih</option>
                                            <?php foreach ($gudang->result() as $key) {
                                                $selected2 = ($key->id == $id_gudang_stock) ? "selected" : "";
                                            ?>
                                                <option value="<?= $key->id ?>" <?= $selected2 ?>><?= $key->gudang ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td style="background-color:#ffd45e">
                                        <select id="keranjang_<?= $row->id_stock ?>" onchange="keranjang(<?= $row->id_stock ?>)" data-id="<?= $row->id_stock ?>" data-field="keranjang" class="form-control">
                                            <option id="">Pilih</option>
                                            <?php foreach ($keranjang->result() as $key) {
                                                $selected2 = ($key->keranjang == $keranjang_stock) ? "selected" : "";
                                            ?>
                                                <option value="<?= $key->keranjang ?>" <?= $selected2 ?>><?= $key->keranjang ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td style="background-color:#ffd45e" align="center"><input type="checkbox" id="produksi_<?= $row->id_stock ?>" data-id='<?= $row->id_stock ?>' data-field='produksi_<?= $row->id_stock ?>' class="checkbox" <?= $cekproduksi ?>></td>
                                    <td style="background-color:#ffd45e" align="center"><input type="checkbox" id="lapangan_<?= $row->id_stock ?>" data-id='<?= $row->id_stock ?>' data-field='lapangan_<?= $row->id_stock ?>' class="checkbox" <?= $ceklapangan ?>></td>
                                    <td><?= $row->created_ ?></td>
                                    <td><?= $row->updated_ ?></td>
                                </tr>
                            <?php
                            } else { ?>
                                <tr bgcolor="#8bb0c9">
                                    <td align="center"><?= $i++ ?></td>
                                    <td><?= $row->section_ata ?>
                                        <br><?php echo button_confirm("Anda yakin mengirim parsial item " . $row->section_ata . "-" . $row->section_allure . "?", "wrh_h/aluminium/kirim_parsial/" . $id_fppp . "/" . $row->id_stock, "#content", 'Kirim Parsial', 'btn btn-xs btn-default', 'data-toggle="tooltip" title="Kirim Parsial"'); ?>
                                        <br><?php //echo button_confirm("Anda yakin membuat di MF item " . $row->section_ata . "-" . $row->section_allure . "?", "wrh_h/aluminium/buat_mf/" . $id_fppp . "/" . $row->id_stock, "#content", 'Buat di MF', 'btn btn-xs btn-info', 'data-toggle="tooltip" title="Buat di MF"'); 
                                            ?>
                                    </td>
                                    <td><?= $row->section_allure ?></td>
                                    <td><?= $row->temper ?></td>
                                    <td><?= $row->warna ?></td>
                                    <td><?= $row->ukuran ?></td>
                                    <td><?= $row->keterangan ?></td>
                                    <td align="center"><?= $row->qty_bom - $row->qty_out ?></td>
                                    <td align="center">-</td>
                                    <td align="center">-</td>
                                    <td align="center"><?= $row->qty_out ?></td>
                                    <td align="center"><?= $row->gudang ?></td>
                                    <td align="center"><?= $row->keranjang ?></td>
                                    <td align="center"><input type="checkbox" onclick="return false;" class="checkbox" <?= $cekproduksi ?>></td>
                                    <td align="center"><input type="checkbox" onclick="return false;" class="checkbox" <?= $ceklapangan ?>></td>
                                    <td><?= $row->created_ ?></td>
                                    <td><?= $row->updated_ ?></td>
                                </tr>
                            <?php } ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <?= button_confirm("Anda yakin menyelesaikan stock out?", "wrh_h/aluminium/buat_surat_jalan_mf/" . $id_fppp, "#content", 'Buat Surat Jalan MF', 'btn btn-success', 'data-toggle="tooltip" title="Buat Surat Jalan"'); ?>
                <?= button('load_silent("wrh_h/aluminium/stok_out_make/' . $id_fppp . '","#content")', 'Kembali ke Gudang Warna', 'btn btn-primary', 'data-toggle="tooltip" title="Kembali ke Gudang Warna"'); ?>
            </div>
            <?php // echo button_confirm("Anda yakin menambahkan item stock out?", "wrh_h/aluminium/additemdetailbom/" . $id_fppp, "#modal", 'Add Item', 'btn btn-info', 'data-toggle="tooltip" title="Add Item"'); 
            ?>
            <?php //echo button_confirm("Anda yakin menyelesaikan stock out?", "wrh_h/aluminium/finishdetailbom/" . $id_fppp, "#content", 'Finish', 'btn btn-success', 'data-toggle="tooltip" title="Finish"'); 
            ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var table = $('#tableku').DataTable({
            ordering: false,
            paging: false,
            scrollX: true,
        });
        var table = $('#tb1').DataTable({
            ordering: true,
            paging: false,
            scrollX: true,
        });
        $("select").select2();
    });

    function update() {
        $("#update").hide();
        $.ajax({
            url: "<?= site_url('wrh_h/aluminium/updateSuratJalan/') ?>",
            dataType: "json",
            type: "POST",
            data: {
                "penerima": $("#penerima").val(),
                "alamat_pengiriman": $("#alamat_pengiriman").val(),
                "sopir": $("#sopir").val(),
                "no_kendaraan": $("#no_kendaraan").val(),
            },
            success: function(img) {
                $.growl.notice({
                    title: 'Berhasil',
                    message: "Mengupdate Surat Jalan!"
                });
                // load_silent("wrh_h/aluminium/detailbom/" + img['id'] + "/", "#content");
            }
        });

    }



    function gudang(id) {
        var fieldname = 'id_gudang';
        var value = $('#id_gudang_' + id).val();
        var edit_id = id;
        var id_fppp = "<?= $id_fppp ?>";
        // Send AJAX request
        $.ajax({
            url: "<?= site_url('wrh_h/aluminium/getQtyRowGudang/') ?>",
            dataType: "json",
            type: "POST",
            data: {
                field: fieldname,
                value: value,
                id: edit_id,
                id_fppp: id_fppp,
                gudang: $('#id_gudang_' + id).val(),
                keranjang: $('#keranjang_' + id).val(),
            },
            success: function(response) {
                console.log("gudang sukses!");
                if (response['qty_gudang'] == null) {
                    var qtygdg = 0;
                } else {
                    var qtygdg = response['qty_gudang'];
                }
                $('#qty_gudang_' + id).html(qtygdg);
                // load_silent("wrh_h/aluminium/detailbom/" + $("#id_fppp").val(), "#content");
            }
        })
    }

    function keranjang(id) {
        var fieldname = 'keranjang';
        var value = $('#keranjang_' + id).val();
        var edit_id = id;
        var id_fppp = "<?= $id_fppp ?>";
        // Send AJAX request
        $.ajax({
            url: "<?= site_url('wrh_h/aluminium/getQtyRowGudang/') ?>",
            dataType: "json",
            type: "POST",
            data: {
                field: fieldname,
                value: value,
                id: edit_id,
                id_fppp: id_fppp,
                gudang: $('#id_gudang_' + id).val(),
                keranjang: $('#keranjang_' + id).val(),
            },
            success: function(response) {

                console.log("gudang sukses!");
                if (response['qty_gudang'] == null) {
                    var qtygdg = 0;
                } else {
                    var qtygdg = response['qty_gudang'];
                }
                $('#qty_gudang_' + id).html(qtygdg);
                // load_silent("wrh_h/aluminium/detailbom/" + $("#id_fppp").val(), "#content");
            }
        })
    }



    $(".checkbox").change(function() {
        if (this.checked) {
            var fieldname = $(this).data('field');
            var value = 1;
            var edit_id = $(this).data('id');
            // Send AJAX request
        } else {
            var fieldname = $(this).data('field');
            var value = 0;
            var edit_id = $(this).data('id');
        }

        if (fieldname == 'produksi_' + edit_id) {
            $('#lapangan_' + edit_id).prop('checked', false); // Unchecks it
        } else {
            $('#produksi_' + edit_id).prop('checked', false); // Checks it
        }

        var qtybom = $('#qty_bom_' + edit_id).html();
        var qty_gudang = $('#qty_gudang_' + edit_id).html();
        var qty_kurang = $('#qty_kurang_' + edit_id).html();
        var qty_aktual = $('#txt_' + edit_id).val();
        if (parseInt(qty_gudang) < parseInt(qty_aktual)) {
            alert("Tidak boleh melebihi Qty Gudang!");
            $('#lapangan_' + edit_id).prop('checked', false);
            $('#produksi_' + edit_id).prop('checked', false);
        } else if (value == '') {
            $(element).hide();
            $(element).prev('.edit').show();
            $(element).prev('.edit').text(0);
        } else {
            $.ajax({
                url: "<?= site_url('wrh_h/aluminium/saveoutcheckmf/') ?>",
                dataType: "json",
                type: "POST",
                data: {
                    field: fieldname,
                    value: value,
                    id: edit_id,
                    gudang: $('#id_gudang_' + edit_id).val(),
                    keranjang: $('#keranjang_' + edit_id).val(),
                    qtytxt: $('#txt_' + edit_id).val(),
                },
                success: function(response) {
                    $.growl.notice({
                        title: 'Sukses',
                        message: "Data Updated!"
                    });
                    if (response['qty_gudang'] == null) {
                        var qtygdg = 0;
                    } else {
                        var qtygdg = response['qty_gudang'];
                    }
                    $('#qty_gudang_' + edit_id).html(qtygdg);
                    $('#qty_kurang_' + edit_id).html(qtybom - qty_aktual);
                }
            });
        }

    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.txtedit').hide();
        // On text click
        $('.edit').click(function() {
            // Hide input element
            $('.txtedit').hide();

            // Show next input element
            $(this).next('.txtedit').show().focus();

            // Hide clicked element
            $(this).hide();
        });

        // Focus out from a textbox
        $('.txtedit').focusout(function() {
            // Get edit id, field name and value
            var fieldname = $(this).data('field');
            var value = $(this).val();
            var edit_id = $(this).data('id');
            // assign instance to element variable
            var element = this;

            var qtybom = $('#qty_bom_' + edit_id).html();
            var qty_gudang = $('#qty_gudang_' + edit_id).html();
            var qty_kurang = $('#qty_kurang_' + edit_id).html();

            if (parseInt(qty_gudang) < parseInt(value)) {
                alert("Tidak boleh melebihi Qty Gudang!");
                $(element).hide();
                $(element).prev('.edit').show();
                $(element).prev('.edit').text(value);
            } else if (value == '') {
                $(element).hide();
                $(element).prev('.edit').show();
                $(element).prev('.edit').text(0);
            } else {
                $.ajax({
                    url: "<?= site_url('wrh_h/aluminium/saveout/') ?>",
                    dataType: "json",
                    type: "POST",
                    data: {
                        field: fieldname,
                        value: value,
                        id: edit_id,
                        gudang: $('#id_gudang_' + edit_id).val(),
                        keranjang: $('#keranjang_' + edit_id).val(),
                    },
                    success: function(response) {

                        // Hide Input element
                        $(element).hide();

                        // Update viewing value and display it
                        $(element).prev('.edit').show();
                        $(element).prev('.edit').text(value);
                        $.growl.notice({
                            title: 'Sukses',
                            message: "Data Updated!"
                        });
                        if (response['qty_gudang'] == null) {
                            var qtygdg = 0;
                        } else {
                            var qtygdg = response['qty_gudang'];
                        }
                        $('#qty_gudang_' + edit_id).html(qtygdg);
                        $('#qty_kurang_' + edit_id).html(qtybom - value);
                        // console.log(qtybom);
                        // console.log(value);
                        // gudang(edit_id);
                        // load_silent("wrh_h/aluminium/detailbom/" + $("#id_sj").val(), "#content");
                    }
                });
                // }
            }
        });
    });
</script>