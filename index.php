<?php

// bismillaahirrahmaanirrahiim
// allahumma sholli 'alaa sayyidinaa muhamamd
// wa 'alaa aalihi wasohbihi ajma'in
// created by marjuqi, October 2019

    $monthly_bruto = $monthly_netto = $yearly_netto = $pkp = $pt = $pb = 0;

    $array_ptkp = array(
        "tk0" => 54*1000*1000,
        "tk1" => 58.5 *1000*1000,
        "tk2" => 63*1000*1000,
        "tk3" => 67.5 *1000*1000,
        "k0" => 58.5 *1000*1000,
        "k1" => 63*1000*1000,
        "k2" => 67.5 *1000*1000,
        "k3" => 72*1000*1000,
        "ki0" => 108 *1000*1000,
        "ki1" => 112.5 *1000*1000,
        "ki2" => 117 *1000*1000,
        "ki3" => 121.5 *1000*1000
    );

    $array_percentage_pajak = array(
        "0.05" => array("min" => 0*1000*1000, "max" => 50*1000*1000),
        "0.15" => array("min" => 50*1000*1000 + 1, "max" => 250*1000*1000),
        "0.25" => array("min" => 250*1000*1000 + 1, "max" => 500*1000*1000),
        "0.30" => array("min" => 500*1000*1000 + 1, "max" => 1*1000*1000*1000*1000*1000*1000)
    );

    function nf($nominal){
        return number_format($nominal,0,"",".");
    }
    
    function writeLog($posted_data){
        
        // DON'T FORGET to set the permission of log.txt 
        // as 644 OR -rw-rw-r--
        
        $array_data = array(
            "posted_data" => $posted_data,
            "meta_client" => array(
                'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'],
                'HTTP_REFERER' => $_SERVER['HTTP_REFERER'],
                'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT']
            )
        );
        $header = "\n\n==== ".date("Y-m-d H:i:s").", IP ".$_SERVER['REMOTE_ADDR']." ===\n";
        $string_data     = $header. json_encode($array_data);
        $filename         = "log.txt";
        
        $myfile = fopen($filename, "a") or die("Unable to open file!");
        fwrite($myfile, $string_data);
        fclose($myfile);
    }

?>
<html>
<head>
    <title>Kalkulator Pajak (v.1.0)</title>
    <meta charset="UTF-8">
    <meta name="description" content="kalkulator pajak (simplified)">
    <meta name="keywords" content="kalkulator pajak, pajak, simplified">
    <meta name="author" content="kangmasjuqi">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style type="text/css">
        p.result{
            color:dark;
            padding:3px;
            font-family:comic sans ms;
            background-color:#fdfc76;
        }
        body {
            margin-left:10px;
        }
        p {
            margin-left:10px;
        }
        span.final_result{
            background-color:#333333;
            color:white;
            padding:3px 10px;
        }
        div.disclaimer{
            background-color:#ffcccc;
            padding:3px 3px 3px 20px;
            font-style:italic;
        }
    </style>
</head>
<body>
<h1>Kalkulator Pajak (v.1.0)</h1>

<!--
penghasilan kotor per bulan = A
<br/>
penghasilan bersih per bulan = B
<br/>
penghasilan bersih per tahun = B x 12 = C
<br/>
(PTKP) penghasilan tidak kena pajak = PTKP
<br/>
(PKP) penghasilan kena pajak = C - PTKP
<br/>
(PT) pajak yang harus dibayarkan untuk 12 bulan = 5% x PKP
<br/>
(PB) pajak yang harus dibayarkan untuk 1 bulan = PT : 12
-->
<hr/>

<h3>Data wajib pajak (simplified)</h3>

<form method="post">
    <!--penghasilan kotor bulanan : <input type="text" name="monthly_bruto"><br/>-->
    <p>penghasilan bersih bulanan : <input type="text" name="monthly_netto"></p>
    
    <p>status menikah : 
    <select name="status_menikah">
        <option value="tk">lajang/cerai</option>
        <option value="k">menikah</option>
    </select></p>

    <p>jumlah tanggungan : 
    <select name="jumlah_tanggungan">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
    </select></p>

    <p>penghasilan istri digabung dalam perhitungan ? 
    <input type="radio" name="status_penghasilan_istri" value="tidak" checked="checked"> tidak digabung
    <input type="radio" name="status_penghasilan_istri" value="ya"> digabung </p>

    <p><input type="submit" value="hitung"></p>
    <input type="hidden" name="flag" value="1">
