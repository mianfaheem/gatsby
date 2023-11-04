
<?php 
/**
 * Plugin Name:       Custom fields GraphQL
 * Description:       Custom fields in Qraphql.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Wali ur Rehman
 * **/



// to add custom field in wordpress dashboard

add_filter('graphql_input_fields', function($input_fields, $type_name) {
  if ($type_name === "RegisterUserInput") {
    $input_fields['phone'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s Phone number .', 'wp-graphql'),
    ];
     $input_fields['company_name'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s company name.', 'wp-graphql'),
    ];
     $input_fields['company_phone'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s company phone.', 'wp-graphql'),
    ];
     $input_fields['company_email'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s company email.', 'wp-graphql'),
    ];
     $input_fields['mailing_address'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s mailing address.', 'wp-graphql'),
    ];
     $input_fields['city'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s city.', 'wp-graphql'),
    ];
     $input_fields['state'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s state.', 'wp-graphql'),
    ];
     $input_fields['zip'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s zip.', 'wp-graphql'),
    ];
    $input_fields['plan'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s plan.', 'wp-graphql'),
    ];
    $input_fields['stripe'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s stripe.', 'wp-graphql'),
    ];
     $input_fields['company_logo'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s Company Logo.', 'wp-graphql'),
    ];
    $input_fields['user_profile'] = [
      'type' => 'String',
      'description' => __('A string containing the user\'s User Profile.', 'wp-graphql'),
    ];
  }

  return $input_fields;
}, 10, 2);
