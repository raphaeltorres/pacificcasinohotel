<?php

class ReportsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function deposit()
	{

		if(ACL::checkUserPermission('reports.deposit') == false){
			return Redirect::action('dashboard');
		}

		$fundin 	 = Fundinout::with('wallet','wallet.playerdetails','operator')->where('fundtype', 'fundin')->get();
		$title 		 = Lang::get('Bought Credits Report');

		$depositList = $fundin->toArray();

		$data = array(
			'acl' 			=> ACL::buildACL(), 
			'depositlist'   => $fundin,
			'title'			=> $title);

		return View::make('reports/deposit',$data);
	}

	public function withdraw()
	{
		if(ACL::checkUserPermission('reports.withdraw') == false){
			return Redirect::action('dashboard');
		}

		$funout 	 = Fundinout::with('wallet','wallet.playerdetails','operator')->where('fundtype', 'fundout')->get();
		$title 		 = Lang::get('Reede Credits Report');
		
		$data = array(
			'acl' 			=> ACL::buildACL(), 
			'depositlist'   => $funout,
			'title'			=> $title);

		return View::make('reports/withdraw',$data);
	}

}
