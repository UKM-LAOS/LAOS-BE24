<?php

namespace App\Http\Controllers\API\Course;

use App\Http\Controllers\API\Compro\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['myCourse.course'])
            ->whereHas('myCourse', function ($query) {
                $query->where('user_id', auth('api')->user()->id);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        return ResponseFormatter::success($transactions, 'Data transaksi berhasil diambil');
    }
}
