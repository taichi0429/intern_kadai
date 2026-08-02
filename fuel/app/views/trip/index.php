<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>旅行リスト</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="page">
    <h2>旅行リスト</h2>

    <?php if ($error): ?>
      <p class="error"><?php echo Security::htmlentities($error); ?></p>
    <?php endif; ?>

    <?php if (empty($trips)): ?>
      <p class="text-muted">登録済みの旅行リストがありません。</p>
    <?php else: ?>
      <ul class="card-list">
        <?php foreach ($trips as $trip): ?>
          <li class="card">
            <p class="card-title"><?php echo $trip['title']; ?></p>

            <div class="weight-meter">
              <progress class="<?php echo $trip['meter_class']; ?>" value="<?php echo (int) $trip['current_weight']; ?>" max="<?php echo (int) $trip['target_weight']; ?>"></progress>
              <p class="weight-meter-text"><?php echo (int) $trip['current_weight']; ?>g / <?php echo $trip['target_weight']; ?>g</p>
            </div>

            <div class="flex gap-12">
              <a href="/item/<?php echo $trip['id']; ?>">パッキングリスト</a>
              <a href="/trip/edit/<?php echo $trip['id']; ?>">編集</a>
              <form action="/trip/delete/<?php echo $trip['id']; ?>" method="POST">
                <?php echo Form::csrf(); ?>
                <button class="btn-link" type="submit" onclick="return confirm('削除してもよろしいですか？');">削除</button>
              </form>
            </div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <p class="mt-16"><a href="/trip/create">+新しい旅行リストを作成</a></p>
  </div>
</body>
</html>
