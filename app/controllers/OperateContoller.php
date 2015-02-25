<?php

class OperateContoller extends \BaseController {

	public $operator_id = '';

	public function __construct()
    {
    	$this->operator_id = Auth::user()->id;
    }

	public function start()
	{
		if(ACL::checkUserPermission('roulette.start') == false){
			return Redirect::action('dashboard');
		}
		
		if(Auth::user()->isOperator())
		{
			$game  = Games::where('game_name', 'Roulette')->take(1)->get()->first();
			$table = Gametables::where('operator_id', $this->operator_id)->take(1)->get()->first();

			if(!empty($table))
			{
				$create_channel = array(
					'channel_id'   => Utils::generateRandomString(),
					'table_id'   => $table->id
				);

				
				$channel_id = '';
				$channel 	= Gamechannel::openchannel()->where('table_id' , $table->id)->take(1)->get()->first();

				if(!empty($channel))
				{
					$channel_id = $channel->id;
				}
				else
				{
					$channel_create = Gamechannel::create($create_channel);
					$channel_id   = $channel_create->id;
				}

			$gamedetails  = Gamechannel::with('tabledetails.gamedetails','tabledetails.operator','bets','bets.playerdetails')->find($channel_id);

			$totalbets 	  = 0;
			$totalplayers = 0; 

			if($gamedetails->bets != null)
			{
				foreach ($gamedetails->bets as $gamebets) {
					$totalbets += $gamebets->bet_amount;
					$totalplayers++;
				}
			}

			$title = Lang::get('Start Roulette Game');

			$data = array(
				'title' 	   => $title,
				'totalbets'	   => $totalbets,
				'totalplayers' => $totalplayers,
				'gamedetails'  => $gamedetails);

				return View::make('operator/create', $data);
			}
			else
			{
				return App::abort(401, 'No Roulette table has been assigned to your account.');
			}

		}
		else
		{
			return App::abort(401, 'You are not a operator.');
		}
	}

}
