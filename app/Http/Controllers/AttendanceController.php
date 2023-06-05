<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttendanceController extends Controller
{
    public function insertAttendance(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'waktu' => 'required|date_format:Y-m-d H:i:s'
        ]);

        $user =
            JWTAuth::parseToken()->authenticate();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $isSupervisor = $user->npp_supervisor;
        if ($isSupervisor !== '-') {
            return response()->json(['error' => 'Kamu tidak diperbolehkan menginput absensi.'], 401);
        }

        $stringWaktu = $request->input('waktu');
        $arrString = explode(" ", $stringWaktu);

        if ($request->input('type') !== 'IN' && $request->input('type') !== 'OUT') {
            return response()->json(['error' => 'Kamu menginput type yang salah.'], 500);
        } else if ($request->input('type') === 'IN') {
            $tanggal = $arrString[0];
            $waktuMasuk = $arrString[1];

            $dataAttendance = new Attendance();
            $dataAttendance->user_id = $user->id;
            $dataAttendance->tanggal = $tanggal;
            $dataAttendance->waktu_masuk = $waktuMasuk;
            $dataAttendance->waktu_pulang = '';
            $dataAttendance->status_masuk = 'REJECT';
            $dataAttendance->status_pulang = 'REJECT';
            $dataAttendance->save();

            return response()->json(['message' => 'Success insert data']);
        } else if ($request->input('type') === 'OUT') {
            $tanggal = $arrString[0];
            $waktuPulang = $arrString[1];

            $attendanceUser = Attendance::where('user_id', '=', $user->id)->first();

            $dataAttendance = new Attendance();
            $dataAttendance->user_id = $user->id;
            $dataAttendance->tanggal = $tanggal;
            $dataAttendance->waktu_masuk = $attendanceUser->waktu_masuk;
            $dataAttendance->waktu_pulang = $waktuPulang;
            $dataAttendance->status_masuk = $attendanceUser->status_masuk;
            $dataAttendance->status_pulang = 'REJECT';
            $dataAttendance->save();

            return response()->json(['message' => 'Success insert data']);
        } else {
            return response()->json(['error' => 'Gagal input data'], 400);
        }
    }

    public function getDataAttendance()
    {
        try {
            $user =
                JWTAuth::parseToken()->authenticate();

            $attendance = Attendance::where('user_id', $user->id)->get();

            foreach ($attendance as $objek) {

                // Menggunakan property dynamic untuk menyisipkan properti 'nama' pada posisi kedua
                $objek->nama = $user->nama;

                unset($objek->created_at); // Menghapus properti 'created_at' dari objek
                unset($objek->updated_at); // Menghapus properti 'updated_at' dari objek
                unset($objek->deleted_at); // Menghapus properti 'deleted_at' dari objek
            }

            return $attendance;

            return response()->json(['message' => 'Success get data', 'data' => $attendance], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Gagal get data'], 400);
        }
    }

    public function isApprove(Request $request, $id)
    {
        try {
            $user =
                JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $isSupervisor = $user->npp_supervisor;
            if ($isSupervisor === '-') {
                return response()->json(['error' => 'Kamu tidak diperbolehkan menginput attendance.'], 403);
            }

            // $userAttend = User::with('attendance')->find($id);
            // return $userAttend;

            $attendance = Attendance::find($id);

            if (!$attendance) {
                return response()->json(['error' => 'Attendance not found'], 404);
            }

            $user_npp = User::find($attendance->user_id);
            // return $user->npp_supervisor;

            if ($user->npp_supervisor !== $user_npp->npp) {
                return response()->json(['error' => 'Anda bukan supervisor untuk kehadiran ini'], 403);
            }

            $status_masuk = $request->input('status_masuk');
            $status_pulang = $request->input('status_pulang');

            if ($status_masuk === 'APPROVE' || $status_pulang === 'APPROVE' || $status_masuk === 'REJECT' || $status_pulang === 'REJECT') {
                $attendance->status_masuk = $request->input('status_masuk');
                $attendance->status_pulang = $request->input('status_pulang');
                $attendance->save();

                return response()->json(['message' => 'Berhasil mengubah status']);
            } else {
                return response()->json(['error' => 'Kamu menginput type yang salah.'], 500);
            }
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Gagal mengirim data'], 400);
        }
    }
}
