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
            <td>id</td>
            <td>type</td>
            <td>media_id</td>
            <td>created_at</td>
        </tr>
        @foreach($data as $k=>$v)
        <tr>
            <td>{{$v->mid}}</td>
            <td>{{$v->type}}</td>
            <td>{{$v->media_id}}</td>
            <td>{{$v->created_at}}</td>
        </tr>
        @endforeach
    </table>
</body>
</html>