<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 25px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">Home</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    <table>
                        <tr>
                            <td>id</td>
                            <td>商品名</td>
                            <td>商品价格</td>
                        </tr>
                        @foreach($data as $k=>$v)
                        <tr>
                            <td>{{$v->goods_id}}</td>
                            <td>{{$v->goods_name}}</td>
                            <td>{{$v->goods_price}}</td>
                        </tr>
                            @endforeach
                    </table>
                </div>
            </div>
        </div>
        <script src="/js/jquery.js"></script>
        <script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
        <script>
            wx.config({
                debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                appId: "{{$jsconfig['appId']}}", // 必填，公众号的唯一标识
                timestamp: "{{$jsconfig['timestamp']}}", // 必填，生成签名的时间戳
                nonceStr: "{{$jsconfig['nonceStr']}}", // 必填，生成签名的随机串
                signature: "{{$jsconfig['signature']}}",// 必填，签名
                jsApiList: ['chooseImage','updateTimelineShareData'] // 必填，需要使用的JS接口列表
            });
            wx.ready(function () {      //需在用户可能点击分享按钮前就先调用
                wx.updateTimelineShareData({
                    title: '最新商品', // 分享标题
                    link: 'http://1809bilige.comcto.com/newgoods', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                    imgUrl: 'http://img5.imgtn.bdimg.com/it/u=2373363566,4017206359&fm=200&gp=0.jpg', // 分享图标
                    success: function () {
                        // 设置成功
                    }
                })
            });
        </script>
    </body>
</html>
