<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {   

        if (isset($_GET['tahun'])) {
            $tahun = $_GET['tahun'];
            $allData  = Transaksi::getData($tahun);
            dd($allData);
            return view('transaksi.detail', compact('allData'));
        } else {
            return view('transaksi.index');
        }
    }
}
