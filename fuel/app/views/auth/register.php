<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>新規ユーザー登録</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="page">
    <h2>新規ユーザー登録</h2>

    <?php if ($error): ?>
      <p class="error"><?php echo Security::htmlentities($error); ?></p>
    <?php endif; ?>

    <!-- POST送信で /auth/register にデータを送る -->
    <form action="/auth/register" method="POST">
      <?php echo Form::csrf(); ?>
      <div class="form-group">
        <label class="form-label" for="username">ユーザー名：</label>
        <input class="form-input" type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="password">パスワード：</label>
        <input class="form-input" type="password" id="password" name="password" required>
      </div>
      <button class="btn" type="submit">登録する</button>
    </form>

    <p class="mt-16"><a href="/auth/login">ログインはこちら</a></p>
  </div>
</body>
</html>
