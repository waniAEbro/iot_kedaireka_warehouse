<?php
$t1 = 'load_silent("klg/fppp/list/1/",".tab-content")';
$t2 = 'load_silent("klg/fppp/list/2/",".tab-content")';
$t3 = 'load_silent("klg/fppp/list/3/",".tab-content")';
$t4 = 'load_silent("klg/fppp/list/4/",".tab-content")';
$t5 = 'load_silent("klg/fppp/list/5/",".tab-content")';
$t6 = 'load_silent("klg/fppp/list/6/",".tab-content")';
$t7 = 'load_silent("klg/fppp/list/7/",".tab-content")';
if ($param_tab == '1') {
    $this->fungsi->run_js($t1);
    $tab_1 = 'active';
    $tab_2 = '';
    $tab_3 = '';
    $tab_4 = '';
    $tab_5 = '';
    $tab_6 = '';
    $tab_7 = '';
} else if ($param_tab == '2') {
    $this->fungsi->run_js($t2);
    $tab_1 = '';
    $tab_2 = 'active';
    $tab_3 = '';
    $tab_4 = '';
    $tab_5 = '';
    $tab_6 = '';
    $tab_7 = '';
} else if ($param_tab == '3') {
    $this->fungsi->run_js($t3);
    $tab_1 = '';
    $tab_2 = '';
    $tab_3 = 'active';
    $tab_4 = '';
    $tab_5 = '';
    $tab_6 = '';
    $tab_7 = '';
} else if ($param_tab == '4') {
    $this->fungsi->run_js($t4);
    $tab_1 = '';
    $tab_2 = '';
    $tab_3 = '';
    $tab_4 = 'active';
    $tab_5 = '';
    $tab_6 = '';
    $tab_7 = '';
} else if ($param_tab == '5') {
    $this->fungsi->run_js($t5);
    $tab_1 = '';
    $tab_2 = '';
    $tab_3 = '';
    $tab_4 = '';
    $tab_5 = 'active';
    $tab_6 = '';
    $tab_7 = '';
} else if ($param_tab == '6') {
    $this->fungsi->run_js($t6);
    $tab_1 = '';
    $tab_2 = '';
    $tab_3 = '';
    $tab_4 = '';
    $tab_5 = '';
    $tab_6 = 'active';
    $tab_7 = '';
} else {
    $this->fungsi->run_js($t7);
    $tab_1 = '';
    $tab_2 = '';
    $tab_3 = '';
    $tab_4 = '';
    $tab_5 = '';
    $tab_6 = '';
    $tab_7 = 'active';
}
?>
<div class="box box-default">

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="<?= $tab_1 ?>"><a data-toggle="tab" onclick='<?php echo $t1; ?>' href="javascript:void(0)">RESIDENTIAL</a></li>
            <li class="<?= $tab_2 ?>"><a onclick='<?php echo $t2; ?>' data-toggle="tab" href="javascript:void(0)">ASTRAL</a></li>
            <li class="<?= $tab_3 ?>"><a onclick='<?php echo $t3; ?>' data-toggle="tab" href="javascript:void(0)">ASTRAL BRAVO</a></li>
            <li class="<?= $tab_4 ?>"><a onclick='<?php echo $t4; ?>' data-toggle="tab" href="javascript:void(0)">ASTRAL BENDING EXTRUTION</a></li>
            <li class="<?= $tab_5 ?>"><a onclick='<?php echo $t5; ?>' data-toggle="tab" href="javascript:void(0)">VITTO</a></li>
            <li class="<?= $tab_6 ?>"><a onclick='<?php echo $t6; ?>' data-toggle="tab" href="javascript:void(0)">HRB</a></li>
            <li class="<?= $tab_7 ?>"><a onclick='<?php echo $t7; ?>' data-toggle="tab" href="javascript:void(0)">MOMO</a></li>
        </ul>
        <div class="tab-content">

        </div>
    </div>

</div>