@extends('layouts.main')

{{-- Web site Title --}}
@section('title')
  {{{ $title }}}
@stop

{{-- Content --}}
@section('content')
<div id="content">
	<!-- row -->	
		<div class="row">
			<div class="col-sm-12">
				
							<div class="well well-sm">
				
								<div class="row">
				
									<div class="col-sm-12 col-md-12 col-lg-6">
										<div class="well well-light well-sm no-margin no-padding">
				
											<div class="row">
				
												<div class="col-sm-12">
													<div id="myCarousel" class="carousel fade profile-carousel">
														<div class="air air-bottom-right padding-10 button">
														 <a href="javascript:void(0);" class="btn txt-color-white bg-color-teal btn-sm roulette-game" style="display:none;"><i class="fa fa-check"></i> Create Roulette Game</a>
														 <a href="javascript:void(0);" id="smart-mod-eg1" class="button-winning btn txt-color-white bg-color-pinkDark btn-sm"><i class="fa fa-gift"></i> Winning Number</a>
														</div>
														<div class="air air-top-left padding-10">
															<h4 class="txt-color-white font-md"></h4>
														</div>
														<ol class="carousel-indicators">
															<li data-target="#myCarousel" data-slide-to="0" class="active"></li>
															<li data-target="#myCarousel" data-slide-to="1" class=""></li>
														</ol>
														<div class="carousel-inner">
															<!-- Slide 1 -->
															<div class="item active">
																<img src="{{asset('assets/img/demo/table-roulette.jpg')}}" alt="demo user">
															</div>
															<!-- Slide 2 -->
															<div class="item">
																<img src="{{asset('assets/img/demo/roulette.jpg')}}" alt="demo user">
															</div>
														</div>
													</div>
												</div>
				
												<div class="col-sm-12">
				
													<div class="row">
				
														<div class="col-sm-3 profile-pic">
															<img src="{{ asset('assets/img/avatars/sunny-big.png') }}" alt="demo user">
															<div class="padding-10">
																<h4 class="font-md"><strong><i class="fa fa-usd"></i> {{ number_format($totalbets, 2, '.', '') }}</strong>
																<br>
																<small>Total Bets</small></h4>
																<br>
																<h4 class="font-md"><strong>{{ $totalplayers }}</strong>
																<br>
																<small># Players</small></h4>
															</div>
														</div>
														<div class="col-sm-6">
															<h1>{{ $gamedetails->tabledetails->gamedetails->game_name}}</span>
															<br>
															<small> Operator: <span class="semi-bold">{{ $gamedetails->tabledetails->operator->username }} </span></small></h1>
				
															<ul class="list-unstyled">
																<li>
																	<p class="text-muted">
																		<i class="fa fa-gamepad"></i>&nbsp;&nbsp;<span class="txt-color-darken game_id">{{ $gamedetails->channel_id }}</span>
																		<span class="hide txt-color-darken channel_id">{{ $gamedetails->id }}</span>
																	</p>
																</li>
																<li>
																	<p class="text-muted">
																		<i class="fa fa-info-circle"></i>&nbsp;&nbsp;<span class="txt-color-darken">@if($gamedetails->channel_status == 1) <span class="">Open</span> @else <span class="">Closed</span> @endif</span>
																	</p>
																</li>
																<li>
																	<p class="text-muted">
																		<i class="fa fa-calendar"></i>&nbsp;&nbsp;<span class="txt-color-darken">Opened <a href="javascript:void(0);" rel="tooltip" title="" data-placement="top" data-original-title="Bet Starts Now!">{{ date("M d Y H:i:s",strtotime($gamedetails->created_at)) }}</a></span>
																	</p>
																</li>
															</ul>
				
														</div>
														<div class="col-sm-3">
															<h1><small>Players</small></h1>
															
															<ul class="list-inline friends-list">
																@if ($gamedetails->bets != null)
                                        							@foreach($gamedetails->bets as $row)
                                        								{{--*/ $rand = rand(1,11) /*--}}
                                        								{{--*/ $assets = "assets/img/avatars/".$rand.".png" /*--}}
                                        							<li>
																		<img src="{{ asset($assets) }}" alt="friend-1">
																	</li>
                                        							@endforeach
                                        						@endif 

															</ul>
															
														</div>
				
													</div>
				
												</div>
				
											</div>
				
											<div class="row">
				
												<div class="col-sm-12">
				
													<div class="padding-10">
				
														<ul class="nav nav-tabs tabs-pull-left">
															<li class="active pull-left">
																<span class="margin-top-10 display-inline">
																<i class="fa fa-money text-success"></i> Current Bets </span>
															</li>
														</ul>
				
														<div class="tab-content padding-top-10">
															<div class="tab-pane fade in active" id="a1">
																
																@if ($gamedetails->bets != null)
																	@foreach($gamedetails->bets as $row)
                                        								{{--*/ $rand = rand(1,11) /*--}}
                                        								{{--*/ $assets = "assets/img/avatars/".$rand.".png" /*--}}
                                        							<div class="user" title="email@company.com">
																		<img src="{{ asset($assets) }}" alt="demo user">
																		<a href="javascript:void(0);">{{ $row->playerdetails->username }}</a>
																		<div class="email">
																		$ {{ $row->bet_amount }}
																		</div>
																	</div>
                                        							@endforeach	
																@endif
																
																<div class="text-center">
																	<ul class="pagination pagination-sm">
																		<li class="disabled">
																			<a href="javascript:void(0);">Prev</a>
																		</li>
																		<li class="active">
																			<a href="javascript:void(0);">1</a>
																		</li>
																		<li>
																			<a href="javascript:void(0);">2</a>
																		</li>
																		<li>
																			<a href="javascript:void(0);">3</a>
																		</li>
																		<li>
																			<a href="javascript:void(0);">...</a>
																		</li>
																		<li>
																			<a href="javascript:void(0);">99</a>
																		</li>
																		<li>
																			<a href="javascript:void(0);">Next</a>
																		</li>
																	</ul>
																</div>
				
															</div>
														</div>
				
													</div>
				
												</div>
				
											</div>
											<!-- end row -->
				
										</div>
				
									</div>

						<div class="col-sm-12 col-md-12 col-lg-6 player-winnings" style="display:none;">
							
							<div class="jarviswidget well" id="wid-id-3" data-widget-colorbutton="false" data-widget-editbutton="false" data-widget-togglebutton="false" data-widget-deletebutton="false" data-widget-fullscreenbutton="false" data-widget-custombutton="false" data-widget-sortable="false">
								<header>
									<span class="widget-icon"> 
									<i class="fa fa-comments"></i> </span>
									<h2>Default Tabs with border </h2>
				
								</header>
				
								<!-- widget div-->
								<div>
				
									<!-- widget edit box -->
									<div class="jarviswidget-editbox">
										<!-- This area used as dropdown edit box -->
				
									</div>
									<!-- end widget edit box -->
				
									<!-- widget content -->
									<div class="widget-body">

										<ul id="myTab1" class="nav nav-tabs bordered">
											<li class="active">
												<a href="#s1" data-toggle="tab">Winning Player 
													<span class="badge bg-color-blue txt-color-white">12</span>
												</a>
											</li>
										</ul>
				
										<div id="myTabContent1" class="tab-content padding-10">
											<div class="tab-pane fade in active" id="s1">
												
											</div>
										</div>
				
									</div>
									<!-- end widget content -->
				
								</div>
								<!-- end widget div -->
				
							</div>
							<!-- end widget -->
									</div>
								</div>
							</div>
				
					</div>
				
				</div>
				
				<!-- end row -->
</div>
@stop