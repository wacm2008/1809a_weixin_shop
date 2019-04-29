<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Layout\Content;
use GuzzleHttp\Client;
use App\WxmaterialModel;
use App\WxuserModel;
use Illuminate\Support\Str;
class ImageController extends Controller
{
    //文件添加
    public function index(Content $content){
        return $content
            ->header('上传图片')
            ->description('description')
            ->body(view('admin.weixin.addimg'));
    }
    //文件上传
    public function upload(Content $content){
        $file=$_FILES['img']['type'];
        $file=explode('/',$file);
        if($file[0]=='image'){
            $type='image';
        }else if($file[0]=='audio'){
            $type='voice';
        }else if($file[0]=='video'){
            $type='video';
        }
        //hasFile方法判断文件在请求中是否存在 isValid方法判断文件在上传过程中是否出错
        if (request()->hasFile('img') && request()->file('img')->isValid()){
            $photo = request()->file('img');
            //获取后缀
            $extension = $photo->getClientOriginalExtension();
            //获取文件名
            $files=time().Str::random(8).'.'.$extension;
            //接收文件保存的相对路径 storeAS手动创文件名 store自动创建
            $store_result = $photo->storeAS($type,$files);
        }

        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.getAccessToken().'&type='.$type;
        $client= new Client();
        $response = $client->request('POST',$url,[
            'multipart' => [
                [
                    'name' => 'media',
                    'contents' => fopen('../storage/app/'.$type.'/'. $files, 'r'),
                ]
            ]
        ]);
        $json =  $response->getBody();
        $arr = json_decode($json,true);
        if($arr){
            $res=[
                'type'=>$arr['type'],
                'media_id'=>$arr['media_id'],
                'created_at'=>time()
            ];
            WxmaterialModel::insertGetId($res);
        }
        return $content->description('上传成功');
    }
    //获取上传文件
//    public function getupload(Content $content){
//        $file=$this->upload();
//        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.getAccessToken().'&type=image';
//        $client= new Client();
//        $response = $client->request('POST',$url,[
//            'multipart' => [
//                [
//                    'name' => 'media',
//                    'contents' => fopen('../storage/app/'.$file, 'r'),
//                ]
//            ]
//        ]);
//        $json =  $response->getBody();
//        $arr = json_decode($json,true);
//        if($arr){
//            $res=[
//                'type'=>$arr['type'],
//                'media_id'=>$arr['media_id'],
//                'created_at'=>time()
//            ];
//            Wxfoto::insertGetId($res);
//        }
//    }
    //素材展示
    public function show(Content $content){
        $data=WxmaterialModel::get();
        return $content
            ->header('素材展示')
            ->description('description')
            ->body(view('admin.weixin.showimg',['data'=>$data]));
    }
    //群发用户展示
    public function muestra(Content $content){
        $data=WxuserModel::get();
        //$dat=WxmaterialModel::get();
        return $content
            ->header('用户展示')
            ->description('description')
            ->body(view('admin.weixin.showuser',['data'=>$data]));
    }
    //群发
    public function sendtodo(){
        $openid=$_GET['openid'];
        $text=$_GET['text'];
        $openid=explode(',',$openid);
        $msg=[
            'touser'=>$openid,
            'msgtype'=>'text',
            'text'=>[
                'content'=>$text,
            ]
        ];
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.getAccessToken();
        $data = json_encode($msg,JSON_UNESCAPED_UNICODE);
        $client = new Client();
        $response=$client->request('POST',$url,[
            'body'=>$data
        ]);
        $res= $response->getBody();
        if($res){
            return json_encode(['code'=>1,'msg'=>'成功']);
        }else{
            return json_encode(['code'=>2,'msg'=>'失败']);
        }
    }
//      public function sendtodo(){
//          //类型
//          $type = $_GET['send_type'];
//          //群发内容
//          $content = $_GET['send_content'];
//          //openid
//          $openid=$_GET['openid'];
//          $openid=explode(',',$openid);
//          if($type=='text'){
//              $msg = [
//                  'touser'=>$openid,
//                  'msgtype'=>$type,
//                  "$type"=>[
//                      'content'=>$content
//                  ]
//              ];
//          }else{
//              $msg = [
//                  'touser'=>$openid,
//                  'msgtype'=>$type,
//                  "$type"=>[
//                      'media_id'=>$content
//                  ]
//              ];
//          }
//          $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.getAccessToken();
//          $data = json_encode($msg,JSON_UNESCAPED_UNICODE);
//          $client = new Client();
//          $response=$client->request('POST',$url,[
//             'body'=>$data
//          ]);
//          if($response){
//              return json_encode(['code'=>1,'msg'=>'成功']);
//          }else{
//              return json_encode(['code'=>2,'msg'=>'失败']);
//          }
//      }
}
