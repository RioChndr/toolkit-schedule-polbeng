<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mata Kuliah</title>
    <style>
        .text-green{
            color:green;
        }
        .text-red{
            color:red;
        }
        a{
            color:inherit !important;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
    <link rel="stylesheet" href="css/bootstrap.min.css">

</head>
<body>
    <div id="app">
        {{isLoading}}
        <div class="container mt-3 mb-3">
            <div class="row">
                <div class="col-md-6">

                    <div class='cari_jadwal'>
                        <h1 class='text-center mb-3'>
                            Awesome App
                        </h1>
                        <hr>
                        <h2 class='text-center'>Jadwal Kuliah</h2>
                        Jurusan : 
                        <select class='form-control' v-model='selectJurusan'>
                            <option v-for='(value, jurusan) in listJadwalKuliah.jurusan' :value='jurusan'> {{jurusan}}</option>
                        </select> <br>
                        <div v-if='selectJurusan'>
                            Program Studi :
                            <select class='form-control' v-model='selectProdi' >
                                <option v-for='(value, prodi) in listJadwalKuliah.jurusan[selectJurusan]' :value='prodi'>{{prodi}}</option>
                            </select> <br>
                        </div>
                        <div v-if='selectProdi'>
                            Semester :
                            <select class='form-control' v-model='selectedSchedule' >
                                <option v-for='(data) in listJadwalKuliah.jurusan[selectJurusan][selectProdi]' :value='data.matkul'>{{data.semester}}</option>
                            </select> <br>
                            <button class="btn btn-primary" @click="showSchedules()" :disabled='!selectedSchedule'>Cari</button>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <hr>
                    <h3 class="text-center">
                        Some Filter to help you..
                    </h3>
                    <div class="cari_dosen">
                        Cari Dosen :
                        <!-- <input v-model='selectDosen' list='listDosen'> -->
                        <div class="input-group mb-3 w-100">
                            <select class='form-control' id='listDosen' v-model='selectDosen'>
                                <option v-for='(dosen) in listJadwalKuliah.dosen' :value='dosen'>{{dosen}}</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" @click="cariData('dosen', selectDosen)" :disabled='!selectDosen'>Cari</button>
                            </div>
                        </div>
                    </div>

                    <div class="cari_ruangan">
                        Cari Ruangan : 
                        <div class="input-group mb-3">
                            <select class='form-control' id='listRuangan' v-model='selectRuangan'>
                                <option v-for='(ruangan) in listJadwalKuliah.ruangan' :value='ruangan'>{{ruangan}}</option>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="button" @click="cariData('ruang', selectRuangan)" :disabled='!selectRuangan'>Cari</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="cari_ruangan_kosong mt-3">
                        Cari ruangan kosong di Hari : 
                        <select class='form-control' v-model='selectDay'>
                            <option v-for='day in days' :value='day'>{{day}}</option>
                        </select> 
                        pada jam 
                        <select class='form-control' v-model='selectHour'>
                            <option v-for='hour in sesiMasuk' :value='hour'>
                                {{hour}}
                            </option>
                        </select>
                        <button class='btn btn-primary mt-3' @click='cariRuanganKosong()' :disabled='!selectDay || !selectHour'>Cari Ruangan !</button>
                    </div>
                </div>
            </div>
            <div v-if="view == 'isCariRuangan'" id='list_ruangan' class='mt-3'>
                    Status Ruangan Kampus : <br>
                    <input type="text" v-model='inputFilterRoom' placeholder="Filter ruangan ...">
                    <!-- {{dataRoomUsed}} -->
                    <ul>
                        <li v-for='(val, namaRuangan, index) in filterRuangan'>
                            {{namaRuangan}}  : 
                            <span v-if='val.isFilled == true' class='badge badge-danger'>Terisi</span> <span v-else class='badge badge-success'>Kosong</span>
                            <span class="text-muted">
                                {{val.class}}
                            </span>
                        </li>
                    </ul>
                </div>
        </div>
      <div >
          <table class='table' id="table_matkul" border=1 v-if="view == 'getSchedule'" style='font-size:12px;'>
              <thead class='thead-dark'>
                  <tr>
                      <!-- <td rowspan="2">Jam</td> -->
                      <th v-for='(day, i) in days' colspan="4">{{day}} </th>
                  </tr>
                  <tr>
                      <template  v-for='n in 5'>
                          <th>Jam</th>
                          <th>Mata Kuliah</th>
                          <th>Dosen</th>
                          <th>Ruangan</th>
                      </template >
                  </tr>
              </thead>
              <tbody>
                  <tr v-for='row in getSchedules()'>
                      <template v-for='col in row'>
                          <td>{{col.jam}}</td>
                          <td>{{col.matkul.matakuliah}}</td>
                          <td>{{col.matkul.dosen}}</td>
                          <td>{{col.matkul.ruang}}</td>
                      </template>
                  </tr>
  
              </tbody>
          </table>
          
          <table class='table' id='table_search' v-if="view == 'isCariDosen'" style='font-size:12px;'>
              <thead class='thead-dark'>
                  <tr>
                      <th>No</th>
                      <th>Jurusan</th>
                      <th>Program Studi</th>
                      <th>Semester</th>
                      <th>Hari</th>
                      <th>Jam</th>
                      <th>Mata kuliah</th>
                      <th>Dosen</th>
                      <th>Ruangan</th>
                  </tr>
              </thead>
              <tr v-for='(value, key) in treefindingDosen'>
                  <td>{{key + 1}}</td>
                  <td>{{value.jurusan}}</td>
                  <td>
                      {{value.prodi}}
                  </td>
                  <td>
                      {{value.semester}}
                  </td>
                  <td>
                      {{value.hari}}
                  </td>
                  <td>
                      {{value.jam}}
                  </td>
                  <td>
                      {{value.matakuliah}}
                  </td>
                  <td>
                      {{value.dosen}}
                  </td>
                  <td>
                      {{value.ruangan}}
                  </td>
                  
              </tr>
          </table>

      </div>
        
       
    </div>

    <div class="footer bg-secondary text-white p-3">
        <div class="text-center">
            <div class=" mb-3">
                <i>
                    Jika aplikasi ini ada kekurangan seperti kesalahan data jadwal, error, atau saran untuk membangun aplikasi ini. silahkan DM saya di IG <a href="http://instagram.com/rio_chndr">@rio_chndr</a>
                    <br>
                    Terima kasih :)
                </i>
            </div>
            Created With ❤ By <a href='http://instagram.com/rio_chndr'>Rio Chandra (Follow me)</a> <br>
        </div>
    </div>
</body>
<script src='js/jquery-3.4.1.min.js'></script>
<script src="js/vue.min.js"></script>
<!-- <script src="js/scrool-toVue.js"></script> -->

<script src='js/app.js'></script>
</html>