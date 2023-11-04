
<?php 
/**
 * Plugin Name:       Zoho with WPGraphql
 * Description:       Zoho with wordpress and Graphql.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Wali ur Rehman
 * **/

define('STRIPE_SECRET_KEY', "sk_test_51Juo3RDnEqnPe9n4mNW9dT6MndGiO9ncIXvGg564Q00I3DIq9fJOwMBPDk0Ue5lqhCG7qvLKtdP64a0TQJS8TzvS00Il4Zhwsb" );

$input=array();
add_action( 'graphql_register_types', function() {

	register_graphql_mutation( 'ZohoAddCompany', [
		'inputFields' => [
			'user_id' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'name' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'email' => [
						'type' => [ 'non_null' => 'String' ],
					],
			'phone' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'logo' => [
				'type' =>  'String' ,
			],
			'zip' => [
				'type' => 'String',
			],
			'city' => [
				'type' => 'String',
			],
			'state' => [
				'type' => 'String',
			],
			'website_url' => [
				'type' => 'String',
			],
			'mailing_address' => [
				'type' => 'String',
			],
		],
		'outputFields' => [
				'companyId' => [
					'type' => 'String',
				],
				'status' => [
					'type' => 'String',
				],

			],
			'mutateAndGetPayload' => function( $input ) {
    		global $wpdb;
    		   $true = $wpdb->get_results('SELECT email FROM wp_zoho_companies WHERE email ="'.$input['email'].'"');
    		  // 	return ['companyId' =>$true[0]->email];
    			if($true[0])
    			{
    				return ['status' => 'Email already Exists'];
    			}
			//	return insertCompany($input);

				global $wpdb;
				$post_data = "email=".$input['email'];
				
				$headers = [
					'Content-Type: application/x-www-form-urlencoded',
				];
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,"https://api.stripe.com/v1/customers");
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY.": "); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$server_output = curl_exec($ch);
				curl_close ($ch);				
				$response = json_decode($server_output);
				$customer_id = $response->id;
				$input['stripe_customer_id'] = $customer_id;

				$company_id = insertCompany($input);

				// $wpdb->update('wp_zoho_companies', array( 'stripe_customer_id'=>$customer_id ) , array( 'id' => $company_id ));

				return [
					'companyId' => $company_id
				];
			}
	]);
	register_graphql_mutation( 'ZohoUpdateComapny', [
		'inputFields' => [
			'id' => [
				'type' => [ 'non_null' => 'Integer' ],
			],
			'name' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'email' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'phone' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'logo' => [
				'type' => 'String'
			],
			'zip' => [
				'type' => 'String',
			],
			'city' => [
				'type' => 'String',
			],
			'state' => [
				'type' => 'String',
			],
			'website_url' => [
				'type' => 'String',
			],
			'mailing_address' => [
				'type' => 'String',
			],
		],
		'outputFields' => [
				'status' => [
					'type' => 'String',
				],

			],
			'mutateAndGetPayload' => function( $input ) {
				global $wpdb;
    		   $true = $wpdb->get_results('SELECT email FROM wp_zoho_companies WHERE email ="'.$input['email'].'" AND id!='.$input['id']);
    			if($true[0])
    			{
    				return ['status' => 'Email already Exists'];
    			}
				$wpdb->update('wp_zoho_companies', $input, array( 'id' => $input['id'] ));
			
				return [
					'status' => 'Company Has Been Updated'
				];
			}
	]);
	register_graphql_mutation( 'addZohoCredentials', [
		'inputFields' => [
			'grant_token' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'refresh_token' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'access_token' => [
						'type' => [ 'non_null' => 'String' ],
					],
			'client_id' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'client_secret' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'scopes' => [
				'type' => [ 'non_null' => 'String' ],
			],
		],
		'outputFields' => [
			'status' => [
				'type' => 'Boolean',
			],
		],
		'mutateAndGetPayload' => function( $input ) {
      	insertZohoCredentialDetails($input);
//     	$zoho_response = CallAPIs('GET', "https://desk.zoho.com/api/v1/tickets?include=contacts,assignee,departments,team,isRead", false);
// 		$obj = json_decode($zoho_response, true);
            return [
				'status' =>true
            ];
		}
	]);
	//done don't touch please
	register_graphql_mutation( 'ZohoCreateContact', [
		'inputFields' => [
			'user_id' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'email' => [
				'type' => 'String',
			  ],
			  'firstName' => [
				'type' => 'String',
			  ],
			  'lastName' => [
				'type' => 'String',
			  ],
			  'type' => [
				'type' => 'String',
			  ],
			  'facebook' => [
				'type' => 'String',
			  ],
			  'twitter' => [
				'type' => 'String',
			  ],
			  'secondaryEmail' => [
				'type' => 'String',
			  ],
			  'phone' => [
				'type' => 'String',
			  ],
			  'mobile' => [
				'type' => 'String',
			  ],
			  'city' => [
				'type' => 'String',
			  ],
			  'country' => [
				'type' => 'String',
			  ],
			  'state' => [
				'type' => 'String',
			  ],
			  'street' => [
				'type' => 'String',
			  ],
			  'zip' => [
				'type' => 'String',
			  ],
			  'description' => [
				'type' => 'String',
			  ],
			  'title' => [
				'type' => 'String',
			  ],
			  'photoURL' => [
				'type' => 'String',
			  ],
			  'webUrl' => [
				'type' => 'String',
			  ],
			  'zip' => [
				'type' => 'String',
			  ],
			  'isDeleted' => [
				'type' => 'boolean',
			  ],
			  'isTrashed' => [
				'type' => 'boolean',
			  ],
			  'isSpam' => [
				'type' => 'boolean',
			  ],
			  'createdTime' => [
				'type' => 'String',
			  ],
			  'modifiedTime' => [
				'type' => 'String',
			  ],
			  'accountId' => [
				'type' => 'String',
			  ],
			  'ownerId' => [
				'type' => 'String',
			  ],
		],
		'outputFields' => [
			'zohoContact' => [
				'type' => 'zohoContact',
			],
			'status' => [
				'type' =>'Integer'
			] ,
			'error' => [
				'type' => 'String'
			],
			'message' => [
				'type' => 'String'
			]
		],
		'mutateAndGetPayload' => function( $input ) {
			$user_id=$input['user_id'];
			unset($input['user_id']);
			$zoho_response = CallAPIs('POST', "https://desk.zoho.com/api/v1/contacts", $input);
			$contact_id=$zoho_response['body']->id;

			$input['user_id']=$user_id;
			$input['contactId']=$contact_id;
			insertZohoContacts($input);
		
			if($zoho_response['status']==200)
			{
							
				return [
					'error' => null,
					'message'=> 'Success',
					'zohoContact' =>$zoho_response['body'],
					'status' => $zoho_response['status'],
				];
			}
			else{
				return [
					'error' => $zoho_response['error'],
					'message'=> $zoho_response['message'],
					'zohoContact' =>null,
					'status' => $zoho_response['status']
				];
			}
		}
	]);
	register_graphql_mutation( 'ZohoUpdateContact', [
		'inputFields' => [
			'user_id' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'firstName' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'lastName' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'email' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'type' => [
				'type' => 'String',
			  ],
			  'facebook' => [
				'type' => 'String',
			  ],
			  'twitter' => [
				'type' => 'String',
			  ],
			  'secondaryEmail' => [
				'type' => 'String',
			  ],
			  'phone' => [
				'type' => 'String',
			  ],
			  'mobile' => [
				'type' => 'String',
			  ],
			  'city' => [
				'type' => 'String',
			  ],
			  'country' => [
				'type' => 'String',
			  ],
			  'state' => [
				'type' => 'String',
			  ],
			  'street' => [
				'type' => 'String',
			  ],
			  'zip' => [
				'type' => 'String',
			  ],
			  'description' => [
				'type' => 'String',
			  ],
			  'title' => [
				'type' => 'String',
			  ],
			  'photoURL' => [
				'type' => 'String',
			  ],
			  'webUrl' => [
				'type' => 'String',
			  ],
			  'zip' => [
				'type' => 'String',
			  ],
			  'isDeleted' => [
				'type' => 'boolean',
			  ],
			  'isTrashed' => [
				'type' => 'boolean',
			  ],
			  'isSpam' => [
				'type' => 'boolean',
			  ],
			  'createdTime' => [
				'type' => 'String',
			  ],
			  'modifiedTime' => [
				'type' => 'String',
			  ],
			  'accountId' => [
				'type' => 'String',
			  ],
			  'ownerId' => [
				'type' => 'String',
			  ],
		],
		'outputFields' => [
			'zohoContact' => [
				'type' => 'zohoContact',
			],
			'status' => [
				'type' =>'Integer'
			] ,
			'error' => [
				'type' => 'String'
			],
			'message' => [
				'type' => 'String'
			],
			'clientId'=>
			[
				'type'=>'String'
			]
		],
		'mutateAndGetPayload' => function( $input ) {
			global $wpdb;
			$user_id = base64_decode($input['user_id']); // the result is not a stringified number, neither printable
			$string=str_replace('user:', '',$user_id  );
			$user_id= json_decode($string);
			$input['user_id']=$user_id;
			unset($input['user_id']);	
			$result = $wpdb->get_results('SELECT contactId FROM wp_zoho_contacts WHERE user_id='.$user_id.'');
			$contact_id=$result[0]->contactId;
			$zoho_response = CallAPIs('PATCH', "https://desk.zoho.com/api/v1/contacts/".$contact_id, $input);
			$input['user_id']=$user_id;
			$input['contactId']=$contact_id;

			if($zoho_response['status']==200)
			{	
				$wpdb->update('wp_zoho_contacts', $input, array( 'user_id' => $user_id ));
				return [
					'error' => null,
					'message'=> 'Success',
					'zohoContact' =>$zoho_response['body'],
					'status' => $zoho_response['status'],
				];
			}
			else{
				return [
					'error' => $zoho_response['error'],
					'message'=> $zoho_response['message'],
					'zohoContact' =>null,
					'status' => $zoho_response['status']
				];
			}
		}
	]);
	//done don't touch please
	register_graphql_mutation( 'ZohoCreateticket', [
		'inputFields' => [
			'subject' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'description' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'priority' => [
				'type' => [ 'non_null' => 'String' ],
			], 
			'user_id' => [
				'type' => ['non_null' => 'String']
			],
			'departmentId' => [
				'type'=> ['non_null' => 'String']
			],
			'company_id'=>[
				'type' => ['non_null' => 'Integer']
			],
			'classification'=>[
			    'type'=> ['non_null' => 'String']
			],
			'watcher'=>[
				'type'=>'String'
			],
			'customer_url'=>[
				'type'=>'String'
			],
			'steps_to_reproduce'=>[
				'type'=>'String'
			],
			'business_impact'=>[
				'type'=>'String'
			],
			'problematic_plugin'=>[
				'type'=>'String'
			],
			'business_use_case'=>[
				'type'=>'String'
			],
			'inspiration'=>[
				'type'=>'String'
			],
			'post_media_to'=>[
				'type'=>'String'
			],
			'post_description'=>[
				'type'=>'String'
			],'uploads' => [
                'type' =>'String'
            ],
			
		],
		'outputFields' => [
			'zohoTicket' => [
				'type' => 'zohoTicket',
			],
			'status' => [
				'type' =>'Integer'
			] ,
			'error' => [
				'type' => 'String'
			],
			'message' => [
				'type' => 'String'
			],
			'contactId' =>[
				'type'=>'String'
			],
		],
		'mutateAndGetPayload' => function( $input ) {
			global $wpdb;
			$user_id = base64_decode($input['user_id']); // the result is not a stringified number, neither printable
			$string=str_replace('user:', '',$user_id  );
			$user_id= json_decode($string);
			$input['user_id']=$user_id;
            $watcher=getWatcherID($input['watcher']);
			$result = $wpdb->get_results('SELECT contactId FROM wp_zoho_contacts WHERE user_id='.$user_id.'');
			$input['contactId']=$result[0]->contactId;
			$stepsToReproduce=$input['steps_to_reproduce'] ? '<br><b>Step to Reproduce: </b>'.$input['steps_to_reproduce'] : '';
			$businessImpact=$input['business_impact'] ? '<br><b>Business Impact: </b>'.$input['business_impact'] : '';
			$businessUseCase=$input['business_use_case'] ? '<br><b>Business Use Case: </b>'.$input['business_use_case'] : '';
			$inspiration=$input['inspiration'] ? '<br><b>Inspiration: </b>'.$input['inspiration'] : '';
			$post_media_to=$input['post_media_to'] ? '<br><b>Post media to: </b>'.$input['post_media_to'] : '';
			$post_description=$input['post_description'] ? '<br><b>Post Description: </b>'.$input['post_description'] : '';
			$newDes=$input['description'].$stepsToReproduce.$businessImpact.$businessUseCase.$inspiration.$post_media_to.$post_description;
			$pp=$input['problematic_plugin'] ?  $input['problematic_plugin'] : 'null';
			$curl=$input['customer_url'] ? $input['customer_url'] : 'null';
			$custom_fields='{
			   "cf_plugin":'.$pp.' ,
                "cf_time_spent": null,
                "cf_customer_url": '.$curl.',
                "cf_kate_time_spent_in_mins": null
			}';
			
			$inputZoho=[
				'subject'=>$input['subject'],
				'description'=>$newDes,
				'priority'=>$input['priority'],
				'departmentId'=>$input['departmentId'],
				'classification'=>$input['classification'],
				'contactId'=>$result[0]->contactId,
				'secondaryContacts'=>'["'.$watcher.'"]',
				'cf'=>$custom_fields
			];
			$zoho_response = CallAPIs('POST', "https://desk.zoho.com/api/v1/tickets", $inputZoho);
		
			if($zoho_response['status']==200)
			{
			    $wpdb->update('wp_zoho_attachments',['ticket_id'=>$zoho_response['body']->id], array( 'id' =>substr($input['uploads'], 1, -1)));  
				$ticket_id=$zoho_response['body']->id;
				$input['ticketNumber']=$zoho_response['body']->ticketNumber;
				$status=$zoho_response['body']->status;
				insertZohoTickets($input,$ticket_id,$status);
				return [
					'error' => null,
					'message'=> 'Success',
					'zohoTicket' =>$zoho_response['body'],
					'status' => $zoho_response['status'],
					'contactId' => $result[0]->contactId
				];
			}
			else{
				return [
					'error' => $zoho_response['error'],
					'message'=> $zoho_response['message'],
					'zohoTicket' =>null,
					'status' => $zoho_response['status'],
					// 'contactId' => 	$client_id
				];
			}
		}
	]);
	
	
	
	
	//zoho update ticket
	register_graphql_mutation( 'ZohoUpdateticket', [
		'inputFields' => [
			'id' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'subject' => [
				'type' => 'String' 
			],
			'description' => [
				'type' =>  'String' 
			],
			'priority' => [
				'type' => 'String' 
			],
			'status'=>[
			    'type'=>'String'
			 ],
			'departmentId' => [
				'type'=> ['non_null' => 'String']
			],
		],
		'outputFields' => [
			'zohoTicket' => [
				'type' => 'zohoTicket',
			],
			'status' => [
				'type' =>'Integer'
			] ,
			'error' => [
				'type' => 'String'
			],
			'message' => [
				'type' => 'String'
			],
			'contactId' =>[
				'type'=>'String'
			]
		],
		'mutateAndGetPayload' => function( $input ) {

			// return ['zohoTicket' =>$input];
			$zoho_response = CallAPIs('PATCH', "https://desk.zoho.com/api/v1/tickets/".$input['id'], $input );
			$ticket_id=$input['id'];
			if($zoho_response['status']==200)
			{
				updateZohoTickets($input,$ticket_id);
				return [
					'error' => null,
					'message'=> 'Success',
					'zohoTicket' =>$zoho_response['body'],
					'status' => $zoho_response['status'],
					// 'contactId' => $result[0]->contactId
				];
			}
			else{
				return [
					'error' => $zoho_response['error'],
					'message'=> $zoho_response['message'],
					'zohoTicket' =>null,
					'status' => $zoho_response['status'],
					// 'contactId' => 	$client_id
				];
			}
		}
	]);

	// zoho delete Company (ticket/timeEntry/attachment/comments)
	register_graphql_mutation( 'ZohoDeleteCompany', [
		'inputFields' => [
			'company_Id' => [
				'type' => [ 'non_null' => 'String' ],
			]
		],
		'outputFields' => [
		    'status' => [
				'type' =>'String'
			] ,
			'error' => [
				'type' => 'String'
			]
		],
		'mutateAndGetPayload' => function( $input ) {
			global $wpdb;
			$result = $wpdb->get_results('SELECT id FROM wp_zoho_companies WHERE id='.$input['company_Id']);
			$comments_ids = [];
			if($result[0]->id && $result[0]->id != null){
				// Delete company
				$deleteCompany = $wpdb->get_results('DELETE FROM wp_zoho_companies WHERE id='.$result[0]->id);
				$ticket_ids = $wpdb->get_results('SELECT id FROM wp_zoho_tickets WHERE company_id='.$result[0]->id);
				foreach($ticket_ids as $single_ticket_id){  /////// Delete time entries and attachments related to that ticket id
					$delete_time_entry = $wpdb->get_results('DELETE FROM wp_zoho_tickets_time_entry WHERE ticketId='.$single_ticket_id->id);
					$delete_attachments = $wpdb->get_results('DELETE FROM wp_zoho_attachments WHERE ticket_id='.$single_ticket_id->id);
					$ticket_comments = $wpdb->get_results('SELECT id FROM wp_zoho_tickets_comments WHERE ticketId='.$single_ticket_id->id);
					$comments_ids = array_merge($comments_ids,$ticket_comments);
					$delete_tickets = $wpdb->get_results('DELETE FROM wp_zoho_tickets WHERE id='.$single_ticket_id->id);
				}
				foreach($comments_ids as $single_comment_id){ 
					// Delete remaining attachments
					$delete_attachments = $wpdb->get_results('DELETE FROM wp_zoho_attachments WHERE comment_id='.$single_comment_id->id);
					// Delete Comments
					$delete_comments = $wpdb->get_results('DELETE FROM wp_zoho_tickets_comments WHERE id='.$single_comment_id->id);
				}

				return [
					'status' => 'Company ID ' . $input['company_Id'] . ' and related data successfuly deleted',
					'error' => null
				];				
			}else{
				return [
					'status' => "Company not exist",
					'error' => null
				];
			}
		}
	]);

