<?php

class Controller_Auth extends Controller
{
  /**
   * ログイン・登録画面は、既にログイン済みなら旅行リスト画面へ流す
   */
  public function before()
  {
    parent::before();

    $guest_only_actions = array('login', 'register');

    if (in_array($this->request->action, $guest_only_actions) and Session::get('user_id') !== null) {
      Response::redirect('trip');
    }
  }

  /**
   * ログイン画面を表示する（GETアクセス）
   * URL: http://localhost/auth/login
   */
  public function get_login()
  {
    return View::forge('auth/login');
  }

  /**
   * ログインボタンが押された時の処理（POSTアクセス）
   */
  public function post_login()
  {
    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('auth/login');
    }

    $username = Input::post('username');
    $password = Input::post('password');

    $user = $username ? Model_User::get_by_username($username) : null;

    if (! $user or ! password_verify($password, $user['password'])) {
      Session::set_flash('error', 'ユーザー名またはパスワードが正しくありません。');
      Response::redirect('auth/login');
    }

    // 「ログイン情報を保持する」がチェックされていれば、セッションCookieの有効期限を延長する
    if (Input::post('remember')) {
      Session::instance()->set_config('expiration_time', 60 * 60 * 24 * 30);
    }

    Session::set('user_id', $user['id']);
    Session::set('username', $user['username']);

    Response::redirect('trip');
  }

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
    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('auth/register');
    }

    $username = Input::post('username');
    $password = Input::post('password');

    if (! $username or ! $password) {
      Session::set_flash('error', 'ユーザー名とパスワードを入力してください。');
      Response::redirect('auth/register');
    }

    if (Model_User::get_by_username($username)) {
      Session::set_flash('error', 'そのユーザー名は既に使用されています。');
      Response::redirect('auth/register');
    }

    $data = array(
      'username' => $username,
      'password' => password_hash($password, PASSWORD_BCRYPT), // パスワードを暗号化
    );

    Model_User::create_user($data);

    Session::set_flash('success', '登録が完了しました。ログインしてください。');
    Response::redirect('auth/login');
  }

  /**
   * ログアウトする（POSTアクセス）
   */
  public function post_logout()
  {
    if (! Security::check_token()) {
      Response::redirect('trip');
    }

    Session::destroy();

    Response::redirect('auth/login');
  }
}
