<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\Studentmodel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

/**
 * Class StudentImportController
 * --------------------------------------------------------------------------
 * คอนโทรลเลอร์สำหรับแกะและนำเข้าข้อมูลนักศึกษา + กลุ่มโครงงาน จากไฟล์ CSV
 * ออกแบบมาเพื่อทำงานร่วมกับ Studentcontroller.php และ Studentmodel ใน Laravel
 * --------------------------------------------------------------------------
 */
class StudentImportController extends Controller
{
    /**
     * 1. ฟังก์ชันพรีวิว/แกะข้อมูลจากไฟล์ CSV (Preview & Parse CSV)
     * อ่านไฟล์ CSV -> ค้นหาหัวตารางอัตโนมัติ -> กรองรหัสนักศึกษา -> จัดกลุ่มโปรเจกต์
     */
    public function previewCsv(Request $request)
    {
        // ตรวจสอบไฟล์อัปโหลด
        $validator = Validator::make($request->all(), [
            'csv_file'      => 'required|file|mimes:csv,txt|max:20480',
            'academic_year' => 'nullable|integer',
        ], [
            'csv_file.required' => 'กรุณาเลือกไฟล์ CSV ที่ต้องการนำเข้า',
            'csv_file.mimes'    => 'ไฟล์ต้องเป็นนามสกุล .csv เท่านั้น',
            'csv_file.max'      => 'ขนาดไฟล์ต้องไม่เกิน 20MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $file = $request->file('csv_file');
        $academicYear = $request->input('academic_year', date('Y') + 543);
        $path = $file->getRealPath();

        // แกะข้อมูล CSV ด้วยฟังก์ชันภายใน
        $parsedData = $this->extractDataFromCsv($path, $academicYear);

        Log::info("CSV parsed successfully: {$parsedData['summary']['total_students']} students, {$parsedData['summary']['total_projects']} projects");

        return response()->json([
            'status'        => 'success',
            'academic_year' => $academicYear,
            'summary'       => $parsedData['summary'],
            'students'      => $parsedData['students'],
            'projects'      => $parsedData['projects'],
        ]);
    }

    /**
     * 2. ฟังก์ชันบันทึกข้อมูลนักศึกษาและโครงงานลงฐานข้อมูลจริง (Store Import Data)
     * รับข้อมูล JSON หรือ Session ที่ผ่านการตรวจสอบแล้ว มาบันทึกลงตาราง:
     * - ตาราง `student`: สร้าง User Account พร้อม hash รหัสผ่าน
     * - ตาราง `projects`: สร้างหัวข้อโครงงาน
     * - ตาราง `project_members`: ผูกสมาชิกกลุ่มเข้ากับโครงงาน
     */
    public function storeImportData(Request $request)
    {
        $students     = $request->input('students', []);
        $projects     = $request->input('projects', []);
        $academicYear = $request->input('academic_year', date('Y') + 543);

        if (empty($students) && empty($projects)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'ไม่มีข้อมูลที่ต้องนำเข้า',
            ], 400);
        }

        $result = [
            'students_created' => 0,
            'students_skipped' => 0,
            'projects_created' => 0,
            'members_linked'   => 0,
            'errors'           => [],
        ];

        DB::beginTransaction();
        try {
            // --------------------------------------------------
            // ก) สร้างบัญชีนักศึกษาลงตาราง `student`
            // --------------------------------------------------
            foreach ($students as $s) {
                $studentId = trim($s['student_id']);
                $name      = trim($s['name']);

                // ตรวจสอบรหัสซ้ำในระบบ
                $exists = Studentmodel::where('student_id', $studentId)
                    ->orWhere('username', $studentId)
                    ->exists();

                if ($exists) {
                    $result['students_skipped']++;
                    $result['errors'][] = "รหัสนักศึกษา {$studentId} ({$name}) มีอยู่ในระบบแล้ว — ข้ามไป";
                    continue;
                }

                // รหัสผ่านเริ่มต้น = รหัสนักศึกษา (ตรงตามโมเดล Studentcontroller)
                Studentmodel::create([
                    'student_id'      => $studentId,
                    'username'        => $studentId,
                    'password'        => Hash::make($studentId),
                    'real_pass'       => $studentId,
                    'name'            => $name,
                    'status'          => 1, // เปิดใช้งานปกติ
                    'email'           => $s['email'] ?? null,
                    'phone'           => $s['phone'] ?? null,
                    'profile_picture' => null,
                ]);

                $result['students_created']++;
            }

            // --------------------------------------------------
            // ข) บันทึกข้อมูลกลุ่มโครงงานลงตาราง `projects` และ `project_members`
            // --------------------------------------------------
            if (Schema::hasTable('projects')) {
                foreach ($projects as $p) {
                    $studentIds = $p['student_ids'] ?? [];

                    // ค้นหา ID อาจารย์ที่ปรึกษาจากตาราง admins (ตรงตามที่ Studentcontroller ใช้ Join)
                    $advisorPresId = $this->findAdminIdByName($p['advisor_president'] ?? null);
                    $advisorCom1Id = $this->findAdminIdByName($p['advisor_committee_1'] ?? null);
                    $advisorCom2Id = $this->findAdminIdByName($p['advisor_committee_2'] ?? null);

                    $projectInsertData = [
                        'title_th'                => $p['title_th'] ?? null,
                        'title_en'                => $p['title_en'] ?? null,
                        'academic_year'           => $academicYear,
                        'advisor_president_id'    => $advisorPresId,
                        'advisor_committee_1_id'  => $advisorCom1Id,
                        'advisor_committee_2_id'  => $advisorCom2Id,
                        'remark'                  => $p['remark'] ?? null,
                        'created_at'              => now(),
                        'updated_at'              => now(),
                    ];

                    // รองรับกรณีตาราง projects เก็บชื่ออาจารย์หรือบริษัทเป็น Text
                    $optionalCols = [
                        'advisor_president'   => $p['advisor_president'] ?? null,
                        'advisor_committee_1' => $p['advisor_committee_1'] ?? null,
                        'advisor_committee_2' => $p['advisor_committee_2'] ?? null,
                        'advisor_special'     => $p['advisor_special'] ?? null,
                        'company_name'        => $p['company_name'] ?? null,
                    ];
                    foreach ($optionalCols as $col => $val) {
                        if (Schema::hasColumn('projects', $col)) {
                            $projectInsertData[$col] = $val;
                        }
                    }

                    // กรองเฉพาะฟิลด์ที่มีอยู่ใน Schema จริงของตาราง projects เพื่อความปลอดภัย 100%
                    $validInsertData = [];
                    foreach ($projectInsertData as $col => $val) {
                        if (Schema::hasColumn('projects', $col)) {
                            $validInsertData[$col] = $val;
                        }
                    }

                    $projectId = DB::table('projects')->insertGetId($validInsertData);

                    $result['projects_created']++;

                    // ผูกสมาชิกกลุ่มลง `project_members`
                    if (Schema::hasTable('project_members')) {
                        foreach ($studentIds as $sid) {
                            DB::table('project_members')->insert([
                                'project_id' => $projectId,
                                'student_id' => $sid,
                                'joined_at'  => now(),
                            ]);
                            $result['members_linked']++;
                        }
                    }
                }
            }

            DB::commit();
            Log::info("CSV Import committed: {$result['students_created']} students created, {$result['projects_created']} projects created.");

            return response()->json([
                'status'  => 'success',
                'message' => "นำเข้าข้อมูลสำเร็จ! สร้างนักศึกษา {$result['students_created']} คน, โครงงาน {$result['projects_created']} กลุ่ม",
                'data'    => $result,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("CSV Import error: " . $e->getMessage());

            return response()->json([
                'status'  => 'error',
                'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =========================================================================
    // ฟังก์ชันช่วยภายในสำหรับการแกะไฟล์ CSV (Private Helper Methods)
    // =========================================================================

    /**
     * คอร์ฟังก์ชันหลักในการอ่านและแกะข้อมูลจากไฟล์ CSV ครบทุกคอลัมน์เหมือน preview.php
     */
    private function extractDataFromCsv($filePath, $academicYear)
    {
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new \Exception('ไม่สามารถเปิดไฟล์ CSV ได้');
        }

        $allRows = [];
        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            $allRows[] = $row;
        }
        fclose($handle);

        $columnMap = null;
        $currentType = 'project';
        $projects = [];
        $currentProject = null;
        $students = [];

        foreach ($allRows as $row) {
            // ตรวจสอบหัวส่วนเพื่อแยกประเภท (โครงงานวิชา 498 หรือ สหกิจวิชา 497)
            $sectionType = $this->detectSectionType($row);
            if ($sectionType !== false) {
                $currentType = $sectionType;
                continue;
            }

            // ตรวจสอบหัวตาราง (Header Row) เพื่อ Auto-Detect ตำแหน่งคอลัมน์
            if ($this->isHeaderRow($row)) {
                $columnMap = $this->autoDetectColumns($row);
                continue;
            }

            if ($columnMap === null) {
                continue;
            }

            // ดึงค่าตามคอลัมน์ที่ตรวจจับได้ (ครบถ้วนทุกฟิลด์เหมือน preview.php)
            $projectNo      = $this->getCellValue($row, $columnMap, 'project_no');
            $studentId      = $this->getCellValue($row, $columnMap, 'student_id');
            $name           = $this->getCellValue($row, $columnMap, 'name');
            $titleTh        = $this->getCellValue($row, $columnMap, 'title_th');
            $titleEn        = $this->getCellValue($row, $columnMap, 'title_en');
            $advisor1       = $this->getCellValue($row, $columnMap, 'advisor1');        // ประธาน
            $advisor2       = $this->getCellValue($row, $columnMap, 'advisor2');        // กรรมการ 1
            $advisor3       = $this->getCellValue($row, $columnMap, 'advisor3');        // กรรมการ 2
            $advisorSpecial = $this->getCellValue($row, $columnMap, 'advisor_special'); // ที่ปรึกษาพิเศษ
            $company        = $this->getCellValue($row, $columnMap, 'company_name');    // สถานที่ฝึกสหกิจ
            $remark         = $this->getCellValue($row, $columnMap, 'remark');

            // ตรวจว่าเป็นรหัสนักศึกษา 10 หลักหรือไม่
            if (!$this->isStudentId($studentId)) {
                continue;
            }

            // 1. จัดเก็บรายชื่อนักศึกษา
            if (!isset($students[$studentId])) {
                $students[$studentId] = [
                    'student_id' => $studentId,
                    'name'       => $name,
                    'type'       => $currentType,
                ];
            }

            // 2. จัดกลุ่มโปรเจกต์
            if ($projectNo !== '' && is_numeric($projectNo)) {
                if ($currentProject !== null) {
                    $projects[] = $currentProject;
                }
                $currentProject = [
                    'project_no'          => $projectNo,
                    'type'                => $currentType,
                    'student_ids'         => [$studentId],
                    'title_th'            => $titleTh,
                    'title_en'            => $titleEn,
                    'advisor_president'   => $advisor1,
                    'advisor_committee_1' => $advisor2,
                    'advisor_committee_2' => $advisor3,
                    'advisor_special'     => $advisorSpecial,
                    'company_name'        => $company,
                    'academic_year'       => $academicYear,
                    'remark'              => $remark,
                ];
            } elseif ($currentProject !== null) {
                // สมาชิกคนที่ 2, 3 ในกลุ่มเดียวกัน
                $currentProject['student_ids'][] = $studentId;
                if (empty($currentProject['title_th']) && !empty($titleTh)) {
                    $currentProject['title_th'] = $titleTh;
                }
                if (empty($currentProject['title_en']) && !empty($titleEn)) {
                    $currentProject['title_en'] = $titleEn;
                }
            }
        }

        if ($currentProject !== null) {
            $projects[] = $currentProject;
        }

        return [
            'summary' => [
                'total_students' => count($students),
                'total_projects' => count($projects),
            ],
            'students' => array_values($students),
            'projects' => $projects,
        ];
    }

    /**
     * ดึงค่าจาก Cell ตามชื่อคอลัมน์ใน Map
     */
    private function getCellValue($row, $map, $key)
    {
        return isset($map[$key]) && $map[$key] !== null ? trim($row[$map[$key]] ?? '') : '';
    }

    /**
     * ค้นหา ID อาจารย์จากตาราง admins โดยเทียบจากชื่อ (สำหรับผูก advisor_president_id ฯลฯ)
     */
    private function findAdminIdByName($name)
    {
        if (empty($name) || !Schema::hasTable('admins')) {
            return null;
        }

        // ตัดคำนำหน้า (อ., ผศ., รศ., ดร., นาย ฯลฯ) เพื่อให้ค้นหาชื่อตรงกันได้ง่ายขึ้น
        $cleanName = preg_replace('/^(อ\.|ผศ\.|รศ\.|ศ\.|ดร\.|นาย|นางสาว|นาง|\s)+/u', '', trim($name));
        if (empty($cleanName)) return null;

        $admin = DB::table('admins')
            ->where('name', 'like', '%' . $cleanName . '%')
            ->first();

        return $admin ? $admin->id : null;
    }

    /**
     * ตรวจจับตำแหน่งคอลัมน์อัตโนมัติจากคำสำคัญในหัวตาราง (Auto-Detect Columns ครบทุกฟิลด์เหมือน preview.php)
     */
    private function autoDetectColumns($headerRow)
    {
        $map = [
            'project_no'      => null, // ลำดับโครงงาน
            'student_id'      => null, // รหัสนักศึกษา
            'name'            => null, // ชื่อ-สกุล
            'title_th'        => null, // ชื่อโครงงานภาษาไทย
            'title_en'        => null, // ชื่อโครงงานภาษาอังกฤษ
            'advisor1'        => null, // ประธาน
            'advisor2'        => null, // กรรมการ 1
            'advisor3'        => null, // กรรมการ 2
            'advisor_special' => null, // ที่ปรึกษาพิเศษ
            'company_name'    => null, // สถานที่ฝึกสหกิจ / บริษัท
            'remark'          => null, // หมายเหตุ
        ];

        $keywords = [
            'project_no'      => ['ลำดับโครงงาน', 'ลำดับโครง', 'ลำดับ'],
            'student_id'      => ['รหัสนักศึกษา', 'รหัส'],
            'name'            => ['ชื่อ-สกุล', 'ชื่อ-นามสกุล', 'ชื่อสกุล'],
            'title_th'        => ['ภาษาไทย', '(ไทย)', 'โครงงาน(ไทย)'],
            'title_en'        => ['ภาษาอังกฤษ', '(อังกฤษ)', 'english'],
            'advisor_special' => ['ที่ปรึกษาพิเศษ', 'พิเศษ'],
            'advisor1'        => ['ประธาน'],
            'advisor2'        => ['กรรมการ'],
            'company_name'    => ['สถานที่ฝึก', 'สหกิจศึกษา', 'บริษัท'],
            'remark'          => ['หมายเหตุ', 'remark'],
        ];

        foreach ($headerRow as $index => $cellValue) {
            $cellClean = mb_strtolower(str_replace(["\r", "\n", " "], '', trim($cellValue)));
            if ($cellClean === '') continue;

            foreach ($keywords as $field => $kwList) {
                foreach ($kwList as $kw) {
                    $kwClean = mb_strtolower(str_replace(["\r", "\n", " "], '', $kw));
                    if (mb_strpos($cellClean, $kwClean) !== false) {
                        // ถ้าเจอกรรมการอีกคอลัมน์ ให้จัดเป็น advisor3 (กรรมการ 2)
                        if ($field === 'advisor2' && $map['advisor2'] !== null && $map['advisor2'] !== $index) {
                            $map['advisor3'] = $index;
                        } else {
                            if ($map[$field] === null) {
                                $map[$field] = $index;
                            }
                        }
                        break 2;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * ตรวจสอบว่าเป็นรหัสนักศึกษาจริงหรือไม่ (ตัวเลข 10 หลัก รหัสรุ่น 56-99)
     */
    private function isStudentId($value)
    {
        $value = trim($value);
        if (!is_numeric($value) || strlen($value) !== 10) return false;
        $prefix = intval(substr($value, 0, 2));
        return $prefix >= 56 && $prefix <= 99;
    }

    /**
     * ตรวจว่าเป็นแถวหัวตาราง (Header Row) หรือไม่
     */
    private function isHeaderRow($row)
    {
        $line = implode(' ', $row);
        return (mb_strpos($line, 'รหัสนักศึกษา') !== false || mb_strpos($line, 'รหัส') !== false);
    }

    /**
     * ตรวจประเภทส่วน (วิชา 497 สหกิจ หรือ วิชา 498 โครงงาน)
     */
    private function detectSectionType($row)
    {
        $line = mb_strtolower(implode(' ', $row));
        if (mb_strpos($line, 'วิชา') === false) return false;

        if (mb_strpos($line, 'สหกิจ') !== false || mb_strpos($line, '497') !== false) {
            return 'coop';
        }
        if (mb_strpos($line, '498') !== false || mb_strpos($line, 'การเรียนรู้อิสระ') !== false) {
            return 'project';
        }
        return false;
    }
}
