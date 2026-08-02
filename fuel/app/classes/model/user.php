<?php
class Model_User
{
    /**
     * IDからユーザー情報を1件取得する
     * 
     * @param int $id
     * @return array|null ユーザー情報の連想配列（見つからなければ null）
     */
    public static function get_by_id($id)
    {
        $result = DB::select()
            ->from('users')
            ->where('id', '=', $id)
            ->execute()
            ->current(); // 連想配列として1件取得

        return $result ? $result : null;
    }

    /**
     * メールアドレスからユーザー情報を1件取得する
     * 
     * @param string $email
     * @return array|null
     */
    public static function get_by_email($email)
    {
        $result = DB::select()
            ->from('users')
            ->where('email', '=', $email)
            ->execute()
            ->current();

        return $result ? $result : null;
    }

    /**
     * 新規ユーザーを手動で追加する（※Auth::create_userを使わない独自登録等の場合）
     * 
     * @param array $data ['username' => '...', 'email' => '...', 'password' => '...']
     * @return int 発行されたユーザーID
     */
    public static function create_user($data)
    {
        // 作成日時をセット
        $data['created_at'] = Date::time()->get_timestamp();

        // INSERT文を実行
        list($insert_id, $affected_rows) = DB::insert('users')
            ->set($data)
            ->execute();

        return $insert_id;
    }

    /**
     * ユーザー情報をupdateする
     * 
     * @param int $id ユーザーID
     * @param array $data 更新したいデータの配列
     * @return int 影響を受けた行数
     */
    public static function update_user($id, $data)
    {
        // 更新日時をセット
        $data['updated_at'] = Date::time()->get_timestamp();

        return DB::update('users')
            ->set($data)
            ->where('id', '=', $id)
            ->execute();
    }
}