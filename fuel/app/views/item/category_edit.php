<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>カテゴリ編集</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="page">
    <h2>カテゴリ編集</h2>

    <?php if ($error): ?>
      <p class="error"><?php echo Security::htmlentities($error); ?></p>
    <?php endif; ?>

    <form action="/item/category_edit/<?php echo $category['id']; ?>" method="POST">
      <?php echo Form::csrf(); ?>
      <input type="hidden" name="trip_id" value="<?php echo (int) $trip_id; ?>">
      <div class="form-group">
        <label class="form-label" for="name">カテゴリ名：</label>
        <input class="form-input" type="text" id="name" name="name" value="<?php echo $category['name']; ?>" required>
      </div>
      <button class="btn" type="submit">更新する</button>
    </form>

    <p class="mt-16"><a href="/item/<?php echo (int) $trip_id; ?>">パッキングリストへ戻る</a></p>
  </div>
</body>
</html>
