<?php
/*
Template Name: Установка курса
Template Post Type: page
*/
?>
<?php //get_header('page'); ?>

<?php
if(isset($_POST["save"])){
    if($_POST["password"] != "koftaGadAM123"){
        echo "Рав, дам гир!";
    }
    $ar = $_POST;
    $res = array();
    $nbt = array();
    foreach($ar["buy"] as $k=>$v){
        $res[] = array(
            "currency" => $k,
            "buy"  => $v,
            "sell" => $ar["sell"][$k],
            "buy_fiz"  => $ar["buy_fiz"][$k],
            "sell_fiz" => $ar["sell_fiz"][$k],
            "datetime" => date("Y-m-d H:i:s")
        );
        $nbt[] = array(
            "currency" => $k,
            "buy"  => $ar["nbt"][$k],
            "sell" => $ar["nbt"][$k],
            "datetime" => date("Y-m-d H:i:s")
        );

    }
    /*
    echo "<pre>";
    var_dump($nbt);
    echo "<pre>";
    exit;*/
    load_rate(array("rate"=>$res));
    load_rate(array("rate"=>$nbt),1);



}
$rateTypes = array(
    "nbt" 	 => "НБТ",
    "card" 	 => "Кошелек",
    "mt"  	 => "Денежные переводы",
    "beznal" => "Безналичные",
    "kassa"  => "Касса",
    "tin"  	 => "Тинькофф"
);
$currencyTypes = array(
    2 => "USD",
    3 => "EUR",
    4 => "RUB"
);

if(isset($_POST["addRates"])) {
//    if($_POST["password"] != "koftaGadAM123"){
//        echo "Рав, дам гир!";
//    }
    $ar = $_POST;
    $res = array();
    foreach($ar["buy"] as $currency=>$types){
        $res[$currency] = array(
            "currency" => $currency,
            "datetime" => date("Y-m-d H:i:s")
        );
    }
    foreach($ar["buy"] as $currency=>$types){
        foreach($types as $type=>$rate){
            $res[$currency][$type."_buy"] = $rate;
            $res[$currency][$type."_sell"] = $ar["sell"][$currency][$type];
        }
    }
    foreach($ar["nbt"] as $currency=>$rate){
        $res[$currency]["nbt"] = $rate;
    }
    /*
    echo "<pre>";
    var_dump($res);
    echo "<pre>";*/
    load_rate_new($res);
    header("Location:https://azizimoliya.tj/setrate");
}
?>
<div class="middle" style="padding-bottom: 239px;">
    <div class="row equal">
    </div>
    <div class="row  section">
        <div id="system-message-container">
        </div>

    </div>
    <div class="row  section">
        <div class="col-md-12" style="margin-bottom:20px;">
            <div class="content" itemscope="" itemtype="https://schema.org/Person">
                <div class="page-header">
                    <h2>
                        <span class="contact-name" itemprop="name"><?php the_title();?></span>
                    </h2>
                </div>
                <div style="display: block; width: 100%; margin: 0px;">
                    <form action="#" method="POST">
                        <h1>Азизи Молия</h1>
                        <h3>Физ. лица</h3>
                        USD<input type="number"  required min="0" step="0.0001" name="buy[2]">
                        <input type="number"  required min="0" step="0.0001" name="sell[2]"><br>
                        EUR<input type="number"  required min="0" step="0.0001" name="buy[3]">
                        <input type="number"  required min="0" step="0.0001" name="sell[3]"><br>
                        RUB<input type="number"  required min="0" step="0.0001" name="buy[4]">
                        <input type="number"  required min="0" step="0.0001" name="sell[4]">
                        <h3>Юр. лица</h3>
                        USD<input type="number"  required min="0" step="0.0001" name="buy_fiz[2]">
                        <input type="number"  required min="0" step="0.0001" name="sell_fiz[2]"><br>
                        EUR<input type="number"  required min="0" step="0.0001" name="buy_fiz[3]">
                        <input type="number"  required min="0" step="0.0001" name="sell_fiz[3]"><br>
                        RUB<input type="number"  required min="0" step="0.0001" name="buy_fiz[4]">
                        <input type="number"  required min="0" step="0.0001" name="sell_fiz[4]"><br>
                        <hr>
                        <h1>НБТ</h1>
                        USD<input type="number"  required min="0" step="0.0001" name="nbt[2]"><br>
                        EUR<input type="number"  required min="0" step="0.0001" name="nbt[3]"><br>
                        RUB<input type="number"  required min="0" step="0.0001" name="nbt[4]"><br>
                        <input name="password" placeholder="Пароль"><br>
                        <input name="save" type="submit" value="Сохранить">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row  section">
        <div class="col" style="margin-bottom:20px;">
            <div class="content" itemscope="" itemtype="https://schema.org/Person">
                <div class="page-header">
                    <h2>
                        <span class="contact-name" itemprop="name"><?php the_title();?></span>
                    </h2>
                </div>
                <div style="display: block; width: 100%; margin: 0px;">
                    <h1>Азизи Молия</h1>
                    <form action="#" method="POST">
                        <table class="table table-sm table-bordered text-center">
                            <tr>
                                <td></td>
                                <?php foreach($currencyTypes as $kk=>$vv):?>
                                    <td><h4><?=$vv;?></h4></td>
                                <?php endforeach;?>
                            </tr>
                            <?php foreach($rateTypes as $k=>$v):?>
                                <tr>
                                    <td class="align-middle">
                                        <h5 class="text-right"><?=$v;?></h5>
                                    </td>
                                    <?php foreach($currencyTypes as $kk=>$vv):?>
                                        <td class="text-center">
                                            <?php if($k != "nbt"):?>
                                                <input type="number" class="form-control w-50 ml-5 d-block" placeholder="Покупка"  required min="0" step="0.0001" name="buy[<?=$kk;?>][<?=$k;?>]">
                                                <input type="number" class="form-control w-50 ml-5 d-block" placeholder="Продажа" required min="0" step="0.0001" name="sell[<?=$kk;?>][<?=$k;?>]">
                                            <?php else:?>
                                                <input type="number" class="form-control w-50 ml-5" required min="0" step="0.0001" name="<?=$k;?>[<?=$kk;?>]">
                                            <?php endif;?>
                                        </td>
                                    <?php endforeach;?>
                                </tr>
                            <?php endforeach;?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <input name="password" class="form-control" placeholder="Пароль"><br>
                                    <input name="addRates" class="btn btn-primary" type="submit" value="Сохранить">
                                </td>
                            </tr>

                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>