<?php

namespace App\Controllers\Admin;

use App\Models\GuruModel;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;

class DataGuru extends BaseController
{
   protected GuruModel $guruModel;

   protected $guruValidationRules = [
      'nuptk' => [
         'rules' => 'required|max_length[20]|min_length[15]',
         'errors' => [
            'required' => 'NIDN harus diisi.',
            'is_unique' => 'NIDN ini telah terdaftar.',
            'min_length[15]' => 'Panjang NIDN minimal 15 karakter'
         ]
      ],
      'nama' => [
         'rules' => 'required|min_length[3]',
         'errors' => [
            'required' => 'Nama harus diisi'
         ]
      ],
      'jk' => ['rules' => 'required', 'errors' => ['required' => 'Jenis kelamin wajib diisi']],
      'no_hp' => 'required|numeric|max_length[20]|min_length[5]'
   ];

   public function __construct()
   {
      $this->guruModel = new GuruModel();
   }

   public function index()
   {
      $data = [
         'title' => 'Data Dosen',
         'ctx' => 'guru',
      ];

      return view('admin/data/data-guru', $data);
   }

   public function ambilDataGuru()
   {
      $result = $this->guruModel->getAllGuru();

      $data = [
         'data' => $result,
         'empty' => empty($result)
      ];

      return view('admin/data/list-data-guru', $data);
   }

   public function formTambahGuru()
   {
      $data = [
         'ctx' => 'guru',
         'title' => 'Tambah Data Dosen'
      ];

      return view('admin/data/create/create-data-guru', $data);
   }

   public function saveGuru()
   {
      // validasi
      if (!$this->validate($this->guruValidationRules)) {
         $data = [
            'ctx' => 'guru',
            'title' => 'Tambah Data Dosen',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/create/create-data-guru', $data);
      }

      // simpan
      $result = $this->guruModel->createGuru(
         nuptk: $this->request->getVar('nuptk'),
         nama: $this->request->getVar('nama'),
         jenisKelamin: $this->request->getVar('jk'),
         alamat: $this->request->getVar('alamat'),
         noHp: $this->request->getVar('no_hp'),
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Tambah data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menambah data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru/create/');
   }

   public function formEditGuru($id)
   {
      $guru = $this->guruModel->getGuruById($id);

      if (empty($guru)) {
         throw new PageNotFoundException('Data guru dengan id ' . $id . ' tidak ditemukan');
      }

      $data = [
         'data' => $guru,
         'ctx' => 'guru',
         'title' => 'Edit Data Dosen',
      ];

      return view('admin/data/edit/edit-data-guru', $data);
   }

   public function updateGuru()
   {
      $idGuru = $this->request->getVar('id');

      // validasi
      if (!$this->validate($this->guruValidationRules)) {
         $data = [
            'data' => $this->guruModel->getGuruById($idGuru),
            'ctx' => 'guru',
            'title' => 'Edit Data Dosen',
            'validation' => $this->validator,
            'oldInput' => $this->request->getVar()
         ];
         return view('/admin/data/edit/edit-data-guru', $data);
      }

      // update
      $result = $this->guruModel->updateGuru(
         id: $idGuru,
         nuptk: $this->request->getVar('nuptk'),
         nama: $this->request->getVar('nama'),
         jenisKelamin: $this->request->getVar('jk'),
         alamat: $this->request->getVar('alamat'),
         noHp: $this->request->getVar('no_hp'),
      );

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Edit data berhasil',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal mengubah data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru/edit/' . $idGuru);
   }

   public function delete($id)
   {
      $result = $this->guruModel->delete($id);

      if ($result) {
         session()->setFlashdata([
            'msg' => 'Data berhasil dihapus',
            'error' => false
         ]);
         return redirect()->to('/admin/guru');
      }

      session()->setFlashdata([
         'msg' => 'Gagal menghapus data',
         'error' => true
      ]);
      return redirect()->to('/admin/guru');
   }
}
