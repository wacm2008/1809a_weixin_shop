<?php

namespace App\Http\Controllers;

use App\QcodeModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\WxuserModel;
use App\WxvoiceModel;
use App\WxfotoModel;
use App\WxtextModel;
use App\GoodsModel;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Facades\Storage;
class WxController extends Controller
{
    public function valid(){
        echo $_GET['echostr'];
    }
    public function valide(){
        //接收微信服务器推送
        $content=file_get_contents("php://input");
        $time=date('Y-m-d H:i:s',time());
        $str=$time.$content."\n";
        file_put_contents("logs/wxlog.log",$str,FILE_APPEND);
        $data=simplexml_load_string($content);
        //var_dump($data);
//         echo 'ToUserName: '. $data->ToUserName;echo '</br>';        // 公众号ID
//         echo 'FromUserName: '. $data->FromUserName;echo '</br>';    // 用户OpenID
//         echo 'CreateTime: '. $data->CreateTime;echo '</br>';        // 时间戳
//         echo 'MsgType: '. $data->MsgType;echo '</br>';              // 消息类型
//         echo 'Event: '. $data->Event;echo '</br>';                  // 事件类型
//         echo 'EventKey: '. $data->EventKey;echo '</br>';
        $wx_id = $data->ToUserName;// 公众号ID
        $openid = $data->FromUserName;//用户OpenID
        $event = $data->Event;//事件类型
        //使用guzzle
        $clinet = new Client();
        $msg_type=$data->MsgType;
        //扫码关注事件
        if($event=='subscribe'){
            if($msg_type=='event'){
                $user=QcodeModel::where(['openid'=>$openid])->first();
                if($user){
                    $data=GoodsModel::orderBy('goods_id','desc')->take(1)->get()->toArray();
                    $goods_name=$data[0]['goods_name'];
                    $str="bienvenido a volverte".$user['nickname'];
                    $url="http://1809bilige.comcto.com/newgoods";
                    $urli='http://img4.imgtn.bdimg.com/it/u=2861992681,4269596371&fm=26&gp=0.jpg';
                    $response_xml='<xml>
                                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                  <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                  <CreateTime>'.time().'</CreateTime>
                                  <MsgType><![CDATA[news]]></MsgType>
                                  <ArticleCount>1</ArticleCount>
                                  <Articles>
                                    <item>
                                      <Title><![CDATA['.$str.']]></Title>
                                      <Description><![CDATA['.$goods_name.']]></Description>
                                      <PicUrl><![CDATA['.$urli.']]></PicUrl>
                                      <Url><![CDATA['.$url.']]></Url>
                                    </item>
                                  </Articles>
                                </xml>';
                    echo $response_xml;
                }else{
                    $arr = $this->getUserInfo($openid);
                    $useinfo = [
                        'openid'    => $arr['openid'],
                        'nickname'  => $arr['nickname'],
                        'sex'  => $arr['sex'],
                        'headimgurl'  => $arr['headimgurl'],
                    ];
                    $re = QcodeModel::insertGetId($useinfo);
                    if($re){
                        $data=GoodsModel::orderBy('goods_id','desc')->take(1)->get()->toArray();
                        $goods_name=$data[0]['goods_name'];
                        $str="muy contento de seguirme".$user['nickname'];;
                        $url="http://1809bilige.comcto.com/newgoods";
                        $urli='http://img4.imgtn.bdimg.com/it/u=2861992681,4269596371&fm=26&gp=0.jpg';
                        $response_xml='<xml>
                                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                  <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                  <CreateTime>'.time().'</CreateTime>
                                  <MsgType><![CDATA[news]]></MsgType>
                                  <ArticleCount>1</ArticleCount>
                                  <Articles>
                                    <item>
                                      <Title><![CDATA['.$str.']]></Title>
                                      <Description><![CDATA['.$goods_name.']]></Description>
                                      <PicUrl><![CDATA['.$urli.']]></PicUrl>
                                      <Url><![CDATA['.$url.']]></Url>
                                    </item>
                                  </Articles>
                                </xml>';
                        echo $response_xml;
                    }
                }
            }
            //根据openid判断用户是否已存在
            $local_user = WxuserModel::where(['openid'=>$openid])->first();
            if($local_user){
                //用户之前关注过
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. 'gracias por haberte vuelto '. $local_user['nickname'] .']]></Content></xml>';
            }else{
                //用户首次关注 获取用户信息
                $arr = $this->getUserInfo($openid);
                //用户信息入库
                $user_info = [
                    'openid'    => $arr['openid'],
                    'nickname'  => $arr['nickname'],
                    'sex'  => $arr['sex'],
                    'headimgurl'  => $arr['headimgurl'],
                ];
                $id = WxuserModel::insertGetId($user_info);
                echo '<xml><ToUserName><![CDATA['.$openid.']]></ToUserName><FromUserName><![CDATA['.$wx_id.']]></FromUserName><CreateTime>'.time().'</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA['. 'gracias por seguirme '. $arr['nickname'] .']]></Content></xml>';
            }
        }
        //图片素材处理
        if($msg_type=='image'){
            //$url=$data->PicUrl;
            //$response=$clinet->get(new Uri($url));
            //$img=file_get_contents($data->PicUrl);
            //$file_name=time().mt_rand(11111,99999).'.jpg';
            //$foto=file_put_contents("wx/images/".$file_name,$img);//下载到本地
            //MediaId url
            //获取扩展文件
            $media_id=$data->MediaId;
            $urli='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getAccessToken().'&media_id='.$media_id;
            $response=$clinet->get(new Uri($urli));
            $headers=$response->getHeaders();//获取响应头信息
            //var_dump($headers);
            $file_info=$headers['Content-disposition'][0];//获取文件名
            $file_name=rtrim(substr($file_info,-20),'""');
            $new_file_name='weixin/foto/'.substr(md5(time().mt_rand()),10,8).'_'.$file_name;
            //echo $new_file_name;
            //保存文件
            $foto=Storage::put($new_file_name,$response->getBody());
            //var_dump($foto);
            //获取用户信息
            //$arr = $this->getUserInfo($openid);
            //图片入库
            $foto_info=[
                'openid'    => $openid,
                'f_time' => time(),
                'foto_address'  => $new_file_name,
            ];
            $res = WxfotoModel::insertGetId($foto_info);
            $response_xml='<xml>
                                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                      <CreateTime>'.time().'</CreateTime>
                                      <MsgType><![CDATA[text]]></MsgType>
                                      <Content><![CDATA[gracias por su mensaje]]></Content>
                                   </xml>';
            echo $response_xml;
        }else if($msg_type=='voice'){
            //语音处理
            $media_id=$data->MediaId;
            $url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->getAccessToken().'&media_id='.$media_id;
            //echo $url;
            $response=$clinet->get(new Uri($url));
            //$amr=file_get_contents($url);
            $file_name = 'weixin/voice/'.time().mt_rand(11111,99999).'.mp3';
            //$voice = file_put_contents('weixin/voice/'.$file_name,$amr);
            $voice=Storage::put($file_name,$response->getBody());
            //var_dump($voice);
            //获取用户信息
            //$arr = $this->getUserInfo($openid);
            //语音入库
            $voice_info=[
                'openid'    => $openid,
                'voice_address'  => $file_name,
                'v_time' => time(),
            ];
            $res = WxvoiceModel::insertGetId($voice_info);
            $response_xml='<xml>
                                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                      <CreateTime>'.time().'</CreateTime>
                                      <MsgType><![CDATA[text]]></MsgType>
                                      <Content><![CDATA[gracias por su mensaje]]></Content>
                                   </xml>';
            echo $response_xml;
        }else if($msg_type=='text'){
            //获取用户信息
            //$arr = $this->getUserInfo($openid);
            //文字入库
            $text_info=[
                'openid'    => $openid,
                'wx_text'  => $data->Content,
                't_time' => time(),
            ];
            $res = WxtextModel::insertGetId($text_info);
            //文本处理
            if(strpos($data->Content,'+天气')){
                //echo $data->Content;exit;
                //获取城市名
                $city=explode('+',$data->Content)[0];
                //echo $city;
                $url='https://free-api.heweather.net/s6/weather/now?key=HE1904161239551731&location='.$city;
                $arr=json_decode(file_get_contents($url),true);
                //print_r($arr);
                if($arr['HeWeather6'][0]['status']=='ok'){
                    $fl=$arr['HeWeather6'][0]['now']['tmp'];//摄氏度
                    $wind_dir=$arr['HeWeather6'][0]['now']['wind_dir'];//风向
                    $wind_sc=$arr['HeWeather6'][0]['now']['wind_sc'];//风力
                    $hum=$arr['HeWeather6'][0]['now']['hum'];//湿度
                    $str="温度：".$fl."\n"."风向：".$wind_dir."\n"."风力：".$wind_sc."\n"."湿度：".$hum."\n";
                    $response_xml='<xml>
                                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                      <CreateTime>'.time().'</CreateTime>
                                      <MsgType><![CDATA[text]]></MsgType>
                                      <Content><![CDATA['.$str.']]></Content>
                                   </xml>';
                    echo $response_xml;
                }else{
                    $response_xml='<xml>
                                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                      <CreateTime>'.time().'</CreateTime>
                                      <MsgType><![CDATA[text]]></MsgType>
                                      <Content><![CDATA[城市名不正确]]></Content>
                                   </xml>';
                    echo $response_xml;
                }
            }else if($data->Content=='最新商品'){
                $data=GoodsModel::orderBy('goods_id','desc')->take(1)->get()->toArray();
                $goods_name=$data[0]['goods_name'];
                $str='最新商品';
                $url="http://1809bilige.comcto.com/newgoods";
                $urli='http://img4.imgtn.bdimg.com/it/u=2861992681,4269596371&fm=26&gp=0.jpg';
                $response_xml='<xml>
                                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                  <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                  <CreateTime>'.time().'</CreateTime>
                                  <MsgType><![CDATA[news]]></MsgType>
                                  <ArticleCount>1</ArticleCount>
                                  <Articles>
                                    <item>
                                      <Title><![CDATA['.$str.']]></Title>
                                      <Description><![CDATA['.$goods_name.']]></Description>
                                      <PicUrl><![CDATA['.$urli.']]></PicUrl>
                                      <Url><![CDATA['.$url.']]></Url>
                                    </item>
                                  </Articles>
                                </xml>';
                echo $response_xml;
            }else if($data->Content=="iPhone X"){
                $goodsInfo=GoodsModel::where(['goods_id'=>3])->first()->toArray();
                $goods_name=$goodsInfo['goods_name'];
                $str=$goods_name;
                $url="http://1809bilige.comcto.com/goodsdetail/3";
                $urli='http://img4.imgtn.bdimg.com/it/u=2861992681,4269596371&fm=26&gp=0.jpg';
                $response_xml='<xml>
                                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                  <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                  <CreateTime>'.time().'</CreateTime>
                                  <MsgType><![CDATA[news]]></MsgType>
                                  <ArticleCount>1</ArticleCount>
                                  <Articles>
                                    <item>
                                      <Title><![CDATA['.$str.']]></Title>
                                      <Description><![CDATA['.$goods_name.']]></Description>
                                      <PicUrl><![CDATA['.$urli.']]></PicUrl>
                                      <Url><![CDATA['.$url.']]></Url>
                                    </item>
                                  </Articles>
                                </xml>';
                echo $response_xml;
            }else{
                $data=GoodsModel::orderBy('goods_id','desc')->take(1)->get()->toArray();
                $goods_name=$data[0]['goods_name'];
                $str='最新商品';
                $url="http://1809bilige.comcto.com/newgoods";
                $urli='http://img4.imgtn.bdimg.com/it/u=2861992681,4269596371&fm=26&gp=0.jpg';
                $response_xml='<xml>
                                  <ToUserName><![CDATA['.$openid.']]></ToUserName>
                                  <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                                  <CreateTime>'.time().'</CreateTime>
                                  <MsgType><![CDATA[news]]></MsgType>
                                  <ArticleCount>1</ArticleCount>
                                  <Articles>
                                    <item>
                                      <Title><![CDATA['.$str.']]></Title>
                                      <Description><![CDATA['.$goods_name.']]></Description>
                                      <PicUrl><![CDATA['.$urli.']]></PicUrl>
                                      <Url><![CDATA['.$url.']]></Url>
                                    </item>
                                  </Articles>
                                </xml>';
                echo $response_xml;
            }
        }
    }
    //获取微信accesstoken
    public function getAccessToken(){
        //是否有缓存
        $key='wx_access_token';
        $token=Redis::get($key);
        //var_dump($token);
        if($token){
            //return $token;
            echo "con cache：";
        }else{
            echo "sin cache：";
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET');
            //echo $url;
            $response=file_get_contents($url);
            //echo $response;
            $arr=json_decode($response,true);
            //print_r($arr);

            //存缓存accesstoken
            $key='wx_access_token';
            Redis::set($key,$arr['access_token']);
            Redis::expire($key,3600);
            $token=$arr['access_token'];
        }
        return $token;
    }
    //微信菜单测试
    public function getaToken(){
        echo $this->getAccessToken();
    }
    //微信菜单创建
    public function createMenu(){
        $server=$_SERVER['REQUEST_SCHEME'] . '://1809bilige.comcto.com/wxweb/v';
        $ser=$_SERVER['REQUEST_SCHEME'] . '://1809bilige.comcto.com/wxweb/k';
        //接口数据
        $url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->getAccessToken();
        //菜单层级
        $post_arr = [
            'button'    => [
                [
                    'type'  => 'click',
                    'name'  => '巴特罗之家',
                    'key'   => 'key_menu_001'
                ],
//                [
//                    'type'  => 'click',
//                    'name'  => '圣家族大教堂',
//                    'key'   => 'key_menu_002'
//                ],
                [
                    'type'  => 'view',
                    'name'  => '签到',
                    'key'   => 'key_menu_002',
                    'url'   => $ser
                ],
                [
                    'type'  => 'view',
                    'name'  => '最新福利',
                    'key'   => 'key_menu_003',
                    'url'   => $server
                ],
            ]
        ];
        //处理中文编码
        $json_str = json_encode($post_arr,JSON_UNESCAPED_UNICODE);
        // 发送请求
        $clinet = new Client();
        //发送 json字符串
        $response = $clinet->request('POST',$url,[
            'body'  => $json_str
        ]);
        //处理响应
        $res_str = $response->getBody();
        //echo $res_str;
        $arr = json_decode($res_str,true);
        print_r($arr);
        //判断错误信息
        if($arr['errcode']>0){
            echo "创建菜单失败";
        }else{
            echo "创建菜单成功";
        }
    }
    //获取微信accesstoken测试
    public function test(){
        $access_token=$this->getAccessToken();
        echo $access_token;
    }
    //获取用户信息
    public function getUserInfo($openid){
        $url='https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->getAccessToken().'&openid='.$openid.'&lang=zh_CN';
        $data=file_get_contents($url);
        $arr=json_decode($data,true);
        return $arr;
    }
    //微信群发
    public function sendQun($openid_arr,$content){
        $msg=[
            'touser'=>$openid_arr,
            'msgtype'=>'text',
            'text'=>[
                'content'=>$content,
            ]
        ];
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->getAccessToken();
        $data = json_encode($msg,JSON_UNESCAPED_UNICODE);
        $clinet = new Client();
        $response = $clinet->request('POST',$url,[
            'body'  => $data
        ]);
        return $response->getBody();
    }
    public function send(){
        $user_list=WxuserModel::where(['sub_status'=>1])->get()->toArray();
        //print_r($user_list);
        $openid_arr=array_column($user_list,'openid');
        //print_r($openid_arr);
        $content=file_get_contents(storage_path('app/proverbio'));
        $arr = explode('|',$content);
        $num = rand(1,100);
        $msg= rtrim($arr[$num],'');
        $res=$this->sendQun($openid_arr,$msg);
        echo $res;
    }
    //授权码
    public function derecho(){
        echo '获取福利 保障您的安全先要授权哦';
        header("refresh:3;url=https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx210a7821bf7f2525&redirect_uri=http%3A%2F%2F1809bilige.comcto.com%2Fwxweb%2Fu&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect");
    }
    public function trono(){
        //http://1809a.weixin.shop/test/urlencode?url=http://1809bilige.comcto.com/wxweb/g
        //http%3A%2F%2F1809bilige.comcto.com%2Fwxweb%2Fg
        echo '签到 保障您的安全先要授权哦';
        header('refresh:3;url=https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx210a7821bf7f2525&redirect_uri=http%3A%2F%2F1809bilige.comcto.com%2Fwxweb%2Fg&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect');
    }
    //签到
    public function sign(){
        $code = $_GET['code'];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
        $response = json_decode(file_get_contents($url),true);
        //print_r($response);
        $access_token = $response['access_token'];
        $openid = $response['openid'];
        //获取用户信息
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_info = json_decode(file_get_contents($url),true);
        //print_r($user_info);
        $local_user = WxuserModel::where(['openid'=>$openid])->first();
        if($local_user){
            //存储redis
            $k = 'firma';
            Redis::lpush($k,$local_user['nickname'].date('Y-m-d H:i:s',time()));
            echo "bienvenida a volverte".$local_user['nickname'];
        }else{
            //用户首次关注 获取用户信息
            $arr = $this->getUserInfo($openid);
            //用户信息入库
            $user_info = [
                'openid'    => $arr['openid'],
                'nickname'  => $arr['nickname'],
                'sex'  => $arr['sex'],
                'headimgurl'  => $arr['headimgurl'],
            ];
            $id = WxuserModel::insertGetId($user_info);
            //存储redis
            $k = 'firma';
            Redis::lpush($k,$arr['nickname'].date('Y-m-d H:i:s',time()));
            echo "muy contento de seguirme".$arr['nickname'];
        }
        echo "<h2>签到成功</h2>";
        //获取签到记录
        $firma_dato = Redis::lrange($k,0,-1);
        echo "<pre>";print_r($firma_dato);echo "</pre>";
    }
    //授权回调
    public function getU(){
        $code = $_GET['code'];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('WX_APPID').'&secret='.env('WX_APPSECRET').'&code='.$code.'&grant_type=authorization_code';
        $response = json_decode(file_get_contents($url),true);
        //print_r($response);
        $access_token = $response['access_token'];
        $openid = $response['openid'];
        //获取用户信息
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $user_info = json_decode(file_get_contents($url),true);
        //print_r($user_info);
        //根据openid判断用户是否已存在
        $local_user = WxuserModel::where(['openid'=>$openid])->first();
        if($local_user){
            //用户之前关注过
            $server=$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] .'/newgoods';
            echo $local_user['nickname'].'gracias por haberte vuelto'.'前往福利中';
            header("refresh:3;url=".$server);
        }else{
            //用户首次关注 获取用户信息
            $arr = $this->getUserInfo($openid);
            //用户信息入库
            $user_info = [
                'openid'    => $arr['openid'],
                'nickname'  => $arr['nickname'],
                'sex'  => $arr['sex'],
                'headimgurl'  => $arr['headimgurl'],
            ];
            $id = WxuserModel::insertGetId($user_info);
            $server=$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] .'/newgoods';
            echo $arr['nickname'].'gracias por seguirme'.'前往福利中';
            header("refresh:3;url=".$server);
        }
    }
    //带参数二维码
    public function qcode(){
        $url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.getAccessToken();
        $data=[
            "expire_seconds"=>666666,
            "action_name"=>"QR_SCENE",
            "action_info"=>[
                "scene"=>[
                    "scene_id"=>123
                ]
            ]
        ];
        //处理中文编码
        $json_str = json_encode($data,JSON_UNESCAPED_UNICODE);
        // 发送请求
        $client = new Client();
        //发送 json字符串
        $response = $client->request('POST',$url,[
            'body'  => $json_str
        ]);
        //处理响应
        $res_str = $response->getBody();
        $arr = json_decode($res_str,true);
        $urli='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$arr['ticket'];
        header("refresh:1;url=$urli");
    }
}
