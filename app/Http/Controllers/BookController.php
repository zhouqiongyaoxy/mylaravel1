<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function getBookList(Request $request)
    {
        $params = $request->all();
        print_r($params);exit;
    }
}