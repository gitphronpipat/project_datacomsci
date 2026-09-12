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
            'content' => 'admin/page_teacherandofficer',
           'active_menu' => 'page_teacherandofficer'
        ];
        log::info('Adminmenucontroller index method called');

        return view('admin/index', $data);
    }

    public function create()
    {
        $data = [
            'content' => 'admin/page_teacherandofficer_add',
            'active_menu' => 'page_teacherandofficer'
        ];
        log::info('Adminmenucontroller create method called');

        return view('admin/index', $data);
    }

    public function corn()
    {
        $data = [
            'content' => 'admin/page_admin',
           'active_menu' => 'dashboard'
        ];
        log::info('Adminmenucontroller index method called');

        return view('admin/index', $data);
    }
}
