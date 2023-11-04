<?php 
/**
 * Plugin Name:       Zoho Webhooks
 * Description:       Webhooks to create, delete or update data
 * Version:           1.0
 * Requires at least: 5.2 
 * Requires PHP:      7.2
 * Author:            Wali ur Rehman
 *
 **/

// To Delete Ticket
add_action('init','delete_ticket');
function delete_ticket(){
  register_rest_route('/api/v1/','deleteTicket',array('method'=>'DELETE','callback'=>'deleteTicket'));
}

function deleteTicket()
{
  $data=array();
  $data['status']='OK';
  $data['message']='You have reached the server';
  return  http_response_code(200);
}

// To Update Ticket
add_action( 'rest_api_init', 'update_ticket' );
function update_ticket(){
  register_rest_route('/api/v1/','updateTicket',array(
    'methods'=>'POST','callback'=>'updateTicket'
  ));
}
function updateTicket( $request )
{
    $params = $request->get_body();

    $data=json_decode($params);
    global $wpdb; 
    $ticket=array();
    $ticket['subject']=$data[0]->payload->subject;
    $ticket['description']=$data[0]->payload->description;
    $ticket['priority']=$data[0]->payload->priority;
	  $wpdb->update('wp_zoho_tickets', $ticket, array( 'id' =>$data[0]->payload->id ));
    return new WP_REST_Response( $data, 200 );
}



// // To Create Ticket
// add_action( 'init', 'create_ticket' );
// function create_ticket(){
//   register_rest_route('/api/v1/','createTicket',array('method'=>'PATCH','callback'=>'createTicket'));
// }
// function createTicket()
// {
//   $data=array();
//   $data['status']='OK';
//   $data['message']='You have reached the server';
//   return  http_response_code(200);
// }

