<?php

namespace App\Http\Controllers;

use App\Models\LichLamViec;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LichLamViecController extends Controller
{
    public function createLichLamViec(Request $request) {
        $x = $this->checkRule(15);
        if($x)  {
            return response()->json([
                'status'    => 0,
                'message'   => 'You are not authorized!',
            ]);
        }
        $data = $request->all();
        $data['ngay_lam_viec'] = $request->ngay_lam_viec;
        $data['id_nhan_vien']  = Auth::guard('admin')->user()->id;
        $now = Carbon::today();

        if ($now->greaterThan(Carbon::parse($data['ngay_lam_viec']))) {
            return response()->json(['status' => 0, 'message' => 'Don not add old work schedules!']);
        }

        $data['gio_bat_dau'] = ($data['buoi_lam_viec'] == 0) ? "8:00:00" : "17:00:00";
        $data['gio_ket_thuc'] = ($data['buoi_lam_viec'] == 0) ? "16:00:00" : "22:00:00";

        $check_buoi_lam_viec = LichLamViec::where('ngay_lam_viec', $data['ngay_lam_viec'])
                                           ->where('buoi_lam_viec', $data['buoi_lam_viec'])
                                           ->where('id_nhan_vien', $data['id_nhan_vien'])
                                           ->first();
        if($check_buoi_lam_viec == null) {
            LichLamViec::create($data);
        }

        return response()->json(['status' => 1, 'message' => 'Added new work schedule']);
    }

    public function updateLichLamViec(Request $request) {
        $x = $this->checkRule(16);
        if($x)  {
            return response()->json([
                'status'    => 0,
                'message'   => 'You are not authorized!',
            ]);
        }
        // $user = auth('sanctum')->user();
        $data = $request->all();
        $res = DB::transaction(function () use ($data) {
            $now = Carbon::today();
            $lichLamViec = LichLamViec::where('id', $data['id'])->where('id_nhan_vien', $data['id_user'])->first();
            if ($now->greaterThan(Carbon::parse($lichLamViec->ngay_lam_viec))) {
                return false;
            } else {
                if($lichLamViec && $lichLamViec->is_done == 0) {
                    $lichLamViec->delete();
                    return true;
                }
            }
        });

        if ($res) {
            return response()->json([
                'status'    => 1,
                'message'   => 'Updated work schedule!',
            ]);
        } else {
            return response()->json([
                'status'    => 0,
                'message'   => 'Cannot cancel work schedule!',
            ]);
        }
    }

    public function upload(Request $request) {
        if ($request->hasfile('files')) {
            foreach($request->file('files') as $file) {
                $name = $file->getClientOriginalName();
                $file->move(public_path().'/files/', $name);  // Lưu file vào thư mục public/files
                // Hoặc bạn có thể lưu trữ nó ở đâu đó và/hoặc lưu đường dẫn vào database
            }
        }

        return response()->json(['message' => 'Uploaded successfully']);
    }

    public function changeIsDone(Request $request) {
        $data = $request->all();
        $currentDate = Carbon::now()->toDateString(); // Lấy ngày hiện tại

        $lichLamViec = LichLamViec::where('id', $data['id'])
                                ->where('id_nhan_vien', $data['id_user'])
                                ->whereDate('ngay_lam_viec', $currentDate) // Thêm điều kiện kiểm tra ngày hiện tại
                                ->first();

        if ($lichLamViec) {
            $lichLamViec->is_done = !$lichLamViec->is_done;
            $lichLamViec->save();
        }

        return response()->json([
            'status'    => 1,
            'message'   => 'Updated work schedule!',
        ]);
    }
}
