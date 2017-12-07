<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
//use Illuminate\Database\Query\Builder;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request as Request;


class TestController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function test()
    {
        $rs = DB::select('select * from users');
        print_r($rs);
        return '2222';
    }

    public function wordToTxt(Request $request,$path)
    {//路径,只传文件夹,反正都在下载目录里
        $uri = '/Users/zhouqiongyao/Downloads/'.$path;
        if ( !file_exists($uri) ) {
            echo "文件夹不存在";
            exit();
        }
        $dir = opendir($uri);
        while ($file = readdir($dir)){
            if($file == '.' || $file == '..') continue;
            $filenameInfo = pathinfo($file);
            if ($filenameInfo['extension'] == 'doc') {
                $content = shell_exec('/usr/local/Cellar/antiword/0.37/bin/antiword -m UTF-8.txt '.$uri.'/'.$file);
                file_put_contents($uri.'/'.$filenameInfo['filename'].'.txt',trim($content));
            }
        }
        echo $uri.'<br /> 转换完成!';exit();
    }
    public function getLgPositions()
    {
        //phpinfo();
       // echo 'ooops!';exit();
        $lgcookie = $this->get_cookie();
        $postdata = [
            'first' => true,
            'pn' => 1,
            'kd' => 'PHP'
        ];
        $url = 'https://www.lagou.com/jobs/positionAjax.json?city=%E6%88%90%E9%83%BD&needAddtionalResult=false&isSchoolJob=0';
        $rs = $this->getLGdata($url,$postdata,$lgcookie);
        $rs = json_decode($rs['body'],true);
        if ($rs && !empty($rs['content']) && !empty($rs['content']['positionResult'])) {
            $totalCount = $rs['content']['positionResult']['totalCount'];
            $pageSize = $rs['content']['pageSize'];
            $pageNo = $rs['content']['pageNo'];
            $data = $rs['content']['positionResult']['result'];
            if (count($data) > 0) {
                $this->dataProcess($data,$lgcookie);
                if ((($pageNo - 1) * $pageSize + $pageSize ) < $totalCount) {
                    do{
                        $postdata = [
                            'first' => false,
                            'pn' => $pageNo,
                            'kd' => 'PHP'
                        ];
                        $rs = $this->getLGdata($url,$postdata,$lgcookie);
                        $rs = json_decode($rs['body'],true);
                        if ($rs && !empty($rs['content']) && !empty($rs['content']['positionResult'])) {
                            $data = $rs['content']['positionResult']['result'];
                            if (count($data) > 0) {
                                $this->dataProcess($data, $lgcookie);
                            }
                        }
                        $pageNo ++;
                    }while((($pageNo - 1) * $pageSize + $pageSize ) < $totalCount);
                }
            }

        }
       // echo $lgcookie;
        //print_r($rs);
    }

    private function dataProcess($data,$cookie)
    {
        foreach ($data as $v){
            $workYearS = $workYearE = 0;
            $salaryS = $salaryE = 0;
            if (!empty($v['workYear'])) {
                $workyear = explode('年', $v['workYear']);
                $workyear = explode('-', $workyear[0]);
                $workYearS = isset($workyear[0]) ? $workyear[0] : 0;
                $workYearE = isset($workyear[1]) ? $workyear[1] : 0;
            }
            if (!empty($v['salary'])) {
                $salary = explode('-', $v['salary']);
                foreach ($salary as $k => $s){
                    if ($k == 0) {
                        $salaryS = substr($s,0,-1);
                    } else {
                        $salaryE = substr($s,0,-1);
                    }
                }
            }
            $companyLabelList = is_array($v['companyLabelList']) ? implode(',',$v['companyLabelList']) : $v['companyLabelList'];
            $positionLables = is_array($v['positionLables']) ? implode(',',$v['positionLables']) : $v['positionLables'];
            $businessZones = is_array($v['businessZones']) ? implode(',',$v['businessZones']) : $v['businessZones'];
            $industryLables = is_array($v['industryLables']) ? implode(',',$v['industryLables']) : $v['industryLables'];
            //获取详情和地址
            $url = 'https://www.lagou.com/jobs/'.$v['positionId'].'.html';
            $detalContent = $this->getLGdata($url,'',$cookie);
            preg_match('/<dd class="job_bt">[\s\S]*<\/dd>/U',$detalContent['body'],$descDetail);
            $descDetail = '';
            if (!empty($descDetail) && !empty($descDetail[0])) {
                $descDetail = html_entity_decode($descDetail[0]);
            }else{
                print_r($descDetail);exit();
            }
            $addressDetail = $this->getAddressLngLat('positionAddress',$detalContent['body']);
            $positionLng = $this->getAddressLngLat('positionLng',$detalContent['body']);
            $positionLat = $this->getAddressLngLat('positionLat',$detalContent['body']);
            $values = $v['positionId'].",'".$v['positionName']."','".$v['companyShortName']."',".$salaryS.",".$salaryE.",".$workYearS.",".$workYearE.",'".$v['education']."','".$v['jobNature']."','".
                     $v['createTime']."','".$positionLables."','".$v['city']."','".$v['district']."','".$v['industryField']."','".$v['financeStage']."','".$v['companySize']."','".$v['positionAdvantage']."','".
                     $companyLabelList."','".$v['companyFullName']."','".$industryLables."','".$businessZones."','".$descDetail."','".$addressDetail."','".$positionLng."','".$positionLat."'";
            $res = DB::insert("replace into lg_job_detail
                      (`positionId`,`positionName`,`companyShortName`,`salaryS`,`salaryE`,`workYearS`,`workYearE`,`education`,`jobNature`,
                     `createTime`,`positionLables`,`city`,`district`,`industryField`,`financeStage`, `companySize`,`positionAdvantage`,
                     `companyLabelList`,`companyFullName`,`industryLables`,`businessZones`,`descDetail`,`addressDetail`,`positionLng`,`positionLat`) 
                     values
                     ({$values})");
        }
        return $res;
    }

    private function getAddressLngLat($field,$content){
        preg_match('/<input type="hidden" name="'.$field.'" value=".*\/>/U',$content,$str);
        $str = explode(' ', $str[0]);
        $str = explode('"', $str[3]);
        return $str[1];
    }
    private function get_cookie(){
        $url_ = 'https://www.lagou.com/jobs/list_PHP?city=%E6%88%90%E9%83%BD&cl=false&fromSearch=true&labelWords=&suginput=';
        $referer_ = 'https://www.lagou.com/jobs/list_PHP?city=%E6%88%90%E9%83%BD&cl=false&fromSearch=true&labelWords=&suginput=';
        $params_ = ['city'=>'成都',
                        'cl'=>false,
                        'fromSearch'=>true,
                        'labelWords' => '',
                        'suginput'=> ''];
        if($url_==null){echo "get_cookie_url_null";exit;}
        if($params_==null){echo "get_params_null";exit;}
        if($referer_==null){echo "get_referer-null";exit;}
        $this_header = array(
            "content-type: application/x-www-form-urlencoded; charset=UTF-8",
           // "Host:www.lagou.com",
           // "Origin:https://www.lagou.com",
            //"Referer:https://www.lagou.com/jobs/list_PHP?city=%E6%88%90%E9%83%BD&cl=false&fromSearch=true&labelWords=&suginput=",
            //"cookie: user_trace_token=20171025160913-c9879a94-b95b-11e7-a7f2-525400f775ce; LGUID=20171025160913-c987a030-b95b-11e7-a7f2-525400f775ce; index_location_city=%E6%88%90%E9%83%BD; login=true; unick=%E5%B0%8F%E5%91%A8%E5%91%A8; showExpriedIndex=1; showExpriedCompanyHome=1; showExpriedMyPublish=1; hasDeliver=2; JSESSIONID=ABAAABAACBHABBIF2A2D7623C5ACEBF5E979FF65658B90F; _gid=GA1.2.1973613914.1511260629; _ga=GA1.2.1728919805.1508918955; Hm_lvt_4233e74dff0ae5bd0a3d81c6ccf756e6=1508918955,1510733609,1510801671,1511421683; Hm_lpvt_4233e74dff0ae5bd0a3d81c6ccf756e6=1511489442; LGSID=20171124101042-ac57e840-d0bc-11e7-a46a-525400f775ce; PRE_UTM=; PRE_HOST=; PRE_SITE=; PRE_LAND=https%3A%2F%2Fwww.lagou.com%2Fjobs%2Flist_PHP%3Fcity%3D%25E6%2588%2590%25E9%2583%25BD%26cl%3Dfalse%26fromSearch%3Dtrue%26labelWords%3D%26suginput%3D; LGRID=20171124101042-ac57e9a8-d0bc-11e7-a46a-525400f775ce; _putrc=7F25C1B0DF82E85A; SEARCH_ID=8c5f21e7c17d40768658d99fce92a6b1"
        );//访问链接时要发送的头信息

        $ch = curl_init($url_);//这里是初始化一个访问对话，并且传入url，这要个必须有
        //curl_setopt就是设置一些选项为以后发起请求服务的
        curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);//一个用来设置HTTP头字段的数组。使用如下的形式的数组进行设置： array('Content-type: text/plain', 'Content-length: 100')
        curl_setopt($ch, CURLOPT_HEADER,1);//如果你想把一个头包含在输出中，设置这个选项为一个非零值，我这里是要输出，所以为 1
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);//将 curl_exec()获取的信息以文件流的形式返回，而不是直接输出。设置为0是直接输出
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);//设置跟踪页面的跳转，有时候你打开一个链接，在它内部又会跳到另外一个，就是这样理解
        curl_setopt($ch,CURLOPT_POST,1);//开启post数据的功能，这个是为了在访问链接的同时向网页发送数据，一般数urlencode码
        curl_setopt($ch,CURLOPT_POSTFIELDS,$params_); //把你要提交的数据放这
        //curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');//获取的cookie 保存到指定的 文件路径，我这里是相对路径，可以是$变量
        //curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');//要发送的cookie文件，注意这里是文件，还一个是变量形式发送
        //curl_setopt($curl, CURLOPT_COOKIE, $this->cookies);//例如这句就是设置以变量的形式发送cookie，注意，这里的cookie变量是要先获取的，见下面获取方式
        curl_setopt ($ch, CURLOPT_REFERER,$referer_); //在HTTP请求中包含一个'referer'头的字符串。告诉服务器我是从哪个页面链接过来的，服务器籍此可以获得一些信息用于处理。
        $content=curl_exec($ch);     //重点来了，上面的众多设置都是为了这个，进行url访问，带着上面的所有设置
        if(curl_errno($ch)){
            echo 'Curl error: '.curl_error($ch);exit(); //这里是设置个错误信息的反馈
        }
        if($content==false){
            echo "get_content_null";exit();
        }
        preg_match('/Set-Cookie:(.*);/iU',$content,$str); //这里采用正则匹配来获取cookie并且保存它到变量$str里，这就是为什么上面可以发送cookie变量的原因
        $cookie = $str[1]; //获得COOKIE（SESSIONID）
        curl_close($ch);//关闭会话
        return     $cookie;//返回cookie
    }

    private function getLGdata($url,$post_data,$cookie){
        $reffer = 'https://www.lagou.com/jobs/list_PHP?city=%E6%88%90%E9%83%BD&cl=false&fromSearch=true&labelWords=&suginput=';
        $origin = 'https://www.lagou.com';
        $host = 'www.lagou.com';
        $location = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36';
        $location = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.1 Safari/603.1.30';
        $cookarr = explode('=', $cookie);
        $cookie = $cookie.'; LGUID='.$cookarr[1].'; index_location_city=%E6%88%90%E9%83%BD; login=true; unick=%E5%B0%8F%E5%91%A8%E5%91%A8; showExpriedIndex=1; showExpriedCompanyHome=1; showExpriedMyPublish=1; hasDeliver=2; JSESSIONID=ABAAABAACBHABBIF2A2D7623C5ACEBF5E979FF65658B90F; _gid=GA1.2.1973613914.1511260629; _ga=GA1.2.1728919805.1508918955; Hm_lvt_4233e74dff0ae5bd0a3d81c6ccf756e6=1508918955,1510733609,1510801671,1511421683; Hm_lpvt_4233e74dff0ae5bd0a3d81c6ccf756e6=1511489442; LGSID='.$cookarr[1].'; PRE_UTM=; PRE_HOST=; PRE_SITE=; PRE_LAND=https%3A%2F%2Fwww.lagou.com%2Fjobs%2Flist_PHP%3Fcity%3D%25E6%2588%2590%25E9%2583%25BD%26cl%3Dfalse%26fromSearch%3Dtrue%26labelWords%3D%26suginput%3D; LGRID='.$cookarr[1].'; _putrc=7F25C1B0DF82E85A; SEARCH_ID=8c5f21e7c17d40768658d99fce92a6b1';
        $post_data = is_array($post_data)?http_build_query($post_data):$post_data;
        //产生一个urlencode之后的请求字符串，因为我们post，传送给网页的数据都是经过处理，一般是urlencode编码后才发送的
        $header = array( //头部信息，上面的函数已说明
            'Accept:*/*',
            'Accept-Charset:text/html,application/xhtml+xml,application/xml;q=0.7,*;q=0.3',
            'Accept-Encoding:gzip,deflate,sdch',
            'Accept-Language:zh-CN,zh;q=0.8',
            'Connection:keep-alive',
            'Content-Type:application/x-www-form-urlencoded',
            //'CLIENT-IP:'.$ip,
            //'X-FORWARDED-FOR:'.$ip,
        );

        //下面的都是头部信息的设置，请根据他们的变量名字，对应上面函数所说明
        $header = array_merge_recursive($header,array("Host:".$host));
        if($origin){
            $header = array_merge_recursive($header,array("Origin:".$origin));
        } else{
            $header = array_merge_recursive($header,array("Origin:".$url));
        }
        if($reffer){
            $header = array_merge_recursive($header,array("Referer:".$reffer));
        } else{
            $header = array_merge_recursive($header,array("Referer:".$url));
        }

        $curl = curl_init();  //这里并没有带参数初始化
        curl_setopt($curl, CURLOPT_URL, $url);//这里传入url
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);//对认证证书来源的检查，不开启次功能
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//从证书中检测 SSL 加密算法
        curl_setopt($curl, CURLOPT_USERAGENT, $location);
        //模拟用户使用的浏览器，自己设置，我的是"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:23.0) Gecko/20100101 Firefox/23.0"
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);//自动设置referer
        curl_setopt($curl, CURLOPT_POST, 1);//开启post
        curl_setopt($curl, CURLOPT_ENCODING, "gzip" );
        //HTTP请求头中"Accept-Encoding: "的值。支持的编码有"identity"，"deflate"和"gzip"。如果为空字符串""，请求头会发送所有支持的编码类型。
        //我上面设置的是*/*
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);//要传送的数据
        //curl_setopt($curl, CURLOPT_COOKIE, $this->cookies);//以变量形式发送cookie，我这里没用它，文件保险点
       // curl_setopt($curl, CURLOPT_COOKIEJAR, 'cookie.txt');    //存cookie的文件名，
        curl_setopt($curl, CURLOPT_COOKIE, $cookie);  //发送
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);//设置超时限制，防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($curl);
        if (curl_errno($curl)) {
            echo  'Curl error: ' . curl_error ( $curl );exit();
        }
        curl_close($curl);
        list($header, $body) = explode("\r\n\r\n", $tmpInfo, 2);//分割出网页源代码的头和bode
        return array("header"=>$header,"body"=>$body,"content"=>$tmpInfo);
    }
}