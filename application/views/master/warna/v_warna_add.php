<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="box-body big">
    <?php echo form_open('', array('name' => 'faddmenugrup', 'class' => 'form-horizontal', 'role' => 'form')); ?>

    <div class="form-group">
        <label class="col-sm-4 control-label">Kode</label>
        <div class="col-sm-8">
            <?php echo form_input(array('name' => 'kode', 'class' => 'form-control')); ?>
            <?php echo form_error('kode'); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">warna</label>
        <div class="col-sm-8">
            <?php echo form_input(array('name' => 'warna', 'class' => 'form-control')); ?>
            <?php echo form_error('warna'); ?>
        </div>
    </div>


    <div class="form-group">
        <label class="col-sm-4 control-label">Save</label>
        <div class="col-sm-8 tutup">
            <?php
            echo button('send_form(document.faddmenugrup,"master/warna/show_addForm/","#divsubcontent")', 'Save', 'btn btn-success') . " ";
            ?>
        </div>
    </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // $('.tutup').click(function(e) {
        //     $('#myModal').modal('hide');
        // });
        $("select").select2();
    });
</script>