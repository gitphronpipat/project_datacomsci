<?php

namespace App\Http\Controllers\officer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class testcontroller extends Controller
{
    /**
     * แสดงหน้าหลักสำหรับเจ้าหน้าที่ (Officer)
     */
    public function index()
    {
        $data = [
            'content' => 'officer.page',
            'active_menu' => 'test',
        ];
        Log::info('Officer testcontroller index method called');

        return view('officer.index', $data);
    }
}
