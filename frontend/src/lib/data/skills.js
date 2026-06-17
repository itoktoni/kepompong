import { get } from 'svelte/store'
import { skills as skillsStore } from '../stores/authStore.js'

const fallbackSkills = [
  { key: 'bersyukur', emoji: '🤲', title: 'Bersyukur', desc: 'Anak belajar mengucap syukur atas nikmat kecil.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['spiritual'], color: '#4CAF50', bg: '#E8F5E9',
    evaluasi: ['Apakah anak mengucap "Alhamdulillah" saat menerima sesuatu?', 'Apakah anak tidak mudah mengeluh tentang makanan atau mainan?', 'Apakah anak bisa menghargai pemberian orang lain?', 'Apakah anak senang berbagi dengan teman?', 'Apakah anak mengucap terima kasih tanpa diminta?'] },
  { key: 'jujur', emoji: '🤝', title: 'Jujur', desc: 'Anak belajar berkata jujur meskipun sulit.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['spiritual'], color: '#4CAF50', bg: '#E8F5E9',
    evaluasi: ['Apakah anak mengakui kesalahan tanpa takut dihukum?', 'Apakah anak bercerita dengan jujur tentang kegiatannya?', 'Apakah anak tidak mengambil barang orang lain?', 'Apakah anak berani mengatakan kebenaran meskipun sulit?', 'Apakah anak tidak berbohong untuk menghindari masalah?'] },
  { key: 'peduli_sesama', emoji: '💛', title: 'Peduli Sesama', desc: 'Anak belajar membantu orang di sekitarnya.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['spiritual'], color: '#4CAF50', bg: '#E8F5E9',
    evaluasi: ['Apakah anak menolong teman yang kesulitan?', 'Apakah anak peduli dengan perasaan orang lain?', 'Apakah anak mau berbagi mainan atau makanan?', 'Apakah anak senang membantu pekerjaan rumah?', 'Apakah anak menunjukkan kasih sayang pada hewan?'] },
  { key: 'menghormati_ortu', emoji: '🙏', title: 'Menghormati Orang Tua', desc: 'Anak belajar sopan dan patuh.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['spiritual'], color: '#4CAF50', bg: '#E8F5E9',
    evaluasi: ['Apakah anak menggunakan kata "tolong" dan "terima kasih"?', 'Apakah anak mendengarkan nasihat orang tua?', 'Apakah anak tidak membantah saat dinasehati?', 'Apakah anak senang membantu orang tua?', 'Apakah anak meminta izin sebelum melakukan sesuatu?'] },
  { key: 'tidak_mudah_menyerah', emoji: '💪', title: 'Tidak Mudah Menyerah', desc: 'Anak belajar mencoba lagi saat gagal.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['karakter'], color: '#FF9800', bg: '#FFF3E0',
    evaluasi: ['Apakah anak terus mencoba saat gagal?', 'Apakah anak tidak mudah marah saat kesulitan?', 'Apakah anak mau belajar hal baru meskipun sulit?', 'Apakah anak tidak langsung menyerah saat tugas berat?', 'Apakah anak termotivasi untuk memperbaiki kesalahan?'] },
  { key: 'berani_bicara', emoji: '🗣', title: 'Berani Bicara', desc: 'Anak belajar menyampaikan pendapat.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['karakter'], color: '#FF9800', bg: '#FFF3E0',
    evaluasi: ['Apakah anak berani menyampaikan pendapat?', 'Apakah anak tidak malu bertanya di depan kelas?', 'Apakah anak bisa menjelaskan keinginannya dengan jelas?', 'Apakah anak berani berbicara dengan orang baru?', 'Apakah anak tidak takut tampil di depan umum?'] },
  { key: 'berani_mencoba', emoji: '🔥', title: 'Berani Mencoba', desc: 'Anak belajar menghadapi tantangan baru.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['karakter'], color: '#FF9800', bg: '#FFF3E0',
    evaluasi: ['Apakah anak mau mencoba makanan baru?', 'Apakah anak tertarik dengan kegiatan yang belum pernah dicoba?', 'Apakah anak tidak takut mencoba permainan baru?', 'Apakah anak mau mencoba sendiri sebelum minta bantuan?', 'Apakah anak senang dengan tantangan baru?'] },
  { key: 'menyelesaikan_tugas', emoji: '🎯', title: 'Menyelesaikan Tugas', desc: 'Anak belajar bertanggung jawab.', ages: [4, 5, 6, 7, 8, 9, 10, 11], pilars: ['karakter'], color: '#FF9800', bg: '#FFF3E0',
    evaluasi: ['Apakah anak menyelesaikan pekerjaan yang dimulai?', 'Apakah anak tidak meninggalkan mainan begitu saja?', 'Apakah anak bisa fokus pada satu tugas sampai selesai?', 'Apakah anak ingat tanggung jawabnya tanpa diingatkan?', 'Apakah anak merasa puas saat menyelesaikan sesuatu?'] },
  { key: 'berpikir_kreatif', emoji: '🎨', title: 'Berpikir Kreatif', desc: 'Anak belajar menemukan ide baru.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kreatifitas'], color: '#2196F3', bg: '#E3F2FD',
    evaluasi: ['Apakah anak suka menggambar atau membuat kerajinan?', 'Apakah anak punya imajinasi yang kreatif saat bermain?', 'Apakah anak bisa menemukan solusi unik untuk masalah?', 'Apakah anak senang bereksperimen dengan bahan baru?', 'Apakah anak suka bercerita atau membuat cerita?'] },
  { key: 'eksperimen', emoji: '🔬', title: 'Eksperimen', desc: 'Anak belajar mencoba hal baru dengan rasa ingin tahu.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kreatifitas'], color: '#2196F3', bg: '#E3F2FD',
    evaluasi: ['Apakah anak bertanya "mengapa" tentang hal di sekitarnya?', 'Apakah anak suka mencoba dan mengamati hasilnya?', 'Apakah anak senang dengan percobaan sederhana?', 'Apakah anak rasa ingin tahu yang tinggi?', 'Apakah anak suka mengamati alam dan hewan?'] },
  { key: 'memecahkan_masalah', emoji: '🧩', title: 'Memecahkan Masalah', desc: 'Anak belajar mencari solusi sendiri.', ages: [4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kreatifitas'], color: '#2196F3', bg: '#E3F2FD',
    evaluasi: ['Apakah anak bisa menyelesaikan puzzle atau teka-teki?', 'Apakah anak berpikir sebelum bertindak?', 'Apakah anak mencari cara sendiri untuk mengatasi kesulitan?', 'Apakah anak bisa menyusun strategi sederhana?', 'Apakah anak belajar dari kesalahan sebelumnya?'] },
  { key: 'berimajinasi', emoji: '✨', title: 'Berimajinasi', desc: 'Anak belajar menggunakan daya imajinasinya.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kreatifitas'], color: '#2196F3', bg: '#E3F2FD',
    evaluasi: ['Apakah anak suka bermain peran (dokter, guru, dll)?', 'Apakah anak bisa membuat cerita dari imajinasinya?', 'Apakah anak punya teman khayalan atau dunia imajinasi?', 'Apakah anak suka bermain dengan benda seolah-olah benda lain?', 'Apakah anak senang mendengarkan atau membaca dongeng?'] },
  { key: 'fokus', emoji: '🎯', title: 'Fokus', desc: 'Anak belajar berkonsentrasi pada satu hal.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['disiplin'], color: '#9C27B0', bg: '#F3E5F5',
    evaluasi: ['Apakah anak bisa duduk tenang selama 15 menit?', 'Apakah anak tidak mudah terganggu saat mengerjakan sesuatu?', 'Apakah anak bisa mendengarkan cerita sampai selesai?', 'Apakah anak fokus saat mengerjakan tugas sekolah?', 'Apakah anak tidak sering melamun saat diajak bicara?'] },
  { key: 'atur_waktu', emoji: '⏰', title: 'Atur Waktu', desc: 'Anak belajar mengelola waktunya.', ages: [4, 5, 6, 7, 8, 9, 10, 11], pilars: ['disiplin'], color: '#9C27B0', bg: '#F3E5F5',
    evaluasi: ['Apakah anak tahu kapan waktunya belajar dan bermain?', 'Apakah anak tidak terlambat bangun tidur?', 'Apakah anak bisa menyelesaikan tugas tepat waktu?', 'Apakah anak tidak terlalu lama menonton layar?', 'Apakah anak punya rutinitas harian yang teratur?'] },
  { key: 'rutin_belajar', emoji: '📖', title: 'Rutin Belajar', desc: 'Anak belajar konsisten belajar setiap hari.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['disiplin'], color: '#9C27B0', bg: '#F3E5F5',
    evaluasi: ['Apakah anak mau belajar tanpa disuruh?', 'Apakah anak punya jadwal belajar yang konsisten?', 'Apakah anak senang membaca buku?', 'Apakah anak mau mengulang pelajaran yang sudah dipelajari?', 'Apakah anak menunjukkan kemajuan dalam belajar?'] },
  { key: 'patuh_aturan', emoji: '✅', title: 'Patuh Aturan', desc: 'Anak belajar mengikuti kesepakatan.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['disiplin'], color: '#9C27B0', bg: '#F3E5F5',
    evaluasi: ['Apakah anak mengikuti aturan di rumah?', 'Apakah anak patuh di sekolah?', 'Apakah anak tidak melanggar kesepakatan yang sudah dibuat?', 'Apakah anak mengerti konsekuensi dari perbuatannya?', 'Apakah anak bisa menunggu giliran?'] },
  { key: 'masak_sederhana', emoji: '🍳', title: 'Masak Sederhana', desc: 'Anak belajar menyiapkan makanan sendiri.', ages: [5, 6, 7, 8, 9, 10, 11], pilars: ['kemandirian'], color: '#3F51B5', bg: '#E8EAF6',
    evaluasi: ['Apakah anak mau membantu di dapur?', 'Apakah anak bisa menyiapkan makanan sederhana?', 'Apakah anak berhati-hati saat menggunakan alat dapur?', 'Apakah anak senang mencoba resep baru?', 'Apakah anak bisa membersihkan setelah memasak?'] },
  { key: 'beres_beres', emoji: '🧹', title: 'Beres-beres', desc: 'Anak belajar merapikan barangnya.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kemandirian'], color: '#3F51B5', bg: '#E8EAF6',
    evaluasi: ['Apakah anak merapikan mainan setelah bermain?', 'Apakah anak bisa merapikan tempat tidurnya sendiri?', 'Apakah anak tidak meninggalkan barang sembarangan?', 'Apakah anak mau membantu merapikan rumah?', 'Apakah anak tahu di mana barang-barangnya disimpan?'] },
  { key: 'kebersihan_diri', emoji: '🚿', title: 'Kebersihan Diri', desc: 'Anak belajar mandi dan sikat gigi sendiri.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kemandirian'], color: '#3F51B5', bg: '#E8EAF6',
    evaluasi: ['Apakah anak bisa mandi sendiri?', 'Apakah anak sikat gigi tanpa disuruh?', 'Apakah anak cuci tangan sebelum makan?', 'Apakah anak bisa memilih baju sendiri?', 'Apakah anak menjaga kebersihan pakaiannya?'] },
  { key: 'mengatur_uang', emoji: '💰', title: 'Mengatur Uang', desc: 'Anak belajar menabung dan berhemat.', ages: [6, 7, 8, 9, 10, 11], pilars: ['kemandirian'], color: '#3F51B5', bg: '#E8EAF6',
    evaluasi: ['Apakah anak punya celengan atau tabungan?', 'Apakah anak tidak boros jajan?', 'Apakah anak bisa membedakan kebutuhan dan keinginan?', 'Apakah anak senang menabung?', 'Apakah anak menghargai uang?'] },
  { key: 'berbagi', emoji: '🎁', title: 'Berbagi', desc: 'Anak belajar membagi dengan orang lain.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['sosial'], color: '#8D6E63', bg: '#EFEBE9',
    evaluasi: ['Apakah anak mau berbagi mainan dengan teman?', 'Apakah anak senang memberi makanan ke orang lain?', 'Apakah anak tidak pelit dengan barangnya?', 'Apakah anak bisa bermain gantian?', 'Apakah anak senang membantu teman yang membutuhkan?'] },
  { key: 'kerja_sama', emoji: '👫', title: 'Kerja Sama', desc: 'Anak belajar bekerja sama dalam tim.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['sosial'], color: '#8D6E63', bg: '#EFEBE9',
    evaluasi: ['Apakah anak bisa bermain bersama tanpa bertengkar?', 'Apakah anak mau bergantian saat bermain?', 'Apakah anak bisa mengikuti aturan kelompok?', 'Apakah anak senang bekerja sama dalam tim?', 'Apakah anak tidak mendominasi saat bermain?'] },
  { key: 'mendengarkan', emoji: '👂', title: 'Mendengarkan', desc: 'Anak belajar mendengarkan orang lain.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['sosial'], color: '#8D6E63', bg: '#EFEBE9',
    evaluasi: ['Apakah anak mendengarkan saat orang lain bicara?', 'Apakah anak tidak memotong pembicaraan?', 'Apakah anak bisa mengikuti instruksi sederhana?', 'Apakah anak memperhatikan saat guru menjelaskan?', 'Apakah anak tidak cuek saat diajak bicara?'] },
  { key: 'empati', emoji: '💗', title: 'Empati', desc: 'Anak belajar memahami perasaan orang lain.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['sosial'], color: '#8D6E63', bg: '#EFEBE9',
    evaluasi: ['Apakah anak bisa merasakan kesedihan teman?', 'Apakah anak menolong teman yang menangis?', 'Apakah anak tidak mengejek orang lain?', 'Apakah anak peduli dengan perasaan adik/kakak?', 'Apakah anak senang menghibur orang yang sedih?'] },
  { key: 'mengenali_emosi', emoji: '😊', title: 'Mengenali Emosi', desc: 'Anak belajar mengenal perasaannya.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['emosi'], color: '#F44336', bg: '#FFEBEE',
    evaluasi: ['Apakah anak bisa menyebutkan perasaannya?', 'Apakah anak tahu kapan ia sedih atau senang?', 'Apakah anak bisa mengenali wajah marah dan senang?', 'Apakah anak bercerita tentang perasaannya?', 'Apakah anak tidak bingung dengan emosinya?'] },
  { key: 'mengelola_marah', emoji: '😤', title: 'Mengelola Marah', desc: 'Anak belajar menenangkan diri.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['emosi'], color: '#F44336', bg: '#FFEBEE',
    evaluasi: ['Apakah anak tidak mudah marah berlebihan?', 'Apakah anak bisa menenangkan diri sendiri?', 'Apakah anak tidak memukul saat marah?', 'Apakah anak bisa mengungkapkan marah dengan kata-kata?', 'Apakah anak tidak merusak barang saat kesal?'] },
  { key: 'quality_time', emoji: '👨‍👩‍👧', title: 'Quality Time', desc: 'Anak belajar menikmati waktu bersama keluarga.', ages: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['emosi'], color: '#F44336', bg: '#FFEBEE',
    evaluasi: ['Apakah anak senang menghabiskan waktu dengan keluarga?', 'Apakah anak tidak lebih memilih gadget daripada keluarga?', 'Apakah anak mau ikut kegiatan keluarga?', 'Apakah anak bercerita tentang harinya?', 'Apakah anak terlihat bahagia saat bersama keluarga?'] },
  { key: 'komunikasi_keluarga', emoji: '💬', title: 'Komunikasi Keluarga', desc: 'Anak belajar bercerita ke orang tua.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['emosi'], color: '#F44336', bg: '#FFEBEE',
    evaluasi: ['Apakah anak bercerita tentang kegiatannya di sekolah?', 'Apakah anak tidak menyimpan masalah sendiri?', 'Apakah anak berani bilang jika ada masalah?', 'Apakah anak mau menceritakan perasaannya?', 'Apakah anak tidak takut bicara dengan orang tua?'] },
  { key: 'olahraga_teratur', emoji: '🏃', title: 'Olahraga Teratur', desc: 'Anak belajar aktif bergerak setiap hari.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kesehatan'], color: '#009688', bg: '#E0F2F1',
    evaluasi: ['Apakah anak aktif bergerak setiap hari?', 'Apakah anak senang bermain di luar?', 'Apakah anak tidak malas bergerak?', 'Apakah anak bisa berlari dan melompat dengan baik?', 'Apakah anak punya olahraga favorit?'] },
  { key: 'makan_sehat', emoji: '🥗', title: 'Makan Sehat', desc: 'Anak belajar memilih makanan bergizi.', ages: [2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kesehatan'], color: '#009688', bg: '#E0F2F1',
    evaluasi: ['Apakah anak mau makan sayur dan buah?', 'Apakah anak tidak terlalu banyak makanan manis?', 'Apakah anak sarapan sebelum beraktivitas?', 'Apakah anak minum air putih yang cukup?', 'Apakah anak tidak terlalu sering jajan sembarangan?'] },
  { key: 'tidur_cukup', emoji: '😴', title: 'Tidur Cukup', desc: 'Anak belajar tidur tepat waktu.', ages: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kesehatan'], color: '#009688', bg: '#E0F2F1',
    evaluasi: ['Apakah anak tidur tepat waktu?', 'Apakah anak tidak sering begadang?', 'Apakah anak bangun dengan segar di pagi hari?', 'Apakah anak punya rutinitas tidur yang teratur?', 'Apakah anak tidak terlalu lama main gadget sebelum tidur?'] },
  { key: 'kebersihan_lingkungan', emoji: '🌿', title: 'Kebersihan Lingkungan', desc: 'Anak belajar menjaga kebersihan sekitar.', ages: [3, 4, 5, 6, 7, 8, 9, 10, 11], pilars: ['kesehatan'], color: '#009688', bg: '#E0F2F1',
    evaluasi: ['Apakah anak tidak membuang sampah sembarangan?', 'Apakah anak mau membersihkan lingkungan?', 'Apakah anak peduli dengan kebersihan taman?', 'Apakah anak tahu cara memilah sampah?', 'Apakah anak senang menanam tanaman?'] }
]

export const skills = fallbackSkills

export function getSkills() {
  const storeData = get(skillsStore)
  return storeData.length ? storeData : fallbackSkills
}

export function filterSkills({ pilarKey, childAge, childAgama, planId } = {}) {
  const list = getSkills()
  return list.filter(s => {
    const pilarOk = !pilarKey || (s.pilars && s.pilars.includes(pilarKey))
    const ageOk = childAge == null || (s.ages && s.ages.some(a => Number(a) === Number(childAge)))
    const agamaOk = !s.agama || !s.agama.length || !childAgama || s.agama.includes(childAgama)
    const planOk = !s.plans || !s.plans.length || !planId || s.plans.includes(planId)
    return pilarOk && ageOk && agamaOk && planOk
  })
}

export function getSkillsByPilar(pilarKey, childAge, childAgama, planId) {
  return filterSkills({ pilarKey, childAge, childAgama, planId })
}

export function getEvaluasi(skillKey) {
  return getSkills().find(s => s.key === skillKey) || null
}
