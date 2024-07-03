<?php
include "../backend/includes/connection.php";
givesolution();

function givesolution()
{
?>
    <table class="table table-bordered">
        <?php
        include "../backend/includes/connection.php";
        $avgKB = 0;
        $avgKM = 0;
        $avgHukuman = 0;
        $datpertandingn = [];
        $iter = 0;
        ?>
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
                            $kemantapan = mysqli_query($koneksi, "SELECT kemantapan FROM nilai_tunggal WHERE id_juri=" . $juri['id_juri'] . " AND id_jadwal=" . $jadwal['id_partai']);
                            $row2 = mysqli_fetch_row($kemantapan);
                            if (abs($kebenaran) + abs($row2[0]) == 0.1) {
                                $kebenaran = 0;
                            } else if ($kebenaran != 0) {
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
                    $alter = [];
                    $loter = 0;
                    for ($i = 1; $i <= 5; $i++) {
                        $loter += $alter[$i] = $array_nilai[$i]['kebenaran'] + $array_nilai[$i]['kemantapan'] - $array_nilai[$i]['hukuman'];
                    }
                    $totalNilai = ($loter - min($alter) - max($alter)) / 3;
                    $datpertandingn[$iterasi]["totalNilai"] = $totalNilai;
                    // $iterasi++;
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
                                                <font size="10" id="toss<?= $iterasi++ ?>">
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
<?php } ?>