//zoho delete ticket
	register_graphql_mutation( 'ZohoDeleteticket', [
		'inputFields' => [
			'ticketIds' => [
				'type' => [ 'non_null' => 'String' ],
			]
		],
		'outputFields' => [
		    'status' => [
				'type' =>'Integer'
			] ,
			'error' => [
				'type' => 'String'
			]
		],
		'mutateAndGetPayload' => function( $input ) {

			$zoho_response = CallAPIs('POST', "https://desk.zoho.com/api/v1/tickets/moveToTrash",$input);
			$ticket_id=$input['id'];
			if($zoho_response['status']==204)
			{
				updateZohoTickets($input,$ticket_id);
				return [
					'error' => null,
					'message'=> 'Ticket Deleted Successfully',
					'status' => $zoho_response['status'],
					// 'contactId' => $result[0]->contactId
				];
			}
			else{
				return [
					'error' => $zoho_response['error'],
					'message'=> $zoho_response['message'],
					'zohoTicket' =>null,
					'status' => $zoho_response['status'],
					// 'contactId' => 	$client_id
				];
			}
		}
	]);


	register_graphql_mutation( 'ZohoCreateUser', [
		'inputFields' => [
			'name' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'password' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'email' => [
				'type' => [ 'non_null' => 'String' ],
			], 
			'created_by'=>[
				'type'=> ['non_null' => 'String'],
			],
			'user_role'=>[
				'type'=> ['non_null' => 'String']
			],
		],
		'outputFields' => [
			'zohoUser' => [
				'type' =>'zohoUser'
			],
			'status'=>[
				'type'=>'String'
			]
		],
		'mutateAndGetPayload' => function( $input ) {
// 			$created_by = base64_decode($input['created_by']); // the result is not a stringified number, neither printable
// 			$string=str_replace('user:', '',$created_by  );
// 			$created_by= json_decode($string);
// 			$input['created_by']=$created_by;

			global $wpdb;
			if(email_exists( $input['email'] ))
			{
				return ['status' => 'Email already Exists'];
			}else {
				$registered=wp_create_user($input['name'],$input['password'],$input['email']);
				if($registered){
					$wpdb->update('wp_users', array('created_by'=>$input['created_by'], 'role'=>$input['user_role']), array( 'id' => $registered ));
					return ['zohoUser' => $input];
				}else {
					return ['status' => 'Something is Wrong'];
				}
			}	
		}
	]);
		register_graphql_mutation( 'ZohoInviteUserByCompany', [
		'inputFields' => [
			'name' => [
				'type' => [ 'non_null' => 'String' ],
			],
			'role' => [
				'type' => [ 'non_null' => 'String' ],
			], 
			'email' => [
				'type' => [ 'non_null' => 'String' ],
			], 
			'comapny_id'=>[
				'type'=> ['non_null' => 'Integer']
			],
		],
		'outputFields' => [
			'status'=>[
				'type'=>'String'
			],
		],
		'mutateAndGetPayload' => function( $input ) {
// 			$created_by = base64_decode($input['created_by']); // the result is not a stringified number, neither printable
// 			$string=str_replace('user:', '',$created_by  );
// 			$created_by= json_decode($string);
// 			$input['created_by']=$created_by;

			global $wpdb;
			$company = $wpdb->get_results('SELECT * FROM wp_zoho_companies WHERE id='.$input['comapny_id'].'');
			    
			if(email_exists( $input['email'] ))
			{
			    $toEmail =$input['email'];
				$subject = 'Invitation by '.$company[0]->name;
	            $body = 'Dear ' .$input['name']. ' You are invited by '.$company[0]->id .' '. $company[0]->name .' Please Folow the '.$company[0]->name.' by the given link' ;
				$headers = array('Content-Type: text/html; charset=UTF-8');
				wp_mail( $toEmail, $subject, $body, $headers );
				return ['status' => 'Email Sent to existing user'];
			}else {
			   // $code = mt_rand(111111,999999);
			    $toEmail =$input['email'];
				$subject = 'Invitation by '.$company[0]->name;
	            $body = 'Dear ' .$input['name']. ' You are invited by '.$company[0]->id .' '. $company[0]->name .' Please singup with the given link and follow '. $company[0]->name ;
				$headers = array('Content-Type: text/html; charset=UTF-8');
				wp_mail( $toEmail, $subject, $body, $headers );
				return ['status' => 'Email Sent to New user'];
			}	
		}
	]);

	register_graphql_mutation( 'ZohoCreateUserByInviteAccept', [
		'inputFields' => [
			'name' => [
				'type' => ['non_null' => 'String'],
			],
			'password' => [
				'type' =>['non_null' => 'String'],
			],
			'email' => [
				'type' => [ 'non_null' => 'String' ],
			], 
			'createdBy' => [
				'type' => ['non_null' => 'Integer'],
			],
			'userRole' => [
				'type' =>['non_null' => 'String']
			]
		],
		'outputFields' => [
			'userId'=>[
				'type'=>'String'
			],
			'status'=>[
				'type'=>'String'
			],
		],
		'mutateAndGetPayload' => function( $input ) {
			global $wpdb;
			$registered=wp_create_user($input['name'],$input['password'],$input['email']);
			if($registered)
			{
				$updateUser = $wpdb->update('wp_users', array('created_by'=>$input['createdBy'], 'role'=>$input['userRole']), array( 'id' => $registered ));
				if ($updateUser) 
				{
					$wpdb->insert('wp_zoho_users_company', array('user_id' => $registered, 'company_id' => $input['createdBy'], 'Status' => 1));
					return ['userId' => $registered, 'status' => 'User Registered'];
				}
				else 
				{
					return ['userId' => null, 'status' => 'Something is Wrong.!'];
				}
			}
		}
	]);

	register_graphql_mutation( 'ZohoUserAcceptInvite', [
		'inputFields' => [
			'userId' => [
				'type' => ['non_null' => 'String'],
			],
			'companyId' => [
				'type' =>['non_null' => 'Integer'],
			]
		],
		'outputFields' => [
			'status'=>[
				'type'=>'String'
			],
		],
		'mutateAndGetPayload' => function( $input ) {
			global $wpdb;
			$user_id = base64_decode($input['userId']); // the result is not a stringified number, neither printable
			$inviteAccepted = $wpdb->update('wp_zoho_users_company', array('Status' => 1), array('user_id' => $user_id  ,'company_id' => $input['companyId']));
			if ($inviteAccepted) 
			{
				return ['status' => 'true'];
			}
			else 
			{
				return ['status' => 'false'];
			}
		}
	]);

    register_graphql_mutation( 'ZohoUpdateUser', [
		'inputFields' => [
			'id'=>[
				'type'=>['non_null'=> 'String']
			],
			'user_role'=>[
				'type'=> 'String'
			],
			'phoneNumber'=>[
				'type'=> 'String'
			],
			'first_name'=>[
				'type'=> 'String'
			],
			'last_name'=>[
				'type'=> 'String'
			],
			'profile'=>[
				'type'=> 'String'
			],
		],
		'outputFields' => [
			'userId' => [
				'type' =>'String'
			],
			'phoneNumber' => [
				'type' =>'String'
			],
			'first_name'=>[
				'type'=> 'String'
			],
			'last_name'=>[
				'type'=> 'String'
			],
			'profile'=>[
				'type'=> 'String'
			],
            'status' => [
				'type' =>'String'
			] 
		],
		'mutateAndGetPayload' => function( $input ) {
			$id = base64_decode($input['id']); // the result is not a stringified number, neither printable
			$string=str_replace('user:', '',$id  );
			$id= json_decode($string);
			$input['id']=base64_encode($id);
			
			$makeforserialize = array( "full" => $input['profile'] );
			$profile = serialize($makeforserialize);
         
			global $wpdb;
			$updated = false;
			if(isset($input['user_role']) && $input['user_role']){
				$updated=$wpdb->update('wp_users', array('role'=>$input['user_role']), array( 'id' => $id ));
			}
			
			if(isset($input['phoneNumber']) && $input['phoneNumber']){
				update_user_meta( $id, 'phone', $input['phoneNumber']);
				$updated = true;
			}
			if(isset($input['first_name']) && $input['first_name']){
				update_user_meta( $id, 'first_name', $input['first_name']);
				$updated = true;
			}
			if(isset($input['last_name']) && $input['last_name']){
				update_user_meta( $id, 'last_name', $input['last_name']);
				$updated = true;
			}
			if(isset($profile) && $profile){
				update_user_meta( $id, 'wp_user_avatars', $makeforserialize);
				$updated = true;
			}

			if($updated){
				return [
                	'userId' =>$input['id'],
					'phoneNumber'=>$input['phoneNumber'],
					'first_name'=>$input['first_name'],
					'last_name'=>$input['last_name'],
					'profile'=>$input['profile'],
                	'status'=>'User Updated'
                ];
			}else {
				return [
                	'userId' => 'User Not Found',
                	'status'=>'Not Updated'
                ];
			}					
		}
	]);

	//ok

	register_graphql_mutation( 'ZohoCreateTicketComment', [
		'inputFields' => [
			'id'=>[
				'type'=>['non_null'=> "String"]
			],
			'content'=>[
				'type'=> ['non_null' => 'String']
			],
			'isPublic'=>[
				'type'=> ['non_null' => 'Boolean']
			],
			'attachmentIds'=>[
				'type'=>  'String'
			],
			'contentType'=>[
				'type'=> 'String'
			],
			'companyId'=>[
				'type'=> ['non_null' => 'String']
			],
		],
		'outputFields' => [
			'status' => [
				'type' =>'Integer'
			] ,
			'error' => [
				'type' => 'String'
			],
			'message' => [
				'type' => 'String'
			],
			'getZohoTicketComment'=>['type'=>'zohoTicketComment'],
			'inserted' =>['type' => 'Boolean'],
			'size' =>['type' => 'string']
		],
		'mutateAndGetPayload' => function( $input ) {
		$companyId=$input['companyId'];
     	unset($input['companyId']);
     	
            $zoho_response = CallAPIs('POST', "https://desk.zoho.com/api/v1/tickets/".$input['id']."/comments", $input);
		
			if($zoho_response['status']==200)
			{
				$data=array();
				$data['id']=$zoho_response['body']->id;
				$data['ticketId']=$input['id'];
				$data['isPublic']=$zoho_response['body']->isPublic;
				$data['commentedTime']=$zoho_response['body']->commentedTime;
				$data['contentType']=$zoho_response['body']->contentType;
				$data['content']=$zoho_response['body']->content;
				$data['commenterId']=$zoho_response['body']->commenterId;
				$data['commentorName']=$zoho_response['body']->commenter->name;
				$data['photoURL']=$zoho_response['body']->commenter->name;
				$data['roleName']=$zoho_response['body']->commenter->roleName;
				$data['type']=$zoho_response['body']->commenter->type;
				$data['email']=$zoho_response['body']->commenter->email;
				$data['companyId']=$companyId;
				global $wpdb;
				$inserted=$wpdb->insert('wp_zoho_tickets_comments', $data);
				$wpdb->update('wp_zoho_attachments',['comment_id'=>$zoho_response['body']->id], array( 'id' =>substr($input['attachmentIds'], 1, -1)));   
				
				return [
					'error' => null,
					'message'=> 'Success',
					'status' => $zoho_response['status'],
					'getZohoTicketComment'=>$zoho_response['body'],
					'inserted' => $inserted
				];
			}
			else{
				return [
					'error' => $zoho_response['error'],
					'message'=> $zoho_response['message'],
					'status' => $zoho_response['status'],
					'ZohoTicketCommet' => Null,
				];
			}		
		}
	]);
    register_graphql_mutation( 'ZohoCreatePlan', [
		'inputFields' => [
			'planeId'=>['type'=>['non_null'=> 'String']],
			'name'=>['type'=> 'String'],
			'description'=>['type'=> 'String'],
			'statement_descriptor'=>['type'=> 'String'],
			'reporting'=>['type'=> 'String'],
			'visitors_info'=>['type'=> 'String'],
			'disk_space_info'=>['type'=> 'String'],
			'security_info'=>['type'=> 'String'],
			'updates_info'=>['type'=> 'String'],
			'companyId'=>['type'=> ['non_null'=> 'String']],
			'unit_amount'=>['type'=> 'String'],
			'currency'=>['type'=> ['non_null'=> 'String']],
			'productId'=>['type'=> ['non_null'=> 'String']],
			'PriceId'=>['type'=> ['non_null'=> 'String']],
		],
		'outputFields' => [
		   	'planeId'=>['type'=>'String'],
		   	'name'=>['type'=> 'String'],
			'description'=>['type'=> 'String'],
			'statement_descriptor'=>['type'=> 'String'],
			'reporting'=>['type'=> 'String'],
			'visitors_info'=>['type'=> 'String'],
			'disk_space_info'=>['type'=> 'String'],
			'security_info'=>['type'=> 'String'],
			'updates_info'=>['type'=> 'String'],
			'companyId'=>['type'=>  'String'],
			'unit_amount'=>['type'=> 'String'],
			'currency'=>['type'=>  'String'],
			'productId'=>['type'=>  'String'],
			'PriceId'=>['type'=> 'String'],
		],
		'mutateAndGetPayload' => function( $input ) {
	        global $wpdb;
            $company_id = $input['companyId'];
            $wpdb->get_results("UPDATE wp_zoho_plans SET is_active = 0 WHERE companyId = '".$company_id."' AND is_active = 1");
	    	$wpdb->insert('wp_zoho_plans', $input);
            return $input;
		}
	]);

	register_graphql_mutation( 'StripeAddSubscription', [
		'inputFields' => [
			'token'=>['type'=>['non_null'=> 'String']],
			'price_ID'=>['type'=>['non_null'=> 'String']],
			'company_id'=>['type'=>['non_null'=> 'String']],
			'hours'=>['type'=>['non_null'=> 'String']],
			'planeId'=>['type'=>['non_null'=> 'String']],
			'name'=>['type'=> 'String'],
			'description'=>['type'=> 'String'],
			'statement_descriptor'=>['type'=> 'String'],
			'reporting'=>['type'=> 'String'],
			'visitors_info'=>['type'=> 'String'],
			'disk_space_info'=>['type'=> 'String'],
			'security_info'=>['type'=> 'String'],
			'updates_info'=>['type'=> 'String'],
			'unit_amount'=>['type'=> 'String'],
			'currency'=>['type'=> ['non_null'=> 'String']],
			'productId'=>['type'=> ['non_null'=> 'String']],
		],
		'outputFields' => [
		   	'response'=>['type'=>'String']
		],
		'mutateAndGetPayload' => function( $input ) {
	        global $wpdb;
			$customer_id = $wpdb->get_results('SELECT stripe_customer_id FROM wp_zoho_companies WHERE id="'.$input['company_id'].'"');

			if($customer_id && $customer_id !== NULL ){
				// Attach card with customer
				$post_data = "source=".$input['token'];
				$headers = [
					'Content-Type: application/x-www-form-urlencoded'
				];
				$URL = 'https://api.stripe.com/v1/customers/'.$customer_id[0]->stripe_customer_id.'/sources';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $URL);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY.": "); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$server_output = curl_exec($ch);
				curl_close ($ch);
				$response = json_decode($server_output);
				$card_id = $response->id;

				// Subscription and charge amount here 
				
				$sub_URL = 'https://api.stripe.com/v1/subscriptions';
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $sub_URL);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, 'customer='. $customer_id[0]->stripe_customer_id.'&items[0][price]='.$input['price_ID']);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY.": "); 
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$sub_server_output = curl_exec($ch);
				curl_close ($ch);
				$sub_response = json_decode($sub_server_output);
				$subsription_plan = [];
				$subsription_plan['subscription_id'] = $sub_response->id;
				$subsription_plan['stripe_customer_id'] = $sub_response->customer;
				$subsription_plan['card_id'] = $card_id;
				$subsription_plan['status'] = "active";
				$subsription_plan['hours'] = (int)$input['hours'];
				$subsription_plan['start_date'] = date("Y-m-d H:i:s", $sub_response->current_period_start);
				$subsription_plan['end_date'] = date("Y-m-d H:i:s", $sub_response->current_period_end);

				$wpdb->insert('zoho_subscription', $subsription_plan);
				$db_plans = [];
				$db_plans['PriceId'] = $input['price_ID'];
				$db_plans['companyId'] = $input['company_id'];
				$db_plans['planeId'] = $input['planeId'];
				$db_plans['name'] = $input['name'];
				$db_plans['description'] = $input['description'];
				$db_plans['statement_descriptor'] = $input['statement_descriptor'];
				$db_plans['reporting'] = $input['reporting'];
				$db_plans['visitors_info'] = $input['visitors_info'];
				$db_plans['disk_space_info'] = $input['disk_space_info'];
				$db_plans['security_info'] = $input['security_info'];
				$db_plans['updates_info'] = $input['updates_info'];
				$db_plans['unit_amount'] = $input['unit_amount'];
				$db_plans['currency'] = $input['currency'];
				$db_plans['productId'] = $input['productId'];
				$plan = $wpdb->insert('wp_zoho_plans', $db_plans);

				return [
					'response' => 'Suucessfully charged and plan added',
				];

			}else{
				return [
					'response' => 'Invalid email User not exist OR Stripe customer ID not exist'
				];
			}
            // return $input;
		}
	]);

	register_graphql_mutation( 'ExtraPurchases', [
		'inputFields' => [
			'token'=>['type'=>['non_null'=> 'String']],
			'hours'=>['type'=>['non_null'=> 'Number']],
			'amount'=>['type'=>['non_null'=> 'Number']],
			'company_id'=>['type'=>['non_null'=> 'String']],
		],
		'outputFields' => [
		   	'response'=>['type'=>'String']
		],
		'mutateAndGetPayload' => function( $input ) {
			global $wpdb;
			$amount = $input['amount'] * 100;
			$post_data = "amount=".$amount."&currency=usd&source=".$input['token'];
			$headers = [
				'Content-Type: application/x-www-form-urlencoded'
			];
			$URL = 'https://api.stripe.com/v1/charges';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $URL);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY.": "); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$server_output = curl_exec($ch);
			curl_close ($ch);
			$response = json_decode($server_output);
			
			$extra_purchases = [];
			$extra_purchases['company_id'] = $input['company_id'];
			$extra_purchases['amount'] = $input['amount'];
			$extra_purchases['hours'] = $input['hours'];
			$extra_purchases['status'] = "active";
			$wpdb->insert('extra_purchases', $extra_purchases);

			return [
				'response' => 'Sucessfully charged Extra purchases',
			];
		}
	]);

	register_graphql_mutation( 'UpdateSubscription', [
		'inputFields' => [
// 			'token'=>['type'=>['non_null'=> 'String']],
			'price_ID'=>['type'=>['non_null'=> 'String']],
			'company_id'=>['type'=>['non_null'=> 'String']],
			'hours'=>['type'=>['non_null'=> 'String']],
		],
		'outputFields' => [
		   	'response'=>['type'=>'String']
		],
		'mutateAndGetPayload' => function( $input ) {
			global $wpdb;
			$customer_id = $wpdb->get_results('SELECT stripe_customer_id,id FROM wp_zoho_companies WHERE id="'.$input['company_id'].'"');
			
			if($customer_id && $customer_id !== NULL ){
    			$subscription_id = $wpdb->get_results('SELECT * FROM zoho_subscription WHERE stripe_customer_id="'.$customer_id[0]->stripe_customer_id.'"'.' AND status="active"');
    			
    			$start_date = date('Y-m-d H:i:s', strtotime($subscription_id[0]->start_date));
    			$end_date = date('Y-m-d H:i:s', strtotime($subscription_id[0]->end_date));
    			$time_used_in_hours = 0.0;
    			$time_abc='';
    			$tickets=$wpdb->get_results('SELECT * FROM wp_zoho_tickets WHERE company_id='.$customer_id[0]->id);
    			foreach($tickets as $key => $item){ 
    				$time=$wpdb->get_results("SELECT SUM(hours)*3600 + SUM(minutes)*60+SUM(seconds) as time FROM wp_zoho_tickets_time_entry WHERE (created_at BETWEEN '".$start_date."'"." AND '".$end_date."'".") AND ticketId=".$item->id);
    				$time_used_in_hours += ($time[0]->time)/3600;
    			}
    			$remaining_time = (double)$subscription_id[0]->hours - (double)$time_used_in_hours;
    			
    			// First cancel the old subscription 
    
    			$headers = [
    				'Content-Type: application/x-www-form-urlencoded'
    			];
    			$url = 'https://api.stripe.com/v1/subscriptions/'.$subscription_id[0]->subscription_id;
    			$ch = curl_init();
    			curl_setopt($ch, CURLOPT_URL, $url);
    			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    			curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY.": "); 
    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    			$result = curl_exec($ch);
    			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    			curl_close($ch);
    			$update_subscription = [];
    			$update_subscription['status'] = 'canceled';
    			$wpdb->update( 'zoho_subscription',$update_subscription, array( 'id' => $subscription_id[0]->id ) );
    
    			// Now Add new subscription and add remaining hours (if any)
    			
    			// Attach card to specific subscription
    			
    // 			$post_data = "source=".$input['token'];
    // 			$URL = 'https://api.stripe.com/v1/customers/'.$customer_id[0]->stripe_customer_id.'/sources';
    // 			$ch = curl_init();
    // 			curl_setopt($ch, CURLOPT_URL, $URL);
    // 			curl_setopt($ch, CURLOPT_POST, 1);
    // 			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    // 			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    // 			curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY.": "); 
    // 			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 			$server_output = curl_exec($ch);
    // 			curl_close ($ch);
    // 			$response = json_decode($server_output);
    // 			$card_id = $response->id;
    
    
    			$sub_URL = 'https://api.stripe.com/v1/subscriptions';
    			$ch = curl_init();
    			curl_setopt($ch, CURLOPT_URL, $sub_URL);
    			curl_setopt($ch, CURLOPT_POST, 1);
    			curl_setopt($ch, CURLOPT_POSTFIELDS, 'customer='. $customer_id[0]->stripe_customer_id.'&items[0][price]='.$input['price_ID']);
    			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    			curl_setopt($ch, CURLOPT_USERPWD, STRIPE_SECRET_KEY.": "); 
    			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    			$sub_server_output = curl_exec($ch);
    			curl_close ($ch);
    			$sub_response = json_decode($sub_server_output);
    			$subsription_plan = [];
    			$subsription_plan['subscription_id'] = $sub_response->id;
    			$subsription_plan['stripe_customer_id'] = $sub_response->customer;
    			$subsription_plan['card_id'] = $subscription_id[0]->card_id;
    			$subsription_plan['status'] = "active";
    			$subsription_plan['hours'] = (double)$input['hours'] + (double)$remaining_time;
    			$subsription_plan['start_date'] = date("Y-m-d H:i:s", $sub_response->current_period_start);
    			$subsription_plan['end_date'] = date("Y-m-d H:i:s", $sub_response->current_period_end);
    			$wpdb->insert('zoho_subscription', $subsription_plan);
    
    
    			return [
    				'response' => 'Subscription sucessfully Updated',
    			];
			}else{
			    return [
					'response' => 'Invalid Company ID OR Stripe customer ID not exist'
				];
			}
		}
	]);



} );


