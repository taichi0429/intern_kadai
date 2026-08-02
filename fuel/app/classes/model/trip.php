<?php

class Model_Trip
{
  /**
   * ログイン中ユーザーの旅行リストを一覧取得する
   *
   * @param int $user_id
   * @return array
   */
  public static function get_by_user($user_id)
  {
    return DB::select()
      ->from('trips')
      ->where('user_id', '=', $user_id)
      ->order_by('created_at', 'desc')
      ->execute()
      ->as_array();
  }

  /**
   * 指定ユーザーが所有する旅行リストを1件取得する（他ユーザーの旅行は取得できない）
   *
   * @param int $id
   * @param int $user_id
   * @return array|null
   */
  public static function get_by_id_and_user($id, $user_id)
  {
    $result = DB::select()
      ->from('trips')
      ->where('id', '=', $id)
      ->where('user_id', '=', $user_id)
      ->execute()
      ->current();

    return $result ? $result : null;
  }

  /**
   * 旅行リストを新規作成する
   *
   * @param array $data ['user_id' => ..., 'title' => ..., 'target_weight' => ...]
   * @return int 発行された旅行ID
   */
  public static function create_trip($data)
  {
    $now = Date::time()->format('mysql');
    $data['created_at'] = $now;
    $data['updated_at'] = $now;

    list($insert_id, $affected_rows) = DB::insert('trips')
      ->set($data)
      ->execute();

    return $insert_id;
  }

  /**
   * 旅行リストを更新する
   *
   * @param int $id
   * @param array $data
   * @return int 影響を受けた行数
   */
  public static function update_trip($id, $data)
  {
    $data['updated_at'] = Date::time()->format('mysql');

    return DB::update('trips')
      ->set($data)
      ->where('id', '=', $id)
      ->execute();
  }

  /**
   * 旅行リストを削除する
   *
   * @param int $id
   * @return int 影響を受けた行数
   */
  public static function delete_trip($id)
  {
    return DB::delete('trips')
      ->where('id', '=', $id)
      ->execute();
  }
}
