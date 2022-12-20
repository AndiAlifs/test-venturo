<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use GuzzleHttp\Client;

class Transaksi extends Model
{
    use HasFactory;


    public function getListMenu()
    {
        $url = "http://tes-web.landa.id/intermediate/menu";
        $client = new Client();
        $response = $client->request('GET', $url);
        $result = json_decode($response->getBody()->getContents(), true);
        $menu = ["makanan"=>[], "minuman"=>[]];
        foreach ($result as $key => $value) {
            if ($value['kategori'] == "makanan") {
                array_push($menu['makanan'], $value['menu']);
            } else {
                array_push($menu['minuman'], $value['menu']);
            }
        }
        return $menu;
    }

    public function getData($tahun)
    {
        $url = "http://tes-web.landa.id/intermediate/transaksi?tahun=$tahun";
        $data = [];
        $dataMenu = Transaksi::getListMenu();

        $client = new Client();
        $response = $client->request('GET', $url);
        $result = json_decode($response->getBody()->getContents(), true);

        $listOfMonth = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        foreach ($dataMenu as $kategori => $menu) {
            $data[$kategori] = [];
            foreach ($menu as $namaMenu) {
                foreach ($listOfMonth as $month) {
                    $data[$kategori][$namaMenu][$month] = 0;
                }
            }
        }

        $listMakanan = $dataMenu['makanan'];
        $listMinuman = $dataMenu['minuman'];
        $listAllMenu = array_merge($listMakanan, $listMinuman);

        $totalByMenu = [];
        foreach ($listAllMenu as $menu) {
            $totalByMenu[$menu] = 0;
        }

        $totalByMonth = [];
        foreach ($listOfMonth as $month) {
            $totalByMonth[$month] = 0;
        }

        $totalAll = 0;

        foreach ($result as $key => $value) {
            $month = date("M", strtotime($value['tanggal']));
            $totalByMenu[$value['menu']] += $value['total'];
            $totalByMonth[$month] += $value['total'];
            $totalAll += $value['total'];
            if (in_array($value['menu'], $listMakanan)) {
                $data['makanan'][$value['menu']][$month] += $value['total'];
            } else {
                $data['minuman'][$value['menu']][$month] += $value['total'];
            }
        }

        $allData = [
            'data' => $data,
            'totalByMenu' => $totalByMenu,
            'totalByMonth' => $totalByMonth,
            'totalAll' => $totalAll
        ];

        return $allData;
    }
}