function getWatcherID($email)
{
	
  	global $wpdb;
  	$watcherID=$wpdb->get_results( 'SELECT contactId FROM wp_zoho_contacts WHERE email="'.$email.'"');
	return $watcherID[0]->contactId;
}



function insertCompany($input)
{
	
  	global $wpdb;
	$user_id = base64_decode($input['user_id']); // the result is not a stringified number, neither printable
	$string=str_replace('user:', '',$user_id  );
	$user_id= json_decode($string);
	$input['user_id']=$user_id;
	$wpdb->insert('wp_zoho_companies', $input);
	return $wpdb->insert_id;
// 	return $input;
}

function insertZohoCredentialDetails($input)
{	
  	global $wpdb;
  	$zoho=array();
	$zoho['grant_token']= $input['grant_token'];
	$zoho['refresh_token'] =$input['refresh_token'];
	$zoho['access_token'] =$input['access_token'];
    $zoho['client_secret'] =$input['client_secret'] ;
	$zoho['client_id'] =$input['client_id'] ;
    $zoho['scopes'] =$input['scopes'] ;

	$results = $wpdb->get_results( 'SELECT * FROM wp_zoho_credentials');
	if ($results)
	{
		foreach($results as $result)
		{
			$wpdb->update( 'wp_zoho_credentials',$zoho, array( 'id' => $result->id ) );
			break;
		}
	}else{
		$wpdb->insert('wp_zoho_credentials', $zoho);
	}
	return $zoho;
}
// function updateZohoContacts($input)
// {
//   	global $wpdb;
// 	$wpdb->update('wp_zoho_contacts', $input, array( 'user_id' => $user_id ));
// 	return $input;
// }


