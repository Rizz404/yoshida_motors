<?php

return [
    // Index
    'page_title'        => 'Auction Pool',
    'heading'           => 'Auction Pool',
    'publish_vehicle'   => '+ Publish Vehicle',
    'no_auctions'       => 'No auctions found.',

    // Table columns
    'vehicle'           => 'Vehicle',
    'status'            => 'Status',
    'bid_window'        => 'Bid Window',
    'bid_count'         => 'Bids',
    'highest_bid'       => 'Highest Bid',
    'view_bids'         => 'View Bids',

    // Status labels
    'status_open'       => 'Open',
    'status_expired'    => 'Expired',
    'status_closed'     => 'Closed',
    'status_awarded'    => 'Awarded',

    // Create
    'create_title'      => 'Publish to Auction',
    'create_heading'    => 'Publish Vehicle to Auction',
    'select_vehicle'    => 'Vehicle',
    'select_vehicle_placeholder' => 'Select a completed appraisal',
    'no_eligible_vehicles' => 'No completed appraisals available for auction.',
    'start_time'        => 'Auction Start',
    'end_time'          => 'Auction End',
    'reserve_price'     => 'Reserve Price',
    'reserve_price_hint' => 'hidden from dealers',
    'reserve_price_placeholder' => 'Optional minimum bid',
    'publish_button'    => 'Publish to Auction',

    // Show (Detail)
    'detail_title'      => 'Auction Detail',
    'detail_heading'    => 'Auction #:id',
    'bids_heading'      => 'Submitted Bids',
    'no_bids'           => 'No bids submitted yet.',
    'dealer'            => 'Dealer',
    'dealer_id_label'   => 'Dealer #:id',
    'bid_amount'        => 'Bid Amount',
    'bid_submitted_at'  => 'Submitted At',
    'award_winner_button' => 'Award Winner',
    'award_confirm'     => 'Award this bid as the winner? This action cannot be undone.',
    'winner_badge'      => 'Winner',
    'vehicle_info'      => 'Vehicle Info',
    'view_appraisal'    => 'View Full Appraisal',

    // Notifications (Flash)
    'notify_published_title'     => 'Success',
    'notify_published_message'   => 'Vehicle has been published to the auction pool.',
    'notify_publish_error_title' => 'Publish Failed',
    'notify_publish_error_message' => 'An error occurred while publishing the auction. Please try again.',
    'notify_awarded_title'       => 'Winner Awarded',
    'notify_awarded_message'     => 'The winning bid has been selected and the dealer has been notified.',
    'notify_award_error_title'   => 'Award Failed',
    'notify_award_error_message' => 'An error occurred while awarding the winner. Please try again.',
    'notify_already_awarded'     => 'This auction has already been awarded.',

    // FCM Push Notifications
    'fcm_new_auction_title' => 'New Auction Available',
    'fcm_new_auction_body'  => 'A :brand :model is now open for bidding in the auction pool.',
    'fcm_won_title'         => 'You Won the Auction!',
    'fcm_won_body'          => 'Congratulations! You have won the auction for the :brand :model.',
    'fcm_closed_title'      => 'Auction Closed',
    'fcm_closed_body'       => 'The auction for the :brand :model has concluded.',
];
