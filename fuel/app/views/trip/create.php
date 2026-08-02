<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>旅行リスト作成</title>
</head>
<body>
  <h2>旅行リスト作成</h2>

  <?php if ($error): ?>
    <p class="error"><?php echo Security::htmlentities($error); ?></p>
  <?php endif; ?>

  <form action="/trip/create" method="POST">
    <?php echo Form::csrf(); ?>
    <div>
      <label for="title">旅行名：</label>
      <input type="text" id="title" name="title" required>
    </div>
    <br>
    <div>
      <label for="target_weight">目標重量（g）：</label>
      <input type="number" id="target_weight" name="target_weight" value="7000" min="1">
    </div>
    <br>
    <button type="submit">作成する</button>
  </form>

  <p><a href="/trip">旅行リストへ戻る</a></p>
</body>
</html>
