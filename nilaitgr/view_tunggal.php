<?php
include "../backend/includes/connection.php";
$avgKB = 0;
$avgKM = 0;
$avgHukuman = 0;
$datpertandingn = [];
$iter = 0;
?>
<html>

<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<link rel="stylesheet" href="css/bootstrap.min.css">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<style type="text/css">
		/* .style1 {
			color: #FFFF00;
			font-weight: bold;
		} */
	</style>
</head>

<body>
	<div class="container-fluid">
		<center>
			<h2>MONITORING PENILAIAN WASJUR</h2>
			<h2>KATEGORI TUNGGAL</h2>
		</center>
		<!-- * Unsur nilai (KEBENARAN) = 100 - (penjumlahan field jurus1 s.d. jurus14) FROM TABLE nilai_tunggal WHERE id_jadwal=id_partai(table jadwal_tgr) AND id_juri=1/2/3/4/5<br>
* Unsur nilai (KEMANTAPAN) = field kemantapan FROM TABLE nilai_tunggal WHERE id_jadwal=id_partai(table jadwal_tgr) AND id_juri=1/2/3/4/5 <br>
* HUKUMAN = penjumlahan field hukum_1 s.d hukum_5 FROM TABLE nilai_tunggal WHERE id_jadwal=id_partai(table jadwal_tgr) AND id_juri=1/2/3/4/5<br>
* NILAI/JURI = (KEBENARAN) + (KEMANTAPAN) - (HUKUMAN) pada masing" juri <br>
* TOTAL NILAI = (penjumlahan NILAI JURI 1 s.d. 5) - (NILAI JURI TERKECIL) - (NILAI JURI TERBESAR) -->

		<div id="jadwaltunggal" class="table-responsive">
			<table class="table table-bordered">
				<tr class="text-center" bgcolor="#FFFF00">
					<td width="28" rowspan="2">NO</td>
					<td width="32" rowspan="2">GOL</td>
					<td width="48" rowspan="2">NAMA</td>
					<td width="107" rowspan="2">KONTINGEN</td>
					<td colspan="2">UNSUR NILAI</td>
					<td width="52" rowspan="2">HUKUMAN</td>
					<td width="66" rowspan="2">NILAI /JURI</td>
					<td width="345" rowspan="2"> TOTAL NILAI</td>
				</tr>
				<tr class="text-center" bgcolor="#FFFF00">
					<td width="96">KEBENARAN</td>
					<td width="110">KEMANTAPAN</td>
				</tr>
				<?php
				$no = 0;

				//Mencari data jadwal pertandingan TUNGGAL
				$sqljadwal = "SELECT * FROM jadwal_tgr
					WHERE kategori='Tunggal'
					ORDER BY id_partai,golongan ASC";
				$jadwal_tunggal = mysqli_query($koneksi, $sqljadwal);
				$iterasi = 0;
				?>
				<?php while ($jadwal = mysqli_fetch_array($jadwal_tunggal)) {
					try { ?>
						<tr class="text-center">
							<td><?php echo $jadwal['noundian']; ?></td>
							<td><?php echo $jadwal['golongan']; ?></td>
							<td><?php echo $jadwal['nama']; ?></td>
							<td><?php echo $jadwal['kontingen']; ?></td>
							<td>
								<?php
								$datpertandingn[$iterasi]["nama"] = $jadwal['nama'];
								$sql = "SELECT * FROM wasit_juri";
								$exec = mysqli_query($koneksi, $sql);
								$array_nilai = [];
								while ($juri = mysqli_fetch_array($exec)) {
									$kebenaran = mysqli_query($koneksi, "SELECT * FROM nilai_tunggal WHERE id_juri=" . $juri['id_juri'] . " AND id_jadwal=" . $jadwal['id_partai']);
									$row = mysqli_fetch_row($kebenaran);
									$kebenaran = 0.1 * ($row[3] + $row[4] + $row[5] + $row[6] + $row[7] + $row[8] + $row[9] + $row[10] + $row[11] + $row[12] + $row[13] + $row[14] + $row[15] + $row[16] - 1);

									if ($kebenaran != 0) {
										$kebenaran = 10 - (-$kebenaran);
									}

									$array_nilai[$juri['id_juri']]['kebenaran'] = $kebenaran;
								?>
									<?= $juri[1] ?> : <?= empty($kebenaran) ? 0 : $kebenaran ?><br />
								<?php } ?> </td>
							<td>
								<?php
								$sql = "SELECT * FROM wasit_juri";

								$exec = mysqli_query($koneksi, $sql);

								while ($juri = mysqli_fetch_array($exec)) {
									$kemantapan = mysqli_query($koneksi, "SELECT kemantapan FROM nilai_tunggal WHERE id_juri=" . $juri['id_juri'] . " AND id_jadwal=" . $jadwal['id_partai']);
									$row = mysqli_fetch_row($kemantapan);
									$kemantapan = $row[0] * 0.1;
									// var_dump($kemantapan);
									$array_nilai[$juri['id_juri']]['kemantapan'] = $kemantapan;
								?>
									<?= $juri[1] ?> : <?= empty($kemantapan) ? 0 : $kemantapan ?><br />
								<?php } ?> </td>
							<td>
								<?php
								$sql = "SELECT * FROM wasit_juri";

								$exec = mysqli_query($koneksi, $sql);

								while ($juri = mysqli_fetch_array($exec)) {

									$hukuman = mysqli_query($koneksi, "SELECT hukum_1,hukum_2,hukum_3,hukum_4,hukum_5 FROM nilai_tunggal WHERE id_juri=" . $juri['id_juri'] . " AND id_jadwal=" . $jadwal['id_partai']);
									$row = mysqli_fetch_row($hukuman);
									$hukuman = ($row[0] + $row[1] + $row[2] + $row[3] + $row[4]) * 0.1;
									$array_nilai[$juri['id_juri']]['hukuman'] = $hukuman;
								?>
									<?= $juri[1] ?> : <?= empty($hukuman) ? 0 : $hukuman ?><br />
								<?php } ?> </td>
							<td>
								<?php
								$sql = "SELECT * FROM wasit_juri";

								$exec = mysqli_query($koneksi, $sql);

								$tempNilai = [];
								$totalNilai = 0;

								while ($juri = mysqli_fetch_array($exec)) {


									if (isset($array_nilai[$juri['id_juri']]['kebenaran'])) {
										$nilai = ($array_nilai[$juri['id_juri']]['kebenaran'] + $array_nilai[$juri['id_juri']]['kemantapan']) - (-$array_nilai[$juri['id_juri']]['hukuman']);
										$tempNilai[] = $nilai;
										$totalNilai += $nilai;
									} else {
										$nilai = 0;
									}
								?>
									<?= $juri[1] ?> : <?= $nilai ?><br />
								<?php } ?> </td>

							<?php
							// new logic 
							for ($i = 1; $i <= 5; $i++) {
								$avgKB += $array_nilai[$i]['kebenaran'];
								$avgKM += $array_nilai[$i]['kemantapan'];
								$avgHukuman += $array_nilai[$i]['hukuman'];
							}
							$avgKB = $avgKB / 5;
							$avgKM = $avgKM / 5;
							$avgHukuman = $avgHukuman / 5;

							$totalNilai = ($avgKB + $avgKM) - $avgHukuman;

							$datpertandingn[$iterasi]["totalNilai"] = $totalNilai;
							$iterasi++;
							?>
							<td class="" data-toggle="modal" data-target="#exampleModal<?= $iter++ ?>">
								<table width="343" height="28" border="0">
									<tr>
										<th width="52" bgcolor="#663399" scope="row">
											<div align="middle"><img src="ipsi.png" align="middle" width="42" height="40" /></div>
										</th>
										<td width="281" bgcolor="#663399"><strong>
												<font size="3" color="#FFFF00">PENCAK SILAT TUNGGAL</font>
											</strong></td>
									</tr>
								</table>
								<table width="343" height="28" border="0">
									<tr>
										<th width="50" scope="row"><img src="logotgr.png" width="50" height="38" align="middle" /></th>
										<td width="283" bgcolor="#996699"><strong>
												<font size="2" color="#FFFFFF"><?php echo $jadwal['noundian']; ?>.<?php echo $jadwal['nama']; ?></font>
											</strong></td>
									</tr>
								</table>
								<div align="center">TOTAL SCORE<br>
									<table width="138" border="0">
										<tr>
											<td height="79">
												<div align="center" style="background-color:#FFFF00"><strong>
														<font size="10">
															<?= number_format($totalNilai, 2) ?>
														</font>
													</strong></div>
											</td>
										</tr>
									</table>

									MIN :
									<?= min($tempNilai) ?>

									MAX :
									<?= max($tempNilai) ?>
								</div>
							</td>
						</tr>
					<?php } catch (Exception $e) {
					} ?>
				<?php $no++;
				} ?>
			</table>
		</div>


		<div class="table-responsive">
			<table class="table">
				<tr>
					<td class="text-left">
						<a href="index.php" class="btn btn-warning" role="button">KEMBALI</a>
					</td>
				</tr>
			</table>

			<script type="text/javascript">
				setInterval(() => {
					window.location.reload();

					setInterval(function() {

						$.ajax({
							url: 'http://localhost/skordigital/juritgr/api.php',
							data: {
								'a': 'get_data_view_tunggal'
							},
							type: "GET",
							success: function(obj) {
								$('#jadwaltunggal').html(obj);

								console.log('Request ... Done');
							}
						});
					}, 20000);
				}, 10000);
			</script>
		</div>
	</div>

	<?php
	for ($i = 1; $i <= count($datpertandingn); $i++) { ?>
		<!-- Modal -->
		<div class="modal fade" id="exampleModal<?= $i - 1 ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel">Detail pertandingan</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<dov class="row justify-content-center">
							<?php if ($i == count($datpertandingn)) { ?>
								<div class="col-sm-12" style="background-color: red; height: 30dvh;">
								<?php } else { ?>
									<div class="col-sm-6" style="background-color: red; height: 30dvh;">
									<?php } ?>
									<td>
										<table width="343" height="28" border="0">
											<tbody>
												<tr>
													<th width="52" bgcolor="#663399" scope="row">
														<div align="middle"><img src="ipsi.png" align="middle" width="42" height="40"></div>
													</th>
													<td width="281" bgcolor="#663399"><strong>
															<font size="3" color="#FFFF00">PENCAK SILAT TUNGGAL</font>
														</strong></td>
												</tr>
											</tbody>
										</table>
										<table width="343" height="28" border="0">
											<tbody>
												<tr>
													<th width="50" scope="row"><img src="logotgr.png" width="50" height="38" align="middle"></th>
													<td width="283" bgcolor="#996699"><strong>
															<font size="2" color="#FFFFFF">

																<?= $datpertandingn[$i - 1]["nama"] ?>

															</font>
														</strong></td>
												</tr>
											</tbody>
										</table>
										<div align="center">TOTAL SCORE<br>
											<table width="138" border="0">
												<tbody>
													<tr>
														<td height="79">
															<div align="center" style="background-color:#FFFF00"><strong>
																	<font size="10">

																		<?= number_format($datpertandingn[$i - 1]["totalNilai"], 2) ?>

																	</font>
																</strong></div>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</td>
									</div>
									<?php
									if ($i !== count($datpertandingn)) {
									?>
										<div class="col-sm-6" style="background-color: blue;height: 30dvh;">
											<td>
												<table width="343" height="28" border="0">
													<tbody>
														<tr>
															<th width="52" bgcolor="#663399" scope="row">
																<div align="middle"><img src="ipsi.png" align="middle" width="42" height="40"></div>
															</th>
															<td width="281" bgcolor="#663399"><strong>
																	<font size="3" color="#FFFF00">PENCAK SILAT TUNGGAL</font>
																</strong></td>
														</tr>
													</tbody>
												</table>
												<table width="343" height="28" border="0">
													<tbody>
														<tr>
															<th width="50" scope="row"><img src="logotgr.png" width="50" height="38" align="middle"></th>
															<td width="283" bgcolor="#996699"><strong>
																	<font size="2" color="#FFFFFF">

																		<?= $datpertandingn[$i]["nama"] ?>

																	</font>
																</strong></td>
														</tr>
													</tbody>
												</table>
												<div align="center">TOTAL SCORE<br>
													<table width="138" border="0">
														<tbody>
															<tr>
																<td height="79">
																	<div align="center" style="background-color:#FFFF00"><strong>
																			<font size="10">
																				<?= number_format($datpertandingn[$i]["totalNilai"], 2) ?>
																			</font>
																		</strong></div>
																</td>
															</tr>
														</tbody>
													</table>
												</div>
											</td>
										</div>
									<?php } ?>
						</dov>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>


</body>

</html>