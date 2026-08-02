<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>新規ユーザー登録</title>
</head>
<body>
  <h2>新規ユーザー登録</h2>

  <?php if ($error): ?>
    <p class="error"><?php echo Security::htmlentities($error); ?></p>
  <?php endif; ?>

  <!-- POST送信で /auth/register にデータを送る -->
  <form action="/auth/register" method="POST">
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
    <button type="submit">登録する</button>
  </form>

  <p><a href="/auth/login">ログインはこちら</a></p>
</body>
</html>
