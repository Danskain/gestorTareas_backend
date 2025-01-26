<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Shared Task</h1>
    <p>This is shered task</p>
    <p>title: {{ $task->title}}</p>
    <p>Description: {{ $task->description}}</p>
    <p>Status: {{ $status}}</p>
    <p>User shered: {{ $user->name}} :: {{ $user->email}}</p>

</body>
</html>
