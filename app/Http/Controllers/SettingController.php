<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // 新增编辑字典
    public function saveDict(Request $request)
    {
        $params = $request->all();
        $rs = ['code' => 1, 'msg' => '操作成功' ];
        if (empty($params['set_key']) || empty($params['set_value'])) {
            return ['code' => 0, 'msg' => '必要参数未传' ];
        }
        if ($params['id'] == -1) { // add
            $dict = DB::table('dict')->where([
                ['deleted', 0],
                ['set_key', $params['set_key']],
                ['set_value', $params['set_value']]
            ])->first();
            if ($dict) {
                return ['code' => 0, 'msg' => '该数据已经存在,不能重复'];
            }
            $insert = DB::table('dict')->insert([
                'set_key' => $params['set_key'],
                'set_value' => $params['set_value'],
                'createtime' => date("Y-m-d H:i:s"),
                'updatetime' => date("Y-m-d H:i:s"),
            ]);
            if (!$insert) {
                return ['code' => 0, 'msg' => '新增数据失败'];
            }
        } else { //update
            $dict = DB::table('dict')->where([['id', $params['id']], ['deleted', 0]])->first();
            if (!$dict) {
                return ['code' => 0, 'msg' => '更新的数据不存在'];
            }
            $dict = DB::table('dict')->where([
                ['set_key', $params['set_key']],
                ['set_value', $params['set_value']],
                ['deleted', 0],
                ['id', '<>', $params['id']]
            ])->first();
            if ($dict) {
                return ['code' => 0, 'msg' => '该数据已经存在,不能重复'];
            }
            $update = DB::table('dict')->where("id", $params['id'])
                ->update(['set_value' => $params['set_value'], 'updatetime' => date("Y-m-d H:i:s")]);
            if (!$update) {
                $rs = ['code' => 0, 'msg' => '更新数据失败'];
            }
        }
        return $rs;
    }

    // 删除字典
    public function deleteDict(Request $request)
    {
        $params = $request->all();
        $rs = ['code' => 1, 'msg' => '操作成功' ];
        if (empty($params['id'])) {
            return ['code' => 0, 'msg' => '必要参数未传' ];
        }
        $dict = DB::table('dict')->where([['id', $params['id']], ['deleted', 0]])->first();
        if (!$dict) {
            return ['code' => 0, 'msg' => '要删除的数据不存在'];
        }
        $bookField = explode('_', $params['set_key'])[1];
        $updateBook = DB::table('books')->where($bookField, $params['id'])
            ->update([$bookField => -1, 'updatetime' => date("Y-m-d H:i:s")]);
        if ($updateBook) {
            $del = DB::table('dict')->where("id", $params['id'])
                ->update(['deleted' => 1, 'updatetime' => date("Y-m-d H:i:s")]);
            if (!$del) {
                return ['code' => 0, 'msg' => '删除数据失败'];
            }
        } else {
            return ['code' => 0, 'msg' => '删除数据失败'];
        }
        return $rs;
    }

    // 查询字典
    public function getDictList(Request $request)
    {
        $params = $request->all();
        $wheres = [['deleted', 0]];
        if (!empty($params['set_key'])) {
            $wheres[] = ['set_key', $params['set_key']];
        }
        if (!empty($params['setkey_like'])) {
            $wheres[] = ['set_key', 'like', $params['setkey_like'].'%'];
        }
        $dicts = DB::table('dict')->where($wheres)->get();
        return $dicts;
    }
}