function insertZohoContacts($input)
{
  	global $wpdb;
	$user_id = base64_decode($input['user_id']); // the result is not a stringified number, neither printable
	$string=str_replace('user:', '',$user_id  );
	$user_id= json_decode($string);
	$input['user_id']=$user_id;
	$results=$wpdb->get_results('SELECT user_id FROM wp_zoho_contacts');

	if($results)
	{
		$update=false;
		foreach ($results as $result)
		{
			if($result->user_id==$user_id)
			{
				$update=true;
				break;
			}
		}
		if($update)
		{
			$wpdb->update('wp_zoho_contacts', $input, array( 'user_id' => $user_id ));
			return $input;
		}else{
			$wpdb->insert('wp_zoho_contacts', $input);
			return $input;
		}
	}

	$wpdb->insert('wp_zoho_contacts', $input);
	return $input;


  	$zoho=array();
    $zoho['user_id']= $user_id;
	$zoho['firstName']= $input->firstName;
	$zoho['lastName'] =$input->lastName;
	$zoho['email'] =$input->email;
    $zoho['contactId'] =$input->id;
	$zoho['zip'] =$input->zip;
	$zoho['country'] =$input->country;
	$zoho['secondaryEmail'] =$input->secondaryEmail;
	$zoho['city'] =$input->city;
	$zoho['facebook'] =$input->facebook;
	$zoho['mobile'] =$input->mobile;
	$zoho['ownerId'] =$input->ownerId;
	$zoho['type'] =$input->type;
	$zoho['title'] =$input->title;
	$zoho['accountId'] =$input->accountId;
	$zoho['twitter'] =$input->twitter;
	$zoho['phone'] =$input->phone;
	$zoho['street'] =$input->street;
	$zoho['state'] =$input->state;
	$results=$wpdb->get_results('SELECT user_id FROM wp_zoho_contacts');
	$update=false;
	if($results)
	{
		foreach ($results as $result)
		{
			if($result->user_id==$user_id)
			{
				$update=true;
				break;
			}
		}
		if($update)
		{
			$wpdb->update('wp_zoho_contacts', $zoho, array( 'user_id' => $user_id ));
		}else{
			$wpdb->insert('wp_zoho_contacts', $zoho);
		}
	}else{
		$wpdb->insert('wp_zoho_contacts', $zoho);
	}

	return $zoho;
}

