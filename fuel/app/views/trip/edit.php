<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>旅行リスト編集</title>
</head>
<body>
  <h2>旅行リスト編集</h2>

  <?php if ($error): ?>
    <p class="error"><?php echo Security::htmlentities($error); ?></p>
  <?php endif; ?>

  <form action="/trip/edit/<?php echo $trip['id']; ?>" method="POST">
    <?php echo Form::csrf(); ?>
    <div>
      <label for="title">旅行名：</label>
      <input type="text" id="title" name="title" value="<?php echo $trip['title']; ?>" required>
    </div>
    <br>
    <div>
      <label for="target_weight">目標重量（g）：</label>
      <input type="number" id="target_weight" name="target_weight" value="<?php echo $trip['target_weight']; ?>" min="1">
    </div>
    <br>
    <button type="submit">更新する</button>
  </form>

  <p><a href="/trip">旅行リストへ戻る</a></p>
</body>
</html>
