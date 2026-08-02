<?php

class Controller_Auth extends Controller
{
    /**
     * ユーザー登録画面を表示する（GETアクセス）
     * URL: http://localhost/auth/register
     */
    public function get_register()
    {
        // View (画面) を読み込んで表示
        return View::forge('auth/register');
    }

    /**
     * 登録ボタンが押された時の処理（POSTアクセス）
     */
    public function post_register()
    {
        // 1. フォームから送信されたデータを取得
        $data = array(
            'username' => Input::post('username'),
            'email'    => Input::post('email'),
            'password' => password_hash(Input::post('password'), PASSWORD_BCRYPT), // パスワードを暗号化
        );

        // 2. 先ほど作成した Model_User の create_user 関数を実行！
        $user_id = Model_User::create_user($data);

        // 3. 完了画面（またはログイン画面）へリダイレクト
        Response::redirect('auth/login');
    }
}