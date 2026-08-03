<?php

class Controller_Item extends Controller_Base
{
  /**
   * 指定した旅行のパッキングリスト画面を表示する（GETアクセス）
   * URL: http://localhost/item/{trip_id}
   */
  public function action_index($trip_id)
  {
    $trip = Model_Trip::get_by_id_and_user($trip_id, $this->user_id);

    if (! $trip) {
      throw new HttpNotFoundException();
    }

    $categories = Model_Category::get_by_user($this->user_id);
    $items      = Model_Item::get_by_trip($trip_id);

    $data = array(
      'trip'              => $trip,
      'categories'        => $categories,
      'items_json'        => json_encode($items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
      'warning_threshold' => Config::get('packing.warning_threshold'),
      'danger_threshold'  => Config::get('packing.danger_threshold'),
    );

    // items_jsonを二重エスケープしないよう、このビューはauto_filterを使わず自前でエスケープする
    return View::forge('item/index', $data, false);
  }

  /**
   * 持ち物を新規作成する（POSTアクセス）
   */
  public function post_item_create()
  {
    $trip_id = Input::post('trip_id');
    $trip = Model_Trip::get_by_id_and_user($trip_id, $this->user_id);

    if (! $trip) {
      throw new HttpNotFoundException();
    }

    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('item/'.$trip_id);
    }

    $category_id = Input::post('category_id');
    $category = Model_Category::get_by_id_and_user($category_id, $this->user_id);
    $name = Input::post('name');

    if (! $category or ! $name) {
      Session::set_flash('error', 'カテゴリとアイテム名を入力してください。');
      Response::redirect('item/'.$trip_id);
    }

    Model_Item::create_item(array(
      'trip_id'     => $trip_id,
      'category_id' => $category_id,
      'name'        => $name,
      'weight'      => $this->normalize_positive_int(Input::post('weight'), 0),
      'quantity'    => $this->normalize_positive_int(Input::post('quantity'), 1),
    ));

    Response::redirect('item/'.$trip_id);
  }

  /**
   * 持ち物編集画面を表示する（GETアクセス）
   */
  public function get_item_edit($id)
  {
    $item = Model_Item::get_owned_item($id, $this->user_id);

    if (! $item) {
      throw new HttpNotFoundException();
    }

    $categories = Model_Category::get_by_user($this->user_id);

    return View::forge('item/edit', array('item' => $item, 'categories' => $categories));
  }

  /**
   * 持ち物を更新する（POSTアクセス）
   */
  public function post_item_edit($id)
  {
    $item = Model_Item::get_owned_item($id, $this->user_id);

    if (! $item) {
      throw new HttpNotFoundException();
    }

    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('item/item_edit/'.$id);
    }

    $category_id = Input::post('category_id');
    $category = Model_Category::get_by_id_and_user($category_id, $this->user_id);
    $name = Input::post('name');

    if (! $category or ! $name) {
      Session::set_flash('error', 'カテゴリとアイテム名を入力してください。');
      Response::redirect('item/item_edit/'.$id);
    }

    Model_Item::update_item($id, array(
      'category_id' => $category_id,
      'name'        => $name,
      'weight'      => $this->normalize_positive_int(Input::post('weight'), 0),
      'quantity'    => $this->normalize_positive_int(Input::post('quantity'), 1),
    ));

