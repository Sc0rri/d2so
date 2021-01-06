<?php

/* @var $this yii\web\View */

$this->title = 'Top perks';

if (Yii::$app->user->isGuest) {
    echo yii\authclient\widgets\AuthChoice::widget([
        'baseAuthUrl' => ['site/auth'],
        'popupMode' => false,
    ]);
} else {
    ksort($data);
    ?>
    <h1><?= $this->title; ?></h1>
    <br/>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th></th>
            <?php foreach($godroll_attributes as $attr) {?>
                <th colspan="2" class="text-center"><?= $attr; ?></th>
            <?php } ?>
        </tr>
        <tr>
            <th></th>
            <?php foreach($godroll_attributes as $attr) {?>
                <th  class="text-center success">pve</th>
                <th  class="text-center danger">pvp</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
            <?php foreach($data as $type=>$weapon) {?>
                <tr>
                    <td><?= $type; ?></td>
                    <?php foreach($godroll_attributes as $attr) {?>
                        <td class="text-center success">
                            <ul style="list-style: none">
                            <?php
                                $total = $weapon[$attr]['pve']['count'];
                                unset($weapon[$attr]['pve']['count']);
                                $data = [];
                                foreach($weapon[$attr]['pve'] as $perk=>$count) {
                                    $percent = round($count*100/$total);
                                    $key = $perk."&nbsp;<b>".$percent."%</b>";
                                    $data[$key] = $percent;
                                }
                                arsort($data);
                                foreach($data as $res_str=>$percent) { ?>
                                    <li style="white-space: nowrap"><?=$res_str; ?></li>
                                <?php } ?>
                            </ul>
                        </td>
                        <td class="text-center danger">
                            <ul style="list-style: none">
                                <?php
                                $total = $weapon[$attr]['pvp']['count'];
                                unset($weapon[$attr]['pvp']['count']);
                                foreach($weapon[$attr]['pvp'] as $perk=>$count) { ?>
                                    <li style="white-space: nowrap"><?= $perk."&nbsp;<b>".round($count*100/$total); ?>%</b></li>
                                <?php } ?>
                            </ul>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>