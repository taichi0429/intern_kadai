<?php

class Model_Category
{
  /**
   * ログイン中ユーザーのカテゴリを一覧取得する
   *
   * @param int $user_id
   * @return array
   */
  public static function get_by_user($user_id)
  {
    return DB::select()
      ->from('categories')
      ->where('user_id', '=', $user_id)
      ->order_by('created_at', 'asc')
      ->execute()
      ->as_array();
  }

  /**
   * 指定ユーザーが所有するカテゴリを1件取得する（他ユーザーのカテゴリは取得できない）
   *
   * @param int $id
   * @param int $user_id
   * @return array|null
   */
  public static function get_by_id_and_user($id, $user_id)
  {
    $result = DB::select()
      ->from('categories')
      ->where('id', '=', $id)
      ->where('user_id', '=', $user_id)
      ->execute()
      ->current();

    return $result ? $result : null;
  }

  /**
   * カテゴリを新規作成する
   *
   * @param array $data ['user_id' => ..., 'name' => ...]
   * @return int 発行されたカテゴリID
   */
  public static function create_category($data)
  {
    $now = Date::time()->format('mysql');
    $data['created_at'] = $now;
    $data['updated_at'] = $now;

    list($insert_id, $affected_rows) = DB::insert('categories')
      ->set($data)
      ->execute();

    return $insert_id;
  }

  /**
   * カテゴリを更新する
   *
   * @param int $id
   * @param array $data
   * @return int 影響を受けた行数
   */
  public static function update_category($id, $data)
  {
    $data['updated_at'] = Date::time()->format('mysql');

    return DB::update('categories')
      ->set($data)
      ->where('id', '=', $id)
      ->execute();
  }

  /**
   * カテゴリを削除する
   *
   * @param int $id
   * @return int 影響を受けた行数
   */
  public static function delete_category($id)
  {
    return DB::delete('categories')
      ->where('id', '=', $id)
      ->execute();
  }
}
