<!DOCTYPE html>
<html>
<head>
    <title>Новая задача</title>
</head>
<body>
<h1>Создана новая задача: {{ $task->title }}</h1>
<p>Описание: {{ $task->description }}</p>
<p>Статус: {{ $task->status }}</p>
</body>
</html>
