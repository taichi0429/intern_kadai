<?php
$error = Session::get_flash('error');
$success = Session::get_flash('success');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>パッキングリスト</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
  <div class="page">
    <div class="page-header">
      <h2><?php echo Security::htmlentities($trip['title']); ?> のパッキングリスト</h2>
      <form action="/auth/logout" method="POST">
        <?php echo Form::csrf(); ?>
        <button class="btn-link" type="submit">ログアウト</button>
      </form>
    </div>

    <?php if ($error): ?>
      <p class="error"><?php echo Security::htmlentities($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
      <p class="success"><?php echo Security::htmlentities($success); ?></p>
    <?php endif; ?>

    <div id="packing-app">
      <div class="weight-meter">
        <progress data-bind="attr: { value: totalWeight, max: targetWeight }, css: meterClass"></progress>
        <p class="weight-meter-text">
          現在の総重量: <span data-bind="text: totalWeight"></span>g
          / 目標重量: <span data-bind="text: targetWeight"></span>g
          (<span data-bind="text: percentage"></span>%)
        </p>
      </div>

      <div class="flex gap-12 mb-16">
        <?php if (! empty($categories)): ?>
          <button class="btn" type="button" onclick="document.getElementById('item-modal').showModal()">+持ち物を追加</button>
        <?php endif; ?>
        <button class="btn" type="button" onclick="document.getElementById('category-modal').showModal()">+新しいカテゴリを追加</button>
        <button class="btn" type="button" onclick="document.getElementById('category-list-modal').showModal()">カテゴリ一覧</button>
      </div>

      <?php if (empty($categories)): ?>
        <p class="text-muted">先にカテゴリを作成してください。</p>
      <?php endif; ?>

      <div data-bind="foreach: groupedItems">
        <h4 data-bind="text: categoryName"></h4>
        <ul class="item-list" data-bind="foreach: items">
          <li class="item-row">
            <label class="item-label">
              <input type="checkbox" data-bind="checked: isPacked">
              <span data-bind="text: name"></span>
              (<span data-bind="text: weight"></span>g &times; <span data-bind="text: quantity"></span>)
            </label>
            <span class="item-actions">
              <a data-bind="attr: { href: '/item/item_edit/' + id }">編集</a>
              <form method="POST" data-bind="attr: { action: '/item/item_delete/' + id }">
                <input type="hidden" name="fuel_csrf_token" data-bind="value: $root.csrfToken">
                <button class="btn-link" type="submit" onclick="return confirm('削除してもよろしいですか？');">削除</button>
              </form>
            </span>
          </li>
        </ul>
      </div>
    </div>

    <p class="mt-16"><a href="/trip">旅行リストへ戻る</a></p>
  </div>

  <?php if (! empty($categories)): ?>
    <dialog id="item-modal">
      <h3>持ち物を追加</h3>
      <form action="/item/item_create" method="POST">
        <?php echo Form::csrf(); ?>
        <input type="hidden" name="trip_id" value="<?php echo (int) $trip['id']; ?>">
        <div class="form-group">
          <label class="form-label" for="item-category-id">カテゴリ：</label>
          <select class="form-select" id="item-category-id" name="category_id" required>
            <?php foreach ($categories as $category): ?>
              <option value="<?php echo (int) $category['id']; ?>"><?php echo Security::htmlentities($category['name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label" for="item-name">アイテム名：</label>
          <input class="form-input" type="text" id="item-name" name="name" required>
        </div>
        <div class="form-group">
          <label class="form-label" for="item-weight">重量（g）※任意：</label>
          <input class="form-input" type="number" id="item-weight" name="weight" min="0">
        </div>
        <div class="form-group">
          <label class="form-label" for="item-quantity">数量：</label>
          <input class="form-input" type="number" id="item-quantity" name="quantity" value="1" min="1" required>
        </div>
        <div class="flex gap-12">
          <button class="btn" type="submit">追加</button>
          <button class="btn-link" type="button" onclick="document.getElementById('item-modal').close()">キャンセル</button>
        </div>
      </form>
    </dialog>
  <?php endif; ?>

  <dialog id="category-modal">
    <h3>新しいカテゴリを追加</h3>
    <form action="/item/category_create" method="POST">
      <?php echo Form::csrf(); ?>
      <input type="hidden" name="trip_id" value="<?php echo (int) $trip['id']; ?>">
      <div class="form-group">
        <label class="form-label" for="category-name">カテゴリ名：</label>
        <input class="form-input" type="text" id="category-name" name="name" required>
      </div>
      <div class="flex gap-12">
        <button class="btn" type="submit">追加</button>
        <button class="btn-link" type="button" onclick="document.getElementById('category-modal').close()">キャンセル</button>
      </div>
    </form>
  </dialog>

  <dialog id="category-list-modal">
    <h3>カテゴリ一覧</h3>
    <?php if (empty($categories)): ?>
      <p class="text-muted">カテゴリがありません。</p>
    <?php else: ?>
      <ul class="item-list">
        <?php foreach ($categories as $category): ?>
          <li class="item-row">
            <span><?php echo Security::htmlentities($category['name']); ?></span>
            <span class="item-actions">
              <a href="/item/category_edit/<?php echo (int) $category['id']; ?>?trip_id=<?php echo (int) $trip['id']; ?>">編集</a>
              <form action="/item/category_delete/<?php echo (int) $category['id']; ?>" method="POST">
                <?php echo Form::csrf(); ?>
                <input type="hidden" name="trip_id" value="<?php echo (int) $trip['id']; ?>">
                <button class="btn-link" type="submit" onclick="return confirm('削除してもよろしいですか？');">削除</button>
              </form>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    <div class="flex gap-12">
      <button class="btn-link" type="button" onclick="document.getElementById('category-list-modal').close()">閉じる</button>
    </div>
  </dialog>

  <script src="/assets/js/knockout.js"></script>
  <script src="/assets/js/packing.js"></script>
  <script id="items-data" type="application/json"><?php echo $items_json; ?></script>
  <script>
    (function () {
      var initialItems = JSON.parse(document.getElementById('items-data').textContent);
      var config = {
        targetWeight: <?php echo (int) $trip['target_weight']; ?>,
        warningThreshold: <?php echo (int) $warning_threshold; ?>,
        dangerThreshold: <?php echo (int) $danger_threshold; ?>,
        csrfToken: '<?php echo Security::fetch_token(); ?>'
      };

      ko.applyBindings(
        new PackingListViewModel(<?php echo (int) $trip['id']; ?>, initialItems, config),
        document.getElementById('packing-app')
      );
    })();
  </script>
</body>
</html>
