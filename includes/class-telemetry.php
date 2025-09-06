<?php
/**
 * Simple telemetry helper for storing compact stats.
 */
class IELTS_Telemetry {
    /**
     * Save stats for a user and item.
     *
     * @param int   $user_id User identifier.
     * @param int   $item_id Item identifier.
     * @param array $stats   Key/value stats to store.
     */
    public static function save_stats( int $user_id, int $item_id, array $stats ) : void {
        $record = [
            'item'  => $item_id,
            'stats' => $stats,
            'time'  => current_time( 'mysql' ),
        ];
        add_user_meta( $user_id, 'ielts_stats', $record );
    }
}