</form>

<hr/>

<h3>Hasil perhitungan</h3>

<?php
    
    if(isset($_POST['flag'])){

        $_POST['monthly_netto'] = (int) $_POST['monthly_netto'];
        
        if(is_numeric($_POST['monthly_netto']) and $_POST['monthly_netto'] <=0){
            echo '<p class="result">Mohon masukkan penghasilan bersih bulanan yang valid</p>';
            
        }
        else {

            $monthly_netto = $_POST['monthly_netto'];
            echo '<p class="result">A. Penghasilan bersih bulanan = '.nf($monthly_netto).'</p>';

            $yearly_netto = 12 * $_POST['monthly_netto'];
            echo '<p class="result">B. Penghasilan bersih tahunan (A x 12) = '.nf($yearly_netto).'</p>';

            if($_POST['status_penghasilan_istri'] == 'ya')
                $kode_tpkp = $_POST['status_menikah'].'i'.$_POST['jumlah_tanggungan'];
            else 
                $kode_tpkp = $_POST['status_menikah'].$_POST['jumlah_tanggungan'];

            $ptkp = $array_ptkp[$kode_tpkp];
            echo '<p class="result">C. Kode PTKP (berdasarkan status_menikah + jumlah_tanggungan) = '.$kode_tpkp.'</p>';

            $ptkp = $array_ptkp[$kode_tpkp];
            echo '<p class="result">D. Penghasilan Tidak Kena Pajak (berdasarkan kode PTKP) = '.nf($ptkp).'</p>';

            $pkp = $yearly_netto - $ptkp;
            echo '<p class="result">E. Penghasilan Kena Pajak (B dikurangi D) = '.nf($pkp).'</p>';

            $total_pajak = 0;

            echo '<p class="result">F. Perhitungan besaran pajak (berdasarkan langkah E)</p>';
            $ii = 0;
            if($pkp > 0){
                echo '<p class="result">';
                foreach($array_percentage_pajak as $p => $range){
                    if($pkp <= 0)
                        break;
                    $p = (float) $p;
                    $nominal = ($pkp < $range['max']) ? $pkp: $range['max'];
                    $pajak = (float)$p * (float)$nominal;
                    $total_pajak = $total_pajak + $pajak;
                    echo "==> ".++$ii.". ".$p." x ".nf($nominal)." = ".nf($pajak).'<br/>';
                    $pkp = $pkp - $range['max'];
                }
                echo '<br/>>> total pajak (F1..F'.$ii.') = '.nf($total_pajak).'</p>';
            }
            else 
                echo '<p class="result">- skipped -</p>';

            $pt_terutang = $total_pajak;
            echo '<p class="result">G. Besaran pajak tahunan = <span class="final_result">'.nf($pt_terutang).'</span></p>';

            $pb_terutang = (float) ($pt_terutang / 12);
            echo '<p class="result">H. (atau) Besaran pajak dalam bulanan (G / 12) = <span class="final_result">'.nf($pb_terutang).'</span></p>';

            echo '
            <div class="disclaimer">
                <p><b>Disclaimer</b> : 
                    Kalkulator pajak ini tidak merepresentasikan perhitungan resmi milik pemerintah 
                    serta tidak dapat digunakan sebagai acuan resmi/baku dalam pelaporan pajak anda.
                </p>
            </div>';

            writeLog($_POST);
        
        }
    }
?>

<hr/>
<h4>Referensi perhitungan</h4>
<p>
https://www.online-pajak.com/ptkp-2019<br/>
https://www.online-pajak.com/tarif-pajak-pph-21<br/>
https://www.pajakbro.com/2016/06/ptkp-2016-terbaru-pdf.html<br/>https://www.finansialku.com/wp-content/uploads/2018/05/Studi-Kasus-Cara-Menghitung-Pajak-Penghasilan-03-PPh-21-Finansialku.jpg
</p>

<h4>Kode sumber</h4>
<p><a href="https://github.com/kangmasjuqi/kalkulator_pajak">https://github.com/kangmasjuqi/kalkulator_pajak</a></p>


</body>
</html>
