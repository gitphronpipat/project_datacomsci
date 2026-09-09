<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Log;

class Adminmenucontroller extends Controller

{

    public function __construct()
    {
        //
    }

    public function index()
    {
        $data = [
            'content' => 'admin/dashboard',
           'active_menu' => 'dashboard'
        ];
        log::info('Adminmenucontroller index method called');

        return view('admin/index', $data);
    }

    public function corn()
    {
        $data = [
            // ใส่ตัวแปรที่จะส่งไปให้ view ตรงนี้
        ];

        return view('admin/index', $data);
    }
}
