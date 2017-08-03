<?php
if (count($this->matches)) {
?>
<table border="1">
    <tr>
    <th><?= implode(", ",$this->nums);;?></th>
    <?php for ($i = 1; $i < 40; $i++) {?>
        <th><?=sprintf("%02d", $i);?></th>
    <?php } ?>
    </tr>
    <?php foreach ($this->matches as $m) {
            $arr = $m->toArray();
    ?>
    <tr>
        <td><?= $m->id . '/' . date('Y-m-d', $m->date);?></td>
    <?php       for ($i = 1; $i < 40; $i++) {?>
        <td <?= in_array($i, $arr) ? 'class="result_num"' : '' ;?>><?= in_array($i, $arr) ? sprintf("%02d", $i) : '';?></td>
    <?php       } ?>
    </tr>
    <?php } ?>
</table>
</br>
<?php 
} 
?>
