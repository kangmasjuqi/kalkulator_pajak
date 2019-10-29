<?php

// bismillaahirrahmaanirrahiim
// allahumma sholli 'alaa sayyidinaa muhamamd
// created by marjuqi, October 219

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

?>

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

<h3>Hasil penghitungan</h3>

<style>
	p.result{
		color:dark;
		padding:3px;
		font-family:comic sans ms;
		background-color:#fdfc76;
	}
</style>

<?php
	
	function nf($nominal){
		return number_format($nominal,0,"",".");
	}
	
	if(isset($_POST['flag'])){

		$monthly_netto = $_POST['monthly_netto'];
		echo '<p class="result">A. Penghasilan bersih bulanan : '.nf($monthly_netto);

		$yearly_netto = 12 * $_POST['monthly_netto'];
		echo '<p class="result">B. Penghasilan bersih tahunan (A x 12) : '.nf($yearly_netto);

		if($_POST['status_penghasilan_istri'] == 'ya')
			$kode_tpkp = $_POST['status_menikah'].'i'.$_POST['jumlah_tanggungan'];
		else 
			$kode_tpkp = $_POST['status_menikah'].$_POST['jumlah_tanggungan'];

		$ptkp = $array_ptkp[$kode_tpkp];
		echo '<p class="result">C. Kode PTKP (berdasarkan status_menikah + jumlah_tanggungan): '.$kode_tpkp;

		$ptkp = $array_ptkp[$kode_tpkp];
		echo '<p class="result">D. Penghasilan tidak kena pajak (berdasarkan kode PTKP): '.nf($ptkp);

		$pkp = $yearly_netto - $ptkp;
		echo '<p class="result">E. PKP - Penghasilan Kena Pajak (B dikurangi D) : '.nf($pkp);

		$total_pajak = 0;

		echo '<p class="result">F. Penghitungan besaran pajak berdasarkan PKP';
		$ii = 0;
		foreach($array_percentage_pajak as $p => $range){
			if($pkp <= 0)
				break;
			$p = (float) $p;
			$nominal = ($pkp < $range['max']) ? $pkp: $range['max'];
			$pajak = $p * $nominal;
			$total_pajak = $total_pajak + $pajak;
			echo "<br/>==> ".++$ii.". ".$p." x ".nf($nominal)." : ".nf($pajak);
			$pkp = $pkp - $range['max'];
		}
		echo '<p class="result">>> total pajak (F1..F'.$ii.'): '.nf($total_pajak);

		$pt_terutang = $total_pajak;
		echo '<p class="result">G. Besaran pajak tahunan: '.nf($pt_terutang);

		$pb_terutang = $pt_terutang / 12;
		echo '<p class="result">H. (atau) Besaran pajak dalam bulanan (G / 12): '.nf($pb_terutang);
	}
?>

<hr/>
<br/>Referensi penghitungan:
<br/>https://www.online-pajak.com/ptkp-2019
<br/>https://www.online-pajak.com/tarif-pajak-pph-21
<br/>https://www.pajakbro.com/2016/06/ptkp-2016-terbaru-pdf.html
<br/>https://www.finansialku.com/wp-content/uploads/2018/05/Studi-Kasus-Cara-Menghitung-Pajak-Penghasilan-03-PPh-21-Finansialku.jpg