function insertZohoTickets($input,$ticket_id,$status)
{
  	global $wpdb;
	$input['id']=$ticket_id;
	$input['status']=$status;
	$wpdb->insert('wp_zoho_tickets', $input);
	return $input;
}
function updateZohoTickets($input, $ticketId)
{
  	global $wpdb;
	$wpdb->update('wp_zoho_tickets', $input, array( 'id' =>$ticketId ));
	return $input;
}

function CallAPIs($method, $url, $data = false)
{	
	$curl = curl_init();
	$postdata = json_encode($data);
	$access_token='';
	global $wpdb;
	$results = $wpdb->get_results( 'SELECT * FROM wp_zoho_credentials');
	if ($results)
	{
		foreach($results as $result)
		{
			$access_token = $result->access_token;
			break;
		}
	}
	$headers = [
		'orgId: 659082188',
		'Authorization:Zoho-oauthtoken '.$access_token.'',
		'Content-Type: application/json',
// 		'Content-Type:multipart/form-data'
	];

	switch ($method)
	{
		case "POST":
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if ($postdata)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
			break;
		case "PUT":
			curl_setopt($curl, CURLOPT_PUT, 1);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			break;
		case "PATCH":
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			if ($postdata)
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
			break;
		case "GET":
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			break;
		default:

	}


	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HEADER, true);    // we want headers

	$result = curl_exec($curl);
	$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
	$header = substr($result, 0, $header_size);
	$body = substr($result, $header_size);

	curl_close($curl);
   
   
	if($httpcode==200)
	{
		$response['status']=$httpcode;
		$response['body']=json_decode($body);
		return $response;
	}else{
		$response['status']=$httpcode;
		$response['error']=json_decode($body)->errorCode;
		$response['message']=json_decode($body)->message;
		return $response;
	}
}
add_action( 'graphql_register_types', 'register_mutations_and_query_models' );

