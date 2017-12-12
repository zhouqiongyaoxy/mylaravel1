<?php
namespace App\Http\Helper;
/**
 * 各种配置类
 */
class Configs
{
    //密保问题配置
    public static function passwordQuestion(){
        return [
            0 => '您的真实姓名',
            1 => '您的学历水平',
            2 => '您小学学校的名称',
            3 => '您初中学校的名称',
            4 => '您高中学校的名称',
            5 => '您大学学校的名称',
            6 => '您叫您老公的昵称',
            7 => '您叫您老婆的昵称',
        ];
    }
}