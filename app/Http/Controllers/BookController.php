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
        $params['pageNo'] = isset($params['pageNo']) ? $params['pageNo'] : 1;
        $params['pageSize'] = isset($params['pageSize']) ? $params['pageSize'] : 10;
       // $offset = ($params['pageNo'] - 1 ) * $params['pageSize'];
        $name = $params['name'];
        $category = isset($params['category']) ? $params['category'] : 0 ;
        $language = isset($params['language']) ? $params['language'] : 0 ;
        $status = isset($params['status']) ? $params['status'] : 0 ;
        $rs = DB::table('books')
                    ->when($name, function ($query) use ($name){
                        return $query->where('name', 'like', '%'.$name.'%');
                        })
                    ->when($category, function ($query) use ($category){
                        return $query->where('category', $category);
                        })
                    ->when($language, function ($query) use ($language){
                        return $query->where('language', $language);
                        })
                    ->when($status, function ($query) use ($status){
                        return $query->where('status', $status);
                        })
                    ->where('deleted', 0)
                    ->orderBy('updatetime', 'desc')
                    ->paginate($params['pageSize']);
                    /*->offset($offset)
                    ->limit($params['pageSize'])
                    ->get();*/
        return $rs;
    }

    // todo 做设置,列表显示要转成对应的设置选项 新增 编辑 删除 借出与收回(新增数据表,人,时间,电话等)
}