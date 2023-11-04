
<?php 
/**
 * Plugin Name:       Custom DB fields
 * Description:       Custom fields in dashbaord.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Wali ur Rehman
 * **/



// to add custom field in wordpress dashboard

function new_contact_methods( $contactmethods ) {
    $contactmethods['phone'] = 'Phone Number';
    $contactmethods['company_name'] = 'Comapny Name';
    $contactmethods['company_phone'] = 'Comapny Phone';
    $contactmethods['company_email'] = 'Comapny Email';
    $contactmethods['mailing_address'] = 'Mailing Address';
    $contactmethods['city'] = 'City';
    $contactmethods['state'] = 'State';
    $contactmethods['zip'] = 'Zip code';
    $contactmethods['plan'] = 'Plan';
    $contactmethods['stripe'] = 'Stripe ID';

    return $contactmethods;
}
add_filter( 'user_contactmethods', 'new_contact_methods', 10, 1 );


// function new_modify_user_table( $column ) {
//     $column['phone'] = 'Phone';
//     return $column;
// }
// add_filter( 'manage_users_columns', 'new_modify_user_table' );

// function new_modify_user_table_row( $val, $column_name, $user_id ) {
//     switch ($column_name) {
//         case 'phone' :
//             return get_the_author_meta( 'phone', $user_id );
//         default:
//     }
//     return $val;
// }
// add_filter( 'manage_users_custom_column', 'new_modify_user_table_row', 10, 3 );

//to add edit fomate

function modify_user_table( $column ) {
    $column['phone'] = 'Phone';
    $column['company_name'] = 'Comapny Name';
    $column['stripe'] = 'Stripe ID';

    return $column;
}
add_filter( 'manage_users_columns', 'modify_user_table' );

function modify_user_table_row( $val, $column_name, $user_id ) {
    switch ($column_name) {
        case 'phone' :
            return get_the_author_meta( 'phone', $user_id );
        case 'company_name' :
            return get_the_author_meta( 'company_name', $user_id );
        case 'company_phone' :
            return get_the_author_meta( 'company_phone', $user_id );
        case 'company_email' :
            return get_the_author_meta( 'company_email', $user_id );
        case 'mailing_address' :
            return get_the_author_meta( 'mailing_address', $user_id );
        case 'city' :
            return get_the_author_meta( 'city', $user_id );
        case 'state' :
            return get_the_author_meta( 'state', $user_id );
        case 'zip' :
            return get_the_author_meta( 'zip', $user_id );
        case 'plan' :
            return get_the_author_meta( 'plan', $user_id );
        case 'stripe' :
            return get_the_author_meta( 'stripe', $user_id );
            
        default:
    }
    return $val;
}
add_filter( 'manage_users_custom_column', 'modify_user_table_row', 10, 3 );