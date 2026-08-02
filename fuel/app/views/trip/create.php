<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>旅行リスト作成</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="page">
    <h2>旅行リスト作成</h2>

    <?php if ($error): ?>
      <p class="error"><?php echo Security::htmlentities($error); ?></p>
    <?php endif; ?>

    <form action="/trip/create" method="POST">
      <?php echo Form::csrf(); ?>
      <div class="form-group">
        <label class="form-label" for="title">旅行名：</label>
        <input class="form-input" type="text" id="title" name="title" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="target_weight">目標重量（g）：</label>
        <input class="form-input" type="number" id="target_weight" name="target_weight" value="7000" min="1">
      </div>
      <button class="btn" type="submit">作成する</button>
    </form>

    <p class="mt-16"><a href="/trip">旅行リストへ戻る</a></p>
  </div>
</body>
</html>
