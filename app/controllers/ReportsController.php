<?php

class ReportsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function deposit()
	{
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
		$funout 	 = Fundinout::with('wallet','wallet.playerdetails','operator')->where('fundtype', 'fundout')->get();
		$title 		 = Lang::get('Reede Credits Report');
		
		$data = array(
			'acl' 			=> ACL::buildACL(), 
			'depositlist'   => $funout,
			'title'			=> $title);

		return View::make('reports/withdraw',$data);
	}

}
