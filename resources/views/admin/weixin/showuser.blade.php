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
    <p>选择发送内容:<textarea id="text"></textarea></p>
    <input type="submit" value="发送"  id="sub">
    {{--<form action="">--}}
    {{--<table border="1">--}}
        {{--<tr>--}}
            {{--<td colspan="2">--}}
                {{--<h4>消息群发</h4>--}}
            {{--</td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td align="center" width="50%">消息类型:</td>--}}
            {{--<td align="center" width="50%">--}}
                {{--<select id="sel">--}}
                    {{--<option value="">--请选择--</option>--}}
                    {{--<option value="1">文本</option>--}}
                    {{--<option value="2">图片/语音/视频</option>--}}
                {{--</select>--}}
            {{--</td>--}}
        {{--</tr>--}}
        {{--<tr height="40px">--}}
            {{--<td colspan="2" align="right"></td>--}}
        {{--</tr>--}}
        {{--<tr>--}}
            {{--<td colspan="2" align="center"><input type="button" value="发送" id="sub"></td>--}}
        {{--</tr>--}}
    {{--</table>--}}
    {{--</form>--}}
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
        //下拉菜单
        {{--$('#sel').change(function () {--}}
            {{--var _this=$(this);--}}
            {{--var sel=_this.val();--}}
            {{--if(sel==1){--}}
                {{--_this.parents('tr').next('tr').find('td').html("<textarea id='content' type='text'></textarea>");--}}
            {{--}else{--}}
                {{--_this.parents('tr').next('tr').find('td').html("<select id='content'>@foreach($dat as $k=>$v)--}}
                    {{--<option value='{{$v->media_id}}' type='{{$v->type}}' class='content'>{{$v->mid.'-'.$v->type}}</option>@endforeach--}}
                    {{--</select>");--}}
            {{--}--}}
        {{--});--}}
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
            // if($('#sel').val()==''){
            //     alert('选择群发类型');
            //     return false;
            // }
            //获取群发内容
            // var send_content = $('#content').val();
            // if(send_content==''){
            //     alert('内容不能为空');
            //     return false;
            // }
            //获取群发类型
            // if($('#sel').val()==1){
            //     var send_type = $('#content').attr('type');
            // }else{
            //     var _content = $('.content');
            //     var send_type = '';
            //     _content.each(function(index){
            //         if($(this).prop('selected')==true){
            //             send_type = $(this).attr('type');
            //         }
            //     })
            // }
            $.ajax({
                type:'get',
                url : "/admin/sendtodo?openid="+openid+"&text="+text,
                success:function(res){
                    if(res.code==1){
                        alert('成功');
                    }
                },
                dataType:'json'
            })
            // $.get(
            //     "/admin/sendtodo",
            //     {openid:openid,send_content:send_content,send_type:send_type},
            //     function(res){
            //         //console.log(res);
            //         if(res.code==1){
            //             alert('发送成功');
            //         }else{
            //             alert('发送失败');
            //         }
            //     },
            //     'json'
            // );
        })
    })
</script>