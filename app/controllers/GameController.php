<?php

class GameController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(ACL::checkUserPermission('games.roulette') == false){
			return Redirect::action('dashboard');
		}

		$title 		 = Lang::get('Roulette Game List');
		
		$channellist = Gamechannel::with('gamedetails','operator')->get();

		$data = array(
			'acl' 		=> ACL::buildACL(), 
			'gamelist'	=> $channellist,
			'title'		=> $title);

		return View::make('games/index',$data);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function operate()
	{
		if(ACL::checkUserPermission('games.operate') == false){
			return Redirect::action('dashboard');
		}

		$game = Games::where('game_name', 'Roulette')->take(1)->get()->first();

		$create_channel = array(
			'channel_id'   => Utils::generateRandomString(),
			'game_id'      => $game->id,
			'operator_id'  => Auth::user()->id
		);

		if (Session::has('game_session'))
		{
			$game_session = Session::get('game_session');
		}
		else
		{
			$channel = Gamechannel::create($create_channel);
			$game_session = $channel->id;
			Session::put('game_session', $game_session);
		}

		$gamedetails  = Gamechannel::with('gamedetails','operator','bets','bets.playerdetails')->find($game_session);

		$totalbets 	  = 0;
		$totalplayers = 0; 

		if($gamedetails->bets != null)
		{
			foreach ($gamedetails->bets as $gamebets) {
				$totalbets += $gamebets->bet_amount;
				$totalplayers++;
			}
		}

		$title 		= Lang::get('Roulette Game');

		$data = array(
			'title' 	   => $title,
			'totalbets'	   => $totalbets,
			'totalplayers' => $totalplayers,
			'gamedetails'  => $gamedetails);

		return View::make('games/create', $data);
	}

	public function winnings()
	{
		//retrieve POST value
		$param = Input::only('gameid','winning_number');

		$rules = array(
			'gameid'	 	 => 'required|exists:game_channel,id',
			'winning_number' => 'required|integer|between:0,36');

		//custom error messaging
		$messages = array(
				'gameid.exists'	 => 'Game doesn\'t exist');

		$validator = Validator::make($param, $rules, $messages);

		if ($validator->passes())
		{
			$gamewinnings = array(
				'game_id' 	 	 => 1,
				'channel_id'  	 => $param['gameid'],
				'winning_number' => $param['winning_number']);

			$winnings = Gamewinnings::create($gamewinnings);
			Gamechannel::where('id',$param['gameid'])->update(array('channel_status' => 0));

			$bets = Gamebets::with('channel','payout')->where('channel_id', $param['gameid'])->get();
			$bet_win = array();

			foreach ($bets as $row) {
				$win  = false; 
				switch ($row->payout->name) 
				{
					case "straight":
						if($row->bet_number == $param['winning_number'])
						{	
							$win = true;
						}
					break;

					case "split":
						$numbers = explode(",", $row->bet_number);
						if(in_array($param['winning_number'], $numbers))
						{
							$win = true;
						} 
					break;

					case "line":
						$numbers = explode(",", $row->bet_number);
						if(in_array($param['winning_number'], $numbers))
						{
							$win = true;
						} 
					break;

					case "square":
						$numbers = explode(",", $row->bet_number);
						if(in_array($param['winning_number'], $numbers))
						{
							$win = true;
						} 
					break;

					case "basket":
						$numbers = explode(",", $row->bet_number);
						if(in_array($param['winning_number'], $numbers))
						{
							$win = true;
						} 
					break;

					case "1stcolumn":
						$numbers_covered = explode(',',$row->payout->numbers_covered);
						if(in_array($param['winning_number'], $numbers_covered))
						{
							$win = true;
						}
					break;

					case "2ndcolumn":
						$numbers_covered = explode(',',$row->payout->numbers_covered);
						if(in_array($param['winning_number'], $numbers_covered))
						{
							$win = true;
						}
					break;

					case "3rdcolumn":
						$numbers_covered = explode(',',$row->payout->numbers_covered);
						if(in_array($param['winning_number'], $numbers_covered))
						{
							$win = true;
						}
					break;

					case "1dozen":
						$numbers_covered = explode(',',$row->payout->numbers_covered);
						if(in_array($param['winning_number'], $numbers_covered))
						{
							$win = true;
						}
					break;

					case "2dozen":
						$numbers_covered = explode(',',$row->payout->numbers_covered);
						if(in_array($param['winning_number'], $numbers_covered))
						{
							$win = true;
						}
					break;

					case "3dozen":
						$numbers_covered = explode(',',$row->payout->numbers_covered);
						if(in_array($param['winning_number'], $numbers_covered))
						{
							$win = true;
						}
					break;

					case "even";
						if($param['winning_number'] % 2 == 0)
						{
							$win = true;
						}
					break;

					case "odd";
						if($param['winning_number'] % 2 != 0)
						{
							$win = true;
						}
					break;
			
					default:
						$numbers_covered = explode(',',$row->payout->numbers_covered);
						if(in_array($param['winning_number'], $numbers_covered))
						{
							$win = true;
						}
				}

				if($win == true)
				{
					$update = array(
						'bet_result' => 'win', 
						'bet_status' => 0);

					Gamebets::where('id',$row->id)->update($update);
					$this->player_winnings($row->payout->payout,$row->player_id,$row->bet_amount,$row->channel->channel_id);
					$bet_win[] = $row->id;
				}
				elseif($win == false)
				{
					$update = array(
						'bet_result' => 'loss', 
						'bet_status' => 0);

					Gamebets::where('id',$row->id)->update($update);
				} 
			}

			if(count($bet_win) > 0)
			{
				$player_winnings = Gamebets::with('payout','playerdetails')->whereIn('id',$bet_win)->get();

				if(!empty($player_winnings->first()))
				{
					$response = array(
        	 		'betwin'  => true,		
        			'message' => 'List of players who won.',
        			'data'    => $player_winnings->toArray());
				}	
			}
			else
			{
        	 	$response = array(
        	 		'betwin'	=> false,
        			'message' 	=> 'No player has won this game.');
			}

			Session::forget('game_session');
		}
		else
		{
			//All error message in validation
			$messages = $validator->messages();

			 //response if merchant doesnt have list of products.
        	 $response = array(
        	 		'betwin'		 => 404,
        			'error'			 => 'not_found',
        			'error_message' => $messages->all());
		}
		
		echo json_encode($response);
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

	public function player_winnings($payout,$player_id,$bet_amount,$channel_id)
	{
		$payout_amount = $payout * $bet_amount + $bet_amount;

		$retrieve = Wallet::where('account_id', $player_id)->first();

		try
		{
			$update = $retrieve->increment('credits', $payout_amount);

			if($update == 1)
			{
				$fundin = array(
					'wallet_id' 	=> $retrieve->id, 
					'onbehalf'  	=> Auth::user()->id,
					'credits'		=> $payout_amount,
					'description'	=> 'Player winnings on roullete ' . $channel_id,
					'fundtype'		=> 'winnings');

					$funds = Fundinout::create($fundin);
			}
			
		}
		catch(Exception $e){
			return false;
		}

	}



}
