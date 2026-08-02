<?php $error = Session::get_flash('error'); ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>旅行リスト</title>
</head>
<body>
  <h2>旅行リスト</h2>

  <?php if ($error): ?>
    <p class="error"><?php echo Security::htmlentities($error); ?></p>
  <?php endif; ?>

  <?php if (empty($trips)): ?>
    <p>登録済みの旅行リストがありません。</p>
  <?php else: ?>
    <ul>
      <?php foreach ($trips as $trip): ?>
        <li>
          <span><?php echo $trip['title']; ?></span>
          <span>目標重量: <?php echo $trip['target_weight']; ?>g</span>
          <a href="/trip/edit/<?php echo $trip['id']; ?>">編集</a>
          <form action="/trip/delete/<?php echo $trip['id']; ?>" method="POST">
            <?php echo Form::csrf(); ?>
            <button type="submit" onclick="return confirm('削除してもよろしいですか？');">削除</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <p><a href="/trip/create">+新しい旅行リストを作成</a></p>
</body>
</html>
