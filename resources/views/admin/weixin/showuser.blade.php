<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <table border="1">
        <tr>
            <td><input type="checkbox" id="allbox"></td>
            <td width="50" align="center"> wid</td>
            <td width="250" align="center">nickname</td>
        </tr>
        @foreach($data as $k=>$v)
         <tr openid="{{$v->openid}}">
             <td><input type="checkbox" class="box"></td>
             <td width="50" align="center"> {{$v->wid}}</td>
             <td width="250" align="center">{{$v->nickname}}</td>
         </tr>
        @endforeach
    </table><br>
    {{--<p>选择发送内容:<input type="text" id="text"></p>--}}
    <select id="sel">
        <option value="">请选择</option>
        <option value="文字">文字</option>
        <option value="多媒体">多媒体</option>
    </select>
    <p>选择发送内容:<textarea id="text"></textarea></p>
    <input type="submit" value="发送"  id="sub">
</body>
</html>
<script type="text/javascript" src="/js/jquery.js"></script>
<script>
    $(function () {
        //全选
        $('#allbox').click(function(){
            var _this=$(this);
            var estado=_this.prop('checked');
            $('.box').prop('checked',estado);
        })
        $('.box').click(function(){
            var _this=$(this);
            if(_this.prop('checked')==false){
                $('#allbox').prop('checked',false);
            }
        })

        //发送提交
        $('#sub').click(function () {
            var box=$('.box');
            var text=$('#text').val();
            var openid='';
            box.each(function (index) {
                var _this=$(this);
                if(_this.prop('checked')==true){
                    openid += _this.parents('tr').attr('openid') + ',';
                }
            })
            openid=openid.substr(0,openid.length-1);
            if(openid==''){
                alert('至少选择一位发送人');
                return false;
            }
            if(text==''){
                alert('选择发送内容');
                return false;
            }
            // $.get(
            //     //"/sendtodo?openid="+openid+"&text="+text,
            //     '/sendtodo',
            //     {openid:openid,text:text},
            //     function (res) {
            //         console.log(res);
            //     }
            // )
            $.ajax({
                type:'get',
                url : 'sendtodo?openid='+openid+'&text='+text,
                success:function(res){
                    if(res.code==1){
                        alert('发送成功');
                    }
                },
                dataType:'json'
            })
        })
    })
</script>