    Response::redirect('item/'.$item['trip_id']);
  }

  /**
   * 持ち物を削除する（POSTアクセス）
   */
  public function post_item_delete($id)
  {
    $item = Model_Item::get_owned_item($id, $this->user_id);

    if (! $item) {
      throw new HttpNotFoundException();
    }

    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('item/'.$item['trip_id']);
    }

    Model_Item::delete_item($id);

    Response::redirect('item/'.$item['trip_id']);
  }

  /**
   * パッキング完了状態を切り替える（POSTアクセス・非同期通信用、JSONを返す）
   */
  public function post_item_toggle($id)
  {
    $item = Model_Item::get_owned_item($id, $this->user_id);

    // CSRFトークンはリクエストごとにローテーションされるため、成功・失敗にかかわらず
    // 次回リクエスト用の最新トークンをレスポンスに含め、クライアント側で追従させる
    $token_ok = Security::check_token();

    if (! $item or ! $token_ok) {
      return Response::forge(json_encode(array(
        'success'    => false,
        'csrf_token' => Security::fetch_token(),
      )), 400)->set_header('Content-Type', 'application/json');
    }

    $is_packed = Input::post('is_packed') ? 1 : 0;

    Model_Item::update_packed($id, $is_packed);

    $total_weight = Model_Item::get_total_weight($item['trip_id']);

    return Response::forge(json_encode(array(
      'success'      => true,
      'total_weight' => $total_weight,
      'csrf_token'   => Security::fetch_token(),
    )))->set_header('Content-Type', 'application/json');
  }

  /**
   * カテゴリを新規作成する（POSTアクセス）
   */
  public function post_category_create()
  {
    $trip_id = Input::post('trip_id');
    $trip = Model_Trip::get_by_id_and_user($trip_id, $this->user_id);

    if (! $trip) {
      throw new HttpNotFoundException();
    }

    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('item/'.$trip_id);
    }

    $name = Input::post('name');

    if (! $name) {
      Session::set_flash('error', 'カテゴリ名を入力してください。');
      Response::redirect('item/'.$trip_id);
    }

    Model_Category::create_category(array(
      'user_id' => $this->user_id,
      'name'    => $name,
    ));

    Response::redirect('item/'.$trip_id);
  }

  /**
   * カテゴリ編集画面を表示する（GETアクセス）
   */
  public function get_category_edit($id)
  {
    $category = Model_Category::get_by_id_and_user($id, $this->user_id);

    if (! $category) {
      throw new HttpNotFoundException();
    }

    $trip_id = Input::get('trip_id');

    return View::forge('item/category_edit', array('category' => $category, 'trip_id' => $trip_id));
  }

  /**
   * カテゴリを更新する（POSTアクセス）
   */
  public function post_category_edit($id)
  {
    $category = Model_Category::get_by_id_and_user($id, $this->user_id);

    if (! $category) {
      throw new HttpNotFoundException();
    }

    $trip_id = Input::post('trip_id');

    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('item/category_edit/'.$id.'?trip_id='.$trip_id);
    }

    $name = Input::post('name');

    if (! $name) {
      Session::set_flash('error', 'カテゴリ名を入力してください。');
      Response::redirect('item/category_edit/'.$id.'?trip_id='.$trip_id);
    }

    Model_Category::update_category($id, array('name' => $name));

    Response::redirect('item/'.$trip_id);
  }

  /**
   * カテゴリを削除する（POSTアクセス）
   */
  public function post_category_delete($id)
  {
    $category = Model_Category::get_by_id_and_user($id, $this->user_id);

    if (! $category) {
      throw new HttpNotFoundException();
    }

    $trip_id = Input::post('trip_id');

    if (! Security::check_token()) {
      Session::set_flash('error', '不正なリクエストです。もう一度お試しください。');
      Response::redirect('item/'.$trip_id);
    }

    if (Model_Item::count_by_category($id) > 0) {
      Session::set_flash('error', 'このカテゴリにはアイテムが登録されているため削除できません。');
      Response::redirect('item/'.$trip_id);
    }

    Model_Category::delete_category($id);

    Response::redirect('item/'.$trip_id);
  }

  /**
   * 数値入力を検証し、未入力・不正値の場合はデフォルト値にする
   *
   * @param string|null $value
   * @param int $default
   * @return int
   */
  private function normalize_positive_int($value, $default)
  {
    if ($value === null or $value === '' or ! is_numeric($value) or $value < 0) {
      return $default;
    }

    return (int) $value;
  }
}
