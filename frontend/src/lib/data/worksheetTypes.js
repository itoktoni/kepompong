import { get } from 'svelte/store'
import { worksheets as worksheetsStore } from '../stores/authStore.js'

const fallbackWorksheetTypes = [
  { id: 'mewarnai_alfabet', icon: 'mdi:crayon-outline', title: 'Mewarnai Huruf', desc: 'Mewarnai huruf A-Z dengan krayon', age: '1-3', ageLabel: '1-3 thn', ages: [1,2,3], bg: '#E3F2FD', iconColor: '#1565C0' },
  { id: 'mewarnai_angka', icon: 'mdi:counter', title: 'Mewarnai Angka', desc: 'Mewarnai angka 1-10', age: '1-3', ageLabel: '1-3 thn', ages: [1,2,3], bg: '#F3E5F5', iconColor: '#6A1B9A' },
  { id: 'tracing_huruf', icon: 'mdi:pencil-outline', title: 'Mengikuti Garis Huruf', desc: 'Mengikuti garis putus-putus huruf', age: '1-3', ageLabel: '1-3 thn', ages: [1,2,3], bg: '#E8F5E9', iconColor: '#2E7D32' },
  { id: 'tracing_angka', icon: 'mdi:pencil-outline', title: 'Mengikuti Garis Angka', desc: 'Mengikuti garis putus-putus angka', age: '1-3', ageLabel: '1-3 thn', ages: [1,2,3], bg: '#FFF3E0', iconColor: '#E65100' },
  { id: 'garis_zigzag', icon: 'mdi:wave', title: 'Garis Zig Zag', desc: 'Mengikuti garis zigzag dan lengkung', age: '1-3', ageLabel: '1-3 thn', ages: [1,2,3], bg: '#FCE4EC', iconColor: '#C2185B' },
  { id: 'cocokan_warna', icon: 'mdi:target', title: 'Cocokan Warna', desc: 'Mencocokkan warna yang sama', age: '1-3', ageLabel: '1-3 thn', ages: [1,2,3], bg: '#E0F2F1', iconColor: '#00695C' },
  { id: 'menulis_huruf', icon: 'mdi:pencil-outline', title: 'Menulis Huruf', desc: 'Latihan menulis huruf A-Z dengan panduan', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#E3F2FD', iconColor: '#1565C0' },
  { id: 'menulis_angka', icon: 'mdi:square-edit-outline', title: 'Menulis Angka', desc: 'Latihan menulis angka 1-10 dengan panduan', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#F3E5F5', iconColor: '#6A1B9A' },
  { id: 'menulis_kotak', icon: 'mdi:file-document-edit-outline', title: 'Menulis Huruf di Kotak', desc: 'Menulis huruf A-Z berulang dalam kotak', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#FFF3E0', iconColor: '#E65100' },
  { id: 'menulis_angka_kotak', icon: 'mdi:counter', title: 'Menulis Angka di Kotak', desc: 'Menulis angka 1-10 berulang dalam kotak', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#F3E5F5', iconColor: '#6A1B9A' },
  { id: 'menebalkan_huruf', icon: 'mdi:pencil-outline', title: 'Menebalkan Huruf', desc: 'Menebalkan huruf A-Z dengan mengikuti garis putus-putus', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#E8F5E9', iconColor: '#2E7D32' },
  { id: 'menebalkan_angka', icon: 'mdi:counter', title: 'Menebalkan Angka', desc: 'Menebalkan angka 1-10 dengan mengikuti garis putus-putus', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#F3E5F5', iconColor: '#6A1B9A' },
  { id: 'huruf_kapital', icon: 'mdi:alphabet-latin', title: 'Huruf Kapital A-Z', desc: 'Latihan menulis huruf kapital dari A sampai Z', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#E3F2FD', iconColor: '#0D47A1' },
  { id: 'angka_1_10', icon: 'mdi:counter', title: 'Angka 1-10', desc: 'Latihan menulis angka dari 1 sampai 10', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#F3E5F5', iconColor: '#6A1B9A' },
  { id: 'mewarnai_buah', icon: 'mdi:food-apple-outline', title: 'Mewarnai Buah', desc: 'Mewarnai gambar buah-buahan', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#FFF3E0', iconColor: '#E65100' },
  { id: 'mewarnai_hewan', icon: 'mdi:cat', title: 'Mewarnai Hewan', desc: 'Mewarnai gambar hewan', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#E8F5E9', iconColor: '#2E7D32' },
  { id: 'mengenal_warna', icon: 'mdi:palette-outline', title: 'Mengenal Warna', desc: 'Mengenal dan mencocokkan warna dasar', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#FCE4EC', iconColor: '#C2185B' },
  { id: 'mengenal_bentuk', icon: 'mdi:shape-outline', title: 'Mengenal Bentuk', desc: 'Mengenal bentuk dasar: lingkaran, segitiga, persegi', age: '3-5', ageLabel: '3-5 thn', ages: [3,4,5], bg: '#E0F2F1', iconColor: '#00695C' },
  { id: 'penjumlahan', icon: 'mdi:plus-circle-outline', title: 'Penjumlahan 1-10', desc: 'Latihan penjumlahan angka 1-10', age: '4-7', ageLabel: '4-7 thn', ages: [4,5,6,7], bg: '#E8F5E9', iconColor: '#2E7D32' },
  { id: 'pengurangan', icon: 'mdi:minus-circle-outline', title: 'Pengurangan 1-10', desc: 'Latihan pengurangan angka 1-10', age: '4-7', ageLabel: '4-7 thn', ages: [4,5,6,7], bg: '#E3F2FD', iconColor: '#1565C0' },
  { id: 'menyalin_kata', icon: 'mdi:square-edit-outline', title: 'Menyalin Kata', desc: 'Menyalin kata-kata sederhana', age: '4-7', ageLabel: '4-7 thn', ages: [4,5,6,7], bg: '#FFF3E0', iconColor: '#E65100' },
  { id: 'pasangan_benda', icon: 'mdi:link-variant', title: 'Pasangan Benda', desc: 'Mencocokkan benda dengan fungsinya', age: '4-7', ageLabel: '4-7 thn', ages: [4,5,6,7], bg: '#F3E5F5', iconColor: '#6A1B9A' },
  { id: 'fill_blanks', icon: 'mdi:pencil-outline', title: 'Lengkapi Huruf', desc: 'Isi huruf yang hilang dalam kata', age: '4-7', ageLabel: '4-7 thn', ages: [4,5,6,7], bg: '#FCE4EC', iconColor: '#C2185B' },
  { id: 'dot_to_dot', icon: 'mdi:link-variant', title: 'Hubungkan Titik', desc: 'Hubungkan titik berurutan untuk membentuk gambar', age: '4-7', ageLabel: '4-7 thn', ages: [4,5,6,7], bg: '#E0F2F1', iconColor: '#00695C' },
  { id: 'math_icons', icon: 'mdi:star-outline', title: 'Berhitung dengan Ikon', desc: 'Penjumlahan & pengurangan dengan gambar bintang', age: '4-7', ageLabel: '4-7 thn', ages: [4,5,6,7], bg: '#E8F5E9', iconColor: '#2E7D32' },
  { id: 'perkalian', icon: 'mdi:multiplication', title: 'Perkalian 1-5', desc: 'Latihan perkalian tabel 1-5', age: '6-9', ageLabel: '6-9 thn', ages: [6,7,8,9], bg: '#E8F5E9', iconColor: '#2E7D32' },
  { id: 'pembagian', icon: 'mdi:division', title: 'Pembagian 1-10', desc: 'Latihan pembagian sederhana', age: '6-9', ageLabel: '6-9 thn', ages: [6,7,8,9], bg: '#E3F2FD', iconColor: '#1565C0' },
  { id: 'menyalin_kalimat', icon: 'mdi:file-document-edit-outline', title: 'Menyalin Kalimat', desc: 'Menyalin kalimat pendek dengan benar', age: '6-9', ageLabel: '6-9 thn', ages: [6,7,8,9], bg: '#FFF3E0', iconColor: '#E65100', isApi: true },
  { id: 'isi_kata', icon: 'mdi:message-text-outline', title: 'Melengkapi Kata', desc: 'Melengkapi kata yang hilang', age: '6-9', ageLabel: '6-9 thn', ages: [6,7,8,9], bg: '#FCE4EC', iconColor: '#C2185B' },
  { id: 'grafik_batang', icon: 'mdi:chart-bar', title: 'Grafik Batang', desc: 'Membaca dan menjawab pertanyaan dari grafik batang', age: '6-9', ageLabel: '6-9 thn', ages: [6,7,8,9], bg: '#E3F2FD', iconColor: '#1565C0' },
  { id: 'word_search', icon: 'mdi:magnify', title: 'Mencari Kata', desc: 'Temukan kata tersembunyi dalam kotak huruf', age: '6-9', ageLabel: '6-9 thn', ages: [6,7,8,9], bg: '#FFF3E0', iconColor: '#E65100' },
  { id: 'maze', icon: 'mdi:flag-checkered', title: 'Labirin', desc: 'Temukan jalan keluar dari labirin', age: '6-9', ageLabel: '6-9 thn', ages: [6,7,8,9], bg: '#F3E5F5', iconColor: '#6A1B9A' },
  { id: 'cerita_pendek', icon: 'mdi:book-open-outline', title: 'Menulis Cerita', desc: 'Menulis cerita pendek dari gambar', age: '7+', ageLabel: '7+ thn', ages: [7,8,9,10,11], bg: '#E8F5E9', iconColor: '#2E7D32' },
  { id: 'rangkuman_buku', icon: 'mdi:bookshelf', title: 'Rangkuman Buku', desc: 'Membuat rangkuman dari buku acak', age: '7+', ageLabel: '7+ thn', ages: [7,8,9,10,11], bg: '#E3F2FD', iconColor: '#1565C0', isApi: true },
  { id: 'soal_cerita', icon: 'mdi:calculator-variant-outline', title: 'Soal Cerita Matematika', desc: 'Menyelesaikan soal cerita matematika', age: '7+', ageLabel: '7+ thn', ages: [7,8,9,10,11], bg: '#F3E5F5', iconColor: '#6A1B9A' },
  { id: 'benda_sekitar', icon: 'mdi:home-outline', title: 'Benda di Sekitar', desc: 'Mengenal dan menulis nama benda sekitar', age: '7+', ageLabel: '7+ thn', ages: [7,8,9,10,11], bg: '#FFF3E0', iconColor: '#E65100' },
  { id: 'geografi', icon: 'mdi:map-outline', title: 'Geografi Indonesia', desc: 'Mencocokkan kota dengan provinsi', age: '7+', ageLabel: '7+ thn', ages: [7,8,9,10,11], bg: '#E3F2FD', iconColor: '#1565C0' }
]

export const worksheetTypes = fallbackWorksheetTypes

export function getWorksheetTypes() {
  const storeData = get(worksheetsStore)
  return storeData.length ? storeData : fallbackWorksheetTypes
}

export function filterWorksheetTypes({ childAge, childAgama, planId } = {}) {
  const list = getWorksheetTypes()
  return list.filter(w => {
    const ageOk = childAge == null || (w.ages && w.ages.includes(childAge))
    const agamaOk = !w.agama || !w.agama.length || !childAgama || w.agama.includes(childAgama)
    const planOk = !w.plans || !w.plans.length || !planId || w.plans.includes(planId)
    return ageOk && agamaOk && planOk
  })
}
