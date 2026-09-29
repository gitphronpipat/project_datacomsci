<?php

namespace App\Http\Controllers\teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class testcontroller extends Controller
{
    /**
     * แสดงหน้าหลักสำหรับอาจารย์ (Teacher)
     */
    public function index()
    {
        
        return view('teacher.index', [
            'content' => 'teacher.page'
        ]);
    }
}
