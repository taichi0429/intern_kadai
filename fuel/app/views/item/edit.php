<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>持ち物編集</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="page">
    <h2>持ち物編集</h2>

    <?php if ($error): ?>
      <p class="error"><?php echo Security::htmlentities($error); ?></p>
    <?php endif; ?>

    <form action="/item/item_edit/<?php echo $item['id']; ?>" method="POST">
      <?php echo Form::csrf(); ?>
      <div class="form-group">
        <label class="form-label" for="category_id">カテゴリ：</label>
        <select class="form-select" id="category_id" name="category_id" required>
          <?php foreach ($categories as $category): ?>
            <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $item['category_id'] ? 'selected' : ''; ?>>
              <?php echo $category['name']; ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label" for="name">アイテム名：</label>
        <input class="form-input" type="text" id="name" name="name" value="<?php echo $item['name']; ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label" for="weight">重量（g）：</label>
        <input class="form-input" type="number" id="weight" name="weight" value="<?php echo $item['weight']; ?>" min="0">
      </div>
      <div class="form-group">
        <label class="form-label" for="quantity">数量：</label>
        <input class="form-input" type="number" id="quantity" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
      </div>
      <button class="btn" type="submit">更新する</button>
    </form>

    <p class="mt-16"><a href="/item/<?php echo $item['trip_id']; ?>">パッキングリストへ戻る</a></p>
  </div>
</body>
</html>
