<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新規ユーザー登録</title>
</head>
<body>
    <h2>新規ユーザー登録</h2>

    <!-- POST送信で /auth/register にデータを送る -->
    <form action="/auth/register" method="POST">
        <div>
            <label for="username">ユーザー名：</label>
            <input type="text" id="username" name="username" required>
        </div>
        <br>
        <div>
            <label for="email">メールアドレス：</label>
            <input type="email" id="email" name="email" required>
        </div>
        <br>
        <div>
            <label for="password">パスワード：</label>
            <input type="password" id="password" name="password" required>
        </div>
        <br>
        <button type="submit">登録する</button>
    </form>
</body>
</html>