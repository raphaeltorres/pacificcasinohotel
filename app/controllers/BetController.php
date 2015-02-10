<?php

class BetController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function roulette()
	{
		if(ACL::checkUserPermission('bet.roulette') == false){
			return Redirect::action('dashboard');
		}

		$gamedetails   = Gamechannel::with('gamedetails','operator')->orderBy('id', 'DESC')->take(1)->get()->first();

		$playerdetails = User::with('wallet')->where('id', Auth::user()->id)->get()->first();

		$title  	   = Lang::get('Bet Roulette');

		$form_open     = Form::open(array('method' => 'post','class' => 'smart-form', 'route' => array('bet.place'))); 

		$data = array(
			'title' 	   	  => $title,
			'playerdetails'   => $playerdetails,
			'gamedetails'	  => $gamedetails,
			'formOpen'        => $form_open);

		return View::make('bet/roulette', $data);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if(ACL::checkUserPermission('bet.roulette') == false){
			return Redirect::action('dashboard');
		}

		if(Input::has('betnumber'))
		{
			$bet_type = array('straight','split','line','square','basket','doublestreet');

			$param = Input::only('betnumber','amount','game_id');

			if(count($param['betnumber']) >= 0 && count($param['betnumber']) <= 6)
			{
				$retrieve = Wallet::where('account_id', Auth::user()->id)->first();
				$index 	  = count($param['betnumber']) - 1;

				try{
					$update = $retrieve->decrement('credits', (float) $param['amount']);

					$fundinout = array(
						 	'wallet_id' 	=>  $retrieve->id, 
						 	'onbehalf'  	=>  Auth::user()->id,
						 	'credits'		=>  $param['amount'],
						 	'description'	=> 'Bet on Roulette amount of $' . (float) $param['amount'],
						 	'fundtype'		=> 'bet');

					$fundout = Fundinout::create($fundinout);

					$bet = array(
						'player_id'  => Auth::user()->id,
						'channel_id' => $param['game_id'],
						'bet_number' => implode(',' , $param['betnumber']),
						'bet_amount' => $param['amount'],
						'bet_type'  =>  $bet_type[$index],
						);

					Gamebets::create($bet);

					$message = 'Bet has been successfully place.';
					return Redirect::action('bet.roulette')->with('success', $message);		
				
				}
				catch(Exception $e){
					return false;
				}
			}
			else
			{
				return Redirect::action('bet.roulette')->with('error', 'You can only place maximum of 6 number.');
			}


			echo json_encode(Input::all());
			exit;
		}


		if (Input::has('bettype'))
		{
			
			$param 	  = Input::only('bettype','amount','game_id');
			$retrieve = Wallet::where('account_id', Auth::user()->id)->first();

			try{
				$update = $retrieve->decrement('credits', (float) $param['amount']);

				$fundinout = array(
						 	'wallet_id' 	=>  $retrieve->id, 
						 	'onbehalf'  	=>  Auth::user()->id,
						 	'credits'		=>  $param['amount'],
						 	'description'	=> 'Bet on Roulette amount of $' . (float) $param['amount'],
						 	'fundtype'		=> 'bet');

				$fundout = Fundinout::create($fundinout);

				$bet = array(
					'player_id'  => Auth::user()->id,
					'channel_id' => $param['game_id'],
					'bet_number' => $param['bettype'],
					'bet_amount' => $param['amount'],
					'bet_type'  => $param['bettype'],
					);

				Gamebets::create($bet);

				$message = 'Bet has been successfully place.';
				return Redirect::action('bet.roulette')->with('success', $message);		
				
				}
				catch(Exception $e){
					return false;
				}
		}

	}


}
