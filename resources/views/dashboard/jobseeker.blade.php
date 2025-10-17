<h1>Jobseeker Dashboard</h1>
<p>Hai, {{ $user['name'] }} — Selamat mencari pekerjaan!</p>

<form method="POST" action="/logout">
  @csrf
  <button type="submit">Logout</button>
</form>
