<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>ログイン</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="page">
    <h2>ログイン</h2>

    <?php if ($error): ?>
      <p class="error"><?php echo Security::htmlentities($error); ?></p>
    <?php endif; ?>

    <!-- POST送信で /auth/login にデータを送る -->
    <form action="/auth/login" method="POST">
      <?php echo Form::csrf(); ?>
      <div class="form-group">
        <label class="form-label" for="username">ユーザー名：</label>
        <input class="form-input" type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">パスワード：</label>
        <input class="form-input" type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <label for="remember">
          <input type="checkbox" id="remember" name="remember" value="1">
          ログイン情報を保持する
        </label>
      </div>
      <button class="btn" type="submit">ログイン</button>
    </form>

    <p class="mt-16"><a href="/auth/register">新規アカウント作成はこちら</a></p>
  </div>
</body>
</html>
