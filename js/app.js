
 
var myapp = new Vue({
    el:"#app",
    data:{
        listJadwalKuliah : null,
        selectJurusan : null,
        selectProdi:null,
        selectedSchedule:null,
        selectDosen:null,
        selectRuangan:null,
        selectHour:null,
        selectDay:null,
        inputFilterRoom: null,
        isCariDosen:false,
        isCariRuangan:false,
        showRoomEmpty:false,
        isLoading:"",
        view:"",
        treefindingDosen :[],
        days : ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
        sesiMasuk : [
            '08.00 – 08.50',
            '08.50 – 09.40',
            '09.50 – 10.40',
            '10.40 – 11.30',
            '11.40 – 12.30',
            '12.30 – 13.30',
            '13.30 – 14.20',
            '14.20 – 15.10',
            '15.10 – 16.00',
            '16.00 – 16.50',
        ],
        dataRoomUsed : {},
        
    },
    created:function(){
        this.isLoading = "Loading cuuk...."

        this.getJson();
    },
    methods: {
        scrollElement:function(idName){
            var elmnt = document.getElementById(idName);
            elmnt.scrollIntoView();
        },
        getJson: function(){
            var _this = this;
            $.getJSON('data_jadwal_kuliah.json',  function(jadwalKuliah){
                _this.listJadwalKuliah = jadwalKuliah;
                console.log(jadwalKuliah);
                _this.isLoading = "";
                _this.listJadwalKuliah.dosen.sort();
                _this.listJadwalKuliah.ruangan.sort();
            });

        },
        showSchedules:function(){
            this.view = 'getSchedule';
            
            // alert('berhasil');
            this.scrollElement("table_matkul")
        },
        getSchedules:function(){
            //ambil daftar mata kuliah
            //lalu jadikan table
            var newData = [];
            var indexDay = 0;
            for (const hari in this.selectedSchedule) {
                if (this.selectedSchedule.hasOwnProperty(hari)) {
                    const matkulHari = this.selectedSchedule[hari];
                    var indexJam = 0;
                    for (const jam in matkulHari) {
                        if (matkulHari.hasOwnProperty(jam)) {
                            const matakuliah = matkulHari[jam];
                            if(newData[indexJam] == null){
                                newData[indexJam] = [];
                            }
                            if(newData[indexJam][indexDay] == null){
                                newData[indexJam][indexDay] = {
                                    'matkul' : matakuliah[0],
                                    'jam' : jam
                                };
                            }
                        }
                        indexJam++;
                    }

                    indexDay++;
                }
            }
            
            return newData;
        },
        cariData:function(keySearch, value){
            this.treefindingDosen = [];
            this.view = 'isCariDosen';

            for (const jurusan in this.listJadwalKuliah.jurusan) {
                if (this.listJadwalKuliah.jurusan.hasOwnProperty(jurusan)) {
                    const prodi = this.listJadwalKuliah.jurusan[jurusan];
                    for (const everyProdi in prodi) {
                        if (prodi.hasOwnProperty(everyProdi)) {
                            const prodiTerpilih = prodi[everyProdi];
                            
                            for (const kelas in prodiTerpilih) {
                                if (prodiTerpilih.hasOwnProperty(kelas)) {
                                    const kelasTerpilih = prodiTerpilih[kelas];
                                    var semester = kelasTerpilih.semester;
                                    var matkul = kelasTerpilih.matkul;

                                    for (const hari in matkul) {
                                        if (matkul.hasOwnProperty(hari)) {
                                            const matkulHarian = matkul[hari];
                                            
                                            for (const jam in matkulHarian) {
                                                if (matkulHarian.hasOwnProperty(jam)) {
                                                    const dataMatkul = matkulHarian[jam][0];

                                                    if(dataMatkul[keySearch] == value){
                                                        // console.log('ketemu');
                                                        this.treefindingDosen.push({
                                                            'jurusan' : jurusan,
                                                            'prodi' : everyProdi,
                                                            'semester' : semester,
                                                            'hari' : hari,
                                                            'jam' : jam,
                                                            'matakuliah' : dataMatkul.matakuliah,
                                                            'dosen' : dataMatkul.dosen,
                                                            'ruangan' : dataMatkul.ruang
                                                        });
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

            }
            this.scrollElement("table_search");
        },
        cariRuanganKosong:function (){
            this.view = 'isCariRuangan';
            // if(this.dataRoomUsed.length == 0){
                this.listJadwalKuliah.ruangan.sort();
                for (let i = 0; i < this.listJadwalKuliah.ruangan.length; i++) {
                    var ruanganN = this.listJadwalKuliah.ruangan[i];
                    this.dataRoomUsed[ruanganN] = {
                        'ruangan' : ruanganN,
                        'isFIlled' : false,
                        'class' : null
                    };
                }
            // }
            //LOOPING AGAYN
            for (const jurusan in this.listJadwalKuliah.jurusan) {
                if (this.listJadwalKuliah.jurusan.hasOwnProperty(jurusan)) {
                    const prodi = this.listJadwalKuliah.jurusan[jurusan];
                    for (const everyProdi in prodi) {
                        if (prodi.hasOwnProperty(everyProdi)) {
                            const prodiTerpilih = prodi[everyProdi];
                            
                            for (const kelas in prodiTerpilih) {
                                if (prodiTerpilih.hasOwnProperty(kelas)) {
                                    const kelasTerpilih = prodiTerpilih[kelas];
                                    var semester = kelasTerpilih.semester;
                                    var matkul = kelasTerpilih.matkul;
                                    if(matkul.hasOwnProperty(this.selectDay)){
                                        if(matkul[this.selectDay].hasOwnProperty(this.selectHour)){
                                            var matkulJamItu = matkul[this.selectDay][this.selectHour][0];
                                            if(matkulJamItu.ruang != null){
                                                console.log(matkulJamItu.ruang+" Terisi");
                                                this.dataRoomUsed[matkulJamItu.ruang] = {
                                                    'isFilled' : true,
                                                    'class' : jurusan+" - "+everyProdi+" - "+semester
                                                };
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            this.scrollElement("list_ruangan");
        },
        
          
    },
    computed:{
        filterRuangan:function(){
            newData = {};
            if(this.inputFilterRoom != null){
                for (const ruangan in this.dataRoomUsed) {
                    if (this.dataRoomUsed.hasOwnProperty(ruangan)) {
                        const data = this.dataRoomUsed[ruangan];
                        if(ruangan.match(new RegExp("\\b"+this.inputFilterRoom+".*", "i"))){
                            if(this.showRoomEmpty){
                                console.log("check");
                                if(data.isFIlled == false){
                                    newData[ruangan] = data;
                                }
                            }else{
                                newData[ruangan] = data;
                            }
                            
                        }
                    }
                }
            }else{
                if(this.showRoomEmpty){
                    for (const ruangan in this.dataRoomUsed) {
                        if (this.dataRoomUsed.hasOwnProperty(ruangan)) {
                            const data = this.dataRoomUsed[ruangan];
                            if(data.isFIlled == false){
                                newData[ruangan] = data;
                            }
                                
                        }
                    }
                }else{
                    newData = this.dataRoomUsed;
                }
            }
            return newData;
        }
    },
    mounted(){
        console.log('hello world');
        
    }
});