function register_mutations_and_query_models() {

	register_graphql_object_type('zohoPlan', [
		'fields'=>[
			'description'=>['type'=> 'String'],
			'unit_amount'=>['type'=> 'String'],
			'currency'=>['type'=>  'String'],
			'created_date'=>['type'=> 'String'],
			'name'=>['type'=> 'String'],
			'address'=>['type'=> 'String'],
			'invoiceNo'=>['type'=> 'String'],			
			'planTickets' => [
			'type' => ['list_of' =>'planTickets'],
				
		  ],
		]
	]);
	register_graphql_object_type('zohoActivePlan', [
        'fields'=>[
            'name'=>['type'=> 'String'],
            'unit_amount'=>['type'=>'String'],
            'created_date'=>['type'=> 'String'],
            'expiry_date'=>['type'=> 'String']
        ]
    ]);
	register_graphql_object_type('planTickets', [
		'fields'=>[
			'ticket_id'=>['type'=> 'String'],
			'subject'=>['type'=> 'String'],
			'time'=>['type'=> 'String'],
			'ticketNumber'=>['type'=> 'String'],
		]
	]);
	register_graphql_object_type('tickets', [
		'fields'=>[
			'type'=>['list_of'=>'ticket']
		]
	]);
	register_graphql_object_type( 'zohoContact', [
		'description' => __( 'Describe what a CustomType is', 'your-textdomain' ),
		'fields' => [
		  'id' => [
			'type' => 'String',
		  ],
		  'email' => [
			'type' => 'String',
		  ],
		  'firstName' => [
			'type' => 'String',
		  ],
		  'lastName' => [
			'type' => 'String',
		  ],
		  'type' => [
			'type' => 'String',
		  ],
		  'facebook' => [
			'type' => 'String',
		  ],
		  'twitter' => [
			'type' => 'String',
		  ],
		  'secondaryEmail' => [
			'type' => 'String',
		  ],
		  'phone' => [
			'type' => 'String',
		  ],
		  'mobile' => [
			'type' => 'String',
		  ],
		  'city' => [
			'type' => 'String',
		  ],
		  'country' => [
			'type' => 'String',
		  ],
		  'state' => [
			'type' => 'String',
		  ],
		  'street' => [
			'type' => 'String',
		  ],
		  'zip' => [
			'type' => 'String',
		  ],
		  'description' => [
			'type' => 'String',
		  ],
		  'title' => [
			'type' => 'String',
		  ],
		  'photoURL' => [
			'type' => 'String',
		  ],
		  'webUrl' => [
			'type' => 'String',
		  ],
		  'isDeleted' => [
			'type' => 'boolean',
		  ],
		  'isTrashed' => [
			'type' => 'boolean',
		  ],
		  'isSpam' => [
			'type' => 'boolean',
		  ],
		  'createdTime' => [
			'type' => 'String',
		  ],
		  'modifiedTime' => [
			'type' => 'String',
		  ],
		  'accountId' => [
			'type' => 'String',
		  ],
		  'ownerId' => [
			'type' => 'String',
		  ],
		  'user_id' => [
			'type' => 'String',
		  ],
		  'company_id' => [
			'type' => 'Integer',
		  ],
		],
	] );

	register_graphql_object_type( 'zohoTicket', [
		'description' => __( 'Describe what a CustomType is', 'your-textdomain' ),
		'fields' => [
		  'id' => [
			'type' => 'String',
		  ],
		  'ticketNumber'=> [
			'type' => 'String',
		  ],
		  'subject' => [
			'type' => 'String',
		  ],
		  'description' => [
			'type' => 'String',
		  ],
		  'priority' => [
			'type' => 'String',
		  ],
		  'departmentId' => [
			'type' => 'String',
		  ],
		  'contactId' => [
			'type' => 'String',
		  ],
		  'departmentIds' => [
			'type' => 'String',
		  ],
		  'viewId' => [
			'type' => 'String',
		  ],
		  'assignee' => [
			'type' => 'String',
		  ],
		  'channel' => [
			'type' => 'String',
		  ],
		  'status' => [
			'type' => 'String',
		  ],
		  'sortBy' => [
			'type' => 'String',
		  ],
		  'receivedInDays' => [
			'type' => 'String',
		  ],
		  'include' => [
			'type' => 'String',
		  ],
		  'fields' => [
			'type' => 'String',
		  ],		  
		  'classification'=> [
			'type'=>'String'
		  ],
		  'created_at'=> [
			'type'=>'String'
		  ],
		  'time'=> [
			'type'=>'String'
		  ],
		  'watcher'=>[
			'type'=>'string'
		  ],
		  'customer_url'=>[
			'type'=>'string'
		  ],
		  'steps_to_reproduce'=>[
			'type'=>'string'
		  ],
		  'business_impact'=>[
			'type'=>'string'
		  ],
		  'problematic_plugin'=>[
			'type'=>'string'
		  ],
		  'business_use_case'=>[
			'type'=>'string'
		  ],
		  'inspiration'=>[
			'type'=>'string'
		  ],
		  'post_media_to'=>[
			'type'=>'string'
		  ],
		  'post_description'=>[
			'type'=>'string'
		  ],
		  'attachment' => [
			'type' => 'string'
		  ],
		  'totalRecords' => [
			'type' => 'Integer'
		  ],
		],
	] );

	register_graphql_object_type( 'zohoCompany', [
		'fields' => [
			'id' => [
			'type' => 'Integer',
				],
		  	'user_id' => [
				'type' => 'Integer',
		  	],
		  	'contact_id' => [
				'type' => 'Integer',
		  	],
			'name' => [
				'type' => 'String',
			],
			'email' => [
				'type' => 'String',
			],
			'logo' => [
				'type' => 'String',
			],
			'zip' => [
				'type' => 'String',
			],
			'city' => [
				'type' => 'String',
			],
			'state' => [
				'type' => 'String',
			],
			'phone' => [
				'type' => 'String',
			],
			'website_url' => [
				'type' => 'String',
			],
			'mailing_address' => [
				'type' => 'String',
			],
		],
	] );
	register_graphql_object_type( 'zohoTicketbyUserId', [
		'description' => __( 'Describe what a CustomType is', 'your-textdomain' ),
		'fields' => [
		  'departmentIds' => [
			'type' => 'String',
		  ],
		  'viewId' => [
			'type' => 'String',
		  ],
		  'assignee' => [
			'type' => 'String',
		  ],
		  'channel' => [
			'type' => 'String',
		  ],
		  'status' => [
			'type' => 'String',
		  ],
		  'sortBy' => [
			'type' => 'String',
		  ],
		  'receivedInDays' => [
			'type' => 'String',
		  ],
		  'include' => [
			'type' => 'String',
		  ],
		  'fields' => [
			'type' => 'String',
		  ],
		  'user_id' => [
			'type' => 'Integer',
		  ],
		  'name' => [
			'type' => 'String',
		  ],
		  'email' => [
			'type' => 'String',
		  ],
		  'logo' => [
			'type' => 'String',
		  ],
		  'phone' => [
			'type' => 'String',
		  ],
		  'id' => [
			'type' => 'Integer',
		  ],
		  'totaltime' => [
			'type' => 'String',
		  ],
		  'tickets' => [
			'type' => ['list_of' =>'zohoTicket'],
		  ],
		  'counts' => [
			'type' =>'ticketCounts',
		  ],
		],
	]);

	register_graphql_object_type('ticketCounts', [
		'fields' => [
			'open' => [
				'type' => 'Integer',
			],
			'waiting' => [
				'type' => 'Integer',
			],
			'escalated' => [
				'type' => 'Integer',
			],
			'completed' => [
				'type' => 'Integer',
			],
		],
	]);

	register_graphql_object_type( 'zohoUser', [
		'fields' => [
        	'id' => [
				'type' => 'String',
		  	],
		  	'created_by' => [
				'type' => 'String',
		  	],
		  	'name' => [
				'type' => 'String',
		  	],
			'email' => [
				'type' => 'String',
			],
			'user_role' => [
				'type' => 'String',
			],
		],
	] );
	register_graphql_object_type( 'zohoTicketComment', [
		'fields' => [
        	'id' => ['type' => 'String'],
		  	'isPublic' => ['type' => 'String'],
		  	'commentedTime' => ['type' => 'String'],
			'contentType' => ['type' => 'String'],
			'content' => ['type' => 'String'],
			'commenterId' => ['type' => 'String'],
			'photoURL'=>['type'=>'string'],
			'commentorName'=>['type'=>'String'],
			'roleName'=>['type'=>'string'],
			'type'=>['type'=>'string'],
			'email'=>['type'=>'String'],
			'companyId'=>['type'=>'String'],
			'attactment'=>['type'=>'String']
		],
	] );
	register_graphql_object_type( 'attachments', [
		'fields' => [
			'size'=>['type'=>'string'],
			'href'=>['type'=>'string'],
			'name'=>['type'=>'string'],
			'id'=>['type'=>'String']
		],
	] );
// 	register_graphql_object_type( 'commenter', [
// 		'fields' => [
// 			'firstName'=>['type'=>'string'],
// 			'lastName'=>['type'=>'string'],
// 			'photoURL'=>['type'=>'string'],
// 			'name'=>['type'=>'String'],
// 			'roleName'=>['type'=>'string'],
// 			'type'=>['type'=>'string'],
// 			'email'=>['type'=>'String']
// 		],
// 	] );
};

//zoho contacts and zoho tickets

function RefreshToken()
{	
	global $wpdb;
	$results = $wpdb->get_results( 'SELECT * FROM wp_zoho_credentials');
	if ($results)
	{
		foreach($results as $result)
		{
			// $wpdb->update( 'wp_zoho_credentials',array('access_token'=>'23455'), array( 'id' => $result->id ) );
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_POST, 1);
			$redirect_url='http://localhost:8000';
			// $url='https://accounts.zoho.com/oauth/v2/token?refresh_token=1000.0964963bc54f8e8055150db7d1abda73.d33f36aedda0a0f05eae8098af3e4dc1&grant_type=refresh_token&client_id=1000.HNT5KUX4GBCXFIKXXTL88O8NEPRFRD&client_secret=67951b0d42f4a14b96ade319998392a0be60321d52&redirect_uri=http://localhost:8000&scope=Desk.tickets.ALL,Desk.contacts.ALL';

			$url='https://accounts.zoho.com/oauth/v2/token?refresh_token='.$result->refresh_token.'&grant_type=refresh_token&client_id='.$result->client_id.'&client_secret='.$result->client_secret.'&redirect_uri='.$redirect_url.'&scope='.'Desk.contacts.WRITE,Desk.contacts.READ,Desk.tickets.ALL,Desk.settings.READ,Desk.basic.READ,Desk.tickets.UPDATE,Desk.events.ALL,Desk.basic.READ,Desk.basic.CREATE'.'';
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_HEADER, true);    // we want headers
			$theResult = curl_exec($curl);
			$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$header = substr($theResult, 0, $header_size);
			$body = substr($theResult, $header_size);
			
			curl_close($curl);
			if($httpcode==200)
			{
				$wpdb->update( 'wp_zoho_credentials',array('access_token'=>json_decode($body)->access_token), array( 'id' => $result->id ) );
				//json_decode($body)->access_token
			}
			break;
		}
	}
}
add_action( 'wp_curl_jobs', 'RefreshToken' );

