<?php
/*
mysql_connect("localhost","root","") or
 die('Could not connect to the database!');


mysql_select_db("bankdatasho") or
 die('No database selected!'); */

$koneksi = mysqli_connect("localhost", "garinpoi_root", "garinpoi_POIN123", "garinpoi_skordigital");
// Check connection
if (mysqli_connect_errno()) {
	echo "Koneksi ke database gagal : " . mysqli_connect_error();
}
