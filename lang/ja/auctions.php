<?php

return [
    // Index
    'page_title'        => 'オークションプール',
    'heading'           => 'オークションプール',
    'publish_vehicle'   => '＋車両を公開',
    'no_auctions'       => 'オークションが見つかりません。',

    // Table columns
    'vehicle'           => '車両',
    'status'            => 'ステータス',
    'bid_window'        => '入札期間',
    'bid_count'         => '入札数',
    'highest_bid'       => '最高入札額',
    'view_bids'         => '入札を見る',

    // Status labels
    'status_open'       => '公開中',
    'status_expired'    => '期限切れ',
    'status_closed'     => 'クローズ',
    'status_awarded'    => '落札済み',

    // Create
    'create_title'      => 'オークションに公開',
    'create_heading'    => '車両をオークションに公開',
    'select_vehicle'    => '車両',
    'select_vehicle_placeholder' => '査定済みの申請を選択',
    'no_eligible_vehicles' => 'オークション対象の査定済み申請がありません。',
    'start_time'        => '開始日時',
    'end_time'          => '終了日時',
    'reserve_price'     => '最低落札価格',
    'reserve_price_hint' => 'ディーラーには非公開',
    'reserve_price_placeholder' => '任意：最低入札額',
    'publish_button'    => 'オークションに公開',

    // Show (Detail)
    'detail_title'      => 'オークション詳細',
    'detail_heading'    => 'オークション #:id',
    'bids_heading'      => '入札一覧',
    'no_bids'           => 'まだ入札がありません。',
    'dealer'            => 'ディーラー',
    'dealer_id_label'   => 'ディーラー #:id',
    'bid_amount'        => '入札額',
    'bid_submitted_at'  => '入札日時',
    'award_winner_button' => '落札者を決定',
    'award_confirm'     => 'この入札を落札者として決定しますか？この操作は元に戻せません。',
    'winner_badge'      => '落札者',
    'vehicle_info'      => '車両情報',
    'view_appraisal'    => '査定詳細を見る',

    // Notifications (Flash)
    'notify_published_title'     => '成功',
    'notify_published_message'   => '車両がオークションプールに公開されました。',
    'notify_publish_error_title' => '公開失敗',
    'notify_publish_error_message' => 'オークションの公開中にエラーが発生しました。もう一度お試しください。',
    'notify_awarded_title'       => '落札者決定',
    'notify_awarded_message'     => '落札者が決定し、ディーラーに通知されました。',
    'notify_award_error_title'   => '決定失敗',
    'notify_award_error_message' => '落札者の決定中にエラーが発生しました。もう一度お試しください。',
    'notify_already_awarded'     => 'このオークションはすでに落札者が決定されています。',

    // FCM Push Notifications
    'fcm_new_auction_title' => '新着オークション',
    'fcm_new_auction_body'  => ':brand :model のオークションが開始されました。',
    'fcm_won_title'         => 'オークション落札！',
    'fcm_won_body'          => 'おめでとうございます！:brand :model のオークションに落札しました。',
    'fcm_closed_title'      => 'オークション終了',
    'fcm_closed_body'       => ':brand :model のオークションが終了しました。',
];
