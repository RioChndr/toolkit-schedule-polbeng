<?php
include('vendor/autoload.php');
use PhpOffice\PhpSpreadsheet\IOFactory;

$file = isset($_GET['file'])?$_GET['file']:'';
$sheetActive = "TI";
$columnDay = 1; //A
$columnHour = 2;
$rowStartScanning = 3;

$TITLE_SMT = "SMT";
$TITLE_MATKUL = "Mata Kuliah";
$TITLE_RUANGAN = "Ruangan";
$SHEET_SCAN = ["TM", "TE", "TS", "TI", "ADM", "BI"];

$jurusan = [];
foreach ($SHEET_SCAN as $j) {
    $jurusan[] = [
        'jurusan' => $j
    ];
}

$dosen = [];
$ruangan = []; 
$programstudi =[];

$data = [
    'jurusan' => [],
    'dosen' => [],
    'ruangan' => []
];

if(empty($file)){
    echo "tidak ada request file, refresh dengan alamnat ...?file=uploads/test.xls";
    die();
}else{
    if(file_exists($file) && !isset($_GET['update'])){
        $req_url = $_SERVER['REQUEST_URI'];
        echo "data sudah di generate. apakah anda ingin genarate ulang ? <br> <a href='$req_url&update=1'>Update data</a> <br> <a href='data_jadwal_kuliah.json'>File JSON</a>";
        die();
    }
    
}
// echo "file : $file <br>";

//baca file
$spreadsheet = IOFactory::load($file); 

//baca seluruh cell
// $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

//lihat sheet yang ada
$worksheetname = $spreadsheet->getSheetNames();
// echo json_encode($worksheetname);

for ($idJurusan=1; $idJurusan <= count($SHEET_SCAN); $idJurusan++) { 
    $selected_sheet = $SHEET_SCAN[$idJurusan - 1];

    $data['jurusan'][$selected_sheet] = [];
    $data_prodi = [];
    //set active sheet berdasarkan nama
    $spreadsheet->setActiveSheetIndexByName($selected_sheet);

    $worksheet = $spreadsheet->getActiveSheet();

    $highestRow = $worksheet->getHighestRow(); //ambil baris terbanyak
    $highestColumn = $worksheet->getHighestColumn();
    $highestColumnIndex = \PhpOffice\PhpSpreadSheet\Cell\Coordinate::columnIndexFromString($highestColumn);

    for($Column = 1; $Column <= $highestColumnIndex; $Column++){
        //get title from row 2
        $title = $worksheet->getCellByColumnAndRow($Column, 2)->getValue();

        //scanning start when title find "SMT"
        if($title == $TITLE_SMT){
            $prodi = $worksheet->getCellByColumnAndRow($Column, 1)->getValue();
            if(empty($data_prodi[$prodi])){
                $data_prodi[$prodi] = [];
                $programstudi[] = [
                    'program_studi' => $prodi
                ];
            }
            $jadwal = [];
            $jadwal['semester'] = $worksheet->getCellByColumnAndRow($Column, 3)->getValue();;
            $jadwal['matkul'] = [];

            $col_smt = $Column;
            $col_matkul = $Column+1;
            $col_dosen = $Column+2;
            $col_ruang = $Column+3;
            $col_hari = 1;
            $col_jam = 2;

            //point, start scanning by row
            for ($Row=$rowStartScanning; $Row <= $highestRow; $Row++) { 
                $cell_matkul    = $worksheet->getCellByColumnAndRow($col_matkul, $Row)->getValue();
                $cell_dosen     = $worksheet->getCellByColumnAndRow($col_dosen, $Row)->getValue();
                $cell_ruang     = $worksheet->getCellByColumnAndRow($col_ruang, $Row)->getValue();

                $cell_hari      = $worksheet->getCellByColumnAndRow($col_hari, $Row)->getValue();
                $cell_jam       = $worksheet->getCellByColumnAndRow($col_jam, $Row)->getValue();

                if($cell_hari == ""){
                    continue;
                }

                if(empty($jadwal['matkul'][$cell_hari])){
                    $jadwal['matkul'][$cell_hari] = [];
                }
                if(empty($jadwal['matkul'][$cell_hari][$cell_jam])){
                    $jadwal['matkul'][$cell_hari][$cell_jam] = [];
                }
                if($cell_dosen != "" && !in_array($cell_dosen, $dosen)) $dosen[] = $cell_dosen;
                if($cell_ruang != "" && !in_array($cell_ruang, $ruangan)) $ruangan[] = $cell_ruang;

                $jadwalMatkul = [
                    'matakuliah' => $cell_matkul,
                    'dosen' => $cell_dosen,
                    'ruang' => $cell_ruang
                ];
                $jadwal['matkul'][$cell_hari][$cell_jam][] = $jadwalMatkul;
            }
            

            $data_prodi[$prodi][] = $jadwal;
        }
    }
    $data['jurusan'][$selected_sheet] = $data_prodi;
}

$data['dosen'] = $dosen;
$data['ruangan'] = $ruangan;

$fp = fopen('data_jadwal_kuliah.json', 'w');
fwrite($fp, json_encode($data));
fclose($fp);
echo "berhasil menambahkan ke file json, <a href='data_jadwal_kuliah.json'>File JSON</a>";

