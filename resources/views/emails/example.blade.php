<!DOCTYPE html>
<html>
<head>
    <title>Example Email</title>
</head>
<body>
<h2>Hello, {{ $user['first_name'] }}</h2>
<h2>{{ $user['last_name'] }}</h2>
<h2>{{ $user['email'] }}</h2>
<h2>{{ $user['message'] }}</h2>


<img src="{{ $user['image'] }}" alt="Example Image">

<p>This is an example email content.</p>
</body>
</html>
