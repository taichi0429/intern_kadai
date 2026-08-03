<?php

class Controller_Trip extends Controller_Base
{
  /**
   * 旅行リストの一覧を表示する（GETアクセス）
   * URL: http://localhost/trip
   */
  public function action_index()
  {
    $trips = Model_Trip::get_by_user($this->user_id);

    foreach ($trips as &$trip) {
      $trip['meter_class'] = $this->weight_meter_class($trip['current_weight'], $trip['target_weight']);
    }
    unset($trip);

    return View::forge('trip/index', array('trips' => $trips));
  }

  /**
   * 旅行リスト作成画面を表示する（GETアクセス）
   * URL: http://localhost/trip/create
   */
  public function get_create()
  {
    return View::forge('trip/create');
  }

  /**
   * 旅行リストを新規作成する（POSTアクセス）
   */
  public function post_create()
  {
    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('trip/create');
    }

    $title = Input::post('title');
    $target_weight = $this->normalize_target_weight(Input::post('target_weight'));

    if (! $title) {
      Session::set_flash('error', '旅行名を入力してください。');
      Response::redirect('trip/create');
    }

    Model_Trip::create_trip(array(
      'user_id'       => $this->user_id,
      'title'         => $title,
      'target_weight' => $target_weight,
    ));

    Response::redirect('trip');
  }

  /**
   * 旅行リスト編集画面を表示する（GETアクセス）
   * URL: http://localhost/trip/edit/1
   */
  public function get_edit($id)
  {
    $trip = Model_Trip::get_by_id_and_user($id, $this->user_id);

    if (! $trip) {
      throw new HttpNotFoundException();
    }

    return View::forge('trip/edit', array('trip' => $trip));
  }

  /**
   * 旅行リストを更新する（POSTアクセス）
   */
  public function post_edit($id)
  {
    $trip = Model_Trip::get_by_id_and_user($id, $this->user_id);

    if (! $trip) {
      throw new HttpNotFoundException();
    }

    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('trip/edit/'.$id);
    }

    $title = Input::post('title');
    $target_weight = $this->normalize_target_weight(Input::post('target_weight'));

    if (! $title) {
      Session::set_flash('error', '旅行名を入力してください。');
      Response::redirect('trip/edit/'.$id);
    }

    Model_Trip::update_trip($id, array(
      'title'         => $title,
      'target_weight' => $target_weight,
    ));

    Response::redirect('trip');
  }

  /**
   * 旅行リストを削除する（POSTアクセス）
   */
  public function post_delete($id)
  {
    $trip = Model_Trip::get_by_id_and_user($id, $this->user_id);

    if (! $trip) {
      throw new HttpNotFoundException();
    }

    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('trip');
    }

    Model_Trip::delete_trip($id);

    Response::redirect('trip');
  }

  /**
   * 目標重量の入力値を検証し、未入力・不正値の場合はデフォルト値(7000g)にする
   *
   * @param string|null $value
   * @return int
   */
  private function normalize_target_weight($value)
  {
    if ($value === null or $value === '' or ! is_numeric($value) or $value <= 0) {
      return 7000;
    }

    return (int) $value;
  }

  /**
   * 現在の総重量と目標重量から、重量メーターの警告色クラスを判定する
   *
   * @param int $current_weight
   * @param int $target_weight
   * @return string
   */
  private function weight_meter_class($current_weight, $target_weight)
  {
    $percentage = $target_weight > 0 ? ($current_weight / $target_weight) * 100 : 0;

    if ($percentage >= Config::get('packing.danger_threshold')) {
      return 'weight-danger';
    }

    if ($percentage >= Config::get('packing.warning_threshold')) {
      return 'weight-warning';
    }

    return 'weight-ok';
  }
}
