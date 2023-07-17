<!DOCTYPE html>
<html>
<head>
    <title>Logged In - Spotify</title>
</head>
<body>
<h1>Welcome, {{ $user->display_name }}!</h1>

<h2>Track Information:</h2>
<p>Track Name: {{ $track->name }}</p>
<p>Artist Name: {{ $track->artists[0]->name }}</p>
</body>
</html>
