<?php

class Model_Item
{
  /**
   * 指定した旅行の持ち物一覧を、カテゴリ名付きで取得する
   *
   * @param int $trip_id
   * @return array
   */
  public static function get_by_trip($trip_id)
  {
    return DB::select('items.*', array('categories.name', 'category_name'))
      ->from('items')
      ->join('categories')
      ->on('categories.id', '=', 'items.category_id')
      ->where('items.trip_id', '=', $trip_id)
      ->order_by('categories.created_at', 'asc')
      ->order_by('items.created_at', 'asc')
      ->execute()
      ->as_array();
  }

  /**
   * 所有旅行に紐づく持ち物を1件取得する（他ユーザーの持ち物は取得できない）
   *
   * @param int $id
   * @param int $user_id
   * @return array|null
   */
  public static function get_owned_item($id, $user_id)
  {
    $result = DB::select('items.*')
      ->from('items')
      ->join('trips')
      ->on('trips.id', '=', 'items.trip_id')
      ->where('items.id', '=', $id)
      ->where('trips.user_id', '=', $user_id)
      ->execute()
      ->current();

    return $result ? $result : null;
  }

  /**
   * 持ち物を新規作成する
   *
   * @param array $data
   * @return int 発行された持ち物ID
   */
  public static function create_item($data)
  {
    $now = Date::time()->format('mysql');
    $data['created_at'] = $now;
    $data['updated_at'] = $now;

    list($insert_id, $affected_rows) = DB::insert('items')
      ->set($data)
      ->execute();

    return $insert_id;
  }

  /**
   * 持ち物を更新する
   *
   * @param int $id
   * @param array $data
   * @return int 影響を受けた行数
   */
  public static function update_item($id, $data)
  {
    $data['updated_at'] = Date::time()->format('mysql');

    return DB::update('items')
      ->set($data)
      ->where('id', '=', $id)
      ->execute();
  }

  /**
   * パッキング完了状態のみを更新する
   *
   * @param int $id
   * @param bool $is_packed
   * @return int 影響を受けた行数
   */
  public static function update_packed($id, $is_packed)
  {
    return DB::update('items')
      ->set(array(
        'is_packed'  => $is_packed ? 1 : 0,
        'updated_at' => Date::time()->format('mysql'),
      ))
      ->where('id', '=', $id)
      ->execute();
  }

  /**
   * 持ち物を削除する
   *
   * @param int $id
   * @return int 影響を受けた行数
   */
  public static function delete_item($id)
  {
    return DB::delete('items')
      ->where('id', '=', $id)
      ->execute();
  }

  /**
   * 指定した旅行の持ち物の総重量(g)を取得する（パッキング完了状態を問わず全アイテム合計）
   *
   * @param int $trip_id
   * @return int
   */
  public static function get_total_weight($trip_id)
  {
    $result = DB::select(array(DB::expr('COALESCE(SUM(weight * quantity), 0)'), 'total_weight'))
      ->from('items')
      ->where('trip_id', '=', $trip_id)
      ->execute()
      ->current();

    return $result ? (int) $result['total_weight'] : 0;
  }

  /**
   * 指定カテゴリに紐づく持ち物の件数を取得する（カテゴリ削除可否の判定に使用）
   *
   * @param int $category_id
   * @return int
   */
  public static function count_by_category($category_id)
  {
    $result = DB::select(array(DB::expr('COUNT(*)'), 'cnt'))
      ->from('items')
      ->where('category_id', '=', $category_id)
      ->execute()
      ->current();

    return $result ? (int) $result['cnt'] : 0;
  }
}