//getZohoContactByUserId
add_action( 'graphql_register_types', function() {
register_graphql_field( 
	'RootQuery', 
		'getZohoContactByUserId', [
			'type'=>[ 'list_of' => 'zohoContact' ],
			'args' => [
				'userId' => [
					'type' => ['non_null'=>'String'],
				],
			],
			'resolve' => function($root, $args, $context, $info ) {
				global $wpdb;
				$user_id = base64_decode($args['userId']); // the result is not a stringified number, neither printable
				$string=str_replace('user:', '',$user_id  );
				$user_id= json_decode($string);
				$contact=$wpdb->get_results('SELECT * FROM wp_zoho_contacts WHERE user_id='.$user_id.'');
				return $contact;
			},	
		],
	);
});
//getZohoContacts
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
			'getZohoContacts', [
				'type'=>[ 'list_of' => 'zohoContact' ],
				'resolve' => function($root, $args, $context, $info ) {
					global $wpdb;
					$contact=$wpdb->get_results('SELECT * FROM wp_zoho_contacts');
					return $contact;
				//	$contacts=CallAPIs('GET', "https://desk.zoho.com/api/v1/contacts?from=1&limit=10");
				//	return $contacts['body']->data;
				},
			],
		);
});
//getCompanies
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
			'getCompanies', [
				'type'=>[ 'list_of' => 'zohoCompany' ],

				'resolve' => function($source, $args, $context, $info ) {
					global $wpdb;
					$results=$wpdb->get_results('SELECT * FROM wp_zoho_companies');
					return $results;
				},
			],
		);
});

//getPlans by company id 
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
			'getZohoPlansByCompanyId', [
				'type'=>[ 'list_of' => 'zohoPlan' ],
				'args' => [
				'companyId' => [
					'type' => ['non_null'=>'String'],
					],
				],

				'resolve' => function($source, $args, $context, $info ) {
					global $wpdb;
					$company_id = $args['companyId'];
					$plans = $wpdb->get_results('SELECT * FROM wp_zoho_plans LEFT JOIN wp_zoho_companies ON wp_zoho_plans.companyId = wp_zoho_companies.id WHERE wp_zoho_plans.companyId = "'.$company_id.'"');
					
					foreach ($plans as $plan) 
					{

						$ary = explode(" ", $plan->description);
						$des1 = $ary[0];
						$des2 = $ary[1];
						$plan->description = $des1." ".$des2."/Mo";

						$plan->unit_amount = ($plan->unit_amount/100);

						$tmp =explode(" ", $plan->created_date);

						$curDate = $tmp[0];
						$curruntDate = strtotime($tmp[0]);
						$plan->created_date = date("m/d/Y", $curruntDate);

						$planMaxDate = date('Y-m-d', strtotime($curDate. ' + 30 days'));


						$plan->address = $plan->mailing_address.' '.$plan->city.' '.$plan->state.', '.$plan->zip;

						$plan->invoiceNo = rand(11111111,99999999);
						$tickets = $wpdb->get_results('SELECT id AS ticket_id, ticketNumber, subject FROM wp_zoho_tickets  WHERE company_id ="'.$company_id.'" AND created_at >= "'.$curDate.'" AND wp_zoho_tickets.created_at <= "'.$planMaxDate.'"');


						foreach($tickets as $key => $item)
						{
    					  $time=$wpdb->get_results('SELECT SUM(hours)*3600 + SUM(minutes)*60+SUM(seconds) as time , id FROM wp_zoho_tickets_time_entry WHERE ticketId = '.$item->ticket_id);
    					  $tickets[$key]->time=($time[0]->time)/3600;
    					}
    					$plan->planTickets=$tickets;
    				}						
					return $plans;
				},
			],
	);
});

// //getPlans
// add_action( 'graphql_register_types', function() {
// 	register_graphql_field( 
// 		'RootQuery', 
// 			'getZohoPlansByCompanyId', [
// 				'type'=>[ 'list_of' => 'zohoPlan' ],
//                 'args' => [
// 					'companyId' => [
// 						'type' => ['non_null'=>'String'],
// 					],
// 				],
// 				'resolve' => function($source, $args, $context, $info ) {
// 					global $wpdb;
// 					 $company_id=$args['companyId'];
// 					$results=$wpdb->get_results('SELECT * FROM wp_zoho_plans WHERE companyId="'.$company_id.'"');
// 					return $results;
// 				},
// 			],
// 		);
// });

//getCompany by compnayid
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
			'getCompanyByCompanyId', [
				'type'=>[ 'list_of' => 'zohoCompany' ],
                'args' => [
					'companyId' => [
						'type' => ['non_null'=>'Integer'],
					],
				],
				'resolve' => function($source, $args, $context, $info ) {
				    $company_id=$args['companyId'];
					global $wpdb;
					$results=$wpdb->get_results('SELECT * FROM wp_zoho_companies WHERE id='.$company_id);
					return $results;
				},
			],
		);
});

//getTicketsByCompanyId
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
			'getTicketsByCompanyId', [
				'type'=>[ 'list_of' => 'zohoTicket'],

				// 'type'=>[ 'String' => 'time'],
				'args' => [
					'comapnyId' => [
						'type' => ['non_null'=>'Integer'],
					],
					'pageNo' => [
						'type' => 'Integer',
					],
					'status' => [
						'type' => 'String',
					],
				],
				'resolve' => function($root, $args, $context, $info ) {
					global $wpdb;
					$company_id=$args['comapnyId'];
					$page_no=$args['pageNo'];
					$status=$args['status'];

					$start_no=0;
					$end_no=10;

					if ($page_no != null) 
					{
						$start_no=$page_no-1;
						if ($start_no != 0) 
						{
							$start_no.=1;
							$end_no=$start_no+(9);
						}
						else
						{
							$end_no=$start_no+(10);
						}
					}
					
					if($status){
					 $tickets=$wpdb->get_results('SELECT * FROM wp_zoho_tickets WHERE company_id='.$company_id.' AND isDeleted=0 AND status="'.$status.'" ORDER BY id DESC LIMIT '.$start_no.','.$end_no);   
					}
					else{
					 $tickets=$wpdb->get_results('SELECT * FROM wp_zoho_tickets WHERE company_id='.$company_id.' AND isDeleted=0 ORDER BY id DESC LIMIT '.$start_no.','.$end_no);   
					}
					

					$numRows=$wpdb->get_results('SELECT COUNT(*) AS num FROM wp_zoho_tickets WHERE company_id='.$company_id.' AND isDeleted=0');
			
					foreach($tickets as $key => $item){
					    
					  $time=$wpdb->get_results('SELECT SUM(hours)*3600 + SUM(minutes)*60+SUM(seconds) as time , id FROM wp_zoho_tickets_time_entry WHERE ticketId = '.$item->id);
					  $tickets[$key]->time=($time[0]->time)/3600;

					  $attachment=$wpdb->get_results('SELECT `name` FROM `wp_zoho_attachments` WHERE `ticket_id`='.$item->id);

					  $tickets[$key]->attachment=$attachment[0]->name;
					  $item->totalRecords=$numRows[0]->num;
					}
					

					return $tickets;
				},	
			],
		);
});

//getTicketsByTitle
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
			'getTicketsByTitle', [
				'type'=>[ 'list_of' => 'zohoTicket'],
				'args' => [
					'companyId' => [
						'type' => ['non_null'=>'Integer'],
					],
					'ticketTitle' => [
						'type' => ['non_null'=>'String'],
					]
				],
				'resolve' => function($root, $args, $context, $info ) {
					global $wpdb;
					$companyId=$args["companyId"];
					$ticketTitle=$args["ticketTitle"];

					$tickets=$wpdb->get_results('SELECT * FROM wp_zoho_tickets WHERE company_id='.$companyId.' AND isDeleted=0 AND subject LIKE "%'.$ticketTitle.'%" ORDER BY `id` DESC');
					return $tickets;
				},	
			],
		);
});

//getZohoCompaniesTicketsByUserId
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery',
			'getZohoCompaniesTicketsByUserId', [
				'type'=>[ 'list_of' => 'zohoTicketbyUserId' ],
				'args' => [
					'userId' => [
					  'type' => ['non_null'=>'String'],
					],
				],
				'resolve' => function($source, $args, $context, $info ) {
					global $wpdb;
					$user_id = base64_decode($args['userId']); // the result is not a stringified number, neither printable
					$string=str_replace('user:', '',$user_id  );
					$user_id= json_decode($string);
					// $arryObj=array();
					$companies=$wpdb->get_results('SELECT * FROM wp_zoho_companies WHERE user_id='.$user_id);
		
					foreach ($companies as  $company)
					{
						$ComapnyTickets=$wpdb->get_results('SELECT * FROM wp_zoho_tickets WHERE company_id='.$company->id.' AND isDeleted=0 LIMIT 3 ');
						$totaltime=0;
						
						$openStatus=$wpdb->get_results('SELECT COUNT(id) as num  FROM wp_zoho_tickets WHERE company_id='.$company->id.' AND status="Open" AND isDeleted=0');
						$waitingStatus=$wpdb->get_results('SELECT COUNT(id) as num FROM wp_zoho_tickets WHERE company_id='.$company->id.' AND status="Waiting" AND isDeleted=0');
						$escalatedStatus=$wpdb->get_results('SELECT COUNT(id) as num FROM wp_zoho_tickets WHERE company_id='.$company->id.' AND status="Escalated" AND isDeleted=0');
						$completedStatus=$wpdb->get_results('SELECT COUNT(id) as num FROM wp_zoho_tickets WHERE company_id='.$company->id.' AND status="Completed" AND isDeleted=0');

						foreach($ComapnyTickets as $key => $item)
						{
    					  $time=$wpdb->get_results('SELECT SUM(hours)*3600 + SUM(minutes)*60+SUM(seconds) as time , id FROM wp_zoho_tickets_time_entry WHERE ticketId = '.$item->id);
    					  $ComapnyTickets[$key]->time=($time[0]->time)/3600;
    					  $totaltime+=($time[0]->time)/3600;
    					}
						$company->tickets=array();
						$company->counts->open=$openStatus[0]->num;;
						$company->counts->waiting=$waitingStatus[0]->num;
						$company->counts->escalated=$escalatedStatus[0]->num;
						$company->counts->completed=$completedStatus[0]->num;

						if($ComapnyTickets)
						{
							$company->tickets=$ComapnyTickets;
							$company->totaltime=$totaltime;
						}
						else
						{
							$company->tickets=NULL;
						}
					}
					return $companies;
				},
			],
		);
});

