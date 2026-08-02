<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログイン</title>
</head>
<body>
  <h2>ログイン</h2>

  <?php if ($error): ?>
    <p class="error"><?php echo Security::htmlentities($error); ?></p>
  <?php endif; ?>

  <!-- POST送信で /auth/login にデータを送る -->
  <form action="/auth/login" method="POST">
    <?php echo Form::csrf(); ?>
    <div>
      <label for="username">ユーザー名：</label>
      <input type="text" id="username" name="username" required>
    </div>
    <br>
    <div>
      <label for="password">パスワード：</label>
      <input type="password" id="password" name="password" required>
    </div>
    <br>
    <div>
      <label for="remember">
        <input type="checkbox" id="remember" name="remember" value="1">
        ログイン情報を保持する
      </label>
    </div>
    <br>
    <button type="submit">ログイン</button>
  </form>

  <p><a href="/auth/register">新規アカウント作成はこちら</a></p>
</body>
</html>
