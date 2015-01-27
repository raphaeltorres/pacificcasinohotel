<?php

class PlayerController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(ACL::checkUserPermission('player.index') == false){
			return Redirect::action('dashboard');
		}

		$userList   = UserMember::with('user','group','wallet')->where('group_id', 4)->get();
		$title 		= Lang::get('Player List');
		$status 	= array('0' => array('label'   => 'default' ,'status'  => 'Inactive'), 
					    	'1' => array('label'  => 'success', 'status' => 'Active'));

		$user = $userList->toArray();

		$data = array(
			'acl' 		=> ACL::buildACL(), 
			'userList'	=> $userList,
			'title'		=> $title,
			'status'	 => $status);

		return View::make('player/index',$data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if(ACL::checkUserPermission('player.create') == false){
			return Redirect::action('dashboard');
		}

		$title 	   = Lang::get('Add Player');
		$formOpen  = Form::open(array('method' => 'post', 'id' => 'form-player','class' => 'smart-form', 'route' => array('player.store')));
		$formClose = Form::close();
		$groupList = ACL::getAllGroup();

		$data = array(
			'formOpen'  => $formOpen, 
			'formClose' => $formClose,
			'groupList' => $groupList,
			'title'		=> $title
			);

		return View::make('player/create', $data);
	}

	public function deposit()
	{
		//retrieve POST value
		$param = Input::only('credits','account_id');

		$rules = array(
			'credits'	 => 'required|numeric|min:1,max:1000000',
			'account_id' => 'exists:acl_users,id');

			//custom error messaging
		$messages = array(
				'buyer_id.exists'	 => 'Buyer id is not valid.',
				'merchant_id.exists' => 'Merchant id is not valid');

		$validator = Validator::make($param, $rules, $messages);

		if ($validator->passes())
		{
			$retrieve = Wallet::where('account_id', $param['account_id'])->first();

			$credits = array(
				'account_id' => $param['account_id'],
				'credits' 	 => $param['credits'], 
			);

			if(!empty($retrieve))
			{
				try{
					$update = $retrieve->increment('credits', $param['credits']);

					if($update == 1)
					{
						$fundin = array(
						 'wallet_id' 	=> $retrieve->id, 
						 'onbehalf'  	=> Auth::user()->id,
						 'credits'		=> $param['credits'],
						 'description'	=> 'Deposit credits',
						 'fundtype'		=> 'fundin'
						 );

						$funds = Fundinout::create($fundin);
					}
				}
				catch(Exception $e){
					return false;
				}
			}
			else
			{
				$add_credits = Wallet::create($credits);

				$fundin = array(
					'wallet_id' 	=> $add_credits->id, 
					'onbehalf'  	=> Auth::user()->id,
					'credits'		=> $param['credits'],
					'description'	=> 'Deposit credits',
					'fundtype'		=> 'fundin'
				);

				$funds = Fundinout::create($fundin);
			}

			$message = 'Credit has been successfully added.';
			return Redirect::action('player.index')->with('success', $message);
		}
		else
		{	
			$messages = $validator->messages();
			return Redirect::action('player.index')->with('error', $messages->all());
		}
	}

	public function withdraw()
	{
		//retrieve POST value
		$param = Input::only('credits','account_id');

		$rules = array(
			'credits'	 => 'required|numeric|min:1,max:1000000',
			'account_id' => 'exists:acl_users,id');

			//custom error messaging
		$messages = array();

		$validator = Validator::make($param, $rules, $messages);

		if ($validator->passes())
		{
			$retrieve = Wallet::where('account_id', $param['account_id'])->first();

			if(!empty($retrieve))
			{
	
				if($retrieve->credits >= $param['credits'])
				{
				
					try{
						$update = $retrieve->decrement('credits', $param['credits']);

						if($update == 1)
						{
							$fundin = array(
						 		'wallet_id' 	=> $retrieve->id, 
						 		'onbehalf'  	=> Auth::user()->id,
						 		'credits'		=> $param['credits'],
						 		'description'	=> 'Withdraw credits',
						 		'fundtype'		=> 'fundout');

							$funds = Fundinout::create($fundin);
						}
					}
					catch(Exception $e){
						return false;
					}

					$message = 'Credit has been successfully withdraw.';
					return Redirect::action('player.index')->with('success', $message);
				}
				else
				{
					return Redirect::action('player.index')->with('error', 'Insufficient credits!');
				}
			}
			else
			{
				return Redirect::action('player.index')->with('error', 'Insufficient credits!');
			}

		}
		else
		{	
			$messages = $validator->messages();
			return Redirect::action('player.index')->with('error', $messages->all());
		}
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if(ACL::checkUserPermission('player.create') == false){
			return Redirect::action('dashboard');
		}

		$rules = array(
    			'username' => 'required|unique:acl_users|alpha_num',
    			'email'    => 'email|unique:acl_users');

		$validator = Validator::make(Input::all(), $rules);

		if ($validator->fails())
		{
			$messages = $validator->messages();
			return Redirect::action('player.create')->withInput(Input::except('password'))->with('error', $messages->all());
		}
		else
		{

			$player_group_id = Group::where('name' , 'Player')->firstOrFail();
			
			$param 		= Input::only('username','email','fullname','company','credits');

			$days = new DateTime(date('Y-m-d H:i:s', strtotime("+30 days")));

			$player_account = array(
				'username'  => $param['username'],
				'email'     => $param['email'],
				'fullname'  => $param['fullname'],
				'company'   => $param['company'],
				'password'  => Hash::make(''),
				'confirmed' => 1,
				'status'    => 1,
				'password_expiration_date' => $days,
				'account_expiration_date'  => $days,
                'created_at'    => new DateTime,
                'updated_at'    => new DateTime);

			$add_account = User::create($player_account);

			$user_member = array(
				'user_id'  		=> $add_account->id,
				'group_id' 		=> $player_group_id->id,
				'date_created'	=> new DateTime
				);

			$add_member = UserMember::create($user_member);

			$credits = array(
				'account_id' => $add_account->id,
				'credits' 	 => $param['credits'], 
				);
		try{

			$add_credits = Wallet::create($credits);
			
			if($param['credits'] > 0)
			{
				$fundin = array(
					'wallet_id' 	=> $add_credits->id, 
					'onbehalf'  	=> Auth::user()->id,
					'credits'		=> $param['credits'],
					'description'	=> 'Fundin on player creation',
					'fundtype'		=> 'fundin'
				);

				$funds = Fundinout::create($fundin);
			}

		}
		catch(Exception $e){
			return false;
		}
			$message = 'Player has been created';
    		return Redirect::action('player.index')->with('success', $message);
		}

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}



}
