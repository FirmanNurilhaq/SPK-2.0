<?php
namespace App\Controllers;
use App\Models\UserModel;

class AuthController extends BaseController {
    public function login() {
        if (session()->get('logged_in')) {
            return redirect()->to(session()->get('role') == 'pemesanan' ? '/pemesanan' : '/pengadaan');
        }
        return view('auth/login');
    }

    public function process() {
        $model = new UserModel();
        $user = $model->where('username', $this->request->getPost('username'))->first();

        if ($user && $user['password'] == $this->request->getPost('password')) {
            session()->set(['id_user' => $user['id_user'], 'role' => $user['role'], 'nama' => $user['nama_lengkap'], 'logged_in' => true]);
            return redirect()->to($user['role'] == 'pemesanan' ? '/pemesanan' : '/pengadaan');
        } 
        return redirect()->back()->with('error', 'Login Gagal');
    }

    public function logout() {
        session()->destroy();
        return redirect()->to('/login');
    }
}