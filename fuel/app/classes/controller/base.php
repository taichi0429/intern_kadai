<?php

abstract class Controller_Base extends Controller
{
  protected $user_id;

  /**
   * ログイン必須ページ共通のガード処理。未ログインならログイン画面へ流す。
   */
  public function before()
  {
    parent::before();

    $this->user_id = Session::get('user_id');

    if ($this->user_id === null) {
      Response::redirect('auth/login');
    }
  }
}