// to get active plan
add_action( 'graphql_register_types', function() {
    register_graphql_field( 
        'RootQuery', 
            'getActiveZohoPlanByCompanyId', [
                'type'=>'zohoActivePlan' ,
                'args' => [
                'companyId' => [
                    'type' => ['non_null'=>'String'],
                    ],
                ],

            'resolve' => function($source, $args, $context, $info ) {
                global $wpdb;
                $company_id = $args['companyId'];
                $activePlan = $wpdb->get_results('SELECT name, created_at, (unit_amount)/100 as unit_amount FROM wp_zoho_plans WHERE  is_active = 1 AND companyId = "'.$company_id.'" ');                        
                
                foreach($activePlan AS $active)
                {
                    $tmp =explode(" ", $active->created_at);

                    $curDate = $tmp[0];
                    $curruntDate = strtotime($tmp[0]);
                    $active->created_date = date("m/d/Y", $curruntDate);
                    $expiryDate = date('Y-m-d', strtotime($curDate. ' + 30 days'));
                    $expiryDate = strtotime($expiryDate);
                    $active->expiry_date = date("m/d/Y", $expiryDate);
                }
                return $activePlan[0];
            },
        ],
    );
});




// get Tickets all comments
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
		'getCommentsByTicketID', [
			'type'=>[ 'list_of' => 'zohoTicketComment'],
			'args' => [
				'ticketID' => [
					'type' => ['non_null'=>'String'],
				],
			],
			'resolve' => function($root, $args, $context, $info ) {
				global $wpdb;
				$ticketID = $args['ticketID']; // the result is not a stringified number, neither printable
				$comments=$wpdb->get_results('SELECT * FROM wp_zoho_tickets_comments WHERE  ticketId='.$ticketID.' ORDER BY created_at ASC');
				foreach($comments as $key => $item)
				{
    				$attactment=$wpdb->get_results('SELECT * FROM wp_zoho_attachments WHERE comment_id = '.$item->id);
    				$comments[$key]->attactment='https://gatsby.lizayfashion.com/attachments/'.$attactment[0]->name;	  
    			}
				
				return $comments;
			},	
		],
	);
});
//getUsersByUserID
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
		'getZohoUsersByUserId', [
			'type'=>[ 'list_of' => 'zohoUser'],
			'args' => [
				'userId' => [
					'type' => ['non_null'=>'String'],
				],
			],
			'resolve' => function($root, $args, $context, $info ) {
				global $wpdb;
				$user_id = base64_decode($args['userId']); // the result is not a stringified number, neither printable
				$string=str_replace('user:', '',$user_id  );
				$user_id= json_decode($string);
				$users=$wpdb->get_results('SELECT * FROM wp_users WHERE created_by='.$user_id.'');
				foreach($users as $user){
					$user->id=base64_encode($user->ID);
					$user->create_by=base64_encode($user->create_by);
					$user->name=$user->user_nicename;
					$user->email=$user->user_email;
				}
				return $users;
			},	
		],
	);
});

//getAdminsAndEditors
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
		'getAdminsAndEditors', [
			'type'=>[ 'list_of' => 'zohoUser'],
			'resolve' => function($root, $args, $context, $info ) {
				global $wpdb;
				$users=$wpdb->get_results('SELECT * FROM wp_users WHERE role="editor" OR role="admin" ');
				foreach($users as $user){
					$user->id=base64_encode('user:'.$user->ID);
					$user->created_by=base64_encode('user:'.$user->created_by);
					$user->name=$user->user_nicename;
					$user->email=$user->user_email;
                    $user->user_role=$user->role;
				}
				return $users;
			},	
		],
	);
});


//getAdminsAndEditorsByUserId
add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
		'getAdminsAndEditorsByUserId', [
			'type'=>[ 'list_of' => 'zohoUser'],
			'args' => [
				'createdBy' => [
					'type' => ['non_null'=>'String'],
				],
			],
			'resolve' => function($root, $args, $context, $info ) {
				global $wpdb;
				$created_by =$args['createdBy']; // the result is not a stringified number, neither printable
				$string=str_replace('user:', '',$created_by  );
				$created_by= json_decode($string);
				$users=$wpdb->get_results('SELECT * FROM wp_users WHERE (role="editor" OR role="admin") AND created_by='.$created_by.' ');
				foreach($users as $user){
					$user->id=base64_encode('user:'.$user->ID);
					$user->created_by=base64_encode('user:'.$user->created_by);
					$user->name=$user->user_nicename;
					$user->email=$user->user_email;
                    $user->user_role=$user->role;
				}
				return $users;
			},	
		],
	);
});

// To Upload Image
add_action( 'rest_api_init', 'upload_zoho_image' );
function upload_zoho_image(){
  header( "Access-Control-Allow-Origin: *" );
  register_rest_route('/api/v1/','uploads',array(
    'methods'=>'POST','callback'=>'attachments'
  ));
}
function attachments( $request )
{
   
    $type=explode("/",$_FILES['file']['type'])[1]; 
	$file = new CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name']);
	$ticket_id=$_POST['ticketId'];
	$access_token='';
	global $wpdb;
	$results = $wpdb->get_results( 'SELECT * FROM wp_zoho_credentials');
	if ($results)
	{
		foreach($results as $result)
		{
			$access_token = $result->access_token;
			break;
		}
	}
	$headers = [
		'orgId: 659082188',
		'Authorization:Zoho-oauthtoken '.$access_token.'',
		'Content-Type:multipart/form-data'
	];
	$curl=curl_init();
		curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://desk.zoho.com/api/v1/tickets/'.$ticket_id.'/attachments?isPublic=true',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('file'=> $file),
		CURLOPT_HTTPHEADER =>$headers
	));
	$result = curl_exec($curl);
	curl_close($curl);
	$jsondata =json_decode($result,true);
    global $wpdb;
    $jsondata['name']=$jsondata['id'].'.'.$type;
    $wpdb->insert('wp_zoho_attachments', $jsondata);
	$curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://desk.zoho.com/api/v1/tickets/'.$ticket_id.'/attachments/'.$jsondata['id'].'/content',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER =>$headers
    ));
    
    $response = curl_exec($curl);
    $myfile = fopen("attachments/".$jsondata['id'].'.'.$type, "w") or die("Unable to open file!");
    fwrite($myfile, $response);
    fclose($myfile);
    curl_close($curl);
	return  $jsondata;
}

//To upload_zoho_ticket_image
add_action( 'rest_api_init', 'upload_zoho_ticket_image' );
function upload_zoho_ticket_image(){
  header( "Access-Control-Allow-Origin: *" );
  register_rest_route('/api/v1/','uploadsTicketAttactment',array(
    'methods'=>'POST','callback'=>'ticketAttachments'
  ));
}
function ticketAttachments( $request )
{
    $type=explode("/",$_FILES['file']['type'])[1];
    $file = new CURLFile($_FILES['file']['tmp_name'], $_FILES['file']['type'], $_FILES['file']['name']);
    $ticket_id=$_POST['ticketId'];
    $access_token='';
    global $wpdb;
    $results = $wpdb->get_results( 'SELECT * FROM wp_zoho_credentials');
    if ($results)
    {
        foreach($results as $result)
        {
            $access_token = $result->access_token;
            break;
        }
    }
    $headers = [
        'orgId: 659082188',
        'Authorization:Zoho-oauthtoken '.$access_token.'',
        'Content-Type:multipart/form-data'
    ];
    $curl=curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://desk.zoho.com/api/v1/tickets/'.$ticket_id.'/attachments?isPublic=true',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('file'=> $file),
        CURLOPT_HTTPHEADER =>$headers
    ));
    $result = curl_exec($curl);
    curl_close($curl);
    $jsondata =json_decode($result,true);
    global $wpdb;
    $jsondata['ticket_id']=$ticket_id;
    $jsondata['name']=$jsondata['id'].'.'.$type;
    $wpdb->insert('wp_zoho_attachments', $jsondata);
    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://desk.zoho.com/api/v1/tickets/'.$ticket_id.'/attachments/'.$jsondata['id'].'/content',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
      CURLOPT_HTTPHEADER =>$headers
    ));
    
    $response = curl_exec($curl);
    $myfile = fopen("attachments/".$jsondata['id'].'.'.$type, "w") or die("Unable to open file!");
    fwrite($myfile, $response);
    fclose($myfile);
    curl_close($curl);
    return  $jsondata;
}


add_action( 'graphql_register_types', function() {
	register_graphql_field( 
		'RootQuery', 
			'ZohoSendMail', [
				'type'=> 'Boolean',
				'resolve' => function($root, $args, $context, $info ) {
					$toEmail = 'waliurrehman16443@gmail.com';
					$subject = 'Verify Mail';
					
					$headers = array('Content-Type: text/html; charset=UTF-8');
					
                    	$body = 'asjdkajsdbahsdbj';
						return wp_mail( $toEmail, $subject, $body, $headers );
					
				},
			],
		);
});