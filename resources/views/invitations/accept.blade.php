
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Accept Invitation</title></head>
<body>
  <h1>Accept Invitation</h1>
  <form method="POST" action="/api/invitations/accept">
    <label>Token <input type="text" name="token" value="{{ request('token') }}"></label><br>
    <label>Name <input type="text" name="name"></label><br>
    <label>Password <input type="password" name="password"></label><br>
    <button type="submit">Accept</button>
  </form>
</body>
</html>
